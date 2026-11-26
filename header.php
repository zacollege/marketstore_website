<!-- BUAT NAVBAR TIAP MENU/HALAMAN -->

<?php
    //buat ambil role user (admin atau pelanggan)
    //dibuat tema default light mode
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
    $tema = isset($_COOKIE['tema']) ? $_COOKIE['tema'] : 'light';

    //jumlah item di keranjang
    $jumlah_keranjang = 0;
    if (isset($koneksi) && isset($_SESSION['id'])) {
        $id_user_nav = (int)$_SESSION['id'];
        $q_nav_cart = mysqli_query($koneksi, "SELECT IFNULL(SUM(qty), 0) AS total_qty FROM cart WHERE user_id = $id_user_nav");
        if ($q_nav_cart && $row_nav_cart = mysqli_fetch_assoc($q_nav_cart)) {
            $jumlah_keranjang = (int)$row_nav_cart['total_qty'];
        }
    } elseif (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $jumlah_keranjang = $jumlah_keranjang + $item['qty'];
        }
    }
?>
<!doctype html>
<html lang="en" data-bs-theme="<?php echo $tema; ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
    <title><?php echo $judul; ?> - Warung Reza</title>
  </head>

  <body>
    <nav class="navbar navbar-expand-md" id="main-navbar">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-fork-knife"></i> Warung Reza
            </a>
                
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">

                    <!-- Tampil Menu Navbar -->
                    <li class="nav-item"><a class="nav-link" href="index.php">Menu</a></li>

                   <!-- Menu Navbar Pelanggan -->
                    <?php if ($role == 'pelanggan') : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="keranjang.php">Keranjang <span class="badge bg-warning text-dark"><?php echo $jumlah_keranjang; ?></span></a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="pesanan.php">Pesanan Saya</a></li>
                    <?php endif; ?>

                    <!-- Menu navbar Admint -->
                    <?php if ($role == 'admin') : ?>
                        <li class="nav-item"><a class="nav-link" href="admin.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_pesanan.php">Pesanan</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_menu.php">Kelola Menu</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_kategori.php">Kategori</a></li>
                    <?php endif; ?>
                </ul>

                <div class="d-flex align-items-center flex-wrap gap-2 my-2 my-md-0">
                    <!-- Toggle dan icon untuk light/dark mode -->
                    <div class="form-check form-switch me-2 mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" id="temaSwitch" <?php if ($tema == 'dark') { echo 'checked'; } ?>>
                        <label class="form-check-label" for="temaSwitch">
                            <i id="temaIcon" class="bi <?php if ($tema == 'dark') { echo 'bi-sun-fill'; } else { echo 'bi-moon-stars-fill'; } ?>"></i>
                        </label>
                    </div>

                    <!-- Nama User, Tombol Signup dan Login -->
                    <?php if (isset($_SESSION['status'])) : ?>
                        <span class="me-2 small"><?php echo htmlspecialchars($_SESSION['nama']); ?> (<?php echo $role; ?>)</span>
                        <a href="backend/logout.php" class="btn btn-danger btn-sm" id="logout-button">Logout</a>
                    <?php else : ?>
                        <a href="login.php" class="btn btn-light btn-sm">Login</a>
                        <a href="sign_up.php" class="btn btn-warning btn-sm">Signup</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>