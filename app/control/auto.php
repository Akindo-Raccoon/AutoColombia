<?php

session_start();

require_once __DIR__ . '/../../app/model/auto.php';

class AutomovilController
{
    // INSERTAR AUTO
    public function insertar()
    {
        $obj = new Auto();
        $bastidor = strtoupper(trim($_POST['num_bastidor']));
        $id_modelo = $_POST['id_modelo'];
        $color = $_POST['color'];
        $anio = $_POST['anio_fabricacion'];
        $estado = $_POST['estado'];
        $ubicacion = $_POST['ubicacion'];
        $id_concesionario = $_POST['id_concesionario'];
        $id_servicio = isset($_POST['id_servicio']) ? $_POST['id_servicio'] : null;
        $fecha_ingreso = $_POST['fecha_ingreso'];

        // Verificar bastidor único
        if ($obj->bastidorExiste($bastidor)) {
            echo "<script>
            Swal.fire({ icon:'warning', title:'Bastidor ya existe',
                text:'Este número de bastidor ya está registrado' })
            .then(() => { window.location='/ProyFinal/app/view/admin/automoviles/listar.php'; });
            </script>";
            return;
        }

        $res = $obj->insertar($bastidor, $id_modelo, $color, $anio, $estado, $ubicacion, $id_concesionario, $id_servicio, $fecha_ingreso);

        if ($res) {
            echo "<script>
            Swal.fire({ icon:'success', title:'¡Automóvil registrado!',
                text:'El vehículo fue añadido al inventario' })
            .then(() => { window.location='/ProyFinal/app/view/admin/automoviles/listar.php'; });
            </script>";
        } else {
            echo "<script>
            Swal.fire({ icon:'error', title:'Error', text:'No se pudo registrar el automóvil' })
            .then(() => { window.location='/ProyFinal/app/view/admin/automoviles/listar.php'; });
            </script>";
        }
    }

    // EDITAR AUTO 
    public function editar()
    {
        $obj = new Auto();
        $bastidor = strtoupper(trim($_POST['num_bastidor']));
        $id_modelo = $_POST['id_modelo'];
        $color = $_POST['color'];
        $anio = $_POST['anio_fabricacion'];
        $estado = $_POST['estado'];
        $ubicacion = $_POST['ubicacion'];
        $id_concesionario = $_POST['id_concesionario'];
        $id_servicio = isset($_POST['id_servicio']) ? $_POST['id_servicio'] : null;
        $fecha_ingreso = $_POST['fecha_ingreso'];

        $res = $obj->editar($bastidor, $id_modelo, $color, $anio, $estado, $ubicacion, $id_concesionario, $id_servicio, $fecha_ingreso);

        if ($res) {
            echo "<script>
            Swal.fire({ icon:'success', title:'¡Automóvil actualizado!',
                text:'Los datos del vehículo fueron actualizados' })
            .then(() => { window.location='/ProyFinal/app/view/admin/automoviles/listar.php'; });
            </script>";
        } else {
            echo "<script>
            Swal.fire({ icon:'error', title:'Error', text:'No se pudo actualizar el automóvil' })
            .then(() => { window.location='/ProyFinal/app/view/admin/automoviles/listar.php'; });
            </script>";
        }
    }

    // ELIMINAR AUTO
    public function eliminar()
    {
        $obj = new Auto();
        $bastidor = $_GET['bastidor'];
        $res = $obj->eliminar($bastidor);

        if ($res) {
            echo "<script>
            Swal.fire({ icon:'success', title:'Automóvil eliminado',
                text:'El vehículo fue eliminado del inventario' })
            .then(() => { window.location='/ProyFinal/app/view/admin/automoviles/listar.php'; });
            </script>";
        } else {
            echo "<script>
            Swal.fire({ icon:'error', title:'No se puede eliminar',
                text:'El vehículo tiene ventas asociadas' })
            .then(() => { window.location='/ProyFinal/app/view/admin/automoviles/listar.php'; });
            </script>";
        }
    }
}