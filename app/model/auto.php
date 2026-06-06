<?php

require_once __DIR__ . '/../../config/conexionBD.php';

class Auto
{

    // LISTAR todos los automóviles con joins a modelo, marca, concesionario
    public function listar()
    {
        $sql = "SELECT a.*,
                       mo.nombre    AS nombre_modelo,
                       ma.nombre    AS nombre_marca,
                       mo.precio_base,
                       c.nombre     AS nombre_concesionario
                FROM automovil a
                INNER JOIN modelo mo        ON a.id_modelo        = mo.id_modelo
                INNER JOIN marca  ma        ON mo.id_marca         = ma.id_marca
                INNER JOIN concesionario c  ON a.id_concesionario  = c.id_concesionario
                ORDER BY a.fecha_ingreso DESC";
        $res = mysqli_query(Conectar::conec(), $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        return $data;
    }

    // BUSCAR automóvil por bastidor 
    public function buscarPorBastidor($bastidor)
    {
        $link = Conectar::conec();
        $bastidor = mysqli_real_escape_string($link, $bastidor);
        $sql = "SELECT * FROM automovil WHERE num_bastidor = '$bastidor'";
        $res = mysqli_query($link, $sql);
        return mysqli_fetch_assoc($res);
    }

    // INSERTAR automóvil 
    public function insertar($bastidor, $id_modelo, $color, $anio, $estado, $ubicacion, $id_concesionario, $id_servicio, $fecha_ingreso)
    {
        $link = Conectar::conec();
        $bastidor = mysqli_real_escape_string($link, $bastidor);
        $id_modelo = intval($id_modelo);
        $color = mysqli_real_escape_string($link, $color);
        $anio = intval($anio);
        $estado = mysqli_real_escape_string($link, $estado);
        $ubicacion = mysqli_real_escape_string($link, $ubicacion);
        $id_concesionario = intval($id_concesionario);
        $id_s = ($id_servicio != '' && $id_servicio != null) ? intval($id_servicio) : 'NULL';
        $fecha_ingreso = mysqli_real_escape_string($link, $fecha_ingreso);

        $sql = "INSERT INTO automovil
                    (num_bastidor, id_modelo, color, anio_fabricacion, estado, ubicacion, id_concesionario, id_servicio, fecha_ingreso)
                VALUES
                    ('$bastidor', $id_modelo, '$color', $anio, '$estado', '$ubicacion', $id_concesionario, $id_s, '$fecha_ingreso')";
        return mysqli_query($link, $sql);
    }

    // EDITAR automóvil 
    public function editar($bastidor, $id_modelo, $color, $anio, $estado, $ubicacion, $id_concesionario, $id_servicio, $fecha_ingreso)
    {
        $link = Conectar::conec();
        $bastidor = mysqli_real_escape_string($link, $bastidor);
        $id_modelo = intval($id_modelo);
        $color = mysqli_real_escape_string($link, $color);
        $anio = intval($anio);
        $estado = mysqli_real_escape_string($link, $estado);
        $ubicacion = mysqli_real_escape_string($link, $ubicacion);
        $id_concesionario = intval($id_concesionario);
        $id_s = ($id_servicio != '' && $id_servicio != null) ? intval($id_servicio) : 'NULL';
        $fecha_ingreso = mysqli_real_escape_string($link, $fecha_ingreso);

        $sql = "UPDATE automovil
                SET id_modelo=$id_modelo, color='$color', anio_fabricacion=$anio,
                    estado='$estado', ubicacion='$ubicacion',
                    id_concesionario=$id_concesionario, id_servicio=$id_s,
                    fecha_ingreso='$fecha_ingreso'
                WHERE num_bastidor = '$bastidor'";
        return mysqli_query($link, $sql);
    }

    // ELIMINAR automóvil 
    public function eliminar($bastidor)
    {
        $link = Conectar::conec();
        $bastidor = mysqli_real_escape_string($link, $bastidor);
        $sql = "DELETE FROM automovil WHERE num_bastidor = '$bastidor'";
        return mysqli_query($link, $sql);
    }

    // LISTAR modelos (para select del formulario) 
    public function listarModelos()
    {
        $sql = "SELECT mo.id_modelo,
                        CONCAT(ma.nombre,' ',mo.nombre) AS nombre_completo,
                        mo.precio_base
                 FROM modelo mo
                 INNER JOIN marca ma ON mo.id_marca = ma.id_marca
                 WHERE mo.activo = 1
                 ORDER BY ma.nombre, mo.nombre";
        $res = mysqli_query(Conectar::conec(), $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        return $data;
    }

    // LISTAR concesionarios
    public function listarConcesionarios()
    {
        $sql = "SELECT * FROM concesionario ORDER BY nombre";
        $res = mysqli_query(Conectar::conec(), $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        return $data;
    }

    // LISTAR servicios oficiales
    public function listarServicios()
    {
        $sql = "SELECT * FROM servicio_oficial ORDER BY nombre";
        $res = mysqli_query(Conectar::conec(), $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        return $data;
    }

    // VERIFICAR si el bastidor ya existe
    public function bastidorExiste($bastidor, $excluir = '')
    {
        $link = Conectar::conec();
        $bastidor = mysqli_real_escape_string($link, $bastidor);
        $excluir = mysqli_real_escape_string($link, $excluir);
        $sql = "SELECT num_bastidor FROM automovil
                     WHERE num_bastidor = '$bastidor' AND num_bastidor != '$excluir'";
        $res = mysqli_query($link, $sql);
        return mysqli_num_rows($res) > 0;
    }
}