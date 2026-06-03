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
    
    // Obtenemos los datos ACTUALIZADOS del producto, incluyendo es_servicio
    $prod_res = $conexion->query("SELECT nombre, precio_venta, es_servicio, stock FROM productos WHERE id = $id_prod");
    $p = $prod_res->fetch_assoc();
    
    $precio = $p['precio_venta'];
    $nombre_p = $p['nombre'];
    $stock_actual = $p['stock'];
    // Aquí forzamos que sea un entero (0 o 1)
    $es_servicio = (isset($p['es_servicio'])) ? intval($p['es_servicio']) : 0;
    
    $vendedor = $_SESSION['usuario'];
    $total = $cantidad_venta * $precio;

    $puede_vender = ($es_servicio == 1) ? true : ($cantidad_venta > 0 && $cantidad_venta <= $stock_actual);

    if ($puede_vender) {
        if ($es_servicio == 0) {
            $nueva_cantidad = $stock_actual - $cantidad_venta;
            $conexion->query("UPDATE productos SET stock = $nueva_cantidad WHERE id = $id_prod");
        }

        // INSERT A LA TABLA VENTAS
        $sql_venta = "INSERT INTO ventas (producto_id, nombre_producto, cantidad, total_venta, vendedor, es_servicio) 
                      VALUES ($id_prod, '$nombre_p', $cantidad_venta, $total, '$vendedor', $es_servicio)";
        
        if ($conexion->query($sql_venta)) {
            $venta_exitosa = true;
        } else {
            $error = "Error al guardar: " . $conexion->error;
        }
    } else {
        $error = "Stock insuficiente.";
    }
}
?>
