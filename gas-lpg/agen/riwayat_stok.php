<?php
/**
 * =====================================================
 * File: agen/riwayat_stok.php
 * Halaman riwayat pergerakan stok
 * =====================================================
 */

$id_agen = $_SESSION['id_user'];

// Filter
$filter_tipe = isset($_GET['tipe']) ? $_GET['tipe'] : '';
$filter_produk = isset($_GET['produk']) ? (int)$_GET['produk'] : 0;

$where = "WHERE rs.id_agen = $id_agen";
if ($filter_tipe != '') {
    $where .= " AND rs.tipe = '$filter_tipe'";
}
if ($filter_produk > 0) {
    $where .= " AND rs.id_produk = $filter_produk";
}

// Ambil data riwayat
$query = mysqli_query($koneksi, "
    SELECT rs.*, p.nama_produk
    FROM tb_riwayat_stok rs
    JOIN tb_produk p ON rs.id_produk = p.id_produk
    $where
    ORDER BY rs.created_at DESC
");

// Ambil daftar produk untuk filter
$query_produk = mysqli_query($koneksi, "SELECT * FROM tb_produk WHERE status = 'aktif'");
?>

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Riwayat Pergerakan Stok</h1>
        </div>
    </div>

    <!-- Filter -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="get" class="form-inline">
                        <input type="hidden" name="page" value="riwayat_stok">
                        
                        <div class="form-group mr-3">
                            <label class="mr-2">Tipe:</label>
                            <select name="tipe" class="form-control">
                                <option value="">Semua</option>
                                <option value="masuk" <?php echo ($filter_tipe == 'masuk') ? 'selected' : ''; ?>>Masuk</option>
                                <option value="keluar" <?php echo ($filter_tipe == 'keluar') ? 'selected' : ''; ?>>Keluar</option>
                            </select>
                        </div>
                        
                        <div class="form-group mr-3">
                            <label class="mr-2">Produk:</label>
                            <select name="produk" class="form-control">
                                <option value="0">Semua</option>
                                <?php while ($p = mysqli_fetch_assoc($query_produk)): ?>
                                    <option value="<?php echo $p['id_produk']; ?>" <?php echo ($filter_produk == $p['id_produk']) ? 'selected' : ''; ?>>
                                        <?php echo $p['nama_produk']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        <a href="index.php?page=riwayat_stok" class="btn btn-secondary ml-2">Reset</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-2"></i>
                        Riwayat Stok
                    </h3>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Produk</th>
                                <th>Tipe</th>
                                <th>Jumlah</th>
                                <th>Stok Sebelum</th>
                                <th>Stok Sesudah</th>
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
                                <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                <td><?php echo $row['nama_produk']; ?></td>
                                <td>
                                    <?php if ($row['tipe'] == 'masuk'): ?>
                                        <span class="badge badge-success"><i class="fas fa-arrow-down mr-1"></i>Masuk</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><i class="fas fa-arrow-up mr-1"></i>Keluar</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['tipe'] == 'masuk'): ?>
                                        <span class="text-success">+<?php echo $row['jumlah']; ?></span>
                                    <?php else: ?>
                                        <span class="text-danger">-<?php echo $row['jumlah']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $row['stok_sebelum']; ?></td>
                                <td><?php echo $row['stok_sesudah']; ?></td>
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
