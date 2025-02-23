<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

include 'claseDomicilio.php'; // Incluye el archivo de la clase Domicilio

if(isset($_GET['deleteid'])){
    $id=$_GET['deleteid'];
// Crear una instancia de la clase Domicilio
    $domicilio = new Domicilio(); // Pasamos la conexión a la clase
    // Llamamos al método eliminar

    if($domicilio->eliminar($id)){
        header('Location: domicilios2.php'); // Redirigimos si la eliminación fue exitosa
    } else {
        die("Error al eliminar el domicilio");
    }
}
?>