    <?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include '../UBICACIONES/claseUbicacion.php'; // Incluye el archivo de la clase Ubicacion
    include '../CONTENEDORES/claseContenedor.php'; // Incluye el archivo de la clase Contenedor
    include 'claseArticulo.php'; // Incluye el archivo de la clase Articulo

try 
{

    // Crear una instancia de la clase Ubicación
    $ubicacionObj = new Ubicacion();
    // Crear una instancia de la clase Contenedor
    $contenedorObj = new Contenedor();
    // Crear una instancia de la clase Articulo
    $articuloObj = new Articulo();


    $id_domicilio = $_GET['id_Domicilio'] ?? '';
    $id_UbicacionGet = $_GET['id_Ubicacion'] ?? '';
    $id_ContenedorGet = $_GET['id_Contenedor'] ?? '';

    $ubicaciones = $ubicacionObj->obtenerUbicacionesPorIdLista($id_UbicacionGet);
    $contenedoresPadres =$contenedorObj->obtenerContenedoresPorIdLista($id_ContenedorGet);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $desc_articulo = $_POST['Desc_Articulo'];
            $id_ubicacion = $_POST['id_UbicacionC'] ?: null;
            $id_padre = $_POST['id_PadreC'] ?: null;
            $id_domicilio = $_POST['id_domicilio'];
            $rutaDestino = null;

        if (!empty($_FILES['imagen']['name']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        //    echo "Extensión: " . $ext . "<br>";
            
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $rutaDestino = 'uploads/' . uniqid() . '.' . $ext;
           //     echo "Ruta destino: " . $rutaDestino . "<br>";
                
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                   // echo "Archivo subido correctamente.<br>";
                } else {
                    echo "Error al mover el archivo.<br>";
                }
            } else {
                echo "Extensión no permitida.<br>";
            }
        } else {
            echo "Error al subir la imagen. Código de error: " . $_FILES['imagen']['error'] . "<br>";
        }

            // Inserta el nuevo articulo
            session_start();
            $id_Usuario = $_SESSION['sesUsuario'];
            
            $articuloObj = new Articulo(
                null,
                empty($id_ubicacion) ? null : $id_ubicacion,
                empty($id_padre) ? null : $id_padre,
                empty($desc_articulo) ? null : $desc_articulo,
                $id_Usuario,  $rutaDestino
            );


            // Intentamos insertar el domicilio
            $insertado = $articuloObj->insertar();

            // Si la inserción es exitosa
            if ($insertado) {
                header('Location: articulos.php?id_Domicilio=' . $id_domicilio);
                exit;
            }

    }

} catch (Exception $e) {
    $loginMessage = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
}

    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Alta de Artículos</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="/VERSION1/styles.css">
        <script>
            function validarFormulario() {
                const ubicacion = document.getElementById('id_Ubicacion').value;
                const contenedorPadre = document.getElementById('id_Padre').value;

                if (ubicacion !== "" && contenedorPadre !== "") {
                    alert("Debe seleccionar solo una opción: Ubicación o Contenedor Padre, no ambas.");
                    return false; // Evita que se envíe el formulario
                }

                if (ubicacion === "" && contenedorPadre === "") {
                    alert("Debe seleccionar al menos una opción: Ubicación o Contenedor Padre.");
                    return false; // Evita que se envíe el formulario
                }

                return confirm("¿Está seguro de que desea agregar este artículo?");
            }
        </script>
    </head>
    <body>
        <div class="background"></div>
        <div class="overlay"></div>
        <div class="container mt-5">
            <h1 class="mb-4">Alta de Artículos</h1>
            
            <!-- Formulario con clases Bootstrap -->
    	<form action="anadirArticulo.php?id_Domicilio=<?= $id_domicilio ?>" method="post" enctype="multipart/form-data" onsubmit="return validarFormulario();">
    	    <div class="form-group">
                    <label for="id_Ubicacion">Ubicación:</label>
                    <select id="id_Ubicacion" name="id_Ubicacion" class="form-control" disabled>
                        <option value="">Seleccione una ubicación</option>
                        <?php foreach ($ubicaciones as $ubicacion): ?>
    			<option value="<?= $ubicacion['id_Ubicacion'] ?>" <?= $ubicacion['id_Ubicacion'] == $id_UbicacionGet ? 'selected' : '' ?>><?= $ubicacion['Desc_Ubicacion'] ?></option>
    		    <?php endforeach; ?>
    		    <input type="hidden" id="id_UbicacionC" name="id_UbicacionC" class="form-control" value="<?= $id_UbicacionGet ?>">

                    </select>
                </div>

                <div class="form-group">
                    <label for="id_Padre">Contenedor Padre:</label>
                    <select id="id_Padre" name="id_Padre" class="form-control" disabled>
                        <option value="">Ninguno</option>
                        <?php foreach ($contenedoresPadres as $contenedorPadre): ?>
                        	<option value="<?= $contenedorPadre['id_Contenedor'] ?>" <?= $contenedorPadre['id_Contenedor'] == $id_ContenedorGet ? 'selected' : '' ?>><?= $contenedorPadre['Desc_Contenedor'] ?></option>
    		    <?php endforeach; ?>
                        <input type="hidden" id="id_PadreC" name="id_PadreC" class="form-control" value="<?= $id_ContenedorGet ?>">

                    </select>
                </div>

                <div class="form-group">
                    <label for="Desc_Articulo">Descripción:</label>
                    <input type="text" id="Desc_Articulo" name="Desc_Articulo" class="form-control" required>
    		<input type="hidden" id="id_domicilio" name="id_domicilio" class="form-control" value="<?= $id_domicilio ?>">
                </div>

    	     <div class="form-group">
            	<label for="imagen">Subir Imagen:</label>
            	<input type="file" id="imagen" name="imagen" class="form-control" accept="image/*" onchange="previsualizarImagen(event)">
            	<img id="preview" src="#" alt="Previsualización de la imagen" style="display:none; max-width:200px; margin-top:10px;">
        	     </div>

                <button type="submit" class="btn btn-primary">Aceptar</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='/VERSION1/ARTICULOS/articulos.php?id_Domicilio=<?= $id_domicilio ?>';">Volver</button>
            </form>
        </div>
        <br><br>
        <div class="container my-5">
            <?php
                if (isset($loginMessage)) {
                    echo $loginMessage;
                }
                ?>
        </div>
    <script>
    function previsualizarImagen(event) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('preview').src = e.target.result;
        document.getElementById('preview').style.display = 'block';
        reader.readAsDataURL(event.target.files[0]);
    }

    </script>
    </body>
    </html>
