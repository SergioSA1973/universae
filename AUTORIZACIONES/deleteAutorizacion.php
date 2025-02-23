<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

include 'claseAutorizacion.php'; // Incluye el archivo de la clase Autorizacion
$id = $_GET['deleteid'];

if(isset($id)){

// Crear una instancia de la clase Autorizacion
    $autorizacion = new Autorizacion(); // Pasamos la conexión a la clase
    // Llamamos al método eliminar
    $row = $autorizacion->obtenerPorId($id);
    if ($row) {
        $selected_domicilio = $row['id_Domicilio'];
    } 

    if($autorizacion->eliminar($id)){
        header('Location: autorizaciones.php?id_Domicilio=' . $selected_domicilio);
    } else {
        die("Error al eliminar la autorizacion");
    }
}
?>