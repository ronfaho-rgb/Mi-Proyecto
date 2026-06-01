<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Credenciales EXTERNAS reales de Railway con el nuevo puerto
$host     = "kodama.proxy.rlwy.net";
$usuario  = "root"; 
$password = "MJWluisAMAblEzNdnRRbXltbnPrRLlTx"; 
$db_name  = "railway"; 
$puerto   = 17247; // Puerto externo asignado por Railway

// Conexión incluyendo el puerto externo correcto
$conexion = new mysqli($host, $usuario, $password, $db_name, $puerto);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
