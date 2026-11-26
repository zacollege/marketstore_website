<?php
    session_start();
    
    //KALO SUDAH LOGIN, LANGSUNG DIARAHKAN SESUAI ROLE. ADMIN KE DASHBOARD ADMIN, PELANGGAN KE TAMPILAN AWAL/MENU

    if(isset($_SESSION['status']) && $_SESSION['status'] == true) {
        if ($_SESSION['role'] == 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: index.php");
        }
        exit();
    }
    //AMBIL EMAIL DARI COOKIE, KALAU SEBELUMNYA CENTANG INGAT EMAIL
    $email_tersimpan = '';
    if (isset($_COOKIE['remember_email'])) {
        $email_tersimpan = $_COOKIE['remember_email'];
    }
    $tema = 'light';
    if (isset($_COOKIE['tema'])) {
        $tema = $_COOKIE['tema'];
    }
?>

<!doctype html>
<html lang="en" data-bs-theme="<?php echo $tema; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Warung Reza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body class="auth-page login-page p-3">
    <div class="card auth-card login-card shadow">
        <div class="text-center mb-3">
            <h3 class="fw-bold"><i class="bi bi-fork-knife"></i> Warung Reza</h3>
            <p class="text-secondary">Silakan login ke akun Anda</p>
        </div>

        <?php include 'notifikasi.php'; ?>

        <form method="POST" action="backend/proses_login.php">
            <label class="form-label"><i class="bi bi-envelope-at-fill"></i> Email</label>
            <input type="email" class="form-control" name="email" placeholder="nama@email.com" value="<?php echo htmlspecialchars($email_tersimpan); ?>" required>

            <label class="form-label mt-3"><i class="bi bi-lock-fill"></i> Password</label>
            <input type="password" class="form-control" name="password" required>

            <div class="form-check mt-3">
                <input type="checkbox" class="form-check-input" id="remember" name="remember" <?php if ($email_tersimpan != '') { echo 'checked'; } ?>>
                <label class="form-check-label" for="remember">Ingat email saya!</label>
            </div>

            <input type="submit" value="Login" class="btn btn-brand w-100 mt-3">
        </form>

        <div class="text-center mt-3">
            <a href="sign_up.php">Belum punya akun? Signup di sini</a>  |  <a href="index.php">Lihat menu</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>