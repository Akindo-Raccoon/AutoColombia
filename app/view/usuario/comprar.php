<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Comprar Vehículo – AutoColombia</title>
    <link rel="stylesheet" href="/ProyFinal/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .extra-card { cursor:pointer; border:2px solid #dee2e6; border-radius:8px; transition:.15s; }
        .extra-card.selected { border-color:#27ae60; background:#f0fff4; }
        .total-box { background:linear-gradient(135deg,#1a3c5e,#2980b9); border-radius:12px; }
    </style>
</head>
<body>
<?php
require_once __DIR__ . '/../../../app/services/session.php';
verificarPerfil('usuario');
require_once __DIR__ . '/../../../app/model/auto.php';
require_once __DIR__ . '/../../../app/model/modelo_auto.php';
require_once __DIR__ . '/../../../app/model/forma_pago.php';

$bastidor = trim($_GET['bastidor'] ?? '');
if (!$bastidor) {
    header('Location: /ProyFinal/app/view/usuario/catalogo.php');
    exit();
}

$objA = new Auto();
$auto = $objA->buscarPorBastidor($bastidor);
if (!$auto || $auto['estado'] !== 'disponible') {
    header('Location: /ProyFinal/app/view/usuario/catalogo.php');
    exit();
}

$objM  = new ModeloAuto();
$modelo = $objM->buscarPorId($auto['id_modelo']);
$extras = $objM->listarExtrasModelo($auto['id_modelo']);

$objFP       = new FormaPago();
$formas_pago = $objFP->listar();
?>

<nav class="navbar navbar-expand-lg navbar-autocolombia navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/ProyFinal/app/view/usuario/dashboard.php">🚗 AutoColombia</a>
        <div class="navbar-nav ms-auto d-flex flex-row gap-3 align-items-center">
            <a class="nav-link text-white" href="/ProyFinal/app/view/usuario/catalogo.php">🚘 Catálogo</a>
            <a class="nav-link text-white" href="/ProyFinal/app/view/usuario/mis_compras.php">🧾 Mis Compras</a>
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown">
                    👤 <?= htmlspecialchars($_SESSION['nombre']) ?>
                    <span class="badge bg-success ms-1">Usuario</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="/ProyFinal/app/control/salir.php">🚪 Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/ProyFinal/app/view/usuario/catalogo.php">Catálogo</a></li>
            <li class="breadcrumb-item active">Comprar Vehículo</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Datos del vehículo -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div style="background:linear-gradient(135deg,#1a3c5e,#2980b9); padding:2rem; text-align:center; border-radius:12px 12px 0 0;">
                    <div style="font-size:5rem">🚗</div>
                    <h4 class="text-white mt-2">
                        <?= htmlspecialchars($modelo['nombre_marca'] . ' ' . $modelo['nombre']) ?>
                    </h4>
                    <span class="badge bg-light text-dark"><?= ucfirst($modelo['tipo_combustible']) ?></span>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr><td class="text-muted">Color</td><td><strong><?= htmlspecialchars($auto['color']) ?></strong></td></tr>
                        <tr><td class="text-muted">Año</td><td><strong><?= $auto['anio_fabricacion'] ?></strong></td></tr>
                        <tr><td class="text-muted">Bastidor</td><td><code><?= htmlspecialchars($auto['num_bastidor']) ?></code></td></tr>
                        <tr><td class="text-muted">Plazas</td><td><strong><?= $modelo['num_plazas'] ?></strong></td></tr>
                        <tr><td class="text-muted">Cilindrada</td><td><strong><?= $modelo['cilindrada'] ?> cc</strong></td></tr>
                        <tr><td class="text-muted">Potencia</td><td><strong><?= $modelo['potencia_fiscal'] ?> CV</strong></td></tr>
                        <?php if ($modelo['descripcion']): ?>
                        <tr><td colspan="2" class="text-muted small pt-2"><?= htmlspecialchars($modelo['descripcion']) ?></td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>

        <!-- Formulario de compra -->
        <div class="col-lg-7">
            <form id="formCompra" method="POST" action="/ProyFinal/app/control/procesar_venta.php">
                <input type="hidden" name="accion" value="comprar">
                <input type="hidden" name="num_bastidor" value="<?= htmlspecialchars($bastidor) ?>">

                <!-- Precio base -->
                <div class="total-box text-white p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Precio base</div>
                            <div class="fs-3 fw-bold" id="precio_base_display">
                                $<?= number_format($modelo['precio_base'], 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="small text-white-50">Total estimado</div>
                            <div class="fs-2 fw-bold" id="total_display">
                                $<?= number_format($modelo['precio_base'], 0, ',', '.') ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($modelo['descuento'] > 0): ?>
                    <div class="mt-2 small text-warning">🏷️ Descuento del <?= $modelo['descuento'] ?>% disponible para ciertos pagos</div>
                    <?php endif; ?>
                </div>

                <!-- Forma de pago -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">💳 Forma de Pago *</label>
                    <select name="id_forma_pago" id="forma_pago" class="form-select form-select-lg" required>
                        <option value="">Selecciona cómo deseas pagar</option>
                        <?php foreach ($formas_pago as $fp): ?>
                        <option value="<?= $fp['id_forma_pago'] ?>">
                            <?= $fp['tipo'] === 'contado' ? '💵 Contado' : '🏦 '.$fp['nombre_financiera'] ?>
                            <?= $fp['condiciones'] ? '– '.$fp['condiciones'] : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Selecciona una forma de pago</div>
                </div>

                <!-- Extras -->
                <?php if (!empty($extras)): ?>
                <div class="mb-4">
                    <label class="form-label fw-semibold">⭐ Extras Opcionales</label>
                    <p class="text-muted small mb-2">Selecciona los accesorios que deseas incluir:</p>
                    <div class="row g-2">
                    <?php foreach ($extras as $e): ?>
                        <div class="col-md-6">
                            <div class="extra-card p-3" onclick="toggleExtra(this, <?= $e['id_extra'] ?>, <?= $e['precio_extra'] ?>)">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold"><?= htmlspecialchars($e['nombre_extra']) ?></div>
                                        <div class="text-muted small"><?= htmlspecialchars($e['desc_extra'] ?? '') ?></div>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($e['categoria']) ?></span>
                                    </div>
                                    <div class="text-success fw-bold text-nowrap ms-2">
                                        +$<?= number_format($e['precio_extra'], 0, ',', '.') ?>
                                    </div>
                                </div>
                                <input type="checkbox" name="extras[]" value="<?= $e['id_extra'] ?>"
                                       class="extra-check d-none">
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Confirmación -->
                <div class="card bg-light border-0 mb-4 p-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Precio base:</span>
                        <span id="resumen_base">$<?= number_format($modelo['precio_base'], 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Extras seleccionados:</span>
                        <span id="resumen_extras">$0</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>TOTAL:</span>
                        <span class="text-success" id="resumen_total">$<?= number_format($modelo['precio_base'], 0, ',', '.') ?></span>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg fw-semibold">
                        ✅ Confirmar Compra
                    </button>
                    <a href="/ProyFinal/app/view/usuario/catalogo.php" class="btn btn-outline-secondary">
                        ← Volver al Catálogo
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/ProyFinal/public/js/sweetalert2.all.min.js"></script>
<script>
const precioBase = <?= floatval($modelo['precio_base']) ?>;
let precioExtras = 0;

function fmt(n) {
    return '$' + n.toLocaleString('es-CO', {maximumFractionDigits:0});
}

function actualizarTotal() {
    const total = precioBase + precioExtras;
    document.getElementById('total_display').textContent  = fmt(total);
    document.getElementById('resumen_base').textContent   = fmt(precioBase);
    document.getElementById('resumen_extras').textContent = fmt(precioExtras);
    document.getElementById('resumen_total').textContent  = fmt(total);
}

function toggleExtra(card, id_extra, precio) {
    const cb = card.querySelector('.extra-check');
    const sel = card.classList.toggle('selected');
    cb.checked = sel;
    precioExtras += sel ? precio : -precio;
    actualizarTotal();
}

document.getElementById('formCompra').addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        this.classList.add('was-validated');
        return;
    }
    e.preventDefault();
    const total = precioBase + precioExtras;
    Swal.fire({
        icon: 'question',
        title: '¿Confirmar compra?',
        html: `<p>Estás a punto de adquirir el vehículo por:</p><h3 class="text-success">${fmt(total)}</h3>`,
        showCancelButton: true,
        confirmButtonText: '✅ Sí, comprar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#27ae60'
    }).then(r => { if (r.isConfirmed) this.submit(); });
});
</script>
</body>
</html>
