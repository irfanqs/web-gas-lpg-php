<?php
/**
 * =====================================================
 * File: agen/stok.php
 * Halaman lihat stok saat ini
 * =====================================================
 */

$id_agen = $_SESSION['id_user'];

// Ambil data stok
$query = mysqli_query($koneksi, "
    SELECT sa.*, p.nama_produk, p.gambar, p.harga
    FROM tb_stok_agen sa
    JOIN tb_produk p ON sa.id_produk = p.id_produk
    WHERE sa.id_agen = $id_agen
    ORDER BY p.nama_produk ASC
");
?>

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Stok Gas Saat Ini</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="index.php?page=tambah_stok" class="btn btn-success">
                <i class="fas fa-plus mr-2"></i>Input Stok Masuk
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-warehouse mr-2"></i>
                        Daftar Stok
                    </h3>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Produk</th>
                                <th>Harga Satuan</th>
                                <th>Jumlah Stok</th>
                                <th>Total Nilai</th>
                                <th>Update Terakhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            $total_nilai = 0;
                            while ($row = mysqli_fetch_assoc($query)): 
                                $nilai = $row['jumlah_stok'] * $row['harga'];
                                $total_nilai += $nilai;
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <strong><?php echo $row['nama_produk']; ?></strong>
                                </td>
                                <td><?php echo formatRupiah($row['harga']); ?></td>
                                <td>
                                    <?php if ($row['jumlah_stok'] < 50): ?>
                                        <span class="badge badge-danger"><?php echo $row['jumlah_stok']; ?> unit</span>
                                        <small class="text-danger">(Stok rendah!)</small>
                                    <?php elseif ($row['jumlah_stok'] < 100): ?>
                                        <span class="badge badge-warning"><?php echo $row['jumlah_stok']; ?> unit</span>
                                    <?php else: ?>
                                        <span class="badge badge-success"><?php echo $row['jumlah_stok']; ?> unit</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo formatRupiah($nilai); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['updated_at'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Total Nilai Stok:</th>
                                <th colspan="2"><?php echo formatRupiah($total_nilai); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
