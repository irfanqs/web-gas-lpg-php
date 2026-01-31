<?php
/**
 * =====================================================
 * File: agen/detail_permintaan.php
 * Halaman detail dan proses permintaan gas
 * =====================================================
 */

$id_agen = $_SESSION['id_user'];
$id_permintaan = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data permintaan
$query = mysqli_query($koneksi, "
    SELECT pg.*, u.nama_depan, u.nama_belakang, u.telepon, u.alamat, u.email,
           p.nama_produk, p.harga
    FROM tb_permintaan_gas pg
    JOIN user u ON pg.id_admin = u.id_user
    JOIN tb_produk p ON pg.id_produk = p.id_produk
    WHERE pg.id_permintaan = $id_permintaan AND pg.id_agen = $id_agen
");

if (mysqli_num_rows($query) == 0) {
    echo "<script>alert('Permintaan tidak ditemukan!'); window.location='index.php?page=permintaan';</script>";
    exit();
}

$data = mysqli_fetch_assoc($query);

// Cek stok agen
$query_stok = mysqli_query($koneksi, "SELECT jumlah_stok FROM tb_stok_agen WHERE id_agen = $id_agen AND id_produk = {$data['id_produk']}");
$stok_agen = mysqli_fetch_assoc($query_stok)['jumlah_stok'] ?? 0;

// Proses setujui
if (isset($_POST['setujui'])) {
    if ($stok_agen < $data['jumlah']) {
        $error = "Stok tidak mencukupi! Stok tersedia: $stok_agen unit";
    } else {
        $catatan = escapeString($_POST['catatan_agen']);
        $waktu = date('Y-m-d H:i:s');
        
        // Update status permintaan
        mysqli_query($koneksi, "
            UPDATE tb_permintaan_gas 
            SET status = 'disetujui', catatan_agen = '$catatan', waktu_respon = '$waktu'
            WHERE id_permintaan = $id_permintaan
        ");
        
        // Kurangi stok agen
        $stok_sebelum = $stok_agen;
        $stok_sesudah = $stok_agen - $data['jumlah'];
        mysqli_query($koneksi, "UPDATE tb_stok_agen SET jumlah_stok = $stok_sesudah WHERE id_agen = $id_agen AND id_produk = {$data['id_produk']}");
        
        // Catat riwayat stok agen (keluar)
        mysqli_query($koneksi, "
            INSERT INTO tb_riwayat_stok (id_agen, id_produk, tipe, jumlah, stok_sebelum, stok_sesudah, keterangan)
            VALUES ($id_agen, {$data['id_produk']}, 'keluar', {$data['jumlah']}, $stok_sebelum, $stok_sesudah, 'Distribusi ke {$data['nama_depan']} - {$data['kode_permintaan']}')
        ");
        
        // Tambah stok admin (tb_produk)
        mysqli_query($koneksi, "UPDATE tb_produk SET stok = stok + {$data['jumlah']} WHERE id_produk = {$data['id_produk']}");
        
        // Catat distribusi
        mysqli_query($koneksi, "
            INSERT INTO tb_distribusi (id_permintaan, id_agen, id_admin, id_produk, jumlah, keterangan)
            VALUES ($id_permintaan, $id_agen, {$data['id_admin']}, {$data['id_produk']}, {$data['jumlah']}, '$catatan')
        ");
        
        // Update status jadi selesai
        mysqli_query($koneksi, "UPDATE tb_permintaan_gas SET status = 'selesai', waktu_selesai = '$waktu' WHERE id_permintaan = $id_permintaan");
        
        echo "<script>alert('Permintaan disetujui dan stok telah didistribusikan!'); window.location='index.php?page=permintaan';</script>";
        exit();
    }
}

// Proses tolak
if (isset($_POST['tolak'])) {
    $catatan = escapeString($_POST['catatan_agen']);
    $waktu = date('Y-m-d H:i:s');
    
    mysqli_query($koneksi, "
        UPDATE tb_permintaan_gas 
        SET status = 'ditolak', catatan_agen = '$catatan', waktu_respon = '$waktu'
        WHERE id_permintaan = $id_permintaan
    ");
    
    echo "<script>alert('Permintaan ditolak!'); window.location='index.php?page=permintaan';</script>";
    exit();
}
?>

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Detail Permintaan</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="index.php?page=permintaan" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Info Permintaan -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info">
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
                                    'disetujui' => 'success',
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
                            <td>Catatan Admin</td>
                            <td><?php echo $data['catatan_admin']; ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <!-- Info Admin -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-store mr-2"></i>
                        Info Admin/Pangkalan
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%">Nama</td>
                            <td><?php echo $data['nama_depan'] . ' ' . $data['nama_belakang']; ?></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td><?php echo $data['email']; ?></td>
                        </tr>
                        <tr>
                            <td>Telepon</td>
                            <td><?php echo $data['telepon']; ?></td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td><?php echo $data['alamat']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Aksi -->
        <div class="col-md-6">
            <!-- Info Stok -->
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">
                        <i class="fas fa-boxes mr-2"></i>
                        Ketersediaan Stok
                    </h3>
                </div>
                <div class="card-body text-center">
                    <h2>
                        <?php if ($stok_agen >= $data['jumlah']): ?>
                            <span class="text-success"><?php echo $stok_agen; ?> unit</span>
                        <?php else: ?>
                            <span class="text-danger"><?php echo $stok_agen; ?> unit</span>
                        <?php endif; ?>
                    </h2>
                    <p class="mb-0">Stok <?php echo $data['nama_produk']; ?> tersedia</p>
                    
                    <?php if ($stok_agen < $data['jumlah']): ?>
                        <div class="alert alert-danger mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Stok tidak mencukupi! Kurang <?php echo $data['jumlah'] - $stok_agen; ?> unit
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="fas fa-check-circle mr-2"></i>
                            Stok mencukupi untuk permintaan ini
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form Aksi (hanya jika status menunggu) -->
            <?php if ($data['status'] == 'menunggu'): ?>
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">
                        <i class="fas fa-tasks mr-2"></i>
                        Proses Permintaan
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Catatan/Keterangan</label>
                        <textarea id="catatan_agen" class="form-control" rows="3" placeholder="Tambahkan catatan (opsional)"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <form method="post" id="formSetujui">
                                <input type="hidden" name="catatan_agen" id="catatan_setujui">
                                <button type="submit" name="setujui" class="btn btn-success btn-block" <?php echo ($stok_agen < $data['jumlah']) ? 'disabled' : ''; ?>>
                                    <i class="fas fa-check mr-2"></i>Setujui
                                </button>
                            </form>
                        </div>
                        <div class="col-6">
                            <form method="post" id="formTolak">
                                <input type="hidden" name="catatan_agen" id="catatan_tolak">
                                <button type="submit" name="tolak" class="btn btn-danger btn-block" onclick="return confirm('Yakin ingin menolak permintaan ini?')">
                                    <i class="fas fa-times mr-2"></i>Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- Info Respon -->
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
                            <td>Catatan</td>
                            <td><?php echo $data['catatan_agen'] ?: '-'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Copy catatan ke form sebelum submit
document.getElementById('formSetujui')?.addEventListener('submit', function() {
    document.getElementById('catatan_setujui').value = document.getElementById('catatan_agen').value;
});
document.getElementById('formTolak')?.addEventListener('submit', function() {
    document.getElementById('catatan_tolak').value = document.getElementById('catatan_agen').value;
});
</script>
