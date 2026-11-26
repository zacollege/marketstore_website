<?php
    include 'backend/koneksi.php';
    session_start();

    //FILTER KATEGORI
    $kat = 0;
    if (isset($_GET['kat'])) {
        $kat = $_GET['kat'];
    }

    $kondisi = '';
        if ($kat != 0) {
        $kondisi = "WHERE m.category_id = " . intval($kat);
    }

    $kategori = mysqli_query($koneksi, "SELECT * FROM categories");

    //AMBIL MENU DAN NAMA KATEGORI
    //HARGA DI BERI FORMAT AGAR ADA KOMA2NYA
    $menu = mysqli_query($koneksi,
        "SELECT m.*, c.name AS kategori, FORMAT(m.price, 0, 'de_DE') AS harga
         FROM menus m JOIN categories c ON c.id = m.category_id
         $kondisi ORDER BY m.name");

    $judul = 'Menu';
    include 'header.php';
?>
<div class="container py-4">
    <?php include 'notifikasi.php'; ?>
    
    <!-- BANNER ATAS CARD MENU-->
     <div class="hero rounded-3 p-5 mb-4">
        <h1 class="fw-bold">Selamat Datang di Warung Reza</h1>
            <p class="mb-0">
                Pilih menu, pesan dine-in atau takeaway, dan pantau status pesananmu.
            </p>
    </div>

        <!--KOTAK SEARCH/CARI MENU-->
         <div class="row g-2 mb-3 align-items-center">
            <div class="col-md-8"><input id="search" class="form-control" placeholder="Cari menu..."></div>
        <!-- KOTAK UNTUK PILIHAN FILTER PENCARIAN MENU 
         cari berdasar nama: javascript
         cari berdasar kategori : link-->
        <div class="col-md-4">
                <div class="btn-group w-100">
                    <a class="btn btn-outline-secondary <?php if ($kat == 0) { echo 'active'; } ?>" href="index.php">Semua</a>
                    <?php while ($k = mysqli_fetch_assoc($kategori)) : ?>
                        <a class="btn btn-outline-secondary <?php if ($kat == $k['id']) { echo 'active'; } ?>"
                            href="?kat=<?php echo $k['id']; ?>"><?php echo $k['name']; ?></a>
                    <?php endwhile; ?>
                    </div>
            </div>
        </div>

        <div class="row g-3">
            <?php while ($row = mysqli_fetch_assoc($menu)) : ?>
                <!--CARD MENU-->
                <div class="col-6 col-md-4 col-lg-3 item" data-name="<?php echo $row['name']; ?>">
                    <div class="card h-100 shadow-sm">
                        <img src="<?php echo $row['image']; ?>" class="card-img-top" alt="<?php echo $row['name']; ?>">
                        <div class="card-body d-flex flex-column">
                            <small class="text-muted"><?php echo $row['kategori']; ?></small>
                            <h6><?php echo $row['name']; ?></h6>
                            <p class="small text-muted flex-grow-1"><?php echo $row['description']; ?></p>
                            <div class="fw-bold text-warning">Rp <?php echo $row['harga']; ?></div>

                            <?php if ($row['stock'] <= 0) : ?>
                                <span class="badge bg-danger mt-2">Habis</span>

                            <?php elseif (!isset($_SESSION['status'])) : ?>
                                <!-- TOMBOL PESAN KALAU BELUM LOGIN  -->
                                <a href="login.php?pesan=belum_login" class="btn btn-sm btn-warning mt-2">Login untuk Memesan</a>

                            <?php elseif ($role == 'admin') : ?>
                                <!-- KALAU ADMIN PENCET TOMBOL PESAN, MAKA AKAN MUNCUL POP-UP ADMIN TIDAK BISA PESAN-->
                                <button type="button" class="btn btn-sm btn-warning mt-2" data-bs-toggle="modal" data-bs-target="#modalAdmin">Pesan<i class="bi bi-arrow-bar-down"></i></button>

                            <?php else : ?>
                                <button type="button" class="btn btn-sm btn-warning mt-2" data-bs-toggle="collapse"
                                    data-bs-target="#order-<?php echo $row['id']; ?>">Pesan<i class="bi bi-arrow-bar-down"></i></button>

                                <!-- DROPDOWN PESAN jika pencet tombol pesan setelah login
                                 berisi jumlah pemesanan (- +) dan catatan menu -->
                                <div class="collapse mt-2" id="order-<?php echo $row['id']; ?>">
                                    <form method="POST" action="backend/proses_keranjang.php">
                                        <input type="hidden" name="aksi" value="tambah">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <div class="input-group input-group-sm mb-2">
                                            <button type="button" class="btn btn-outline-secondary" data-step="-1">-</button>
                                            <input type="number" name="qty" class="form-control text-center qty-input"
                                                value="1" min="1" max="<?php echo $row['stock']; ?>">
                                            <button type="button" class="btn btn-outline-secondary" data-step="1">+</button>
                                        </div>
                                        <input name="note" class="form-control form-control-sm mb-2" maxlength="150"
                                            placeholder="Catatan (mis. tanpa cabai)">
                                        <button class="btn btn-sm btn-warning w-100">Tambah ke Keranjang</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- POP-UP ADMIN JIKA PENCET TOMBOL PESAN -->
    <div class="modal fade" id="modalAdmin" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Akses Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Akun ini memiliki hak akses sebagai <b>admin</b> dan tidak dapat melakukan pemesanan sebagai pelanggan.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Mengerti</button>
                </div>
            </div>
        </div>
    </div>

<?php include 'footer.php'; ?>
    <script>
        // PENCARIAN MENU. CARD MENU DISEMBUNYIKAN KALAU TIDAK MENGANDUNG KATA KUNCI YANG DIMASUKKAN.
        const search = document.getElementById('search');
        search.addEventListener('input', function () {
            const kata = search.value.toLowerCase();
            document.querySelectorAll('.item').forEach(function (kartu) {
                const nama = kartu.dataset.name.toLowerCase();
                if (nama.includes(kata)) {
                    kartu.style.display = '';
                } else {
                    kartu.style.display = 'none';
                }
            });
        });

        //TOMBOL UNTUK JUMLAH PESANAN (- +) DI TIAP DROPDOWN CARD MENU
        document.addEventListener('click', function (event) {
            const tombol = event.target.closest('[data-step]');
            if (!tombol) { return; }
            const input = tombol.parentElement.querySelector('.qty-input');
            let baru = Number(input.value) + Number(tombol.dataset.step);
            if (baru < 1) { baru = 1; }
            if (baru > Number(input.max)) { baru = Number(input.max); }
            input.value = baru;
        });

        // JIKA ADMIN MEMAKSA MENGAKSES keranjang.php, muncul pop-up admin ga bisa memesan. sama kyk kalau pencet tombol pesan 
        <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'admin_tidak_bisa_pesan') : ?>
        new bootstrap.Modal(document.getElementById('modalAdmin')).show();
        <?php endif; ?>
    </script>
</body>

</html>