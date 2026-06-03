<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('America/Managua');
session_start();

if (!isset($_SESSION['usuario']) || strtolower(trim($_SESSION['rol'] ?? '')) !== 'admin') {
    header("Location: index.php");
    exit();
}
include("conexion.php");

$mensaje = "";

if (isset($_POST["importar"])) {
    if (isset($_FILES['archivo_excel']) && $_FILES['archivo_excel']['error'] === UPLOAD_ERR_OK) {
        $filename = $_FILES["archivo_excel"]["tmp_name"];
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
                // YA EXISTE: Actualizamos los datos
                $conexion->query("UPDATE productos SET categoria='$categoria', factura='$factura', stock_inicial=$s_inicial, precio_compra=$p_compra, precio_venta=$p_venta, stock=$s_actual WHERE nombre='$nombre'");
                $actualizados++;
            } else {
                // NO EXISTE: Insertamos nuevo
                $conexion->query("INSERT INTO productos (nombre, categoria, factura, stock_inicial, precio_compra, precio_venta, stock) VALUES ('$nombre', '$categoria', '$factura', $s_inicial, $p_compra, $p_venta, $s_actual)");
                $insertados++;
            }
        }
        fclose($file);
        $mensaje = "<div class='alert alert-success shadow-sm'>✅ <b>Proceso terminado:</b> $insertados nuevos, $actualizados actualizados.</div>";
    } else {
        $mensaje = "<div class='alert alert-danger'>❌ Error al subir el archivo. Verifique que sea un CSV válido.</div>";
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
                        <?php echo $mensaje; ?>
                        <form action="importar.php" method="post" enctype="multipart/form-data">
                            <div class="mb-4 text-start">
                                <label class="form-label fw-bold small text-uppercase">Seleccionar Archivo CSV</label>
                                <input type="file" name="archivo_excel" class="form-control form-control-lg" accept=".csv" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" name="importar" class="btn btn-primary fw-bold">Procesar Inventario</button>
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
