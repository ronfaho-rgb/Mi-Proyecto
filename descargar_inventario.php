<?php
include("conexion.php");

// Nombre del archivo con la fecha actual
$filename = "Inventario_Ebenezer_" . date('Y-m-d') . ".csv";

// Configurar cabeceras para la descarga
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=$filename");

// Abrir el puntero de salida
$output = fopen("php://output", "w");

// Agregar BOM para que Excel reconozca los acentos (UTF-8)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Definir los encabezados de las columnas según tu tabla
fputcsv($output, array('ID', 'Producto', 'Categoría', 'N° Factura', 'Entrada Inicial', 'Stock Actual', 'P. Compra', 'P. Venta'));

// Consultar los productos
$query = "SELECT id, nombre, categoria, factura, stock_inicial, stock, precio_compra, precio_venta FROM productos";
$result = $conexion->query($query);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>