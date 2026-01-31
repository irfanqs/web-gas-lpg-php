<?php
/**
 * =====================================================
 * File: admin/cetak_laporan.php
 * Halaman Cetak Laporan Penjualan (PDF-friendly)
 * =====================================================
 */
include '../koneksi/koneksi.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    echo "<script>alert('Akses ditolak!'); window.close();</script>";
    exit();
}

$nama_admin = $_SESSION['nama'];

// Filter tanggal - default dari 1 Januari
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-01-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');

// Ambil data penjualan - urut ascending
$query_penjualan = mysqli_query($koneksi, "
    SELECT p.*, u.nama_depan, u.nama_belakang, pr.nama_produk 
    FROM tb_pesanan p 
    JOIN user u ON p.id_user = u.id_user 
    JOIN tb_produk pr ON p.id_produk = pr.id_produk 
    WHERE p.status = 'completed' 
    AND DATE(p.waktu_selesai) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
    ORDER BY p.waktu_selesai ASC
");

// Hitung total
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - <?php echo date('d-m-Y'); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 12px;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #333;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 16px;
            font-weight: normal;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 15px;
        }
        .info-section table {
            border: none;
        }
        .info-section td {
            border: none;
            padding: 2px 10px 2px 0;
            vertical-align: top;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th, .data-table td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        .data-table td.center { text-align: center; }
        .data-table td.right { text-align: right; }
        .data-table tfoot td {
            font-weight: bold;
            background-color: #f5f5f5;
        }
        
        .summary-box {
            border: 2px solid #333;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        .summary-box h3 {
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
        }
        .summary-box table {
            width: 100%;
        }
        .summary-box td {
            padding: 5px 0;
            border: none;
        }
        .summary-box td.label { width: 60%; }
        .summary-box td.value { 
            text-align: right; 
            font-weight: bold;
            font-size: 13px;
        }
        
        .footer {
            margin-top: 30px;
        }
        .signature {
            float: right;
            width: 200px;
            text-align: center;
        }
        .signature .date {
            margin-bottom: 60px;
        }
        .signature .name {
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        
        .no-print { margin-bottom: 20px; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #2c3e50; color: white; border: none; border-radius: 5px;">
            🖨️ Cetak Laporan
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; background: #e74c3c; color: white; border: none; border-radius: 5px; margin-left: 10px;">
            ✖️ Tutup
        </button>
    </div>

    <!-- Header -->
    <div class="header">
        <h1><?php echo APP_NAME; ?></h1>
        <h2>LAPORAN PENJUALAN</h2>
        <p>Jl. Contoh Alamat No. 123, Kota, Provinsi</p>
    </div>

    <!-- Info Laporan -->
    <div class="info-section">
        <table>
            <tr>
                <td><strong>Periode Laporan</strong></td>
                <td>: <?php echo date('d F Y', strtotime($tanggal_awal)); ?> s/d <?php echo date('d F Y', strtotime($tanggal_akhir)); ?></td>
            </tr>
            <tr>
                <td><strong>Tanggal Cetak</strong></td>
                <td>: <?php echo date('d F Y, H:i'); ?> WIB</td>
            </tr>
            <tr>
                <td><strong>Dicetak Oleh</strong></td>
                <td>: <?php echo $nama_admin; ?></td>
            </tr>
        </table>
    </div>

    <!-- Tabel Data -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal Pembelian</th>
                <th width="15%">Kode Pembelian</th>
                <th width="20%">Nama Pembeli</th>
                <th width="15%">Produk</th>
                <th width="10%">Jumlah</th>
                <th width="20%">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = mysqli_fetch_assoc($query_penjualan)): 
            ?>
            <tr>
                <td class="center"><?php echo $no++; ?></td>
                <td class="center"><?php echo date('d/m/Y H:i', strtotime($row['waktu_selesai'])); ?></td>
                <td><?php echo $row['kode_pesanan']; ?></td>
                <td><?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?></td>
                <td><?php echo $row['nama_produk']; ?></td>
                <td class="center"><?php echo $row['jumlah']; ?></td>
                <td class="right">Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="right"><strong>TOTAL</strong></td>
                <td class="center"><strong><?php echo $total['total_stok_terjual'] ?? 0; ?></strong></td>
                <td class="right"><strong>Rp <?php echo number_format($total['total_pendapatan'] ?? 0, 0, ',', '.'); ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <!-- Summary Box -->
    <div class="summary-box">
        <h3>REKAPITULASI</h3>
        <table>
            <tr>
                <td class="label">Total Pendapatan</td>
                <td class="value">Rp <?php echo number_format($total['total_pendapatan'] ?? 0, 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <td class="label">Total Transaksi</td>
                <td class="value"><?php echo $total['total_transaksi'] ?? 0; ?> Transaksi</td>
            </tr>
            <tr>
                <td class="label">Total Gas Terjual</td>
                <td class="value"><?php echo $total['total_stok_terjual'] ?? 0; ?> Unit</td>
            </tr>
        </table>
    </div>

    <!-- Footer & Signature -->
    <div class="footer">
        <div class="signature">
            <p class="date">..................., <?php echo date('d F Y'); ?></p>
            <p>Admin</p>
            <p class="name"><?php echo $nama_admin; ?></p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>
