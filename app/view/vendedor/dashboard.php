<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Vendedor – AutoColombia</title>
    <link rel="stylesheet" href="/ProyFinal/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php
    require_once __DIR__ . '/../../../app/services/session.php';
    verificarPerfil('vendedor');
    ?>

    <!-- Navbar vendedor -->
    <nav class="navbar navbar-expand-lg navbar-autocolombia navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">🚗 AutoColombia</a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown">
                        👤 <?= htmlspecialchars($_SESSION['nombre']) ?>
                        <span class="badge bg-info ms-1">Vendedor</span>
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
                <div class="card card-header-primary">
                    <div class="card-body text-white">
                        <h4>¡Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?>! 👋</h4>
                        <p class="mb-0">Panel de Vendedor – AutoColombia</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Ver inventario de automóviles -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body text-center py-4">
                        <div style="font-size:3rem">🚘</div>
                        <h5 class="mt-2">Ver Inventario</h5>
                        <p class="text-muted">Consulta los automóviles disponibles en el concesionario</p>
                        <a href="/ProyFinal/app/view/vendedor/listarAuto.php" class="btn btn-primary">Ver
                            Automóviles</a>
                    </div>
                </div>
            </div>
            <!-- Información del sistema -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body text-center py-4">
                        <div style="font-size:3rem">ℹ️</div>
                        <h5 class="mt-2">Mi Cuenta</h5>
                        <p class="text-muted">
                            <strong>Email:</strong> <?= htmlspecialchars($_SESSION['email']) ?><br>
                            <strong>Perfil:</strong> Vendedor
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/ProyFinal/public/js/sweetalert2.all.min.js"></script>
</body>

</html>