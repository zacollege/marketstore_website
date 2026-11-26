<!-- FILE UNTUK TAMPILKAN PESAN POP UP PEMBATALAN PESANAN YANG BERISI OPSI2 ALASAN PEMBATALAN DAN KONFIRMASI PEMBATALAN
DIPAKE DI pesanan.php dan admin_pesanan.php
nantinya diproses di proses_batal.php -->


<div class="modal fade" id="modalBatal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="backend/proses_batal.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Batalkan Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="batalId">
                <p>Apakah Anda yakin ingin membatalkan pesanan? <b id="batalMenu"></b>?</p>

                <!-- OPSI ALASAN PEMBATALAN PESANAN-->
                <label class="form-label">Alasan pembatalan <span class="text-danger">*</span></label>
                <select name="alasan" id="batalAlasan" class="form-select mb-2">
                    <option value="">-- Pilih alasan --</option>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') : ?>
                        <option value="Stok produk habis.">Stok produk habis.</option>
                        <option value="Bahan baku tidak tersedia.">Bahan baku tidak tersedia.</option>
                        <option value="Warung sedang tutup atau tidak beroperasi.">Warung sedang tutup atau tidak beroperasi.</option>
                        <option value="Warung sedang mengalami kendala operasional.">Warung sedang mengalami kendala operasional.</option>
                        <option value="Pesanan terlalu banyak, sehingga tidak dapat diproses.">Pesanan terlalu banyak, sehingga tidak dapat diproses.</option>
                        <option value="Terjadi kesalahan pada harga atau informasi produk.">Terjadi kesalahan pada harga atau informasi produk.</option>
                        <option value="Menu yang dipesan sedang tidak dapat dibuat.">Menu yang dipesan sedang tidak dapat dibuat.</option>
                        <option value="Kendala teknis pada sistem pemesanan.">Kendala teknis pada sistem pemesanan.</option>
                    <?php else : ?>
                        <option value="Salah memilih menu">Salah memilih menu</option>
                        <option value="Ingin mengubah pesanan">Ingin mengubah pesanan</option>
                        <option value="Terlalu lama menunggu">Terlalu lama menunggu</option>
                    <?php endif; ?>
                    <option value="Alasan lainnya">Alasan lainnya</option>
                </select>

                <!-- KOLOM KALAU MEMILIH Alasan lainnya -->
                <textarea name="alasan_lain" id="batalLain" class="form-control mb-2 d-none" rows="2" maxlength="150"
                    placeholder="Tuliskan alasan Anda..."></textarea>

                <!-- INFORMASI REFUND KALAU SUDAH DIBAYAR (QRIS ATAU TRANSFER) -->
                <div id="infoRefund" class="alert alert-info d-none mb-0">
                    Pesanan ini sudah dibayar. Dana akan dikembalikan. 
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="batalSetuju">
                        <label class="form-check-label" for="batalSetuju">Saya memahami dan setuju</label>
                    </div>
                </div>
                <div id="infoTanpaRefund" class="alert alert-secondary d-none mb-0">
                    Pesanan ini belum dibayar, tidak ada refund!.
                </div>
            </div>
            <!-- TOMBOL KONFIRMASI BATALKAN PESANAN-->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                
                <!-- BATALKAN PESANAN BARU BISA SETELAH KOLOM ALASAN DIISI -->
                <button type="submit" class="btn btn-danger" id="batalOk" disabled>Ya, Batalkan Pesanan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const batalAlasan = document.getElementById('batalAlasan');
    const batalLain = document.getElementById('batalLain');
    const batalSetuju = document.getElementById('batalSetuju');
    const batalOk = document.getElementById('batalOk');
    let sudahDibayar = false;

    // Tombol OK aktif hanya jika: alasan dipilih, (jika "lainnya" -> teks diisi), (jika sudah dibayar -> centang setuju)
    function cekForm() {
        let valid = true;
        if (batalAlasan.value == '') { valid = false; }
        if (batalAlasan.value == 'Alasan lainnya') {
            batalLain.classList.remove('d-none');
            if (batalLain.value.trim() == '') { valid = false; }
        } else {
            batalLain.classList.add('d-none');
        }
        if (sudahDibayar && !batalSetuju.checked) { valid = false; }
        batalOk.disabled = !valid;
    }
    batalAlasan.addEventListener('change', cekForm);
    batalLain.addEventListener('input', cekForm);
    batalSetuju.addEventListener('change', cekForm);

    // Saat pop-up dibuka: isi data pesanan dari tombol yang diklik, lalu reset form
    document.getElementById('modalBatal').addEventListener('show.bs.modal', function (event) {
        const tombol = event.relatedTarget;
        document.getElementById('batalId').value = tombol.dataset.id;
        document.getElementById('batalMenu').textContent = tombol.dataset.menu;
        sudahDibayar = (tombol.dataset.dibayar == '1');
        batalAlasan.value = '';
        batalLain.value = '';
        batalSetuju.checked = false;
        if (sudahDibayar) {
            document.getElementById('infoRefund').classList.remove('d-none');
            document.getElementById('infoTanpaRefund').classList.add('d-none');
        } else {
            document.getElementById('infoRefund').classList.add('d-none');
            document.getElementById('infoTanpaRefund').classList.remove('d-none');
        }
        cekForm();
    });
</script>