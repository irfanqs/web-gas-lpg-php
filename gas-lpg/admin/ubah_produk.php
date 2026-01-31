<?php
/**
 * =====================================================
 * File: admin/ubah_produk.php
 * Halaman untuk mengubah data produk
 * =====================================================
 */

// Cek parameter ID
if (!isset($_GET['id'])) {
    echo "<script>window.location='index.php?page=kelola_produk';</script>";
    exit();
}

$id = intval($_GET['id']);
$query_produk = mysqli_query($koneksi, "SELECT * FROM tb_produk WHERE id_produk = $id");
$produk = mysqli_fetch_assoc($query_produk);

if (!$produk) {
    echo "<script>alert('Produk tidak ditemukan!'); window.location='index.php?page=kelola_produk';</script>";
    exit();
}

// Proses update produk
if (isset($_POST['update'])) {
    $nama = escapeString($_POST['nama_produk']);
    $deskripsi = escapeString($_POST['deskripsi']);
    $harga = floatval($_POST['harga']);
    $status = escapeString($_POST['status']);
    
    // Upload gambar baru jika ada
    $gambar_query = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = 'produk_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], '../uploads/produk/' . $gambar);
        $gambar_query = ", gambar = '$gambar'";
    }
    
    // Stok tidak bisa diubah manual, hanya dari distribusi agen
    $query = mysqli_query($koneksi, "UPDATE tb_produk SET 
                                     nama_produk = '$nama', 
                                     deskripsi = '$deskripsi', 
                                     harga = $harga, 
                                     status = '$status' 
                                     $gambar_query 
                                     WHERE id_produk = $id");
    
    if ($query) {
        echo "<script>alert('Produk berhasil diupdate!'); window.location='index.php?page=kelola_produk';</script>";
    } else {
        $error = 'Gagal mengupdate produk!';
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
            <h4><i class="fas fa-edit mr-2" style="color: #f39c12;"></i>Ubah Produk</h4>
        </div>
    </div>

    <!-- Form Ubah -->
    <div class="card">
        <div class="card-body">
            <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama_produk" 
                           value="<?php echo $produk['nama_produk']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea class="form-control" name="deskripsi" rows="3"><?php echo $produk['deskripsi']; ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Harga <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="harga" 
                                   value="<?php echo $produk['harga']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Stok Saat Ini</label>
                            <input type="number" class="form-control" 
                                   value="<?php echo $produk['stok']; ?>" disabled>
                            <small class="text-muted">Stok hanya bisa bertambah dari distribusi agen. <a href="index.php?page=request_gas">Request Gas</a></small>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Gambar Produk</label>
                    <?php if ($produk['gambar']): ?>
                    <div class="mb-2">
                        <img src="../uploads/produk/<?php echo $produk['gambar']; ?>" 
                             alt="Gambar saat ini" style="max-height: 100px;">
                        <p class="text-muted">Gambar saat ini</p>
                    </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" name="gambar" accept="image/*">
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar</small>
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="status">
                        <option value="aktif" <?php echo ($produk['status'] == 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                        <option value="nonaktif" <?php echo ($produk['status'] == 'nonaktif') ? 'selected' : ''; ?>>Nonaktif</option>
                    </select>
                </div>
                
                <button type="submit" name="update" class="btn btn-success">
                    <i class="fas fa-save mr-2"></i>Update
                </button>
            </form>
        </div>
    </div>
</div>
