<nav class="navbar navbar-expand-lg navbar-autocolombia navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/ProyFinal/app/view/admin/dashboard.php">🚗 AutoColombia</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarAdmin">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        🛡️ <?= htmlspecialchars($_SESSION['nombre']) ?>
                        <span class="badge bg-warning text-dark ms-1">Admin</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/ProyFinal/app/control/salir.php">🚪 Cerrar Sesión</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- Sidebar + Contenido -->
<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <nav class="col-md-2 d-md-block sidebar py-3">
            <div class="d-flex flex-column">

                <p class="text-white-50 small px-3 mt-2 mb-1 text-uppercase">Principal</p>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' && strpos($_SERVER['PHP_SELF'],'admin') !== false ? 'active' : '' ?>"
                            href="/ProyFinal/app/view/admin/dashboard.php">🏠 Dashboard</a>
                    </li>
                </ul>

                <p class="text-white-50 small px-3 mb-1 text-uppercase">Catálogo</p>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/marcas/') !== false ? 'active' : '' ?>"
                            href="/ProyFinal/app/view/admin/marcas/listar.php">🏷️ Marcas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/modelos/') !== false ? 'active' : '' ?>"
                            href="/ProyFinal/app/view/admin/modelos/listar.php">🚙 Modelos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/automoviles/') !== false ? 'active' : '' ?>"
                            href="/ProyFinal/app/view/admin/automoviles/listar.php">🚘 Automóviles</a>
                    </li>
                </ul>

                <p class="text-white-50 small px-3 mb-1 text-uppercase">Personas</p>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/usuarios/') !== false ? 'active' : '' ?>"
                            href="/ProyFinal/app/view/admin/usuarios/listar.php">👥 Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/vendedores/') !== false ? 'active' : '' ?>"
                            href="/ProyFinal/app/view/admin/vendedores/listar.php">🧑‍💼 Vendedores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/concesionarios/') !== false ? 'active' : '' ?>"
                            href="/ProyFinal/app/view/admin/concesionarios/listar.php">🏢 Concesionarios</a>
                    </li>
                </ul>

                <p class="text-white-50 small px-3 mb-1 text-uppercase">Ventas</p>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/ventas/') !== false ? 'active' : '' ?>"
                            href="/ProyFinal/app/view/admin/ventas/listar.php">🧾 Ventas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/formas_pago/') !== false ? 'active' : '' ?>"
                            href="/ProyFinal/app/view/admin/formas_pago/listar.php">💳 Formas de Pago</a>
                    </li>
                </ul>

                <p class="text-white-50 small px-3 mb-1 text-uppercase">Reportes</p>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/reportes/') !== false ? 'active' : '' ?>"
                            href="/ProyFinal/app/view/admin/reportes/index.php">📊 Reportes</a>
                    </li>
                </ul>

            </div>
        </nav>