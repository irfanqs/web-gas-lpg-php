<?php
/**
 * =====================================================
 * File: admin/konfirmasi_pesanan.php
 * Halaman untuk admin mengkonfirmasi pesanan yang sudah dibayar
 * =====================================================
 */

// Ambil pesanan yang menunggu konfirmasi (status = 'paid')
$query_pesanan = mysqli_query($koneksi, "
    SELECT p.*, u.nama_depan, u.nama_belakang, u.email, pr.nama_produk,
           pb.metode_pembayaran, pb.bukti_pembayaran
    FROM tb_pesanan p 
    JOIN user u ON p.id_user = u.id_user 
    JOIN tb_produk pr ON p.id_produk = pr.id_produk 
    LEFT JOIN tb_pembayaran pb ON p.id_pesanan = pb.id_pesanan
    WHERE p.status = 'paid'
    ORDER BY p.waktu_bayar DESC
");
?>

<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-clipboard-check mr-2" style="color: #f39c12;"></i>Konfirmasi Pesanan</h4>
            <p class="text-muted">Daftar pesanan yang menunggu konfirmasi pembayaran</p>
        </div>
    </div>

    <!-- Tabel Pesanan -->
    <div class="card">
        <div class="card-body">
            <?php if (mysqli_num_rows($query_pesanan) > 0): ?>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead style="background-color: #2c3e50; color: white;">
                        <tr>
                            <th>No</th>
                            <th>Kode Pesanan</th>
                            <th>Pembeli</th>
                            <th>Produk</th>
                            <th>Total</th>
                            <th>Metode Bayar</th>
                            <th>Waktu Bayar</th>
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
                            <td>
                                <?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?><br>
                                <small class="text-muted"><?php echo $row['email']; ?></small>
                            </td>
                            <td><?php echo $row['nama_produk']; ?> (<?php echo $row['jumlah']; ?>)</td>
                            <td><?php echo formatRupiah($row['total_harga']); ?></td>
                            <td><?php echo $row['metode_pembayaran']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['waktu_bayar'])); ?></td>
                            <td>
                                <a href="index.php?page=detail_konfirmasi&id=<?php echo $row['id_pesanan']; ?>" 
                                   class="btn btn-sm btn-info" title="Lihat Detail">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h5 class="text-muted">Tidak ada pesanan yang menunggu konfirmasi</h5>
                <p class="text-muted">Semua pesanan sudah dikonfirmasi</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
