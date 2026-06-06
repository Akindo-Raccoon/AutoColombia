<?php
require_once __DIR__ . '/../../config/conexionBD.php';

class FormaPago
{
    public function listar()
    {
        $sql  = "SELECT * FROM forma_pago ORDER BY tipo, nombre_financiera";
        $res  = mysqli_query(Conectar::conec(), $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) $data[] = $row;
        return $data;
    }

    public function buscarPorId($id)
    {
        $id  = intval($id);
        return mysqli_fetch_assoc(mysqli_query(Conectar::conec(),
            "SELECT * FROM forma_pago WHERE id_forma_pago = $id"));
    }

    public function insertar($tipo, $nombre_financiera, $condiciones, $tasa_interes)
    {
        $link             = Conectar::conec();
        $tipo             = mysqli_real_escape_string($link, $tipo);
        $nombre_financiera = $nombre_financiera ? "'" . mysqli_real_escape_string($link, $nombre_financiera) . "'" : 'NULL';
        $condiciones      = mysqli_real_escape_string($link, $condiciones);
        $tasa_interes     = ($tasa_interes !== '' && $tasa_interes !== null) ? floatval($tasa_interes) : 'NULL';

        $sql = "INSERT INTO forma_pago (tipo, nombre_financiera, condiciones, tasa_interes)
                VALUES ('$tipo', $nombre_financiera, '$condiciones', $tasa_interes)";
        return mysqli_query($link, $sql);
    }

    public function editar($id, $tipo, $nombre_financiera, $condiciones, $tasa_interes)
    {
        $link             = Conectar::conec();
        $id               = intval($id);
        $tipo             = mysqli_real_escape_string($link, $tipo);
        $nombre_financiera = $nombre_financiera ? "'" . mysqli_real_escape_string($link, $nombre_financiera) . "'" : 'NULL';
        $condiciones      = mysqli_real_escape_string($link, $condiciones);
        $tasa_interes     = ($tasa_interes !== '' && $tasa_interes !== null) ? floatval($tasa_interes) : 'NULL';

        $sql = "UPDATE forma_pago SET tipo='$tipo', nombre_financiera=$nombre_financiera,
                condiciones='$condiciones', tasa_interes=$tasa_interes
                WHERE id_forma_pago = $id";
        return mysqli_query($link, $sql);
    }

    public function eliminar($id)
    {
        $id = intval($id);
        return mysqli_query(Conectar::conec(), "DELETE FROM forma_pago WHERE id_forma_pago = $id");
    }
}
