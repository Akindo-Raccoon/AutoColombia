<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Usuario – AutoColombia</title>
    <link rel="stylesheet" href="/ProyFinal/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php
    require_once __DIR__ . '/../../../app/services/session.php';
    verificarPerfil('usuario');

    require_once __DIR__ . '/../../../app/model/auto.php';
    require_once __DIR__ . '/../../../app/model/venta.php';
    $obj = new Auto();
    $automoviles = $obj->listar();
    $disponibles = array_filter($automoviles, fn($a) => $a['estado'] === 'disponible');

    $objV = new Venta();
    $compras = $objV->listarPorUsuario($_SESSION['id_usuario']);
    ?>

    <!-- Navbar usuario -->
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

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm" style="background: linear-gradient(135deg,#27ae60,#2ecc71); border:none;">
                    <div class="card-body text-white p-4">
                        <h3 class="fw-bold">¡Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?>! 👋</h3>
                        <p class="mb-0 fs-5">Encuentra el auto de tus sueños en AutoColombia.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <div style="font-size: 4rem;">🚘</div>
                    <h4 class="mt-3">Catálogo de Vehículos</h4>
                    <p class="text-muted">Explora nuestra amplia selección de automóviles. Tenemos <strong><?= count($disponibles) ?></strong> vehículos disponibles.</p>
                    <a href="/ProyFinal/app/view/usuario/catalogo.php" class="btn btn-primary mt-auto">Ir al Catálogo</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <div style="font-size: 4rem;">🧾</div>
                    <h4 class="mt-3">Mis Compras</h4>
                    <p class="text-muted">Revisa el historial de tus adquisiciones y descarga tus recibos. Tienes <strong><?= count($compras) ?></strong> compras realizadas.</p>
                    <a href="/ProyFinal/app/view/usuario/mis_compras.php" class="btn btn-success mt-auto">Ver mis compras</a>
                </div>
            </div>
        </div>
    </div>    
    <script src="/ProyFinal/public/js/sweetalert2.all.min.js"></script>
</body>
</html>