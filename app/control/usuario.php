<?php

session_start();

require_once __DIR__ . '/../../app/model/usuario.php';
require_once __DIR__ . '/../../app/model/vendedor.php';

class UsuarioController
{

    // INSERTAR USUARIO 
    public function insertar()
    {
        $obj = new Usuario();
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $perfil = $_POST['perfil'];
        $id_vendedor = isset($_POST['id_vendedor']) ? $_POST['id_vendedor'] : null;

        // Verificar email único
        if ($obj->emailExiste($email)) {
            echo "<script>
            Swal.fire({ icon:'warning', title:'Email ya existe',
                text:'Este email ya está registrado en el sistema' })
            .then(() => { window.location='/ProyFinal/app/view/admin/usuarios/listar.php'; });
            </script>";
            return;
        }

        $res = $obj->insertar($nombre, $apellidos, $email, $password, $perfil, $id_vendedor);

        if ($res) {
            echo "<script>
            Swal.fire({ icon:'success', title:'¡Usuario creado!',
                text:'El usuario fue registrado correctamente' })
            .then(() => { window.location='/ProyFinal/app/view/admin/usuarios/listar.php'; });
            </script>";
        } else {
            echo "<script>
            Swal.fire({ icon:'error', title:'Error', text:'No se pudo crear el usuario' })
            .then(() => { window.location='/ProyFinal/app/view/admin/usuarios/listar.php'; });
            </script>";
        }
    }

    // EDITAR USUARIO
    public function editar()
    {
        $obj = new Usuario();
        $id = $_POST['id_usuario'];
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $email = $_POST['email'];
        $perfil = $_POST['perfil'];
        $activo = isset($_POST['activo']) ? 1 : 0;
        $id_vendedor = isset($_POST['id_vendedor']) ? $_POST['id_vendedor'] : null;

        // Verificar email único (excluyendo el usuario actual)
        if ($obj->emailExiste($email, $id)) {
            echo "<script>
            Swal.fire({ icon:'warning', title:'Email ya existe',
                text:'Ese email ya lo usa otro usuario' })
            .then(() => { window.location='/ProyFinal/app/view/admin/usuarios/listar.php'; });
            </script>";
            return;
        }

        $res = $obj->editar($id, $nombre, $apellidos, $email, $perfil, $id_vendedor, $activo);

        if ($res) {
            echo "<script>
            Swal.fire({ icon:'success', title:'¡Usuario actualizado!',
                text:'Los datos fueron guardados correctamente' })
            .then(() => { window.location='/ProyFinal/app/view/admin/usuarios/listar.php'; });
            </script>";
        } else {
            echo "<script>
            Swal.fire({ icon:'error', title:'Error', text:'No se pudo actualizar el usuario' })
            .then(() => { window.location='/ProyFinal/app/view/admin/usuarios/listar.php'; });
            </script>";
        }
    }

    // ELIMINAR USUARIO
    public function eliminar()
    {
        $obj = new Usuario();
        $id = $_GET['id'];
        $res = $obj->eliminar($id);

        if ($res) {
            echo "<script>
            Swal.fire({ icon:'success', title:'Usuario eliminado',
                text:'El usuario fue eliminado del sistema' })
            .then(() => { window.location='/ProyFinal/app/view/admin/usuarios/listar.php'; });
            </script>";
        } else {
            echo "<script>
            Swal.fire({ icon:'error', title:'No se puede eliminar',
                text:'El usuario tiene registros relacionados' })
            .then(() => { window.location='/ProyFinal/app/view/admin/usuarios/listar.php'; });
            </script>";
        }
    }

    // CAMBIAR PASSWORD
    public function cambiarPassword()
    {
        $obj = new Usuario();
        $id = $_POST['id_usuario'];
        $nueva = $_POST['nueva_password'];
        $res = $obj->cambiarPassword($id, $nueva);

        if ($res) {
            echo "<script>
            Swal.fire({ icon:'success', title:'Contraseña actualizada',
                text:'La nueva contraseña fue guardada correctamente' })
            .then(() => { window.location='/ProyFinal/app/view/admin/usuarios/listar.php'; });
            </script>";
        } else {
            echo "<script>
            Swal.fire({ icon:'error', title:'Error', text:'No se pudo cambiar la contraseña' })
            .then(() => { window.location='/ProyFinal/app/view/admin/usuarios/listar.php'; });
            </script>";
        }
    }
}