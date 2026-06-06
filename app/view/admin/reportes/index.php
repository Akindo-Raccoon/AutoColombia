<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reportes de Ventas – AutoColombia</title>
    <link rel="stylesheet" href="/ProyFinal/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            .sidebar, nav.navbar, .btn { display: none !important; }
            main { margin: 0 !important; padding: 0 !important; }
            .col-md-10 { width: 100% !important; max-width: 100% !important; flex: 0 0 100% !important; margin: 0 !important; }
            .container-fluid > .row > nav { display: none !important; }
            body { font-size: 12px; }
            .card { border: 1px solid #ccc !important; box-shadow: none !important; }
        }
        .stat-card { border-left: 4px solid; border-radius: 8px; }
        .stat-card.verde  { border-color: #27ae60; }
        .stat-card.azul   { border-color: #2980b9; }
        .stat-card.naranja{ border-color: #f39c12; }
    </style>
</head>
<body>
<?php
require_once __DIR__ . '/../../../../app/services/session.php';
verificarPerfil('admin');
require_once __DIR__ . '/../../../../app/model/venta.php';
require_once __DIR__ . '/../../../../app/model/vendedor.php';
require_once __DIR__ . '/../../../../app/model/concesionario.php';
require_once __DIR__ . '/../../../../app/model/marca.php';
require_once __DIR__ . '/../../../../app/model/forma_pago.php';

$objV   = new Venta();
$objVd  = new Vendedor();
$objC   = new Concesionario();
$objM   = new Marca();
$objFP  = new FormaPago();

$vendedores     = $objVd->listar();
$concesionarios = $objC->listar();
$marcas         = $objM->listar();
$formas_pago    = $objFP->listar();

// ── Procesar filtros ────────────────────────────────────────────
$filtros   = [];
$generado  = false;
$ventas    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $generado = true;

    $periodo = $_POST['periodo'] ?? '';
    if ($periodo === 'semanal') {
        $filtros['fecha_inicio'] = date('Y-m-d', strtotime('-7 days'));
        $filtros['fecha_fin']    = date('Y-m-d');
    } elseif ($periodo === 'quincenal') {
        $filtros['fecha_inicio'] = date('Y-m-d', strtotime('-15 days'));
        $filtros['fecha_fin']    = date('Y-m-d');
    } elseif ($periodo === 'mensual') {
        $filtros['fecha_inicio'] = date('Y-m-01');
        $filtros['fecha_fin']    = date('Y-m-d');
    } elseif ($periodo === 'todas') {
        // Histórico completo, no se aplican filtros de fecha
    } else {
        // Rango personalizado
        if (!empty($_POST['fecha_inicio'])) $filtros['fecha_inicio'] = $_POST['fecha_inicio'];
        if (!empty($_POST['fecha_fin']))    $filtros['fecha_fin']    = $_POST['fecha_fin'];
    }

    if (!empty($_POST['id_vendedor']))     $filtros['id_vendedor']     = $_POST['id_vendedor'];
    if (!empty($_POST['id_concesionario']))$filtros['id_concesionario']= $_POST['id_concesionario'];
    if (!empty($_POST['id_marca']))        $filtros['id_marca']        = $_POST['id_marca'];
    if (!empty($_POST['id_forma_pago']))   $filtros['id_forma_pago']   = $_POST['id_forma_pago'];
    if (!empty($_POST['top']) && intval($_POST['top']) > 0)
                                           $filtros['top']             = $_POST['top'];

    $ventas = $objV->reporte($filtros);
}

$total_ingresos  = array_sum(array_column($ventas, 'precio_cobrado'));
$promedio        = count($ventas) > 0 ? $total_ingresos / count($ventas) : 0;
?>
<?php include('../navbar.php'); ?>

<main class="col-md-10 ms-sm-auto px-4 py-4">

    <!-- Filtros -->
    <div class="no-print">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>📊 Módulo de Reportes de Ventas</h4>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header card-header-primary text-white fw-semibold">
                🔧 Parámetros del Reporte
            </div>
            <div class="card-body">
                <form method="POST" id="formReporte">
                    <div class="row g-3">

                        <!-- Período predefinido -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Período Predefinido</label>
                            <select name="periodo" id="sel_periodo" class="form-select" onchange="toggleFechas(this.value)">
                                <option value="">— Rango personalizado —</option>
                                <option value="todas"     <?= ($_POST['periodo']??'')==='todas'     ? 'selected':'' ?>>📅 Histórico Completo</option>
                                <option value="semanal"   <?= ($_POST['periodo']??'')==='semanal'   ? 'selected':'' ?>>📅 Última semana</option>
                                <option value="quincenal" <?= ($_POST['periodo']??'')==='quincenal' ? 'selected':'' ?>>📅 Últimos 15 días</option>
                                <option value="mensual"   <?= ($_POST['periodo']??'')==='mensual'   ? 'selected':'' ?>>📅 Mes actual</option>
                            </select>
                        </div>

                        <!-- Rango personalizado -->
                        <div class="col-md-4" id="campoFechaI">
                            <label class="form-label fw-semibold">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control"
                                value="<?= htmlspecialchars($_POST['fecha_inicio'] ?? '') ?>"
                                max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-4" id="campoFechaF">
                            <label class="form-label fw-semibold">Fecha Fin</label>
                            <input type="date" name="fecha_fin" class="form-control"
                                value="<?= htmlspecialchars($_POST['fecha_fin'] ?? '') ?>"
                                max="<?= date('Y-m-d') ?>">
                        </div>

                        <!-- Vendedor -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Vendedor</label>
                            <select name="id_vendedor" class="form-select">
                                <option value="">Todos los vendedores</option>
                                <?php foreach ($vendedores as $vd): ?>
                                <option value="<?= $vd['id_vendedor'] ?>"
                                    <?= ($_POST['id_vendedor']??'')==$vd['id_vendedor'] ? 'selected':'' ?>>
                                    <?= htmlspecialchars($vd['nombre'].' '.$vd['apellidos']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Concesionario -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Concesionario</label>
                            <select name="id_concesionario" class="form-select">
                                <option value="">Todos los concesionarios</option>
                                <?php foreach ($concesionarios as $c): ?>
                                <option value="<?= $c['id_concesionario'] ?>"
                                    <?= ($_POST['id_concesionario']??'')==$c['id_concesionario'] ? 'selected':'' ?>>
                                    <?= htmlspecialchars($c['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Marca -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Marca</label>
                            <select name="id_marca" class="form-select">
                                <option value="">Todas las marcas</option>
                                <?php foreach ($marcas as $ma): ?>
                                <option value="<?= $ma['id_marca'] ?>"
                                    <?= ($_POST['id_marca']??'')==$ma['id_marca'] ? 'selected':'' ?>>
                                    <?= htmlspecialchars($ma['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Forma de pago -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Forma de Pago</label>
                            <select name="id_forma_pago" class="form-select">
                                <option value="">Todas las formas de pago</option>
                                <?php foreach ($formas_pago as $fp): ?>
                                <option value="<?= $fp['id_forma_pago'] ?>"
                                    <?= ($_POST['id_forma_pago']??'')==$fp['id_forma_pago'] ? 'selected':'' ?>>
                                    <?= htmlspecialchars(ucfirst($fp['tipo']) . ($fp['nombre_financiera'] ? ' – '.$fp['nombre_financiera'] : '')) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Top N -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Top N ventas (0 = todas)</label>
                            <input type="number" name="top" class="form-control" min="0" max="100"
                                value="<?= htmlspecialchars($_POST['top'] ?? '0') ?>"
                                placeholder="Ej: 10 para top 10">
                        </div>

                        <div class="col-12 d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary px-4">
                                📊 Generar Reporte
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ── RESULTADO ──────────────────────────────────────────── -->
    <?php if ($generado): ?>

    <!-- Encabezado imprimible -->
    <div class="d-none d-print-block mb-3 text-center">
        <h2>🚗 AutoColombia – Reporte de Ventas</h2>
        <p class="text-muted">Generado el <?= date('d/m/Y H:i') ?></p>
        <?php if (!empty($filtros['fecha_inicio'])): ?>
        <p>Período: <?= date('d/m/Y', strtotime($filtros['fecha_inicio'])) ?> al <?= date('d/m/Y', strtotime($filtros['fecha_fin'] ?? date('Y-m-d'))) ?></p>
        <?php endif; ?>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card verde p-3">
                <div class="text-muted small">Total Ventas</div>
                <div class="fs-2 fw-bold text-success"><?= count($ventas) ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card azul p-3">
                <div class="text-muted small">Ingresos Totales</div>
                <div class="fs-4 fw-bold text-primary">$<?= number_format($total_ingresos, 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card naranja p-3">
                <div class="text-muted small">Promedio por Venta</div>
                <div class="fs-4 fw-bold text-warning">$<?= number_format($promedio, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>

    <!-- Botón imprimir -->
    <div class="d-flex justify-content-end mb-3 no-print gap-2">
        <button class="btn btn-outline-secondary" onclick="window.print()">🖨️ Imprimir / Exportar PDF</button>
    </div>

    <!-- Tabla de ventas -->
    <?php if (empty($ventas)): ?>
        <div class="alert alert-info">No se encontraron ventas con los filtros seleccionados.</div>
    <?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0" id="tablaReporte">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Vehículo</th>
                            <th>Concesionario</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Forma de Pago</th>
                            <th>Precio Cobrado</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ventas as $i => $v): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= date('d/m/Y', strtotime($v['fecha_venta'])) ?></td>
                            <td>
                                <strong><?= htmlspecialchars($v['nombre_marca'] . ' ' . $v['nombre_modelo']) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($v['color']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($v['nombre_concesionario']) ?></td>
                            <td>
                                <?= $v['nombre_usuario']
                                    ? htmlspecialchars($v['nombre_usuario'].' '.$v['apellidos_usuario'])
                                    : '<span class="text-muted">–</span>' ?>
                            </td>
                            <td>
                                <?= $v['nombre_vendedor']
                                    ? htmlspecialchars($v['nombre_vendedor'].' '.$v['apellidos_vendedor'])
                                    : '<span class="text-muted">–</span>' ?>
                            </td>
                            <td>
                                <?php if ($v['tipo_pago'] === 'contado'): ?>
                                    <span class="badge bg-success">Contado</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark"><?= htmlspecialchars($v['nombre_financiera']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><strong>$<?= number_format($v['precio_cobrado'], 0, ',', '.') ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <td colspan="7" class="text-end fw-bold">TOTAL</td>
                            <td class="fw-bold">$<?= number_format($total_ingresos, 0, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php endif; ?>

</main>
</div>
</div>

<?php include('../../layouts/footer.php'); ?>
<script src="/ProyFinal/public/js/sweetalert2.all.min.js"></script>
<script>
function toggleFechas(val) {
    const ocultar = val !== '';
    document.getElementById('campoFechaI').style.opacity = ocultar ? '0.4' : '1';
    document.getElementById('campoFechaF').style.opacity = ocultar ? '0.4' : '1';
    document.getElementById('campoFechaI').querySelector('input').disabled = ocultar;
    document.getElementById('campoFechaF').querySelector('input').disabled = ocultar;
}
// Inicializar
toggleFechas(document.getElementById('sel_periodo').value);
</script>
</body>
</html>
