<?php
date_default_timezone_set('America/Managua');
session_start();
if (!isset($_SESSION['usuario']) || strtolower(trim($_SESSION['rol'] ?? '')) !== 'admin') {
    header("Location: index.php");
    exit();
}
include("conexion.php");

// --- LÓGICA PARA ANULAR VENTA ---
if (isset($_GET['anular_id'])) {
    $id_venta = intval($_GET['anular_id']);
    
    $consulta = $conexion->query("SELECT producto_id, cantidad FROM ventas WHERE id = $id_venta");
    
    if ($consulta && $consulta->num_rows > 0) {
        $v = $consulta->fetch_assoc();
        $id_prod = $v['producto_id'];
        $cant = $v['cantidad'];
        
        $conexion->query("UPDATE productos SET stock = stock + $cant WHERE id = $id_prod");
        $conexion->query("DELETE FROM ventas WHERE id = $id_venta");
        
        echo "<script>window.location='reporte_ventas.php?msj=ok';</script>";
        exit();
    }
}

// Consultas de Ventas (Caja) - Detecta automáticamente el nombre de la columna
$columna = ($conexion->query("SHOW COLUMNS FROM ventas LIKE 'total_venta'")->num_rows > 0) ? "total_venta" : "total";

$hoy = $conexion->query("SELECT SUM($columna) as total FROM ventas WHERE DATE(fecha) = CURDATE()")->fetch_assoc()['total'] ?? 0;
$mes = $conexion->query("SELECT SUM($columna) as total FROM ventas WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())")->fetch_assoc()['total'] ?? 0;
$anio = $conexion->query("SELECT SUM($columna) as total FROM ventas WHERE YEAR(fecha) = YEAR(CURDATE())")->fetch_assoc()['total'] ?? 0;

$total_valor = $conexion->query("SELECT SUM(precio_compra * stock) as total FROM productos")->fetch_assoc()['total'] ?? 0;

$ultimas_ventas = $conexion->query("SELECT * FROM ventas ORDER BY fecha DESC LIMIT 20");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Corte de Caja - Ebenezer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f3f6; font-family: 'Segoe UI', sans-serif; }
        .header-top { background: white; border-radius: 15px; padding: 15px 25px; margin-top: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .main-banner { background-color: #1a2c4e; border-radius: 25px; color: white; padding: 30px; margin-top: 20px; text-align: center; box-shadow: 0 10px 30px rgba(26, 44, 78, 0.15); }
        .stat-card { border: none; border-radius: 20px; color: white; padding: 25px; box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
        .bg-profesional-1 { background-color: #2c3e50; } 
        .bg-profesional-2 { background-color: #16a085; } 
        .bg-profesional-3 { background-color: #c0392b; } 
        .table-container { background: white; border-radius: 25px; padding: 30px; margin-top: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
    </style>
</head>
<body class="container">
    <div class="header-top d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <a href="productos.php" class="btn btn-dark btn-sm me-3">📦 Ver Inventario</a>
            <h5 class="fw-bold m-0">Balance de Ingresos</h5>
        </div>
        <div>
            <?php if(isset($_GET['msj'])): ?>
                <span class="badge bg-success me-3">Venta anulada correctamente</span>
            <?php endif; ?>
            <span class="badge bg-primary rounded-pill px-3">Administrador</span>
        </div>
    </div>

    <div class="main-banner">
        <div class="row align-items-center">
            <div class="col-md-6 border-end border-secondary">
                <p class="text-uppercase small mb-1 opacity-75">Inversión Actual en Stock</p>
                <h1 class="fw-bold">C$ <?php echo number_format($total_valor, 2); ?></h1>
            </div>
            <div class="col-md-6">
                <p class="text-uppercase small mb-1 opacity-75">Estado del Sistema</p>
                <h1 class="fw-bold text-success">OPERATIVO</h1>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-md-4">
            <div class="stat-card bg-profesional-1 text-center">
                <h6 class="text-uppercase small opacity-75">Ventas de Hoy</h6>
                <h2 class="fw-bold">C$ <?php echo number_format($hoy, 2); ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-profesional-2 text-center">
                <h6 class="text-uppercase small opacity-75">Total Mes</h6>
                <h2 class="fw-bold">C$ <?php echo number_format($mes, 2); ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-profesional-3 text-center">
                <h6 class="text-uppercase small opacity-75">Anual</h6>
                <h2 class="fw-bold">C$ <?php echo number_format($anio, 2); ?></h2>
            </div>
        </div>
    </div>

    <div class="table-container mb-5">
        <h5 class="fw-bold mb-4">📜 Detalle de Ventas Recientes</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light text-uppercase small">
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-center">Vendedor</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($v = $ultimas_ventas->fetch_assoc()): 
                        $monto = $v['total_venta'] ?? $v['total'];
                    ?>
                    <tr>
                        <td class="small text-muted"><?php echo date('d/m H:i', strtotime($v['fecha'])); ?></td>
                        <td class="fw-bold"><?php echo $v['nombre_producto']; ?></td>
                        <td class="text-center"><?php echo $v['cantidad']; ?></td>
                        <td class="text-end fw-bold text-success">C$ <?php echo number_format($monto, 2); ?></td>
                        <td class="text-center small"><?php echo $v['vendedor']; ?></td>
                        <td class="text-center">
                            <a href="reporte_ventas.php?anular_id=<?php echo $v['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Anular esta venta?');">X</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
