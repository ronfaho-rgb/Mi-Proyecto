<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Credenciales nuevas de tu base de datos en Railway
$host     = "mysql.railway.internal";
$usuario  = "root"; // En Railway el usuario por defecto casi siempre es root
$password = "MJWluisAMAblEzNdnRRbXltbnPrRLlTx";
$db_name  = "railway"; // En Railway la base de datos automática se llama railway
$puerto   = 3306; // El puerto estándar de MySQL

// Conexión incluyendo el parámetro del puerto al final para Railway
$conexion = new mysqli($host, $usuario, $password, $db_name, $puerto);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
