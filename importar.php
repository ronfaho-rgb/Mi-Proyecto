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
        
        // 1. Omitir la primera línea (encabezados)
        fgetcsv($file);

        $insertados = 0;
        $duplicados = 0;

        // 2. Leemos el CSV línea por línea
        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
            
            // Si la línea está vacía, saltar
            if (count($column) < 5) continue;

            // --- CORRECCIÓN DE COLUMNAS (Mapeo Real) ---
            // Ignoramos $column[0] porque es el ID que mueve todo
            $nombre         = $conexion->real_escape_string(trim($column[1]));
            $categoria      = $conexion->real_escape_string(trim($column[2]));
            $factura        = $conexion->real_escape_string(trim($column[3] ?? 'N/A'));
            $stock_inicial  = intval($column[4]); // Entrada Inicial
            $stock_actual   = intval($column[5]); // Stock Actual
            $precio_compra  = floatval($column[6]);
            $precio_venta   = floatval($column[7]);

            if (empty($nombre)) continue;

            // --- VALIDACIÓN DE DUPLICADOS ---
            $check = $conexion->query("SELECT id FROM productos WHERE nombre = '$nombre' LIMIT 1");
            
            if ($check->num_rows > 0) {
                $duplicados++;
            } else {
                // --- INSERTAR EN EL ORDEN DE TU BASE DE DATOS ---
                // nombre, categoria, factura, stock_inicial, precio_compra, precio_venta, stock
                $sql = "INSERT INTO productos (nombre, categoria, factura, stock_inicial, precio_compra, precio_venta, stock) 
                        VALUES ('$nombre', '$categoria', '$factura', $stock_inicial, $precio_compra, $precio_venta, $stock_actual)";
                
                if ($conexion->query($sql)) {
                    $insertados++;
                }
            }
        }
        fclose($file);
        
        $mensaje = "<div class='alert alert-success shadow-sm'>
                        ✅ <b>Carga finalizada:</b> $insertados nuevos, $duplicados omitidos.
                    </div>";
    } else {
        $mensaje = "<div class='alert alert-danger'>❌ El archivo no es válido.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Importar - Ebenezer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f9; font-family: 'Segoe UI', sans-serif; }
        .card { border-radius: 20px; border: none; }
        .btn-primary { background-color: #1a2c4e; border: none; border-radius: 12px; padding: 12px; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-body p-5 text-center">
                        <h3 class="fw-bold mb-3">📥 Restaurar Inventario</h3>
                        <p class="text-muted small mb-4">Se ha corregido la alineación de Producto y Categoría.</p>
                        
                        <?php echo $mensaje; ?>

                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="mb-4 text-start">
                                <label class="form-label fw-bold small text-uppercase">Seleccionar Archivo CSV</label>
                                <input type="file" name="archivo_excel" class="form-control form-control-lg" accept=".csv" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" name="importar" class="btn btn-primary fw-bold">Procesar Inventario Corregido</button>
                                <a href="productos.php" class="btn btn-link text-decoration-none text-muted mt-2">Ver Inventario</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>