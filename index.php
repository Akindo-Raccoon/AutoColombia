<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AutoColombia – Acceso</title>

    <link rel="stylesheet" href="./public/css/bootstrap.min.css">
    <link rel="stylesheet" href="./public/css/sweetalert2.min.css">
    <link rel="stylesheet" href="./public/css/style.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <?php
    session_start();

    $error_login = '';
    if (isset($_SESSION['error_login'])) {
        $error_login = $_SESSION['error_login'];
        unset($_SESSION['error_login']);
    }

    $accion = isset($_GET['accion']) ? $_GET['accion'] : 'login';
    ?>

    <div class="login-wrapper">
        <div class="login-card card shadow-lg">

            <!-- Encabezado -->
            <div class="card-header-primary text-center py-4">
                <div class="login-logo mb-2">🚗</div>
                <h4 class="mb-0 fw-bold text-white">AutoColombia</h4>
                <small class="text-white-50">Sistema de Gestión de Concesionario</small>
            </div>

            <div class="card-body p-4 bg-white">

                <!-- Pestañas -->
                <ul class="nav nav-pills nav-fill mb-4">
                    <li class="nav-item">
                        <a class="nav-link <?= $accion === 'login' ? 'active' : '' ?>" href="index.php?accion=login">
                            Iniciar sesión
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $accion === 'registro' ? 'active' : '' ?>"
                            href="index.php?accion=registro">
                            Registrarse
                        </a>
                    </li>
                </ul>

                <?php if ($accion === 'login'): ?>
                    <!-- 
                 FORMULARIO DE LOGIN -->
                    <form id="formLogin" novalidate>

                        <!-- Selector de rol -->
                        <p class="text-muted small fw-semibold text-uppercase mb-2">Ingresar como</p>
                        <div class="row g-2 mb-4" role="group" aria-label="Tipo de usuario">

                            <div class="col-4">
                                <input type="radio" class="btn-check" name="rol_login" id="rol_usuario" value="usuario"
                                    autocomplete="off" checked>
                                <label
                                    class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center gap-1 py-2"
                                    for="rol_usuario">
                                    <span class="fs-4">👤</span>
                                    <span class="small fw-semibold">Usuario</span>
                                </label>
                            </div>

                            <div class="col-4">
                                <input type="radio" class="btn-check" name="rol_login" id="rol_vendedor" value="vendedor"
                                    autocomplete="off">
                                <label
                                    class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center gap-1 py-2"
                                    for="rol_vendedor">
                                    <span class="fs-4">💼</span>
                                    <span class="small fw-semibold">Vendedor</span>
                                </label>
                            </div>

                            <div class="col-4">
                                <input type="radio" class="btn-check" name="rol_login" id="rol_admin" value="admin"
                                    autocomplete="off">
                                <label
                                    class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center gap-1 py-2"
                                    for="rol_admin">
                                    <span class="fs-4">🛡️</span>
                                    <span class="small fw-semibold">Admin</span>
                                </label>
                            </div>

                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" id="email" class="form-control" placeholder="tu@email.com" required
                                autocomplete="username">
                            <div class="invalid-feedback">Ingresa un correo válido</div>
                        </div>

                        <!-- Contraseña -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <input type="password" id="password" class="form-control" placeholder="••••••••" required
                                    minlength="4" autocomplete="current-password">
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePass('password', this)" aria-label="Mostrar contraseña">👁</button>
                            </div>
                            <div class="invalid-feedback">La contraseña es obligatoria</div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Ingresar al sistema
                            </button>
                        </div>

                        <div class="text-center">
                            <a href="#" id="btnRecuperarPass" class="text-muted small">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>

                    </form>

                <?php else: ?>
                    <!-- 
                 FORMULARIO DE REGISTRO-->
                    <form id="formRegistro" novalidate>

                        <!-- Selector de rol -->
                        <p class="text-muted small fw-semibold text-uppercase mb-2">Registrarse como</p>
                        <div class="row g-2 mb-4" role="group" aria-label="Tipo de cuenta">

                            <div class="col-6">
                                <input type="radio" class="btn-check" name="rol_registro" id="reg_rol_usuario"
                                    value="usuario" autocomplete="off" checked>
                                <label
                                    class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center gap-1 py-2"
                                    for="reg_rol_usuario">
                                    <span class="fs-4">👤</span>
                                    <span class="small fw-semibold">Usuario</span>
                                </label>
                            </div>

                            <div class="col-6">
                                <input type="radio" class="btn-check" name="rol_registro" id="reg_rol_vendedor"
                                    value="vendedor" autocomplete="off">
                                <label
                                    class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center gap-1 py-2"
                                    for="reg_rol_vendedor">
                                    <span class="fs-4">💼</span>
                                    <span class="small fw-semibold">Vendedor</span>
                                </label>
                            </div>

                        </div>

                        <!-- Nombre y apellidos -->
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="reg_nombre" class="form-label">Nombre</label>
                                <input type="text" id="reg_nombre" class="form-control" placeholder="Nombre" required
                                    pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]+" title="Solo letras">
                                <div class="invalid-feedback">Solo letras, obligatorio</div>
                            </div>
                            <div class="col-6">
                                <label for="reg_apellidos" class="form-label">Apellidos</label>
                                <input type="text" id="reg_apellidos" class="form-control" placeholder="Apellidos" required
                                    pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]+" title="Solo letras">
                                <div class="invalid-feedback">Solo letras, obligatorio</div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="reg_email" class="form-label">Correo electrónico</label>
                            <input type="email" id="reg_email" class="form-control" placeholder="tu@email.com" required
                                autocomplete="email">
                            <div class="invalid-feedback">Ingresa un correo válido</div>
                        </div>

                        <!-- Contraseñas -->
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label for="reg_password" class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <input type="password" id="reg_password" class="form-control"
                                        placeholder="Mín. 6 caracteres" required minlength="6" autocomplete="new-password">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePass('reg_password', this)"
                                        aria-label="Mostrar contraseña">👁</button>
                                </div>
                                <div class="invalid-feedback">Mínimo 6 caracteres</div>
                            </div>
                            <div class="col-6">
                                <label for="reg_confirmar" class="form-label">Confirmar</label>
                                <div class="input-group">
                                    <input type="password" id="reg_confirmar" class="form-control"
                                        placeholder="Repite la clave" required minlength="6" autocomplete="new-password">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePass('reg_confirmar', this)"
                                        aria-label="Mostrar contraseña">👁</button>
                                </div>
                                <div class="invalid-feedback">Deben coincidir</div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                Crear mi cuenta
                            </button>
                        </div>

                    </form>
                <?php endif; ?>

            </div><!-- /card-body -->
        </div><!-- /login-card -->
    </div><!-- /login-wrapper -->

    <?php include("app/view/layouts/footer.php"); ?>

    <script src="./public/js/sweetalert2.all.min.js"></script>

    <script type="module">
        import { loginFirebase, registrarFirebase, recuperarPassword }
            from './public/js/firebase-config.js';

        /* ── Helpers ── */
        window.togglePass = function (id, btn) {
            const campo = document.getElementById(id);
            campo.type = campo.type === 'password' ? 'text' : 'password';
            btn.textContent = campo.type === 'password' ? '👁' : '🙈';
        };

        function loader(titulo) {
            Swal.fire({
                title: titulo, allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }

        function addHidden(form, name, value) {
            const i = document.createElement('input');
            i.type = 'hidden'; i.name = name; i.value = value;
            form.appendChild(i);
        }

        <?php if ($error_login): ?>
            Swal.fire({
                icon: 'warning', title: 'Sesión cerrada',
                text: '<?= htmlspecialchars($error_login) ?>'
            });
        <?php endif; ?>

        /* ── LOGIN ── */
        const formLogin = document.getElementById('formLogin');
        if (formLogin) {
            formLogin.addEventListener('submit', async function (e) {
                e.preventDefault();
                if (!formLogin.checkValidity()) {
                    formLogin.classList.add('was-validated');
                    return;
                }

                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value;
                const rol = document.querySelector('input[name="rol_login"]:checked')?.value ?? 'usuario';

                loader('Iniciando sesión...');

                try {
                    await loginFirebase(email, password);

                    const f = document.createElement('form');
                    f.method = 'POST';
                    f.action = 'app/control/procesar_login.php';
                    addHidden(f, 'email', email);
                    addHidden(f, 'password', password);
                    addHidden(f, 'rol', rol);
                    document.body.appendChild(f);
                    f.submit();

                } catch (err) {
                    const msgs = {
                        'auth/user-not-found': 'No existe una cuenta con ese correo.',
                        'auth/wrong-password': 'Contraseña incorrecta.',
                        'auth/invalid-email': 'El correo no tiene formato válido.',
                        'auth/too-many-requests': 'Demasiados intentos. Intenta más tarde.',
                    };
                    Swal.fire({
                        icon: 'error', title: 'Error de acceso',
                        text: msgs[err.code] || 'Credenciales incorrectas. Verifica e intenta de nuevo.'
                    });
                }
            });
        }

        /* ── REGISTRO ── */
        const formRegistro = document.getElementById('formRegistro');
        if (formRegistro) {
            formRegistro.addEventListener('submit', async function (e) {
                e.preventDefault();
                if (!formRegistro.checkValidity()) {
                    formRegistro.classList.add('was-validated');
                    return;
                }

                const nombre = document.getElementById('reg_nombre').value.trim();
                const apellidos = document.getElementById('reg_apellidos').value.trim();
                const email = document.getElementById('reg_email').value.trim();
                const password = document.getElementById('reg_password').value;
                const confirmar = document.getElementById('reg_confirmar').value;
                const rol = document.querySelector('input[name="rol_registro"]:checked')?.value ?? 'usuario';

                if (password !== confirmar) {
                    Swal.fire({
                        icon: 'error', title: 'Contraseñas distintas',
                        text: 'Las contraseñas no coinciden. Verifica.'
                    });
                    return;
                }

                loader('Creando cuenta...');

                try {
                    await registrarFirebase(email, password);

                    const f = document.createElement('form');
                    f.method = 'POST';
                    f.action = 'app/control/procesar_registro.php';
                    addHidden(f, 'nombre', nombre);
                    addHidden(f, 'apellidos', apellidos);
                    addHidden(f, 'email', email);
                    addHidden(f, 'password', password);
                    addHidden(f, 'confirmar', password);
                    addHidden(f, 'rol', rol);
                    document.body.appendChild(f);
                    f.submit();

                } catch (err) {
                    const msgs = {
                        'auth/email-already-in-use': 'Este correo ya tiene una cuenta registrada.',
                        'auth/weak-password': 'La contraseña debe tener al menos 6 caracteres.',
                        'auth/invalid-email': 'El correo no tiene formato válido.',
                    };
                    Swal.fire({
                        icon: 'error', title: 'Error al registrar',
                        text: msgs[err.code] || 'No se pudo crear la cuenta. Intenta de nuevo.'
                    });
                }
            });
        }

        /* ── RECUPERAR CONTRASEÑA ── */
        const btnRecuperar = document.getElementById('btnRecuperarPass');
        if (btnRecuperar) {
            btnRecuperar.addEventListener('click', async function (e) {
                e.preventDefault();
                const { value: email } = await Swal.fire({
                    title: 'Recuperar contraseña',
                    text: 'Te enviaremos un enlace de recuperación a tu correo',
                    input: 'email',
                    inputPlaceholder: 'tu@email.com',
                    showCancelButton: true,
                    confirmButtonText: 'Enviar enlace',
                    cancelButtonText: 'Cancelar',
                    inputValidator: v => !v ? 'Debes ingresar tu correo' : undefined
                });
                if (!email) return;
                try {
                    await recuperarPassword(email);
                    Swal.fire({
                        icon: 'success', title: '¡Correo enviado!',
                        text: `Revisa tu bandeja en ${email}. Si no llega, revisa spam.`
                    });
                } catch (err) {
                    const msg = err.code === 'auth/user-not-found'
                        ? 'No existe una cuenta con ese correo.'
                        : 'No se pudo enviar el correo.';
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                }
            });
        }
    </script>

</body>

</html>