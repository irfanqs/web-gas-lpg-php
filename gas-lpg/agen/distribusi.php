<?php
/**
 * =====================================================
 * File: agen/distribusi.php
 * Halaman riwayat distribusi gas ke admin
 * =====================================================
 */

$id_agen = $_SESSION['id_user'];

// Filter bulan
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

// Ambil data distribusi
$query = mysqli_query($koneksi, "
    SELECT d.*, u.nama_depan, u.nama_belakang, p.nama_produk, p.harga,
           pg.kode_permintaan
    FROM tb_distribusi d
    JOIN user u ON d.id_admin = u.id_user
    JOIN tb_produk p ON d.id_produk = p.id_produk
    LEFT JOIN tb_permintaan_gas pg ON d.id_permintaan = pg.id_permintaan
    WHERE d.id_agen = $id_agen
    AND DATE_FORMAT(d.waktu_distribusi, '%Y-%m') = '$filter_bulan'
    ORDER BY d.waktu_distribusi DESC
");

// Hitung total distribusi bulan ini
$query_total = mysqli_query($koneksi, "
    SELECT SUM(d.jumlah) as total_unit, SUM(d.jumlah * p.harga) as total_nilai
    FROM tb_distribusi d
    JOIN tb_produk p ON d.id_produk = p.id_produk
    WHERE d.id_agen = $id_agen
    AND DATE_FORMAT(d.waktu_distribusi, '%Y-%m') = '$filter_bulan'
");
$total = mysqli_fetch_assoc($query_total);
?>

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Riwayat Distribusi</h1>
        </div>
    </div>

    <!-- Filter & Summary -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <form method="get" class="form-inline">
                        <input type="hidden" name="page" value="distribusi">
                        <label class="mr-2">Bulan:</label>
                        <input type="month" name="bulan" class="form-control mr-2" value="<?php echo $filter_bulan; ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?php echo number_format($total['total_unit'] ?? 0); ?></h3>
                    <p>Total Unit Distribusi</p>
                </div>
                <div class="icon">
                    <i class="fas fa-boxes"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?php echo formatRupiah($total['total_nilai'] ?? 0); ?></h3>
                    <p>Total Nilai Distribusi</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-truck mr-2"></i>
                        Daftar Distribusi - <?php echo date('F Y', strtotime($filter_bulan . '-01')); ?>
                    </h3>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kode Permintaan</th>
                                <th>Admin/Pangkalan</th>
                                <th>Produk</th>
                                <th>Jumlah</th>
                                <th>Nilai</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query)): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['waktu_distribusi'])); ?></td>
                                <td><?php echo $row['kode_permintaan'] ?? '-'; ?></td>
                                <td><?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?></td>
                                <td><?php echo $row['nama_produk']; ?></td>
                                <td><?php echo $row['jumlah']; ?> unit</td>
                                <td><?php echo formatRupiah($row['jumlah'] * $row['harga']); ?></td>
                                <td><?php echo $row['keterangan'] ?: '-'; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
