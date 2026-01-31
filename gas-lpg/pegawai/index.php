<?php
  include '../koneksi/koneksi.php';

  session_start();

    if($_SESSION['role'] == '')
    {
        echo "<script>
                alert('Maaf anda belom login, silahkan login terlebih dahulu');
                window.location='../login.php'
            </script>";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Celluer Phone | Pegawai</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="../assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="../assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="../assets/plugins/jqvmap/jqvmap.min.css">
   <!-- DataTables -->
  <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="../assets/plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../assets/plugins/summernote/summernote-bs4.min.css">
  <!-- Custom Style -->
  <link rel="stylesheet" href="../assets/dist/css/style.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars" style="color: #f39c12;"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="../logout.php">
          <i class="fas fa-power-off" style="color: #f39c12;"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-warning elevation-4" style="background-color: #1e272e;">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="../assets/dist/img/logo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span style="color: #f39c12;">Celuller</span> <span style="color: #f1f2f6;">Phone</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <?php $page = isset($_GET['page']) ? $_GET['page'] : 'home'; ?>
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="index.php?page=home" class="nav-link <?php echo ($page == 'home' || $page == '') ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?page=pesanan" class="nav-link <?php echo in_array($page, ['pesanan', 'pembayaran', 'cetak']) ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-shopping-cart"></i>
              <p>
                Pesanan Pelanggan
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?page=kelola_pesanan" class="nav-link <?php echo in_array($page, ['kelola_pesanan', 'detail_pesanan', 'ubah_pesanan', 'tambah_pesanan', 'hapus_pesanan']) ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-user-tag"></i>
              <p>
                Kelola Pesanan
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?page=kelola_transaksi" class="nav-link <?php echo in_array($page, ['kelola_transaksi', 'ubah_transaksi', 'tambah_transaksi', 'hapus_transaksi']) ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-receipt"></i>
              <p>
                Kelola Transaksi
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?page=pegawai" class="nav-link <?php echo in_array($page, ['pegawai', 'ubah_pegawai', 'tambah_pegawai', 'hapus_pegawai', 'detail_pegawai']) ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-user"></i>
              <p>
                Kelola Pegawai
              </p>
            </a>
          </li>

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
    <?php
        if(isset($_GET['page'])) {

            switch($_GET['page']) {

                //kelola pesanan pelanggan
                case 'pesanan';
                    include 'data_pesanan/data_pesanan.php';
                break;
                case 'pembayaran';
                    include 'data_pesanan/pembayaran.php';
                break;
                case 'cetak';
                    include 'data_pesanan/cetak.php';
                break;

                //kelola pesanan
                case 'kelola_pesanan';
                    include 'kelola_pesanan/kelola_pesanan.php';
                break;
                case 'detail_pesanan';
                    include 'kelola_pesanan/detail_pesanan.php';
                break;
                case 'ubah_pesanan';
                    include 'kelola_pesanan/ubah.php';
                break;
                case 'tambah_pesanan';
                    include 'kelola_pesanan/tambah.php';
                break;
                case 'hapus_pesanan';
                    include 'kelola_pesanan/hapus.php';
                break;

                //kelola transaksi
                case 'kelola_transaksi';
                    include 'kelola_transaksi/kelola_transaksi.php';
                break;
                case 'ubah_transaksi';
                    include 'kelola_transaksi/ubah.php';
                break;
                case 'tambah_transaksi';
                    include 'kelola_transaksi/tambah.php';
                break;
                case 'hapus_transaksi';
                    include 'kelola_transaksi/hapus.php';
                break;

                //kelola pegawai
                case 'pegawai';
                    include 'kelola_pegawai/kelola_pegawai.php';
                break;
                case 'ubah_pegawai';
                    include 'kelola_pegawai/ubah.php';
                break;
                case 'tambah_pegawai';
                    include 'kelola_pegawai/tambah.php';
                break;
                case 'hapus_pegawai';
                    include 'kelola_pegawai/hapus.php';
                break;
                case 'detail_pegawai';
                  include 'kelola_pegawai/detail_pegawai.php';
                break;

                default:
                    include 'home.php';
                break;

            }
        }
    ?>

        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2023-2024</strong> <span>App Celuller | Repost by <a href='https://stokcoding.com/' title='StokCoding.com' target='_blank'>StokCoding.com</a>
    </span>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../assets/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="../assets/plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="../assets/plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="../assets/plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="../assets/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="../assets/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="../assets/plugins/moment/moment.min.js"></script>
<script src="../assets/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="../assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="../assets/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../assets/plugins/jszip/jszip.min.js"></script>
<script src="../assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="../assets/plugins/pdfmake/vfs_fonts.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="../assets/dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../assets/dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="../assets/dist/js/pages/dashboard.js"></script>
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>
</body>
</html>