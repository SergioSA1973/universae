<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include '../DOMICILIOS/claseDomicilio.php'; // Incluye el archivo de la clase Domicilio
include '../UBICACIONES/claseUbicacion.php'; // Incluye el archivo de la clase Ubicacion
include 'claseContenedor.php'; // Incluye el archivo de la clase Contenedor

// Crear una instancia de la clase Domicilio
$domicilioObj = new Domicilio();
// Crear una instancia de la clase Ubicación
$ubicacionObj = new Ubicacion();
// Crear una instancia de la clase Contenedor
$contenedorObj = new Contenedor();
try 
{
    session_start();
    $idUsuario = $_SESSION['sesUsuario'];
    if (empty($idUsuario) ) {
        throw new Exception("La sesión de usuario debe contener datos.");
    }

    // Obtener domicilios del usuario
    $domicilios = $domicilioObj->obtenerDomiciliosPorUsuario($idUsuario);

    $selected_domicilio = $_GET['id_Domicilio'] ?? '';
    $selected_ubicacion = $_GET['id_Ubicacion'] ?? '';

    // Obtener todas las ubicaciones para el domicilio seleccionado
    $ubicaciones = [];
    if ($selected_domicilio) {
        $ubicaciones = $ubicacionObj->obtenerUbicacionesPorIdDomicilio($selected_domicilio); 
    }

    $contenedores =$contenedorObj->FiltrarContenedores($selected_domicilio, $selected_ubicacion);

} catch (Exception $e) {
    $loginMessage = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Contenedores</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
	<link rel="stylesheet" type="text/css" href="/VERSION1/styles.css">

    <!-- Script para validar que el domicilio esté seleccionado -->
    <script>
        function validarDomicilio(domicilio) {
            if (domicilio.value === '') {
                alert('Por favor, seleccione un domicilio antes de editar.');
                return false; // Evita que el enlace de edición se siga
            }
            return true; // Permite que el enlace de edición se siga
        }
    </script>
</head>
<body>
    <div class="background"></div>
    <div class="overlay"></div>
    <div class="navbar" id="navbar">
        <?php include '../enlaces.php'; ?>
    </div>

    <div class="container mt-5">
        <h1 class="mb-4">Listado de Contenedores</h1>
        
        <!-- Formulario con clases de Bootstrap -->
        <form method="get" action="contenedores.php" class="form-inline mb-4">
            <div class="form-group mr-3">
                <label for="id_Domicilio" class="sr-only">Filtrar por Domicilio:</label>
                <select name="id_Domicilio" id="id_Domicilio" class="form-control" onchange="resetUbicacionAndSubmit()">
    			<option value="">Seleccione un domicilio</option>
    			<?php foreach ($domicilios as $domicilio): ?>
        			<option value="<?= $domicilio['id_Domicilio'] ?>" <?= $domicilio['id_Domicilio'] == $selected_domicilio ? 'selected' : '' ?>>
            				<?= $domicilio['Desc_Domicilio'] ?>
        			</option>
    			<?php endforeach; ?>
		</select>

            </div>

            <div class="form-group">
                <label for="id_Ubicacion" class="sr-only">Filtrar por Ubicación:</label>
                <select name="id_Ubicacion" id="id_Ubicacion" class="form-control" onchange="this.form.submit()">
    			<option value="">Seleccione una ubicación</option>
    			<?php foreach ($ubicaciones as $ubicacion): ?>
        			<option value="<?= $ubicacion['id_Ubicacion'] ?>" <?= $ubicacion['id_Ubicacion'] == $selected_ubicacion ? 'selected' : '' ?>>
            				<?= $ubicacion['Desc_Ubicacion'] ?>
        			</option>
    			<?php endforeach; ?>
		</select>

            </div>
        </form>
        
        <!-- Botón de agregar con clases de Bootstrap -->
        <a href="#" class="btn btn-primary mb-4" onclick="return validarAgregarContenedor();">Añadir Contenedor</a>

        <!-- Tabla con clases de Bootstrap -->
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Ubicación</th>
                    <th>Contenedor Padre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($contenedores)) 
                    {
                ?>
                    <?php foreach ($contenedores as $contenedor): ?>
                    <tr>
                        <td><?= $contenedor['id_Contenedor'] ?></td>
                        <td><?= $contenedor['Desc_Contenedor'] ?></td>
                        <td><?= $contenedor['Desc_Ubicacion'] ?></td>
                        <td><?= $contenedor['Contenedor_Padre'] ?></td>
                        <td>
                            <!-- Llamar a la función validarDomicilio al hacer clic en el enlace de edición -->
                            <a href="editar_contenedor.php?id=<?= $contenedor['id_Contenedor'] ?>&id_Domicilio=<?= $selected_domicilio ?>&id_Ubicacion=<?= $selected_ubicacion ?>" class="btn btn-sm btn-warning" onclick="return validarDomicilio(document.getElementById('id_Domicilio'));">Editar</a>


                            <a href="#" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $contenedor['id_Contenedor'] ?>);">Eliminar</a>

                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php } else 
                {
                ?>
                    <tr>
                            <td colspan="5" class="text-center">No hay contenedores disponibles.</td>
                        </tr>
                <?php }?>

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
    function resetUbicacionAndSubmit() {
        document.getElementById('id_Ubicacion').value = ''; // Restablece la ubicación
        document.forms[0].submit(); // Envía el formulario
    }

    function validarAgregarContenedor() {
        var domicilio = document.getElementById('id_Domicilio').value;
        
        if (domicilio === '') {
            alert('Por favor, seleccione un domicilio para dar de alta un contenedor.');
            return false; // Evita la acción del enlace
        }

        // Si es válido, redirigir a la URL con el domicilio seleccionado
        var ubicacion = document.getElementById('id_Ubicacion').value;
        window.location.href = "agregar_contenedor.php?id_Domicilio=" + domicilio + "&id_Ubicacion=" + ubicacion;
        return false;
    }


    function confirmDelete(id_Contenedor) {
            var xhr = new XMLHttpRequest();
            var selectedDomicilio = document.getElementById('id_Domicilio').value;
            var selectedUbicacion = document.getElementById('id_Ubicacion').value;

            xhr.open("GET", "checkContenedores.php?id_Contenedor=" + id_Contenedor, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var response = xhr.responseText;
                    if (response == 'true') {
                        if (confirm("¿Estás seguro de que deseas eliminar el contenedor seleccionado?")) {
                            window.location.href = 'eliminar_contenedor.php?id=' + id_Contenedor + '&id_Domicilio=' + selectedDomicilio + '&id_Ubicacion=' + selectedUbicacion;
                        }
                    } else {
                        alert("Este contenedor tiene contenedores o articulos asociados");
                    }
                }
            };
            xhr.send();
        }

</script>


</body>
</html>
