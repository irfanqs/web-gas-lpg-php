<?php
/**
 * =====================================================
 * File: admin/tambah_produk.php
 * Halaman untuk menambah produk baru
 * =====================================================
 */

// Proses tambah produk
if (isset($_POST['tambah'])) {
    $nama = escapeString($_POST['nama_produk']);
    $deskripsi = escapeString($_POST['deskripsi']);
    $harga = floatval($_POST['harga']);
    $stok = intval($_POST['stok']);
    $status = escapeString($_POST['status']);
    
    // Upload gambar
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = 'produk_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], '../uploads/produk/' . $gambar);
    }
    
    $query = mysqli_query($koneksi, "INSERT INTO tb_produk (nama_produk, deskripsi, harga, stok, gambar, status) 
                                     VALUES ('$nama', '$deskripsi', $harga, $stok, '$gambar', '$status')");
    
    if ($query) {
        echo "<script>alert('Produk berhasil ditambahkan!'); window.location='index.php?page=kelola_produk';</script>";
    } else {
        $error = 'Gagal menambahkan produk!';
    }
}
?>

<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="index.php?page=kelola_produk" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <h4><i class="fas fa-plus mr-2" style="color: #f39c12;"></i>Tambah Produk</h4>
        </div>
    </div>

    <!-- Form Tambah -->
    <div class="card">
        <div class="card-body">
            <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama_produk" required>
                </div>
                
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea class="form-control" name="deskripsi" rows="3"></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Harga <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="harga" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="stok" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Gambar Produk</label>
                    <input type="file" class="form-control" name="gambar" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="status">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
                
                <button type="submit" name="tambah" class="btn btn-success">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </form>
        </div>
    </div>
</div>
