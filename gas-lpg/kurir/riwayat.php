<?php
/**
 * =====================================================
 * File: kurir/riwayat.php
 * Halaman riwayat pengantaran kurir
 * =====================================================
 */

$id_kurir = $_SESSION['id_user'];

// Ambil riwayat pengantaran
$query_riwayat = mysqli_query($koneksi, "
    SELECT p.*, pr.nama_produk 
    FROM tb_pesanan p 
    JOIN tb_produk pr ON p.id_produk = pr.id_produk 
    WHERE p.id_kurir = $id_kurir AND p.status = 'completed'
    ORDER BY p.waktu_selesai DESC
");
?>

<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-history mr-2" style="color: #f39c12;"></i>Riwayat Pengantaran</h4>
            <p class="text-muted">Daftar pesanan yang sudah selesai diantar</p>
        </div>
    </div>

    <!-- Tabel Riwayat -->
    <div class="card">
        <div class="card-body">
            <?php if (mysqli_num_rows($query_riwayat) > 0): ?>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead style="background-color: #2c3e50; color: white;">
                        <tr>
                            <th>No</th>
                            <th>Kode Pesanan</th>
                            <th>Penerima</th>
                            <th>Alamat</th>
                            <th>Produk</th>
                            <th>Waktu Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($query_riwayat)): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo $row['kode_pesanan']; ?></strong></td>
                            <td>
                                <?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?><br>
                                <small class="text-muted"><?php echo $row['telepon']; ?></small>
                            </td>
                            <td><?php echo $row['alamat_pengantaran']; ?></td>
                            <td><?php echo $row['nama_produk']; ?> (<?php echo $row['jumlah']; ?>)</td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['waktu_selesai'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada riwayat pengantaran</h5>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
