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

// Consultas de datos (Filtrando servicios con es_servicio = 0)
$total_prod = $conexion->query("SELECT COUNT(*) as total FROM productos WHERE es_servicio = 0")->fetch_assoc()['total'];
$stock_total = $conexion->query("SELECT SUM(stock) as total FROM productos WHERE es_servicio = 0")->fetch_assoc()['total'];
$stock_bajo = $conexion->query("SELECT COUNT(*) as total FROM productos WHERE stock <= 5 AND es_servicio = 0")->fetch_assoc()['total'];

// Solo calculamos si es admin para ahorrar recursos
$total_valor = 0;
$ganancia_total = 0;
if ($rol_usuario === 'admin') {
    $total_valor = $conexion->query("SELECT SUM(precio_venta * stock) as total FROM productos WHERE es_servicio = 0")->fetch_assoc()['total'];
    $ganancia_total = $conexion->query("SELECT SUM((precio_venta - precio_compra) * stock) as total FROM productos WHERE es_servicio = 0")->fetch_assoc()['total'];
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
        .card-custom { border: none; border-radius: 20px; color: white; padding: 20px 10px; transition: 0.3s; height: 100%; }
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

<div class="container-fluid px-4">
    <?php if ($rol_usuario === 'admin'): ?>
    <div class="row g-3 mb-5 text-center justify-content-center">
        <!-- Tarjetas ahora ocupan col-md-2 para caber las 5 -->
        <div class="col-md-2">
            <div class="card-custom shadow" style="background: linear-gradient(45deg, #00dbde, #fc00ff);">
                <small>VARIEDAD</small>
                <h5><?php echo $total_prod; ?></h5>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card-custom shadow" style="background: linear-gradient(45deg, #11998e, #38ef7d);">
                <small>STOCK TOTAL</small>
                <h5><?php echo number_format($stock_total); ?></h5>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card-custom shadow" style="background: linear-gradient(45deg, #ff9966, #ff5e62);">
                <small>VALOR VENTA</small>
                <h6>C$ <?php echo number_format($total_valor, 2); ?></h6>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card-custom shadow" style="background: linear-gradient(45deg, #6a11cb, #2575fc);">
                <small>GANANCIA</small>
                <h6>C$ <?php echo number_format($ganancia_total, 2); ?></h6>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card-custom shadow" style="background: linear-gradient(45deg, #ee0979, #ff6a00);">
                <small>STOCK BAJO</small>
                <h5><?php echo $stock_bajo; ?></h5>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-4 text-center justify-content-center">
        <?php if ($rol_usuario === 'admin'): ?>
            <div class="col-md-3">
                <div class="card card-menu p-4 shadow-sm">
                    <div style="font-size: 45px;">📋</div>
                    <h4 class="fw-bold mt-2">Gestión</h4>
                    <a href="productos.php" class="btn btn-primary w-100 btn-action mt-auto">Ver Listado</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-menu p-4 shadow-sm">
                    <div style="font-size: 45px;">📥</div>
                    <h4 class="fw-bold mt-2">Entradas</h4>
                    <a href="agregar.php" class="btn btn-success w-100 btn-action mt-auto">Nuevo Producto</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-menu p-4 shadow-sm">
                    <div style="font-size: 45px;">📤</div>
                    <h4 class="fw-bold mt-2">Excel</h4>
                    <a href="importar.php" class="btn btn-info text-white w-100 btn-action mt-auto">Subir Archivo</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-menu p-4 shadow-sm">
                    <div style="font-size: 45px;">📊</div>
                    <h4 class="fw-bold mt-2">Reportes</h4>
                    <a href="dashboard.php" class="btn btn-warning w-100 btn-action mt-auto">Ver Gráficos</a>
                </div>
            </div>
        <?php else: ?>
            <div class="col-md-4">
                <div class="card card-menu p-4 shadow-sm">
                    <h4 class="fw-bold">Inventario</h4>
                    <a href="productos.php" class="btn btn-primary w-100 btn-action">Ver Listado</a>
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
