<?php
/**
 * =====================================================
 * File: admin/detail_konfirmasi.php
 * Halaman detail pesanan untuk konfirmasi admin
 * =====================================================
 */

// Cek parameter ID
if (!isset($_GET['id'])) {
    echo "<script>
            alert('ID pesanan tidak ditemukan!');
            window.location='index.php?page=konfirmasi_pesanan';
          </script>";
    exit();
}

$id_pesanan = intval($_GET['id']);

// Ambil data pesanan
$query_pesanan = mysqli_query($koneksi, "
    SELECT p.*, u.nama_depan as pembeli_nama_depan, u.nama_belakang as pembeli_nama_belakang, 
           u.email as pembeli_email, u.telepon as pembeli_telepon,
           pr.nama_produk, pr.gambar, pr.harga as harga_satuan
    FROM tb_pesanan p 
    JOIN user u ON p.id_user = u.id_user 
    JOIN tb_produk pr ON p.id_produk = pr.id_produk 
    WHERE p.id_pesanan = $id_pesanan
");

if (mysqli_num_rows($query_pesanan) == 0) {
    echo "<script>
            alert('Pesanan tidak ditemukan!');
            window.location='index.php?page=konfirmasi_pesanan';
          </script>";
    exit();
}

$pesanan = mysqli_fetch_assoc($query_pesanan);

// Ambil data pembayaran
$query_pembayaran = mysqli_query($koneksi, "SELECT * FROM tb_pembayaran WHERE id_pesanan = $id_pesanan");
$pembayaran = mysqli_fetch_assoc($query_pembayaran);

// Ambil daftar kurir untuk dropdown
$query_kurir = mysqli_query($koneksi, "SELECT * FROM user WHERE role = 'Kurir' ORDER BY nama_depan");

// =====================================================
// PROSES KONFIRMASI PESANAN
// =====================================================
if (isset($_POST['konfirmasi'])) {
    $id_kurir = intval($_POST['id_kurir']);
    
    // Ambil nama kurir
    $query_nama_kurir = mysqli_query($koneksi, "SELECT nama_depan, nama_belakang FROM user WHERE id_user = $id_kurir");
    $data_kurir = mysqli_fetch_assoc($query_nama_kurir);
    $nama_kurir = $data_kurir['nama_depan'] . ' ' . $data_kurir['nama_belakang'];
    
    // Update status pesanan menjadi 'confirmed'
    $query_update = mysqli_query($koneksi, "
        UPDATE tb_pesanan 
        SET status = 'confirmed', id_kurir = $id_kurir, waktu_konfirmasi = NOW() 
        WHERE id_pesanan = $id_pesanan
    ");
    
    // Update status pembayaran menjadi 'success'
    mysqli_query($koneksi, "UPDATE tb_pembayaran SET status_pembayaran = 'success', waktu_pembayaran = NOW() WHERE id_pesanan = $id_pesanan");
    
    // Kirim notifikasi ke kurir
    $pesan_kurir = "Ada pesanan dari {$pesanan['nama_depan']} {$pesanan['nama_belakang']} sebanyak {$pesanan['jumlah']} {$pesanan['nama_produk']} di alamat {$pesanan['alamat_pengantaran']}.";
    mysqli_query($koneksi, "
        INSERT INTO tb_notifikasi (id_user, id_pesanan, judul, pesan, tipe) 
        VALUES ($id_kurir, $id_pesanan, 'Pesanan Baru untuk Diantar', '$pesan_kurir', 'konfirmasi')
    ");
    
    // Kirim notifikasi ke pembeli
    $nama_pembeli = $pesanan['nama_depan'] . ' ' . $pesanan['nama_belakang'];
    $pesan_pembeli = "Pesanan atas nama $nama_pembeli kode pesanan {$pesanan['kode_pesanan']} sebanyak {$pesanan['jumlah']} unit akan diantarkan oleh $nama_kurir siap menuju lokasi.";
    mysqli_query($koneksi, "
        INSERT INTO tb_notifikasi (id_user, id_pesanan, judul, pesan, tipe) 
        VALUES ({$pesanan['id_user']}, $id_pesanan, 'Pesanan Dikonfirmasi', '$pesan_pembeli', 'konfirmasi')
    ");
    
    echo "<script>
            alert('Pesanan berhasil dikonfirmasi! Notifikasi telah dikirim ke kurir dan pembeli.');
            window.location='index.php?page=konfirmasi_pesanan';
          </script>";
    exit();
}

// =====================================================
// PROSES TOLAK PESANAN
// =====================================================
if (isset($_POST['tolak'])) {
    $alasan = escapeString($_POST['alasan_tolak']);
    
    // Update status pesanan menjadi 'cancelled'
    mysqli_query($koneksi, "UPDATE tb_pesanan SET status = 'cancelled' WHERE id_pesanan = $id_pesanan");
    
    // Update status pembayaran menjadi 'failed'
    mysqli_query($koneksi, "UPDATE tb_pembayaran SET status_pembayaran = 'failed' WHERE id_pesanan = $id_pesanan");
    
    echo "<script>
            alert('Pesanan ditolak.');
            window.location='index.php?page=konfirmasi_pesanan';
          </script>";
    exit();
}
?>

<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="index.php?page=konfirmasi_pesanan" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <h4><i class="fas fa-clipboard-check mr-2" style="color: #f39c12;"></i>Detail Konfirmasi Pesanan</h4>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Kiri: Info Pesanan -->
        <div class="col-md-8">
            <!-- Card Info Pesanan -->
            <div class="card">
                <div class="card-header" style="background-color: #2c3e50; color: white;">
                    <h5 class="mb-0"><i class="fas fa-receipt mr-2"></i>Informasi Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Kode Pesanan</strong></td>
                                    <td>: <?php echo $pesanan['kode_pesanan']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Produk</strong></td>
                                    <td>: <?php echo $pesanan['nama_produk']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Jumlah</strong></td>
                                    <td>: <?php echo $pesanan['jumlah']; ?> unit</td>
                                </tr>
                                <tr>
                                    <td><strong>Harga Satuan</strong></td>
                                    <td>: <?php echo formatRupiah($pesanan['harga_satuan']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Total Harga</strong></td>
                                    <td>: <strong style="color: #e74c3c;"><?php echo formatRupiah($pesanan['total_harga']); ?></strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Waktu Pesan</strong></td>
                                    <td>: <?php echo date('d/m/Y H:i', strtotime($pesanan['waktu_pesan'])); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Waktu Bayar</strong></td>
                                    <td>: <?php echo date('d/m/Y H:i', strtotime($pesanan['waktu_bayar'])); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Info Pembeli -->
            <div class="card">
                <div class="card-header" style="background-color: #3498db; color: white;">
                    <h5 class="mb-0"><i class="fas fa-user mr-2"></i>Informasi Pembeli</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%"><strong>Nama</strong></td>
                            <td>: <?php echo $pesanan['pembeli_nama_depan'] . ' ' . $pesanan['pembeli_nama_belakang']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Email</strong></td>
                            <td>: <?php echo $pesanan['pembeli_email']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Telepon</strong></td>
                            <td>: <?php echo $pesanan['pembeli_telepon']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Card Info Pengantaran -->
            <div class="card">
                <div class="card-header" style="background-color: #27ae60; color: white;">
                    <h5 class="mb-0"><i class="fas fa-truck mr-2"></i>Informasi Pengantaran</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%"><strong>Penerima</strong></td>
                            <td>: <?php echo $pesanan['nama_depan'] . ' ' . $pesanan['nama_belakang']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Telepon</strong></td>
                            <td>: <?php echo $pesanan['telepon']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Alamat</strong></td>
                            <td>: <?php echo $pesanan['alamat_pengantaran']; ?></td>
                        </tr>
                        <?php if ($pesanan['catatan']): ?>
                        <tr>
                            <td><strong>Catatan</strong></td>
                            <td>: <?php echo $pesanan['catatan']; ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Bukti Pembayaran & Aksi -->
        <div class="col-md-4">
            <!-- Card Bukti Pembayaran -->
            <div class="card">
                <div class="card-header" style="background-color: #f39c12; color: white;">
                    <h5 class="mb-0"><i class="fas fa-credit-card mr-2"></i>Bukti Pembayaran</h5>
                </div>
                <div class="card-body">
                    <p><strong>Metode:</strong> <?php echo $pembayaran['metode_pembayaran']; ?></p>
                    <p><strong>Jumlah:</strong> <?php echo formatRupiah($pembayaran['jumlah_bayar']); ?></p>
                    <hr>
                    <?php if ($pembayaran['bukti_pembayaran']): ?>
                    <p><strong>Bukti Transfer:</strong></p>
                    <a href="../uploads/bukti_pembayaran/<?php echo $pembayaran['bukti_pembayaran']; ?>" target="_blank">
                        <img src="../uploads/bukti_pembayaran/<?php echo $pembayaran['bukti_pembayaran']; ?>" 
                             alt="Bukti Pembayaran" class="img-fluid" style="max-height: 300px;">
                    </a>
                    <p class="mt-2"><small class="text-muted">Klik gambar untuk memperbesar</small></p>
                    <?php else: ?>
                    <p class="text-muted">Tidak ada bukti pembayaran</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card Aksi Konfirmasi -->
            <div class="card">
                <div class="card-header" style="background-color: #9b59b6; color: white;">
                    <h5 class="mb-0"><i class="fas fa-check-circle mr-2"></i>Aksi Konfirmasi</h5>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <!-- Pilih Kurir -->
                        <div class="form-group">
                            <label><strong>Pilih Kurir untuk Mengantar:</strong></label>
                            <select class="form-control" name="id_kurir" required>
                                <option value="">-- Pilih Kurir --</option>
                                <?php while ($kurir = mysqli_fetch_assoc($query_kurir)): ?>
                                <option value="<?php echo $kurir['id_user']; ?>">
                                    <?php echo $kurir['nama_depan'] . ' ' . $kurir['nama_belakang']; ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Tombol Konfirmasi -->
                        <button type="submit" name="konfirmasi" class="btn btn-success btn-block">
                            <i class="fas fa-check mr-2"></i>Konfirmasi Pesanan
                        </button>
                    </form>

                    <hr>

                    <!-- Form Tolak -->
                    <form action="" method="POST" onsubmit="return confirm('Yakin ingin menolak pesanan ini?');">
                        <div class="form-group">
                            <label><strong>Alasan Penolakan (opsional):</strong></label>
                            <textarea class="form-control" name="alasan_tolak" rows="2" placeholder="Masukkan alasan..."></textarea>
                        </div>
                        <button type="submit" name="tolak" class="btn btn-danger btn-block">
                            <i class="fas fa-times mr-2"></i>Tolak Pesanan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
