<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'claseDomicilio.php'; // Incluye el archivo de la clase Domicilio

$id = $_GET['updateid'];
$domicilioObj = new Domicilio();
$row = $domicilioObj->obtenerPorId($id);
if ($row) {
    $name = $row['Desc_Domicilio'];
} else {
    die("Domicilio no encontrado");
}

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
  
    try 
    {
        // Crear una instancia de la clase Domicilio
        $domicilioObjMod = new Domicilio( $id, $name, null ); // Pasamos la conexión a la base de datos
        // Intentamos insertar el domicilio
        $modificado = $domicilioObjMod->modificar();

        // Si la inserción es exitosa, redirigimos a la página de domicilios
        if ($modificado) {
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/VERSION1/styles.css">

    <title>Modificación de Domicilios</title>

    <script type="text/javascript">
        function confirmSubmit() {
            return confirm("¿Estás seguro de que deseas modificar este domicilio?");
        }
    </script>
</head>
<body>
    <div class="background"></div>
    <div class="overlay"></div>
    <div class="container my-5">
        <form method="post" onsubmit="return confirmSubmit();">
            <br>
            <h3>Modificación de Domicilio</h3>

            <div class="form-group">
                <label>Domicilio</label>
                <input type="text" class="form-control" 
                       placeholder="Introduce la descripción del domicilio" required size="80" maxlength="100" 
                       name="name" autocomplete="off" value="<?php echo $name ?>">
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
