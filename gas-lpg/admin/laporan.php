<?php
/**
 * =====================================================
 * File: admin/laporan.php
 * Halaman laporan penjualan
 * =====================================================
 */

// Filter tanggal
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');

// Ambil data penjualan
$query_penjualan = mysqli_query($koneksi, "
    SELECT p.*, u.nama_depan, u.nama_belakang, pr.nama_produk 
    FROM tb_pesanan p 
    JOIN user u ON p.id_user = u.id_user 
    JOIN tb_produk pr ON p.id_produk = pr.id_produk 
    WHERE p.status = 'completed' 
    AND DATE(p.waktu_selesai) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
    ORDER BY p.waktu_selesai DESC
");

// Hitung total pendapatan
$query_total = mysqli_query($koneksi, "
    SELECT SUM(total_harga) as total, COUNT(*) as jumlah 
    FROM tb_pesanan 
    WHERE status = 'completed' 
    AND DATE(waktu_selesai) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
");
$total = mysqli_fetch_assoc($query_total);
?>

<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-file-invoice mr-2" style="color: #f39c12;"></i>Laporan Penjualan</h4>
            <p class="text-muted">Laporan penjualan berdasarkan periode</p>
        </div>
    </div>

    <!-- Filter Tanggal -->
    <div class="card mb-3">
        <div class="card-body">
            <form action="" method="GET" class="form-inline">
                <input type="hidden" name="page" value="laporan">
                <div class="form-group mr-3">
                    <label class="mr-2">Dari:</label>
                    <input type="date" class="form-control" name="tanggal_awal" value="<?php echo $tanggal_awal; ?>">
                </div>
                <div class="form-group mr-3">
                    <label class="mr-2">Sampai:</label>
                    <input type="date" class="form-control" name="tanggal_akhir" value="<?php echo $tanggal_akhir; ?>">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </form>
        </div>
    </div>

    <!-- Ringkasan -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?php echo formatRupiah($total['total'] ?? 0); ?></h3>
                    <p>Total Pendapatan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?php echo $total['jumlah'] ?? 0; ?></h3>
                    <p>Total Transaksi</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Laporan -->
    <div class="card">
        <div class="card-header">
            <a href="cetak_laporan.php?tanggal_awal=<?php echo $tanggal_awal; ?>&tanggal_akhir=<?php echo $tanggal_akhir; ?>" target="_blank" class="btn btn-secondary">
                <i class="fas fa-print mr-2"></i>Cetak
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead style="background-color: #2c3e50; color: white;">
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Pembeli</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($query_penjualan)): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row['kode_pesanan']; ?></td>
                            <td><?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?></td>
                            <td><?php echo $row['nama_produk']; ?></td>
                            <td><?php echo $row['jumlah']; ?></td>
                            <td><?php echo formatRupiah($row['total_harga']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['waktu_selesai'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
