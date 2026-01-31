<?php
/**
 * =====================================================
 * File: admin/detail_pesanan.php
 * Halaman detail pesanan untuk admin
 * =====================================================
 */

// Cek parameter ID
if (!isset($_GET['id'])) {
    echo "<script>window.location='index.php?page=kelola_pesanan';</script>";
    exit();
}

$id_pesanan = intval($_GET['id']);

// Ambil data pesanan
$query_pesanan = mysqli_query($koneksi, "
    SELECT p.*, pr.nama_produk, pr.gambar, pr.harga as harga_satuan,
           u.nama_depan as pembeli_nama_depan, u.nama_belakang as pembeli_nama_belakang, u.email as pembeli_email,
           k.nama_depan as kurir_nama_depan, k.nama_belakang as kurir_nama_belakang
    FROM tb_pesanan p 
    JOIN tb_produk pr ON p.id_produk = pr.id_produk 
    JOIN user u ON p.id_user = u.id_user
    LEFT JOIN user k ON p.id_kurir = k.id_user
    WHERE p.id_pesanan = $id_pesanan
");

$pesanan = mysqli_fetch_assoc($query_pesanan);

if (!$pesanan) {
    echo "<script>alert('Pesanan tidak ditemukan!'); window.location='index.php?page=kelola_pesanan';</script>";
    exit();
}

// Fungsi untuk mendapatkan badge status
function getStatusBadge($status) {
    $badges = [
        'pending' => '<span class="badge badge-warning">Menunggu Pembayaran</span>',
        'paid' => '<span class="badge badge-info">Menunggu Konfirmasi</span>',
        'confirmed' => '<span class="badge badge-primary">Dikonfirmasi</span>',
        'delivering' => '<span class="badge badge-secondary">Sedang Diantar</span>',
        'completed' => '<span class="badge badge-success">Selesai</span>',
        'cancelled' => '<span class="badge badge-danger">Dibatalkan</span>',
        'expired' => '<span class="badge badge-dark">Kadaluarsa</span>'
    ];
    return $badges[$status] ?? '<span class="badge badge-secondary">Unknown</span>';
}
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="index.php?page=kelola_pesanan" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <h4><i class="fas fa-receipt mr-2" style="color: #f39c12;"></i>Detail Pesanan</h4>
        </div>
    </div>

    <div class="row">
        <!-- Info Pesanan -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header" style="background-color: #2c3e50; color: white;">
                    <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Informasi Pesanan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Kode Pesanan</strong></td>
                            <td>: <?php echo $pesanan['kode_pesanan']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>: <?php echo getStatusBadge($pesanan['status']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Waktu Pesan</strong></td>
                            <td>: <?php echo date('d/m/Y H:i', strtotime($pesanan['waktu_pesan'])); ?></td>
                        </tr>
                        <?php if ($pesanan['waktu_bayar']): ?>
                        <tr>
                            <td><strong>Waktu Bayar</strong></td>
                            <td>: <?php echo date('d/m/Y H:i', strtotime($pesanan['waktu_bayar'])); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($pesanan['waktu_konfirmasi']): ?>
                        <tr>
                            <td><strong>Waktu Konfirmasi</strong></td>
                            <td>: <?php echo date('d/m/Y H:i', strtotime($pesanan['waktu_konfirmasi'])); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($pesanan['waktu_selesai']): ?>
                        <tr>
                            <td><strong>Waktu Selesai</strong></td>
                            <td>: <?php echo date('d/m/Y H:i', strtotime($pesanan['waktu_selesai'])); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <!-- Info Pembeli -->
            <div class="card">
                <div class="card-header" style="background-color: #9b59b6; color: white;">
                    <h5 class="mb-0"><i class="fas fa-user mr-2"></i>Informasi Pembeli</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Nama</strong></td>
                            <td>: <?php echo $pesanan['pembeli_nama_depan'] . ' ' . $pesanan['pembeli_nama_belakang']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Email</strong></td>
                            <td>: <?php echo $pesanan['pembeli_email']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Info Pengantaran & Produk -->
        <div class="col-md-6">
            <!-- Info Penerima -->
            <div class="card">
                <div class="card-header" style="background-color: #e67e22; color: white;">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt mr-2"></i>Informasi Pengantaran</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Penerima</strong></td>
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
                        <?php if ($pesanan['kurir_nama_depan']): ?>
                        <tr>
                            <td><strong>Kurir</strong></td>
                            <td>: <?php echo $pesanan['kurir_nama_depan'] . ' ' . $pesanan['kurir_nama_belakang']; ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <!-- Info Produk -->
            <div class="card">
                <div class="card-header" style="background-color: #27ae60; color: white;">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart mr-2"></i>Detail Produk</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="../uploads/produk/<?php echo $pesanan['gambar'] ?: 'default.png'; ?>" 
                             alt="<?php echo $pesanan['nama_produk']; ?>" 
                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 5px;" class="mr-3">
                        <div>
                            <h6 class="mb-1"><?php echo $pesanan['nama_produk']; ?></h6>
                            <small class="text-muted"><?php echo formatRupiah($pesanan['harga_satuan']); ?> x <?php echo $pesanan['jumlah']; ?></small>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total</strong>
                        <strong style="color: #e74c3c; font-size: 1.2em;"><?php echo formatRupiah($pesanan['total_harga']); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
