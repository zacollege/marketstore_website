<?php
    include 'backend/koneksi.php';
    session_start();
    
    //MASTIIN SUDAH LOGIN
    if (!isset($_SESSION['status']) || $_SESSION['status'] != true) {
        header("Location: login.php?pesan=belum_login");
        exit();
    }
    //MELARANG ADMIN AKSES
    if ($_SESSION['role'] == 'admin') {
        header("Location: index.php?pesan=admin_tidak_bisa_pesan");
        exit();
    }
    $id_user = $_SESSION['id'];

    //ISI KERANJANG
    //FORMATIN HARGA AGAR ADA KOMANYA
    $keranjang = mysqli_query($koneksi,
        "SELECT k.*, m.name, FORMAT(m.price, 0, 'de_DE') AS harga, FORMAT(m.price * k.qty, 0, 'de_DE') AS subtotal
         FROM cart k JOIN menus m ON m.id = k.menu_id WHERE k.user_id = $id_user");

    // TOTAL HARGA
    $q_total = mysqli_query($koneksi,
        "SELECT FORMAT(IFNULL(SUM(m.price * k.qty), 0), 0, 'de_DE') AS total_rp
         FROM cart k JOIN menus m ON m.id = k.menu_id WHERE k.user_id = $id_user");
    $data_total = mysqli_fetch_assoc($q_total);

    //DEFAULT FORM.)
    $f_type = isset($_GET['type']) ? $_GET['type'] : 'dine-in';
    $f_meja = isset($_GET['meja']) ? $_GET['meja'] : '';
    $f_bayar = isset($_GET['payment']) ? $_GET['payment'] : 'Tunai';

    //PEMBAYARAN
    $bayar = '';
    if (isset($_GET['bayar'])) {
        $id_bayar = $_GET['bayar'];
        $q_bayar = mysqli_query($koneksi,
            "SELECT *, LPAD(id, 5, '0') AS kode, FORMAT(total, 0, 'de_DE') AS total_rp FROM orders
             WHERE id = $id_bayar AND user_id = $id_user AND payment_status = 'Menunggu Pembayaran' AND status <> 'dibatalkan'");
        $bayar = mysqli_fetch_assoc($q_bayar);
    }

    $judul = 'Keranjang';
    include 'header.php';
?>
    <div class="container py-4">
        <h3>Keranjang</h3>
        <?php include 'notifikasi.php'; ?>

        <?php if (mysqli_num_rows($keranjang) == 0) : ?>
            <p class="text-muted">Keranjang kosong.</p>
            <a href="index.php" class="btn btn-warning">Lihat Menu</a>
        <?php else : ?>

            <!-- TABEL KERANJANG (tiap baris punya form kecil untuk mengubah jumlah) -->
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr><th>Menu</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($keranjang)) : ?>
                            <tr>
                                <td>
                                    <?php echo $row['name']; ?>
                                    <div class="small text-muted"><?php echo $row['note']; ?></div>
                                </td>
                                <td>Rp <?php echo $row['harga']; ?></td>
                                <td>
                                    <form method="POST" action="backend/proses_keranjang.php" class="d-flex gap-1 form-perbarui" onsubmit="return konfirmasiPerbarui(this);">
                                        <input type="hidden" name="aksi" value="perbarui">
                                        <input type="hidden" name="id_cart" value="<?php echo $row['id']; ?>">
                                        <input type="number" min="0" step="1" required name="qty" value="<?php echo $row['qty']; ?>" data-qty-awal="<?php echo (int)$row['qty']; ?>" class="form-control form-control-sm qty-keranjang" style="width:80px">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" disabled>Perbarui</button>
                                    </form>
                                </td>
                                <td>Rp <?php echo $row['subtotal']; ?></td>
                                <td>
                                    <!-- Tombol bertulisan "Batal" (bukan simbol X) -->
                                    <a href="backend/proses_keranjang.php?aksi=hapus&id_cart=<?php echo $row['id']; ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Batalkan menu ini dari keranjang?')">Batal</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr><th colspan="3" class="text-end">Total</th><th>Rp <?php echo $data_total['total_rp']; ?></th><th></th></tr>
                    </tfoot>
                </table>
            </div>
            <a href="index.php" class="btn btn-outline-warning">+ Tambah Pesanan</a>

            <hr>

            <!-- FORM CHECKOUT -->
            <form method="POST" action="backend/proses_checkout.php" id="formCheckout" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" id="checkout">Jenis Pesanan</label>
                    <select name="type" id="orderType" class="form-select">
                        <option value="dine-in" <?php if ($f_type == 'dine-in') { echo 'selected'; } ?>>Dine-in</option>
                        <option value="takeaway" <?php if ($f_type == 'takeaway') { echo 'selected'; } ?>>Takeaway</option>
                    </select>
                </div>
                <div class="col-md-4" id="tableBox">
                    <label class="form-label">No. Meja</label>
                    <input type="number" min="1" name="table_no" class="form-control" value="<?php echo htmlspecialchars($f_meja); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pembayaran</label>
                    <select name="payment" class="form-select">
                        <option <?php if ($f_bayar == 'Tunai') { echo 'selected'; } ?>>Tunai</option>
                        <option <?php if ($f_bayar == 'QRIS') { echo 'selected'; } ?>>QRIS</option>
                        <option <?php if ($f_bayar == 'Transfer') { echo 'selected'; } ?>>Transfer</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalVerifikasi">
                        Konfirmasi Pesanan
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <!--POP-UP VERIFIKASI -->
    <div class="modal fade" id="modalVerifikasi" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verifikasi Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda sudah yakin untuk membeli, atau masih ingin menambahkan menu lainnya?
                </div>
                <div class="modal-footer">
                    <a href="index.php" class="btn btn-outline-secondary">Tambah Menu Lain</a>
                    <!-- atribut form="formCheckout" membuat tombol ini tetap mengirim form checkout di atas -->
                    <button type="submit" form="formCheckout" class="btn btn-warning">Ya, Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- POP-UP PEMBAYARAN -->
    <?php if ($bayar) : ?>
        <div class="modal fade" id="modalBayar" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pembayaran <?php echo $bayar['payment']; ?></h5>
                    </div>
                    <div class="modal-body text-center">
                        <div class="text-muted">Kode Pemesanan <b><?php echo $bayar['kode']; ?></b></div>
                        <h4 class="my-2">Total Rp <?php echo $bayar['total_rp']; ?></h4>

                        <?php if ($bayar['payment'] == 'QRIS') : ?>
                            <p class="mb-2">Scan QR Code berikut</p>
                            <!-- Gambar QR: ganti file gambar/qris.png jika ingin memakai gambar lain -->
                            <img src="qris.png" alt="QRIS" width="200">
                        <?php else : ?>
                            <p class="mb-2">Transfer M-Banking ke Virtual Account</p>
                            <div class="fs-3 fw-bold">8808 0812 <?php echo $bayar['kode']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <form method="POST" action="backend/proses_bayar.php" class="w-100">
                            <input type="hidden" name="id" value="<?php echo $bayar['id']; ?>">
                            <button class="btn btn-success w-100">Selesai</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

<?php include 'footer.php'; ?>
    <script>
        // Tombol Perbarui
        document.querySelectorAll('.form-perbarui').forEach(function (form) {
            const inputQty = form.querySelector('.qty-keranjang');
            const tombolPerbarui = form.querySelector('button[type="submit"]');
            const qtyAwal = Number(inputQty.dataset.qtyAwal);

            function cekPerubahanQty() {
                if (inputQty.value === '' || Number(inputQty.value) !== qtyAwal) {
                    tombolPerbarui.classList.remove('btn-outline-secondary');
                    tombolPerbarui.classList.add('btn-success');
                    tombolPerbarui.disabled = false;
                } else {
                    tombolPerbarui.classList.remove('btn-success');
                    tombolPerbarui.classList.add('btn-outline-secondary');
                    tombolPerbarui.disabled = true;
                }
            }

            inputQty.addEventListener('input', cekPerubahanQty);
        });

        // Jumlah 0 (hapus)
        function konfirmasiPerbarui(form) {
            const inputQty = form.querySelector('.qty-keranjang');
            if (Number(inputQty.value) === 0) {
                if (!confirm('Batalkan menu ini dari keranjang?')) {
                    inputQty.value = inputQty.dataset.qtyAwal;
                    inputQty.dispatchEvent(new Event('input'));
                    return false;
                }
            }
            return true;
        }

        // Kolom nomor meja 
        const orderType = document.getElementById('orderType');
        function aturMeja() {
            if (orderType) {
                if (orderType.value == 'dine-in') {
                    document.getElementById('tableBox').style.display = '';
                } else {
                    document.getElementById('tableBox').style.display = 'none';
                }
            }
        }
        if (orderType) {
            orderType.addEventListener('change', aturMeja);
        }
        aturMeja();

        // pop-up pembayaran otomatis
        <?php if ($bayar) : ?>
        new bootstrap.Modal(document.getElementById('modalBayar')).show();
        <?php endif; ?>
    </script>
</body>

</html>