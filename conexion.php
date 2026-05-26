<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conexion = new mysqli(
    "sql313.infinityfree.com",  // HOST
    "if0_41477748",              // USUARIO
    "PigbPP5Qgv8vi",              // PASSWORD
    "if0_41477748_inventario"    // BASE DE DATOS
);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}


