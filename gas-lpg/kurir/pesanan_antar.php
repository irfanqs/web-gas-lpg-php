<?php
/**
 * =====================================================
 * File: kurir/pesanan_antar.php
 * Halaman daftar pesanan yang harus diantar oleh kurir
 * =====================================================
 */

$id_kurir = $_SESSION['id_user'];

// Proses pembatalan pesanan
if (isset($_POST['batalkan_pesanan'])) {
    $id_pesanan = intval($_POST['id_pesanan']);
    $alasan = escapeString($_POST['alasan_pembatalan']);
    
    // Cek apakah pesanan sudah lebih dari 3 hari
    $query_cek = mysqli_query($koneksi, "SELECT * FROM tb_pesanan WHERE id_pesanan = $id_pesanan AND id_kurir = $id_kurir AND status = 'confirmed'");
    $pesanan_batal = mysqli_fetch_assoc($query_cek);
    
    if ($pesanan_batal) {
        $waktu_konfirmasi = strtotime($pesanan_batal['waktu_konfirmasi']);
        $waktu_sekarang = time();
        $selisih_hari = ($waktu_sekarang - $waktu_konfirmasi) / (60 * 60 * 24);
        
        if ($selisih_hari >= 3) {
            // Update status pesanan menjadi cancelled
            mysqli_query($koneksi, "UPDATE tb_pesanan SET status = 'cancelled' WHERE id_pesanan = $id_pesanan");
            
            // Kirim notifikasi ke pembeli
            $nama_pembeli = $pesanan_batal['nama_depan'] . ' ' . $pesanan_batal['nama_belakang'];
            $kode = $pesanan_batal['kode_pesanan'];
            $tgl_pesan = date('d/m/Y', strtotime($pesanan_batal['waktu_pesan']));
            $pesan_notif = "Maaf, pesanan dengan kode pesanan $kode atas nama $nama_pembeli di tanggal $tgl_pesan tidak bisa diantarkan karena: $alasan";
            
            mysqli_query($koneksi, "INSERT INTO tb_notifikasi (id_user, id_pesanan, judul, pesan, tipe) 
                VALUES ({$pesanan_batal['id_user']}, $id_pesanan, 'Pesanan Dibatalkan', '$pesan_notif', 'konfirmasi')");
            
            echo "<script>alert('Pesanan berhasil dibatalkan'); window.location='index.php?page=pesanan_antar';</script>";
            exit();
        } else {
            echo "<script>alert('Pesanan belum bisa dibatalkan. Minimal 3 hari setelah konfirmasi.');</script>";
        }
    }
}

// Ambil pesanan yang sudah dikonfirmasi untuk kurir ini
$query_pesanan = mysqli_query($koneksi, "
    SELECT p.*, pr.nama_produk 
    FROM tb_pesanan p 
    JOIN tb_produk pr ON p.id_produk = pr.id_produk 
    WHERE p.id_kurir = $id_kurir AND p.status = 'confirmed'
    ORDER BY p.waktu_konfirmasi ASC
");
?>

<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-truck mr-2" style="color: #f39c12;"></i>Pesanan untuk Diantar</h4>
            <p class="text-muted">Daftar pesanan yang menunggu untuk diantar</p>
        </div>
    </div>

    <!-- Daftar Pesanan -->
    <div class="row">
        <?php if (mysqli_num_rows($query_pesanan) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($query_pesanan)): 
                // Hitung selisih hari untuk tombol batalkan
                $waktu_konfirmasi = strtotime($row['waktu_konfirmasi']);
                $selisih_hari = (time() - $waktu_konfirmasi) / (60 * 60 * 24);
                $bisa_batalkan = $selisih_hari >= 3;
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-header" style="background-color: #f39c12; color: white;">
                        <strong><?php echo $row['kode_pesanan']; ?></strong>
                    </div>
                    <div class="card-body">
                        <h5><?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?></h5>
                        <p class="mb-1">
                            <i class="fas fa-calendar mr-2"></i><strong>Tanggal Pesan:</strong> <?php echo date('d/m/Y H:i', strtotime($row['waktu_pesan'])); ?>
                        </p>
                        <p class="mb-1">
                            <i class="fas fa-phone mr-2"></i><?php echo $row['telepon']; ?>
                        </p>
                        <p class="mb-1">
                            <i class="fas fa-map-marker-alt mr-2"></i><?php echo $row['alamat_pengantaran']; ?>
                        </p>
                        <hr>
                        <p class="mb-1">
                            <strong>Produk:</strong> <?php echo $row['nama_produk']; ?>
                        </p>
                        <p class="mb-1">
                            <strong>Jumlah:</strong> <?php echo $row['jumlah']; ?> unit
                        </p>
                        <?php if ($row['catatan']): ?>
                        <p class="mb-1">
                            <strong>Catatan:</strong> <?php echo $row['catatan']; ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <a href="index.php?page=detail_antar&id=<?php echo $row['id_pesanan']; ?>" 
                           class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-motorcycle mr-2"></i>Mulai Antar
                        </a>
                        <?php if ($bisa_batalkan): ?>
                        <button type="button" class="btn btn-danger btn-block" data-toggle="modal" 
                                data-target="#modalBatal<?php echo $row['id_pesanan']; ?>">
                            <i class="fas fa-times mr-2"></i>Batalkan Pesanan
                        </button>
                        
                        <!-- Modal Pembatalan -->
                        <div class="modal fade" id="modalBatal<?php echo $row['id_pesanan']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Batalkan Pesanan</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form action="" method="POST">
                                        <div class="modal-body">
                                            <p><strong>Kode:</strong> <?php echo $row['kode_pesanan']; ?></p>
                                            <p><strong>Atas Nama:</strong> <?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?></p>
                                            <p><strong>Tanggal Pesan:</strong> <?php echo date('d/m/Y', strtotime($row['waktu_pesan'])); ?></p>
                                            <hr>
                                            <div class="form-group">
                                                <label>Alasan Pembatalan <span class="text-danger">*</span></label>
                                                <textarea class="form-control" name="alasan_pembatalan" rows="3" required
                                                          placeholder="Masukkan alasan tidak bisa mengantar..."></textarea>
                                            </div>
                                            <input type="hidden" name="id_pesanan" value="<?php echo $row['id_pesanan']; ?>">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                            <button type="submit" name="batalkan_pesanan" class="btn btn-danger">Konfirmasi Batalkan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <?php 
                        $sisa_hari = ceil(3 - $selisih_hari);
                        ?>
                        <small class="text-muted d-block text-center">Pembatalan tersedia dalam <?php echo $sisa_hari; ?> hari lagi</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h5 class="text-muted">Tidak ada pesanan yang menunggu</h5>
                    <p class="text-muted">Semua pesanan sudah diantar</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
