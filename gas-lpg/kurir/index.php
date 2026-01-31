<?php
/**
 * =====================================================
 * File: kurir/index.php
 * Halaman utama untuk kurir (layout dengan sidebar)
 * =====================================================
 */

// Include koneksi database
include '../koneksi/koneksi.php';

// Mulai session
session_start();

// =====================================================
// CEK AUTENTIKASI
// Pastikan user sudah login dan role-nya Kurir
// =====================================================
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Kurir') {
    echo "<script>
            alert('Maaf anda belum login atau tidak memiliki akses!');
            window.location='../login.php'
          </script>";
    exit();
}

// Ambil halaman yang diminta dari parameter URL
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Hitung notifikasi yang belum dibaca
$id_user = $_SESSION['id_user'];
$query_notif = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_notifikasi WHERE id_user = $id_user AND is_read = 0");
$notif_count = mysqli_fetch_assoc($query_notif)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo APP_NAME; ?> | Kurir</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <!-- Theme style AdminLTE -->
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
    <!-- Custom Style -->
    <link rel="stylesheet" href="../assets/dist/css/style.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- =====================================================
         NAVBAR (Header Atas)
         ===================================================== -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Tombol Toggle Sidebar (Hamburger Menu) -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars" style="color: #f39c12;"></i>
                </a>
            </li>
        </ul>

        <!-- Menu Kanan Navbar -->
        <ul class="navbar-nav ml-auto">
            <!-- Notifikasi -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="fas fa-bell" style="color: #f39c12;"></i>
                    <?php if ($notif_count > 0): ?>
                    <span class="badge badge-danger navbar-badge"><?php echo $notif_count; ?></span>
                    <?php endif; ?>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-header"><?php echo $notif_count; ?> Notifikasi Baru</span>
                    <div class="dropdown-divider"></div>
                    <a href="index.php?page=notifikasi" class="dropdown-item dropdown-footer">Lihat Semua Notifikasi</a>
                </div>
            </li>
            <!-- Nama User -->
            <li class="nav-item mr-3">
                <span class="nav-link">
                    <i class="fas fa-user mr-2" style="color: #f39c12;"></i>
                    <?php echo $_SESSION['nama']; ?>
                </span>
            </li>
            <!-- Tombol Logout -->
            <li class="nav-item">
                <a class="nav-link" href="../logout.php" title="Logout">
                    <i class="fas fa-power-off" style="color: #f39c12;"></i>
                </a>
            </li>
        </ul>
    </nav>

    <!-- =====================================================
         SIDEBAR (Menu Samping)
         ===================================================== -->
    <aside class="main-sidebar sidebar-dark-warning elevation-4" style="background-color: #1e272e;">
        <!-- Logo Brand -->
        <a href="index.php?page=home" class="brand-link">
            <img src="../assets/dist/img/logo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span style="color: #f39c12;">Gas LPG</span> 
            <span style="color: #f1f2f6;">Delivery</span>
        </a>

        <!-- Sidebar Menu -->
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    
                    <!-- Menu: Dashboard -->
                    <li class="nav-item">
                        <a href="index.php?page=home" class="nav-link <?php echo ($page == 'home') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <!-- Menu: Pesanan untuk Diantar -->
                    <li class="nav-item">
                        <a href="index.php?page=pesanan_antar" class="nav-link <?php echo ($page == 'pesanan_antar' || $page == 'detail_antar') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-truck" style="color: #F79F1F;"></i>
                            <p>Pesanan untuk Diantar</p>
                        </a>
                    </li>

                    <!-- Menu: Sedang Diantar -->
                    <li class="nav-item">
                        <a href="index.php?page=sedang_antar" class="nav-link <?php echo ($page == 'sedang_antar') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-motorcycle" style="color: #F79F1F;"></i>
                            <p>Sedang Diantar</p>
                        </a>
                    </li>

                    <!-- Menu: Riwayat Pengantaran -->
                    <li class="nav-item">
                        <a href="index.php?page=riwayat" class="nav-link <?php echo ($page == 'riwayat') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-history" style="color: #F79F1F;"></i>
                            <p>Riwayat Pengantaran</p>
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
    </aside>

    <!-- =====================================================
         CONTENT WRAPPER (Area Konten Utama)
         ===================================================== -->
    <div class="content-wrapper">
        <div class="content-header">
            <?php
            // =====================================================
            // ROUTING HALAMAN
            // Include file sesuai parameter 'page'
            // =====================================================
            switch ($page) {
                case 'home':
                    include 'home.php';
                    break;
                case 'pesanan_antar':
                    include 'pesanan_antar.php';
                    break;
                case 'detail_antar':
                    include 'detail_antar.php';
                    break;
                case 'sedang_antar':
                    include 'sedang_antar.php';
                    break;
                case 'riwayat':
                    include 'riwayat.php';
                    break;
                case 'notifikasi':
                    include 'notifikasi.php';
                    break;
                default:
                    include 'home.php';
                    break;
            }
            ?>
        </div>
    </div>

    <!-- =====================================================
         FOOTER
         ===================================================== -->
    <footer class="main-footer">
        <strong>Copyright &copy; <?php echo date('Y'); ?></strong> 
        <span><?php echo APP_NAME; ?></span>
    </footer>

</div>

<!-- =====================================================
     JAVASCRIPT LIBRARIES
     ===================================================== -->
<!-- jQuery -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<!-- AdminLTE App -->
<script src="../assets/dist/js/adminlte.js"></script>

<!-- Inisialisasi DataTables -->
<script>
$(function () {
    $("#dataTable").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "language": {
            "search": "Cari:",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            },
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "emptyTable": "Tidak ada data tersedia"
        }
    });
});
</script>

</body>
</html>
