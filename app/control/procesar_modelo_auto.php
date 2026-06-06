<?php
session_start();
require_once __DIR__ . '/../../app/services/session.php';
require_once __DIR__ . '/../../app/model/modelo_auto.php';

verificarPerfil('admin');

$obj    = new ModeloAuto();
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
        $res = $obj->insertar(
            trim($_POST['nombre']           ?? ''),
            intval($_POST['id_marca']       ?? 0),
            floatval($_POST['precio_base']  ?? 0),
            floatval($_POST['descuento']    ?? 0),
            floatval($_POST['potencia_fiscal'] ?? 0),
            intval($_POST['cilindrada']     ?? 0),
            trim($_POST['tipo_combustible'] ?? ''),
            intval($_POST['num_puertas']    ?? 0),
            intval($_POST['num_plazas']     ?? 0),
            trim($_POST['descripcion']      ?? '')
        );
        if ($res) {
            alertaYRedir('success', '¡Modelo creado!', 'El modelo fue registrado correctamente.',
                '/ProyFinal/app/view/admin/modelos/listar.php');
        }
        alertaYRedir('error', 'Error', 'No se pudo crear el modelo.',
            '/ProyFinal/app/view/admin/modelos/listar.php');
        break;

    case 'editar':
        $res = $obj->editar(
            intval($_POST['id_modelo']      ?? 0),
            trim($_POST['nombre']           ?? ''),
            intval($_POST['id_marca']       ?? 0),
            floatval($_POST['precio_base']  ?? 0),
            floatval($_POST['descuento']    ?? 0),
            floatval($_POST['potencia_fiscal'] ?? 0),
            intval($_POST['cilindrada']     ?? 0),
            trim($_POST['tipo_combustible'] ?? ''),
            intval($_POST['num_puertas']    ?? 0),
            intval($_POST['num_plazas']     ?? 0),
            trim($_POST['descripcion']      ?? ''),
            intval($_POST['activo']         ?? 1)
        );
        if ($res) {
            alertaYRedir('success', '¡Modelo actualizado!', 'El modelo fue actualizado correctamente.',
                '/ProyFinal/app/view/admin/modelos/listar.php');
        }
        alertaYRedir('error', 'Error', 'No se pudo actualizar el modelo.',
            '/ProyFinal/app/view/admin/modelos/listar.php');
        break;

    case 'eliminar':
        $id = intval($_GET['id'] ?? 0);
        if ($obj->eliminar($id)) {
            alertaYRedir('success', 'Eliminado', 'El modelo fue eliminado.',
                '/ProyFinal/app/view/admin/modelos/listar.php');
        }
        alertaYRedir('error', 'Error',
            'No se pudo eliminar. Verifica que no tenga automóviles asociados.',
            '/ProyFinal/app/view/admin/modelos/listar.php');
        break;

    default:
        header('Location: /ProyFinal/app/view/admin/modelos/listar.php');
        exit();
}
