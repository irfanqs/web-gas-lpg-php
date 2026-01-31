<?php
/**
 * =====================================================
 * File: lupa_password.php
 * Halaman untuk mencari password berdasarkan email
 * =====================================================
 */

require 'koneksi/koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo APP_NAME; ?> | Lupa Password</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
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
            <p class="login-box-msg" style="color:#dfe6e9;">Masukkan Email Untuk Mencari Password Anda</p>

            <form action="" method="post">
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Masukkan Email Anda" name="email" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope" style="color: #f39c12;"></span>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-block" name="cari" style="background-color: #e74c3c; color:#ecf0f1;">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
            </form>

            <?php
            if (isset($_POST['cari'])) {
                $email = escapeString($_POST['email']);
                $query = mysqli_query($koneksi, "SELECT nama_depan, nama_belakang, email, password FROM user WHERE email='$email'");
                $cek = mysqli_num_rows($query);
                
                if ($cek > 0) {
                    $data = mysqli_fetch_assoc($query);
            ?>
            <div class="mt-4 p-3" style="background-color: #34495e; border-radius: 5px;">
                <h6 style="color: #f39c12;">Data Ditemukan:</h6>
                <table style="color:#dfe6e9; width: 100%;">
                    <tr>
                        <td width="30%">Nama</td>
                        <td width="5%">:</td>
                        <td><?php echo $data['nama_depan'] . ' ' . $data['nama_belakang']; ?></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td><?php echo $data['email']; ?></td>
                    </tr>
                    <tr>
                        <td>Password</td>
                        <td>:</td>
                        <td><?php echo $data['password']; ?></td>
                    </tr>
                </table>
            </div>
            <?php 
                } else { 
            ?>
            <div class="alert alert-danger mt-3">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Email tidak ditemukan!
            </div>
            <?php 
                } 
            } 
            ?>

            <div class="mt-4">
                <a href="login.php" class="btn btn-block" style="background-color: #e67e22; color:#ecf0f1;">
                    <i class="fas fa-sign-in-alt mr-2"></i>Kembali ke Login
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
