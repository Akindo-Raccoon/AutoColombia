<?php
// model/vendedor.php
require_once __DIR__ . '/../../config/conexionBD.php';

class Vendedor
{
    // LISTAR todos los vendedores con nombre del concesionario
    public function listar()
    {
        $sql = "SELECT v.*, c.nombre AS nombre_concesionario
                FROM vendedor v
                INNER JOIN concesionario c ON v.id_concesionario = c.id_concesionario
                ORDER BY v.id_vendedor ASC";
        $res  = mysqli_query(Conectar::conec(), $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        return $data;
    }

    // BUSCAR vendedor por ID
    public function buscarPorId($id)
    {
        $id  = intval($id);
        $sql = "SELECT * FROM vendedor WHERE id_vendedor = $id";
        $res = mysqli_query(Conectar::conec(), $sql);
        return mysqli_fetch_assoc($res);
    }

    // BUSCAR vendedor por EMAIL
    // Usado en el registro para vincular un nuevo usuario con
    // su registro de vendedor (que el admin crea previamente).
    public function buscarPorEmail($email)
    {
        $link  = Conectar::conec();
        $email = mysqli_real_escape_string($link, $email);
        $sql   = "SELECT * FROM vendedor WHERE email = '$email' LIMIT 1";
        $res   = mysqli_query($link, $sql);
        return mysqli_fetch_assoc($res);
    }

    // INSERTAR nuevo vendedor
    public function insertar($nombre, $apellidos, $nif, $domicilio, $telefono, $email, $fecha_contratacion, $id_concesionario)
    {
        $link              = Conectar::conec();
        $nombre            = mysqli_real_escape_string($link, $nombre);
        $apellidos         = mysqli_real_escape_string($link, $apellidos);
        $nif               = mysqli_real_escape_string($link, $nif);
        $domicilio         = mysqli_real_escape_string($link, $domicilio);
        $telefono          = mysqli_real_escape_string($link, $telefono);
        $email             = mysqli_real_escape_string($link, $email);
        $fecha_contratacion = mysqli_real_escape_string($link, $fecha_contratacion);
        $id_concesionario  = intval($id_concesionario);

        $sql = "INSERT INTO vendedor
                    (nombre, apellidos, nif, domicilio, telefono, email, fecha_contratacion, id_concesionario)
                VALUES
                    ('$nombre', '$apellidos', '$nif', '$domicilio', '$telefono', '$email', '$fecha_contratacion', $id_concesionario)";
        return mysqli_query($link, $sql);
    }

    // EDITAR vendedor
    public function editar($id, $nombre, $apellidos, $nif, $domicilio, $telefono, $email, $fecha_contratacion, $id_concesionario, $activo)
    {
        $link              = Conectar::conec();
        $id                = intval($id);
        $nombre            = mysqli_real_escape_string($link, $nombre);
        $apellidos         = mysqli_real_escape_string($link, $apellidos);
        $nif               = mysqli_real_escape_string($link, $nif);
        $domicilio         = mysqli_real_escape_string($link, $domicilio);
        $telefono          = mysqli_real_escape_string($link, $telefono);
        $email             = mysqli_real_escape_string($link, $email);
        $fecha_contratacion = mysqli_real_escape_string($link, $fecha_contratacion);
        $id_concesionario  = intval($id_concesionario);
        $activo            = intval($activo);

        $sql = "UPDATE vendedor
                SET nombre='$nombre', apellidos='$apellidos', nif='$nif',
                    domicilio='$domicilio', telefono='$telefono', email='$email',
                    fecha_contratacion='$fecha_contratacion',
                    id_concesionario=$id_concesionario, activo=$activo
                WHERE id_vendedor = $id";
        return mysqli_query($link, $sql);
    }

    // ELIMINAR vendedor
    public function eliminar($id)
    {
        $id  = intval($id);
        $sql = "DELETE FROM vendedor WHERE id_vendedor = $id";
        return mysqli_query(Conectar::conec(), $sql);
    }

    // LISTAR concesionarios (para el select del formulario)
    public function listarConcesionarios()
    {
        $sql  = "SELECT * FROM concesionario ORDER BY nombre";
        $res  = mysqli_query(Conectar::conec(), $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        return $data;
    }

    // VERIFICAR si el NIF ya existe
    public function nifExiste($nif, $excluir_id = 0)
    {
        $link       = Conectar::conec();
        $nif        = mysqli_real_escape_string($link, $nif);
        $excluir_id = intval($excluir_id);
        $sql        = "SELECT id_vendedor FROM vendedor
                       WHERE nif = '$nif' AND id_vendedor != $excluir_id";
        $res        = mysqli_query($link, $sql);
        return mysqli_num_rows($res) > 0;
    }
}