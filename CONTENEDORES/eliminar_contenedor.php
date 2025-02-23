<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'claseContenedor.php'; // Incluye el archivo de la clase Contenedor

$id = $_GET['id'];
$id_domicilio = $_GET['id_Domicilio'] ?? '';
$id_ubicacion = $_GET['id_Ubicacion'] ?? '';

// Verifica si se ha proporcionado un ID

if (isset($id)) {

    $contenedor = new Contenedor(); // Pasamos la conexión a la base de datos

    // Llamamos al método eliminar
    if($contenedor->eliminar($id)){
        header('Location: contenedores.php?id_Domicilio=' . $id_domicilio . '&id_Ubicacion=' . $id_ubicacion);
    } else {
        die("Error al eliminar Contenedor");
    }



} else {
    echo "ID de contenedor proporcionado no es correcto.";
}





?>
