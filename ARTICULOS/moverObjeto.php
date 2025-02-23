<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'claseArticulo.php'; // Incluye el archivo de la clase Articulo
$articuloObj = new Articulo();

// Obtener los parámetros desde la URL
$tipoObjeto = $_GET['tipoObjeto'];
$idObjeto = $_GET['idObjeto'];
$tipoDestino = $_GET['tipoDestino'];
$idDestino = $_GET['idDestino'];

if($articuloObj->moverArticulo($tipoObjeto, $idObjeto, $tipoDestino, $idDestino)){
    echo "Movimiento realizado con éxito";
} else {
    echo "error";
}

?>




