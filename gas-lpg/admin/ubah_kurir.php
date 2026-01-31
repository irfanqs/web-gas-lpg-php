<?php
/**
 * =====================================================
 * File: admin/ubah_kurir.php
 * Halaman untuk mengubah data kurir
 * =====================================================
 */

// Cek parameter ID
if (!isset($_GET['id'])) {
    echo "<script>window.location='index.php?page=kelola_kurir';</script>";
    exit();
}

$id = intval($_GET['id']);
$query_kurir = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = $id AND role = 'Kurir'");
$kurir = mysqli_fetch_assoc($query_kurir);

if (!$kurir) {
    echo "<script>alert('Kurir tidak ditemukan!'); window.location='index.php?page=kelola_kurir';</script>";
    exit();
}

// Proses update kurir
if (isset($_POST['update'])) {
    $nama_depan = escapeString($_POST['nama_depan']);
    $nama_belakang = escapeString($_POST['nama_belakang']);
    $telepon = escapeString($_POST['telepon']);
    $alamat = escapeString($_POST['alamat']);
    $jk = escapeString($_POST['jk']);
    
    // Update password jika diisi
    $password_query = '';
    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        $password_query = ", password = '$password'";
    }
    
    $query = mysqli_query($koneksi, "UPDATE user SET 
                                     nama_depan = '$nama_depan', 
                                     nama_belakang = '$nama_belakang', 
                                     telepon = '$telepon', 
                                     alamat = '$alamat', 
                                     jk = '$jk' 
                                     $password_query 
                                     WHERE id_user = $id");
    
    if ($query) {
        echo "<script>alert('Kurir berhasil diupdate!'); window.location='index.php?page=kelola_kurir';</script>";
    } else {
        $error = 'Gagal mengupdate kurir!';
    }
}
?>

<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="index.php?page=kelola_kurir" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <h4><i class="fas fa-edit mr-2" style="color: #f39c12;"></i>Ubah Kurir</h4>
        </div>
    </div>

    <!-- Form Ubah -->
    <div class="card">
        <div class="card-body">
            <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Depan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_depan" 
                                   value="<?php echo $kurir['nama_depan']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Belakang</label>
                            <input type="text" class="form-control" name="nama_belakang" 
                                   value="<?php echo $kurir['nama_belakang']; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" value="<?php echo $kurir['email']; ?>" disabled>
                    <small class="text-muted">Email tidak dapat diubah</small>
                </div>
                
                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" class="form-control" name="password">
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                </div>
                
                <div class="form-group">
                    <label>Telepon <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="telepon" 
                           value="<?php echo $kurir['telepon']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea class="form-control" name="alamat" rows="2"><?php echo $kurir['alamat']; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select class="form-control" name="jk">
                        <option value="L" <?php echo ($kurir['jk'] == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="P" <?php echo ($kurir['jk'] == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>
                
                <button type="submit" name="update" class="btn btn-success">
                    <i class="fas fa-save mr-2"></i>Update
                </button>
            </form>
        </div>
    </div>
</div>
