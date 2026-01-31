<?php
/**
 * =====================================================
 * File: agen/home.php
 * Dashboard untuk agen
 * =====================================================
 */

$id_agen = $_SESSION['id_user'];

// Total stok
$query_stok = mysqli_query($koneksi, "SELECT SUM(jumlah_stok) as total FROM tb_stok_agen WHERE id_agen = $id_agen");
$total_stok = mysqli_fetch_assoc($query_stok)['total'] ?? 0;

// Permintaan menunggu
$query_pending = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_permintaan_gas WHERE id_agen = $id_agen AND status = 'menunggu'");
$total_pending = mysqli_fetch_assoc($query_pending)['total'] ?? 0;

// Total distribusi bulan ini
$bulan_ini = date('Y-m');
$query_distribusi = mysqli_query($koneksi, "SELECT SUM(jumlah) as total FROM tb_distribusi WHERE id_agen = $id_agen AND DATE_FORMAT(waktu_distribusi, '%Y-%m') = '$bulan_ini'");
$total_distribusi = mysqli_fetch_assoc($query_distribusi)['total'] ?? 0;

// Total admin yang dilayani
$query_admin = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM user WHERE id_agen = $id_agen AND role = 'Admin'");
$total_admin = mysqli_fetch_assoc($query_admin)['total'] ?? 0;

// Permintaan terbaru
$query_permintaan = mysqli_query($koneksi, "
    SELECT pg.*, u.nama_depan, u.nama_belakang, p.nama_produk 
    FROM tb_permintaan_gas pg
    JOIN user u ON pg.id_admin = u.id_user
    JOIN tb_produk p ON pg.id_produk = p.id_produk
    WHERE pg.id_agen = $id_agen
    ORDER BY pg.waktu_permintaan DESC
    LIMIT 5
");
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Dashboard Agen</h1>
        </div>
    </div>

    <!-- Info Boxes -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?php echo number_format($total_stok); ?></h3>
                    <p>Total Stok Gas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <a href="index.php?page=stok" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?php echo $total_pending; ?></h3>
                    <p>Permintaan Menunggu</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="index.php?page=permintaan" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?php echo number_format($total_distribusi); ?></h3>
                    <p>Distribusi Bulan Ini</p>
                </div>
                <div class="icon">
                    <i class="fas fa-truck"></i>
                </div>
                <a href="index.php?page=distribusi" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3><?php echo $total_admin; ?></h3>
                    <p>Admin/Pangkalan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-store"></i>
                </div>
                <a href="#" class="small-box-footer">
                    <i class="fas fa-info-circle"></i> Info
                </a>
            </div>
        </div>
    </div>

    <!-- Permintaan Terbaru -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        Permintaan Terbaru
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Admin</th>
                                <th>Produk</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($query_permintaan) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($query_permintaan)): ?>
                                <tr>
                                    <td><?php echo $row['kode_permintaan']; ?></td>
                                    <td><?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?></td>
                                    <td><?php echo $row['nama_produk']; ?></td>
                                    <td><?php echo $row['jumlah']; ?> unit</td>
                                    <td>
                                        <?php
                                        $badge = [
                                            'menunggu' => 'warning',
                                            'disetujui' => 'success',
                                            'ditolak' => 'danger',
                                            'selesai' => 'info'
                                        ];
                                        ?>
                                        <span class="badge badge-<?php echo $badge[$row['status']]; ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($row['waktu_permintaan'])); ?></td>
                                    <td>
                                        <a href="index.php?page=detail_permintaan&id=<?php echo $row['id_permintaan']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada permintaan</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
