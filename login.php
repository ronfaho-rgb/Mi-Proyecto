<?php
// Forzamos que la sesión se inicie limpiamente
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("conexion.php");

// --- INICIO DE REPARACIÓN AUTOMÁTICA ---
// Esta línea actualizará la base de datos con el formato correcto de SHA256 que tu servidor necesita
$clave_reparar = hash('sha256', 'Abril2026**');
$conexion->query("UPDATE usuarios SET password='$clave_reparar' WHERE usuario='admin'");
// --- FIN DE REPARACIÓN ---

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario = $conexion->real_escape_string($_POST['usuario']);
    $password = hash('sha256', $_POST['password']);

    // Traemos todos los datos, incluyendo la columna 'rol'
    $sql = "SELECT * FROM usuarios WHERE usuario='$usuario' AND password='$password'";
    $resultado = $conexion->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $datos = $resultado->fetch_assoc();
        
        // REGENERAR ID DE SESIÓN (Seguridad)
        session_regenerate_id(true);
        
        $_SESSION['usuario'] = $datos['usuario'];
        $_SESSION['rol'] = $datos['rol']; 

        // Redirigimos al index
        header("Location: index.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Librería Ebenezer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: sans-serif; }
        .login-card { width: 100%; max-width: 400px; padding: 40px; border-radius: 25px; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.1); background: white; }
        .btn-primary { background: #4e73df; border: none; border-radius: 12px; transition: 0.3s; }
        .btn-primary:hover { background: #2e59d9; transform: scale(1.02); }
        .form-control { border-radius: 10px; padding: 12px; border: 1px solid #d1d3e2; }
    </style>
</head>
<body>

<div class="card login-card">
    <div class="text-center mb-4">
        <div style="font-size: 50px;">📦</div>
        <h2 class="fw-bold text-dark">Librería Ebenezer</h2>
        <p class="text-muted">Acceso al Sistema</p>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger p-2 text-center" style="font-size: 0.85rem; border-radius: 10px;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label small fw-bold">Nombre de Usuario</label>
            <input type="text" name="usuario" class="form-control" placeholder="Escribe tu usuario" required autofocus>
        </div>
        <div class="mb-4">
            <label class="form-label small fw-bold">Contraseña</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 fw-bold p-3 shadow-sm">
            Entrar ahora
        </button>
    </form>
</div>

</body>
</html>