<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Lee las variables nativas que ya configuraste en la pantalla de Render
$host     = getenv('MYSQLHOST');
$usuario  = getenv('MYSQLUSER'); 
$password = getenv('MYSQLPASSWORD'); 
$db_name  = getenv('MYSQLDATABASE'); 
$puerto   = getenv('MYSQLPORT') ?: 3306; 

$conexion = new mysqli($host, $usuario, $password, $db_name, (int)$puerto);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>
