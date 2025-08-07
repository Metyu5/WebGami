<?php
session_start();
session_unset();  // Menghapus semua session variables
session_destroy();  // Menghancurkan session

// Redirect ke halaman login atau halaman lain setelah logout
header("Location: index.php");
exit();
?>
