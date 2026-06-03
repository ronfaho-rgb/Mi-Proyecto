<?php
date_default_timezone_set('America/Managua');
session_start();
if (!isset($_SESSION['usuario']) || strtolower(trim($_SESSION['rol'] ?? '')) !== 'admin') {
    header("Location: index.php");
    exit();
}
include("conexion.php");

$mensaje = "";

if (isset($_POST["importar"])) {
    $filename = $_FILES["archivo_excel"]["tmp_name"];
    if ($_FILES["archivo_excel"]["size"] > 0) {
        $file = fopen($filename, "r");
        fgetcsv($file); // Saltamos encabezados

        $actualizados = 0;
        $insertados = 0;

        while (($col = fgetcsv($file, 10000, ",")) !== FALSE) {
            if (count($col) < 5) continue;

            $nombre        = $conexion->real_escape_string(trim($col[1]));
            $categoria     = $conexion->real_escape_string(trim($col[2]));
            $factura       = $conexion->real_escape_string(trim($col[3] ?? 'N/A'));
            $s_inicial     = intval(preg_replace('/[^0-9]/', '', $col[4]));
            $s_actual      = intval(preg_replace('/[^0-9]/', '', $col[5]));
            $p_compra      = floatval(preg_replace('/[^0-9.]/', '', $col[6]));
            $p_venta       = floatval(preg_replace('/[^0-9.]/', '', $col[7]));

            if (empty($nombre)) continue;

            // BUSCAR SI EXISTE PARA ACTUALIZAR (Evita duplicados)
            $check = $conexion->query("SELECT id FROM productos WHERE nombre = '$nombre' LIMIT 1");
            
            if ($check->num_rows > 0) {
                // YA EXISTE: Actualizamos
                $conexion->query("UPDATE productos SET categoria='$categoria', factura='$factura', stock_inicial=$s_inicial, precio_compra=$p_compra, precio_venta=$p_venta, stock=$s_actual WHERE nombre='$nombre'");
                $actualizados++;
            } else {
                // NO EXISTE: Insertamos
                $conexion->query("INSERT INTO productos (nombre, categoria, factura, stock_inicial, precio_compra, precio_venta, stock) VALUES ('$nombre', '$categoria', '$factura', $s_inicial, $p_compra, $p_venta, $s_actual)");
                $insertados++;
            }
        }
        fclose($file);
        $mensaje = "<div class='alert alert-success'>✅ <b>Proceso terminado:</b> $insertados nuevos, $actualizados actualizados.</div>";
    }
}
?>
<!-- (Mantén aquí el mismo HTML de tu archivo importar.php) -->
