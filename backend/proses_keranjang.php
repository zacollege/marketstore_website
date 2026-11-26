<?php
    include __DIR__ . '/koneksi.php';
    session_start();
    //MASTIIN LOGIN
    if (!isset($_SESSION['status']) || $_SESSION['status'] != true) {
        header("Location: ../login.php?pesan=belum_login");
        exit();
    }
    //MENGECUALIKAN ADMIN
    if ($_SESSION['role'] == 'admin') {
        header("Location: ../index.php?pesan=admin_tidak_bisa_pesan");
        exit();
    }

    $id_user = $_SESSION['id'];

    $aksi = '';
    if (isset($_POST['aksi'])) {
        $aksi = $_POST['aksi'];
    }
    if (isset($_GET['aksi'])) {
        $aksi = $_GET['aksi'];
    }

    // TAMBAH MENU DI KERANJANG
    if ($aksi == 'tambah') {
        $id_menu = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;
        $catatan = isset($_POST['note']) ? htmlspecialchars(trim($_POST['note'])) : '';
        if ($id_menu <= 0) {
            header("Location: ../index.php?pesan=jumlah_tidak_valid");
            exit();
        }
        if ($qty < 1) {
            $qty = 1;
        }

        // MEMASTIKAN MENU MASIH TERSEDIA
        $q_menu = mysqli_query($koneksi, "SELECT stock FROM menus WHERE id = $id_menu");
        if (!$q_menu || mysqli_num_rows($q_menu) == 0) {
            header("Location: ../index.php?pesan=item_tidak_ditemukan");
            exit();
        }
        $data_menu = mysqli_fetch_assoc($q_menu);
        if ((int)$data_menu['stock'] <= 0) {
            header("Location: ../index.php?pesan=stok_habis");
            exit();
        }
        if ($qty > (int)$data_menu['stock']) {
            $qty = (int)$data_menu['stock'];
        }

        // MENAMBAH JUMLAH KALAU MENU SUDAH DITAMBAH KE KERANJANG SBLMNYA
        $cek = mysqli_query($koneksi, "SELECT * FROM cart WHERE user_id = $id_user AND menu_id = $id_menu");
        if ($cek && mysqli_num_rows($cek) > 0) {
            $hasil = mysqli_query($koneksi, "UPDATE cart SET qty = qty + $qty, note = '$catatan' WHERE user_id = $id_user AND menu_id = $id_menu");
        } 
        //MENAMBAH BARIS BARU KALAU SEBELUMNYA BLM DITAMBAHKAN KE KERANJANG
        else {
            $hasil = mysqli_query($koneksi, "INSERT INTO cart (user_id, menu_id, qty, note) VALUES ($id_user, $id_menu, $qty, '$catatan')");
        }

        // MEMBATASI JUMLAH PESANAN HANYA UNTUK MENU YANG DITAMBAHKAN
        if ($hasil) {
            $hasil_stok = mysqli_query($koneksi, "UPDATE cart k JOIN menus m ON m.id = k.menu_id SET k.qty = m.stock WHERE k.user_id = $id_user AND k.menu_id = $id_menu AND k.qty > m.stock");
        } else {
            $hasil_stok = false;
        }

        if ($hasil && $hasil_stok) {
            header("Location: ../index.php?pesan=ditambahkan");
        } else {
            header("Location: ../index.php?pesan=keranjang_gagal");
        }
        exit();
    }

    // MENAMBAH JUMLAH MENU DARI KERANJANG
    if ($aksi == 'perbarui') {
        $id_cart_input = isset($_POST['id_cart']) && is_scalar($_POST['id_cart']) ? trim((string)$_POST['id_cart']) : '';
        $qty_input = isset($_POST['qty']) && is_scalar($_POST['qty']) ? trim((string)$_POST['qty']) : '';
        $id_cart = ctype_digit($id_cart_input) ? (int)$id_cart_input : false;
        $qty = ctype_digit($qty_input) ? (int)$qty_input : false;

        // MEMASTIKAN ID DAN JUMLAH PESANAN VALID
        if ($id_cart === false || $id_cart <= 0 || $qty === false || $qty < 0) {
            header("Location: ../keranjang.php?pesan=jumlah_tidak_valid");
            exit();
        }

        // MEMASTIKAN ITEM KERANJANG MILIK PENGGUNA DAN MENGECEK STOK MENU
        $q_item = mysqli_query($koneksi, "SELECT k.id, m.stock FROM cart k JOIN menus m ON m.id = k.menu_id WHERE k.id = $id_cart AND k.user_id = $id_user");
        if (!$q_item || mysqli_num_rows($q_item) == 0) {
            header("Location: ../keranjang.php?pesan=item_tidak_ditemukan");
            exit();
        }
        $data_item = mysqli_fetch_assoc($q_item);

        // KALAU JUMLAH 0, HAPUS ITEM SETELAH PENGGUNA MENYETUJUI KONFIRMASI DI HALAMAN KERANJANG
        if ($qty == 0) {
            $hasil = mysqli_query($koneksi, "DELETE FROM cart WHERE id = $id_cart AND user_id = $id_user");
            if ($hasil) {
                header("Location: ../keranjang.php?pesan=item_dihapus");
            } else {
                header("Location: ../keranjang.php?pesan=pembaruan_gagal");
            }
            exit();
        }

        // MENOLAK JUMLAH YANG MELEBIHI STOK TANPA MENGUBAH ITEM LAIN
        if ($qty > (int)$data_item['stock']) {
            header("Location: ../keranjang.php?pesan=stok_kurang");
            exit();
        }

        // EDIT JUMLAH HANYA PADA ITEM YANG DIPILIH
        $hasil = mysqli_query($koneksi, "UPDATE cart SET qty = $qty WHERE id = $id_cart AND user_id = $id_user");
        if ($hasil) {
            header("Location: ../keranjang.php?pesan=keranjang_diperbarui");
        } else {
            header("Location: ../keranjang.php?pesan=pembaruan_gagal");
        }
        exit();
    }

    // HAPUS SALAH SATU MENU DI KERANJNAG 
    if ($aksi == 'hapus') {
        $id_cart = $_GET['id_cart'];
        mysqli_query($koneksi, "DELETE FROM cart WHERE id = $id_cart AND user_id = $id_user");
        header("Location: ../keranjang.php?pesan=item_dihapus");
        exit();
    }

    header("Location: ../keranjang.php");
?>