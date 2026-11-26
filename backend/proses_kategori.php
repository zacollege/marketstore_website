<?php
    include __DIR__ . '/koneksi.php';
    session_start();

    //MEMASTIKAN LOGIN DAN ROLE ADMIN
    if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
        header("Location: ../login.php?pesan=belum_login");
        exit();
    }

    //TAMBAH KATEGORI(CREATE)
    if (isset($_POST['submitInsert'])) {
        $nama = htmlspecialchars(trim($_POST['name']));
        $query = mysqli_query($koneksi, "INSERT INTO categories (name) VALUES ('$nama')");
        if ($query) {
            header("Location: ../admin_kategori.php?pesan=tersimpan");
            exit();
        }
    }

    //EDIT KATEGORI 
    if (isset($_POST['submitEdit'])) {
        $id = $_POST['id'];
        $nama = htmlspecialchars(trim($_POST['name']));
        $query = mysqli_query($koneksi, "UPDATE categories SET name = '$nama' WHERE id = $id");
        if ($query) {
            header("Location: ../admin_kategori.php?pesan=tersimpan");
            exit();
        }
    }

    //DELETE KATEGORI 
    if (isset($_GET['deleteId'])) {
        $id = $_GET['deleteId'];

        //MENGECUALIKAN HAPUS KATEGORI YANG MASIH DIPAKE DI MENU
        $cek = mysqli_query($koneksi, "SELECT COUNT(*) AS n FROM menus WHERE category_id = $id");
        $data = mysqli_fetch_assoc($cek);
        if ($data['n'] > 0) {
            header("Location: ../admin_kategori.php?pesan=gagal_dipakai");
            exit();
        }

        $query = mysqli_query($koneksi, "DELETE FROM categories WHERE id = $id");
        if ($query) {
            header("Location: ../admin_kategori.php?pesan=dihapus");
            exit();
        }
    }

    header("Location: ../admin_kategori.php");
?>