<?php
// procesar_login.php
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
    header("Location: /ProyFinal/index.php?accion=registro");
    exit();
}

$nombre    = trim($_POST['nombre']    ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$email     = trim($_POST['email']     ?? '');
$password  = trim($_POST['password']  ?? '');
$confirmar = trim($_POST['confirmar'] ?? '');
$rol       = trim($_POST['rol']       ?? '');

// Validación backend: todos los campos obligatorios
if (empty($nombre) || empty($apellidos) || empty($email) || empty($password) || empty($rol)) {
    echo "<script>
    Swal.fire({ icon:'error', title:'Datos incompletos',
        text:'Todos los campos son obligatorios.' })
    .then(() => { window.location='/ProyFinal/index.php?accion=registro'; });
    </script>";
    exit();
}

$ctrl = new Auth();
$ctrl->registrar($nombre, $apellidos, $email, $password, $confirmar, $rol);

?>
</body>
</html>