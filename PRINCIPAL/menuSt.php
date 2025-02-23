
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal</title>
    <link rel="stylesheet" href="/VERSION1/styles.css">
</head>

<body>
    <div class="background"></div>
    <div class="overlay"></div>
    <div class="navbar" id="navbar">
        <?php include '../enlaces.php'; ?>
    </div>
    <?php
    session_start();
    if (isset($_SESSION['sesUsername'])) {
        echo "Usuario: " . $_SESSION['sesUsername'];
    } else {
        echo "No se ha iniciado sesión.";
    }
    ?>

    <div class="content">
       
    </div>

    <script src="/VERSION1/scriptM.js"></script>
</body>
</html>