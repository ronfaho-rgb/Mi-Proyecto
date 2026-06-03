<?php
date_default_timezone_set('America/Managua');
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
include("conexion.php");

// 1. Datos para Gráfica de Barras (Solo Físicos)
$sql_grafica = "SELECT nombre, stock FROM productos WHERE es_servicio = 0 ORDER BY stock DESC LIMIT 10";
$res_grafica = $conexion->query($sql_grafica);
$nombres = []; $cantidades = [];
if($res_grafica){
    while($row = $res_grafica->fetch_assoc()){
        $nombres[] = $row['nombre'];
        $cantidades[] = $row['stock'];
    }
}

// 2. Datos para Gráfica de Dona (Solo Físicos)
$sql_cat = "SELECT categoria, SUM(stock) as total FROM productos WHERE es_servicio = 0 GROUP BY categoria";
$res_cat = $conexion->query($sql_cat);
$cat_nombres = []; $cat_totales = [];
if($res_cat){
    while($row = $res_cat->fetch_assoc()){
        $cat_nombres[] = $row['categoria'];
        $cat_totales[] = $row['total'];
    }
}

// 3. Resumen de Valor (Físicos)
$res_valor = $conexion->query("SELECT SUM(precio_venta * stock) as total FROM productos WHERE es_servicio = 0");
$total_valor = ($res_valor && ($row = $res_valor->fetch_assoc())) ? (float)$row['total'] : 0.00;

// 4. CORRECCIÓN: Datos de Ventas de Servicios (Solo lo vendido HOY)
$alquiler_costo = 6537.24;
// Se añadió el filtro DATE(fecha) = CURDATE() para que coincida con tus ventas diarias
$sql_ingresos_servicios = "SELECT IFNULL(SUM(total_venta), 0) as total FROM ventas WHERE es_servicio = 1 AND DATE(fecha) = CURDATE()";
$res_servicios = $conexion->query($sql_ingresos_servicios);
$total_servicios = 0.00;
if ($res_servicios) {
    $row_serv = $res_servicios->fetch_assoc();
    $total_servicios = (float)$row_serv['total'];
}

$diferencia_rentabilidad = $total_servicios - $alquiler_costo;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte - Librería Ebenezer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8f9fa; font-family: sans-serif; }
        .card { border-radius: 15px; border: none; }
        .chart-container { height: 250px; }
        .fs-7 { font-size: 0.75rem; }
    </style>
</head>
<body class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 shadow-sm rounded">
        <h4 class="m-0 fw-bold">📊 Análisis de Inventario</h4>
        <a href="index.php" class="btn btn-outline-dark btn-sm">Inicio</a>
    </div>

    <div class="row g-3 mb-4 text-center">
        <div class="col-md-3">
            <div class="card p-3 shadow-sm" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
                <small class="text-uppercase fs-7 opacity-75">Inversión Venta</small>
                <h5 class="fw-bold mb-0">C$ <?php echo number_format($total_valor, 2); ?></h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm bg-white">
                <small class="text-uppercase text-muted fs-7">Ventas Servicios (HOY)</small>
                <h5 class="fw-bold mb-0 text-dark">C$ <?php echo number_format($total_servicios, 2); ?></h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm bg-white">
                <small class="text-uppercase text-muted fs-7">Estado Impresora</small>
                <h5 class="fw-bold mb-0 <?php echo ($diferencia_rentabilidad >= 0) ? 'text-success' : 'text-danger'; ?>">
                    C$ <?php echo number_format($diferencia_rentabilidad, 2); ?>
                </h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm bg-white">
                <small class="text-uppercase text-muted fs-7">Alquiler: C$ <?php echo number_format($alquiler_costo, 2); ?></small>
                <a href="descargar_inventario.php" class="btn btn-sm btn-info text-white fw-bold mt-1">Exportar Excel</a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-5"><div class="card p-3 shadow-sm"><canvas id="graficaCategorias" class="chart-container"></canvas></div></div>
        <div class="col-md-7"><div class="card p-3 shadow-sm"><canvas id="graficaStock" class="chart-container"></canvas></div></div>
    </div>

    <script>
        new Chart(document.getElementById('graficaStock'), {
            type: 'bar',
            data: { labels: <?php echo json_encode($nombres); ?>, datasets: [{ data: <?php echo json_encode($cantidades); ?>, backgroundColor: '#667eea' }] },
            options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
        new Chart(document.getElementById('graficaCategorias'), {
            type: 'doughnut',
            data: { labels: <?php echo json_encode($cat_nombres); ?>, datasets: [{ data: <?php echo json_encode($cat_totales); ?>, backgroundColor: ['#667eea', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'] }] },
            options: { maintainAspectRatio: false }
        });
    </script>
</body>
</html>
