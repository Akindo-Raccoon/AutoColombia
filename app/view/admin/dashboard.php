<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Administrador – AutoColombia</title>
    <link rel="stylesheet" href="/ProyFinal/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php
require_once __DIR__ . '/../../../app/services/session.php';
verificarPerfil('admin');
require_once __DIR__ . '/../../../config/conexionBD.php';

$db = Conectar::conec();

$total_usuarios   = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) AS t FROM usuario"))['t'];
$total_vendedores = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) AS t FROM vendedor"))['t'];
$total_autos      = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) AS t FROM automovil"))['t'];
$total_disponibles= mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) AS t FROM automovil WHERE estado='disponible'"))['t'];
$total_ventas     = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) AS t FROM venta"))['t'];
$total_marcas     = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) AS t FROM marca"))['t'];
$ingreso_total    = mysqli_fetch_assoc(mysqli_query($db, "SELECT COALESCE(SUM(precio_cobrado),0) AS t FROM venta"))['t'];
$ventas_mes       = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) AS t FROM venta WHERE MONTH(fecha_venta)=MONTH(NOW()) AND YEAR(fecha_venta)=YEAR(NOW())"))['t'];
$ingreso_mes      = mysqli_fetch_assoc(mysqli_query($db, "SELECT COALESCE(SUM(precio_cobrado),0) AS t FROM venta WHERE MONTH(fecha_venta)=MONTH(NOW()) AND YEAR(fecha_venta)=YEAR(NOW())"))['t'];

// Últimas 5 ventas
$res_ultimas = mysqli_query($db, "SELECT v.id_venta, v.fecha_venta, v.precio_cobrado,
    mo.nombre AS nombre_modelo, ma.nombre AS nombre_marca,
    vend.nombre AS nombre_vendedor, u.nombre AS nombre_usuario
    FROM venta v
    INNER JOIN automovil a ON v.num_bastidor=a.num_bastidor
    INNER JOIN modelo mo ON a.id_modelo=mo.id_modelo
    INNER JOIN marca ma ON mo.id_marca=ma.id_marca
    LEFT JOIN vendedor vend ON v.id_vendedor=vend.id_vendedor
    LEFT JOIN usuario u ON v.id_usuario=u.id_usuario
    ORDER BY v.fecha_venta DESC LIMIT 5");
$ultimas_ventas = [];
while ($row = mysqli_fetch_assoc($res_ultimas)) $ultimas_ventas[] = $row;
?>
<?php include('navbar.php'); ?>

<main class="col-md-10 ms-sm-auto px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>🏠 Panel de Administración</h4>
        <span class="text-muted small">Bienvenido, <strong><?= htmlspecialchars($_SESSION['nombre']) ?></strong> 🛡️</span>
    </div>

    <!-- Fila 1: métricas principales -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card text-white" style="background:#1a3c5e">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><div class="fs-3 fw-bold"><?= $total_usuarios ?></div><div class="small">Usuarios</div></div>
                    <span style="font-size:2rem">👥</span>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="/ProyFinal/app/view/admin/usuarios/listar.php" class="text-white small">Ver todos →</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card text-white" style="background:#2980b9">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><div class="fs-3 fw-bold"><?= $total_vendedores ?></div><div class="small">Vendedores</div></div>
                    <span style="font-size:2rem">🧑‍💼</span>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="/ProyFinal/app/view/admin/vendedores/listar.php" class="text-white small">Ver todos →</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card text-white" style="background:#27ae60">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><div class="fs-3 fw-bold"><?= $total_autos ?></div><div class="small">Automóviles</div></div>
                    <span style="font-size:2rem">🚘</span>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="/ProyFinal/app/view/admin/automoviles/listar.php" class="text-white small">Ver inventario →</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card text-white" style="background:#f39c12">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><div class="fs-3 fw-bold"><?= $total_disponibles ?></div><div class="small">Disponibles</div></div>
                    <span style="font-size:2rem">✅</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 2: métricas de ventas -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card text-white" style="background:#8e44ad">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><div class="fs-3 fw-bold"><?= $total_ventas ?></div><div class="small">Ventas Totales</div></div>
                    <span style="font-size:2rem">🧾</span>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="/ProyFinal/app/view/admin/ventas/listar.php" class="text-white small">Ver ventas →</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card text-white" style="background:#e74c3c">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><div class="fs-5 fw-bold">$<?= number_format($ingreso_total, 0, ',', '.') ?></div><div class="small">Ingresos Totales</div></div>
                    <span style="font-size:2rem">💰</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card text-white" style="background:#16a085">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><div class="fs-3 fw-bold"><?= $ventas_mes ?></div><div class="small">Ventas este mes</div></div>
                    <span style="font-size:2rem">📅</span>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="/ProyFinal/app/view/admin/reportes/index.php" class="text-white small">Ver reporte →</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card text-white" style="background:#d35400">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><div class="fs-5 fw-bold">$<?= number_format($ingreso_mes, 0, ',', '.') ?></div><div class="small">Ingresos del mes</div></div>
                    <span style="font-size:2rem">📈</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimas ventas + accesos rápidos -->
    <div class="row g-3">
        <!-- Últimas ventas -->
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header fw-semibold">🧾 Últimas Ventas</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>#</th><th>Fecha</th><th>Vehículo</th><th>Cliente/Vendedor</th><th>Precio</th></tr>
                        </thead>
                        <tbody>
                        <?php if (empty($ultimas_ventas)): ?>
                            <tr><td colspan="5" class="text-center py-3 text-muted">Sin ventas aún</td></tr>
                        <?php else: ?>
                        <?php foreach ($ultimas_ventas as $v): ?>
                            <tr>
                                <td>#<?= $v['id_venta'] ?></td>
                                <td><?= date('d/m/Y', strtotime($v['fecha_venta'])) ?></td>
                                <td><?= htmlspecialchars($v['nombre_marca'].' '.$v['nombre_modelo']) ?></td>
                                <td><?= htmlspecialchars($v['nombre_usuario'] ?? ($v['nombre_vendedor'] ?? '–')) ?></td>
                                <td class="text-success fw-bold">$<?= number_format($v['precio_cobrado'],0,',','.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="/ProyFinal/app/view/admin/ventas/listar.php" class="btn btn-sm btn-outline-primary">Ver todas las ventas</a>
                    <a href="/ProyFinal/app/view/admin/reportes/index.php" class="btn btn-sm btn-outline-secondary ms-2">📊 Generar Reporte</a>
                </div>
            </div>
        </div>

        <!-- Accesos rápidos -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header fw-semibold">⚡ Accesos Rápidos</div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="/ProyFinal/app/view/admin/automoviles/listar.php" class="btn btn-outline-success">🚘 Gestionar Automóviles</a>
                    <a href="/ProyFinal/app/view/admin/marcas/listar.php" class="btn btn-outline-secondary">🏷️ Gestionar Marcas</a>
                    <a href="/ProyFinal/app/view/admin/modelos/listar.php" class="btn btn-outline-secondary">🚙 Gestionar Modelos</a>
                    <a href="/ProyFinal/app/view/admin/concesionarios/listar.php" class="btn btn-outline-secondary">🏢 Concesionarios</a>
                    <a href="/ProyFinal/app/view/admin/formas_pago/listar.php" class="btn btn-outline-secondary">💳 Formas de Pago</a>
                    <a href="/ProyFinal/app/view/admin/reportes/index.php" class="btn btn-primary">📊 Módulo de Reportes</a>
                </div>
            </div>
        </div>
    </div>
</main>
</div>
</div>

<?php include('../layouts/footer.php'); ?>
<script src="/ProyFinal/public/js/sweetalert2.all.min.js"></script>
</body>
</html>