<?php
session_start();
require_once __DIR__ . '/../../app/services/session.php';
require_once __DIR__ . '/../../app/model/forma_pago.php';

verificarPerfil('admin');

$obj    = new FormaPago();
$accion = $_REQUEST['accion'] ?? '';

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
    case 'insertar':
        $tipo             = trim($_POST['tipo']              ?? '');
        $nombre_financiera = trim($_POST['nombre_financiera'] ?? '');
        $condiciones      = trim($_POST['condiciones']       ?? '');
        $tasa_interes     = $_POST['tasa_interes'] !== '' ? $_POST['tasa_interes'] : null;

        if ($obj->insertar($tipo, $nombre_financiera ?: null, $condiciones, $tasa_interes)) {
            alertaYRedir('success', '¡Forma de pago creada!', 'Registrada correctamente.',
                '/ProyFinal/app/view/admin/formas_pago/listar.php');
        }
        alertaYRedir('error', 'Error', 'No se pudo crear la forma de pago.',
            '/ProyFinal/app/view/admin/formas_pago/listar.php');
        break;

    case 'editar':
        $id               = intval($_POST['id_forma_pago']   ?? 0);
        $tipo             = trim($_POST['tipo']              ?? '');
        $nombre_financiera = trim($_POST['nombre_financiera'] ?? '');
        $condiciones      = trim($_POST['condiciones']       ?? '');
        $tasa_interes     = $_POST['tasa_interes'] !== '' ? $_POST['tasa_interes'] : null;

        if ($obj->editar($id, $tipo, $nombre_financiera ?: null, $condiciones, $tasa_interes)) {
            alertaYRedir('success', '¡Actualizada!', 'Forma de pago actualizada correctamente.',
                '/ProyFinal/app/view/admin/formas_pago/listar.php');
        }
        alertaYRedir('error', 'Error', 'No se pudo actualizar la forma de pago.',
            '/ProyFinal/app/view/admin/formas_pago/listar.php');
        break;

    case 'eliminar':
        $id = intval($_GET['id'] ?? 0);
        if ($obj->eliminar($id)) {
            alertaYRedir('success', 'Eliminada', 'Forma de pago eliminada.',
                '/ProyFinal/app/view/admin/formas_pago/listar.php');
        }
        alertaYRedir('error', 'Error',
            'No se pudo eliminar. Puede estar en uso en ventas registradas.',
            '/ProyFinal/app/view/admin/formas_pago/listar.php');
        break;

    default:
        header('Location: /ProyFinal/app/view/admin/formas_pago/listar.php');
        exit();
}
