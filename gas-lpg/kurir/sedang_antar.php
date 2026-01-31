<?php
/**
 * =====================================================
 * File: kurir/sedang_antar.php
 * Halaman pesanan yang sedang diantar oleh kurir
 * =====================================================
 */

$id_kurir = $_SESSION['id_user'];

// =====================================================
// PROSES SELESAI ANTAR
// =====================================================
if (isset($_POST['selesai_antar'])) {
    $id_pesanan = intval($_POST['id_pesanan']);
    
    // Ambil data pesanan untuk notifikasi
    $query_data = mysqli_query($koneksi, "SELECT * FROM tb_pesanan WHERE id_pesanan = $id_pesanan AND id_kurir = $id_kurir");
    $data_pesanan = mysqli_fetch_assoc($query_data);
    
    if ($data_pesanan) {
        // Update status pesanan menjadi 'completed'
        mysqli_query($koneksi, "UPDATE tb_pesanan SET status = 'completed', waktu_selesai = NOW() WHERE id_pesanan = $id_pesanan");
        
        // Kirim notifikasi ke admin
        $pesan_admin = "Pesanan atas nama {$data_pesanan['nama_depan']} {$data_pesanan['nama_belakang']}, sebanyak {$data_pesanan['jumlah']} unit di alamat {$data_pesanan['alamat_pengantaran']} sudah selesai.";
        
        // Ambil semua admin untuk notifikasi
        $query_admin = mysqli_query($koneksi, "SELECT id_user FROM user WHERE role = 'Admin'");
        while ($admin = mysqli_fetch_assoc($query_admin)) {
            mysqli_query($koneksi, "
                INSERT INTO tb_notifikasi (id_user, id_pesanan, judul, pesan, tipe) 
                VALUES ({$admin['id_user']}, $id_pesanan, 'Pesanan Selesai Diantar', '$pesan_admin', 'selesai')
            ");
        }
        
        echo "<script>
                alert('Pengantaran selesai! Terima kasih.');
                window.location='index.php?page=sedang_antar';
              </script>";
        exit();
    }
}

// Ambil pesanan yang sedang diantar
$query_pesanan = mysqli_query($koneksi, "
    SELECT p.*, pr.nama_produk 
    FROM tb_pesanan p 
    JOIN tb_produk pr ON p.id_produk = pr.id_produk 
    WHERE p.id_kurir = $id_kurir AND p.status = 'delivering'
    ORDER BY p.waktu_antar ASC
");
?>

<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-motorcycle mr-2" style="color: #f39c12;"></i>Sedang Diantar</h4>
            <p class="text-muted">Pesanan yang sedang dalam perjalanan</p>
        </div>
    </div>

    <!-- Daftar Pesanan -->
    <div class="row">
        <?php if (mysqli_num_rows($query_pesanan) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($query_pesanan)): ?>
            <div class="col-md-6">
                <div class="card border-warning">
                    <div class="card-header" style="background-color: #e67e22; color: white;">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong><?php echo $row['kode_pesanan']; ?></strong>
                            <span class="badge badge-light">Sedang Diantar</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5><?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?></h5>
                        <p class="mb-1">
                            <i class="fas fa-phone mr-2"></i>
                            <a href="tel:<?php echo $row['telepon']; ?>"><?php echo $row['telepon']; ?></a>
                        </p>
                        <p class="mb-1">
                            <i class="fas fa-map-marker-alt mr-2"></i><?php echo $row['alamat_pengantaran']; ?>
                        </p>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-1"><strong>Produk:</strong></p>
                                <p><?php echo $row['nama_produk']; ?> (<?php echo $row['jumlah']; ?>)</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1"><strong>Mulai Antar:</strong></p>
                                <p><?php echo date('H:i', strtotime($row['waktu_antar'])); ?></p>
                            </div>
                        </div>
                        <?php if ($row['catatan']): ?>
                        <p class="mb-0 text-muted">
                            <strong>Catatan:</strong> <?php echo $row['catatan']; ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-6">
                                <a href="tel:<?php echo $row['telepon']; ?>" class="btn btn-info btn-block">
                                    <i class="fas fa-phone mr-1"></i>Hubungi
                                </a>
                            </div>
                            <div class="col-6">
                                <form action="" method="POST" style="display: inline;" 
                                      onsubmit="return confirm('Pesanan sudah sampai dan selesai diantar?');">
                                    <input type="hidden" name="id_pesanan" value="<?php echo $row['id_pesanan']; ?>">
                                    <button type="submit" name="selesai_antar" class="btn btn-success btn-block">
                                        <i class="fas fa-check mr-1"></i>Selesai
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada pesanan yang sedang diantar</h5>
                    <a href="index.php?page=pesanan_antar" class="btn btn-primary mt-3">
                        <i class="fas fa-truck mr-2"></i>Lihat Pesanan untuk Diantar
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
