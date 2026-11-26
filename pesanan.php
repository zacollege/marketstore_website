<?php
    include 'backend/koneksi.php';
    session_start();
    
    //KALAU BELUM LOGIN, DIARAHKAN KE LOGIN
    if (!isset($_SESSION['status']) || $_SESSION['status'] != true) {
        header("Location: login.php?pesan=belum_login");
        exit();
    }
    //KALAU LOGIN ROLE ADMIN, MUNCUL POP UP ADMIN GA BISA PESAN
    if ($_SESSION['role'] == 'admin') {
        header("Location: index.php?pesan=admin_tidak_bisa_pesan");
        exit();
    }
    $id_user = $_SESSION['id'];

    //  LPAD(id,5,'0')  -> kode 5 digit (1 jadi 00001)
    //  FORMAT(...)     -> angka bertitik (25000 jadi 25.000)

    //DATE_FORMAT buat format tanggal dan jam menu dipesan
    //GROUP_CONCAT buat menggabungkan menu yang dipesan. Nanti jadi satu pas di bagian pop-up batal
    $pesanan = mysqli_query($koneksi,
        "SELECT o.*, LPAD(o.id, 5, '0') AS kode, FORMAT(o.total, 0, 'de_DE') AS total_rp,
                DATE_FORMAT(o.created_at, '%d-%m-%Y %H:%i') AS waktu,
                (SELECT GROUP_CONCAT(m.name SEPARATOR ', ') FROM order_details d JOIN menus m ON m.id = d.menu_id WHERE d.order_id = o.id) AS nama_menu
         FROM orders o WHERE o.user_id = $id_user ORDER BY o.id DESC");

    $judul = 'Pesanan Saya';
    include 'header.php';
?>
    <div class="container py-4">
        <h3>Pesanan Saya</h3>
        <?php include 'notifikasi.php'; ?>
        <!-- kalo blm ada pesanan -->
        <?php if (mysqli_num_rows($pesanan) == 0) : ?>
            <p class="text-muted">Belum ada pesanan.</p>
        <?php endif; ?>

        <?php while ($o = mysqli_fetch_assoc($pesanan)) : ?>
            <?php
                //PESANAN YANG DIORDER
                $detail = mysqli_query($koneksi,
                    "SELECT d.qty, d.note, m.name FROM order_details d JOIN menus m ON m.id = d.menu_id WHERE d.order_id = " . $o['id']);

                // UNTUK STATUS PESANAN
                $warna = 'warning';
                if ($o['status'] == 'dibatalkan') { $warna = 'danger'; }
                if ($o['status'] == 'selesai') { $warna = 'success'; }
                $persen = 25;
                if ($o['status'] == 'diproses') { $persen = 50; }
                if ($o['status'] == 'siap') { $persen = 75; }
                if ($o['status'] == 'selesai') { $persen = 100; }

                //STATUS PEMBAYARAN
                $warna_bayar = 'secondary';
                if ($o['payment_status'] == 'Dibayar') { $warna_bayar = 'success'; }
                if ($o['payment_status'] == 'Menunggu Pembayaran') { $warna_bayar = 'warning'; }
            ?>

            <!--CARD PESANAN-->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between flex-wrap gap-1">
                    <span>
                        <b>Kode Pemesanan: <?php echo $o['kode']; ?></b> · <?php echo $o['type']; ?>
                        <?php if ($o['table_no'] != '') { echo '· Meja ' . $o['table_no']; } ?>
                        · <?php echo $o['waktu']; ?>
                    </span>
                    <span>
                        <span class="badge bg-<?php echo $warna_bayar; ?>"><?php echo $o['payment_status']; ?></span>
                        <span class="badge bg-<?php echo $warna; ?>"><?php echo $o['status']; ?></span>
                    </span>
                </div>
                <div class="card-body">
                    <?php if ($o['status'] != 'dibatalkan') : ?>
                        <div class="progress mb-3" style="height:8px">
                            <div class="progress-bar bg-warning" style="width:<?php echo $persen; ?>%"></div>
                        </div>
                    <?php endif; ?>

                    <?php while ($x = mysqli_fetch_assoc($detail)) : ?>
                        <div class="border-bottom py-2">
                            <?php echo $x['name']; ?> x <?php echo $x['qty']; ?> <small class="text-muted"><?php echo $x['note']; ?></small>
                        </div>
                    <?php endwhile; ?>

                    <div class="d-flex justify-content-between align-items-end mt-3 flex-wrap gap-2">
                        <!-- BARCODE PESANAN (CUMA GAMBAR)-->
                        <div>
                            <div class="small text-muted">Tunjukkan ke kasir</div>
                            <img src="https://img.magnific.com/free-psd/barcode-illustration-isolated_23-2150584086.jpg?semt=ais_hybrid&w=740&q=80" alt="Barcode" height="50" class="bg-white p-1 rounded">
                            <div><?php echo $o['kode']; ?></div>
                        </div>
                        <div class="text-end">
                            <b class="fs-5">Total: Rp <?php echo $o['total_rp']; ?></b>
                        </div>
                    </div>

                    <?php if ($o['status'] == 'dibatalkan') : ?>
                        <!-- INFORMASI PEMBATALAN KALAU DIBATALKAN-->
                        <div class="alert alert-danger mt-3 mb-0 small">
                            Dibatalkan oleh <?php echo $o['cancelled_by']; ?>. Alasan: <?php echo $o['cancel_reason']; ?>
                            <?php if ($o['refund_status'] != '-') : ?>
                                <br><b><?php echo $o['refund_status']; ?></b>
                            <?php endif; ?>
                        </div>
                    <?php elseif ($o['status'] == 'baru') : ?>
                        <div class="mt-3">
                            
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalBatal"
                                data-id="<?php echo $o['id']; ?>" data-menu="<?php echo $o['nama_menu']; ?>"
                                data-dibayar="<?php if ($o['payment_status'] == 'Dibayar') { echo 1; } else { echo 0; } ?>">Batalkan Pesanan</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

<?php include 'footer.php'; ?>
<?php include 'modal_batal.php'; ?>
</body>

</html>
