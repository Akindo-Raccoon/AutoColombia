<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Formas de Pago – AutoColombia</title>
    <link rel="stylesheet" href="/ProyFinal/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php
require_once __DIR__ . '/../../../../app/services/session.php';
verificarPerfil('admin');
require_once __DIR__ . '/../../../../app/model/forma_pago.php';
$obj   = new FormaPago();
$pagos = $obj->listar();
?>
<?php include('../navbar.php'); ?>

<main class="col-md-10 ms-sm-auto px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>💳 Formas de Pago</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPago">+ Nueva Forma de Pago</button>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr><th>#</th><th>Tipo</th><th>Financiera</th><th>Condiciones</th><th>Tasa Interés</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                    <?php if (empty($pagos)): ?>
                        <tr><td colspan="6" class="text-center py-3 text-muted">No hay formas de pago registradas</td></tr>
                    <?php else: ?>
                    <?php foreach ($pagos as $p): ?>
                        <tr>
                            <td><?= $p['id_forma_pago'] ?></td>
                            <td>
                                <?php if ($p['tipo'] === 'contado'): ?>
                                    <span class="badge bg-success">💵 Contado</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark">🏦 Financiera</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($p['nombre_financiera'] ?? '–') ?></td>
                            <td class="small text-muted"><?= htmlspecialchars($p['condiciones']) ?></td>
                            <td><?= $p['tasa_interes'] ? $p['tasa_interes'] . '% mensual' : '–' ?></td>
                            <td>
                                <button class="btn btn-warning btn-accion"
                                    onclick="abrirEditar(<?= $p['id_forma_pago'] ?>,'<?= $p['tipo'] ?>','<?= htmlspecialchars($p['nombre_financiera']??'',ENT_QUOTES) ?>','<?= htmlspecialchars($p['condiciones'],ENT_QUOTES) ?>','<?= $p['tasa_interes']??'' ?>')">
                                    ✏️ Editar</button>
                                <button class="btn btn-danger btn-accion"
                                    onclick="confirmarEliminar(<?= $p['id_forma_pago'] ?>)">🗑️ Eliminar</button>
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

<!-- MODAL -->
<div class="modal fade" id="modalPago" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header card-header-primary">
                <h5 class="modal-title text-white" id="tituloModal">Nueva Forma de Pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPago" method="POST" action="/ProyFinal/app/control/procesar_forma_pago.php" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="accionModal" value="insertar">
                    <input type="hidden" name="id_forma_pago" id="idModal" value="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipo *</label>
                            <select name="tipo" id="mod_tipo" class="form-select" required onchange="toggleFinanciera(this.value)">
                                <option value="">Selecciona tipo</option>
                                <option value="contado">💵 Contado</option>
                                <option value="financiera">🏦 Financiera</option>
                            </select>
                            <div class="invalid-feedback">Selecciona un tipo</div>
                        </div>
                        <div class="col-md-6" id="campoFinanciera">
                            <label class="form-label">Nombre de la Financiera</label>
                            <input type="text" name="nombre_financiera" id="mod_financiera" class="form-control" placeholder="Ej: Bancolombia">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Condiciones</label>
                            <input type="text" name="condiciones" id="mod_condiciones" class="form-control" placeholder="Ej: Hasta 60 meses, cuota fija">
                        </div>
                        <div class="col-md-4" id="campoTasa">
                            <label class="form-label">Tasa de Interés (% mensual)</label>
                            <input type="number" name="tasa_interes" id="mod_tasa" class="form-control" min="0" max="100" step="0.01" placeholder="1.20">
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
function toggleFinanciera(val){
    const show = val==='financiera';
    document.getElementById('campoFinanciera').style.opacity = show?'1':'0.4';
    document.getElementById('campoTasa').style.opacity       = show?'1':'0.4';
}
function abrirEditar(id,tipo,financiera,condiciones,tasa){
    document.getElementById('tituloModal').textContent='Editar Forma de Pago';
    document.getElementById('accionModal').value='editar';
    document.getElementById('idModal').value=id;
    document.getElementById('mod_tipo').value=tipo;
    document.getElementById('mod_financiera').value=financiera;
    document.getElementById('mod_condiciones').value=condiciones;
    document.getElementById('mod_tasa').value=tasa;
    toggleFinanciera(tipo);
    new bootstrap.Modal(document.getElementById('modalPago')).show();
}
document.querySelector('[data-bs-target="#modalPago"]').addEventListener('click',function(){
    document.getElementById('tituloModal').textContent='Nueva Forma de Pago';
    document.getElementById('accionModal').value='insertar';
    document.getElementById('idModal').value='';
    document.getElementById('formPago').reset();
    document.getElementById('formPago').classList.remove('was-validated');
    toggleFinanciera('');
});
document.getElementById('formPago').addEventListener('submit',function(e){
    if(!this.checkValidity()){e.preventDefault();this.classList.add('was-validated');}
});
function confirmarEliminar(id){
    Swal.fire({icon:'warning',title:'¿Eliminar forma de pago?',
    text:'¿Eliminar este método de pago? No debe estar en uso en ventas.',
    showCancelButton:true,confirmButtonText:'Sí, eliminar',cancelButtonText:'Cancelar',confirmButtonColor:'#e74c3c'})
    .then(r=>{if(r.isConfirmed)window.location='/ProyFinal/app/control/procesar_forma_pago.php?accion=eliminar&id='+id;});
}
</script>
</body>
</html>
