<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Escapar datos para evitar inyecciones SQL
    $usuario = $conexion->real_escape_string($_POST['usuario']);
    // Usar la misma función hash que usaste para crear las contraseñas
    $password = hash('sha256', $_POST['password']);

    // Ajusta 'usuario' si tu columna en la base de datos se llama 'username'
    $sql = "SELECT * FROM usuarios WHERE usuario='$usuario' AND password='$password'";
    $resultado = $conexion->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $datos = $resultado->fetch_assoc();
        
        session_regenerate_id(true);
        
        $_SESSION['usuario'] = $datos['usuario'];
        $_SESSION['rol'] = $datos['rol']; 

        header("Location: index.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>
