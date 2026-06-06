<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modelos – AutoColombia</title>
    <link rel="stylesheet" href="/ProyFinal/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php
require_once __DIR__ . '/../../../../app/services/session.php';
verificarPerfil('admin');
require_once __DIR__ . '/../../../../app/model/modelo_auto.php';
$obj     = new ModeloAuto();
$modelos = $obj->listar();
$marcas  = $obj->listarMarcas();
$combustibles = ['gasolina','diesel','hibrido','electrico','gas'];
?>
<?php include('../navbar.php'); ?>

<main class="col-md-10 ms-sm-auto px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>🚙 Gestión de Modelos</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalModelo">+ Nuevo Modelo</button>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr><th>#</th><th>Marca</th><th>Modelo</th><th>Precio Base</th><th>Descuento</th><th>Combustible</th><th>Plazas</th><th>Estado</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                    <?php if (empty($modelos)): ?>
                        <tr><td colspan="9" class="text-center py-3 text-muted">No hay modelos registrados</td></tr>
                    <?php else: ?>
                    <?php foreach ($modelos as $m): ?>
                        <tr>
                            <td><?= $m['id_modelo'] ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($m['nombre_marca']) ?></span></td>
                            <td><strong><?= htmlspecialchars($m['nombre']) ?></strong></td>
                            <td>$<?= number_format($m['precio_base'], 0, ',', '.') ?></td>
                            <td><?= $m['descuento'] ?>%</td>
                            <td><span class="badge bg-info text-dark"><?= ucfirst($m['tipo_combustible']) ?></span></td>
                            <td><?= $m['num_plazas'] ?></td>
                            <td><?= $m['activo'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?></td>
                            <td>
                                <button class="btn btn-warning btn-accion" onclick="abrirEditar(
                                    <?= $m['id_modelo'] ?>,'<?= htmlspecialchars($m['nombre'],ENT_QUOTES) ?>',
                                    <?= $m['id_marca'] ?>,<?= $m['precio_base'] ?>,<?= $m['descuento'] ?>,
                                    <?= $m['potencia_fiscal'] ?>,<?= $m['cilindrada'] ?>,
                                    '<?= $m['tipo_combustible'] ?>',<?= $m['num_puertas'] ?>,<?= $m['num_plazas'] ?>,
                                    '<?= htmlspecialchars($m['descripcion'],ENT_QUOTES) ?>',<?= $m['activo'] ?>)">✏️ Editar</button>
                                <button class="btn btn-danger btn-accion"
                                    onclick="confirmarEliminar(<?= $m['id_modelo'] ?>,'<?= htmlspecialchars($m['nombre'],ENT_QUOTES) ?>')">🗑️ Eliminar</button>
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
<div class="modal fade" id="modalModelo" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header card-header-primary">
                <h5 class="modal-title text-white" id="tituloModal">Nuevo Modelo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formModelo" method="POST" action="/ProyFinal/app/control/procesar_modelo_auto.php" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="accionModal" value="insertar">
                    <input type="hidden" name="id_modelo" id="idModal" value="">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Nombre del Modelo *</label>
                            <input type="text" name="nombre" id="mod_nombre" class="form-control" required placeholder="Ej: Corolla">
                            <div class="invalid-feedback">Obligatorio</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Marca *</label>
                            <select name="id_marca" id="mod_marca" class="form-select" required>
                                <option value="">Selecciona marca</option>
                                <?php foreach ($marcas as $ma): ?>
                                <option value="<?= $ma['id_marca'] ?>"><?= htmlspecialchars($ma['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Selecciona una marca</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tipo de Combustible *</label>
                            <select name="tipo_combustible" id="mod_combustible" class="form-select" required>
                                <option value="">Selecciona</option>
                                <?php foreach ($combustibles as $c): ?>
                                <option value="<?= $c ?>"><?= ucfirst($c) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Obligatorio</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Precio Base (COP) *</label>
                            <input type="number" name="precio_base" id="mod_precio" class="form-control" required min="1000000" step="100000" placeholder="62000000">
                            <div class="invalid-feedback">Mínimo $1.000.000</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Descuento (%)</label>
                            <input type="number" name="descuento" id="mod_descuento" class="form-control" min="0" max="50" step="0.1" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Potencia Fiscal (CV)</label>
                            <input type="number" name="potencia_fiscal" id="mod_potencia" class="form-control" min="0" step="0.01" placeholder="76.00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cilindrada (cc)</label>
                            <input type="number" name="cilindrada" id="mod_cilindrada" class="form-control" min="0" placeholder="1200">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Nº Puertas</label>
                            <input type="number" name="num_puertas" id="mod_puertas" class="form-control" min="2" max="6" value="5">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Nº Plazas</label>
                            <input type="number" name="num_plazas" id="mod_plazas" class="form-control" min="1" max="9" value="5">
                        </div>
                        <div class="col-md-3 d-none" id="campoActivo">
                            <label class="form-label">Estado</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="activo" id="mod_activo" value="1" checked>
                                <label class="form-check-label" for="mod_activo">Modelo activo</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" id="mod_desc" class="form-control" rows="2" placeholder="Descripción del modelo..."></textarea>
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
function abrirEditar(id,nombre,id_marca,precio,descuento,potencia,cilindrada,combustible,puertas,plazas,desc,activo){
    document.getElementById('tituloModal').textContent='Editar Modelo';
    document.getElementById('accionModal').value='editar';
    document.getElementById('idModal').value=id;
    document.getElementById('mod_nombre').value=nombre;
    document.getElementById('mod_marca').value=id_marca;
    document.getElementById('mod_precio').value=precio;
    document.getElementById('mod_descuento').value=descuento;
    document.getElementById('mod_potencia').value=potencia;
    document.getElementById('mod_cilindrada').value=cilindrada;
    document.getElementById('mod_combustible').value=combustible;
    document.getElementById('mod_puertas').value=puertas;
    document.getElementById('mod_plazas').value=plazas;
    document.getElementById('mod_desc').value=desc;
    document.getElementById('mod_activo').checked=activo==1;
    document.getElementById('campoActivo').classList.remove('d-none');
    new bootstrap.Modal(document.getElementById('modalModelo')).show();
}
document.querySelector('[data-bs-target="#modalModelo"]').addEventListener('click',function(){
    document.getElementById('tituloModal').textContent='Nuevo Modelo';
    document.getElementById('accionModal').value='insertar';
    document.getElementById('idModal').value='';
    document.getElementById('formModelo').reset();
    document.getElementById('formModelo').classList.remove('was-validated');
    document.getElementById('campoActivo').classList.add('d-none');
});
document.getElementById('formModelo').addEventListener('submit',function(e){
    if(!this.checkValidity()){e.preventDefault();this.classList.add('was-validated');}
});
function confirmarEliminar(id,nombre){
    Swal.fire({icon:'warning',title:'¿Eliminar modelo?',text:`¿Eliminar el modelo "${nombre}"?`,
    showCancelButton:true,confirmButtonText:'Sí, eliminar',cancelButtonText:'Cancelar',confirmButtonColor:'#e74c3c'})
    .then(r=>{if(r.isConfirmed)window.location='/ProyFinal/app/control/procesar_modelo_auto.php?accion=eliminar&id='+id;});
}
</script>
</body>
</html>
