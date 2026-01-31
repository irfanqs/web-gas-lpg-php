<?php
/**
 * =====================================================
 * File: kurir/detail_antar.php
 * Halaman detail pesanan untuk kurir mulai mengantar
 * =====================================================
 */

// Cek parameter ID
if (!isset($_GET['id'])) {
    echo "<script>
            alert('ID pesanan tidak ditemukan!');
            window.location='index.php?page=pesanan_antar';
          </script>";
    exit();
}

$id_pesanan = intval($_GET['id']);
$id_kurir = $_SESSION['id_user'];

// Ambil data pesanan (pastikan milik kurir yang login)
$query_pesanan = mysqli_query($koneksi, "
    SELECT p.*, pr.nama_produk, pr.gambar 
    FROM tb_pesanan p 
    JOIN tb_produk pr ON p.id_produk = pr.id_produk 
    WHERE p.id_pesanan = $id_pesanan AND p.id_kurir = $id_kurir
");

if (mysqli_num_rows($query_pesanan) == 0) {
    echo "<script>
            alert('Pesanan tidak ditemukan!');
            window.location='index.php?page=pesanan_antar';
          </script>";
    exit();
}

$pesanan = mysqli_fetch_assoc($query_pesanan);

// =====================================================
// PROSES MULAI ANTAR
// =====================================================
if (isset($_POST['mulai_antar'])) {
    // Update status pesanan menjadi 'delivering'
    mysqli_query($koneksi, "UPDATE tb_pesanan SET status = 'delivering', waktu_antar = NOW() WHERE id_pesanan = $id_pesanan");
    
    echo "<script>
            alert('Pengantaran dimulai! Semangat!');
            window.location='index.php?page=sedang_antar';
          </script>";
    exit();
}
?>

<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="index.php?page=pesanan_antar" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <h4><i class="fas fa-truck mr-2" style="color: #f39c12;"></i>Detail Pesanan</h4>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Info Pesanan -->
        <div class="col-md-8">
            <!-- Card Info Penerima -->
            <div class="card">
                <div class="card-header" style="background-color: #3498db; color: white;">
                    <h5 class="mb-0"><i class="fas fa-user mr-2"></i>Informasi Penerima</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4><?php echo $pesanan['nama_depan'] . ' ' . $pesanan['nama_belakang']; ?></h4>
                            <p class="mb-2">
                                <i class="fas fa-phone mr-2" style="color: #f39c12;"></i>
                                <a href="tel:<?php echo $pesanan['telepon']; ?>"><?php echo $pesanan['telepon']; ?></a>
                            </p>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="tel:<?php echo $pesanan['telepon']; ?>" class="btn btn-success">
                                <i class="fas fa-phone mr-2"></i>Hubungi
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Alamat Pengantaran -->
            <div class="card">
                <div class="card-header" style="background-color: #e74c3c; color: white;">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt mr-2"></i>Alamat Pengantaran</h5>
                </div>
                <div class="card-body">
                    <h5><?php echo $pesanan['alamat_pengantaran']; ?></h5>
                    <?php if ($pesanan['catatan']): ?>
                    <hr>
                    <p class="mb-0"><strong>Catatan:</strong> <?php echo $pesanan['catatan']; ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card Detail Produk -->
            <div class="card">
                <div class="card-header" style="background-color: #27ae60; color: white;">
                    <h5 class="mb-0"><i class="fas fa-box mr-2"></i>Detail Produk</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%"><strong>Produk</strong></td>
                            <td>: <?php echo $pesanan['nama_produk']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Jumlah</strong></td>
                            <td>: <?php echo $pesanan['jumlah']; ?> unit</td>
                        </tr>
                        <tr>
                            <td><strong>Total Harga</strong></td>
                            <td>: <?php echo formatRupiah($pesanan['total_harga']); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kolom Aksi -->
        <div class="col-md-4">
            <!-- Card Info Pesanan -->
            <div class="card">
                <div class="card-header" style="background-color: #f39c12; color: white;">
                    <h5 class="mb-0"><i class="fas fa-receipt mr-2"></i>Info Pesanan</h5>
                </div>
                <div class="card-body">
                    <p><strong>Kode:</strong> <?php echo $pesanan['kode_pesanan']; ?></p>
                    <p><strong>Status:</strong> 
                        <span class="badge badge-primary">Siap Diantar</span>
                    </p>
                    <p><strong>Waktu Pesan:</strong><br>
                        <?php echo date('d/m/Y H:i', strtotime($pesanan['waktu_pesan'])); ?>
                    </p>
                    <p><strong>Dikonfirmasi:</strong><br>
                        <?php echo date('d/m/Y H:i', strtotime($pesanan['waktu_konfirmasi'])); ?>
                    </p>
                </div>
            </div>

            <!-- Card Aksi -->
            <div class="card">
                <div class="card-header" style="background-color: #9b59b6; color: white;">
                    <h5 class="mb-0"><i class="fas fa-motorcycle mr-2"></i>Aksi</h5>
                </div>
                <div class="card-body">
                    <form action="" method="POST" onsubmit="return confirm('Mulai mengantar pesanan ini?');">
                        <button type="submit" name="mulai_antar" class="btn btn-success btn-block btn-lg">
                            <i class="fas fa-motorcycle mr-2"></i>Mulai Antar
                        </button>
                    </form>
                    <p class="text-muted mt-3 text-center" style="font-size: 14px;">
                        Klik tombol di atas untuk memulai pengantaran
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
