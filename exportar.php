<?php
include("conexion.php");

// Cabeceras para descargar como Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=productos.xls");

// Consulta
$sql = "SELECT * FROM productos";
$resultado = $conexion->query($sql);

// Encabezados
echo "ID\tNombre\tCategoria\tPrecio Compra\tPrecio Venta\tStock\n";

// Datos
while($fila = $resultado->fetch_assoc()){
    echo $fila['id']."\t".
         $fila['nombre']."\t".
         $fila['categoria']."\t".
         $fila['precio_compra']."\t".
         $fila['precio_venta']."\t".
         $fila['stock']."\n";
}
?>
