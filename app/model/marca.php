<?php
require_once __DIR__ . '/../../config/conexionBD.php';

class Marca
{
    public function listar()
    {
        $sql = "SELECT * FROM marca ORDER BY nombre";
        $res  = mysqli_query(Conectar::conec(), $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) $data[] = $row;
        return $data;
    }

    public function buscarPorId($id)
    {
        $id  = intval($id);
        $sql = "SELECT * FROM marca WHERE id_marca = $id";
        return mysqli_fetch_assoc(mysqli_query(Conectar::conec(), $sql));
    }

    public function insertar($nombre, $pais_origen, $descripcion)
    {
        $link        = Conectar::conec();
        $nombre      = mysqli_real_escape_string($link, $nombre);
        $pais_origen = mysqli_real_escape_string($link, $pais_origen);
        $descripcion = mysqli_real_escape_string($link, $descripcion);
        $sql = "INSERT INTO marca (nombre, pais_origen, descripcion)
                VALUES ('$nombre', '$pais_origen', '$descripcion')";
        return mysqli_query($link, $sql);
    }

    public function editar($id, $nombre, $pais_origen, $descripcion)
    {
        $link        = Conectar::conec();
        $id          = intval($id);
        $nombre      = mysqli_real_escape_string($link, $nombre);
        $pais_origen = mysqli_real_escape_string($link, $pais_origen);
        $descripcion = mysqli_real_escape_string($link, $descripcion);
        $sql = "UPDATE marca SET nombre='$nombre', pais_origen='$pais_origen',
                descripcion='$descripcion' WHERE id_marca = $id";
        return mysqli_query($link, $sql);
    }

    public function eliminar($id)
    {
        $id  = intval($id);
        return mysqli_query(Conectar::conec(), "DELETE FROM marca WHERE id_marca = $id");
    }

    public function nombreExiste($nombre, $excluir_id = 0)
    {
        $link   = Conectar::conec();
        $nombre = mysqli_real_escape_string($link, $nombre);
        $excluir_id = intval($excluir_id);
        $res = mysqli_query($link, "SELECT id_marca FROM marca WHERE nombre='$nombre' AND id_marca != $excluir_id");
        return mysqli_num_rows($res) > 0;
    }
}
