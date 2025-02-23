<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include '../DOMICILIOS/claseDomicilio.php'; // Incluye el archivo de la clase Domicilio
include '../UBICACIONES/claseUbicacion.php'; // Incluye el archivo de la clase Ubicacion
include 'claseContenedor.php'; // Incluye el archivo de la clase Contenedor

$selected_domicilio = $_GET['id_Domicilio'] ?? '';

$ubicacionObj = new Ubicacion();
$contenedorObj = new Contenedor();

try 
{
    $ubicaciones = $ubicacionObj->obtenerUbicacionesPorIdDomicilio($selected_domicilio); 
    $contenedoresPadres =[];
    if ($selected_domicilio) {
        $contenedoresPadres = $contenedorObj->ObtenerContenedoresFormulario($selected_domicilio);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $desc_contenedor = $_POST['Desc_Contenedor'];
        $id_ubicacion = $_POST['id_Ubicacion'] ?: null;
        $id_padre = $_POST['id_Padre'] ?: null;
        $selected_domicilio = $_POST['txtIdDomicilio'] ?: null;

        $contenedorObj = new Contenedor(
            null,
            empty($id_ubicacion) ? null : $id_ubicacion,
            empty($id_padre) ? null : $id_padre,
            empty($desc_contenedor) ? null : $desc_contenedor
        );

        // Intentamos insertar el domicilio
        $insertado = $contenedorObj->insertar();

        // Si la inserción es exitosa, redirigimos a la página de contenedor
        if ($insertado) {
            header('Location: contenedores.php?id_Domicilio=' . $selected_domicilio);
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
    <title>Alta de Contenedor</title>
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

            return confirm("¿Está seguro de que desea dar de alta este contenedor?");
        }
    </script>
</head>
<body>
    <div class="background"></div>
    <div class="overlay"></div>
    <div class="container mt-5">
        <h1 class="mb-4">Alta de Contenedor</h1>
        
        <!-- Formulario con clases Bootstrap -->
        <form action="agregar_contenedor.php" method="post" onsubmit="return validarFormulario();">
            <div class="form-group">
                <label for="id_Ubicacion">Ubicación:</label>
                <select id="id_Ubicacion" name="id_Ubicacion" class="form-control">
                    <option value="">Seleccione una ubicación</option>
                    <?php foreach ($ubicaciones as $ubicacion): ?>
                        <option value="<?= $ubicacion['id_Ubicacion'] ?>"><?= $ubicacion['Desc_Ubicacion'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_Padre">Contenedor Padre:</label>
                <select id="id_Padre" name="id_Padre" class="form-control">
                    <option value="">Ninguno</option>
                    <?php foreach ($contenedoresPadres as $contenedorPadre): ?>
                        <option value="<?= $contenedorPadre['id_Contenedor'] ?>"><?= $contenedorPadre['Desc_Contenedor'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="Desc_Contenedor">Descripción:</label>
                <input type="text" id="Desc_Contenedor" name="Desc_Contenedor" class="form-control" required>

                <input type="hidden" id="txtIdDomicilio" name="txtIdDomicilio" class="form-control" value="<?= $selected_domicilio ?>">
            </div>

            <button type="submit" class="btn btn-primary">Aceptar</button>
           
            <button type="button" class="btn btn-secondary" onclick="window.location.href='/VERSION1/CONTENEDORES/contenedores.php?id_Domicilio=<?= $selected_domicilio ?>';">Volver</button>

            <div class="container my-5">
            <?php
                if (isset($loginMessage)) {
                    echo $loginMessage;
                }
                ?>
            </div>

        </form>
    </div>
</body>
</html>
