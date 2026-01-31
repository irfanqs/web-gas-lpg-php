<?php
/**
 * =====================================================
 * File: admin/tambah_kurir.php
 * Halaman untuk menambah kurir baru
 * =====================================================
 */

// Proses tambah kurir
if (isset($_POST['tambah'])) {
    $nama_depan = escapeString($_POST['nama_depan']);
    $nama_belakang = escapeString($_POST['nama_belakang']);
    $email = escapeString($_POST['email']);
    $password = $_POST['password'];
    $telepon = escapeString($_POST['telepon']);
    $alamat = escapeString($_POST['alamat']);
    $jk = escapeString($_POST['jk']);
    
    // Cek email sudah ada atau belum
    $cek = mysqli_query($koneksi, "SELECT * FROM user WHERE email = '$email'");
    if (mysqli_num_rows($cek) > 0) {
        $error = 'Email sudah terdaftar!';
    } else {
        $query = mysqli_query($koneksi, "INSERT INTO user (nama_depan, nama_belakang, email, password, telepon, alamat, jk, role) 
                                         VALUES ('$nama_depan', '$nama_belakang', '$email', '$password', '$telepon', '$alamat', '$jk', 'Kurir')");
        
        if ($query) {
            echo "<script>alert('Kurir berhasil ditambahkan!'); window.location='index.php?page=kelola_kurir';</script>";
        } else {
            $error = 'Gagal menambahkan kurir!';
        }
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
            <h4><i class="fas fa-plus mr-2" style="color: #f39c12;"></i>Tambah Kurir</h4>
        </div>
    </div>

    <!-- Form Tambah -->
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
                            <input type="text" class="form-control" name="nama_depan" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Belakang</label>
                            <input type="text" class="form-control" name="nama_belakang">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                
                <div class="form-group">
                    <label>Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                
                <div class="form-group">
                    <label>Telepon <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="telepon" required>
                </div>
                
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea class="form-control" name="alamat" rows="2"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select class="form-control" name="jk">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                
                <button type="submit" name="tambah" class="btn btn-success">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </form>
        </div>
    </div>
</div>
