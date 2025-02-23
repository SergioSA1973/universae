
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Iniciar Sesión</title>
        <!-- Enlace a Bootstrap CSS -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="/VERSION1/estilos.css">
    </head>
    <body>
        <?php
    	ini_set('display_errors', 1);
    	ini_set('display_startup_errors', 1);
    	error_reporting(E_ALL);

       // include 'connect.php';
        include 'claseUsuario.php'; // Incluye el archivo de la clase Usuario
        $loginMessage = "";
        session_start();

            $idUsuario = $_SESSION['sesUsuario'];
            if (empty($idUsuario) ) {
                throw new Exception("La sesión de usuario debe contener datos.");
            }

        if ($_SERVER["REQUEST_METHOD"] == "POST") 
        {
            $usuario = new Usuario(); // Pasamos la conexión a la clase
            $row = $usuario->obtenerPorIdUsuario($idUsuario);

        	if(isset($row['Username']))
            {
                $passwordAnterior=$_POST['passwordAnterior']; 
                //Aqui tengo que comprobar si la clave anterior coincide con la guardada en la bd
                if($row['Password'] == $passwordAnterior)
                {
                    //la clave anterior coincide con la text de clave anterior
            		$passwordNueva=$_POST['passwordNueva'];  
                    $passwordRepetir=$_POST['passwordRepetir'];

                    if($passwordNueva == $passwordRepetir)
                    {
                        $usuarioObj = new Usuario($idUsuario,  null, null, $passwordRepetir);
                        $modificar = $usuarioObj->modificar();

                    	if ($modificar) {
                        	// echo "Data insertion successful!!";
            	    	      $loginMessage = "<div class='alert alert-success'>Se ha cambiado correctamente la clave del usuario</div>";
                    	}else{
                        	die(mysql_error($con));
                    	}
                    }
                    else
                    {
                        $loginMessage = "<div class='alert alert-danger'>La nueva clave debe coincidir con el campo repetir contraseña.</div>";       
                    }
                }
                else
                {
                    $loginMessage = "<div class='alert alert-danger'>La clave anterior no coincide con la guardada en base de datos</div>";    
                }
        	}
        	else
        	{
        		$loginMessage = "<div class='alert alert-danger'>El Usuario no existe en la base de datos.</div>";
        	}
        }
        ?>



        <div class="login-form">
    	<form id="registro"  method="post" action="">

                <div class="form-group">
                    <label for="passwordAnterior">Contraseña Anterior:</label>
                    <input type="password" class="form-control" id="passwordAnterior" name="passwordAnterior" required>
                </div>

                <div class="form-group">
                    <label for="passwordNueva">Contraseña Nueva:</label>
                    <input type="password" class="form-control" id="passwordNueva" name="passwordNueva" required>
                </div>

                <div class="form-group">
                    <label for="passwordRepetir">Repetir Contraseña:</label>
                    <input type="password" class="form-control" id="passwordRepetir" name="passwordRepetir" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Cambiar</button>

                <button type="button" class="btn btn-secondary btn-block" onclick="window.location.href='/VERSION1/PRINCIPAL/menuSt.php';">Volver</button>

            </form>
    	<!-- Mostrar mensaje de resultado del login -->
            <?php echo $loginMessage; ?>
        </div>

        <!-- Enlace a Bootstrap JS y dependencias (opcional) -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



    </body>
    </html>