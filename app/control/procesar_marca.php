<?php
session_start();
require_once __DIR__ . '/../../app/services/session.php';
require_once __DIR__ . '/../../app/model/marca.php';
require_once __DIR__ . '/../../app/model/auto.php';

verificarPerfil('admin');

$obj    = new Marca();
$accion = $_REQUEST['accion'] ?? '';

// ── Función helper para alertas ─────────────────────────────────
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
    // ── INSERTAR ────────────────────────────────────────────────
    case 'insertar':
        $nombre      = trim($_POST['nombre'] ?? '');
        $pais_origen = trim($_POST['pais_origen'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if ($obj->nombreExiste($nombre)) {
            alertaYRedir('warning', 'Nombre duplicado',
                "Ya existe una marca con el nombre \"$nombre\".",
                '/ProyFinal/app/view/admin/marcas/listar.php');
        }
        if ($obj->insertar($nombre, $pais_origen, $descripcion)) {
            alertaYRedir('success', '¡Marca creada!',
                "La marca \"$nombre\" fue registrada correctamente.",
                '/ProyFinal/app/view/admin/marcas/listar.php');
        }
        alertaYRedir('error', 'Error', 'No se pudo crear la marca.',
            '/ProyFinal/app/view/admin/marcas/listar.php');
        break;

    // ── EDITAR ──────────────────────────────────────────────────
    case 'editar':
        $id          = intval($_POST['id_marca'] ?? 0);
        $nombre      = trim($_POST['nombre'] ?? '');
        $pais_origen = trim($_POST['pais_origen'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if ($obj->nombreExiste($nombre, $id)) {
            alertaYRedir('warning', 'Nombre duplicado',
                "Ya existe otra marca con el nombre \"$nombre\".",
                '/ProyFinal/app/view/admin/marcas/listar.php');
        }
        if ($obj->editar($id, $nombre, $pais_origen, $descripcion)) {
            alertaYRedir('success', '¡Marca actualizada!',
                "La marca fue actualizada correctamente.",
                '/ProyFinal/app/view/admin/marcas/listar.php');
        }
        alertaYRedir('error', 'Error', 'No se pudo actualizar la marca.',
            '/ProyFinal/app/view/admin/marcas/listar.php');
        break;

    // ── ELIMINAR ────────────────────────────────────────────────
    case 'eliminar':
        $id = intval($_GET['id'] ?? 0);
        if ($obj->eliminar($id)) {
            alertaYRedir('success', 'Eliminada', 'La marca fue eliminada.',
                '/ProyFinal/app/view/admin/marcas/listar.php');
        }
        alertaYRedir('error', 'Error',
            'No se pudo eliminar. Verifica que no tenga modelos asociados.',
            '/ProyFinal/app/view/admin/marcas/listar.php');
        break;

    default:
        header('Location: /ProyFinal/app/view/admin/marcas/listar.php');
        exit();
}
