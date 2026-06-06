<?php
session_start();
require_once __DIR__ . '/../../app/services/session.php';
require_once __DIR__ . '/../../app/model/venta.php';
require_once __DIR__ . '/../../app/model/auto.php';
require_once __DIR__ . '/../../app/model/modelo_auto.php';
require_once __DIR__ . '/../../app/model/forma_pago.php';

verificarPerfil(['admin', 'usuario']);

$accion = $_REQUEST['accion'] ?? '';
$objV   = new Venta();
$objA   = new Auto();

function alertaYRedir($icon, $title, $text, $url)
{
    $title = addslashes($title);
    $text  = addslashes($text);
    echo "<!DOCTYPE html><html><head>
    <script src='/ProyFinal/public/js/sweetalert2.all.min.js'></script>
    </head><body><script>
    Swal.fire({icon:'$icon',title:'$title',text:'$text'})
        .then(()=>{ window.location='$url'; });
    </script></body></html>";
    exit();
}

switch ($accion) {
    // ── COMPRA POR USUARIO ──────────────────────────────────────
    case 'comprar':
        $num_bastidor  = trim($_POST['num_bastidor']  ?? '');
        $id_forma_pago = intval($_POST['id_forma_pago'] ?? 0);
        $extras        = $_POST['extras'] ?? [];          // array de id_extra
        $id_usuario    = $_SESSION['id_usuario'];
        $fecha_venta   = date('Y-m-d');
        $fecha_entrega = date('Y-m-d', strtotime('+7 days'));

        // Verificar que el auto esté disponible
        $auto = $objA->buscarPorBastidor($num_bastidor);
        if (!$auto || $auto['estado'] !== 'disponible') {
            alertaYRedir('error', 'Auto no disponible',
                'Este vehículo ya no está disponible para compra.',
                '/ProyFinal/app/view/usuario/catalogo.php');
        }

        // Calcular precio total con extras
        $objM   = new ModeloAuto();
        $objFP  = new FormaPago();
        $modelo_info  = $objM->buscarPorId($auto['id_modelo']);
        $precio_total = floatval($modelo_info['precio_base']);

        // Extras seleccionados
        $extras_detalle = [];
        if (!empty($extras)) {
            $extrasModelo = $objM->listarExtrasModelo($auto['id_modelo']);
            $extras_map   = [];
            foreach ($extrasModelo as $e) {
                $extras_map[$e['id_extra']] = $e;
            }
            foreach ($extras as $id_extra) {
                $id_extra = intval($id_extra);
                if (isset($extras_map[$id_extra])) {
                    $precio_total += floatval($extras_map[$id_extra]['precio_extra']);
                    $extras_detalle[] = [
                        'id_extra'    => $id_extra,
                        'precio_extra' => floatval($extras_map[$id_extra]['precio_extra'])
                    ];
                }
            }
        }

        // Insertar venta
        $id_venta = $objV->insertar(
            $num_bastidor,
            $fecha_venta,
            $fecha_entrega,
            null,           // matricula: se asigna después
            $precio_total,
            $id_forma_pago,
            'vendedor',     // tipo_vendedor por defecto
            null,           // id_vendedor: null (venta directa usuario)
            null,           // id_servicio
            $id_usuario,
            'Compra directa por usuario'
        );

        if (!$id_venta) {
            alertaYRedir('error', 'Error en la compra',
                'No se pudo procesar la compra. Intenta de nuevo.',
                '/ProyFinal/app/view/usuario/catalogo.php');
        }

        // Insertar extras de la venta
        foreach ($extras_detalle as $ex) {
            $objV->insertarExtra($id_venta, $ex['id_extra'], $ex['precio_extra']);
        }

        // Marcar el auto como vendido
        $objV->marcarVendido($num_bastidor);

        // Redirigir al recibo
        header("Location: /ProyFinal/app/view/usuario/recibo.php?id=$id_venta");
        exit();

    // ── ELIMINAR VENTA (solo admin) ─────────────────────────────
    case 'eliminar':
        verificarPerfil('admin');
        $id = intval($_GET['id'] ?? 0);
        // Obtener bastidor para reactivar el auto
        $venta = $objV->buscarPorId($id);
        if ($venta) {
            $link = Conectar::conec();
            $b    = mysqli_real_escape_string($link, $venta['num_bastidor']);
            mysqli_query($link, "UPDATE automovil SET estado='disponible' WHERE num_bastidor='$b'");
        }
        if ($objV->eliminar($id)) {
            alertaYRedir('success', 'Eliminada', 'La venta fue eliminada y el auto vuelve a estar disponible.',
                '/ProyFinal/app/view/admin/ventas/listar.php');
        }
        alertaYRedir('error', 'Error', 'No se pudo eliminar la venta.',
            '/ProyFinal/app/view/admin/ventas/listar.php');
        break;

    default:
        header('Location: /ProyFinal/app/view/usuario/catalogo.php');
        exit();
}
