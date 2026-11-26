<?php
    session_start();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Signup - Warung Reza</title>
  </head>
    <body class="auth-page login-page p-3">
        <div class="card auth-card login-card shadow">
            <div class="text-center mb-3">
                <h3 class="fw-bold">Buat Akun Warung Reza</h3>
            </div>

            <?php include 'notifikasi.php'; ?>

            <form method="POST" action="backend/proses_signup.php">
                <label class="form-label"><i class="bi bi-person-fill"></i> Nama</label>
                <input type="text" class="form-control" name="name" required>

                <label class="form-label mt-3"><i class="bi bi-envelope-at-fill"></i> Email</label>
                <input type="email" class="form-control" name="email" placeholder="nama@email.com" required>

                <label class="form-label mt-3"><i class="bi bi-lock-fill"></i> Password</label>
                <input type="password" class="form-control" name="password" minlength="8" placeholder="Minimal 8 karakter" required>

                <input type="submit" value="Signup" class="btn btn-brand w-100 mt-3">
            </form>

            <div class="text-center mt-3"><a href="login.php">Sudah punya akun? Login</a></div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>