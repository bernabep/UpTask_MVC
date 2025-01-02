<?php

// $db = mysqli_connect('192.168.0.89', 'remote', 'root', 'uptask_mvc');
$db = mysqli_connect('89.58.31.246', 'appsalon-bpandofdesarrollador', 'T9LCZ)fy98_-zrm29M', 'appsalon_bpandofdesarrollador_uptask_mvc');

if (!$db) {
    echo "Error: No se pudo conectar a MySQL.";
    echo "errno de depuración: " . mysqli_connect_errno();
    echo "error de depuración: " . mysqli_connect_error();
    exit;
}
