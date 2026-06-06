<?php
// model/usuario.php
require_once __DIR__ . '/../../config/conexionBD.php';

class Usuario
{

    // LISTAR todos los usuarios 
    public function listar()
    {
        $sql = "SELECT u.*, v.nombre AS nombre_vendedor
                FROM usuario u
                LEFT JOIN vendedor v ON u.id_vendedor = v.id_vendedor
                ORDER BY u.id_usuario ASC";
        $res = mysqli_query(Conectar::conec(), $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        return $data;
    }

    // BUSCAR usuario por ID 
    public function buscarPorId($id)
    {
        $id = intval($id);
        $sql = "SELECT * FROM usuario WHERE id_usuario = $id";
        $res = mysqli_query(Conectar::conec(), $sql);
        return mysqli_fetch_assoc($res);
    }

    // BUSCAR usuario por EMAIL 
    // Usado en el login para validar credenciales
    public function buscarPorEmail($email)
    {
        $email = mysqli_real_escape_string(Conectar::conec(), $email);
        $sql = "SELECT * FROM usuario WHERE email = '$email' AND activo = 1";
        $res = mysqli_query(Conectar::conec(), $sql);
        return mysqli_fetch_assoc($res);
    }

    // INSERTAR nuevo usuario 
    public function insertar($nombre, $apellidos, $email, $password, $perfil, $id_vendedor)
    {

        $link = Conectar::conec();
        $nombre = mysqli_real_escape_string($link, $nombre);
        $apellidos = mysqli_real_escape_string($link, $apellidos);
        $email = mysqli_real_escape_string($link, $email);
        // Guardar password con MD5 (igual que el estilo del ejemplo de clase)
        $pass_hash = md5($password);
        $perfil = mysqli_real_escape_string($link, $perfil);
        $id_v = ($id_vendedor != '' && $id_vendedor != null) ? intval($id_vendedor) : 'NULL';

        $sql = "INSERT INTO usuario (nombre, apellidos, email, password, perfil, id_vendedor)
                VALUES ('$nombre', '$apellidos', '$email', '$pass_hash', '$perfil', $id_v)";
        return mysqli_query($link, $sql);
    }

    // EDITAR usuario existente
    public function editar($id, $nombre, $apellidos, $email, $perfil, $id_vendedor, $activo)
    {
        $link = Conectar::conec();
        $id = intval($id);
        $nombre = mysqli_real_escape_string($link, $nombre);
        $apellidos = mysqli_real_escape_string($link, $apellidos);
        $email = mysqli_real_escape_string($link, $email);
        $perfil = mysqli_real_escape_string($link, $perfil);
        $activo = intval($activo);
        $id_v = ($id_vendedor != '' && $id_vendedor != null) ? intval($id_vendedor) : 'NULL';

        $sql = "UPDATE usuario
                SET nombre='$nombre', apellidos='$apellidos', email='$email',
                    perfil='$perfil', id_vendedor=$id_v, activo=$activo
                WHERE id_usuario = $id";
        return mysqli_query($link, $sql);
    }

    // CAMBIAR contraseña
    public function cambiarPassword($id, $nueva_pass)
    {
        $link = Conectar::conec();
        $id = intval($id);
        $pass_hash = md5($nueva_pass);
        $sql = "UPDATE usuario SET password='$pass_hash' WHERE id_usuario = $id";
        return mysqli_query($link, $sql);
    }

    // ELIMINAR usuario
    public function eliminar($id)
    {
        $id = intval($id);
        $sql = "DELETE FROM usuario WHERE id_usuario = $id";
        return mysqli_query(Conectar::conec(), $sql);
    }

    // ACTUALIZAR último acceso
    public function actualizarAcceso($id)
    {
        $id = intval($id);
        $sql = "UPDATE usuario SET ultimo_acceso = NOW() WHERE id_usuario = $id";
        mysqli_query(Conectar::conec(), $sql);
    }

    // VERIFICAR si el email ya existe
    public function emailExiste($email, $excluir_id = 0)
    {
        $link = Conectar::conec();
        $email = mysqli_real_escape_string($link, $email);
        $excluir_id = intval($excluir_id);
        $sql = "SELECT id_usuario FROM usuario
                  WHERE email = '$email' AND id_usuario != $excluir_id";
        $res = mysqli_query($link, $sql);
        return mysqli_num_rows($res) > 0;
    }
}
