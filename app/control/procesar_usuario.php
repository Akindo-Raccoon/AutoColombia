<?php
// procesar_usuario.php
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
    require_once __DIR__ . '/../../app/control/usuario.php';

    $ctrl = new UsuarioController();
    $accion = $_REQUEST['accion'] ?? '';

    // Llamar al método según la acción recibida
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
        case 'cambiar_password':
            $ctrl->cambiarPassword();
            break;
        default:
            // Acción no reconocida
            echo "<script>
        Swal.fire({ icon:'error', title:'Error', text:'Acción no válida' })
        .then(() => { window.location='/ProyFinal/app/view/admin/usuarios/listar.php'; });
        </script>";
    }
    ?>
</body>

</html>