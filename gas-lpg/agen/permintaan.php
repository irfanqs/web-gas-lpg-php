<?php
/**
 * =====================================================
 * File: agen/permintaan.php
 * Halaman daftar permintaan gas dari admin
 * =====================================================
 */

$id_agen = $_SESSION['id_user'];

// Filter status
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

$where = "WHERE pg.id_agen = $id_agen";
if ($filter_status != '') {
    $where .= " AND pg.status = '$filter_status'";
}

// Ambil data permintaan
$query = mysqli_query($koneksi, "
    SELECT pg.*, u.nama_depan, u.nama_belakang, u.telepon, u.alamat, p.nama_produk
    FROM tb_permintaan_gas pg
    JOIN user u ON pg.id_admin = u.id_user
    JOIN tb_produk p ON pg.id_produk = p.id_produk
    $where
    ORDER BY 
        CASE pg.status 
            WHEN 'menunggu' THEN 1 
            WHEN 'disetujui' THEN 2 
            WHEN 'selesai' THEN 3 
            WHEN 'ditolak' THEN 4 
        END,
        pg.waktu_permintaan DESC
");
?>

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Permintaan Gas dari Admin</h1>
        </div>
    </div>

    <!-- Filter -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="btn-group">
                <a href="index.php?page=permintaan" class="btn <?php echo ($filter_status == '') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    Semua
                </a>
                <a href="index.php?page=permintaan&status=menunggu" class="btn <?php echo ($filter_status == 'menunggu') ? 'btn-warning' : 'btn-outline-warning'; ?>">
                    Menunggu
                </a>
                <a href="index.php?page=permintaan&status=selesai" class="btn <?php echo ($filter_status == 'selesai') ? 'btn-info' : 'btn-outline-info'; ?>">
                    Selesai
                </a>
                <a href="index.php?page=permintaan&status=ditolak" class="btn <?php echo ($filter_status == 'ditolak') ? 'btn-danger' : 'btn-outline-danger'; ?>">
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
                        <i class="fas fa-clipboard-list mr-2"></i>
                        Daftar Permintaan
                    </h3>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Admin/Pangkalan</th>
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
                                <td>
                                    <?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?>
                                    <br><small class="text-muted"><?php echo $row['telepon']; ?></small>
                                </td>
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
                                    <a href="index.php?page=detail_permintaan&id=<?php echo $row['id_permintaan']; ?>" class="btn btn-sm btn-info">
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
