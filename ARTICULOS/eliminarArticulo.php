<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'claseArticulo.php'; // Incluye el archivo de la clase Articulo

// Crear una instancia de la clase Articulo
$articuloObj = new Articulo();

$id = $_GET['id_Articulo'];
$id_domicilio = $_GET['id_Domicilio'] ?? '';

// Verifica si se ha proporcionado un ID
if (isset($id)) {
   
    $articuloObj = new Articulo(); // Pasamos la conexión a la base de datos

    // Llamamos al método eliminar
    if($articuloObj->eliminar($id)){
        header('Location: articulos.php?id_Domicilio=' . $id_domicilio);
        exit();
    } else {
        die("Error al eliminar Articulo");
    }

} else {
    echo "ID de articulo no proporcionado.";
}
?>
