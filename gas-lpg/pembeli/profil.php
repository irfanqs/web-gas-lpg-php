<?php
/**
 * =====================================================
 * File: pembeli/profil.php
 * Halaman profil untuk pembeli mengubah data diri
 * =====================================================
 */

$id_user = $_SESSION['id_user'];

// Ambil data user
$query_user = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = $id_user");
$user = mysqli_fetch_assoc($query_user);

$success_message = '';
$error_message = '';

// Proses update profil
if (isset($_POST['update_profil'])) {
    $telepon = escapeString($_POST['telepon']);
    $alamat = escapeString($_POST['alamat']);
    
    // Validasi telepon hanya angka
    if (!preg_match('/^[0-9]+$/', $telepon)) {
        $error_message = 'Nomor telepon hanya boleh berisi angka!';
    } elseif (strlen($telepon) < 10 || strlen($telepon) > 15) {
        $error_message = 'Nomor telepon harus 10-15 digit!';
    } elseif (empty($alamat)) {
        $error_message = 'Alamat tidak boleh kosong!';
    } else {
        $query_update = mysqli_query($koneksi, "
            UPDATE user SET telepon = '$telepon', alamat = '$alamat' WHERE id_user = $id_user
        ");
        
        if ($query_update) {
            $success_message = 'Profil berhasil diperbarui!';
            // Refresh data user
            $query_user = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = $id_user");
            $user = mysqli_fetch_assoc($query_user);
        } else {
            $error_message = 'Gagal memperbarui profil!';
        }
    }
}

// Proses ubah password
if (isset($_POST['ubah_password'])) {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    
    if ($password_lama != $user['password']) {
        $error_message = 'Password lama salah!';
    } elseif (strlen($password_baru) < 6) {
        $error_message = 'Password baru minimal 6 karakter!';
    } elseif ($password_baru != $konfirmasi_password) {
        $error_message = 'Konfirmasi password tidak cocok!';
    } else {
        mysqli_query($koneksi, "UPDATE user SET password = '$password_baru' WHERE id_user = $id_user");
        $success_message = 'Password berhasil diubah!';
    }
}
?>

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-user-cog mr-2" style="color: #f39c12;"></i>Profil Saya</h4>
            <p class="text-muted">Kelola informasi akun Anda</p>
        </div>
    </div>

    <?php if ($success_message): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-2"></i><?php echo $success_message; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle mr-2"></i><?php echo $error_message; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Card Info Profil -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header" style="background-color: #2c3e50; color: white;">
                    <h5 class="mb-0"><i class="fas fa-user mr-2"></i>Informasi Profil</h5>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" class="form-control" 
                                   value="<?php echo $user['nama_depan'] . ' ' . $user['nama_belakang']; ?>" 
                                   readonly style="background-color: #e9ecef;">
                            <small class="text-muted">Nama tidak dapat diubah</small>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" value="<?php echo $user['email']; ?>" 
                                   readonly style="background-color: #e9ecef;">
                            <small class="text-muted">Email tidak dapat diubah</small>
                        </div>
                        <div class="form-group">
                            <label>Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="telepon" 
                                   value="<?php echo $user['telepon']; ?>" 
                                   pattern="[0-9]{10,15}" required>
                        </div>
                        <div class="form-group">
                            <label>Alamat Pengantaran <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="alamat" rows="3" required><?php echo $user['alamat']; ?></textarea>
                            <small class="text-muted">Alamat ini akan digunakan saat checkout</small>
                        </div>
                        <button type="submit" name="update_profil" class="btn btn-primary btn-block">
                            <i class="fas fa-save mr-2"></i>Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Card Ubah Password -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header" style="background-color: #e74c3c; color: white;">
                    <h5 class="mb-0"><i class="fas fa-lock mr-2"></i>Ubah Password</h5>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label>Password Lama</label>
                            <input type="password" class="form-control" name="password_lama" required>
                        </div>
                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" class="form-control" name="password_baru" 
                                   minlength="6" required>
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>
                        <div class="form-group">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" name="konfirmasi_password" required>
                        </div>
                        <button type="submit" name="ubah_password" class="btn btn-danger btn-block">
                            <i class="fas fa-key mr-2"></i>Ubah Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
