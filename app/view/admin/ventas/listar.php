<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ventas – AutoColombia</title>
    <link rel="stylesheet" href="/ProyFinal/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/ProyFinal/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php
require_once __DIR__ . '/../../../../app/services/session.php';
verificarPerfil('admin');
require_once __DIR__ . '/../../../../app/model/venta.php';
$obj    = new Venta();
$ventas = $obj->listar();

$total_ingresos  = array_sum(array_column($ventas, 'precio_cobrado'));
$total_ventas    = count($ventas);
?>
<?php include('../navbar.php'); ?>

<main class="col-md-10 ms-sm-auto px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>🧾 Gestión de Ventas</h4>
        <div class="d-flex gap-2">
            <span class="badge bg-primary fs-6 px-3 py-2"><?= $total_ventas ?> ventas</span>
            <span class="badge bg-success fs-6 px-3 py-2">$<?= number_format($total_ingresos, 0, ',', '.') ?> total</span>
        </div>
    </div>

    <!-- Buscador rápido -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <input type="text" id="buscador" class="form-control" placeholder="🔍 Buscar por bastidor, marca, modelo, vendedor, usuario...">
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0" id="tablaVentas">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Fecha Venta</th>
                            <th>Vehículo</th>
                            <th>Cliente / Vendedor</th>
                            <th>Forma de Pago</th>
                            <th>Precio Cobrado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTabla">
                    <?php if (empty($ventas)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">No hay ventas registradas</td></tr>
                    <?php else: ?>
                    <?php foreach ($ventas as $v): ?>
                        <tr>
                            <td><strong>#<?= $v['id_venta'] ?></strong></td>
                            <td>
                                <?= date('d/m/Y', strtotime($v['fecha_venta'])) ?>
                                <?php if ($v['fecha_entrega']): ?>
                                <br><small class="text-muted">Entrega: <?= date('d/m/Y', strtotime($v['fecha_entrega'])) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($v['nombre_marca'] . ' ' . $v['nombre_modelo']) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($v['color']) ?> | <code><?= htmlspecialchars($v['num_bastidor']) ?></code></small>
                            </td>
                            <td>
                                <?php if ($v['nombre_usuario']): ?>
                                    <span class="badge bg-success">👤 <?= htmlspecialchars($v['nombre_usuario'] . ' ' . $v['apellidos_usuario']) ?></span>
                                <?php endif; ?>
                                <?php if ($v['nombre_vendedor']): ?>
                                    <br><span class="badge bg-info text-dark">💼 <?= htmlspecialchars($v['nombre_vendedor'] . ' ' . $v['apellidos_vendedor']) ?></span>
                                <?php endif; ?>
                                <?php if (!$v['nombre_usuario'] && !$v['nombre_vendedor']): ?>
                                    <span class="text-muted small">–</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($v['tipo_pago'] === 'contado'): ?>
                                    <span class="badge bg-success">💵 Contado</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark">🏦 <?= htmlspecialchars($v['nombre_financiera']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><strong class="text-success">$<?= number_format($v['precio_cobrado'], 0, ',', '.') ?></strong></td>
                            <td>
                                <a href="/ProyFinal/app/view/usuario/recibo.php?id=<?= $v['id_venta'] ?>" 
                                   class="btn btn-info btn-accion text-white" target="_blank">🧾 Recibo</a>
                                <button class="btn btn-danger btn-accion"
                                    onclick="confirmarEliminar(<?= $v['id_venta'] ?>, '#<?= $v['id_venta'] ?>')">🗑️</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <p class="text-muted small mt-2" id="contador"></p>
</main>
</div>
</div>

<?php include('../../layouts/footer.php'); ?>
<script src="/ProyFinal/public/js/sweetalert2.all.min.js"></script>
<script>
const filas = document.querySelectorAll('#cuerpoTabla tr');
document.getElementById('buscador').addEventListener('input', function(){
    const q = this.value.toLowerCase();
    let v = 0;
    filas.forEach(f => {
        const ok = !q || f.textContent.toLowerCase().includes(q);
        f.style.display = ok ? '' : 'none';
        if (ok) v++;
    });
    document.getElementById('contador').textContent = q ? `${v} de ${filas.length} ventas mostradas` : '';
});

function confirmarEliminar(id, label){
    Swal.fire({
        icon:'warning', title:'¿Anular esta venta?',
        text:`Venta ${label}: El auto volverá a estar disponible. Esta acción no se puede deshacer.`,
        showCancelButton:true, confirmButtonText:'Sí, anular',
        cancelButtonText:'Cancelar', confirmButtonColor:'#e74c3c'
    }).then(r=>{ if(r.isConfirmed) window.location='/ProyFinal/app/control/procesar_venta.php?accion=eliminar&id='+id; });
}
</script>
</body>
</html>
