<?php
date_default_timezone_set('America/Managua');
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
include("conexion.php");

// 1. Datos para Gráfica de Barras (Top 10 Productos - Solo Físicos)
$sql_grafica = "SELECT nombre, stock FROM productos WHERE es_servicio = 0 ORDER BY stock DESC LIMIT 10";
$res_grafica = $conexion->query($sql_grafica);
$nombres = []; $cantidades = [];
if($res_grafica){
    while($row = $res_grafica->fetch_assoc()){
        $nombres[] = $row['nombre'];
        $cantidades[] = $row['stock'];
    }
}

// 2. Datos para Gráfica de Dona (Distribución por Categoría - Solo Físicos)
$sql_cat = "SELECT categoria, SUM(stock) as total FROM productos WHERE es_servicio = 0 GROUP BY categoria";
$res_cat = $conexion->query($sql_cat);
$cat_nombres = []; $cat_totales = [];
if($res_cat){
    while($row = $res_cat->fetch_assoc()){
        $cat_nombres[] = $row['categoria'];
        $cat_totales[] = $row['total'];
    }
}

// 3. Resumen de Valor (Inversión Total en Venta - Solo Físicos)
$res_valor = $conexion->query("SELECT SUM(precio_venta * stock) as total FROM productos WHERE es_servicio = 0");
$total_valor = ($res_valor) ? $res_valor->fetch_assoc()['total'] : 0;

// 4. Datos del Alquiler y Servicios
$alquiler_costo = 6537.24;
$sql_ingresos_servicios = "SELECT SUM(total_venta) as total_servicios FROM ventas WHERE es_servicio = 1";
$res_servicios = $conexion->query($sql_ingresos_servicios);
$total_servicios = ($res_servicios) ? $res_servicios->fetch_assoc()['total_servicios'] : 0;
$diferencia_rentabilidad = $total_servicios - $alquiler_costo;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Visual - Librería Ebenezer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-custom { background: white; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .card { border: none; border-radius: 20px; transition: 0.3s; }
        .report-card { background: white; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .chart-container { position: relative; height: 320px; width: 100%; }
        .gradient-custom { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 20px; }
        .bg-alquiler { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .btn-exportar { background-color: #00d1ff; border: none; color: white; font-weight: bold; }
    </style>
</head>
<body class="container py-4">

    <div class="navbar-custom d-flex justify-content-between align-items-center p-3 mb-4">
        <div class="d-flex align-items-center">
            <a href="index.php" class="btn btn-dark btn-sm me-3">🏠 Inicio</a>
            <h4 class="m-0 fw-bold">Análisis de Inventario</h4>
        </div>
        <div class="text-end">
            <span class="badge bg-primary px-3 py-2">Usuario: <?php echo $_SESSION['usuario']; ?></span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card gradient-custom p-4 shadow h-100 text-center">
                <h6>Inversión Venta</h6>
                <h3 class="fw-bold">C$ <?php echo number_format($total_valor, 2); ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-alquiler p-4 shadow h-100 text-center">
                <h6>Ventas Servicios</h6>
                <h3 class="fw-bold">C$ <?php echo number_format($total_servicios, 2); ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 shadow h-100 text-center bg-white">
                <h6>Estado Impresora</h6>
                <h3 class="fw-bold <?php echo ($diferencia_rentabilidad >= 0) ? 'text-success' : 'text-danger'; ?>">
                    C$ <?php echo number_format($diferencia_rentabilidad, 2); ?>
                </h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 shadow h-100 text-center bg-white">
                <p class="small text-muted">Costo Alquiler: C$ <?php echo number_format($alquiler_costo, 2); ?></p>
                <a href="descargar_inventario.php" class="btn btn-exportar w-100">Exportar Excel</a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-5"><div class="card report-card h-100"><canvas id="graficaCategorias"></canvas></div></div>
        <div class="col-md-7"><div class="card report-card h-100"><canvas id="graficaStock"></canvas></div></div>
    </div>

    <script>
        new Chart(document.getElementById('graficaStock').getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($nombres); ?>,
                datasets: [{ data: <?php echo json_encode($cantidades); ?>, backgroundColor: '#4e73df' }]
            }, options: { maintainAspectRatio: false }
        });
        new Chart(document.getElementById('graficaCategorias').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($cat_nombres); ?>,
                datasets: [{ data: <?php echo json_encode($cat_totales); ?>, backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'] }]
            }, options: { maintainAspectRatio: false }
        });
    </script>
</body>
</html>
