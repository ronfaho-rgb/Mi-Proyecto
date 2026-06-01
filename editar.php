<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
include("conexion.php");

// 1. Obtener los datos actuales del producto
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM productos WHERE id = $id";
    $resultado = $conexion->query($sql);
    $producto = $resultado->fetch_assoc();
}

// 2. Lógica para guardar los cambios
if ($_POST) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $factura = $_POST['factura']; 
    $p_compra = $_POST['precio_compra'];
    $p_venta = $_POST['precio_venta'];
    $stock = $_POST['stock'];
    $stock_inicial = $_POST['stock_inicial']; 

    $nombre_db = $conexion->real_escape_string($nombre);
    $categoria_db = $conexion->real_escape_string($categoria);
    $factura_db = $conexion->real_escape_string($factura);

    $sql_update = "UPDATE productos SET 
                    nombre='$nombre_db', 
                    categoria='$categoria_db', 
                    factura='$factura_db', 
                    precio_compra=$p_compra, 
                    precio_venta=$p_venta, 
                    stock=$stock,
                    stock_inicial=$stock_inicial 
                   WHERE id=$id";

    if ($conexion->query($sql_update)) {
        header("Location: productos.php?success=1");
        exit();
    } else {
        $error = "Error al actualizar: " . $conexion->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto - Ebenezer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f3f6; font-family: 'Segoe UI', sans-serif; }
        .card { border-radius: 20px; border: none; }
        .btn-warning { background-color: #f39c12; border: none; font-weight: bold; }
    </style>
</head>
<body class="container mt-5">
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark py-3">
                <h4 class="m-0 fw-bold">📝 Editar Producto: <?php echo $producto['nombre']; ?></h4>
            </div>
            <div class="card-body p-4">
                <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Producto:</label>
                        <input class="form-control" type="text" name="nombre" value="<?php echo $producto['nombre']; ?>" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Categoría:</label>
                            <input class="form-control" type="text" name="categoria" value="<?php echo $producto['categoria']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Número de Factura:</label>
                            <input class="form-control" type="text" name="factura" value="<?php echo $producto['factura']; ?>">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-primary">Entrada Inicial (Stock Inicial):</label>
                            <input class="form-control border-primary" type="number" name="stock_inicial" value="<?php echo $producto['stock_inicial']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-success">Stock Actual (Físico):</label>
                            <input class="form-control border-success" type="number" name="stock" value="<?php echo $producto['stock']; ?>" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Precio Compra:</label>
                            <div class="input-group">
                                <span class="input-group-text">C$</span>
                                <input class="form-control" type="number" step="0.01" name="precio_compra" value="<?php echo $producto['precio_compra']; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Precio Venta:</label>
                            <div class="input-group">
                                <span class="input-group-text">C$</span>
                                <input class="form-control" type="number" step="0.01" name="precio_venta" value="<?php echo $producto['precio_venta']; ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning px-5">Guardar Cambios</button>
                        <a href="productos.php" class="btn btn-secondary px-4">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
