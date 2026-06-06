<?php

session_start();
require_once __DIR__ . '/../../app/services/session.php';
verificarPerfil('admin');
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/ProyFinal/public/css/sweetalert2.min.css">
</head>

<body>
    <script src="/ProyFinal/public/js/sweetalert2.all.min.js"></script>
    <?php
    require_once __DIR__ . '/../../app/control/auto.php';

    $ctrl = new AutomovilController();
    $accion = $_REQUEST['accion'] ?? '';

    switch ($accion) {
        case 'insertar':
            $ctrl->insertar();
            break;
        case 'editar':
            $ctrl->editar();
            break;
        case 'eliminar':
            $ctrl->eliminar();
            break;
        default:
            echo "<script>
        Swal.fire({ icon:'error', title:'Error', text:'Acción no válida' })
        .then(() => { window.location='/ProyFinal/app/view/admin/automoviles/listar.php'; });
        </script>";
    }
    ?>
</body>

</html>