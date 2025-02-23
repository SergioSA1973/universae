<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'claseDomicilio.php'; // Incluye el archivo de la clase Domicilio


if (isset($_GET['id'])) {
    $id = $_GET['id'];

     // Crear una instancia de la clase Domicilio
    $domicilioObj = new Domicilio();

    // Verificar si el domicilio tiene ubicaciones asociadas
    if ($domicilioObj->tieneUbicacionesAsociadas($id)) {
        echo 'false'; // Tiene ubicaciones asociadas, no se puede eliminar
    } else {
        echo 'true'; // No tiene ubicaciones asociadas, se puede eliminar
    }
}
?>
