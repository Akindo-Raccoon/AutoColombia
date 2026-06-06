<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Concesionarios – AutoColombia</title>
    <link rel="stylesheet" href="/ProyFinal/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php
require_once __DIR__ . '/../../../../app/services/session.php';
verificarPerfil('admin');
require_once __DIR__ . '/../../../../app/model/concesionario.php';
$obj            = new Concesionario();
$concesionarios = $obj->listar();
?>
<?php include('../navbar.php'); ?>

<main class="col-md-10 ms-sm-auto px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>🏢 Gestión de Concesionarios</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalConc">+ Nuevo Concesionario</button>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr><th>#</th><th>Nombre</th><th>NIF</th><th>Domicilio</th><th>Teléfono</th><th>Email</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                    <?php if (empty($concesionarios)): ?>
                        <tr><td colspan="7" class="text-center py-3 text-muted">No hay concesionarios</td></tr>
                    <?php else: ?>
                    <?php foreach ($concesionarios as $c): ?>
                        <tr>
                            <td><?= $c['id_concesionario'] ?></td>
                            <td><strong><?= htmlspecialchars($c['nombre']) ?></strong></td>
                            <td><code><?= htmlspecialchars($c['nif']) ?></code></td>
                            <td><?= htmlspecialchars($c['domicilio']) ?></td>
                            <td><?= htmlspecialchars($c['telefono']) ?></td>
                            <td><?= htmlspecialchars($c['email']) ?></td>
                            <td>
                                <button class="btn btn-warning btn-accion"
                                    onclick="abrirEditar(<?= $c['id_concesionario'] ?>,'<?= htmlspecialchars($c['nombre'],ENT_QUOTES) ?>','<?= htmlspecialchars($c['domicilio'],ENT_QUOTES) ?>','<?= htmlspecialchars($c['nif'],ENT_QUOTES) ?>','<?= htmlspecialchars($c['telefono'],ENT_QUOTES) ?>','<?= htmlspecialchars($c['email'],ENT_QUOTES) ?>')">
                                    ✏️ Editar</button>
                                <button class="btn btn-danger btn-accion"
                                    onclick="confirmarEliminar(<?= $c['id_concesionario'] ?>,'<?= htmlspecialchars($c['nombre'],ENT_QUOTES) ?>')">
                                    🗑️ Eliminar</button>
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
<div class="modal fade" id="modalConc" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header card-header-primary">
                <h5 class="modal-title text-white" id="tituloModal">Nuevo Concesionario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formConc" method="POST" action="/ProyFinal/app/control/procesar_concesionario.php" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="accionModal" value="insertar">
                    <input type="hidden" name="id_concesionario" id="idModal" value="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre *</label>
                            <input type="text" name="nombre" id="mod_nombre" class="form-control" required placeholder="AutoColombia Bogotá">
                            <div class="invalid-feedback">Obligatorio</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIF *</label>
                            <input type="text" name="nif" id="mod_nif" class="form-control" required placeholder="900123456-1">
                            <div class="invalid-feedback">Obligatorio</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Domicilio *</label>
                            <input type="text" name="domicilio" id="mod_domicilio" class="form-control" required placeholder="Av. El Dorado 68-50, Bogotá">
                            <div class="invalid-feedback">Obligatorio</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" id="mod_telefono" class="form-control" placeholder="6011234567">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="mod_email" class="form-control" placeholder="info@concesionario.com">
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
function abrirEditar(id,nombre,domicilio,nif,telefono,email){
    document.getElementById('tituloModal').textContent='Editar Concesionario';
    document.getElementById('accionModal').value='editar';
    document.getElementById('idModal').value=id;
    document.getElementById('mod_nombre').value=nombre;
    document.getElementById('mod_nif').value=nif;
    document.getElementById('mod_domicilio').value=domicilio;
    document.getElementById('mod_telefono').value=telefono;
    document.getElementById('mod_email').value=email;
    new bootstrap.Modal(document.getElementById('modalConc')).show();
}
document.querySelector('[data-bs-target="#modalConc"]').addEventListener('click',function(){
    document.getElementById('tituloModal').textContent='Nuevo Concesionario';
    document.getElementById('accionModal').value='insertar';
    document.getElementById('idModal').value='';
    document.getElementById('formConc').reset();
    document.getElementById('formConc').classList.remove('was-validated');
});
document.getElementById('formConc').addEventListener('submit',function(e){
    if(!this.checkValidity()){e.preventDefault();this.classList.add('was-validated');}
});
function confirmarEliminar(id,nombre){
    Swal.fire({icon:'warning',title:'¿Eliminar concesionario?',text:`¿Eliminar "${nombre}"? Verifica que no tenga vendedores o autos.`,
    showCancelButton:true,confirmButtonText:'Sí, eliminar',cancelButtonText:'Cancelar',confirmButtonColor:'#e74c3c'})
    .then(r=>{if(r.isConfirmed)window.location='/ProyFinal/app/control/procesar_concesionario.php?accion=eliminar&id='+id;});
}
</script>
</body>
</html>
