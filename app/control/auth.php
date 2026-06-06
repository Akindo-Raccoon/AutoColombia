<?php
// auth.php
session_start();

require_once __DIR__ . '/../../app/model/usuario.php';
require_once __DIR__ . '/../../app/model/vendedor.php';

class Auth
{
    // ══════════════════════════════════════════════════════════
    // LOGIN
    // ══════════════════════════════════════════════════════════
    public function login($email, $password, $rol)
    {
        $obj  = new Usuario();
        $user = $obj->buscarPorEmail($email);

        if (!$user || $user['password'] !== md5($password)) {
            $this->alertaYRedirigir(
                'error',
                'Credenciales incorrectas',
                'El email o la contraseña son incorrectos.',
                '/ProyFinal/index.php'
            );
            return;
        }

        if ($user['perfil'] !== $rol) {
            $this->alertaYRedirigir(
                'warning',
                'Perfil no coincide',
                'Tu cuenta no corresponde al perfil seleccionado. Verifica e intenta de nuevo.',
                '/ProyFinal/index.php'
            );
            return;
        }

        if (!$user['activo']) {
            $this->alertaYRedirigir(
                'warning',
                'Cuenta inactiva',
                'Tu cuenta está desactivada. Contacta al administrador.',
                '/ProyFinal/index.php'
            );
            return;
        }

        $_SESSION['id_usuario'] = $user['id_usuario'];
        $_SESSION['nombre']     = $user['nombre'];
        $_SESSION['email']      = $user['email'];
        $_SESSION['perfil']     = $user['perfil'];
        $_SESSION['timeout']    = time();

        $obj->actualizarAcceso($user['id_usuario']);
        $this->redirigirSegunPerfil($user['perfil']);
    }

    // ══════════════════════════════════════════════════════════
    // REGISTRO
    //
    // usuario  → se inserta directamente en tabla usuario.
    // vendedor → si ya existe en tabla vendedor con ese email,
    //            se vincula. Si no existe, se crea el registro
    //            en vendedor automáticamente y luego se vincula.
    // ══════════════════════════════════════════════════════════
    public function registrar($nombre, $apellidos, $email, $password, $confirmar, $rol)
    {
        $rolesPermitidos = ['usuario', 'vendedor'];
        if (!in_array($rol, $rolesPermitidos, true)) {
            $this->alertaYRedirigir(
                'error',
                'Perfil no permitido',
                'No es posible registrar ese tipo de perfil desde aquí.',
                '/ProyFinal/index.php?accion=registro'
            );
            return;
        }

        if ($password !== $confirmar) {
            $this->alertaYRedirigir(
                'error',
                'Contraseñas no coinciden',
                'Por favor verifica que ambas contraseñas sean iguales.',
                '/ProyFinal/index.php?accion=registro'
            );
            return;
        }

        $objUsuario = new Usuario();

        if ($objUsuario->emailExiste($email)) {
            $this->alertaYRedirigir(
                'warning',
                'Email ya registrado',
                'Este email ya tiene una cuenta. ¿Olvidaste tu contraseña?',
                '/ProyFinal/index.php?accion=registro'
            );
            return;
        }

        // ── Lógica específica por rol ──────────────────────────
        $id_vendedor = null;

        if ($rol === 'vendedor') {
            $objVendedor   = new Vendedor();
            $vendedorExist = $objVendedor->buscarPorEmail($email);

            if ($vendedorExist) {
                // Ya existe en tabla vendedor → solo vincular
                $id_vendedor = $vendedorExist['id_vendedor'];
            } else {
                // No existe → crear el registro en tabla vendedor
                // con los datos del formulario.
                // NIF y concesionario son obligatorios en la tabla;
                // se usan valores provisionales que el admin puede
                // completar después desde el panel de vendedores.
                $nif_provisional = 'PEND-' . strtoupper(substr(md5($email), 0, 8));
                $res_vendedor = $objVendedor->insertar(
                    $nombre,
                    $apellidos,
                    $nif_provisional, // NIF provisional hasta que admin lo complete
                    '',               // domicilio vacío
                    '',               // teléfono vacío
                    $email,
                    date('Y-m-d'),    // fecha contratación = hoy
                    1                 // id_concesionario por defecto (el primero)
                );

                if (!$res_vendedor) {
                    $this->alertaYRedirigir(
                        'error',
                        'Error al registrar',
                        'No se pudo crear el perfil de vendedor. Intenta de nuevo.',
                        '/ProyFinal/index.php?accion=registro'
                    );
                    return;
                }

                // Obtener el id recién creado para la FK
                $vendedorNuevo = $objVendedor->buscarPorEmail($email);
                $id_vendedor   = $vendedorNuevo['id_vendedor'];
            }
        }

        // Insertar en tabla usuario con perfil y vínculo correctos
        $res = $objUsuario->insertar($nombre, $apellidos, $email, $password, $rol, $id_vendedor);

        if ($res) {
            $this->alertaYRedirigir(
                'success',
                '¡Registro exitoso!',
                'Tu cuenta fue creada. Ya puedes iniciar sesión.',
                '/ProyFinal/index.php'
            );
        } else {
            $this->alertaYRedirigir(
                'error',
                'Error al registrar',
                'Ocurrió un problema al crear la cuenta. Intenta de nuevo.',
                '/ProyFinal/index.php?accion=registro'
            );
        }
    }

    // ══════════════════════════════════════════════════════════
    // LOGOUT
    // ══════════════════════════════════════════════════════════
    public function logout()
    {
        session_unset();
        session_destroy();
        header("Location: /ProyFinal/index.php");
        exit();
    }

    // ══════════════════════════════════════════════════════════
    // PRIVADOS
    // ══════════════════════════════════════════════════════════
    private function redirigirSegunPerfil($perfil)
    {
        $rutas = [
            'admin'    => "/ProyFinal/app/view/admin/dashboard.php",
            'vendedor' => "/ProyFinal/app/view/vendedor/dashboard.php",
            'usuario'  => "/ProyFinal/app/view/usuario/dashboard.php",
        ];
        $destino = $rutas[$perfil] ?? "/ProyFinal/index.php";

        echo "<script>
        Swal.fire({
            icon:  'success',
            title: '¡Bienvenido!',
            text:  'Sesión iniciada correctamente',
            timer: 1500,
            showConfirmButton: false
        }).then(() => { window.location='{$destino}'; });
        </script>";
    }

    private function alertaYRedirigir($icono, $titulo, $texto, $url)
    {
        $titulo = addslashes($titulo);
        $texto  = addslashes($texto);
        echo "<script>
        Swal.fire({
            icon:  '{$icono}',
            title: '{$titulo}',
            text:  '{$texto}'
        }).then(() => { window.location='{$url}'; });
        </script>";
    }
}