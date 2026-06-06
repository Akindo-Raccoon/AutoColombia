<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vendedores – AutoColombia</title>
    <link rel="stylesheet" href="/ProyFinal/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php
    require_once __DIR__ . '/../../../../app/services/session.php';
    verificarPerfil('admin');

    require_once __DIR__ . '/../../../../app/model/vendedor.php';
    $obj = new Vendedor();
    $vendedores = $obj->listar();
    $concesionarios = $obj->listarConcesionarios();
    ?>

    <?php include('../navbar.php'); ?>

    <main class="col-md-10 ms-sm-auto px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>🧑‍💼 Gestión de Vendedores</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalVendedor">
                + Nuevo Vendedor
            </button>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>NIF/CC</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Concesionario</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($vendedores)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-3 text-muted">No hay vendedores registrados</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($vendedores as $v): ?>
                                    <tr>
                                        <td><?= $v['id_vendedor'] ?></td>
                                        <td><?= htmlspecialchars($v['nombre'] . ' ' . $v['apellidos']) ?></td>
                                        <td><?= htmlspecialchars($v['nif']) ?></td>
                                        <td><?= htmlspecialchars($v['telefono']) ?></td>
                                        <td><?= htmlspecialchars($v['email']) ?></td>
                                        <td><?= htmlspecialchars($v['nombre_concesionario']) ?></td>
                                        <td>
                                            <?php if ($v['activo']): ?>
                                                <span class="badge bg-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-accion" onclick="abrirEditar(
                                                <?= $v['id_vendedor'] ?>,
                                                '<?= htmlspecialchars($v['nombre'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($v['apellidos'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($v['nif'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($v['domicilio'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($v['telefono'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($v['email'], ENT_QUOTES) ?>',
                                                '<?= $v['fecha_contratacion'] ?>',
                                                <?= $v['id_concesionario'] ?>,
                                                <?= $v['activo'] ?>
                                            )">✏️ Editar</button>

                                            <button class="btn btn-danger btn-accion"
                                                onclick="confirmarEliminar(<?= $v['id_vendedor'] ?>, '<?= htmlspecialchars($v['nombre'], ENT_QUOTES) ?>')">
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

    <!-- ══ MODAL VENDEDOR ════════════════════════════════════════ -->
    <div class="modal fade" id="modalVendedor" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header card-header-primary">
                    <h5 class="modal-title text-white" id="tituloModal">Nuevo Vendedor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formVendedor" method="POST" action="/ProyFinal/app/control/procesar_vendedor.php" novalidate>
                    <div class="modal-body">
                        <input type="hidden" name="accion" id="accionModal" value="insertar">
                        <input type="hidden" name="id_vendedor" id="idModal" value="">

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Nombre *</label>
                                <input type="text" name="nombre" id="mod_nombre" class="form-control" required
                                    pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]+" placeholder="Nombre del vendedor">
                                <div class="invalid-feedback">Solo letras, obligatorio</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Apellidos *</label>
                                <input type="text" name="apellidos" id="mod_apellidos" class="form-control" required
                                    pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]+" placeholder="Apellidos">
                                <div class="invalid-feedback">Solo letras, obligatorio</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">NIF / Cédula *</label>
                                <input type="text" name="nif" id="mod_nif" class="form-control" required
                                    pattern="\d{6,12}" title="Solo números, 6 a 12 dígitos"
                                    placeholder="Ej: 1020304050">
                                <div class="invalid-feedback">Solo números, 6-12 dígitos</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Domicilio</label>
                                <input type="text" name="domicilio" id="mod_domicilio" class="form-control"
                                    placeholder="Dirección completa">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" name="telefono" id="mod_telefono" class="form-control"
                                    pattern="\d{7,15}" title="Solo números" placeholder="Ej: 3101234567">
                                <div class="invalid-feedback">Solo números</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" id="mod_email" class="form-control"
                                    placeholder="vendedor@email.com">
                                <div class="invalid-feedback">Email inválido</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fecha de contratación</label>
                                <input type="date" name="fecha_contratacion" id="mod_fecha" class="form-control"
                                    max="<?= date('Y-m-d') ?>">
                                <div class="invalid-feedback">Fecha no puede ser futura</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Concesionario *</label>
                                <select name="id_concesionario" id="mod_concesionario" class="form-select" required>
                                    <option value="">Selecciona concesionario</option>
                                    <?php foreach ($concesionarios as $c): ?>
                                        <option value="<?= $c['id_concesionario'] ?>">
                                            <?= htmlspecialchars($c['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Selecciona un concesionario</div>
                            </div>
                            <div class="col-md-4 d-none" id="campoActivo">
                                <label class="form-label">Estado</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="activo" id="mod_activo"
                                        value="1">
                                    <label class="form-check-label" for="mod_activo">Vendedor activo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include("../../layouts/footer.php"); ?>
    
    <script src="/ProyFinal/public/js/sweetalert2.all.min.js"></script>
    <script>
        function abrirEditar(id, nombre, apellidos, nif, domicilio, telefono, email, fecha, id_concesionario, activo) {
            document.getElementById('tituloModal').textContent = 'Editar Vendedor';
            document.getElementById('accionModal').value = 'editar';
            document.getElementById('idModal').value = id;
            document.getElementById('mod_nombre').value = nombre;
            document.getElementById('mod_apellidos').value = apellidos;
            document.getElementById('mod_nif').value = nif;
            document.getElementById('mod_domicilio').value = domicilio;
            document.getElementById('mod_telefono').value = telefono;
            document.getElementById('mod_email').value = email;
            document.getElementById('mod_fecha').value = fecha;
            document.getElementById('mod_concesionario').value = id_concesionario;
            document.getElementById('mod_activo').checked = activo == 1;
            document.getElementById('campoActivo').classList.remove('d-none');

            new bootstrap.Modal(document.getElementById('modalVendedor')).show();
        }

        document.querySelector('[data-bs-target="#modalVendedor"]').addEventListener('click', function () {
            document.getElementById('tituloModal').textContent = 'Nuevo Vendedor';
            document.getElementById('accionModal').value = 'insertar';
            document.getElementById('idModal').value = '';
            document.getElementById('formVendedor').reset();
            document.getElementById('formVendedor').classList.remove('was-validated');
            document.getElementById('campoActivo').classList.add('d-none');
        });

        document.getElementById('formVendedor').addEventListener('submit', function (e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                this.classList.add('was-validated');
            }
        });

        function confirmarEliminar(id, nombre) {
            Swal.fire({
                icon: 'warning',
                title: '¿Eliminar vendedor?',
                text: `¿Eliminar a "${nombre}"? Esta acción no se puede deshacer.`,
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#e74c3c'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = '/ProyFinal/app/control/procesar_vendedor.php?accion=eliminar&id=' + id;
                }
            });
        }
    </script>
</body>

</html>