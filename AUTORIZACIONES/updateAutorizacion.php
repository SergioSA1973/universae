<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'claseAutorizacion.php'; // Incluye el archivo de la clase Domicilio

$id = $_GET['updateid'];
$autorizacionObj = new Autorizacion(); // Pasamos la conexión a la clase
$row = $autorizacionObj->obtenerPorId($id);
if ($row) {
    $Username = $row['Username'];
    $selected_domicilio = $row['id_Domicilio'];
} else {
    die("Autorizacion no encontrado");
}

if (isset($_POST['submit'])) {


    $Username = $_POST['name'];
    $idDomicilio = $_POST['id_Domicilio'];

    $autorizacionObj = new Autorizacion();
    if ($autorizacionObj->exiteUsuarioAutorizado($Username)) {
        if ($autorizacionObj->exiteUsuarioAutorizadoEnDomicilio($id, $idDomicilio, $Username)){
            $MessageUsuario = "<div class='alert alert-danger'>El usuario a autorizar ya esta dado de alta en el domicilio</div>";
        }
        else
        {
            // Crear una instancia de la clase Autorizacion
            $autorizacionObjMod = new Autorizacion($id, $Username, $idDomicilio ); // Pasamos la conexión a la base de datos
            // Intentamos modificar la Autorizacion
            $modificado = $autorizacionObjMod->modificar();

            // Si la inserción es exitosa, redirigimos a la página de autorizaciones
            if ($modificado) {
                header('Location: autorizaciones.php?id_Domicilio=' . $idDomicilio);
                exit;
            }
        }
    } else {
        $MessageUsuario = "<div class='alert alert-danger'>El usuario no existe en la base de datos</div>";
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

    <title>Modificación de Autorizaciones</title>


    <script type="text/javascript">
        function confirmSubmit() {
            return confirm("¿Estás seguro de que deseas modificar la autorizacion?");
        }
    </script>
    
</head>
<body>
    <div class="background"></div>
    <div class="overlay"></div>
    <div class="container my-5">
        <form method="post" id="miFormulario" onsubmit="return confirmSubmit();">
            <br>
            <h3>Modificación de Autorización</h3>

            <div class="form-group">

                <label for="id_Domicilio" class="mr-2">Domicilio:</label>
                <select name="id_Domicilio" id="id_Domicilio" class="form-control mr-2" required>
                    <option value="">Seleccione un domicilio</option>
                    <?php 
                        try 
                        {
                            session_start();    
                            $idUsuario = $_SESSION['sesUsuario'];
                            if (empty($idUsuario) ) {
                                throw new Exception("La sesión de usuario debe contener datos.");
                            }
                            $domicilioObj = new Autorizacion();

                            // Obtener los domicilios asociados al usuario
                            $domicilios = $domicilioObj->obtenerDomiciliosPorUsuario($idUsuario);

                            if (!empty($domicilios)) {
                                foreach ($domicilios as $domicilio){ 
                                    ?>
                                    <option value="<?= $domicilio['id_Domicilio'] ?>" <?= $domicilio['id_Domicilio'] == $selected_domicilio ? 'selected' : '' ?>>
                                        <?= $domicilio['Desc_Domicilio'] ?>
                                    </option>
                            <?php
                               } 
                            }
                        } catch (Exception $e) {
                            $loginMessage = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                        }
                    ?>
                </select>


                <label>Usuario:</label>
                <input type="text" class="form-control" 
                       placeholder="Introduce la descripción del usuario a autorizar" required size="80" maxlength="100" 
                       name="name" autocomplete="off" value="<?php echo $Username?>">
            </div>

            <button type="submit" class="btn btn-primary" name="submit">Aceptar</button>
            <button type="button" class="btn btn-secondary" onclick="volverConDomicilio()">Volver</button>

            <div class="container my-5">
                    <?php
                    if (isset($MessageUsuario)) {
                        echo $MessageUsuario;
                    }
                    ?>
            </div>
            <div class="container my-5">
                    <?php
                    if (isset($loginMessage)) {
                        echo $loginMessage;
                    }
                    ?>
            </div>
            <script>
                function volverConDomicilio() {
                    var idDomicilio = document.getElementById("id_Domicilio").value;
                    
                    window.location.href = '/VERSION1/AUTORIZACIONES/autorizaciones.php?id_Domicilio=' + encodeURIComponent(idDomicilio);
                    
                }
            </script>
        </form>
    </div>
</body>
</html>
