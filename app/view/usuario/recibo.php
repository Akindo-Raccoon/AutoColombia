<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recibo de Compra – AutoColombia</title>
    <link rel="stylesheet" href="/ProyFinal/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        @media print {
            .no-print { display:none !important; }
            body { font-size:12px; }
            .container { max-width:100% !important; }
        }
        .recibo-header { background:linear-gradient(135deg,#1a3c5e,#2980b9); border-radius:12px 12px 0 0; }
        .recibo-card { border-radius:12px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,.12); }
        .sello { border:3px solid #27ae60; color:#27ae60; border-radius:50%; width:80px; height:80px;
                 display:flex; align-items:center; justify-content:center; font-size:1.5rem;
                 font-weight:bold; transform:rotate(-15deg); }
    </style>
</head>
<body class="bg-light">
<?php
require_once __DIR__ . '/../../../app/services/session.php';
verificarPerfil(['usuario','admin','vendedor']);
require_once __DIR__ . '/../../../app/model/venta.php';

$id_venta = intval($_GET['id'] ?? 0);
if (!$id_venta) {
    header('Location: /ProyFinal/app/view/usuario/mis_compras.php');
    exit();
}

$objV  = new Venta();
$venta = $objV->buscarPorId($id_venta);
$extras = $objV->listarExtras($id_venta);

if (!$venta) {
    header('Location: /ProyFinal/app/view/usuario/mis_compras.php');
    exit();
}

$total_extras = array_sum(array_column($extras, 'precio_cobrado_extra'));
$num_recibo   = 'AC-' . str_pad($id_venta, 6, '0', STR_PAD_LEFT);
?>

<div class="no-print py-3 px-4 d-flex gap-2 justify-content-between align-items-center bg-white shadow-sm">
    <a href="/ProyFinal/app/view/usuario/mis_compras.php" class="btn btn-outline-secondary btn-sm">← Mis Compras</a>
    <div class="d-flex gap-2">
        <button class="btn btn-primary btn-sm" onclick="window.print()">🖨️ Imprimir / Guardar PDF</button>
        <a href="/ProyFinal/app/view/usuario/catalogo.php" class="btn btn-success btn-sm">🚘 Ver más autos</a>
    </div>
</div>

<div class="container py-4" style="max-width:750px">
    <div class="recibo-card bg-white">

        <!-- Encabezado -->
        <div class="recibo-header text-white p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div style="font-size:2rem">🚗</div>
                    <h3 class="mb-0 fw-bold">AutoColombia</h3>
                    <div class="text-white-50 small">Sistema de Gestión de Concesionario</div>
                </div>
                <div class="text-end">
                    <div class="text-white-50 small">RECIBO DE COMPRA</div>
                    <div class="fs-4 fw-bold"><?= $num_recibo ?></div>
                    <div class="text-white-50 small"><?= date('d/m/Y H:i') ?></div>
                </div>
            </div>
        </div>

        <div class="p-4">
            <!-- Datos del cliente -->
            <div class="row mb-4">
                <div class="col-6">
                    <div class="text-muted small fw-semibold text-uppercase mb-1">Datos del Cliente</div>
                    <div class="fw-bold"><?= htmlspecialchars($venta['nombre_usuario'] . ' ' . $venta['apellidos_usuario']) ?></div>
                    <div class="small text-muted"><?= htmlspecialchars($venta['email_usuario'] ?? '') ?></div>
                </div>
                <div class="col-6 text-end">
                    <div class="text-muted small fw-semibold text-uppercase mb-1">Fechas</div>
                    <div class="small"><strong>Venta:</strong> <?= date('d/m/Y', strtotime($venta['fecha_venta'])) ?></div>
                    <?php if ($venta['fecha_entrega']): ?>
                    <div class="small"><strong>Entrega:</strong> <?= date('d/m/Y', strtotime($venta['fecha_entrega'])) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <hr>

            <!-- Datos del vehículo -->
            <div class="mb-4">
                <div class="text-muted small fw-semibold text-uppercase mb-2">Vehículo Adquirido</div>
                <div class="card bg-light border-0 p-3">
                    <div class="row">
                        <div class="col-2 text-center" style="font-size:3rem">🚗</div>
                        <div class="col-10">
                            <h5 class="mb-1"><?= htmlspecialchars($venta['nombre_marca'] . ' ' . $venta['nombre_modelo']) ?></h5>
                            <div class="row g-1 small text-muted">
                                <div class="col-6">🎨 Color: <strong><?= htmlspecialchars($venta['color']) ?></strong></div>
                                <div class="col-6">📅 Año: <strong><?= $venta['anio_fabricacion'] ?></strong></div>
                                <div class="col-12">🔖 Bastidor: <code><?= htmlspecialchars($venta['num_bastidor']) ?></code></div>
                                <?php if ($venta['matricula']): ?>
                                <div class="col-12">🚗 Matrícula: <strong><?= htmlspecialchars($venta['matricula']) ?></strong></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desglose de precios -->
            <div class="mb-4">
                <div class="text-muted small fw-semibold text-uppercase mb-2">Desglose de Precios</div>
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <td>Precio base del vehículo</td>
                            <td class="text-end">$<?= number_format($venta['precio_cobrado'] - $total_extras, 0, ',', '.') ?></td>
                        </tr>
                        <?php foreach ($extras as $ex): ?>
                        <tr>
                            <td class="text-muted ps-3">+ <?= htmlspecialchars($ex['nombre_extra']) ?>
                                <span class="badge bg-secondary ms-1"><?= htmlspecialchars($ex['categoria']) ?></span>
                            </td>
                            <td class="text-end text-muted">+$<?= number_format($ex['precio_cobrado_extra'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold table-success">
                            <td>TOTAL COBRADO</td>
                            <td class="text-end fs-5">$<?= number_format($venta['precio_cobrado'], 0, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Forma de pago -->
            <div class="mb-4">
                <div class="text-muted small fw-semibold text-uppercase mb-2">Forma de Pago</div>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($venta['tipo_pago'] === 'contado'): ?>
                        <span class="badge bg-success fs-6 px-3 py-2">💵 Pago de Contado</span>
                    <?php else: ?>
                        <span class="badge bg-info text-dark fs-6 px-3 py-2">🏦 <?= htmlspecialchars($venta['nombre_financiera']) ?></span>
                    <?php endif; ?>
                    <?php if ($venta['condiciones']): ?>
                    <span class="text-muted small"><?= htmlspecialchars($venta['condiciones']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Vendedor -->
            <?php if ($venta['nombre_vendedor']): ?>
            <div class="mb-4">
                <div class="text-muted small fw-semibold text-uppercase mb-1">Atendido por</div>
                <div>💼 <?= htmlspecialchars($venta['nombre_vendedor'] . ' ' . $venta['apellidos_vendedor']) ?></div>
            </div>
            <?php endif; ?>

            <!-- Sello / estado -->
            <div class="d-flex justify-content-between align-items-end mt-4 pt-3 border-top">
                <div class="text-muted small">
                    <div>AutoColombia – Sistema de Gestión de Concesionario</div>
                    <div>Este documento es el comprobante oficial de su compra.</div>
                    <div>Gracias por confiar en AutoColombia. 🚗</div>
                </div>
                <div class="sello text-center" style="flex-shrink:0">
                    <div>✅<br><span style="font-size:.6rem">PAGADO</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
