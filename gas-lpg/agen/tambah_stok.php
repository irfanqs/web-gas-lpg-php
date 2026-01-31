<?php
/**
 * =====================================================
 * File: agen/tambah_stok.php
 * Halaman input stok masuk
 * =====================================================
 */

$id_agen = $_SESSION['id_user'];

// Ambil daftar produk
$query_produk = mysqli_query($koneksi, "SELECT * FROM tb_produk WHERE status = 'aktif' ORDER BY nama_produk");

// Proses tambah stok
if (isset($_POST['tambah_stok'])) {
    $id_produk = (int)$_POST['id_produk'];
    $jumlah = (int)$_POST['jumlah'];
    $keterangan = escapeString($_POST['keterangan']);
    
    if ($jumlah <= 0) {
        $error = "Jumlah harus lebih dari 0!";
    } else {
        // Cek apakah sudah ada stok untuk produk ini
        $cek = mysqli_query($koneksi, "SELECT * FROM tb_stok_agen WHERE id_agen = $id_agen AND id_produk = $id_produk");
        
        if (mysqli_num_rows($cek) > 0) {
            $stok_lama = mysqli_fetch_assoc($cek);
            $stok_sebelum = $stok_lama['jumlah_stok'];
            $stok_sesudah = $stok_sebelum + $jumlah;
            
            // Update stok
            mysqli_query($koneksi, "UPDATE tb_stok_agen SET jumlah_stok = $stok_sesudah WHERE id_agen = $id_agen AND id_produk = $id_produk");
        } else {
            $stok_sebelum = 0;
            $stok_sesudah = $jumlah;
            
            // Insert stok baru
            mysqli_query($koneksi, "INSERT INTO tb_stok_agen (id_agen, id_produk, jumlah_stok) VALUES ($id_agen, $id_produk, $jumlah)");
        }
        
        // Catat riwayat
        mysqli_query($koneksi, "
            INSERT INTO tb_riwayat_stok (id_agen, id_produk, tipe, jumlah, stok_sebelum, stok_sesudah, keterangan)
            VALUES ($id_agen, $id_produk, 'masuk', $jumlah, $stok_sebelum, $stok_sesudah, '$keterangan')
        ");
        
        $success = "Stok berhasil ditambahkan!";
    }
}
?>

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Input Stok Masuk</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="index.php?page=stok" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Form Input Stok
                    </h3>
                </div>
                <form method="post">
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Produk Gas <span class="text-danger">*</span></label>
                            <select name="id_produk" class="form-control" required>
                                <option value="">-- Pilih Produk --</option>
                                <?php while ($produk = mysqli_fetch_assoc($query_produk)): ?>
                                    <option value="<?php echo $produk['id_produk']; ?>">
                                        <?php echo $produk['nama_produk']; ?> - <?php echo formatRupiah($produk['harga']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Jumlah Stok Masuk <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah" class="form-control" min="1" required placeholder="Masukkan jumlah">
                        </div>

                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Stok dari Pertamina, No. DO: xxx"></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" name="tambah_stok" class="btn btn-success">
                            <i class="fas fa-save mr-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Stok Saat Ini -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-2"></i>
                        Stok Saat Ini
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stok_query = mysqli_query($koneksi, "
                                SELECT p.nama_produk, COALESCE(sa.jumlah_stok, 0) as stok
                                FROM tb_produk p
                                LEFT JOIN tb_stok_agen sa ON p.id_produk = sa.id_produk AND sa.id_agen = $id_agen
                                WHERE p.status = 'aktif'
                            ");
                            while ($s = mysqli_fetch_assoc($stok_query)):
                            ?>
                            <tr>
                                <td><?php echo $s['nama_produk']; ?></td>
                                <td>
                                    <?php if ($s['stok'] < 50): ?>
                                        <span class="badge badge-danger"><?php echo $s['stok']; ?></span>
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
