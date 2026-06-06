<?php
require_once __DIR__ . '/config/conexionBD.php';
$link = Conectar::conec();
$res = mysqli_query($link, "SELECT id_usuario, email, password, perfil, activo FROM usuario");
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
