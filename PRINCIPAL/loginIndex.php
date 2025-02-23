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
// Inicializar variables para mensajes
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
     include 'claseUsuario.php'; // Incluye el archivo de la clase Usuario
    $loginMessage = "";

    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

	 // Obtener datos del formulario
           $nombre_usuario = $_POST['nombre_usuario'];
           $password = $_POST['password'];

           if (empty($nombre_usuario) || empty($password)) {
            throw new Exception("El nombre de usuario o la contraseña no pueden estar vacíos.");
        }


        $usuario = new Usuario(); 
        $row = $usuario->obtenerPorUsername($nombre_usuario);
        if(isset($row)){
          if(!isset($row['Username'])){
             $loginMessage = "<div class='alert alert-danger'>Usuario no registrado.</div>";
         }
         else
         {			
			// Verificar si el usuario existe y si la contraseña es correcta
             $passBD =password_hash($row['Password'], PASSWORD_DEFAULT);
             if (password_verify($password, $passBD)) {

                session_start();
                $_SESSION['sesUsuario']=$row['id_Usuario']; 
                $_SESSION['sesUsername'] = $row['Username'];

              //  echo "variable de sesion: ".$_SESSION['sesUsuario'];

                header('location:menuSt.php');
            } else {
             $loginMessage = "<div class='alert alert-danger'>Nombre de usuario o contraseña incorrectos.</div>";
         }
     }
 }
 else
 {
   $loginMessage = "<div class='alert alert-danger'>Usuario no registrado.</div>";

}
}
} catch (Exception $e) {
    $loginMessage = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
}

?>

<div class="login-form">
    <form id="loginForm" onsubmit="return validarFormulario()" method="post" action="">
        <div class="form-group">
            <label for="nombre_usuario">Nombre de Usuario:</label>
            <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
            <div class="invalid-feedback">Por favor, ingrese su nombre de usuario.</div>
        </div>

        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <div class="invalid-feedback">Por favor, ingrese su contraseña.</div>
        </div>

        <div class="form-group d-flex justify-content-between">
            <a href="/VERSION1/PRINCIPAL/recuperar_password.php">Olvidé mi contraseña</a>
            <a href="/VERSION1/PRINCIPAL/registro.php">Registrarse</a>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
    </form>

    <!-- Mostrar mensaje de resultado del login -->
    <?php echo $loginMessage; ?>
</div>

<!-- Enlace a Bootstrap JS y dependencias (opcional) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Script para validaciones -->
<script src="script.js"></script>
</body>
</html>
