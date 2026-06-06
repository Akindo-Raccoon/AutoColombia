<?php
session_start();
require_once __DIR__ . '/../../app/services/session.php';
require_once __DIR__ . '/../../app/model/concesionario.php';

verificarPerfil('admin');

$obj    = new Concesionario();
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
        $nif = trim($_POST['nif'] ?? '');
        if ($obj->nifExiste($nif)) {
            alertaYRedir('warning', 'NIF duplicado', "Ya existe un concesionario con el NIF \"$nif\".",
                '/ProyFinal/app/view/admin/concesionarios/listar.php');
        }
        if ($obj->insertar(
            trim($_POST['nombre']    ?? ''),
            trim($_POST['domicilio'] ?? ''),
            $nif,
            trim($_POST['telefono']  ?? ''),
            trim($_POST['email']     ?? '')
        )) {
            alertaYRedir('success', '¡Creado!', 'Concesionario registrado correctamente.',
                '/ProyFinal/app/view/admin/concesionarios/listar.php');
        }
        alertaYRedir('error', 'Error', 'No se pudo crear el concesionario.',
            '/ProyFinal/app/view/admin/concesionarios/listar.php');
        break;

    case 'editar':
        $id  = intval($_POST['id_concesionario'] ?? 0);
        $nif = trim($_POST['nif'] ?? '');
        if ($obj->nifExiste($nif, $id)) {
            alertaYRedir('warning', 'NIF duplicado', "Otro concesionario ya usa el NIF \"$nif\".",
                '/ProyFinal/app/view/admin/concesionarios/listar.php');
        }
        if ($obj->editar($id,
            trim($_POST['nombre']    ?? ''),
            trim($_POST['domicilio'] ?? ''),
            $nif,
            trim($_POST['telefono']  ?? ''),
            trim($_POST['email']     ?? '')
        )) {
            alertaYRedir('success', '¡Actualizado!', 'Concesionario actualizado correctamente.',
                '/ProyFinal/app/view/admin/concesionarios/listar.php');
        }
        alertaYRedir('error', 'Error', 'No se pudo actualizar el concesionario.',
            '/ProyFinal/app/view/admin/concesionarios/listar.php');
        break;

    case 'eliminar':
        $id = intval($_GET['id'] ?? 0);
        if ($obj->eliminar($id)) {
            alertaYRedir('success', 'Eliminado', 'Concesionario eliminado.',
                '/ProyFinal/app/view/admin/concesionarios/listar.php');
        }
        alertaYRedir('error', 'Error',
            'No se pudo eliminar. Verifica que no tenga vendedores o autos asociados.',
            '/ProyFinal/app/view/admin/concesionarios/listar.php');
        break;

    default:
        header('Location: /ProyFinal/app/view/admin/concesionarios/listar.php');
        exit();
}
