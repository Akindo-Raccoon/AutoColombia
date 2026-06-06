<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Marcas – AutoColombia</title>
    <link rel="stylesheet" href="/ProyFinal/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php
require_once __DIR__ . '/../../../../app/services/session.php';
verificarPerfil('admin');
require_once __DIR__ . '/../../../../app/model/marca.php';
$obj    = new Marca();
$marcas = $obj->listar();
?>
<?php include('../navbar.php'); ?>

<main class="col-md-10 ms-sm-auto px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>🏷️ Gestión de Marcas</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMarca">
            + Nueva Marca
        </button>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th><th>Nombre</th><th>País de Origen</th><th>Descripción</th><th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($marcas)): ?>
                        <tr><td colspan="5" class="text-center py-3 text-muted">No hay marcas registradas</td></tr>
                    <?php else: ?>
                    <?php foreach ($marcas as $m): ?>
                        <tr>
                            <td><?= $m['id_marca'] ?></td>
                            <td><strong><?= htmlspecialchars($m['nombre']) ?></strong></td>
                            <td><?= htmlspecialchars($m['pais_origen']) ?></td>
                            <td class="text-muted small"><?= htmlspecialchars($m['descripcion']) ?></td>
                            <td>
                                <button class="btn btn-warning btn-accion"
                                    onclick="abrirEditar(<?= $m['id_marca'] ?>,'<?= htmlspecialchars($m['nombre'], ENT_QUOTES) ?>','<?= htmlspecialchars($m['pais_origen'], ENT_QUOTES) ?>','<?= htmlspecialchars($m['descripcion'], ENT_QUOTES) ?>')">
                                    ✏️ Editar
                                </button>
                                <button class="btn btn-danger btn-accion"
                                    onclick="confirmarEliminar(<?= $m['id_marca'] ?>,'<?= htmlspecialchars($m['nombre'], ENT_QUOTES) ?>')">
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

<!-- MODAL MARCA -->
<div class="modal fade" id="modalMarca" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header card-header-primary">
                <h5 class="modal-title text-white" id="tituloModal">Nueva Marca</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formMarca" method="POST" action="/ProyFinal/app/control/procesar_marca.php" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="accionModal" value="insertar">
                    <input type="hidden" name="id_marca" id="idModal" value="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre de la marca *</label>
                            <input type="text" name="nombre" id="mod_nombre" class="form-control" required
                                placeholder="Ej: Toyota">
                            <div class="invalid-feedback">Campo obligatorio</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">País de origen</label>
                            <input type="text" name="pais_origen" id="mod_pais" class="form-control"
                                placeholder="Ej: Japón">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" id="mod_desc" class="form-control" rows="3"
                                placeholder="Breve descripción de la marca..."></textarea>
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

<?php include('../../layouts/footer.php'); ?>
<script src="/ProyFinal/public/js/sweetalert2.all.min.js"></script>
<script>
function abrirEditar(id, nombre, pais, desc) {
    document.getElementById('tituloModal').textContent = 'Editar Marca';
    document.getElementById('accionModal').value = 'editar';
    document.getElementById('idModal').value     = id;
    document.getElementById('mod_nombre').value  = nombre;
    document.getElementById('mod_pais').value    = pais;
    document.getElementById('mod_desc').value    = desc;
    new bootstrap.Modal(document.getElementById('modalMarca')).show();
}
document.querySelector('[data-bs-target="#modalMarca"]').addEventListener('click', function() {
    document.getElementById('tituloModal').textContent = 'Nueva Marca';
    document.getElementById('accionModal').value = 'insertar';
    document.getElementById('idModal').value     = '';
    document.getElementById('formMarca').reset();
    document.getElementById('formMarca').classList.remove('was-validated');
});
document.getElementById('formMarca').addEventListener('submit', function(e) {
    if (!this.checkValidity()) { e.preventDefault(); this.classList.add('was-validated'); }
});
function confirmarEliminar(id, nombre) {
    Swal.fire({
        icon: 'warning', title: '¿Eliminar marca?',
        text: `¿Eliminar la marca "${nombre}"? Esta acción no se puede deshacer.`,
        showCancelButton: true, confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar', confirmButtonColor: '#e74c3c'
    }).then(r => { if (r.isConfirmed) window.location='/ProyFinal/app/control/procesar_marca.php?accion=eliminar&id='+id; });
}
</script>
</body>
</html>
