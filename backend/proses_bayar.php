<?php
    include __DIR__ . '/koneksi.php';
    session_start();

    //MASTIIN SUDAH LOGIN
    if (!isset($_SESSION['status']) || $_SESSION['role'] != 'pelanggan') {
        header("Location: ../login.php?pesan=belum_login");
        exit();
    }
    
    //AMBIL ID
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $id_user = $_SESSION['id'];

    //SET STATUS SUDAH DIBAYAR KALO UDAH BAYAR
    $hasil = mysqli_query($koneksi,
        "UPDATE orders SET payment_status = 'Dibayar'
         WHERE id = $id AND user_id = $id_user AND payment_status = 'Menunggu Pembayaran' AND status <> 'dibatalkan'");

    if ($hasil && mysqli_affected_rows($koneksi) > 0) {
        header("Location: ../pesanan.php?pesan=pembayaran_berhasil");
    } else {
        header("Location: ../pesanan.php?pesan=data_tidak_valid");
    }
    exit();
?>