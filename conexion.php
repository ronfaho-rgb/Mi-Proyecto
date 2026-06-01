<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * CONFIGURACIÓN DE CONEXIÓN - LIBRERÍA EBENEZER
 * Este archivo detecta automáticamente las variables de entorno en Render.
 * Si no están configuradas, usa por defecto tus credenciales activas de Railway.
 */

$host     = getenv('DB_HOST')     ?: "kodama.proxy.rlwy.net";
$usuario  = getenv('DB_USER')     ?: "root"; 
$password = getenv('DB_PASSWORD') ?: "MJWluisAMAblEzNdnRRbXltbnPrRLlTx"; 
$db_name  = getenv('DB_NAME')     ?: "railway"; 
$puerto   = getenv('DB_PORT')     ?: 3306; // Usamos el puerto estándar activo en tu Railway

// Crear la conexión con la base de datos MySQL
$conexion = new mysqli($host, $usuario, $password, $db_name, (int)$puerto);

// Validar si la conexión falló
if ($conexion->connect_error) {
    die("Error crítico de conexión a la base de datos: " . $conexion->connect_error);
}

// Asegurar que el intercambio de datos soporte eñes y acentos en Nicaragua
$conexion->set_charset("utf8mb4");
?>
