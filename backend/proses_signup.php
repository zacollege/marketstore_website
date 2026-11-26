<?php
    include __DIR__ . '/koneksi.php';

    $nama = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $password_asli = isset($_POST['password']) ? trim($_POST['password']) : '';
    $password = htmlspecialchars($password_asli);

    
    //CEK KALO GA DI ISI
    if (empty($nama) || empty($email) || empty($password)) {
        header("Location: ../sign_up.php?pesan=data_tidak_valid");
        exit();
    }
    //MEMASTIKAN PASSWORD MINIMAL 8 KARAKTER
    if (strlen($password_asli) < 8) {
        header("Location: ../sign_up.php?pesan=password_pendek");
        exit();
    }
    //MENGHINDARI EMAIL YANG SAMA SIGN UP LAGI, JADI EMAIL YANG SAMA TIDAK BISA TERPAKAI LAGI
    $cek_query = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$email'");

    if (mysqli_num_rows($cek_query) > 0) {
        header("Location: ../sign_up.php?pesan=email_terpakai");
        exit();
    } else {
        // AKUN BARU DIBUAT ROLENYA HANYA PELANGGAN, ADMIN CUMA SATU
        $query = mysqli_query($koneksi,
            "INSERT INTO users (name, email, password, role) VALUES ('$nama', '$email', '$password', 'pelanggan')");

        //AKUN BERHASIL DIDAFTARKAN, LANJUT LOGIN 
        if ($query) {
            header("Location: ../login.php?pesan=akun_dibuat");
        }
    }
?>