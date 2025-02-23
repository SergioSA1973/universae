<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'claseUbicacion.php'; // Incluye el archivo de la clase Domicilio


if (isset($_GET['id_Ubicacion'])) {
    $id = $_GET['id_Ubicacion'];

     // Crear una instancia de la clase Domicilio
    $ubicacionObj = new Ubicacion();

    // Verificar si el domicilio tiene ubicaciones asociadas
    if ($ubicacionObj->tieneContenedoresPorUbicacion($id)) {
        echo 'false'; // Tiene ubicaciones asociadas, no se puede eliminar
    } else {
        if ($ubicacionObj->cantidadArticulosPorUbicacion($id)) 
        {
            echo 'false';
        }
        else
        {
            echo 'true'; // No tiene ubicaciones asociadas, se puede eliminar    
        }
        
    }
}
?>
