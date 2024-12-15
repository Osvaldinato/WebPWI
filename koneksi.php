<?php
$host = 'sql111.infinityfree.com';
$user = 'if0_37916323';
$password = 'wiTcWpHG5b9oWZ';
$database = 'if0_37916323_db_pendaftaran';

// Membuat koneksi
$koneksi = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($koneksi->connect_error) {
    die('Koneksi gagal: ' . $koneksi->connect_error);
}
?>
