<?php
/**
 * =====================================================
 * File: regis.php
 * Halaman registrasi dengan Google reCAPTCHA v2
 * =====================================================
 */

require 'koneksi/koneksi.php';
session_start();
error_reporting(0);

// Cek jika sudah login
if (isset($_SESSION['role']) && $_SESSION['role'] != '') {
    alertRedirect('Anda sudah login!', 'logout.php');
}

// =====================================================
// PROSES REGISTRASI
// =====================================================
$error_message = '';
$success_message = '';

if (isset($_POST['register'])) {
    // Ambil data dari form
    $nama_depan = escapeString($_POST['nama_depan']);
    $nama_belakang = escapeString($_POST['nama_belakang']);
    $email = escapeString($_POST['email']);
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    $telepon = escapeString($_POST['telepon']);
    $alamat = escapeString($_POST['alamat']);
    $jk = escapeString($_POST['jk']);
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    
    // Validasi reCAPTCHA v2
    if (empty($recaptcha_response)) {
        $error_message = 'Silakan centang captcha terlebih dahulu!';
    } elseif (!validateRecaptcha($recaptcha_response)) {
        $error_message = 'Verifikasi captcha gagal. Silakan coba lagi!';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $nama_depan)) {
        // Validasi nama depan hanya huruf
        $error_message = 'Nama depan hanya boleh berisi huruf!';
    } elseif (!empty($nama_belakang) && !preg_match('/^[a-zA-Z\s]+$/', $nama_belakang)) {
        // Validasi nama belakang hanya huruf (jika diisi)
        $error_message = 'Nama belakang hanya boleh berisi huruf!';
    } elseif (!preg_match('/^[0-9]+$/', $telepon)) {
        // Validasi telepon hanya angka
        $error_message = 'Nomor telepon hanya boleh berisi angka!';
    } elseif (strlen($telepon) < 10 || strlen($telepon) > 15) {
        // Validasi panjang telepon
        $error_message = 'Nomor telepon harus 10-15 digit!';
    } elseif ($password !== $konfirmasi_password) {
        // Validasi password cocok
        $error_message = 'Password dan konfirmasi password tidak cocok!';
    } elseif (strlen($password) < 6) {
        // Validasi panjang password
        $error_message = 'Password minimal 6 karakter!';
    } else {
        // Cek email sudah terdaftar atau belum
        $cek_email = mysqli_query($koneksi, "SELECT * FROM user WHERE email='$email'");
        
        if (mysqli_num_rows($cek_email) > 0) {
            $error_message = 'Email sudah terdaftar! Silakan gunakan email lain.';
        } else {
            // Insert data user baru ke database
            $query = mysqli_query($koneksi, "INSERT INTO user (nama_depan, nama_belakang, email, password, telepon, alamat, jk, role) 
                                             VALUES ('$nama_depan', '$nama_belakang', '$email', '$password', '$telepon', '$alamat', '$jk', 'Pembeli')");
            
            if ($query) {
                $success_message = 'Registrasi berhasil! Silakan login.';
            } else {
                $error_message = 'Registrasi gagal. Silakan coba lagi!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo APP_NAME; ?> | Registrasi</title>
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <!-- iCheck Bootstrap -->
    <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/dist/css/style.css">
    <!-- reCAPTCHA v2 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body class="hold-transition register-page" style="background-color:#222f3e;">

<div class="register-box" style="width: 450px;">
    <!-- Logo -->
    <div class="register-logo">
        <a href="#">
            <b style="color: #f39c12;">Gas LPG</b> 
            <span style="color:#dfe6e9;">Website</span>
        </a>
    </div>

    <div class="card">
        <div class="card-body register-card-body" style="background-color:#2c3e50;">
            <p class="login-box-msg" style="color:#dfe6e9;">Daftar Akun Baru</p>

            <!-- Pesan Error -->
            <?php if ($error_message != ''): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo $error_message; ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <!-- Pesan Sukses -->
            <?php if ($success_message != ''): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo $success_message; ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <!-- Form Registrasi -->
            <form action="" method="post">
                <!-- Nama Depan & Belakang -->
                <div class="row">
                    <div class="col-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Nama Depan" name="nama_depan" 
                                   pattern="[a-zA-Z\s]+" title="Nama depan hanya boleh berisi huruf" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user" style="color: #f39c12;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Nama Belakang" name="nama_belakang"
                                   pattern="[a-zA-Z\s]*" title="Nama belakang hanya boleh berisi huruf">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user" style="color: #f39c12;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Email" name="email" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope" style="color: #f39c12;"></span>
                        </div>
                    </div>
                </div>

                <!-- Nomor Telepon -->
                <div class="input-group mb-3">
                    <input type="tel" class="form-control" placeholder="Nomor Telepon" name="telepon" 
                           pattern="[0-9]{10,15}" title="Nomor telepon harus 10-15 digit angka" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-phone" style="color: #f39c12;"></span>
                        </div>
                    </div>
                </div>

                <!-- Alamat -->
                <div class="input-group mb-3">
                    <textarea class="form-control" placeholder="Alamat Lengkap" name="alamat" rows="2" required></textarea>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-map-marker-alt" style="color: #f39c12;"></span>
                        </div>
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div class="input-group mb-3">
                    <select class="form-control" name="jk" required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-venus-mars" style="color: #f39c12;"></span>
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Password (min. 6 karakter)" name="password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock" style="color: #f39c12;"></span>
                        </div>
                    </div>
                </div>

                <!-- Konfirmasi Password -->
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Konfirmasi Password" name="konfirmasi_password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock" style="color: #f39c12;"></span>
                        </div>
                    </div>
                </div>

                <!-- reCAPTCHA v2 Checkbox -->
                <div class="mb-3 d-flex justify-content-center">
                    <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>" data-theme="dark"></div>
                </div>

                <!-- Tombol Daftar -->
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-block" name="register" style="background-color: #e74c3c; color:#ecf0f1;">
                            <i class="fas fa-user-plus mr-2"></i>Daftar
                        </button>
                    </div>
                </div>
            </form>

            <!-- Link ke Login -->
            <div class="text-center mt-4">
                <p style="color:#dfe6e9;">Sudah punya akun? 
                    <a href="login.php" style="color:#F79F1F;">Login di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/dist/js/adminlte.min.js"></script>

</body>
</html>
