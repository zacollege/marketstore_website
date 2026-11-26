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

    $kategori = mysqli_query($koneksi, "SELECT * FROM categories");

    // MODE EDIT MENU
    $edit = '';
    if (isset($_GET['edit'])) {
        $id_edit = $_GET['edit'];
        $q_edit = mysqli_query($koneksi, "SELECT * FROM menus WHERE id = $id_edit");
        $edit = mysqli_fetch_assoc($q_edit);
    }

    //KONEKSI DATABASE MENU
    $daftar = mysqli_query($koneksi,
        "SELECT m.*, c.name AS kategori, FORMAT(m.price, 0, 'de_DE') AS harga
         FROM menus m JOIN categories c ON c.id = m.category_id ORDER BY m.id DESC");

    $judul = 'Kelola Menu';
    include 'header.php';

?>

<div class="container py-4">
        <h3>Kelola Menu</h3>
        <?php include 'notifikasi.php'; ?>

        <!-- FORM UNTUK TAMBAH ATAU EDIT MENU MAKANAN/MINUMAN-->
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="backend/proses_menu.php" class="row g-2">
                    <input type="hidden" name="id" value="<?php if ($edit) { echo $edit['id']; } ?>">
                    <div class="col-md-3">
                        <input name="name" class="form-control" placeholder="Nama" required value="<?php if ($edit) { echo $edit['name']; } ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="category_id" class="form-select">
                            <?php while ($k = mysqli_fetch_assoc($kategori)) : ?>
                                <option value="<?php echo $k['id']; ?>" <?php if ($edit && $edit['category_id'] == $k['id']) { echo 'selected'; } ?>><?php echo $k['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="price" class="form-control" placeholder="Harga" required value="<?php if ($edit) { echo $edit['price']; } ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="stock" class="form-control" placeholder="Stok" required value="<?php if ($edit) { echo $edit['stock']; } ?>">
                    </div>

                    <div class="col-md-2">
                        <?php if ($edit) : ?>
                            <button type="submit" class="btn btn-success w-100" name="submitEdit">Simpan</button>
                        <?php else : ?>
                            <button type="submit" class="btn btn-warning w-100" name="submitInsert">Tambah</button>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <input name="image" class="form-control" placeholder="URL foto" value="<?php if ($edit) { echo $edit['image']; } ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <input name="description" class="form-control" placeholder="Deskripsi" value="<?php if ($edit) { echo $edit['description']; } ?>">
                    </div>
                </form>
            </div>
        </div>

        <!-- TABEL -->
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr><th>Menu</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php while ($m = mysqli_fetch_assoc($daftar)) : ?>
                        <tr>
                            <td><?php echo $m['name']; ?></td>
                            <td><?php echo $m['kategori']; ?></td>
                            <td>Rp <?php echo $m['harga']; ?></td>
                            <td><?php echo $m['stock']; ?></td>
                            <td>
                                <a href="admin_menu.php?edit=<?php echo $m['id']; ?>" class="btn btn-success btn-sm"><i class="bi bi-pencil-square"></i></a>
                                <a href="backend/proses_menu.php?deleteId=<?php echo $m['id']; ?>" onclick="return confirm('Hapus menu ini?')" class="btn btn-danger btn-sm"><i class="bi bi-trash3-fill"></i></a>
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

