<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'claseContenedor.php'; // Incluye el archivo de la clase Domicilio


if (isset($_GET['id_Contenedor'])) {
    $id = $_GET['id_Contenedor'];

    $contenedorObj = new Contenedor();

    // Verificar si el contenedor
    if ($contenedorObj->tieneContenedoresHijos($id)) {
        echo 'false'; // Tiene contenedores asociadas, no se puede eliminar
    } else {
        if ($contenedorObj->cantidadArticulosPorContenedor($id)) 
        {
            echo 'false';
        }
        else
        {
            echo 'true'; // No tiene articulos asociadas, se puede eliminar    
        }
        
    }
}
?>
