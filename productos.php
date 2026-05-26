<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("Location: login.php"); exit(); }
include("conexion.php");

$rol_usuario = strtolower(trim($_SESSION['rol'] ?? 'vendedor'));

// Consulta SQL que calcula las ventas restando el stock actual del inicial
$sql = "SELECT *, (stock_inicial - stock) AS vendidos FROM productos ORDER BY nombre ASC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario Ebenezer - Nicaragua</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background-image: url('https://www.transparenttextures.com/patterns/dark-wood.png'), 
                              linear-gradient(to right, #1a2c4e, #2c3e50);
            background-color: #1a2c4e;
            background-blend-mode: overlay;
            background-attachment: fixed;
            color: #ecf0f1;
            font-family: 'Segoe UI', sans-serif;
            padding-bottom: 40px;
        }
        .main-wrapper { max-width: 1450px; margin: 0 auto; padding: 0 15px; }
        .table-container { 
            background: rgba(255, 255, 255, 0.95);
            padding: 30px; border-radius: 15px; 
            box-shadow: 0 15px 40px rgba(0,0,0,0.2); 
        }
        .table thead { background: linear-gradient(to right, #2c3e50, #1a2c4e); color: white; }
        .table td { vertical-align: middle !important; color: #333; }
        
        /* Buscador mejorado */
        .search-box { max-width: 600px; margin: 0 auto 30px auto; position: relative; }
        .search-box input { 
            border-radius: 20px; padding: 15px 20px 15px 55px; border: none;
            background: rgba(255, 255, 255, 0.9); box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .search-icon { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: #1a2c4e; font-size: 1.3em; }

        /* Estilos de Badges para Stock */
        .badge-inicial { background-color: #e9ecef; color: #495057; font-weight: bold; padding: 6px 12px; border-radius: 8px; border: 1px solid #ced4da; }
        .badge-vendidos { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 6px 12px; border-radius: 8px; font-weight: bold; }
        .badge-actual { font-weight: bold; padding: 6px 12px; border-radius: 8px; min-width: 60px; display: inline-block; }
        .btn-header { font-weight: 600; border-radius: 12px; transition: 0.3s; }
    </style>
</head>
<body class="py-4">

    <div class="main-wrapper">
        <div class="d-flex justify-content-between align-items-center mb-4 text-white">
            <div>
                <h2 class="fw-bold m-0">📦 Listado de Inventario</h2>
                <p class="small opacity-80 m-0">Librería Ebenezer - Nicaragua</p>
            </div>
            <div class="d-flex gap-2">
                <?php if ($rol_usuario === 'admin'): ?>
                    <a href="agregar.php" class="btn btn-success btn-header shadow-sm px-4">+ Nuevo Producto</a>
                    <a href="dashboard.php" class="btn btn-warning btn-header text-dark shadow-sm px-4">📊 Gráficos</a>
                    <a href="reporte_ventas.php" class="btn btn-info btn-header text-white shadow-sm px-4">💰 Caja</a>
                <?php endif; ?>
                <a href="index.php" class="btn btn-outline-light btn-header px-4">Dashboard</a>
                <a href="logout.php" class="btn btn-danger btn-header shadow-sm px-4">Salir</a>
            </div>
        </div>

        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" id="inputBusqueda" class="form-control" placeholder="Buscar por producto, categoría o factura...">
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover text-center mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-start">Producto</th>
                            <th>Categoría</th>
                            <th>Entrada Inicial</th>
                            <th>Ventas</th>
                            <th>Stock Actual</th>
                            <th>P. Compra</th>
                            <th>P. Venta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="listaProductos">
                        <?php $n = 1; while ($f = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><span class="text-muted small"><?php echo $n; ?></span></td>
                            <td class="text-start">
                                <span class="fw-bold d-block"><?php echo $f['nombre']; ?></span>
                                <small class="text-muted">Fact: <?php echo $f['factura'] ?: 'N/A'; ?></small>
                            </td>
                            <td><span class="badge bg-light text-dark border px-3"><?php echo $f['categoria']; ?></span></td>
                            
                            <td><span class="badge-inicial"><?php echo $f['stock_inicial'] ?: $f['stock']; ?></span></td>

                            <td><span class="badge-vendidos"><?php echo max(0, $f['vendidos']); ?></span></td>

                            <td>
                                <span class="badge-actual <?php echo ($f['stock'] <= 5) ? 'bg-danger text-white' : 'bg-success text-white'; ?>">
                                    <?php echo $f['stock']; ?>
                                </span>
                            </td>

                            <td class="text-muted">C$ <?php echo number_format($f['precio_compra'], 2); ?></td>
                            <td class="fw-bold text-primary">C$ <?php echo number_format($f['precio_venta'], 2); ?></td>
                            
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="vender.php?id=<?php echo $f['id']; ?>" class="btn btn-success btn-sm px-3 fw-bold">Vender</a>
                                    <?php if ($rol_usuario === 'admin'): ?>
                                        <a href="editar.php?id=<?php echo $f['id']; ?>" class="btn btn-warning btn-sm text-white">Editar</a>
                                        <a href="eliminar_producto.php?id=<?php echo $f['id']; ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('¿Eliminar <?php echo $f['nombre']; ?>?');">X</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php $n++; endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Buscador en tiempo real
        document.getElementById('inputBusqueda').addEventListener('keyup', function() {
            let filtro = this.value.toLowerCase();
            let filas = document.getElementById('listaProductos').getElementsByTagName('tr');
            for (let i = 0; i < filas.length; i++) {
                let textoFila = filas[i].textContent.toLowerCase();
                filas[i].style.display = (textoFila.indexOf(filtro) > -1) ? "" : "none";
            }
        });
    </script>
</body>
</html>