<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis Compras – AutoColombia</title>
    <link rel="stylesheet" href="/ProyFinal/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php
require_once __DIR__ . '/../../../app/services/session.php';
verificarPerfil('usuario');
require_once __DIR__ . '/../../../app/model/venta.php';
$objV   = new Venta();
$compras = $objV->listarPorUsuario($_SESSION['id_usuario']);
$total_gastado = array_sum(array_column($compras, 'precio_cobrado'));
?>

<nav class="navbar navbar-expand-lg navbar-autocolombia navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/ProyFinal/app/view/usuario/dashboard.php">🚗 AutoColombia</a>
        <div class="navbar-nav ms-auto d-flex flex-row gap-3 align-items-center">
            <a class="nav-link text-white" href="/ProyFinal/app/view/usuario/catalogo.php">🚘 Catálogo</a>
            <a class="nav-link text-white active" href="/ProyFinal/app/view/usuario/mis_compras.php">🧾 Mis Compras</a>
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>🧾 Mis Compras</h4>
        <?php if (!empty($compras)): ?>
        <div class="d-flex gap-2">
            <span class="badge bg-primary fs-6 px-3 py-2"><?= count($compras) ?> compra(s)</span>
            <span class="badge bg-success fs-6 px-3 py-2">Total: $<?= number_format($total_gastado, 0, ',', '.') ?></span>
        </div>
        <?php endif; ?>
    </div>

    <?php if (empty($compras)): ?>
    <div class="text-center py-5">
        <div style="font-size:5rem">🛒</div>
        <h5 class="mt-3 text-muted">Aún no has realizado ninguna compra</h5>
        <p class="text-muted">Explora nuestro catálogo y encuentra el auto perfecto para ti.</p>
        <a href="/ProyFinal/app/view/usuario/catalogo.php" class="btn btn-success btn-lg mt-2">
            🚘 Ver Catálogo
        </a>
    </div>
    <?php else: ?>
    <div class="row g-3">
    <?php foreach ($compras as $c): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-left:4px solid #27ae60 !important; border-radius:10px; overflow:hidden;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold mb-0">
                                🚗 <?= htmlspecialchars($c['nombre_marca'] . ' ' . $c['nombre_modelo']) ?>
                            </h6>
                            <small class="text-muted"><?= htmlspecialchars($c['color']) ?> · <?= $c['anio_fabricacion'] ?></small>
                        </div>
                        <span class="badge bg-success">✅ Comprado</span>
                    </div>
                    <div class="row g-1 small mb-3">
                        <div class="col-6 text-muted">📅 Fecha:</div>
                        <div class="col-6"><?= date('d/m/Y', strtotime($c['fecha_venta'])) ?></div>
                        <?php if ($c['fecha_entrega']): ?>
                        <div class="col-6 text-muted">🚚 Entrega:</div>
                        <div class="col-6"><?= date('d/m/Y', strtotime($c['fecha_entrega'])) ?></div>
                        <?php endif; ?>
                        <div class="col-6 text-muted">💳 Pago:</div>
                        <div class="col-6">
                            <?= $c['tipo_pago'] === 'contado' ? '💵 Contado' : '🏦 '.$c['nombre_financiera'] ?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <strong class="text-success fs-5">$<?= number_format($c['precio_cobrado'], 0, ',', '.') ?></strong>
                        <a href="/ProyFinal/app/view/usuario/recibo.php?id=<?= $c['id_venta'] ?>"
                           class="btn btn-outline-primary btn-sm">
                            🧾 Ver Recibo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

    <div class="mt-4 text-center">
        <a href="/ProyFinal/app/view/usuario/catalogo.php" class="btn btn-success">🚘 Ver más automóviles</a>
    </div>
    <?php endif; ?>
</div>

<script src="/ProyFinal/public/js/sweetalert2.all.min.js"></script>
</body>
</html>
