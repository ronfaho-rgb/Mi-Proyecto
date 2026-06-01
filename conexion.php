<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * CONEXIÓN DE RESPALDO DIRECTA - LIBRERÍA EBENEZER
 * Ponemos los datos fijos de tu Railway activo para evitar que lleguen vacíos.
 */

$host     = "kodama.proxy.rlwy.net";
$usuario  = "root"; 
$password = "MJWluisAMAblEzNdnRRbXltbnPrRLlTx"; 
$db_name  = "railway"; 
$puerto   = 17247; // Tu puerto público externo asignado por Railway

// Crear la conexión forzando los datos reales
$conexion = new mysqli($host, $usuario, $password, $db_name, (int)$puerto);

// Validar si la conexión falló
if ($conexion->connect_error) {
    die("Error crítico de conexión a la base de datos externa: " . $conexion->connect_error);
}

// Configurar codificación para soporte de eñes y acentos en Nicaragua
$conexion->set_charset("utf8mb4");
?>
