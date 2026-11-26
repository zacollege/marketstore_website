<?php
//CRUD SEMUA MENU MAKANAN ATAU MINUMAN YANG BISA AKSES HANYA ADMIN. PROSES CRUD ADA DI backedn proses_menu.php

    include 'backend/koneksi.php';
    session_start();

    // HALAMAN TERAKSES KALAU SUDAH LOGIN DAN ROLE:ADMIN
    if (!isset($_SESSION['status']) || $_SESSION['status'] != true) {
        header("Location: login.php?pesan=belum_login");
        exit();
    }
    if ($_SESSION['role'] != 'admin') {
        header("Location: index.php");
        exit();
    }

    $daftar = mysqli_query($koneksi, "SELECT * FROM categories");

    $judul = 'Kategori';
    include 'header.php';
?>

<div class="container py-4">
        <h3>Kelola Kategori</h3>
        <?php include 'notifikasi.php'; ?>

        <!--Tambah kategori-->
        <form method="POST" action="backend/proses_kategori.php" class="d-flex gap-2 mb-3">
            <input name="name" class="form-control" placeholder="Kategori baru" required>
            <button type="submit" name="submitInsert" class="btn btn-warning">Tambah</button>
        </form>

        <!-- TAMPILIN FORM KECIL PER BARIS KATEGORI UNTUK EDIT DAN HAPUS KATEGORI TSB-->
        <div class="table-responsive">
            <table class="table align-middle">
                <tbody>
                    <?php while ($k = mysqli_fetch_assoc($daftar)) : ?>
                        <tr>
                            <td>
                                <form method="POST" action="backend/proses_kategori.php" class="d-flex gap-2 align-items-center">
                                    <input type="hidden" name="id" value="<?php echo $k['id']; ?>">
                                    <input name="name" value="<?php echo $k['name']; ?>" class="form-control form-control-sm">
                                    <button type="submit" name="submitEdit" class="btn btn-success btn-sm"><i class="bi bi-pencil-square"></i></button>
                                    <a href="backend/proses_kategori.php?deleteId=<?php echo $k['id']; ?>" onclick="return confirm('Hapus kategori ini?')" class="btn btn-danger btn-sm"><i class="bi bi-trash3-fill"></i></a>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php include 'footer.php'; ?>
</body>

</html>
