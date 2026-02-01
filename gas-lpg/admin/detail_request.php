<?php
/**
 * =====================================================
 * File: admin/detail_request.php
 * Halaman detail permintaan gas
 * =====================================================
 */

$id_admin = $_SESSION['id_user'];
$id_permintaan = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data permintaan
$query = mysqli_query($koneksi, "
    SELECT pg.*, 
           u.nama_depan as nama_agen, u.nama_belakang as nama_belakang_agen, u.telepon as telepon_agen, u.email as email_agen,
           p.nama_produk, p.harga
    FROM tb_permintaan_gas pg
    JOIN user u ON pg.id_agen = u.id_user
    JOIN tb_produk p ON pg.id_produk = p.id_produk
    WHERE pg.id_permintaan = $id_permintaan AND pg.id_admin = $id_admin
");

if (mysqli_num_rows($query) == 0) {
    echo "<script>alert('Permintaan tidak ditemukan!'); window.location='index.php?page=request_gas';</script>";
    exit();
}

$data = mysqli_fetch_assoc($query);

// Ambil data distribusi jika sudah selesai
$distribusi = null;
if ($data['status'] == 'selesai') {
    $query_dist = mysqli_query($koneksi, "SELECT * FROM tb_distribusi WHERE id_permintaan = $id_permintaan");
    $distribusi = mysqli_fetch_assoc($query_dist);
}
?>

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Detail Permintaan</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="index.php?page=request_gas" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Info Permintaan -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        Info Permintaan
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%">Kode Permintaan</td>
                            <td><strong><?php echo $data['kode_permintaan']; ?></strong></td>
                        </tr>
                        <tr>
                            <td>Produk</td>
                            <td><?php echo $data['nama_produk']; ?></td>
                        </tr>
                        <tr>
                            <td>Jumlah Diminta</td>
                            <td><strong><?php echo $data['jumlah']; ?> unit</strong></td>
                        </tr>
                        <tr>
                            <td>Harga Satuan</td>
                            <td><?php echo formatRupiah($data['harga']); ?></td>
                        </tr>
                        <tr>
                            <td>Total Nilai</td>
                            <td><strong><?php echo formatRupiah($data['jumlah'] * $data['harga']); ?></strong></td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>
                                <?php
                                $badge = [
                                    'menunggu' => 'warning',
                                    'ditolak' => 'danger',
                                    'selesai' => 'info'
                                ];
                                ?>
                                <span class="badge badge-<?php echo $badge[$data['status']]; ?> p-2">
                                    <?php echo strtoupper($data['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Waktu Request</td>
                            <td><?php echo date('d/m/Y H:i', strtotime($data['waktu_permintaan'])); ?></td>
                        </tr>
                        <?php if ($data['catatan_admin']): ?>
                        <tr>
                            <td>Catatan Anda</td>
                            <td><?php echo $data['catatan_admin']; ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <!-- Info Agen -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-tie mr-2"></i>
                        Info Agen
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%">Nama</td>
                            <td><?php echo $data['nama_agen'] . ' ' . $data['nama_belakang_agen']; ?></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td><?php echo $data['email_agen']; ?></td>
                        </tr>
                        <tr>
                            <td>Telepon</td>
                            <td><?php echo $data['telepon_agen']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Status & Timeline -->
        <div class="col-md-6">
            <!-- Status Card -->
            <div class="card">
                <div class="card-header bg-<?php echo $badge[$data['status']]; ?>">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-2"></i>
                        Status Permintaan
                    </h3>
                </div>
                <div class="card-body text-center">
                    <?php if ($data['status'] == 'menunggu'): ?>
                        <i class="fas fa-clock fa-4x text-warning mb-3"></i>
                        <h4>Menunggu Respon Agen</h4>
                        <p class="text-muted">Permintaan Anda sedang diproses oleh agen</p>
                    <?php elseif ($data['status'] == 'selesai'): ?>
                        <i class="fas fa-check-double fa-4x text-info mb-3"></i>
                        <h4>Distribusi Selesai</h4>
                        <p class="text-muted">Stok telah ditambahkan ke inventori Anda</p>
                    <?php elseif ($data['status'] == 'ditolak'): ?>
                        <i class="fas fa-times-circle fa-4x text-danger mb-3"></i>
                        <h4>Permintaan Ditolak</h4>
                        <p class="text-muted">Agen menolak permintaan Anda</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Respon Agen -->
            <?php if ($data['status'] != 'menunggu'): ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-reply mr-2"></i>
                        Respon Agen
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%">Waktu Respon</td>
                            <td><?php echo $data['waktu_respon'] ? date('d/m/Y H:i', strtotime($data['waktu_respon'])) : '-'; ?></td>
                        </tr>
                        <?php if ($data['waktu_selesai']): ?>
                        <tr>
                            <td>Waktu Selesai</td>
                            <td><?php echo date('d/m/Y H:i', strtotime($data['waktu_selesai'])); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td>Catatan Agen</td>
                            <td><?php echo $data['catatan_agen'] ?: '-'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Info Distribusi -->
            <?php if ($distribusi): ?>
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">
                        <i class="fas fa-truck mr-2"></i>
                        Info Distribusi
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%">Jumlah Diterima</td>
                            <td><strong><?php echo $distribusi['jumlah']; ?> unit</strong></td>
                        </tr>
                        <tr>
                            <td>Waktu Distribusi</td>
                            <td><?php echo date('d/m/Y H:i', strtotime($distribusi['waktu_distribusi'])); ?></td>
                        </tr>
                    </table>
                    <div class="alert alert-success mb-0">
                        <i class="fas fa-check-circle mr-2"></i>
                        Stok produk Anda telah bertambah <?php echo $distribusi['jumlah']; ?> unit
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
