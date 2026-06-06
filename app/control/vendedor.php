<?php

session_start();

require_once __DIR__ . '/../../app/model/vendedor.php';

class VendedorController
{

    // INSERTAR VENDEDOR
    public function insertar()
    {
        $obj = new Vendedor();
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $nif = $_POST['nif'];
        $domicilio = $_POST['domicilio'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];
        $fecha_contratacion = $_POST['fecha_contratacion'];
        $id_concesionario = $_POST['id_concesionario'];

        // Verificar NIF único
        if ($obj->nifExiste($nif)) {
            echo "<script>
            Swal.fire({ icon:'warning', title:'NIF ya existe',
                text:'Este documento de identidad ya está registrado' })
            .then(() => { window.location='/ProyFinal/app/view/admin/vendedores/listar.php'; });
            </script>";
            return;
        }

        $res = $obj->insertar($nombre, $apellidos, $nif, $domicilio, $telefono, $email, $fecha_contratacion, $id_concesionario);

        if ($res) {
            echo "<script>
            Swal.fire({ icon:'success', title:'¡Vendedor registrado!',
                text:'El vendedor fue añadido correctamente' })
            .then(() => { window.location='/ProyFinal/app/view/admin/vendedores/listar.php'; });
            </script>";
        } else {
            echo "<script>
            Swal.fire({ icon:'error', title:'Error', text:'No se pudo registrar el vendedor' })
            .then(() => { window.location='/ProyFinal/app/view/admin/vendedores/listar.php'; });
            </script>";
        }
    }

    // EDITAR VENDEDOR
    public function editar()
    {
        $obj = new Vendedor();
        $id = $_POST['id_vendedor'];
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $nif = $_POST['nif'];
        $domicilio = $_POST['domicilio'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];
        $fecha_contratacion = $_POST['fecha_contratacion'];
        $id_concesionario = $_POST['id_concesionario'];
        $activo = isset($_POST['activo']) ? 1 : 0;

        if ($obj->nifExiste($nif, $id)) {
            echo "<script>
            Swal.fire({ icon:'warning', title:'NIF ya existe',
                text:'Ese documento ya lo usa otro vendedor' })
            .then(() => { window.location='/ProyFinal/app/view/admin/vendedores/listar.php'; });
            </script>";
            return;
        }

        $res = $obj->editar($id, $nombre, $apellidos, $nif, $domicilio, $telefono, $email, $fecha_contratacion, $id_concesionario, $activo);

        if ($res) {
            echo "<script>
            Swal.fire({ icon:'success', title:'¡Vendedor actualizado!',
                text:'Los datos fueron guardados correctamente' })
            .then(() => { window.location='/ProyFinal/app/view/admin/vendedores/listar.php'; });
            </script>";
        } else {
            echo "<script>
            Swal.fire({ icon:'error', title:'Error', text:'No se pudo actualizar el vendedor' })
            .then(() => { window.location='/ProyFinal/app/view/admin/vendedores/listar.php'; });
            </script>";
        }
    }

    // ELIMINAR VENDEDOR
    public function eliminar()
    {
        $obj = new Vendedor();
        $id = $_GET['id'];
        $res = $obj->eliminar($id);

        if ($res) {
            echo "<script>
            Swal.fire({ icon:'success', title:'Vendedor eliminado',
                text:'El vendedor fue eliminado del sistema' })
            .then(() => { window.location='/ProyFinal/app/view/admin/vendedores/listar.php'; });
            </script>";
        } else {
            echo "<script>
            Swal.fire({ icon:'error', title:'No se puede eliminar',
                text:'El vendedor tiene ventas u usuarios asociados' })
            .then(() => { window.location='/ProyFinal/app/view/admin/vendedores/listar.php'; });
            </script>";
        }
    }
}