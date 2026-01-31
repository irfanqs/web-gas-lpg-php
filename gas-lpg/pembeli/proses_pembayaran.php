<?php
/**
 * =====================================================
 * File: pembeli/proses_pembayaran.php
 * Memproses hasil pembayaran dari Midtrans Snap
 * =====================================================
 */

// Include koneksi
include '../koneksi/koneksi.php';
session_start();

// Cek autentikasi
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Pembeli') {
    header('location:../login.php');
    exit();
}

// Ambil parameter dari URL
$status = isset($_GET['status']) ? $_GET['status'] : '';
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';

if (empty($order_id)) {
    header('location:index.php?page=riwayat');
    exit();
}

// Ambil data pesanan berdasarkan kode pesanan
$order_id_escaped = escapeString($order_id);
$query_pesanan = mysqli_query($koneksi, "
    SELECT p.*, pr.nama_produk 
    FROM tb_pesanan p 
    JOIN tb_produk pr ON p.id_produk = pr.id_produk 
    WHERE p.kode_pesanan = '$order_id_escaped'
");

if (mysqli_num_rows($query_pesanan) == 0) {
    echo "<script>alert('Pesanan tidak ditemukan!'); window.location='index.php?page=riwayat';</script>";
    exit();
}

$pesanan = mysqli_fetch_assoc($query_pesanan);
$id_pesanan = $pesanan['id_pesanan'];

// Proses berdasarkan status
if ($status == 'success') {
    // Update status pesanan menjadi 'paid'
    mysqli_query($koneksi, "UPDATE tb_pesanan SET status = 'paid', waktu_bayar = NOW() WHERE id_pesanan = $id_pesanan");
    
    // Update status pembayaran
    mysqli_query($koneksi, "UPDATE tb_pembayaran SET status_pembayaran = 'success', waktu_pembayaran = NOW() WHERE id_pesanan = $id_pesanan");
    
    // Kirim notifikasi ke admin
    $pesan_notif = "Pesanan baru dari {$pesanan['nama_depan']} {$pesanan['nama_belakang']} sebanyak {$pesanan['jumlah']} {$pesanan['nama_produk']} - Pembayaran berhasil via Midtrans.";
    
    $query_admin = mysqli_query($koneksi, "SELECT id_user FROM user WHERE role = 'Admin'");
    while ($admin = mysqli_fetch_assoc($query_admin)) {
        mysqli_query($koneksi, "
            INSERT INTO tb_notifikasi (id_user, id_pesanan, judul, pesan, tipe) 
            VALUES ({$admin['id_user']}, $id_pesanan, 'Pembayaran Berhasil', '$pesan_notif', 'pembayaran')
        ");
    }
    
    // Hapus session checkout
    unset($_SESSION['id_pesanan']);
    unset($_SESSION['kode_pesanan']);
    
    $message = 'Pembayaran berhasil! Pesanan Anda akan segera diproses.';
    $alert_type = 'success';
    
} elseif ($status == 'pending') {
    // Status pending - menunggu pembayaran
    $message = 'Pembayaran sedang diproses. Silakan selesaikan pembayaran sesuai instruksi.';
    $alert_type = 'warning';
    
    // Hapus session checkout
    unset($_SESSION['id_pesanan']);
    unset($_SESSION['kode_pesanan']);
    
} else {
    // Status lainnya
    $message = 'Status pembayaran: ' . $status;
    $alert_type = 'info';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo APP_NAME; ?> | Hasil Pembayaran</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
</head>
<body class="hold-transition" style="background-color: #f4f6f9;">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center py-5">
                    <?php if ($status == 'success'): ?>
                        <!-- Icon Sukses -->
                        <div class="mb-4">
                            <i class="fas fa-check-circle fa-5x text-success"></i>
                        </div>
                        <h3 class="text-success">Pembayaran Berhasil!</h3>
                    <?php elseif ($status == 'pending'): ?>
                        <!-- Icon Pending -->
                        <div class="mb-4">
                            <i class="fas fa-clock fa-5x text-warning"></i>
                        </div>
                        <h3 class="text-warning">Menunggu Pembayaran</h3>
                    <?php else: ?>
                        <!-- Icon Info -->
                        <div class="mb-4">
                            <i class="fas fa-info-circle fa-5x text-info"></i>
                        </div>
                        <h3 class="text-info">Informasi Pembayaran</h3>
                    <?php endif; ?>
                    
                    <p class="mt-3"><?php echo $message; ?></p>
                    
                    <div class="alert alert-light mt-4">
                        <strong>Kode Pesanan:</strong> <?php echo $order_id; ?>
                    </div>
                    
                    <div class="mt-4">
                        <a href="index.php?page=riwayat" class="btn btn-primary">
                            <i class="fas fa-list mr-2"></i>Lihat Riwayat Pesanan
                        </a>
                        <a href="index.php?page=home" class="btn btn-secondary ml-2">
                            <i class="fas fa-home mr-2"></i>Kembali ke Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
