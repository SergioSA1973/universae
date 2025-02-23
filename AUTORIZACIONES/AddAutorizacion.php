<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'claseAutorizacion.php'; // Incluye el archivo de la clase Domicilio


// Verificar si el formulario fue enviado
if (isset($_POST['submit'])) {
    $domicilio = $_POST['id_Domicilio'];
    $username = $_POST['name'];

    $autorizacionObj = new Autorizacion();
    if ($autorizacionObj->exiteUsuarioAutorizado($username)) {
        if ($autorizacionObj->exiteUsuarioAutorizadoEnDomicilio(0, $domicilio, $username)){
            $MessageUsuario = "<div class='alert alert-danger'>El usuario a autorizar ya esta dado de alta en el domicilio</div>";
        }
        else
        {
            // Crear una instancia de la clase Domicilio
            $autorizacionObj = new Autorizacion( null,  $username, $domicilio); // Pasamos la conexión a la base de datos

            // Intentamos insertar la autorizacion
            $insertado = $autorizacionObj->insertar();

            // Si la inserción es exitosa, redirigimos a la página de autorizaciones
            if ($insertado) {
                header('Location: autorizaciones.php?id_Domicilio=' . $domicilio);
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
    <link href=https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/VERSION1/styles.css">
    <title>Alta de Autorización</title>
    <script type="text/javascript">
        function confirmSubmit() {
            return confirm("¿Estás seguro de que deseas dar de alta la autorización?");
        }
    </script>
</head>

<body>
    <div class="background"></div>
    <div class="overlay"></div>
    <div class="container">
        <br>
        <h3>Alta de Autorización</h3>

        <form method="post" onsubmit="return confirmSubmit();">
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
                                    <option value="<?= $domicilio['id_Domicilio'] ?>" >
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
                <input type="text" class="form-control" size="80" maxlength="100" placeholder="Introduzca el usuario" required name="name" autocomplete="off">
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

 