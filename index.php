<?php
date_default_timezone_set('America/Managua');
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include("conexion.php");

// Limpieza de rol para asegurar la validación
$rol_usuario = strtolower(trim($_SESSION['rol'] ?? 'vendedor'));

// Consultas de datos
$total_prod = $conexion->query("SELECT COUNT(*) as total FROM productos")->fetch_assoc()['total'];
$stock_total = $conexion->query("SELECT SUM(stock) as total FROM productos")->fetch_assoc()['total'];
$stock_bajo = $conexion->query("SELECT COUNT(*) as total FROM productos WHERE stock <= 5")->fetch_assoc()['total'];

// Solo calculamos el valor si es admin para ahorrar recursos
$total_valor = 0;
if ($rol_usuario === 'admin') {
    $total_valor = $conexion->query("SELECT SUM(precio_venta * stock) as total FROM productos")->fetch_assoc()['total'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Librería Ebenezer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .card-custom { border: none; border-radius: 20px; color: white; padding: 25px; transition: 0.3s; height: 100%; }
        .btn-action { border-radius: 12px; padding: 12px; font-weight: bold; border: none; box-shadow: 0 4px 10px rgba(0,0,0,0.1); font-size: 0.9rem; }
        .navbar-ebenezer { background: #1a1a1a; padding: 15px; }
        .card-menu { border-radius: 20px; border: none; transition: 0.3s; height: 100%; }
        .card-menu:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-ebenezer mb-4 shadow">
    <div class="container">
        <span class="navbar-brand fw-bold">📦 Librería Ebenezer</span>
        <div class="text-white small">
            <span class="me-3">👤 <?php echo $_SESSION['usuario']; ?></span>
            <span class="badge bg-primary"><?php echo ucfirst($rol_usuario); ?></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm ms-3">Salir</a>
        </div>
    </div>
</nav>

<div class="container">
    <?php if ($rol_usuario === 'admin'): ?>
    <div class="row g-4 mb-5 text-center justify-content-center">
        <div class="col-md-3">
            <div class="card-custom shadow" style="background: linear-gradient(45deg, #00dbde, #fc00ff);">
                <h6>VARIEDAD</h6>
                <h2><?php echo $total_prod; ?></h2>
                <small>Productos Únicos</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-custom shadow" style="background: linear-gradient(45deg, #11998e, #38ef7d);">
                <h6>STOCK TOTAL</h6>
                <h2><?php echo number_format($stock_total); ?></h2>
                <small>Unidades en Estantería</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-custom shadow" style="background: linear-gradient(45deg, #ff9966, #ff5e62);">
                <h6>VALOR TOTAL</h6>
                <h3>C$ <?php echo number_format($total_valor, 2); ?></h3>
                <small>Inversión Estimada</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-custom shadow" style="background: linear-gradient(45deg, #ee0979, #ff6a00);">
                <h6>STOCK BAJO</h6>
                <h2><?php echo $stock_bajo; ?></h2>
                <small>Productos por Agotarse</small>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-4 text-center">
        <?php if ($rol_usuario === 'admin'): ?>
            <div class="col-md-3">
                <div class="card card-menu p-4 shadow-sm">
                    <div style="font-size: 45px;">📋</div>
                    <h4 class="fw-bold mt-2">Gestión</h4>
                    <p class="text-muted small">Consultar existencias.</p>
                    <a href="productos.php" class="btn btn-primary w-100 btn-action mt-auto">Ver Listado</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-menu p-4 shadow-sm">
                    <div style="font-size: 45px;">📥</div>
                    <h4 class="fw-bold mt-2">Entradas</h4>
                    <p class="text-muted small">Nueva mercadería.</p>
                    <a href="agregar.php" class="btn btn-success w-100 btn-action mt-auto">Nuevo Producto</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-menu p-4 shadow-sm">
                    <div style="font-size: 45px;">📤</div>
                    <h4 class="fw-bold mt-2">Excel</h4>
                    <p class="text-muted small">Importar masivamente.</p>
                    <a href="importar.php" class="btn btn-info text-white w-100 btn-action mt-auto">Subir Archivo</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-menu p-4 shadow-sm">
                    <div style="font-size: 45px;">📊</div>
                    <h4 class="fw-bold mt-2">Reportes</h4>
                    <p class="text-muted small">Análisis y gráficos.</p>
                    <a href="dashboard.php" class="btn btn-warning w-100 btn-action mt-auto">Ver Gráficos</a>
                </div>
            </div>
        <?php else: ?>
            <div class="col-md-4">
                <div class="card card-menu p-4 shadow-sm">
                    <div style="font-size: 45px;">📋</div>
                    <h4 class="fw-bold mt-2">Inventario</h4>
                    <p class="text-muted small">Consultar existencias disponibles.</p>
                    <a href="productos.php" class="btn btn-primary w-100 btn-action">Ver Listado</a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card p-4 shadow-sm border-0 d-flex align-items-center justify-content-center" style="border-radius: 20px; height: 100%;">
                    <h4 class="text-muted fw-bold">Modo Vendedor Activo</h4>
                    <p class="mb-0 text-center">Tienes acceso limitado a la consulta de productos y stock.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="text-center mt-5 mb-4 text-muted small">
    © 2026 Librería Ebenezer - Nicaragua
</footer>

</body>
</html>
