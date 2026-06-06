<?php

require_once __DIR__ . '/../../app/control/auth.php';

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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /ProyFinal/index.php");
    exit();
}

$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');
$rol      = trim($_POST['rol']      ?? '');

// Validación backend: todos los campos son obligatorios
if (empty($email) || empty($password) || empty($rol)) {
    echo "<script>
    Swal.fire({ icon:'error', title:'Datos incompletos',
        text:'Email, contraseña y perfil son obligatorios.' })
    .then(() => { window.location='/ProyFinal/index.php'; });
    </script>";
    exit();
}

// Validar que el rol sea uno de los permitidos
$rolesValidos = ['usuario', 'vendedor', 'admin'];
if (!in_array($rol, $rolesValidos, true)) {
    echo "<script>
    Swal.fire({ icon:'error', title:'Perfil inválido',
        text:'El perfil seleccionado no es válido.' })
    .then(() => { window.location='/ProyFinal/index.php'; });
    </script>";
    exit();
}

$ctrl = new Auth();
$ctrl->login($email, $password, $rol);

?>
</body>
</html>