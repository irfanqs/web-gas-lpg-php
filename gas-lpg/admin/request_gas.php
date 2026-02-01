<?php
/**
 * =====================================================
 * File: admin/request_gas.php
 * Halaman daftar permintaan gas ke agen
 * =====================================================
 */

$id_admin = $_SESSION['id_user'];

// Filter status
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

$where = "WHERE pg.id_admin = $id_admin";
if ($filter_status != '') {
    $where .= " AND pg.status = '$filter_status'";
}

// Ambil data permintaan
$query = mysqli_query($koneksi, "
    SELECT pg.*, u.nama_depan as nama_agen, p.nama_produk
    FROM tb_permintaan_gas pg
    JOIN user u ON pg.id_agen = u.id_user
    JOIN tb_produk p ON pg.id_produk = p.id_produk
    $where
    ORDER BY pg.waktu_permintaan DESC
");

// Cek apakah admin punya agen
$query_agen = mysqli_query($koneksi, "SELECT id_agen FROM user WHERE id_user = $id_admin");
$data_admin = mysqli_fetch_assoc($query_agen);
$has_agen = !empty($data_admin['id_agen']);
?>

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0"><i class="fas fa-truck-loading mr-2" style="color: #f39c12;"></i>Request Gas ke Agen</h1>
        </div>
        <div class="col-sm-6 text-right">
            <?php if ($has_agen): ?>
            <a href="index.php?page=tambah_request" class="btn btn-success">
                <i class="fas fa-plus mr-2"></i>Buat Request Baru
            </a>
            <?php else: ?>
            <button class="btn btn-secondary" disabled title="Anda belum terhubung dengan agen">
                <i class="fas fa-plus mr-2"></i>Buat Request Baru
            </button>
            <small class="text-danger d-block mt-1">Anda belum terhubung dengan agen</small>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filter -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="btn-group">
                <a href="index.php?page=request_gas" class="btn <?php echo ($filter_status == '') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    Semua
                </a>
                <a href="index.php?page=request_gas&status=menunggu" class="btn <?php echo ($filter_status == 'menunggu') ? 'btn-warning' : 'btn-outline-warning'; ?>">
                    Menunggu
                </a>
                <a href="index.php?page=request_gas&status=selesai" class="btn <?php echo ($filter_status == 'selesai') ? 'btn-info' : 'btn-outline-info'; ?>">
                    Selesai
                </a>
                <a href="index.php?page=request_gas&status=ditolak" class="btn <?php echo ($filter_status == 'ditolak') ? 'btn-danger' : 'btn-outline-danger'; ?>">
                    Ditolak
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-2"></i>
                        Daftar Permintaan Gas
                    </h3>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered table-striped">
                        <thead style="background-color: #2c3e50; color: white;">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Agen</th>
                                <th>Produk</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Waktu Request</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query)): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo $row['kode_permintaan']; ?></strong></td>
                                <td><?php echo $row['nama_agen']; ?></td>
                                <td><?php echo $row['nama_produk']; ?></td>
                                <td><strong><?php echo $row['jumlah']; ?></strong> unit</td>
                                <td>
                                    <?php
                                    $badge = [
                                        'menunggu' => 'warning',
                                        'ditolak' => 'danger',
                                        'selesai' => 'info'
                                    ];
                                    $icon = [
                                        'menunggu' => 'clock',
                                        'ditolak' => 'times',
                                        'selesai' => 'check-double'
                                    ];
                                    ?>
                                    <span class="badge badge-<?php echo $badge[$row['status']]; ?>">
                                        <i class="fas fa-<?php echo $icon[$row['status']]; ?> mr-1"></i>
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['waktu_permintaan'])); ?></td>
                                <td>
                                    <a href="index.php?page=detail_request&id=<?php echo $row['id_permintaan']; ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
