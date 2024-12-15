<?php
$host = "localhost";
$user = "root"; // Ganti sesuai user database
$pass = "";     // Ganti sesuai password database
$dbname = "flicksy";

// Membuat koneksi
$conn = new mysqli($host, $user, $pass, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
