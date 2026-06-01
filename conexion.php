<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * CONEXIÓN EN VIVO - LIBRERÍA EBENEZER
 * Ponemos los datos idénticos a la pestaña de Variables de tu Railway actual.
 */

$host     = "mysql.railway.internal";     // Reemplaza esto
$usuario  = "root"; 
$password = "MJWluisAMAblEzNdnRRbXltbnPrRLlTx "; // Reemplaza esto
$db_name  = "railway"; 
$puerto   = 3306;     // Reemplaza esto (sin comillas si es número)

// Conexión directa
$conexion = new mysqli($host, $usuario, $password, $db_name, (int)$puerto);

if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>
