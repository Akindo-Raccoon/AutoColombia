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
require_once __DIR__ . '/../../../app/services/session.php';
verificarPerfil(['vendedor']);

require_once __DIR__ . '/../../../app/model/auto.php';
$obj          = new Auto();
$automoviles  = $obj->listar();
$modelos      = $obj->listarModelos();
?>

<?php include('navbar.php'); ?>

<main class="col-md-10 ms-sm-auto px-4 py-4">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>🚘 Inventario de Automóviles</h4>
        <span class="badge bg-secondary fs-6">
            <?= count($automoviles) ?> vehículo<?= count($automoviles) !== 1 ? 's' : '' ?>
        </span>
    </div>

    <!-- Filtros -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="row g-2 align-items-end">

                <div class="col-md-4">
                    <label class="form-label small mb-1">Buscar</label>
                    <input type="text" id="filtro_texto" class="form-control form-control-sm"
                           placeholder="Bastidor, marca, modelo, color…">
                </div>

                <div class="col-md-3">
                    <label class="form-label small mb-1">Estado</label>
                    <select id="filtro_estado" class="form-select form-select-sm">
                        <option value="">Todos los estados</option>
                        <option value="disponible">Disponible</option>
                        <option value="reservado">Reservado</option>
                        <option value="vendido">Vendido</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small mb-1">Modelo</label>
                    <select id="filtro_modelo" class="form-select form-select-sm">
                        <option value="">Todos los modelos</option>
                        <?php foreach ($modelos as $m): ?>
                        <option value="<?= htmlspecialchars($m['nombre_completo']) ?>">
                            <?= htmlspecialchars($m['nombre_completo']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-outline-secondary btn-sm w-100"
                            onclick="limpiarFiltros()">
                        Limpiar filtros
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0" id="tablaAutos">
                    <thead>
                        <tr>
                            <th>Bastidor</th>
                            <th>Marca / Modelo</th>
                            <th>Color</th>
                            <th>Año</th>
                            <th>Estado</th>
                            <th>Precio base</th>
                            <th>Concesionario</th>
                            <th>Ubicación</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTabla">
                    <?php if (empty($automoviles)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-3 text-muted">
                                No hay automóviles registrados
                            </td>
                        </tr>
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
                                    'reservado'  => 'badge-reservado',
                                ];
                                $cls = $badges[$a['estado']] ?? 'bg-secondary';
                                ?>
                                <span class="badge <?= $cls ?>">
                                    <?= ucfirst($a['estado']) ?>
                                </span>
                            </td>
                            <td>$<?= number_format($a['precio_base'], 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($a['nombre_concesionario']) ?></td>
                            <td>
                                <?php if ($a['ubicacion'] === 'servicio_oficial'): ?>
                                    <span class="badge bg-info text-dark">Serv. Oficial</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark border">Concesionario</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Contador de resultados filtrados -->
    <p class="text-muted small mt-2 mb-0" id="contadorResultados"></p>

</main>

<?php include("../../view/layouts/footer.php"); ?>

<script>
const filas = document.querySelectorAll('#cuerpoTabla tr');

function aplicarFiltros() {
    const texto  = document.getElementById('filtro_texto').value.toLowerCase().trim();
    const estado = document.getElementById('filtro_estado').value.toLowerCase();
    const modelo = document.getElementById('filtro_modelo').value.toLowerCase();

    let visibles = 0;

    filas.forEach(function(fila) {
        const contenido = fila.textContent.toLowerCase();

        const coincideTexto  = !texto  || contenido.includes(texto);
        const coincideEstado = !estado || contenido.includes(estado);
        const coincideModelo = !modelo || contenido.includes(modelo);

        const mostrar = coincideTexto && coincideEstado && coincideModelo;
        fila.style.display = mostrar ? '' : 'none';
        if (mostrar) visibles++;
    });

    const total = filas.length;
    const contador = document.getElementById('contadorResultados');
    if (texto || estado || modelo) {
        contador.textContent = visibles + ' de ' + total + ' vehículo' + (total !== 1 ? 's' : '') + ' mostrados';
    } else {
        contador.textContent = '';
    }
}

function limpiarFiltros() {
    document.getElementById('filtro_texto').value  = '';
    document.getElementById('filtro_estado').value = '';
    document.getElementById('filtro_modelo').value = '';
    aplicarFiltros();
}

document.getElementById('filtro_texto').addEventListener('input',  aplicarFiltros);
document.getElementById('filtro_estado').addEventListener('change', aplicarFiltros);
document.getElementById('filtro_modelo').addEventListener('change', aplicarFiltros);
</script>

</body>
</html>