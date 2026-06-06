<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Automóviles – AutoColombia</title>
    <link rel="stylesheet" href="/ProyFinal/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php
require_once __DIR__ . '/../../../../app/services/session.php';
verificarPerfil(['admin', 'vendedor']);

require_once __DIR__ . '/../../../../app/model/auto.php';
$obj           = new Auto();
$automoviles   = $obj->listar();
$modelos       = $obj->listarModelos();
$concesionarios = $obj->listarConcesionarios();
$servicios     = $obj->listarServicios();

// Solo admin puede crear/editar/eliminar
$es_admin = ($_SESSION['perfil'] === 'admin');
?>

<?php include('../navbar.php'); ?>

        <main class="col-md-10 ms-sm-auto px-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>🚘 Inventario de Automóviles</h4>
                <?php if ($es_admin): ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAuto">
                    + Nuevo Automóvil
                </button>
                <?php endif; ?>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Bastidor</th>
                                    <th>Marca / Modelo</th>
                                    <th>Color</th>
                                    <th>Año</th>
                                    <th>Estado</th>
                                    <th>Precio Base</th>
                                    <th>Concesionario</th>
                                    <?php if ($es_admin): ?><th>Acciones</th><?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($automoviles)): ?>
                                <tr><td colspan="8" class="text-center py-3 text-muted">No hay automóviles registrados</td></tr>
                            <?php else: ?>
                            <?php foreach ($automoviles as $a): ?>
                                <tr>
                                    <td><code><?= htmlspecialchars($a['num_bastidor']) ?></code></td>
                                    <td><?= htmlspecialchars($a['nombre_marca'] . ' ' . $a['nombre_modelo']) ?></td>
                                    <td><?= htmlspecialchars($a['color']) ?></td>
                                    <td><?= $a['anio_fabricacion'] ?></td>
                                    <td>
                                        <?php
                                        $badges = [
                                            'disponible' => 'badge-disponible',
                                            'vendido'    => 'badge-vendido',
                                            'reservado'  => 'badge-reservado'
                                        ];
                                        $cls = $badges[$a['estado']] ?? 'bg-secondary';
                                        ?>
                                        <span class="badge <?= $cls ?>"><?= ucfirst($a['estado']) ?></span>
                                    </td>
                                    <td>$<?= number_format($a['precio_base'], 0, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($a['nombre_concesionario']) ?></td>
                                    <?php if ($es_admin): ?>
                                    <td>
                                        <button class="btn btn-warning btn-accion"
                                            onclick="abrirEditar(
                                                '<?= htmlspecialchars($a['num_bastidor'], ENT_QUOTES) ?>',
                                                <?= $a['id_modelo'] ?>,
                                                '<?= htmlspecialchars($a['color'], ENT_QUOTES) ?>',
                                                <?= $a['anio_fabricacion'] ?>,
                                                '<?= $a['estado'] ?>',
                                                '<?= $a['ubicacion'] ?>',
                                                <?= $a['id_concesionario'] ?>,
                                                <?= $a['id_servicio'] ?? 'null' ?>,
                                                '<?= $a['fecha_ingreso'] ?>'
                                            )">✏️ Editar</button>

                                        <button class="btn btn-danger btn-accion"
                                            onclick="confirmarEliminar('<?= htmlspecialchars($a['num_bastidor'], ENT_QUOTES) ?>')">
                                            🗑️ Eliminar
                                        </button>
                                    </td>
                                    <?php endif; ?>
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

<!-- ══ MODAL AUTOMÓVIL ════════════════════════════════════════ -->
<?php if ($es_admin): ?>
<div class="modal fade" id="modalAuto" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header card-header-primary">
                <h5 class="modal-title text-white" id="tituloModal">Nuevo Automóvil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAuto" method="POST"
                  action="/ProyFinal/app/control/procesar_auto.php"
                  novalidate>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="accionModal" value="insertar">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Número de Bastidor *</label>
                            <input type="text" name="num_bastidor" id="mod_bastidor" class="form-control"
                                   required maxlength="17" minlength="5"
                                   style="text-transform:uppercase"
                                   placeholder="Ej: 8LNHM13N14Y410600">
                            <div class="invalid-feedback">Obligatorio, máx 17 caracteres</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Modelo *</label>
                            <select name="id_modelo" id="mod_modelo" class="form-select" required>
                                <option value="">Selecciona modelo</option>
                                <?php foreach ($modelos as $m): ?>
                                <option value="<?= $m['id_modelo'] ?>">
                                    <?= htmlspecialchars($m['nombre_completo']) ?>
                                    ($<?= number_format($m['precio_base'], 0, ',', '.') ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Selecciona un modelo</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Color *</label>
                            <input type="text" name="color" id="mod_color" class="form-control"
                                   required maxlength="50" placeholder="Ej: Blanco Perla">
                            <div class="invalid-feedback">Obligatorio</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Año de fabricación *</label>
                            <input type="number" name="anio_fabricacion" id="mod_anio" class="form-control"
                                   required min="2000" max="<?= date('Y') + 1 ?>"
                                   placeholder="<?= date('Y') ?>">
                            <div class="invalid-feedback">Año entre 2000 y <?= date('Y')+1 ?></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estado *</label>
                            <select name="estado" id="mod_estado" class="form-select" required>
                                <option value="">Selecciona estado</option>
                                <option value="disponible">Disponible</option>
                                <option value="vendido">Vendido</option>
                                <option value="reservado">Reservado</option>
                            </select>
                            <div class="invalid-feedback">Selecciona un estado</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Ubicación *</label>
                            <select name="ubicacion" id="mod_ubicacion" class="form-select" required>
                                <option value="">Selecciona ubicación</option>
                                <option value="concesionario">Concesionario</option>
                                <option value="servicio_oficial">Servicio Oficial</option>
                            </select>
                            <div class="invalid-feedback">Selecciona una ubicación</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha de ingreso *</label>
                            <input type="date" name="fecha_ingreso" id="mod_fecha" class="form-control"
                                   required max="<?= date('Y-m-d') ?>">
                            <div class="invalid-feedback">Obligatorio</div>
                        </div>
                        <div class="col-md-6">
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
                        <div class="col-md-6">
                            <label class="form-label">Servicio Oficial (opcional)</label>
                            <select name="id_servicio" id="mod_servicio" class="form-select">
                                <option value="">-- Ninguno --</option>
                                <?php foreach ($servicios as $s): ?>
                                <option value="<?= $s['id_servicio'] ?>">
                                    <?= htmlspecialchars($s['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
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
<?php endif; ?>

<script src="/ProyFinal/public/js/sweetalert2.all.min.js"></script>
<?php if ($es_admin): ?>
<script>
function abrirEditar(bastidor, id_modelo, color, anio, estado, ubicacion, id_conc, id_serv, fecha) {
    document.getElementById('tituloModal').textContent   = 'Editar Automóvil';
    document.getElementById('accionModal').value         = 'editar';
    document.getElementById('mod_bastidor').value        = bastidor;
    document.getElementById('mod_bastidor').readOnly     = true;   // No cambiar bastidor
    document.getElementById('mod_modelo').value          = id_modelo;
    document.getElementById('mod_color').value           = color;
    document.getElementById('mod_anio').value            = anio;
    document.getElementById('mod_estado').value          = estado;
    document.getElementById('mod_ubicacion').value       = ubicacion;
    document.getElementById('mod_concesionario').value   = id_conc;
    document.getElementById('mod_servicio').value        = id_serv || '';
    document.getElementById('mod_fecha').value           = fecha;

    new bootstrap.Modal(document.getElementById('modalAuto')).show();
}

document.querySelector('[data-bs-target="#modalAuto"]').addEventListener('click', function() {
    document.getElementById('tituloModal').textContent   = 'Nuevo Automóvil';
    document.getElementById('accionModal').value         = 'insertar';
    document.getElementById('mod_bastidor').readOnly     = false;
    document.getElementById('formAuto').reset();
    document.getElementById('formAuto').classList.remove('was-validated');
});

document.getElementById('formAuto').addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        this.classList.add('was-validated');
    }
});

function confirmarEliminar(bastidor) {
    Swal.fire({
        icon:              'warning',
        title:             '¿Eliminar automóvil?',
        text:              `¿Eliminar el vehículo con bastidor "${bastidor}"?`,
        showCancelButton:  true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText:  'Cancelar',
        confirmButtonColor:'#e74c3c'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = '/ProyFinal/app/control/procesar_auto.php?accion=eliminar&bastidor=' + bastidor;
        }
    });
}
</script>
<?php endif; ?>
</body>
</html>
