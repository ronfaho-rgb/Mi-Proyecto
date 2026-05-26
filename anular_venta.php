<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    die("Acceso denegado. Solo el administrador puede anular ventas.");
}
include("conexion.php");

if (isset($_GET['id'])) {
    $id_venta = $_GET['id'];

    // 1. Obtener los datos de la venta antes de borrarla
    $sql_venta = "SELECT id_producto, cantidad FROM ventas WHERE id = $id_venta";
    $res_venta = $conexion->query($sql_venta);

    if ($res_venta->num_rows > 0) {
        $datos = $res_venta->fetch_assoc();
        $id_prod = $datos['id_producto'];
        $cantidad_vendida = $datos['cantidad'];

        // 2. Devolver el stock al producto
        $update_stock = "UPDATE productos SET stock = stock + $cantidad_vendida WHERE id = $id_prod";
        
        if ($conexion->query($update_stock)) {
            // 3. Ahora sí, eliminar el registro de la venta
            $delete_venta = "DELETE FROM ventas WHERE id = $id_venta";
            $conexion->query($delete_venta);
            
            header("Location: historial_ventas.php?msj=Venta anulada y stock recuperado");
        }
    }
}
?>