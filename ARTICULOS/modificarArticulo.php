    <?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);


    include '../UBICACIONES/claseUbicacion.php'; // Incluye el archivo de la clase Ubicacion
    include '../CONTENEDORES/claseContenedor.php'; // Incluye el archivo de la clase Contenedor
    include 'claseArticulo.php'; // Incluye el archivo de la clase Articulo

    // Crear una instancia de la clase Ubicación
    $ubicacionObj = new Ubicacion();
    // Crear una instancia de la clase Contenedor
    $contenedorObj = new Contenedor();
    // Crear una instancia de la clase Articulo
    $articuloObj = new Articulo();

try 
{
    $id_domicilio = $_GET['id_Domicilio'] ?? '';
    $id_Articulo = $_GET['id_Articulo'] ?? '';

    $id_UbicacionBD = 0;
    $id_ContenedorBD = 0;

    $ubicaciones = [];
    $articulo = [];
    $contenedores = [];

    $articulo = $articuloObj->obtenerPorId($id_Articulo);
    if ($articulo && isset($articulo['id_ubicacion'])) {
        $id_UbicacionBD = $articulo['id_ubicacion'];
    }

    if ($articulo && isset($articulo['id_contenedor'])) {
        $id_ContenedorBD = $articulo['id_contenedor'];
    }


    $ubicaciones = $ubicacionObj->obtenerUbicacionesPorIdDomicilio($id_domicilio);
    $contenedores = $contenedorObj->ObtenerContenedoresFormulario($id_domicilio);


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_Articulo  = $_POST['id_Articulo'];
        $desc_articulo = $_POST['Desc_Articulo'];
        $id_ubicacion = $_POST['id_Ubicacion'] ?: null;
        $id_padre = $_POST['id_Padre'] ?: null;
        $id_domicilio = $_POST['id_domicilio'];
        $rutaDestino = null;
        $id_img=$_POST['id_img'];

    //print_r($_FILES);

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
    	if (empty($rutaDestino)) {
    		$rutaDestino = $id_img;
    	}


        // Modificacion el nuevo articulo
        session_start();
        $id_Usuario = $_SESSION['sesUsuario'];
        

        $articuloObj = new Articulo(
            $id_Articulo,
            empty($id_ubicacion) ? null : $id_ubicacion,
            empty($id_padre) ? null : $id_padre,
            empty($desc_articulo) ? null : $desc_articulo,
            $id_Usuario,  $rutaDestino
        );


        // Intentamos insertar el domicilio
        $modificado = $articuloObj->modificar();

        // Si la inserción es exitosa
        if ($modificado) {
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
        <title>Modificación de Artículos</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="/VERSION1/styles.css">
        <script>
            function validarFormulario() {
                const ubicacion = document.getElementById('id_Ubicacion').value;
                const contenedorPadre = document.getElementById('id_Padre').value;

                if (ubicacion !== "" && contenedorPadre !== "") {
                    alert("Debe seleccionar solo una opción: Ubicación o Contenedor Padre, no ambas.");
                    return false;
                }

                if (ubicacion === "" && contenedorPadre === "") {
                    alert("Debe seleccionar al menos una opción: Ubicación o Contenedor Padre.");
                    return false;
                }

                return confirm("¿Está seguro de modificar este artículo?");
            }

            // Mostrar la previsualización si se selecciona una nueva imagen
            function previsualizarImagen(event) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('preview').src = e.target.result;
                    document.getElementById('preview').style.display = 'block';
                };
                reader.readAsDataURL(event.target.files[0]);
            }

            // Verificar si hay una imagen por defecto cargada
            window.onload = function() {
                const previewImage = document.getElementById('preview');
                if (previewImage.src && previewImage.src !== window.location.href) {
                    previewImage.style.display = 'block';
                } else {
                    previewImage.style.display = 'none';
                }
            }
        </script>
    </head>
    <body>
        <div class="background"></div>
        <div class="overlay"></div>
        <div class="container mt-5">
            <h1 class="mb-4">Modificación de Artículos</h1>
            
            <form action="modificarArticulo.php?id_Domicilio=<?= $id_domicilio ?>" method="post" enctype="multipart/form-data" onsubmit="return validarFormulario();">
                <div class="form-group">
                    <label for="id_Ubicacion">Ubicación:</label>
                    <select id="id_Ubicacion" name="id_Ubicacion" class="form-control">
                        <option value="">Seleccione una ubicación</option>
                        <?php foreach ($ubicaciones as $ubicacion): ?>
                            <option value="<?= $ubicacion['id_Ubicacion'] ?>" <?= $ubicacion['id_Ubicacion'] == $id_UbicacionBD ? 'selected' : '' ?>><?= $ubicacion['Desc_Ubicacion'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_Padre">Contenedor Padre:</label>
                    <select id="id_Padre" name="id_Padre" class="form-control">
                        <option value="">Ninguno</option>
                        <?php foreach ($contenedores as $contenedorPadre): ?>
                            <option value="<?= $contenedorPadre['id_Contenedor'] ?>" <?= $contenedorPadre['id_Contenedor'] == $id_ContenedorBD ? 'selected' : '' ?>><?= $contenedorPadre['Desc_Contenedor'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="Desc_Articulo">Descripción:</label>
                    <input type="text" id="Desc_Articulo" name="Desc_Articulo" class="form-control" required value="<?= $articulo['Desc_Articulo'] ?>">
                    <input type="hidden" id="id_domicilio" name="id_domicilio" class="form-control" value="<?= $id_domicilio ?>">
                    <input type="hidden" id="id_Articulo" name="id_Articulo" class="form-control" value="<?= $id_Articulo ?>">
                </div>

                <div class="form-group">
                    <label for="imagen">Subir Imagen:</label>
                    <input type="file" id="imagen" name="imagen" class="form-control" accept="image/*" onchange="previsualizarImagen(event)">
                    
                    <!-- Verifica si la imagen existe y carga la imagen almacenada en el servidor -->
                    <?php if (!empty($articulo['img_Foto'])): ?>
                        <img id="preview" src="<?= $articulo['img_Foto'] ?>" alt="Previsualización de la imagen" style="display:block; max-width:200px; margin-top:10px;">
                    	<input type="hidden" id="id_img" name="id_img" class="form-control" value="<?= $articulo['img_Foto'] ?>">
    		<?php else: ?>
                        <img id="preview" src="#" alt="Previsualización de la imagen" style="display:none; max-width:200px; margin-top:10px;">
                    <?php endif; ?>
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
    </body>
    </html>
