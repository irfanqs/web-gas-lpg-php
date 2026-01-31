<?php
/**
 * =====================================================
 * File: login.php
 * Halaman login dengan Google reCAPTCHA v2
 * =====================================================
 */

require 'koneksi/koneksi.php';
session_start();

// Cek jika sudah login
if (isset($_SESSION['role']) && $_SESSION['role'] != '') {
    switch ($_SESSION['role']) {
        case 'Admin':
            header('location:admin/index.php?page=home');
            exit();
        case 'Kurir':
            header('location:kurir/index.php?page=home');
            exit();
        case 'Pembeli':
            header('location:pembeli/index.php?page=home');
            exit();
    }
}

// Proses login
$error_message = '';

if (isset($_POST['login'])) {
    $email = escapeString($_POST['email']);
    $password = $_POST['password'];
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    
    // Validasi reCAPTCHA v2
    if (empty($recaptcha_response)) {
        $error_message = 'Silakan centang captcha terlebih dahulu!';
    } elseif (!validateRecaptcha($recaptcha_response)) {
        $error_message = 'Verifikasi captcha gagal. Silakan coba lagi!';
    } else {
        // Query untuk mencari user
        $query = mysqli_query($koneksi, "SELECT * FROM user WHERE email='$email'");
        $cek = mysqli_num_rows($query);
        
        if ($cek > 0) {
            $data = mysqli_fetch_assoc($query);
            
            if ($password === $data['password']) {
                // Login berhasil
                $_SESSION['id_user'] = $data['id_user'];
                $_SESSION['nama'] = $data['nama_depan'] . ' ' . $data['nama_belakang'];
                $_SESSION['email'] = $data['email'];
                $_SESSION['role'] = $data['role'];
                
                switch ($data['role']) {
                    case 'Admin':
                        header('location:admin/index.php?page=home');
                        exit();
                    case 'Kurir':
                        header('location:kurir/index.php?page=home');
                        exit();
                    case 'Pembeli':
                        header('location:pembeli/index.php?page=home');
                        exit();
                }
            } else {
                $error_message = 'Password salah!';
            }
        } else {
            $error_message = 'Email tidak terdaftar!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo APP_NAME; ?> | Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="assets/dist/css/style.css">
    <!-- reCAPTCHA v2 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body class="hold-transition login-page" style="background-color:#222f3e;">

<div class="login-box">
    <div class="login-logo">
        <a href="#">
            <b style="color: #f39c12;">Gas LPG</b> 
            <span style="color:#dfe6e9;">Website</span>
        </a>
    </div>

    <div class="card">
        <div class="card-body login-card-body" style="background-color:#2c3e50;">
            <p class="login-box-msg" style="color:#dfe6e9;">Masuk Untuk Memulai Sesi Anda</p>

            <?php if ($error_message != ''): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo $error_message; ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <form action="" method="post">
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Email" name="email" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope" style="color: #f39c12;"></span>
                        </div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Password" name="password" required>
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

                <div class="row">
                    <div class="col-7">
                        <a href="lupa_password.php" style="color:#F79F1F;">Lupa Password?</a>
                    </div>
                    <div class="col-5">
                        <button type="submit" class="btn btn-block" name="login" style="background-color: #e74c3c; color:#ecf0f1;">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </button>
                    </div>
                </div>
            </form>

            <div class="social-auth-links text-center mb-3 mt-4">
                <p style="color:#dfe6e9;">- Belum Punya Akun? -</p>
                <a href="regis.php" class="btn btn-block" style="background-color: #e67e22; color:#ecf0f1;">
                    <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                </a>
            </div>
        </div>
    </div>
</div>

<script src="assets/plugins/jquery/jquery.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/dist/js/adminlte.min.js"></script>

</body>
</html>
