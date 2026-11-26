<?php
//hapus semua data login dan data pesanan dsb, balik ke login
    session_start();
    session_destroy();
    header("Location: ../login.php?pesan=logout");
    exit();
?>