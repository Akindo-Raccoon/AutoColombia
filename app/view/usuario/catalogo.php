<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catálogo – AutoColombia</title>
    <link rel="stylesheet" href="/ProyFinal/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .auto-card { transition: transform .2s, box-shadow .2s; border-radius: 12px; overflow: hidden; }
        .auto-card:hover { transform: translateY(-4px); box-shadow: 0 10px 30px rgba(0,0,0,.12); }
        .precio-tag { font-size: 1.3rem; font-weight: 700; color: #1a3c5e; }
        .filter-bar { background: #f8f9fa; border-radius: 10px; padding: 1rem; }
    </style>
</head>
<body>
<?php
require_once __DIR__ . '/../../../app/services/session.php';
verificarPerfil('usuario');
require_once __DIR__ . '/../../../app/model/auto.php';
$objA        = new Auto();
$automoviles = $objA->listar();
$modelos     = $objA->listarModelos();
$disponibles = array_filter($automoviles, fn($a) => $a['estado'] === 'disponible');
?>

<!-- Navbar usuario -->
<nav class="navbar navbar-expand-lg navbar-autocolombia navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/ProyFinal/app/view/usuario/dashboard.php">🚗 AutoColombia</a>
        <div class="navbar-nav ms-auto d-flex flex-row gap-3 align-items-center">
            <a class="nav-link text-white active" href="/ProyFinal/app/view/usuario/catalogo.php">🚘 Catálogo</a>
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

<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>🚘 Catálogo de Automóviles</h4>
        <span class="badge bg-success fs-6 px-3 py-2"><?= count($disponibles) ?> disponible(s)</span>
    </div>

    <!-- Filtros -->
    <div class="filter-bar mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Buscar</label>
                <input type="text" id="filtro_texto" class="form-control" placeholder="Marca, modelo, color...">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Modelo</label>
                <select id="filtro_modelo" class="form-select">
                    <option value="">Todos los modelos</option>
                    <?php foreach ($modelos as $m): ?>
                    <option value="<?= htmlspecialchars($m['nombre_completo']) ?>"><?= htmlspecialchars($m['nombre_completo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Precio máximo</label>
                <select id="filtro_precio" class="form-select">
                    <option value="">Sin límite</option>
                    <option value="60000000">Hasta $60.000.000</option>
                    <option value="90000000">Hasta $90.000.000</option>
                    <option value="120000000">Hasta $120.000.000</option>
                    <option value="150000000">Hasta $150.000.000</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">✕ Limpiar</button>
            </div>
        </div>
    </div>

    <!-- Tarjetas -->
    <div class="row g-3" id="gridAutos">
    <?php if (empty($disponibles)): ?>
        <div class="col-12">
            <div class="alert alert-info text-center">
                <div style="font-size:3rem">🚗</div>
                No hay automóviles disponibles en este momento. Vuelve pronto.
            </div>
        </div>
    <?php else: ?>
    <?php foreach ($disponibles as $a): ?>
        <div class="col-md-4 col-lg-3 auto-item"
             data-modelo="<?= htmlspecialchars($a['nombre_marca'].' '.$a['nombre_modelo']) ?>"
             data-precio="<?= $a['precio_base'] ?>">
            <div class="card auto-card h-100 border-0 shadow-sm">
                <!-- Cabecera del card con degradado -->
                <div style="background:linear-gradient(135deg,#1a3c5e,#2980b9); padding:1.2rem 1rem; text-align:center;">
                    <div style="font-size:3rem">🚗</div>
                    <div class="text-white fw-bold mt-1"><?= htmlspecialchars($a['nombre_marca']) ?></div>
                    <div class="text-white-50 small"><?= htmlspecialchars($a['nombre_modelo']) ?></div>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small mb-3">
                        <li>🎨 <strong>Color:</strong> <?= htmlspecialchars($a['color']) ?></li>
                        <li>📅 <strong>Año:</strong> <?= $a['anio_fabricacion'] ?></li>
                        <li>🏢 <strong>Sede:</strong> <?= htmlspecialchars($a['nombre_concesionario']) ?></li>
                        <li>🔖 <strong>Bastidor:</strong> <code style="font-size:.7rem"><?= htmlspecialchars($a['num_bastidor']) ?></code></li>
                    </ul>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge badge-disponible px-2 py-1">✅ Disponible</span>
                        <span class="precio-tag">$<?= number_format($a['precio_base'], 0, ',', '.') ?></span>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pt-0">
                    <a href="/ProyFinal/app/view/usuario/comprar.php?bastidor=<?= urlencode($a['num_bastidor']) ?>"
                       class="btn btn-success w-100 fw-semibold">
                        🛒 Comprar / Reservar
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php endif; ?>
    </div>
    <p class="text-muted small mt-3" id="contadorAutos"></p>
</div>

<script src="/ProyFinal/public/js/sweetalert2.all.min.js"></script>
<script>
const items = document.querySelectorAll('.auto-item');

function aplicarFiltros() {
    const texto  = document.getElementById('filtro_texto').value.toLowerCase().trim();
    const modelo = document.getElementById('filtro_modelo').value.toLowerCase();
    const precio = parseFloat(document.getElementById('filtro_precio').value) || Infinity;
    let v = 0;
    items.forEach(item => {
        const m = item.dataset.modelo.toLowerCase();
        const p = parseFloat(item.dataset.precio);
        const ok = (!texto || m.includes(texto) || item.textContent.toLowerCase().includes(texto))
                && (!modelo || m.includes(modelo))
                && p <= precio;
        item.style.display = ok ? '' : 'none';
        if (ok) v++;
    });
    const tot = items.length;
    document.getElementById('contadorAutos').textContent =
        (texto||modelo||precio<Infinity) ? `${v} de ${tot} autos mostrados` : '';
}
function limpiarFiltros() {
    document.getElementById('filtro_texto').value  = '';
    document.getElementById('filtro_modelo').value = '';
    document.getElementById('filtro_precio').value = '';
    aplicarFiltros();
}
document.getElementById('filtro_texto').addEventListener('input',  aplicarFiltros);
document.getElementById('filtro_modelo').addEventListener('change', aplicarFiltros);
document.getElementById('filtro_precio').addEventListener('change', aplicarFiltros);
</script>
</body>
</html>
