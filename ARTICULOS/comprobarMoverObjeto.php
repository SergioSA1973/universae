<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*
include 'connect.php';
include 'funcionesVarias.php';
include 'connectSimple.php';
*/
include 'claseArticulo.php'; // Incluye el archivo de la clase Articulo
$articuloObj = new Articulo();


// Obtener los parámetros desde la URL
$tipoObjeto = $_GET['tipoObjeto'];
$idObjeto = $_GET['idObjeto'];
$tipoDestino = $_GET['tipoDestino'];
$idDestino = $_GET['idDestino'];


$contenedoresHijos = $articuloObj->obtenerContenedoresRecursivos2( $idObjeto);
	$ids = array_column($contenedoresHijos, 'id_Contenedor');

//print_r($ids);

if (in_array($idDestino, $ids)) {
    		
		echo trim("invalid");  // Si no es válido
	} else {
    		
		echo trim("valid");  // Si es válido
	}







?>




