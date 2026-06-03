<?php
date_default_timezone_set('America/Managua');
session_start();
if (!isset($_SESSION['usuario'])) { header("Location: login.php"); exit(); }
include("conexion.php");

$venta_exitosa = false;
$error = "";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = $conexion->query("SELECT * FROM productos WHERE id = $id");
    $p = $res->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_prod = intval($_POST['id']);
    $cantidad_venta = intval($_POST['cantidad']);
    
    // Obtenemos los datos ACTUALIZADOS del producto
    $prod_res = $conexion->query("SELECT nombre, precio_venta, es_servicio, stock FROM productos WHERE id = $id_prod");
    $p = $prod_res->fetch_assoc();
    
    $precio = $p['precio_venta'];
    $nombre_p = $p['nombre'];
    $stock_actual = $p['stock'];
    $es_servicio = intval($p['es_servicio']); // ESTE es el valor que define si es servicio
    
    $vendedor = $_SESSION['usuario'];
    $total = $cantidad_venta * $precio;

    // Lógica: Si es servicio ($es_servicio == 1), permitimos venta sin validar stock
    $puede_vender = ($es_servicio == 1) ? true : ($cantidad_venta > 0 && $cantidad_venta <= $stock_actual);

    if ($puede_vender) {
        // 1. Restar Stock SOLO si NO es servicio
        if ($es_servicio == 0) {
            $nueva_cantidad = $stock_actual - $cantidad_venta;
            $conexion->query("UPDATE productos SET stock = $nueva_cantidad WHERE id = $id_prod");
        }

        // 2. INSERTAR EN VENTAS (Forzamos el valor de $es_servicio)
        $sql_venta = "INSERT INTO ventas (producto_id, nombre_producto, cantidad, total_venta, vendedor, es_servicio) 
                      VALUES ($id_prod, '$nombre_p', $cantidad_venta, $total, '$vendedor', $es_servicio)";
        
        if ($conexion->query($sql_venta)) {
            $venta_exitosa = true;
        } else {
            $error = "Error al guardar en base de datos: " . $conexion->error;
        }
    } else {
        $error = "Stock insuficiente.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Venta - Ebenezer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">
<div class="container mt-5">
    <?php if ($venta_exitosa): ?>
        <script>
            Swal.fire({ icon: 'success', title: 'Venta Registrada', text: 'Total: C$ <?php echo number_format($total,2); ?>', confirmButtonText: 'Ok' })
            .then(() => { window.location.href = 'productos.php'; });
        </script>
    <?php endif; ?>
    <?php if ($error != ""): ?>
        <script>Swal.fire({ icon: 'error', title: 'Error', text: '<?php echo $error; ?>' });</script>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow border-0" style="border-radius: 20px;">
                <div class="card-body p-4 text-center">
                    <h3 class="fw-bold mb-4">🛒 <?php echo ($p['es_servicio'] == 1) ? 'Registrar Servicio' : 'Vender Producto'; ?></h3>
                    <h5 class="text-primary"><?php echo $p['nombre']; ?></h5>
                    <hr>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                        <input type="hidden" name="stock_actual" value="<?php echo $p['stock']; ?>">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Cantidad:</label>
                            <input type="number" name="cantidad" class="form-control form-control-lg text-center" value="1" min="1" <?php echo ($p['es_servicio'] == 0) ? 'max="'.$p['stock'].'"' : ''; ?> autofocus required>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold mb-2">Confirmar Venta</button>
                        <a href="productos.php" class="btn btn-link text-muted">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
