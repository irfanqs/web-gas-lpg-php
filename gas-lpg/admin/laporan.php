<?php
/**
 * =====================================================
 * File: admin/laporan.php
 * Halaman laporan penjualan
 * =====================================================
 */

// Filter tanggal - default dari 1 Januari tahun ini
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-01-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');

// Ambil data penjualan - urut ascending berdasarkan tanggal
$query_penjualan = mysqli_query($koneksi, "
    SELECT p.*, u.nama_depan, u.nama_belakang, pr.nama_produk 
    FROM tb_pesanan p 
    JOIN user u ON p.id_user = u.id_user 
    JOIN tb_produk pr ON p.id_produk = pr.id_produk 
    WHERE p.status = 'completed' 
    AND DATE(p.waktu_selesai) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
    ORDER BY p.waktu_selesai ASC
");

// Hitung total pendapatan, transaksi, dan stok terjual
$query_total = mysqli_query($koneksi, "
    SELECT 
        SUM(total_harga) as total_pendapatan, 
        COUNT(*) as total_transaksi,
        SUM(jumlah) as total_stok_terjual
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
            <p class="text-muted">Laporan detail penjualan berdasarkan periode</p>
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
        <div class="col-md-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?php echo formatRupiah($total['total_pendapatan'] ?? 0); ?></h3>
                    <p>Total Pendapatan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?php echo $total['total_transaksi'] ?? 0; ?></h3>
                    <p>Total Transaksi</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?php echo $total['total_stok_terjual'] ?? 0; ?> Unit</h3>
                    <p>Total Gas Terjual</p>
                </div>
                <div class="icon">
                    <i class="fas fa-fire"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Laporan -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-table mr-2"></i>Detail Transaksi
            </h3>
            <div class="card-tools">
                <a href="cetak_laporan.php?tanggal_awal=<?php echo $tanggal_awal; ?>&tanggal_akhir=<?php echo $tanggal_akhir; ?>" target="_blank" class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf mr-2"></i>Cetak PDF
                </a>
                <a href="export_laporan.php?tanggal_awal=<?php echo $tanggal_awal; ?>&tanggal_akhir=<?php echo $tanggal_akhir; ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead style="background-color: #2c3e50; color: white;">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Pembelian</th>
                            <th>Kode Pembelian</th>
                            <th>Nama Pembeli</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($query_penjualan)): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['waktu_selesai'])); ?></td>
                            <td><strong><?php echo $row['kode_pesanan']; ?></strong></td>
                            <td><?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?></td>
                            <td><?php echo $row['nama_produk']; ?></td>
                            <td><?php echo $row['jumlah']; ?> unit</td>
                            <td><?php echo formatRupiah($row['total_harga']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot style="background-color: #f5f5f5; font-weight: bold;">
                        <tr>
                            <td colspan="5" class="text-right">TOTAL:</td>
                            <td><?php echo $total['total_stok_terjual'] ?? 0; ?> unit</td>
                            <td><?php echo formatRupiah($total['total_pendapatan'] ?? 0); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
