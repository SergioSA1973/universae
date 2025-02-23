<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../DOMICILIOS/claseDomicilio.php'; // Incluye el archivo de la clase Domicilio
include '../UBICACIONES/claseUbicacion.php'; // Incluye el archivo de la clase Ubicacion
include '../CONTENEDORES/claseContenedor.php'; // Incluye el archivo de la clase Contenedor
include '../ARTICULOS/claseArticulo.php'; // Incluye el archivo de la clase Articulo

// Crear una instancia de la clase Domicilio
$domicilioObj = new Domicilio();
// Crear una instancia de la clase Ubicación
$ubicacionObj = new Ubicacion();
// Crear una instancia de la clase Contenedor
$contenedorObj = new Contenedor();
// Crear una instancia de la clase Articulo
$articuloObj = new Articulo();

try 
{
    session_start();
    $idUsuario = $_SESSION['sesUsuario'];
    if (empty($idUsuario) ) {
        throw new Exception("La sesión de usuario debe contener datos.");
    }

    // Obtener todos los domicilios para el combo
    $domicilios = $domicilioObj->obtenerDomiciliosPorUsuarioMasAutorizado($idUsuario);

    $selected_domicilio = $_GET['id_Domicilio'] ?? '';
    $selected_ubicacion = $_GET['id_Ubicacion'] ?? '';
    $selected_contenedor = $_GET['id_Contenedor'] ?? '';
    $selected_articulo = $_GET['id_Articulo'] ?? ''; // Variable que no estaba definida

    // Obtener todas las ubicaciones para el domicilio seleccionado
    $ubicaciones = [];
    if ($selected_domicilio) {
        $ubicaciones = $ubicacionObj->obtenerUbicacionesPorIdDomicilio($selected_domicilio);
    }

    $contenedores = [];
    if ($selected_ubicacion) {
        $contenedores =$contenedorObj->obtenerContenedoresPorIdUbicacionSinPadre($selected_ubicacion);
    }

    $articulos = [];
    if ($selected_contenedor) {
       $articulos = $articuloObj->FiltrarComboArticulos($selected_contenedor);
    }
    else
    {
        $articulos = $articuloObj->FiltrarComboArticulosDinamico($selected_domicilio, $selected_ubicacion, $selected_contenedor);
    }

    // ----------------------FIN DEL ALTA DE COMBOS---------------------------
    // ----------------------CONSULTA GENERAL --------------------------------
    // Construir la consulta de articulos con los filtros aplicados
    $generales = [];
    if ($selected_domicilio || $selected_ubicacion || $selected_contenedor || $selected_articulo) 
    {
        $generales = $articuloObj->FiltrarConsultaGeneralArticulosListado($selected_domicilio, $selected_ubicacion, $selected_contenedor, $selected_articulo);
    }
    // -----------------------------FIN CONSULTA GENERAL------------------------

} catch (Exception $e) {
    $loginMessage = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscador de Artículos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/VERSION1/styles.css">
    <style>
	.img-thumbnail-custom {
    		width: 80px; /* Puedes ajustar el tamaño según lo necesites */
    		height: auto;
	}

        /* Estilos para impresión */
        @media print {
            body * {
                display: none;
            }
            .table, .table * {
                display: table;
                visibility: visible;
            }
            table {
                visibility: visible;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }
            .export-excel-btn {
                display: none;
            }
        }
        /* Para colocar el botón de exportación a la derecha */
        .export-btn-container {
            display: flex;
            justify-content: flex-end;
        }
    </style>
</head>
<body>
    <div class="background"></div>
    <div class="overlay"></div>
    <div class="navbar" id="navbar">
        <?php include '../enlaces.php'; ?>
    </div>
    <div class="container mt-5">
        <h1 class="mb-4">Buscador de Artículos</h1>

        <!-- Formulario con clases de Bootstrap -->
        <form method="get" action="listados.php" class="mb-4">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="id_Domicilio">Domicilio:</label>
                    <select name="id_Domicilio" id="id_Domicilio" class="form-control" onchange="resetUbicacionContenedorArticuloAndSubmit()">
                        <option value="">Seleccione un domicilio</option>
                        <?php foreach ($domicilios as $domicilio): ?>
                            <option value="<?= $domicilio['id_Domicilio'] ?>" <?= $domicilio['id_Domicilio'] == $selected_domicilio ? 'selected' : '' ?>>
                                <?= $domicilio['Desc_Domicilio'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label for="id_Ubicacion">Ubicación:</label>
                    <select name="id_Ubicacion" id="id_Ubicacion" class="form-control" onchange="resetContenedorArticuloAndSubmit()">
                        <option value="">Seleccione una ubicación</option>
                        <?php foreach ($ubicaciones as $ubicacion): ?>
                            <option value="<?= $ubicacion['id_Ubicacion'] ?>" <?= $ubicacion['id_Ubicacion'] == $selected_ubicacion ? 'selected' : '' ?>>
                                <?= $ubicacion['Desc_Ubicacion'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label for="id_Contenedor">Contenedor Padre:</label>
                    <select name="id_Contenedor" id="id_Contenedor" class="form-control" onchange="resetArticuloAndSubmit()">
                        <option value="">Seleccione un contenedor</option>
                        <?php foreach ($contenedores as $contenedor): ?>
                            <option value="<?= $contenedor['id_Contenedor'] ?>" <?= $contenedor['id_Contenedor'] == $selected_contenedor ? 'selected' : '' ?>>
                                <?= $contenedor['Desc_Contenedor'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label for="id_Articulo">Artículo:</label>
                    <select name="id_Articulo" id="id_Articulo" class="form-control" onchange="this.form.submit()">
                        <option value="">Seleccione un artículo</option>
                        <?php foreach ($articulos as $articulo): ?>
                            <option value="<?= $articulo['id_Articulo'] ?>" <?= $articulo['id_Articulo'] == $selected_articulo ? 'selected' : '' ?>>
                                <?= $articulo['Desc_Articulo'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
	<div class="d-flex justify-content-between align-items-center mt-4">
            <h2>Resultados de la Búsqueda</h2>
            <div class="export-btn-container">
                <button class="btn btn-success export-excel-btn" onclick="exportarAExcel()">Exportar Rejilla a Excel</button>
            </div>
        </div>
        <!-- Tabla con clases de Bootstrap -->
        <table class="table table-bordered" id="tabla-resultados">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Domicilio</th>
                    <th>Ubicación</th>
                    <th>Contenedor Padre</th>
                    <th>Contenedor</th>
                    <th>Artículo</th>
	            <th>Usuario</th>
		    <th>Fecha</th>
		    <th>Foto</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($generales)) 
                {
                ?>
                    <?php foreach ($generales as $general): ?>
                    <tr>
                        <td><?= $general['id_Articulo'] ?></td>
                        <td><?= htmlspecialchars($general['Desc_Domicilio']) ?></td>
                        <td><?= htmlspecialchars($general['Desc_Ubicacion']) ?></td>
                        <td><?= htmlspecialchars($general['Contenedor_Padre']) ?></td>
                        <td><?= htmlspecialchars($general['desc_Contenedor']) ?></td>
                        <td><?= htmlspecialchars($general['Desc_Articulo']) ?></td>
    		            <td><?= htmlspecialchars($general['Username']) ?></td>
    		            <td><?= htmlspecialchars($general['Fec_Modificacion']) ?></td>
    			        <td>
    				        <?php if (!empty($general['img_Foto'])): ?>
                                		<img src="<?= htmlspecialchars('../ARTICULOS/' . $general['img_Foto']) ?>" alt="Foto" class="img-thumbnail-custom">
                            <?php endif; ?>

    			         </td>
                    </tr>
                    <?php endforeach; ?>
                <?php 
                } else 
                    {
                    ?>
                        <tr>
                            <td colspan="9" class="text-center">No existen registros disponibles.</td>
                        </tr>
                    <?php 
                    }
                ?>
            </tbody>
        </table>
        <br><br>

        <div class="container my-5">
            <?php
                if (isset($loginMessage)) {
                    echo $loginMessage;
                }
                ?>
        </div>
    </div>

    <script>
        function resetUbicacionContenedorArticuloAndSubmit() {
            document.getElementById('id_Ubicacion').value = ''; 
            document.getElementById('id_Contenedor').value = ''; 
            document.getElementById('id_Articulo').value = ''; 
            document.forms[0].submit();
        }

        function resetContenedorArticuloAndSubmit() {
            document.getElementById('id_Contenedor').value = ''; 
            document.getElementById('id_Articulo').value = ''; 
            document.forms[0].submit();
        }

        function resetArticuloAndSubmit() {
            document.getElementById('id_Articulo').value = ''; 
            document.forms[0].submit();
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>


<script type="text/javascript">
        function exportarAExcel() {
            var wb = XLSX.utils.book_new();
            var ws_data = [];
            
            // Recorre cada fila de la tabla
            var table = document.getElementById('tabla-resultados');
            var rows = table.querySelectorAll('tr');
            
            rows.forEach(function(row, rowIndex) {
                var cells = row.querySelectorAll('td, th');
                var rowData = [];
                cells.forEach(function(cell) {
                    var cellText = cell.innerText.trim();
                    var img = cell.querySelector('img');
                    if (img) {
                        // Convierte imagen a base64
                        var imgData = img.src;
                        // Ajusta la celda para mostrar la URL de la imagen en lugar del base64
                        cellText = imgData;
                    }
                    rowData.push(cellText);
                });
                ws_data.push(rowData);
            });
            
            // Crear hoja de trabajo
            var ws = XLSX.utils.aoa_to_sheet(ws_data);
            XLSX.utils.book_append_sheet(wb, ws, 'Datos');
            
            // Guardar el archivo
            XLSX.writeFile(wb, 'datos.xlsx');
        }
    </script>

</body>
</html>
