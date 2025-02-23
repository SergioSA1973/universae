
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
    <div class="login-form">
        <form action="Correo.php" method="post">
            <div class="form-group">
		        <label for="email">Correo Electrónico:</label><br>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Recuperar Contraseña</button>
            <button type="button" class="btn btn-secondary btn-block" onclick="window.location.href='/VERSION1/PRINCIPAL/loginIndex.php';">Volver</button>
        </form>
    </div>

    <!-- Enlace a Bootstrap JS y dependencias (opcional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>