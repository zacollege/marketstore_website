<?php
    include __DIR__ . '/koneksi.php';
    session_start();

    //MASTIIN SDH LOGIN DAN ROLE DIKHUSUSKAN BUAT ADMIN
    if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
        header("Location: ../login.php?pesan=belum_login");
        exit();
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $query = mysqli_query($koneksi, "SELECT status FROM orders WHERE id = $id");
    $o = $query ? mysqli_fetch_assoc($query) : false;

    $status_baru = '';
    if ($o['status'] == 'baru') {
        $status_baru = 'diproses';
    } else if ($o['status'] == 'diproses') {
        $status_baru = 'siap';
    } else if ($o['status'] == 'siap') {
        $status_baru = 'selesai';
    }

    if ($status_baru != '') {
        $hasil = mysqli_query($koneksi, "UPDATE orders SET status = '$status_baru' WHERE id = $id");
    } else {
        $hasil = false;
    }

    if ($hasil && mysqli_affected_rows($koneksi) > 0) {
        header("Location: ../admin_pesanan.php?pesan=status_diubah");
    } else {
        header("Location: ../admin_pesanan.php?pesan=data_tidak_valid");
    }
    exit();
?>