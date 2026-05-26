<?php
session_start();
if (!isset($_SESSION['usuario']) || strtolower(trim($_SESSION['rol'] ?? '')) !== 'admin') {
    header("Location: index.php");
    exit();
}

include("conexion.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Ejecutar la eliminación
    $sql = "DELETE FROM productos WHERE id = $id";
    
    if ($conexion->query($sql)) {
        // Regresa al listado con un mensaje de éxito (opcional)
        header("Location: productos.php?eliminado=1");
    } else {
        echo "Error al eliminar: " . $conexion->error;
    }
} else {
    header("Location: productos.php");
}
?>