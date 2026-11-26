<?php
    include __DIR__ . '/koneksi.php';
    session_start();

    //MASTIIN LOGIN DAN MENGKHUSUSKAN UTK ROLE ADMIN AJA
    if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
        header("Location: ../login.php?pesan=belum_login");
        exit();
    }

    //TAMBAH MENU
    if (isset($_POST['submitInsert'])) {
        $kat = $_POST['category_id'];
        $nama = htmlspecialchars(trim($_POST['name']));
        $desk = htmlspecialchars(trim($_POST['description']));
        $harga = $_POST['price'];
        $stok = $_POST['stock'];
        $gambar = htmlspecialchars(trim($_POST['image']));

        $query = mysqli_query($koneksi,
            "INSERT INTO menus (category_id, name, description, price, stock, image) VALUES ($kat, '$nama', '$desk', $harga, $stok, '$gambar')");
        if ($query) {
            header("Location: ../admin_menu.php?pesan=tersimpan");
            exit();
        }
    }

    //EDIT MENU
    if (isset($_POST['submitEdit'])) {
        $id = $_POST['id'];
        $kat = $_POST['category_id'];
        $nama = htmlspecialchars(trim($_POST['name']));
        $desk = htmlspecialchars(trim($_POST['description']));
        $harga = $_POST['price'];
        $stok = $_POST['stock'];
        $gambar = htmlspecialchars(trim($_POST['image']));

        $query = mysqli_query($koneksi,
            "UPDATE menus SET category_id = $kat, name = '$nama', description = '$desk', price = $harga, stock = $stok, image = '$gambar' WHERE id = $id");
        if ($query) {
            header("Location: ../admin_menu.php?pesan=tersimpan");
            exit();
        }
    }

    //DELETE MENU
    if (isset($_GET['deleteId'])) {
        $id = $_GET['deleteId'];

        ////MENGECUALIKAN HAPUS MENU YANG SUDAH DIPESAN, AGAR RIWAYAT TTP ADA
        $cek = mysqli_query($koneksi, "SELECT COUNT(*) AS n FROM order_details WHERE menu_id = $id");
        $data = mysqli_fetch_assoc($cek);
        if ($data['n'] > 0) {
            header("Location: ../admin_menu.php?pesan=gagal_dipakai");
            exit();
        }

        $query = mysqli_query($koneksi, "DELETE FROM menus WHERE id = $id");
        if ($query) {
            header("Location: ../admin_menu.php?pesan=dihapus");
            exit();
        }
    }

    header("Location: ../admin_menu.php");

?>
