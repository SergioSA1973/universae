<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'claseDomicilio.php'; // Incluye el archivo de la clase Domicilio

session_start();

// Verificar si el formulario fue enviado
if (isset($_POST['submit'])) {
    $domicilio = $_POST['name'];
    $idUsuario = $_POST['txtUsuario'];

    try 
    {
        // Crear una instancia de la clase Domicilio
        $domicilioObj = new Domicilio( null, $domicilio, $idUsuario); // Pasamos la conexión a la base de datos

        // Intentamos insertar el domicilio
        $insertado = $domicilioObj->insertar();

        // Si la inserción es exitosa, redirigimos a la página de domicilios
        if ($insertado) {
            header('Location: domicilios2.php');
            exit;
        }
    
    } catch (Exception $e) {                           
        $loginMessage = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

?>


<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link href=https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/VERSION1/styles.css">

    <title>Crud Application</title>

    <script type="text/javascript">
        function confirmSubmit() {
            return confirm("¿Estás seguro de que deseas dar de alta este domicilio?");
        }
    </script>
</head>

<body>
    <div class="background"></div>
    <div class="overlay"></div>    
    <div class="container">
        <br>
        <h3>Alta de Domicilio</h3>

        <form method="post" onsubmit="return confirmSubmit();">
            <div class="form-group">
                <label>Domicilio</label>
                <input type="text" class="form-control" size="80" maxlength="100" placeholder="Introduzca la descripción del domicilio" required name="name" autocomplete="off">
                <input type="hidden" class="form-control" size="20" maxlength="20" name="txtUsuario"  value="<?php echo $_SESSION['sesUsuario']; ?>">
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Aceptar</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='/VERSION1/DOMICILIOS/domicilios2.php';">Volver</button>

        </form>
    </div>
    <div class="container my-5">
        <?php
        if (isset($loginMessage)) {
            echo $loginMessage;
        }
        ?>
    </div>
</body>
</html>

 