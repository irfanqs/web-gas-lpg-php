<?php
/**
 * Halaman Cetak Laporan Penjualan
 */
include '../koneksi/koneksi.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    echo "<script>alert('Akses ditolak!'); window.close();</script>";
    exit();
}

$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');

$query_penjualan = mysqli_query($koneksi, "
    SELECT p.*, u.nama_depan, u.nama_belakang, pr.nama_produk 
    FROM tb_pesanan p 
    JOIN user u ON p.id_user = u.id_user 
    JOIN tb_produk pr ON p.id_produk = pr.id_produk 
    WHERE p.status = 'completed' 
    AND DATE(p.waktu_selesai) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
    ORDER BY p.waktu_selesai DESC
");

$query_total = mysqli_query($koneksi, "
    SELECT SUM(total_harga) as total, COUNT(*) as jumlah 
    FROM tb_pesanan 
    WHERE status = 'completed' 
    AND DATE(waktu_selesai) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
");
$total = mysqli_fetch_assoc($query_total);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Penjualan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { text-align: center; margin-bottom: 5px; }
        .info { text-align: center; margin-bottom: 20px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #2c3e50; color: white; }
        .total-row { font-weight: bold; background-color: #f5f5f5; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()" style="padding: 10px 20px; margin-bottom: 20px; cursor: pointer;">
        🖨️ Cetak Sekarang
    </button>
    
    <h2>LAPORAN PENJUALAN</h2>
    <p class="info">
        Periode: <?php echo date('d/m/Y', strtotime($tanggal_awal)); ?> - <?php echo date('d/m/Y', strtotime($tanggal_akhir)); ?><br>
        Total Transaksi: <?php echo $total['jumlah'] ?? 0; ?> | 
        Total Pendapatan: Rp <?php echo number_format($total['total'] ?? 0, 0, ',', '.'); ?>
    </p>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Pembeli</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Total</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = mysqli_fetch_assoc($query_penjualan)): 
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $row['kode_pesanan']; ?></td>
                <td><?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?></td>
                <td><?php echo $row['nama_produk']; ?></td>
                <td><?php echo $row['jumlah']; ?></td>
                <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($row['waktu_selesai'])); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
