<?php
require_once __DIR__ . '/../../config/conexionBD.php';

class Concesionario
{
    public function listar()
    {
        $sql  = "SELECT * FROM concesionario ORDER BY nombre";
        $res  = mysqli_query(Conectar::conec(), $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) $data[] = $row;
        return $data;
    }

    public function buscarPorId($id)
    {
        $id  = intval($id);
        return mysqli_fetch_assoc(mysqli_query(Conectar::conec(),
            "SELECT * FROM concesionario WHERE id_concesionario = $id"));
    }

    public function insertar($nombre, $domicilio, $nif, $telefono, $email)
    {
        $link      = Conectar::conec();
        $nombre    = mysqli_real_escape_string($link, $nombre);
        $domicilio = mysqli_real_escape_string($link, $domicilio);
        $nif       = mysqli_real_escape_string($link, $nif);
        $telefono  = mysqli_real_escape_string($link, $telefono);
        $email     = mysqli_real_escape_string($link, $email);
        $sql = "INSERT INTO concesionario (nombre, domicilio, nif, telefono, email)
                VALUES ('$nombre', '$domicilio', '$nif', '$telefono', '$email')";
        return mysqli_query($link, $sql);
    }

    public function editar($id, $nombre, $domicilio, $nif, $telefono, $email)
    {
        $link      = Conectar::conec();
        $id        = intval($id);
        $nombre    = mysqli_real_escape_string($link, $nombre);
        $domicilio = mysqli_real_escape_string($link, $domicilio);
        $nif       = mysqli_real_escape_string($link, $nif);
        $telefono  = mysqli_real_escape_string($link, $telefono);
        $email     = mysqli_real_escape_string($link, $email);
        $sql = "UPDATE concesionario SET nombre='$nombre', domicilio='$domicilio',
                nif='$nif', telefono='$telefono', email='$email'
                WHERE id_concesionario = $id";
        return mysqli_query($link, $sql);
    }

    public function eliminar($id)
    {
        $id = intval($id);
        return mysqli_query(Conectar::conec(), "DELETE FROM concesionario WHERE id_concesionario = $id");
    }

    public function nifExiste($nif, $excluir_id = 0)
    {
        $link = Conectar::conec();
        $nif  = mysqli_real_escape_string($link, $nif);
        $excluir_id = intval($excluir_id);
        $res  = mysqli_query($link, "SELECT id_concesionario FROM concesionario WHERE nif='$nif' AND id_concesionario != $excluir_id");
        return mysqli_num_rows($res) > 0;
    }
}
