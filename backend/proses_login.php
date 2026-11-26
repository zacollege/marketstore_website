<?php
    include __DIR__ . '/koneksi.php';
    session_start();

    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $password = isset($_POST['password']) ? htmlspecialchars(trim($_POST['password'])) : '';

    if (!empty($email) && !empty($password)) {
        // CEK AKUN ADA ATAU GA DI DATABASE
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($koneksi, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            // CEK PASSWORD ADA ATAU GA
            if ($password == $row['password']) {
                //KALO SESUAI DAN BERHASIL LOGIN, SIMPAN DI SESSION
                $_SESSION['status'] = true;
                $_SESSION['id'] = $row['id'];
                $_SESSION['nama'] = $row['name'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = $row['role'];

                // COOKIE BUAT FITUR INGAT EMAIL (8 JAM)
                if (isset($_POST['remember'])) {
                    setcookie('remember_email', $row['email'], time() + (3600 * 8), "/");
                } else {
                    setcookie('remember_email', '', time() - 3600, "/");
                }

                // KALO ADMIN SETELAH LOGIN DIARAHKAN KE DASHBOARD ADMIN
                if ($row['role'] == 'admin') {
                    header("Location: ../admin.php");
                //KALO PELANGGAN DIARAHKAN KE BAGIAN MENU/TAMPILAN AWAL
                } else {
                    header("Location: ../index.php");
                }
                exit();
            } 
            //KALO PW GA SESUAI, TETEP DI MENU LOGIN
            else {
                header("Location: ../login.php?pesan=data_salah");
                exit();
            }
        }
        //KALO EMAIL/PW SALAH
        else {
            header("Location: ../login.php?pesan=data_salah");
            exit();
        }
    } else {
        header("Location: ../login.php?pesan=login_kolom_kosong");
        exit();
    }
?>
