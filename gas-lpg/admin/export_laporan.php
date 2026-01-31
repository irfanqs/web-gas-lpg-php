<?php
/**
 * =====================================================
 * File: admin/export_laporan.php
 * Export laporan penjualan ke Excel
 * =====================================================
 */
include '../koneksi/koneksi.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    exit('Akses ditolak');
}

$nama_admin = $_SESSION['nama'];

$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-01-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');

// Ambil data
$query_penjualan = mysqli_query($koneksi, "
    SELECT p.*, u.nama_depan, u.nama_belakang, pr.nama_produk 
    FROM tb_pesanan p 
    JOIN user u ON p.id_user = u.id_user 
    JOIN tb_produk pr ON p.id_produk = pr.id_produk 
    WHERE p.status = 'completed' 
    AND DATE(p.waktu_selesai) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
    ORDER BY p.waktu_selesai ASC
");

$query_total = mysqli_query($koneksi, "
    SELECT 
        SUM(total_harga) as total_pendapatan, 
        COUNT(*) as total_transaksi,
        SUM(jumlah) as total_stok_terjual
    FROM tb_pesanan 
    WHERE status = 'completed' 
    AND DATE(waktu_selesai) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
");
$total = mysqli_fetch_assoc($query_total);

// Set header untuk download Excel
$filename = "laporan_penjualan_{$tanggal_awal}_{$tanggal_akhir}.xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");
?>
<html>
<head>
    <meta charset="utf-8">
    <style>
        table { border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #2c3e50; color: white; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .header { font-size: 14px; font-weight: bold; }
        .summary { background-color: #f5f5f5; font-weight: bold; }
    </style>
</head>
<body>

<table>
    <tr><td colspan="7" class="header">LAPORAN PENJUALAN</td></tr>
    <tr><td colspan="7">Periode: <?php echo date('d/m/Y', strtotime($tanggal_awal)); ?> - <?php echo date('d/m/Y', strtotime($tanggal_akhir)); ?></td></tr>
    <tr><td colspan="7">Dicetak: <?php echo date('d/m/Y H:i'); ?></td></tr>
    <tr><td colspan="7">Oleh: <?php echo $nama_admin; ?></td></tr>
    <tr><td colspan="7"></td></tr>
</table>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Pembelian</th>
            <th>Kode Pembelian</th>
            <th>Nama Pembeli</th>
            <th>Produk</th>
            <th>Jumlah</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        while ($row = mysqli_fetch_assoc($query_penjualan)): 
        ?>
        <tr>
            <td class="text-center"><?php echo $no++; ?></td>
            <td class="text-center"><?php echo date('d/m/Y H:i', strtotime($row['waktu_selesai'])); ?></td>
            <td><?php echo $row['kode_pesanan']; ?></td>
            <td><?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?></td>
            <td><?php echo $row['nama_produk']; ?></td>
            <td class="text-center"><?php echo $row['jumlah']; ?></td>
            <td class="text-right"><?php echo $row['total_harga']; ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
    <tfoot>
        <tr class="summary">
            <td colspan="5" class="text-right">TOTAL</td>
            <td class="text-center"><?php echo $total['total_stok_terjual'] ?? 0; ?></td>
            <td class="text-right"><?php echo $total['total_pendapatan'] ?? 0; ?></td>
        </tr>
    </tfoot>
</table>

<br>
<table>
    <tr><td colspan="2" class="header">REKAPITULASI</td></tr>
    <tr>
        <td>Total Pendapatan</td>
        <td class="text-right"><?php echo $total['total_pendapatan'] ?? 0; ?></td>
    </tr>
    <tr>
        <td>Total Transaksi</td>
        <td class="text-right"><?php echo $total['total_transaksi'] ?? 0; ?> Transaksi</td>
    </tr>
    <tr>
        <td>Total Gas Terjual</td>
        <td class="text-right"><?php echo $total['total_stok_terjual'] ?? 0; ?> Unit</td>
    </tr>
</table>

</body>
</html>
