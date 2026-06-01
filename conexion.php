<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * CONEXIÓN DEFINITIVA - LIBRERÍA EBENEZER
 * Usamos el host público del proxy de Railway para que Render pueda verlo desde afuera.
 */

// Datos fijos reales para evitar errores de red interna
$host     = "kodama.proxy.rlwy.net";
$usuario  = "root"; 
$password = "MJWluisAMAblEzNdnRRbXltbnPrRLlTx"; 
$db_name  = "railway"; 
$puerto   = 17247; 

// Conexión forzada
$conexion = new mysqli($host, $usuario, $password, $db_name, $puerto);

if ($conexion->connect_error) {
    die("Error de conexión externa: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>
