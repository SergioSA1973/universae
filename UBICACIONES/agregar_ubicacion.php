<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'claseUbicacion.php'; // Incluye el archivo de la clase Ubicacion


$id_domicilio = $_GET['id_Domicilio'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try 
    {
        $desc_ubicacion = $_POST['Desc_Ubicacion'];
        // Crear una instancia de la clase Ubicación
        $ubicacionObj = new Ubicacion( null, $id_domicilio, $desc_ubicacion); // Pasamos la conexión a la base de datos

        // Intentamos insertar el domicilio
        $insertado = $ubicacionObj->insertar();

        // Si la inserción es exitosa, redirigimos a la página de ubicaciones
        if ($insertado) {

            header('Location: ubicaciones.php?id_Domicilio=' . $id_domicilio);
            exit;
        }

    } catch (Exception $e) {
        $loginMessage = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Alta de Ubicaciones</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/VERSION1/styles.css">
</head>
<body>
    <div class="background"></div>
    <div class="overlay"></div>
    <div class="container mt-5">
        <h1 class="mb-4">Alta de Ubicación</h1>
        <form action="agregar_ubicacion.php?id_Domicilio=<?= $id_domicilio ?>" method="post">

            <div class="form-group">
                <label for="Desc_Ubicacion">Descripción:</label>
                <input type="text" class="form-control" id="Desc_Ubicacion" name="Desc_Ubicacion" required>
            </div>
            <button type="submit" class="btn btn-primary" onclick="return confirm('¿Estás seguro de que deseas añadir esta ubicación?')">Aceptar</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='/VERSION1/UBICACIONES/ubicaciones.php?id_Domicilio=<?= $id_domicilio ?>';">Volver</button>
        </form>
    </div>

    <div class="container my-5">
            <?php
            if (isset($loginMessage)) {
                echo $loginMessage;
            }
            ?>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
