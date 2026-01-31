<?php
/**
 * =====================================================
 * File: admin/kelola_pesanan.php
 * Halaman untuk melihat semua pesanan
 * =====================================================
 */

// Ambil semua pesanan
$query_pesanan = mysqli_query($koneksi, "
    SELECT p.*, u.nama_depan as pembeli_nama, u.nama_belakang as pembeli_nama_belakang, 
           pr.nama_produk, k.nama_depan as kurir_nama
    FROM tb_pesanan p 
    JOIN user u ON p.id_user = u.id_user 
    JOIN tb_produk pr ON p.id_produk = pr.id_produk 
    LEFT JOIN user k ON p.id_kurir = k.id_user
    ORDER BY p.waktu_pesan DESC
");

// Fungsi untuk badge status
function getBadgeClass($status) {
    $badges = [
        'pending' => 'badge-warning',
        'paid' => 'badge-info',
        'confirmed' => 'badge-primary',
        'delivering' => 'badge-secondary',
        'completed' => 'badge-success',
        'cancelled' => 'badge-danger',
        'expired' => 'badge-dark'
    ];
    return $badges[$status] ?? 'badge-secondary';
}
?>

<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-shopping-cart mr-2" style="color: #f39c12;"></i>Semua Pesanan</h4>
            <p class="text-muted">Daftar semua pesanan dalam sistem</p>
        </div>
    </div>

    <!-- Tabel Pesanan -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead style="background-color: #2c3e50; color: white;">
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Pembeli</th>
                            <th>Produk</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Kurir</th>
                            <th>Tanggal</th>
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
                            <td><?php echo $row['pembeli_nama'] . ' ' . $row['pembeli_nama_belakang']; ?></td>
                            <td><?php echo $row['nama_produk']; ?> (<?php echo $row['jumlah']; ?>)</td>
                            <td><?php echo formatRupiah($row['total_harga']); ?></td>
                            <td>
                                <span class="badge <?php echo getBadgeClass($row['status']); ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $row['kurir_nama'] ?: '-'; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['waktu_pesan'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
