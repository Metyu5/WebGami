<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sigam";

// Buat koneksi
$koneksi = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($koneksi->connect_error) {
    // Anda bisa memilih untuk die() di sini jika koneksi gagal total
    // Atau hanya menyimpan error untuk ditangani nanti di halaman pemanggil
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Tidak ada output HTML atau CSS di sini!
// File ini hanya bertugas membuat objek $koneksi.
?>