<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "school_library";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set charset untuk mencegah error karakter
mysqli_set_charset($conn, "utf8mb4");
?>
