<?php
/**
 * =====================================================
 * File: agen/laporan.php
 * Halaman laporan agen
 * =====================================================
 */

$id_agen = $_SESSION['id_user'];

// Filter periode
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');

// Laporan Stok Masuk
$query_masuk = mysqli_query($koneksi, "
    SELECT p.nama_produk, SUM(rs.jumlah) as total
    FROM tb_riwayat_stok rs
    JOIN tb_produk p ON rs.id_produk = p.id_produk
    WHERE rs.id_agen = $id_agen 
    AND rs.tipe = 'masuk'
    AND DATE(rs.created_at) BETWEEN '$tgl_awal' AND '$tgl_akhir'
    GROUP BY rs.id_produk
");

// Laporan Distribusi
$query_distribusi = mysqli_query($koneksi, "
    SELECT p.nama_produk, SUM(d.jumlah) as total, SUM(d.jumlah * p.harga) as nilai
    FROM tb_distribusi d
    JOIN tb_produk p ON d.id_produk = p.id_produk
    WHERE d.id_agen = $id_agen
    AND DATE(d.waktu_distribusi) BETWEEN '$tgl_awal' AND '$tgl_akhir'
    GROUP BY d.id_produk
");

// Laporan per Admin
$query_per_admin = mysqli_query($koneksi, "
    SELECT u.nama_depan, u.nama_belakang, SUM(d.jumlah) as total, SUM(d.jumlah * p.harga) as nilai
    FROM tb_distribusi d
    JOIN user u ON d.id_admin = u.id_user
    JOIN tb_produk p ON d.id_produk = p.id_produk
    WHERE d.id_agen = $id_agen
    AND DATE(d.waktu_distribusi) BETWEEN '$tgl_awal' AND '$tgl_akhir'
    GROUP BY d.id_admin
    ORDER BY total DESC
");

// Total
$query_total = mysqli_query($koneksi, "
    SELECT 
        (SELECT SUM(jumlah) FROM tb_riwayat_stok WHERE id_agen = $id_agen AND tipe = 'masuk' AND DATE(created_at) BETWEEN '$tgl_awal' AND '$tgl_akhir') as total_masuk,
        (SELECT SUM(jumlah) FROM tb_distribusi WHERE id_agen = $id_agen AND DATE(waktu_distribusi) BETWEEN '$tgl_awal' AND '$tgl_akhir') as total_keluar
");
$totals = mysqli_fetch_assoc($query_total);
?>

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Laporan Agen</h1>
        </div>
        <div class="col-sm-6 text-right">
            <div class="btn-group">
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="export_excel.php?tipe=distribusi&tgl_awal=<?php echo $tgl_awal; ?>&tgl_akhir=<?php echo $tgl_akhir; ?>">
                        <i class="fas fa-truck mr-2"></i>Laporan Distribusi
                    </a>
                    <a class="dropdown-item" href="export_excel.php?tipe=stok&tgl_awal=<?php echo $tgl_awal; ?>&tgl_akhir=<?php echo $tgl_akhir; ?>">
                        <i class="fas fa-boxes mr-2"></i>Laporan Stok
                    </a>
                </div>
            </div>
            <a href="cetak_laporan.php?tgl_awal=<?php echo $tgl_awal; ?>&tgl_akhir=<?php echo $tgl_akhir; ?>" target="_blank" class="btn btn-danger">
                <i class="fas fa-file-pdf mr-2"></i>Cetak PDF
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="get" class="form-inline">
                        <input type="hidden" name="page" value="laporan">
                        
                        <div class="form-group mr-3">
                            <label class="mr-2">Dari:</label>
                            <input type="date" name="tgl_awal" class="form-control" value="<?php echo $tgl_awal; ?>">
                        </div>
                        
                        <div class="form-group mr-3">
                            <label class="mr-2">Sampai:</label>
                            <input type="date" name="tgl_akhir" class="form-control" value="<?php echo $tgl_akhir; ?>">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i> Tampilkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div class="row">
        <div class="col-md-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?php echo number_format($totals['total_masuk'] ?? 0); ?></h3>
                    <p>Total Stok Masuk</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-down"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><?php echo number_format($totals['total_keluar'] ?? 0); ?></h3>
                    <p>Total Distribusi</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?php echo number_format(($totals['total_masuk'] ?? 0) - ($totals['total_keluar'] ?? 0)); ?></h3>
                    <p>Selisih (Masuk - Keluar)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-balance-scale"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Laporan Stok Masuk -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">
                        <i class="fas fa-arrow-down mr-2"></i>
                        Stok Masuk per Produk
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($query_masuk)): ?>
                            <tr>
                                <td><?php echo $row['nama_produk']; ?></td>
                                <td class="text-right"><?php echo number_format($row['total']); ?> unit</td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Laporan Distribusi -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger">
                    <h3 class="card-title">
                        <i class="fas fa-arrow-up mr-2"></i>
                        Distribusi per Produk
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-right">Jumlah</th>
                                <th class="text-right">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($query_distribusi)): ?>
                            <tr>
                                <td><?php echo $row['nama_produk']; ?></td>
                                <td class="text-right"><?php echo number_format($row['total']); ?> unit</td>
                                <td class="text-right"><?php echo formatRupiah($row['nilai']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribusi per Admin -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">
                        <i class="fas fa-store mr-2"></i>
                        Distribusi per Admin/Pangkalan
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Admin/Pangkalan</th>
                                <th class="text-right">Total Unit</th>
                                <th class="text-right">Total Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query_per_admin)): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?></td>
                                <td class="text-right"><?php echo number_format($row['total']); ?> unit</td>
                                <td class="text-right"><?php echo formatRupiah($row['nilai']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
