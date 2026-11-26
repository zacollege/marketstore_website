<?php
    include 'backend/koneksi.php';
    session_start();

    if (!isset($_SESSION['status']) || $_SESSION['status'] != true) {
        header("Location: login.php?pesan=belum_login");
        exit();
    }
    //CEK DATABASE ROLE ADMIN
    if ($_SESSION['role'] != 'admin') {
        header("Location: index.php");
        exit();
    }

    $dari = '';
    $sampai = '';
    $tanggal = '';
    if (isset($_GET['dari']) && !empty($_GET['dari'])) {
        $dari = htmlspecialchars($_GET['dari']);
        $tanggal = $tanggal . " AND DATE(created_at) >= '$dari'";
    }
    if (isset($_GET['sampai']) && !empty($_GET['sampai'])) {
        $sampai = htmlspecialchars($_GET['sampai']);
        $tanggal = $tanggal . " AND DATE(created_at) <= '$sampai'";
    }

    $q1 = mysqli_query($koneksi,
        "SELECT COUNT(*) AS n, FORMAT(IFNULL(SUM(total), 0), 0, 'de_DE') AS total_rp FROM orders WHERE status <> 'dibatalkan' $tanggal");
    $ringkas = mysqli_fetch_assoc($q1);

    $q2 = mysqli_query($koneksi, "SELECT COUNT(*) AS n FROM orders WHERE DATE(created_at) = CURDATE()");
    $hari_ini = mysqli_fetch_assoc($q2);

    $q3 = mysqli_query($koneksi, "SELECT COUNT(*) AS n FROM menus WHERE stock > 0");
    $tersedia = mysqli_fetch_assoc($q3);

    $q4 = mysqli_query($koneksi, "SELECT COUNT(*) AS n FROM orders WHERE status = 'dibatalkan' $tanggal");
    $batal = mysqli_fetch_assoc($q4);

    // MENAMPILKAN 5 menu terlaris
    $terlaris = mysqli_query($koneksi,
        "SELECT m.name, SUM(d.qty) AS q FROM order_details d
         JOIN orders o ON o.id = d.order_id JOIN menus m ON m.id = d.menu_id
         WHERE o.status <> 'dibatalkan' $tanggal
         GROUP BY m.id ORDER BY q DESC LIMIT 5");

    $judul = 'Dashboard Admin';
    include 'header.php';
?>

<div class="container py-4">
        <div class="d-flex justify-content-between flex-wrap">
            <h3>Dashboard & Laporan</h3>
            <!-- Filter periode -->
            <form class="d-flex gap-2 mb-3">
                <input type="date" name="dari" value="<?php echo $dari; ?>" class="form-control">
                <input type="date" name="sampai" value="<?php echo $sampai; ?>" class="form-control">
                <button class="btn btn-warning">Filter</button>
            </form>
        </div>
        <?php include 'notifikasi.php'; ?>

        <!-- KARTU RINGKASAN -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md">
                <div class="card text-center h-100"><div class="card-body">
                    <div class="text-muted small">Transaksi</div>
                    <div class="fs-5 fw-bold"><?php echo $ringkas['n']; ?></div>
                </div></div>
            </div>
            <div class="col-6 col-md">
                <div class="card text-center h-100"><div class="card-body">
                    <div class="text-muted small">Pendapatan</div>
                    <div class="fs-5 fw-bold">Rp <?php echo $ringkas['total_rp']; ?></div>
                </div></div>
            </div>
            <div class="col-6 col-md">
                <div class="card text-center h-100"><div class="card-body">
                    <div class="text-muted small">Pesanan hari ini</div>
                    <div class="fs-5 fw-bold"><?php echo $hari_ini['n']; ?></div>
                </div></div>
            </div>
            <div class="col-6 col-md">
                <div class="card text-center h-100"><div class="card-body">
                    <div class="text-muted small">Menu tersedia</div>
                    <div class="fs-5 fw-bold"><?php echo $tersedia['n']; ?></div>
                </div></div>
            </div>
            <div class="col-6 col-md">
                <div class="card text-center h-100"><div class="card-body">
                    <div class="text-muted small">Dibatalkan</div>
                    <div class="fs-5 fw-bold"><?php echo $batal['n']; ?></div>
                </div></div>
            </div>
        </div>

        <!-- MENU TERLARIS -->
        <div class="card"><div class="card-body">
            <h6>Menu Terlaris</h6>
            <ol>
                <?php while ($t = mysqli_fetch_assoc($terlaris)) : ?>
                    <li><?php echo $t['name']; ?> <span class="badge bg-warning text-dark"><?php echo $t['q']; ?> porsi</span></li>
                <?php endwhile; ?>
            </ol>
        </div></div>
    </div>

<?php include 'footer.php'; ?>
</body>

</html>