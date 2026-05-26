<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include("conexion.php");

// Consulta de reporte
$sql = "SELECT 
    COUNT(*) as total_productos,
    SUM(stock) as total_stock,
    SUM(precio_venta * stock) as valor_total
FROM productos";

$resultado = $conexion->query($sql);
$datos = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>📊 Reporte de Inventario</h2>

<div class="alert alert-info">
    <p><strong>Total productos:</strong> <?php echo $datos['total_productos']; ?></p>
    <p><strong>Total stock:</strong> <?php echo $datos['total_stock']; ?></p>
    <p><strong>Valor total inventario:</strong> $<?php echo $datos['valor_total']; ?></p>
</div>

<a href="productos.php" class="btn btn-secondary">Volver</a>

</body>
</html>
