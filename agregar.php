<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
include("conexion.php"); 

$error_duplicado = false;

if ($_POST) {
    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $factura = $_POST['factura']; 
    $p_compra = $_POST['precio_compra'];
    $p_venta = $_POST['precio_venta'];
    // Capturamos el valor del nuevo campo "Entrada Inicial"
    $entrada_inicial = $_POST['stock_inicial'];

    // Limpieza de datos
    $nombre_db = $conexion->real_escape_string($nombre);
    $categoria_db = $conexion->real_escape_string($categoria);
    $factura_db = $conexion->real_escape_string($factura);

    // Verificar si ya existe
    $sql_check = "SELECT id FROM productos WHERE nombre = '$nombre_db'";
    $res_check = $conexion->query($sql_check);

    if ($res_check && $res_check->num_rows > 0) {
        $error_duplicado = true;
    } else {
        // Al registrar, la Entrada Inicial y el Stock Actual son el mismo valor
        $sql = "INSERT INTO productos (nombre, categoria, factura, stock_inicial, stock, precio_compra, precio_venta)
                VALUES ('$nombre_db', '$categoria_db', '$factura_db', $entrada_inicial, $entrada_inicial, $p_compra, $p_venta)";

        if ($conexion->query($sql)) {
            echo "<script>window.location.href='productos.php?success=1';</script>";
            exit(); 
        } else {
            echo "<div class='alert alert-danger'>Error: " . $conexion->error . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Producto - Ebenezer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f3f6; font-family: 'Segoe UI', sans-serif; }
        .card { border-radius: 20px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .btn-success { background-color: #16a085; border: none; }
        .label-blue { color: #0d6efd; }
        .border-blue { border: 2px solid #0d6efd; }
    </style>
</head>
<body class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h2 class="mb-4 fw-bold text-dark">➕ Registro de Nuevo Producto</h2>

                <?php if ($error_duplicado): ?>
                    <div class="alert alert-danger">El producto ya existe en el inventario.</div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Producto:</label>
                        <input class="form-control" type="text" name="nombre" placeholder="Ej: Lapiceros BIC" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Categoría:</label>
                            <input class="form-control" type="text" name="categoria" placeholder="Ej: Papelería" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">N° Factura:</label>
                            <input class="form-control" type="text" name="factura" placeholder="Opcional">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Precio Compra:</label>
                            <div class="input-group">
                                <span class="input-group-text">C$</span>
                                <input class="form-control" type="number" step="0.01" name="precio_compra" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Precio Venta:</label>
                            <div class="input-group">
                                <span class="input-group-text">C$</span>
                                <input class="form-control" type="number" step="0.01" name="precio_venta" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold label-blue">Entrada Inicial:</label>
                            <input class="form-control border-blue" type="number" name="stock_inicial" placeholder="Cantidad que entra" required>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-success px-5 fw-bold text-white">Guardar Producto</button>
                        <a href="productos.php" class="btn btn-outline-secondary px-4">Volver al Inventario</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>