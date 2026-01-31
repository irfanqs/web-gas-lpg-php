<?php
/**
 * =====================================================
 * File: agen/index.php
 * Halaman utama untuk agen (layout dengan sidebar)
 * =====================================================
 */

include '../koneksi/koneksi.php';
session_start();

// Cek autentikasi
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Agen') {
    echo "<script>
            alert('Maaf anda belum login atau tidak memiliki akses!');
            window.location='../login.php'
          </script>";
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$id_user = $_SESSION['id_user'];

// Hitung notifikasi belum dibaca
$query_notif = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_notifikasi WHERE id_user = $id_user AND is_read = 0");
$notif_count = mysqli_fetch_assoc($query_notif)['total'];

// Hitung permintaan menunggu
$query_pending = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_permintaan_gas WHERE id_agen = $id_user AND status = 'menunggu'");
$pending_count = mysqli_fetch_assoc($query_pending)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo APP_NAME; ?> | Agen</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../assets/dist/css/style.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars" style="color: #27ae60;"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <!-- Notifikasi -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="fas fa-bell" style="color: #27ae60;"></i>
                    <?php if ($notif_count > 0): ?>
                    <span class="badge badge-danger navbar-badge"><?php echo $notif_count; ?></span>
                    <?php endif; ?>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-header"><?php echo $notif_count; ?> Notifikasi Baru</span>
                    <div class="dropdown-divider"></div>
                    <a href="index.php?page=notifikasi" class="dropdown-item dropdown-footer">Lihat Semua</a>
                </div>
            </li>
            <li class="nav-item mr-3">
                <span class="nav-link">
                    <i class="fas fa-user mr-2" style="color: #27ae60;"></i>
                    <?php echo $_SESSION['nama']; ?>
                </span>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php" title="Logout">
                    <i class="fas fa-power-off" style="color: #27ae60;"></i>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-success elevation-4" style="background-color: #1e272e;">
        <a href="index.php?page=home" class="brand-link">
            <img src="../assets/dist/img/logo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span style="color: #27ae60;">Gas LPG</span> 
            <span style="color: #f1f2f6;">Agen</span>
        </a>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="index.php?page=home" class="nav-link <?php echo ($page == 'home') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <!-- Kelola Stok -->
                    <li class="nav-item <?php echo in_array($page, ['stok', 'tambah_stok', 'riwayat_stok']) ? 'menu-open' : ''; ?>">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-boxes" style="color: #27ae60;"></i>
                            <p>
                                Kelola Stok
                                <i class="right fas fa-angle-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="index.php?page=stok" class="nav-link <?php echo ($page == 'stok') ? 'active' : ''; ?>">
                                    <i class="fas fa-warehouse nav-icon" style="color: #27ae60;"></i>
                                    <p>Stok Saat Ini</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?page=tambah_stok" class="nav-link <?php echo ($page == 'tambah_stok') ? 'active' : ''; ?>">
                                    <i class="fas fa-plus-circle nav-icon" style="color: #27ae60;"></i>
                                    <p>Input Stok Masuk</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?page=riwayat_stok" class="nav-link <?php echo ($page == 'riwayat_stok') ? 'active' : ''; ?>">
                                    <i class="fas fa-history nav-icon" style="color: #27ae60;"></i>
                                    <p>Riwayat Stok</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Permintaan Gas -->
                    <li class="nav-item">
                        <a href="index.php?page=permintaan" class="nav-link <?php echo in_array($page, ['permintaan', 'detail_permintaan']) ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-clipboard-list" style="color: #27ae60;"></i>
                            <p>
                                Permintaan Gas
                                <?php if ($pending_count > 0): ?>
                                <span class="badge badge-warning right"><?php echo $pending_count; ?></span>
                                <?php endif; ?>
                            </p>
                        </a>
                    </li>

                    <!-- Distribusi -->
                    <li class="nav-item">
                        <a href="index.php?page=distribusi" class="nav-link <?php echo ($page == 'distribusi') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-truck" style="color: #27ae60;"></i>
                            <p>Riwayat Distribusi</p>
                        </a>
                    </li>

                    <!-- Laporan -->
                    <li class="nav-item">
                        <a href="index.php?page=laporan" class="nav-link <?php echo ($page == 'laporan') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-file-invoice" style="color: #27ae60;"></i>
                            <p>Laporan</p>
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content -->
    <div class="content-wrapper">
        <div class="content-header">
            <?php
            switch ($page) {
                case 'home':
                    include 'home.php';
                    break;
                case 'stok':
                    include 'stok.php';
                    break;
                case 'tambah_stok':
                    include 'tambah_stok.php';
                    break;
                case 'riwayat_stok':
                    include 'riwayat_stok.php';
                    break;
                case 'permintaan':
                    include 'permintaan.php';
                    break;
                case 'detail_permintaan':
                    include 'detail_permintaan.php';
                    break;
                case 'distribusi':
                    include 'distribusi.php';
                    break;
                case 'laporan':
                    include 'laporan.php';
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

    <footer class="main-footer">
        <strong>Copyright &copy; <?php echo date('Y'); ?></strong> 
        <span><?php echo APP_NAME; ?> - Agen Panel</span>
    </footer>

</div>

<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/dist/js/adminlte.js"></script>

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
