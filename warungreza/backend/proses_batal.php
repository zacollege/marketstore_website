<?php
    include __DIR__ . '/koneksi.php';
    session_start();

    //MASTIIN SDH LOGIN
    if (!isset($_SESSION['status']) || $_SESSION['status'] != true) {
        header("Location: ../login.php?pesan=belum_login");
        exit();
    }
    //AMBIL ROLE 
    $role = $_SESSION['role'];

    //Selesai proses batal, admin ke dashboard, pelanggan ke halaman pesanan
    if ($role == 'admin') {
        $balik = '../admin_pesanan.php';
    } else {
        $balik = '../pesanan.php';
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $alasan = isset($_POST['alasan']) ? htmlspecialchars(trim($_POST['alasan'])) : '';
    $lain = isset($_POST['alasan_lain']) ? htmlspecialchars(trim($_POST['alasan_lain'])) : '';

    //MEWAJIBKAN ALASAN DIISI KALO PILIH "Alasan lainnya"
    if (empty($alasan) || ($alasan == 'Alasan lainnya' && empty($lain))) {
        header("Location: $balik?pesan=alasan_wajib");
        exit();
    }
    if ($alasan == 'Alasan lainnya') {
        $alasan = 'Lainnya: ' . $lain;
    }

    //Admin bisa membatalkan pesanan dalam status apapun
    //Pelanggan cuma bisa membatalkan kalo statusnya masih "baru"
    if ($role == 'admin') {
        $syarat = "status IN ('baru','diproses','siap')";
    } else {
        $syarat = "status = 'baru' AND user_id = " . $_SESSION['id'];
    }
    $query = mysqli_query($koneksi, "SELECT * FROM orders WHERE id = $id AND $syarat");
    $o = mysqli_fetch_assoc($query);
    if (!$o) {
        header("Location: $balik?pesan=alasan_wajib");
        exit();
    }

    //Proses Refund yang cuma terjadi kalau statusnya sudah dibayar (QRIS, transfer)
    $refund = '-';
    if ($o['payment_status'] == 'Dibayar') {
        $refund = 'Refund Berhasil';
    }

    mysqli_query($koneksi,
        "UPDATE orders SET status = 'dibatalkan', cancel_reason = '$alasan', cancelled_by = '$role', refund_status = '$refund'
         WHERE id = $id");

    // STOK MENU DIKEMBALIKAN LAGI SETELAH DIBATALKAN
    $hasil_stok = mysqli_query($koneksi,
        "UPDATE menus m JOIN order_details d ON d.menu_id = m.id SET m.stock = m.stock + d.qty WHERE d.order_id = $id");
    if (!$hasil_stok) { header("Location: $balik?pesan=data_tidak_valid"); exit(); }

    header("Location: $balik?pesan=dibatalkan");
    exit();
?>
