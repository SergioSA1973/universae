<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'claseUbicacion.php'; // Incluye el archivo de la clase Ubicacion

$id = $_GET['id'];
$id_domicilio = $_GET['id_Domicilio'];
/*
echo  $id ;
echo "---";
echo $id_domicilio;
exit();
*/
// Verifica si se ha proporcionado un ID
if (isset($id)) {

    $ubicacion = new Ubicacion(); // Pasamos la conexión a la base de datos

    // Llamamos al método eliminar
    if($ubicacion->eliminar($id)){
        header('Location: ubicaciones.php?id_Domicilio=' . $id_domicilio);
    } else {
        die("Error al eliminar ubicación");
    }



} else {
    echo "ID de ubicacion proporcionado no es correcto.";
}
?>
