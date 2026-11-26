<?php
    include 'backend/koneksi.php';
    session_start();

    // HALAMAN TERAKSES KALAU SUDAH LOGIN DAN ROLE:ADMIN
    if (!isset($_SESSION['status']) || $_SESSION['status'] != true) {
        header("Location: login.php?pesan=belum_login");
        exit();
    }
    if ($_SESSION['role'] != 'admin') {
        header("Location: index.php");
        exit();
    }

    // UNTUK PESANAN YANG MASIH DALAM ANTREAN, MENAMPILKAN SEMUA MENU2 YANG DIPESAN, STATUS, DSB
    $aktif = mysqli_query($koneksi,
        "SELECT o.*, u.name AS nama, LPAD(o.id, 5, '0') AS kode, DATE_FORMAT(o.created_at, '%H:%i') AS jam,
                (SELECT GROUP_CONCAT(m.name SEPARATOR ', ') FROM order_details d JOIN menus m ON m.id = d.menu_id WHERE d.order_id = o.id) AS nama_menu
         FROM orders o JOIN users u ON u.id = o.user_id
         WHERE o.status IN ('baru','diproses','siap') ORDER BY o.created_at"
    );

    //MENAMPILKAN RIWATAT PESANAN YANG SUDAH SELESAI ATAU DIBATALKAN
    $riwayat = mysqli_query($koneksi,
        "SELECT o.*, u.name AS nama, LPAD(o.id, 5, '0') AS kode, FORMAT(o.total, 0, 'de_DE') AS total_rp
         FROM orders o JOIN users u ON u.id = o.user_id
         WHERE o.status IN ('selesai','dibatalkan') ORDER BY o.id DESC LIMIT 20");

    $judul = 'Pesanan Masuk';
    include 'header.php';


?>

<div class="container py-4">
        <div class="d-flex justify-content-between">
            <h3>Antrean Pesanan</h3>
            <a href="admin_pesanan.php" class="btn btn-outline-secondary btn-sm align-self-center">Muat ulang</a>
        </div>
        <?php include 'notifikasi.php'; ?>

        <!-- KALO BELUM ADA PESANAN MASUK -->
        <?php if (mysqli_num_rows($aktif) == 0) : ?>
            <p class="text-muted">Tidak ada pesanan aktif.</p>
        <?php endif; ?>

        <div class="row g-3 mb-5">
            <?php while ($o = mysqli_fetch_assoc($aktif)) : ?>
                <?php
                    //HUBUNGKAN DATABASE ORDERAN
                    $detail = mysqli_query($koneksi,
                        "SELECT d.qty, d.note, m.name FROM order_details d JOIN menus m ON m.id = d.menu_id WHERE d.order_id = " . $o['id']);

                    // Untuk tulisan tombol status berikutnya (proses dan siap)
                    $label = '';
                    if ($o['status'] == 'baru') { $label = 'Proses'; }
                    if ($o['status'] == 'diproses') { $label = 'Siap'; }

                    //status bayar
                    $warna_bayar = 'secondary';
                    if ($o['payment_status'] == 'Dibayar') { $warna_bayar = 'success'; }
                    if ($o['payment_status'] == 'Menunggu Pembayaran') { $warna_bayar = 'warning'; }
                ?>

                <!-- CARD ANTREAN PESANAN BERISI DATA2 LENGKAP PESANAN -->
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between">
                            <b>#<?php echo $o['kode']; ?> <?php echo $o['nama']; ?></b>
                            <span class="badge bg-warning"><?php echo $o['status']; ?></span>
                        </div>
                        <div class="card-body">
                            <div class="small text-muted mb-2">
                                <?php echo $o['type']; ?><?php if ($o['table_no'] != '') { echo ' · Meja ' . $o['table_no']; } ?>
                                · <?php echo $o['payment']; ?> · <?php echo $o['jam']; ?><br>
                                <span class="badge bg-<?php echo $warna_bayar; ?>"><?php echo $o['payment_status']; ?></span>
                            </div>
                            <ul class="list-unstyled">
                                <?php while ($x = mysqli_fetch_assoc($detail)) : ?>
                                    <li><?php echo $x['qty']; ?>× <?php echo $x['name']; ?>
                                        <?php if ($x['note'] != '') : ?><em class="text-danger">(<?php echo $x['note']; ?>)</em><?php endif; ?></li>
                                <?php endwhile; ?>
                            </ul>
                            <div class="d-flex gap-2 flex-wrap align-items-center">
                                <?php if ($o['status'] == 'siap') : ?>
                                    <!-- BUTTON SELESAIKAN PESANAN-->
                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalSelesai"
                                        data-id="<?php echo $o['id']; ?>" data-kode="<?php echo $o['kode']; ?>">Selesai</button>
                                <?php else : ?>
                                    <form method="POST" action="backend/proses_status.php" class="m-0">
                                        <input type="hidden" name="id" value="<?php echo $o['id']; ?>">
                                        <button class="btn btn-sm btn-warning">→ <?php echo $label; ?></button>
                                    </form>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalBatal"
                                    data-id="<?php echo $o['id']; ?>" data-menu="<?php echo $o['nama_menu']; ?>"
                                    data-dibayar="<?php if ($o['payment_status'] == 'Dibayar') { echo 1; } else { echo 0; } ?>">Batalkan</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- CARD ISI RIWAYAT PESANAN (YANG SUDAH SELESAI ATAUPUN BATAL, KALO BATAL ADA TAMPIL ALASANNYA DAN STATUS REFUND) -->
        <h5>Riwayat Pesanan</h5>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <tr><th>Kode</th><th>Pelanggan</th><th>Total</th><th>Status</th><th>Alasan (oleh)</th><th>Refund</th></tr>
                <?php while ($o = mysqli_fetch_assoc($riwayat)) : ?>
                    <tr>
                        <td><?php echo $o['kode']; ?></td>
                        <td><?php echo $o['nama']; ?></td>
                        <td>Rp <?php echo $o['total_rp']; ?></td>
                        <td><?php echo $o['status']; ?></td>
                        <td>
                            <?php if ($o['cancel_reason'] != '') { echo $o['cancel_reason'] . ' (' . $o['cancelled_by'] . ')'; } else { echo '-'; } ?>
                        </td>
                        <td><?php echo $o['refund_status']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

    <!-- POP UP KONFIRMASI UNTUK SELESAIKAN PESANAN ATAU TIDAK JADI-->
    <div class="modal fade" id="modalSelesai" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="backend/proses_status.php" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Selesaikan Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="selesaiId">
                    Yakin ingin menyelesaikan pesanan <b id="selesaiKode"></b>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                    <button class="btn btn-success">Ya, Selesaikan</button>
                </div>
            </form>
        </div>
    </div>

<?php include 'footer.php'; ?>
<?php include 'modal_batal.php'; ?>
    <script>
        // Isi pop-up "Selesai" dengan data pesanan dari tombol yang diklik
            document.getElementById('modalSelesai').addEventListener('show.bs.modal', function (event) {
            document.getElementById('selesaiId').value = event.relatedTarget.dataset.id;
            document.getElementById('selesaiKode').textContent = event.relatedTarget.dataset.kode;
        });
    </script>
</body>

</html>