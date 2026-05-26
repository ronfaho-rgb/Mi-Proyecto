<?php
date_default_timezone_set('America/Managua');
session_start();
include("conexion.php");

if (isset($_POST["importar"])) {
    $archivo = $_FILES["archivo_excel"]["tmp_name"];
    if ($archivo) {
        $file = fopen($archivo, "r");
        fgetcsv($file); // Saltar encabezados

        while (($datos = fgetcsv($file, 0, ",")) !== FALSE) {
            if (count($datos) < 5) continue;

            // MAPEADO SEGÚN TU TABLA (image_8f5282.png)
            // $datos[1] es el Nombre real, $datos[2] es la Categoría real
            $nombre    = $conexion->real_escape_string(trim($datos[1]));
            $categoria = $conexion->real_escape_string(trim($datos[2]));
            $factura   = $conexion->real_escape_string(trim($datos[3]));
            
            // Convertir a números para evitar el error "Deprecated: number_format"
            $s_inicial = intval(preg_replace('/[^0-9]/', '', $datos[4]));
            $s_actual  = intval(preg_replace('/[^0-9]/', '', $datos[5]));
            $p_compra  = floatval(preg_replace('/[^0-9.]/', '', $datos[6]));
            $p_venta   = floatval(preg_replace('/[^0-9.]/', '', $datos[7]));

            if (!empty($nombre)) {
                // INSERTAR USANDO LOS NOMBRES DE COLUMNA DE TU IMAGEN
                $sql = "INSERT INTO productos (nombre, categoria, factura, stock_inicial, precio_compra, precio_venta, stock) 
                        VALUES ('$nombre', '$categoria', '$factura', $s_inicial, $p_compra, $p_venta, $s_actual)";
                $conexion->query($sql);
            }
        }
        fclose($file);
        echo "<script>alert('Inventario Alineado Correctamente'); window.location='productos.php';</script>";
    }
}
?>