<?php

// Iniciar sesión solo si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tiempo máximo de sesión: 30 minutos 
define('TIEMPO_SESION', 1800);

// ── VERIFICAR si la sesión está activa 
function verificarSesion()
{
    if (!isset($_SESSION['id_usuario']) || $_SESSION['id_usuario'] == '') {
        // No hay sesión: redirigir al login
        redirigirAlLogin("Debes iniciar sesión para continuar");
    }

    // Verificar timeout de sesión
    if (isset($_SESSION['timeout'])) {
        $tiempo_inactivo = time() - $_SESSION['timeout'];
        if ($tiempo_inactivo > TIEMPO_SESION) {
            session_destroy();
            redirigirAlLogin("Tu sesión expiró por inactividad");
        }
    }

    // Actualizar tiempo de actividad
    $_SESSION['timeout'] = time();
}

// $perfil puede ser: 'admin', 'vendedor', 'usuario'
// o un array: ['admin', 'vendedor']
function verificarPerfil($perfiles_permitidos)
{
    verificarSesion();

    if (!is_array($perfiles_permitidos)) {
        $perfiles_permitidos = [$perfiles_permitidos];
    }

    // Verificar si el perfil del usuario está en los permitidos
    if (!in_array($_SESSION['perfil'], $perfiles_permitidos)) {
        echo "<script src='/ProyFinal/public/js/sweetalert2.min.js'></script>
        <script>
        Swal.fire({
            icon: 'error',
            title: 'Acceso denegado',
            text: 'No tienes permisos para acceder a esta sección'
        }).then(() => { history.back(); });
        </script>";
        exit();
    }
}

// REDIRIGIR al login con mensaje de error 
function redirigirAlLogin($mensaje = '')
{
    // Calcular la ruta relativa al index.php desde cualquier subdirectorio
    $niveles = substr_count($_SERVER['PHP_SELF'], '/') - 1;
    $ruta = str_repeat('../', $niveles);

    if ($mensaje) {
        // Guardar mensaje en sesión para mostrarlo en login
        $_SESSION['error_login'] = $mensaje;
    }

    header("Location: /ProyFinal/index.php");
    exit();
}

// OBTENER nombre del usuario logueado
function getNombreUsuario()
{
    return isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
}

// OBTENER perfil del usuario logueado
function getPerfilUsuario()
{
    return isset($_SESSION['perfil']) ? $_SESSION['perfil'] : '';
}