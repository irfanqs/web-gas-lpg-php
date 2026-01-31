<?php
/**
 * =====================================================
 * File: pembeli/riwayat.php
 * Halaman riwayat pesanan pembeli
 * =====================================================
 */

$id_user = $_SESSION['id_user'];

// Proses cancel pesanan
if (isset($_GET['cancel'])) {
    $id_cancel = intval($_GET['cancel']);
    // Cek apakah pesanan milik user ini dan statusnya pending
    $cek = mysqli_query($koneksi, "SELECT * FROM tb_pesanan WHERE id_pesanan = $id_cancel AND id_user = $id_user AND status = 'pending'");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($koneksi, "UPDATE tb_pesanan SET status = 'cancelled' WHERE id_pesanan = $id_cancel");
        echo "<script>alert('Pesanan berhasil dibatalkan!'); window.location='index.php?page=riwayat';</script>";
    } else {
        echo "<script>alert('Pesanan tidak dapat dibatalkan!'); window.location='index.php?page=riwayat';</script>";
    }
}

// Ambil semua pesanan user
$query_pesanan = mysqli_query($koneksi, "
    SELECT p.*, pr.nama_produk, pr.gambar 
    FROM tb_pesanan p 
    JOIN tb_produk pr ON p.id_produk = pr.id_produk 
    WHERE p.id_user = $id_user 
    ORDER BY p.waktu_pesan DESC
");

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
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-history mr-2" style="color: #f39c12;"></i>Riwayat Pesanan</h4>
            <p class="text-muted">Daftar semua pesanan yang pernah Anda buat</p>
        </div>
    </div>

    <!-- Tabel Riwayat Pesanan -->
    <div class="card">
        <div class="card-body">
            <?php if (mysqli_num_rows($query_pesanan) > 0): ?>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead style="background-color: #2c3e50; color: white;">
                        <tr>
                            <th>No</th>
                            <th>Kode Pesanan</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($query_pesanan)): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo $row['kode_pesanan']; ?></strong></td>
                            <td><?php echo $row['nama_produk']; ?></td>
                            <td><?php echo $row['jumlah']; ?></td>
                            <td><?php echo formatRupiah($row['total_harga']); ?></td>
                            <td><?php echo getStatusBadge($row['status']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['waktu_pesan'])); ?></td>
                            <td>
                                <a href="index.php?page=detail_pesanan&id=<?php echo $row['id_pesanan']; ?>" 
                                   class="btn btn-sm btn-info" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($row['status'] == 'pending'): ?>
                                <a href="index.php?page=riwayat&cancel=<?php echo $row['id_pesanan']; ?>" 
                                   class="btn btn-sm btn-danger" title="Batalkan"
                                   onclick="return confirm('Yakin ingin membatalkan pesanan ini?');">
                                    <i class="fas fa-times"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada pesanan</h5>
                <p class="text-muted">Anda belum pernah melakukan pemesanan</p>
                <a href="index.php?page=produk" class="btn" style="background-color: #f39c12; color: white;">
                    <i class="fas fa-shopping-cart mr-2"></i>Pesan Sekarang
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
