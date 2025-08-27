<?php
$host = "localhost";
$user = "root";
$pass = "password";
$dbname = "db_fiyan";

// Membuat koneksi
$conn = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
