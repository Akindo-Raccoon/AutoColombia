<?php
require_once __DIR__ . '/../../config/conexionBD.php';

class ModeloAuto
{
    public function listar()
    {
        $sql = "SELECT mo.*, ma.nombre AS nombre_marca
                FROM modelo mo
                INNER JOIN marca ma ON mo.id_marca = ma.id_marca
                ORDER BY ma.nombre, mo.nombre";
        $res  = mysqli_query(Conectar::conec(), $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) $data[] = $row;
        return $data;
    }

    public function buscarPorId($id)
    {
        $id  = intval($id);
        $sql = "SELECT mo.*, ma.nombre AS nombre_marca
                FROM modelo mo
                INNER JOIN marca ma ON mo.id_marca = ma.id_marca
                WHERE mo.id_modelo = $id";
        return mysqli_fetch_assoc(mysqli_query(Conectar::conec(), $sql));
    }

    public function listarMarcas()
    {
        $res  = mysqli_query(Conectar::conec(), "SELECT * FROM marca ORDER BY nombre");
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) $data[] = $row;
        return $data;
    }

    // Listar extras disponibles para un modelo
    public function listarExtrasModelo($id_modelo)
    {
        $id_modelo = intval($id_modelo);
        $sql = "SELECT me.*, e.nombre AS nombre_extra, e.descripcion AS desc_extra, e.categoria
                FROM modelo_extra me
                INNER JOIN extra e ON me.id_extra = e.id_extra
                WHERE me.id_modelo = $id_modelo";
        $res  = mysqli_query(Conectar::conec(), $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) $data[] = $row;
        return $data;
    }

    public function insertar($nombre, $id_marca, $precio_base, $descuento, $potencia_fiscal,
                              $cilindrada, $tipo_combustible, $num_puertas, $num_plazas, $descripcion)
    {
        $link            = Conectar::conec();
        $nombre          = mysqli_real_escape_string($link, $nombre);
        $id_marca        = intval($id_marca);
        $precio_base     = floatval($precio_base);
        $descuento       = floatval($descuento);
        $potencia_fiscal = floatval($potencia_fiscal);
        $cilindrada      = intval($cilindrada);
        $tipo_combustible = mysqli_real_escape_string($link, $tipo_combustible);
        $num_puertas     = intval($num_puertas);
        $num_plazas      = intval($num_plazas);
        $descripcion     = mysqli_real_escape_string($link, $descripcion);

        $sql = "INSERT INTO modelo (nombre, id_marca, precio_base, descuento, potencia_fiscal,
                cilindrada, tipo_combustible, num_puertas, num_plazas, descripcion)
                VALUES ('$nombre', $id_marca, $precio_base, $descuento, $potencia_fiscal,
                $cilindrada, '$tipo_combustible', $num_puertas, $num_plazas, '$descripcion')";
        return mysqli_query($link, $sql);
    }

    public function editar($id, $nombre, $id_marca, $precio_base, $descuento, $potencia_fiscal,
                            $cilindrada, $tipo_combustible, $num_puertas, $num_plazas, $descripcion, $activo)
    {
        $link            = Conectar::conec();
        $id              = intval($id);
        $nombre          = mysqli_real_escape_string($link, $nombre);
        $id_marca        = intval($id_marca);
        $precio_base     = floatval($precio_base);
        $descuento       = floatval($descuento);
        $potencia_fiscal = floatval($potencia_fiscal);
        $cilindrada      = intval($cilindrada);
        $tipo_combustible = mysqli_real_escape_string($link, $tipo_combustible);
        $num_puertas     = intval($num_puertas);
        $num_plazas      = intval($num_plazas);
        $descripcion     = mysqli_real_escape_string($link, $descripcion);
        $activo          = intval($activo);

        $sql = "UPDATE modelo SET nombre='$nombre', id_marca=$id_marca, precio_base=$precio_base,
                descuento=$descuento, potencia_fiscal=$potencia_fiscal, cilindrada=$cilindrada,
                tipo_combustible='$tipo_combustible', num_puertas=$num_puertas, num_plazas=$num_plazas,
                descripcion='$descripcion', activo=$activo
                WHERE id_modelo = $id";
        return mysqli_query($link, $sql);
    }

    public function eliminar($id)
    {
        $id = intval($id);
        return mysqli_query(Conectar::conec(), "DELETE FROM modelo WHERE id_modelo = $id");
    }
}
