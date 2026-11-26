<?php
    include __DIR__ . '/koneksi.php';
    session_start();

    //MASTIIN DAH LOGIN
    if (!isset($_SESSION['status']) || $_SESSION['status'] != true) {
        header("Location: ../login.php?pesan=belum_login");
        exit();
    }

    //Membatasi role admin
    if ($_SESSION['role'] == 'admin') {
        header("Location: ../index.php?pesan=admin_tidak_bisa_pesan");
        exit();
    }

    $id_user = $_SESSION['id'];
    $jenis = isset($_POST['type']) ? $_POST['type'] : '';
    $meja = isset($_POST['table_no']) ? htmlspecialchars(trim($_POST['table_no'])) : '';
    $metode = isset($_POST['payment']) ? $_POST['payment'] : '';

    //MEMASTIKAN JENIS PESANAN
    if ($jenis != 'dine-in' && $jenis != 'takeaway') {
        header("Location: ../keranjang.php?pesan=data_tidak_valid");
        exit();
    }

    //MEMASTIKAN METODE PEMBAYARAN
    if ($metode != 'Tunai' && $metode != 'QRIS' && $metode != 'Transfer') {
        header("Location: ../keranjang.php?pesan=data_tidak_valid");
        exit();
    }

    //KALAU ADA KEGAGALAN CHECKOUT, BALIK KE KERANJANG, PESANAN SM PEMBAYARAN MASIH TTP ADA
    $kembali = "../keranjang.php?type=$jenis&meja=$meja&payment=$metode";

    // MEWAJIBKAN ISI NOMOR MEJA UNTUK DINE IN
    if ($jenis == 'dine-in' && empty($meja)) {
        header("Location: $kembali&pesan=meja_kosong");
        exit();
    }

    //TAKEAWAY GA PERLU ISI NO MEJA
    if ($jenis == 'takeaway') {
        $meja = '';
    }

    //KALAU KERANJANG MASIH KOSONG
    $q_isi = mysqli_query($koneksi, "SELECT COUNT(*) AS n FROM cart WHERE user_id = $id_user");
    $d_isi = mysqli_fetch_assoc($q_isi);
    if ($d_isi['n'] == 0) {
        header("Location: ../keranjang.php");
        exit();
    }

    // MEMASTIKAN TIDAK ADA ITEM DENGAN JUMLAH 0 ATAU KURANG DARI 0
    $q_jumlah = mysqli_query($koneksi, "SELECT COUNT(*) AS n FROM cart WHERE user_id = $id_user AND qty <= 0");
    $d_jumlah = mysqli_fetch_assoc($q_jumlah);
    if ($d_jumlah['n'] > 0) {
        header("Location: ../keranjang.php?pesan=jumlah_tidak_valid");
        exit();
    }

    //CEK APAKAH STOK MENCUKUPI
    $q_stok = mysqli_query($koneksi,
        "SELECT COUNT(*) AS n FROM cart k JOIN menus m ON m.id = k.menu_id WHERE k.user_id = $id_user AND k.qty > m.stock");
    $d_stok = mysqli_fetch_assoc($q_stok);
    if ($d_stok['n'] > 0) {
        header("Location: $kembali&pesan=stok_kurang");
        exit();
    }

    //HITUNG TOTAL HARGA PESANAN
    $q_total = mysqli_query($koneksi,
        "SELECT SUM(m.price * k.qty) AS total FROM cart k JOIN menus m ON m.id = k.menu_id WHERE k.user_id = $id_user");
    $d_total = mysqli_fetch_assoc($q_total);
    $total = $d_total['total'];

    //STATUS PEMBAYARAN
    if ($metode == 'Tunai') {
        $status_bayar = 'Bayar di Kasir';
    } else {
        $status_bayar = 'Menunggu Pembayaran';
    }

    //SIMPAN PESANAN
    $hasil_order = mysqli_query($koneksi,
        "INSERT INTO orders (user_id, type, table_no, payment, payment_status, total)
         VALUES ($id_user, '$jenis', '$meja', '$metode', '$status_bayar', $total)");
    if (!$hasil_order) {
        header("Location: ../keranjang.php?pesan=checkout_gagal");
        exit();
    }

    //AMBIL ID PESANAN YANG BARU DIBUAT
    $id_pesanan = mysqli_insert_id($koneksi);

    //MENYIMPAN KE TABEL ORDER
    $hasil_detail = mysqli_query($koneksi,
        "INSERT INTO order_details (order_id, menu_id, qty, price, note)
         SELECT $id_pesanan, k.menu_id, k.qty, m.price, k.note
         FROM cart k JOIN menus m ON m.id = k.menu_id WHERE k.user_id = $id_user");
    if (!$hasil_detail) {
        header("Location: ../keranjang.php?pesan=checkout_gagal");
        exit();
    }

    //MENGURANGI STOK MENU, SESUAI DGN YG DIPESAN
    $hasil_stock = mysqli_query($koneksi,
        "UPDATE menus m JOIN cart k ON k.menu_id = m.id SET m.stock = m.stock - k.qty WHERE k.user_id = $id_user");
    if (!$hasil_stock) {
        header("Location: ../keranjang.php?pesan=checkout_gagal");
        exit();
    }

    //KOSONGKAN KERANJANG
    $hasil_hapus_cart = mysqli_query($koneksi, "DELETE FROM cart WHERE user_id = $id_user");
    if (!$hasil_hapus_cart) {
        header("Location: ../keranjang.php?pesan=checkout_gagal");
        exit();
    }

    //KALAU TUNAI LANGSUNG KE DAFTAR PESANAN
    //KALAU QRIS/TRANSFER DIARAHKAN KE KERANJANG
    if ($metode == 'Tunai') {
        header("Location: ../pesanan.php?pesan=pesanan_dibuat");
    } else {
        header("Location: ../keranjang.php?bayar=$id_pesanan");
    }
    exit();
?>