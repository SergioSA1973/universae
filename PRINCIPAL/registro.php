
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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {      
    	$nombre_usuario=$_POST['nombre_usuario'];
        $usuario = new Usuario(); // Pasamos la conexión a la clase
        $row = $usuario->obtenerPorUsername($nombre_usuario);

    	if(!isset($row['Username'])){
    		$email=$_POST['email'];
    		$password=$_POST['password'];           
            $usuarioObj = new Usuario(null,  $nombre_usuario, $email, $password);          
            $insertado = $usuarioObj->insertar();

        	if ($insertado) {
            	// echo "Data insertion successful!!";
        	      $loginMessage = "<div class='alert alert-success'>Se ha dado de alta correctamente al nuevo usuario</div>";

        	}else{
            	die(mysql_error($con));
        	}
    	}
    	else
    	{
    		$loginMessage = "<div class='alert alert-danger'>El Usuario ya existe en la base de datos.</div>";
    	}
    }
    ?>

    <div class="login-form">
	<form id="registro"  method="post" action="">

            <div class="form-group">
                <label for="nombre_usuario">Nombre de Usuario:</label>
                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" class="form-control" id="email" name="email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" oninvalid="this.setCustomValidity('Por favor, introduce un correo electrónico válido.')" oninput="this.setCustomValidity('')">
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Registrar</button>
            <button type="button" class="btn btn-secondary btn-block" onclick="window.location.href='/VERSION1/PRINCIPAL/loginIndex.php';">Volver</button>

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