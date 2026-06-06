<?php
require_once __DIR__ . '/../../config/conexionBD.php';

class Venta
{
    // LISTAR todas las ventas con joins
    public function listar()
    {
        $sql = "SELECT v.*,
                       a.color, a.anio_fabricacion,
                       mo.nombre  AS nombre_modelo,
                       ma.nombre  AS nombre_marca,
                       vend.nombre AS nombre_vendedor,
                       vend.apellidos AS apellidos_vendedor,
                       fp.tipo    AS tipo_pago,
                       fp.nombre_financiera,
                       u.nombre   AS nombre_usuario,
                       u.apellidos AS apellidos_usuario
                FROM venta v
                INNER JOIN automovil a   ON v.num_bastidor  = a.num_bastidor
                INNER JOIN modelo mo     ON a.id_modelo     = mo.id_modelo
                INNER JOIN marca  ma     ON mo.id_marca     = ma.id_marca
                INNER JOIN forma_pago fp ON v.id_forma_pago = fp.id_forma_pago
                LEFT  JOIN vendedor vend ON v.id_vendedor   = vend.id_vendedor
                LEFT  JOIN usuario  u    ON v.id_usuario    = u.id_usuario
                ORDER BY v.fecha_venta DESC";
        $res  = mysqli_query(Conectar::conec(), $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        return $data;
    }

    // BUSCAR venta por ID
    public function buscarPorId($id)
    {
        $link = Conectar::conec();
        $id   = intval($id);
        $sql  = "SELECT v.*,
                        a.color, a.anio_fabricacion,
                        mo.nombre AS nombre_modelo,
                        ma.nombre AS nombre_marca,
                        fp.tipo   AS tipo_pago,
                        fp.nombre_financiera,
                        fp.condiciones,
                        fp.tasa_interes,
                        vend.nombre    AS nombre_vendedor,
                        vend.apellidos AS apellidos_vendedor,
                        u.nombre       AS nombre_usuario,
                        u.apellidos    AS apellidos_usuario,
                        u.email        AS email_usuario
                 FROM venta v
                 INNER JOIN automovil a   ON v.num_bastidor  = a.num_bastidor
                 INNER JOIN modelo mo     ON a.id_modelo     = mo.id_modelo
                 INNER JOIN marca  ma     ON mo.id_marca     = ma.id_marca
                 INNER JOIN forma_pago fp ON v.id_forma_pago = fp.id_forma_pago
                 LEFT  JOIN vendedor vend ON v.id_vendedor   = vend.id_vendedor
                 LEFT  JOIN usuario  u    ON v.id_usuario    = u.id_usuario
                 WHERE v.id_venta = $id";
        $res  = mysqli_query($link, $sql);
        return mysqli_fetch_assoc($res);
    }

    // LISTAR extras de una venta
    public function listarExtras($id_venta)
    {
        $id_venta = intval($id_venta);
        $sql = "SELECT ve.*, e.nombre AS nombre_extra, e.categoria
                FROM venta_extra ve
                INNER JOIN extra e ON ve.id_extra = e.id_extra
                WHERE ve.id_venta = $id_venta";
        $res  = mysqli_query(Conectar::conec(), $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        return $data;
    }

    // INSERTAR nueva venta (compra por usuario)
    public function insertar($num_bastidor, $fecha_venta, $fecha_entrega, $matricula,
                              $precio_cobrado, $id_forma_pago, $tipo_vendedor,
                              $id_vendedor, $id_servicio, $id_usuario, $observaciones)
    {
        $link           = Conectar::conec();
        $num_bastidor   = mysqli_real_escape_string($link, $num_bastidor);
        $fecha_venta    = mysqli_real_escape_string($link, $fecha_venta);
        $fecha_entrega  = $fecha_entrega ? "'" . mysqli_real_escape_string($link, $fecha_entrega) . "'" : 'NULL';
        $matricula      = $matricula ? "'" . mysqli_real_escape_string($link, $matricula) . "'" : 'NULL';
        $precio_cobrado = floatval($precio_cobrado);
        $id_forma_pago  = intval($id_forma_pago);
        $tipo_vendedor  = mysqli_real_escape_string($link, $tipo_vendedor);
        $id_vendedor    = ($id_vendedor && $id_vendedor != '') ? intval($id_vendedor) : 'NULL';
        $id_servicio    = ($id_servicio  && $id_servicio  != '') ? intval($id_servicio)  : 'NULL';
        $id_usuario     = ($id_usuario   && $id_usuario   != '') ? intval($id_usuario)   : 'NULL';
        $observaciones  = mysqli_real_escape_string($link, $observaciones);

        $sql = "INSERT INTO venta
                    (num_bastidor, fecha_venta, fecha_entrega, matricula,
                     precio_cobrado, id_forma_pago, es_stock, tipo_vendedor,
                     id_vendedor, id_servicio, id_usuario, observaciones)
                VALUES
                    ('$num_bastidor', '$fecha_venta', $fecha_entrega, $matricula,
                     $precio_cobrado, $id_forma_pago, 1, '$tipo_vendedor',
                     $id_vendedor, $id_servicio, $id_usuario, '$observaciones')";

        if (mysqli_query($link, $sql)) {
            return mysqli_insert_id($link);
        }
        return false;
    }

    // INSERTAR extra de venta
    public function insertarExtra($id_venta, $id_extra, $precio_cobrado_extra)
    {
        $link                = Conectar::conec();
        $id_venta            = intval($id_venta);
        $id_extra            = intval($id_extra);
        $precio_cobrado_extra = floatval($precio_cobrado_extra);
        $sql = "INSERT INTO venta_extra (id_venta, id_extra, precio_cobrado_extra)
                VALUES ($id_venta, $id_extra, $precio_cobrado_extra)";
        return mysqli_query($link, $sql);
    }

    // ELIMINAR venta
    public function eliminar($id)
    {
        $link = Conectar::conec();
        $id   = intval($id);
        // Primero eliminar extras
        mysqli_query($link, "DELETE FROM venta_extra WHERE id_venta = $id");
        return mysqli_query($link, "DELETE FROM venta WHERE id_venta = $id");
    }

    // VENTAS POR USUARIO
    public function listarPorUsuario($id_usuario)
    {
        $id_usuario = intval($id_usuario);
        $sql = "SELECT v.*,
                       mo.nombre AS nombre_modelo,
                       ma.nombre AS nombre_marca,
                       a.color, a.anio_fabricacion,
                       fp.tipo   AS tipo_pago,
                       fp.nombre_financiera
                FROM venta v
                INNER JOIN automovil a   ON v.num_bastidor  = a.num_bastidor
                INNER JOIN modelo mo     ON a.id_modelo     = mo.id_modelo
                INNER JOIN marca  ma     ON mo.id_marca     = ma.id_marca
                INNER JOIN forma_pago fp ON v.id_forma_pago = fp.id_forma_pago
                WHERE v.id_usuario = $id_usuario
                ORDER BY v.fecha_venta DESC";
        $res  = mysqli_query(Conectar::conec(), $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        return $data;
    }

    // MARCAR automóvil como vendido
    public function marcarVendido($num_bastidor)
    {
        $link = Conectar::conec();
        $b    = mysqli_real_escape_string($link, $num_bastidor);
        return mysqli_query($link, "UPDATE automovil SET estado='vendido' WHERE num_bastidor='$b'");
    }

    // REPORTE parametrizable
    public function reporte($filtros = [])
    {
        $link  = Conectar::conec();
        $where = [];

        if (!empty($filtros['fecha_inicio'])) {
            $fi = mysqli_real_escape_string($link, $filtros['fecha_inicio']);
            $where[] = "v.fecha_venta >= '$fi'";
        }
        if (!empty($filtros['fecha_fin'])) {
            $ff = mysqli_real_escape_string($link, $filtros['fecha_fin']);
            $where[] = "v.fecha_venta <= '$ff'";
        }
        if (!empty($filtros['id_vendedor'])) {
            $where[] = "v.id_vendedor = " . intval($filtros['id_vendedor']);
        }
        if (!empty($filtros['id_concesionario'])) {
            $where[] = "a.id_concesionario = " . intval($filtros['id_concesionario']);
        }
        if (!empty($filtros['id_marca'])) {
            $where[] = "ma.id_marca = " . intval($filtros['id_marca']);
        }
        if (!empty($filtros['id_forma_pago'])) {
            $where[] = "v.id_forma_pago = " . intval($filtros['id_forma_pago']);
        }

        $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $limit    = (!empty($filtros['top']) && intval($filtros['top']) > 0)
                    ? 'LIMIT ' . intval($filtros['top'])
                    : '';

        $sql = "SELECT v.*,
                       mo.nombre  AS nombre_modelo,
                       ma.nombre  AS nombre_marca,
                       a.color,
                       c.nombre   AS nombre_concesionario,
                       vend.nombre AS nombre_vendedor,
                       vend.apellidos AS apellidos_vendedor,
                       fp.tipo    AS tipo_pago,
                       fp.nombre_financiera,
                       u.nombre   AS nombre_usuario,
                       u.apellidos AS apellidos_usuario
                FROM venta v
                INNER JOIN automovil a   ON v.num_bastidor  = a.num_bastidor
                INNER JOIN modelo mo     ON a.id_modelo     = mo.id_modelo
                INNER JOIN marca  ma     ON mo.id_marca     = ma.id_marca
                INNER JOIN concesionario c ON a.id_concesionario = c.id_concesionario
                INNER JOIN forma_pago fp ON v.id_forma_pago = fp.id_forma_pago
                LEFT  JOIN vendedor vend ON v.id_vendedor   = vend.id_vendedor
                LEFT  JOIN usuario  u    ON v.id_usuario    = u.id_usuario
                $whereSQL
                ORDER BY v.fecha_venta DESC
                $limit";

        $res  = mysqli_query(Conectar::conec(), $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        return $data;
    }
}
