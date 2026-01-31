<?php
/**
 * =====================================================
 * File: admin/tambah_request.php
 * Halaman buat request gas baru ke agen
 * =====================================================
 */

$id_admin = $_SESSION['id_user'];

// Ambil data agen yang terhubung dengan admin ini
$query_admin = mysqli_query($koneksi, "SELECT u.*, a.nama_depan as nama_agen, a.id_user as id_agen 
                                        FROM user u 
                                        LEFT JOIN user a ON u.id_agen = a.id_user 
                                        WHERE u.id_user = $id_admin");
$data_admin = mysqli_fetch_assoc($query_admin);

if (empty($data_admin['id_agen'])) {
    echo "<script>alert('Anda belum terhubung dengan agen!'); window.location='index.php?page=request_gas';</script>";
    exit();
}

$id_agen = $data_admin['id_agen'];

// Ambil daftar produk
$query_produk = mysqli_query($koneksi, "SELECT * FROM tb_produk WHERE status = 'aktif' ORDER BY nama_produk");

// Ambil stok agen untuk produk
$query_stok_agen = mysqli_query($koneksi, "
    SELECT sa.id_produk, sa.jumlah_stok, p.nama_produk 
    FROM tb_stok_agen sa 
    JOIN tb_produk p ON sa.id_produk = p.id_produk
    WHERE sa.id_agen = $id_agen
");
$stok_agen = [];
while ($s = mysqli_fetch_assoc($query_stok_agen)) {
    $stok_agen[$s['id_produk']] = $s['jumlah_stok'];
}

// Proses tambah request
if (isset($_POST['submit'])) {
    $id_produk = (int)$_POST['id_produk'];
    $jumlah = (int)$_POST['jumlah'];
    $catatan = escapeString($_POST['catatan']);
    
    if ($jumlah <= 0) {
        $error = "Jumlah harus lebih dari 0!";
    } else {
        $kode = generateKodePermintaan();
        
        $query = mysqli_query($koneksi, "
            INSERT INTO tb_permintaan_gas (kode_permintaan, id_admin, id_agen, id_produk, jumlah, catatan_admin)
            VALUES ('$kode', $id_admin, $id_agen, $id_produk, $jumlah, '$catatan')
        ");
        
        if ($query) {
            // Kirim notifikasi ke agen
            $nama_admin = $_SESSION['nama'];
            mysqli_query($koneksi, "
                INSERT INTO tb_notifikasi (id_user, judul, pesan, tipe)
                VALUES ($id_agen, 'Permintaan Gas Baru', 'Ada permintaan gas baru dari $nama_admin dengan kode $kode', 'pesanan_baru')
            ");
            
            echo "<script>alert('Request berhasil dikirim ke agen!'); window.location='index.php?page=request_gas';</script>";
            exit();
        } else {
            $error = "Gagal mengirim request!";
        }
    }
}
?>

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Buat Request Gas Baru</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="index.php?page=request_gas" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Form Request Gas
                    </h3>
                </div>
                <form method="post">
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Agen Tujuan</label>
                            <input type="text" class="form-control" value="<?php echo $data_admin['nama_agen']; ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label>Produk Gas <span class="text-danger">*</span></label>
                            <select name="id_produk" class="form-control" id="selectProduk" required>
                                <option value="">-- Pilih Produk --</option>
                                <?php 
                                mysqli_data_seek($query_produk, 0);
                                while ($produk = mysqli_fetch_assoc($query_produk)): 
                                    $stok = $stok_agen[$produk['id_produk']] ?? 0;
                                ?>
                                    <option value="<?php echo $produk['id_produk']; ?>" data-stok="<?php echo $stok; ?>">
                                        <?php echo $produk['nama_produk']; ?> - <?php echo formatRupiah($produk['harga']); ?> 
                                        (Stok Agen: <?php echo $stok; ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Jumlah Request <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah" class="form-control" min="1" required placeholder="Masukkan jumlah">
                            <small class="text-muted" id="stokInfo"></small>
                        </div>

                        <div class="form-group">
                            <label>Catatan (Opsional)</label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan untuk agen..."></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" name="submit" class="btn btn-warning">
                            <i class="fas fa-paper-plane mr-2"></i>Kirim Request
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Stok Admin -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">
                        <i class="fas fa-boxes mr-2"></i>
                        Stok Anda Saat Ini
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stok_admin = mysqli_query($koneksi, "SELECT * FROM tb_produk WHERE status = 'aktif'");
                            while ($s = mysqli_fetch_assoc($stok_admin)):
                            ?>
                            <tr>
                                <td><?php echo $s['nama_produk']; ?></td>
                                <td>
                                    <?php if ($s['stok'] < 10): ?>
                                        <span class="badge badge-danger"><?php echo $s['stok']; ?></span>
                                    <?php elseif ($s['stok'] < 30): ?>
                                        <span class="badge badge-warning"><?php echo $s['stok']; ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-success"><?php echo $s['stok']; ?></span>
                                    <?php endif; ?>
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

<script>
document.getElementById('selectProduk').addEventListener('change', function() {
    var selected = this.options[this.selectedIndex];
    var stok = selected.getAttribute('data-stok');
    if (stok) {
        document.getElementById('stokInfo').innerHTML = 'Stok tersedia di agen: <strong>' + stok + '</strong> unit';
    } else {
        document.getElementById('stokInfo').innerHTML = '';
    }
});
</script>
