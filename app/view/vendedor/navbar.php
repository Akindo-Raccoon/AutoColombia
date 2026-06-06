<nav class="navbar navbar-expand-lg navbar-autocolombia navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/ProyFinal/app/view/usuario/dashboard.php">🚗 AutoColombia</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUsuario">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarUsuario">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        👤 <?= htmlspecialchars($_SESSION['nombre']) ?>
                        <span class="badge badge-usuario ms-1">Usuario</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/ProyFinal/app/control/salir.php">
                            🚪 Cerrar sesión
                        </a></li>
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
                <p class="text-white-50 small px-3 mt-2 mb-1 text-uppercase">Menú</p>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>"
                           href="/ProyFinal/app/view/vendedor/dashboard.php">
                            🏠 Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'catalogo') !== false ? 'active' : '' ?>"
                           href="/ProyFinal/app/view/vendedor/listarAuto.php">
                            🚘 Catálogo
                        </a>
                    </li>
                </ul>
            </div>
        </nav>