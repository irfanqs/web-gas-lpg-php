<?php
/**
 * =====================================================
 * File: pembeli/detail_pesanan.php
 * Halaman detail pesanan untuk pembeli
 * =====================================================
 */

// Cek parameter ID
if (!isset($_GET['id'])) {
    echo "<script>
            alert('ID pesanan tidak ditemukan!');
            window.location='index.php?page=riwayat';
          </script>";
    exit();
}

$id_pesanan = intval($_GET['id']);
$id_user = $_SESSION['id_user'];

// Ambil data pesanan (pastikan milik user yang login)
$query_pesanan = mysqli_query($koneksi, "
    SELECT p.*, pr.nama_produk, pr.gambar, pr.deskripsi,
           k.nama_depan as kurir_nama_depan, k.nama_belakang as kurir_nama_belakang, k.telepon as kurir_telepon
    FROM tb_pesanan p 
    JOIN tb_produk pr ON p.id_produk = pr.id_produk 
    LEFT JOIN user k ON p.id_kurir = k.id_user
    WHERE p.id_pesanan = $id_pesanan AND p.id_user = $id_user
");

if (mysqli_num_rows($query_pesanan) == 0) {
    echo "<script>
            alert('Pesanan tidak ditemukan!');
            window.location='index.php?page=riwayat';
          </script>";
    exit();
}

$pesanan = mysqli_fetch_assoc($query_pesanan);

// Ambil data pembayaran jika ada
$query_pembayaran = mysqli_query($koneksi, "SELECT * FROM tb_pembayaran WHERE id_pesanan = $id_pesanan");
$pembayaran = mysqli_fetch_assoc($query_pembayaran);

// Fungsi untuk mendapatkan badge status
function getStatusBadgeDetail($status) {
    $badges = [
        'pending' => '<span class="badge badge-warning badge-lg">Menunggu Pembayaran</span>',
        'paid' => '<span class="badge badge-info badge-lg">Menunggu Konfirmasi Admin</span>',
        'confirmed' => '<span class="badge badge-primary badge-lg">Dikonfirmasi - Menunggu Kurir</span>',
        'delivering' => '<span class="badge badge-secondary badge-lg">Sedang Diantar</span>',
        'completed' => '<span class="badge badge-success badge-lg">Pesanan Selesai</span>',
        'cancelled' => '<span class="badge badge-danger badge-lg">Dibatalkan</span>',
        'expired' => '<span class="badge badge-dark badge-lg">Kadaluarsa</span>'
    ];
    return $badges[$status] ?? '<span class="badge badge-secondary">Unknown</span>';
}
?>

<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="index.php?page=riwayat" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <h4><i class="fas fa-receipt mr-2" style="color: #f39c12;"></i>Detail Pesanan</h4>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Kiri: Info Pesanan -->
        <div class="col-md-8">
            <!-- Card Status -->
            <div class="card">
                <div class="card-header" style="background-color: #2c3e50; color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle mr-2"></i>
                        Status Pesanan
                    </h5>
                </div>
                <div class="card-body text-center">
                    <h3><?php echo getStatusBadgeDetail($pesanan['status']); ?></h3>
                    <p class="text-muted mt-2">Kode Pesanan: <strong><?php echo $pesanan['kode_pesanan']; ?></strong></p>
                </div>
            </div>

            <!-- Card Detail Produk -->
            <div class="card">
                <div class="card-header" style="background-color: #f39c12; color: white;">
                    <h5 class="mb-0"><i class="fas fa-box mr-2"></i>Detail Produk</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <img src="../uploads/produk/<?php echo $pesanan['gambar'] ?: 'default.png'; ?>" 
                                 alt="<?php echo $pesanan['nama_produk']; ?>" 
                                 class="img-fluid" style="max-height: 120px;">
                        </div>
                        <div class="col-md-9">
                            <h5><?php echo $pesanan['nama_produk']; ?></h5>
                            <p class="text-muted"><?php echo $pesanan['deskripsi']; ?></p>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="30%">Jumlah</td>
                                    <td>: <?php echo $pesanan['jumlah']; ?> unit</td>
                                </tr>
                                <tr>
                                    <td>Total Harga</td>
                                    <td>: <strong style="color: #e74c3c;"><?php echo formatRupiah($pesanan['total_harga']); ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Info Pengantaran -->
            <div class="card">
                <div class="card-header" style="background-color: #3498db; color: white;">
                    <h5 class="mb-0"><i class="fas fa-truck mr-2"></i>Informasi Pengantaran</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%">Penerima</td>
                            <td>: <?php echo $pesanan['nama_depan'] . ' ' . $pesanan['nama_belakang']; ?></td>
                        </tr>
                        <tr>
                            <td>Telepon</td>
                            <td>: <?php echo $pesanan['telepon']; ?></td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>: <?php echo $pesanan['alamat_pengantaran']; ?></td>
                        </tr>
                        <?php if ($pesanan['catatan']): ?>
                        <tr>
                            <td>Catatan</td>
                            <td>: <?php echo $pesanan['catatan']; ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>

                    <?php if ($pesanan['id_kurir'] && in_array($pesanan['status'], ['delivering', 'completed'])): ?>
                    <hr>
                    <h6><i class="fas fa-motorcycle mr-2"></i>Info Kurir</h6>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="30%">Nama Kurir</td>
                            <td>: <?php echo $pesanan['kurir_nama_depan'] . ' ' . $pesanan['kurir_nama_belakang']; ?></td>
                        </tr>
                        <tr>
                            <td>Telepon Kurir</td>
                            <td>: <?php echo $pesanan['kurir_telepon']; ?></td>
                        </tr>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Timeline & Pembayaran -->
        <div class="col-md-4">
            <!-- Card Pembayaran -->
            <?php if ($pembayaran): ?>
            <div class="card">
                <div class="card-header" style="background-color: #27ae60; color: white;">
                    <h5 class="mb-0"><i class="fas fa-credit-card mr-2"></i>Info Pembayaran</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td>Metode</td>
                            <td>: <?php echo $pembayaran['metode_pembayaran']; ?></td>
                        </tr>
                        <tr>
                            <td>Jumlah</td>
                            <td>: <?php echo formatRupiah($pembayaran['jumlah_bayar']); ?></td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>: 
                                <?php 
                                $status_bayar = [
                                    'pending' => '<span class="badge badge-warning">Pending</span>',
                                    'success' => '<span class="badge badge-success">Berhasil</span>',
                                    'failed' => '<span class="badge badge-danger">Gagal</span>',
                                    'expired' => '<span class="badge badge-dark">Expired</span>'
                                ];
                                echo $status_bayar[$pembayaran['status_pembayaran']] ?? '-';
                                ?>
                            </td>
                        </tr>
                    </table>
                    <?php if ($pembayaran['bukti_pembayaran']): ?>
                    <hr>
                    <p class="mb-2"><strong>Bukti Pembayaran:</strong></p>
                    <img src="../uploads/bukti_pembayaran/<?php echo $pembayaran['bukti_pembayaran']; ?>" 
                         alt="Bukti Pembayaran" class="img-fluid" style="max-height: 200px;">
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Card Timeline -->
            <div class="card">
                <div class="card-header" style="background-color: #9b59b6; color: white;">
                    <h5 class="mb-0"><i class="fas fa-clock mr-2"></i>Timeline</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled" style="font-size: 14px;">
                        <li class="mb-2">
                            <i class="fas fa-circle text-success mr-2" style="font-size: 8px;"></i>
                            <strong>Pesanan Dibuat</strong><br>
                            <small class="text-muted ml-3"><?php echo date('d/m/Y H:i', strtotime($pesanan['waktu_pesan'])); ?></small>
                        </li>
                        <?php if ($pesanan['waktu_bayar']): ?>
                        <li class="mb-2">
                            <i class="fas fa-circle text-info mr-2" style="font-size: 8px;"></i>
                            <strong>Pembayaran Diupload</strong><br>
                            <small class="text-muted ml-3"><?php echo date('d/m/Y H:i', strtotime($pesanan['waktu_bayar'])); ?></small>
                        </li>
                        <?php endif; ?>
                        <?php if ($pesanan['waktu_konfirmasi']): ?>
                        <li class="mb-2">
                            <i class="fas fa-circle text-primary mr-2" style="font-size: 8px;"></i>
                            <strong>Dikonfirmasi Admin</strong><br>
                            <small class="text-muted ml-3"><?php echo date('d/m/Y H:i', strtotime($pesanan['waktu_konfirmasi'])); ?></small>
                        </li>
                        <?php endif; ?>
                        <?php if ($pesanan['waktu_antar']): ?>
                        <li class="mb-2">
                            <i class="fas fa-circle text-secondary mr-2" style="font-size: 8px;"></i>
                            <strong>Sedang Diantar</strong><br>
                            <small class="text-muted ml-3"><?php echo date('d/m/Y H:i', strtotime($pesanan['waktu_antar'])); ?></small>
                        </li>
                        <?php endif; ?>
                        <?php if ($pesanan['waktu_selesai']): ?>
                        <li class="mb-2">
                            <i class="fas fa-circle text-success mr-2" style="font-size: 8px;"></i>
                            <strong>Pesanan Selesai</strong><br>
                            <small class="text-muted ml-3"><?php echo date('d/m/Y H:i', strtotime($pesanan['waktu_selesai'])); ?></small>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
