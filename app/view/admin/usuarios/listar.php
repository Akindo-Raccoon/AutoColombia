<!doctype html>
<html lang="es">

<!-- usuarios/listar.php -->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuarios – AutoColombia</title>
    <link rel="stylesheet" href="/ProyFinal/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php
    require_once __DIR__ . '/../../../../app/services/session.php';
    verificarPerfil('admin');

    require_once __DIR__ . '/../../../../app/model/usuario.php';
    require_once __DIR__ . '/../../../../app/model/vendedor.php';

    $obj_u = new Usuario();
    $obj_v = new Vendedor();

    // Si viene un ID en la URL, cargar datos para editar
    $editar = false;
    $usuario_editar = null;
    if (isset($_GET['editar'])) {
        $editar = true;
        $usuario_editar = $obj_u->buscarPorId($_GET['editar']);
    }

    // Listar todos los usuarios
    $usuarios = $obj_u->listar();
    $vendedores = $obj_v->listarConcesionarios(); // reutilizamos para el select
    $lista_vendedores = $obj_v->listar();            // para asignar vendedor al usuario
    ?>

    <!-- Navbar -->
    <?php include('../navbar.php'); ?>

    <!-- CONTENIDO -->
    <main class="col-md-10 ms-sm-auto px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>👥 Gestión de Usuarios</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                + Nuevo Usuario
            </button>
        </div>

        <!-- TABLA DE USUARIOS -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Perfil</th>
                                <th>Estado</th>
                                <th>Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usuarios)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-3 text-muted">No hay usuarios registrados</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($usuarios as $u): ?>
                                    <tr>
                                        <td><?= $u['id_usuario'] ?></td>
                                        <td><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellidos']) ?></td>
                                        <td><?= htmlspecialchars($u['email']) ?></td>
                                        <td>
                                            <?php
                                            $badge = ['admin' => 'badge-admin', 'vendedor' => 'badge-vendedor', 'usuario' => 'badge-usuario'];
                                            $cls = $badge[$u['perfil']] ?? 'bg-secondary';
                                            ?>
                                            <span class="badge <?= $cls ?>"><?= ucfirst($u['perfil']) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($u['activo']): ?>
                                                <span class="badge bg-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($u['fecha_registro'])) ?></td>
                                        <td>
                                            <!-- Botón editar: pasa ID al modal -->
                                            <button class="btn btn-warning btn-accion" onclick="abrirEditar(
                                                <?= $u['id_usuario'] ?>,
                                                '<?= htmlspecialchars($u['nombre'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($u['apellidos'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($u['email'], ENT_QUOTES) ?>',
                                                '<?= $u['perfil'] ?>',
                                                <?= $u['id_vendedor'] ?? 'null' ?>,
                                                <?= $u['activo'] ?>
                                            )">✏️ Editar</button>

                                            <!-- Botón eliminar con confirmación SweetAlert -->
                                            <button class="btn btn-danger btn-accion"
                                                onclick="confirmarEliminar(<?= $u['id_usuario'] ?>, '<?= htmlspecialchars($u['nombre'], ENT_QUOTES) ?>')">
                                                🗑️ Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    </div>
    </div>

    <!-- ══ MODAL CREAR USUARIO ════════════════════════════════════ -->
    <div class="modal fade" id="modalUsuario" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header card-header-primary">
                    <h5 class="modal-title text-white" id="tituloModal">Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <!-- El action cambia según si es crear o editar -->
                <form id="formUsuario" method="POST" action="/ProyFinal/app/control/procesar_usuario.php" novalidate>
                    <div class="modal-body">
                        <!-- Campo oculto para el ID (solo en edición) -->
                        <input type="hidden" name="accion" id="accionModal" value="insertar">
                        <input type="hidden" name="id_usuario" id="idModal" value="">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre *</label>
                                <input type="text" name="nombre" id="mod_nombre" class="form-control" required
                                    pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]+" title="Solo letras" placeholder="Ej: Carlos">
                                <div class="invalid-feedback">Solo letras, campo obligatorio</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Apellidos *</label>
                                <input type="text" name="apellidos" id="mod_apellidos" class="form-control" required
                                    pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]+" title="Solo letras"
                                    placeholder="Ej: Ramírez Torres">
                                <div class="invalid-feedback">Solo letras, campo obligatorio</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" id="mod_email" class="form-control" required
                                    placeholder="usuario@email.com">
                                <div class="invalid-feedback">Ingresa un email válido</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Perfil *</label>
                                <select name="perfil" id="mod_perfil" class="form-select" required>
                                    <option value="">Selecciona perfil</option>
                                    <option value="admin">Administrador</option>
                                    <option value="vendedor">Vendedor</option>
                                    <option value="usuario">Usuario</option>
                                </select>
                                <div class="invalid-feedback">Selecciona un perfil</div>
                            </div>
                            <!-- Contraseña: solo en creación, opcional en edición -->
                            <div class="col-md-6" id="campoPassword">
                                <label class="form-label">Contraseña *</label>
                                <input type="password" name="password" id="mod_password" class="form-control" required
                                    minlength="6" placeholder="Mínimo 6 caracteres">
                                <div class="invalid-feedback">Mínimo 6 caracteres</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vendedor asociado (opcional)</label>
                                <select name="id_vendedor" id="mod_vendedor" class="form-select">
                                    <option value="">-- Ninguno --</option>
                                    <?php foreach ($lista_vendedores as $v): ?>
                                        <option value="<?= $v['id_vendedor'] ?>">
                                            <?= htmlspecialchars($v['nombre'] . ' ' . $v['apellidos']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- Activo: solo en edición -->
                            <div class="col-md-6 d-none" id="campoActivo">
                                <label class="form-label">Estado</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="activo" id="mod_activo"
                                        value="1">
                                    <label class="form-check-label" for="mod_activo">Usuario activo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardarUsuario">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include("../../layouts/footer.php"); ?>

    <script src="/ProyFinal/public/js/sweetalert2.all.min.js"></script>
    <script>
        // ── Abrir modal en modo EDITAR ──────────────────────────────
        function abrirEditar(id, nombre, apellidos, email, perfil, id_vendedor, activo) {
            document.getElementById('tituloModal').textContent = 'Editar Usuario';
            document.getElementById('accionModal').value = 'editar';
            document.getElementById('idModal').value = id;
            document.getElementById('mod_nombre').value = nombre;
            document.getElementById('mod_apellidos').value = apellidos;
            document.getElementById('mod_email').value = email;
            document.getElementById('mod_perfil').value = perfil;
            document.getElementById('mod_activo').checked = activo == 1;

            // En edición, la contraseña no es obligatoria
            const campoPw = document.getElementById('campoPassword');
            const inputPw = document.getElementById('mod_password');
            campoPw.classList.add('d-none');
            inputPw.removeAttribute('required');
            inputPw.value = '';

            // Mostrar campo activo
            document.getElementById('campoActivo').classList.remove('d-none');

            // Asignar vendedor si tiene
            if (id_vendedor) {
                document.getElementById('mod_vendedor').value = id_vendedor;
            }

            // Abrir el modal
            new bootstrap.Modal(document.getElementById('modalUsuario')).show();
        }

        // Al abrir modal en modo CREAR: resetear
        document.querySelector('[data-bs-target="#modalUsuario"]').addEventListener('click', function () {
            document.getElementById('tituloModal').textContent = 'Nuevo Usuario';
            document.getElementById('accionModal').value = 'insertar';
            document.getElementById('idModal').value = '';
            document.getElementById('formUsuario').reset();
            document.getElementById('formUsuario').classList.remove('was-validated');

            // Mostrar campo contraseña obligatorio
            const campoPw = document.getElementById('campoPassword');
            const inputPw = document.getElementById('mod_password');
            campoPw.classList.remove('d-none');
            inputPw.setAttribute('required', true);

            // Ocultar campo activo
            document.getElementById('campoActivo').classList.add('d-none');
        });

        // ── Validar formulario antes de enviar ──────────────────────
        document.getElementById('formUsuario').addEventListener('submit', function (e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                this.classList.add('was-validated');
            }
        });

        // ── Confirmar eliminación con SweetAlert ────────────────────
        function confirmarEliminar(id, nombre) {
            Swal.fire({
                icon: 'warning',
                title: '¿Eliminar usuario?',
                text: `¿Seguro que deseas eliminar a "${nombre}"? Esta acción no se puede deshacer.`,
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#e74c3c'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = '/ProyFinal/app/control/procesar_usuario.php?accion=eliminar&id=' + id;
                }
            });
        }
    </script>
</body>

</html>