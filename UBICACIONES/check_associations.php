<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../connect.php';

$id_Ubicacion = $_GET['id_Ubicacion'] ?? 0;
$hasAssociations = false;

// Verificar si hay contenedores asociados a esta ubicación
$sqlContenedores = "SELECT COUNT(*) FROM Contenedores WHERE id_Ubicacion = ? AND Fec_Anulacion IS NULL";
$stmtContenedores = $pdo->prepare($sqlContenedores);
$stmtContenedores->execute([$id_Ubicacion]);
$countContenedores = $stmtContenedores->fetchColumn();

if ($countContenedores > 0) {
    $hasAssociations = true;
}

// Verificar si hay artículos asociados a esta ubicación
$sqlArticulos = "SELECT COUNT(*) FROM Articulos WHERE id_Ubicacion = ? AND Fec_Anulacion IS NULL";
$stmtArticulos = $pdo->prepare($sqlArticulos);
$stmtArticulos->execute([$id_Ubicacion]);
$countArticulos = $stmtArticulos->fetchColumn();

if ($countArticulos > 0) {
    $hasAssociations = true;
}

// Devolver el resultado en formato JSON
echo json_encode(['hasAssociations' => $hasAssociations]);
?>
