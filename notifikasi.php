<!-- KUMPULAN  KOTAK NOTIFIKASI -->
<?php
if (isset($_GET['pesan'])) {
        $pesan = $_GET['pesan'];
        $tipe = '';
        $teks = '';

        if ($pesan == 'belum_login') { $tipe = 'warning'; $teks = 'Anda belum login!'; }
        else if ($pesan == 'logout') { $tipe = 'primary'; $teks = 'Anda sudah logout!'; }
        else if ($pesan == 'data_salah') { $tipe = 'danger'; $teks = 'Email atau password salah!'; }
        else if ($pesan == 'akun_dibuat') { $tipe = 'success'; $teks = 'Akun berhasil dibuat, silakan login!'; }
        else if ($pesan == 'email_terpakai') { $tipe = 'warning'; $teks = 'Email sudah terdaftar, gunakan email lain atau login!'; }
        else if ($pesan == 'data_tidak_valid') { $tipe = 'warning'; $teks = 'Data tidak valid, semua kolom wajib diisi!'; }
        else if ($pesan == 'kolom_kosong') { $tipe = 'warning'; $teks = 'Email dan password wajib diisi!'; }
        else if ($pesan == 'signup_kolom_kosong') { $tipe = 'warning'; $teks = 'Nama, email, dan password wajib diisi!'; }
        else if ($pesan == 'login_kolom_kosong') { $tipe = 'warning'; $teks = 'Email dan password wajib diisi!'; }
        else if ($pesan == 'password_pendek') { $tipe = 'warning'; $teks = 'Password minimal 8 karakter!'; }
        else if ($pesan == 'ditambahkan') { $tipe = 'success'; $teks = 'Menu berhasil ditambahkan ke keranjang!'; }
        else if ($pesan == 'keranjang_diperbarui') { $tipe = 'success'; $teks = 'Keranjang berhasil diperbarui!'; }
        else if ($pesan == 'item_dihapus') { $tipe = 'info'; $teks = 'Menu berhasil dihapus dari keranjang!'; }
        else if ($pesan == 'meja_kosong') { $tipe = 'danger'; $teks = 'Nomor meja wajib diisi untuk dine-in'; }
        else if ($pesan == 'stok_kurang') { $tipe = 'danger'; $teks = 'Stok salah satu menu tidak mencukupi'; }
        else if ($pesan == 'stok_habis') { $tipe = 'danger'; $teks = 'Menu sedang habis dan tidak dapat ditambahkan ke keranjang!'; }
        else if ($pesan == 'jumlah_tidak_valid') { $tipe = 'warning'; $teks = 'Jumlah pesanan tidak valid. Periksa kembali jumlah menu di keranjang!'; }
        else if ($pesan == 'item_tidak_ditemukan') { $tipe = 'warning'; $teks = 'Item keranjang tidak ditemukan!'; }
        else if ($pesan == 'keranjang_gagal') { $tipe = 'danger'; $teks = 'Menu gagal ditambahkan ke keranjang. Silakan coba kembali!'; }
        else if ($pesan == 'pembaruan_gagal') { $tipe = 'danger'; $teks = 'Keranjang gagal diperbarui. Silakan coba kembali!'; }
        else if ($pesan == 'data_tidak_valid') { $tipe = 'warning'; $teks = 'Data tidak valid atau proses gagal. Periksa kembali!'; }
        else if ($pesan == 'checkout_gagal') { $tipe = 'danger'; $teks = 'Pesanan gagal diproses. Silakan coba kembali!'; }
        else if ($pesan == 'pesanan_dibuat') { $tipe = 'success'; $teks = 'Pesanan berhasil dibuat'; }
        else if ($pesan == 'pembayaran_berhasil') { $tipe = 'success'; $teks = 'Pembayaran Berhasil!'; }
        else if ($pesan == 'dibatalkan') { $tipe = 'info'; $teks = 'Pesanan berhasil dibatalkan.'; }
        else if ($pesan == 'alasan_wajib') { $tipe = 'danger'; $teks = 'Alasan pembatalan wajib dipilih / diisi!'; }
        else if ($pesan == 'status_diubah') { $tipe = 'success'; $teks = 'Status pesanan diperbarui!'; }
        else if ($pesan == 'tersimpan') { $tipe = 'success'; $teks = 'Data berhasil disimpan'; }
        else if ($pesan == 'dihapus') { $tipe = 'info'; $teks = 'Data berhasil dihapus'; }
        else if ($pesan == 'gagal_dipakai') { $tipe = 'danger'; $teks = 'Tidak bisa dihapus karena masih dipakai data lain'; }

        if ($teks != '') {
            echo '<div class="alert alert-' . $tipe . ' d-flex justify-content-between align-items-center">';
            echo '<span>' . $teks . '</span>';
            echo '</div>';
        }
    }
?>