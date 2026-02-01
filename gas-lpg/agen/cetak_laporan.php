<?php
/**
 * =====================================================
 * File: agen/cetak_laporan.php
 * Cetak laporan agen (print-friendly)
 * =====================================================
 */

include '../koneksi/koneksi.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Agen') {
    exit('Akses ditolak');
}

$id_agen = $_SESSION['id_user'];
$nama_agen = $_SESSION['nama'];

$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-d');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-t');

// Data laporan
$query_distribusi = mysqli_query($koneksi, "
    SELECT d.*, u.nama_depan, u.nama_belakang, p.nama_produk, p.harga
    FROM tb_distribusi d
    JOIN user u ON d.id_admin = u.id_user
    JOIN tb_produk p ON d.id_produk = p.id_produk
    WHERE d.id_agen = $id_agen
    AND DATE(d.waktu_distribusi) BETWEEN '$tgl_awal' AND '$tgl_akhir'
    ORDER BY d.waktu_distribusi DESC
");

$query_total = mysqli_query($koneksi, "
    SELECT SUM(d.jumlah) as total_unit, SUM(d.jumlah * p.harga) as total_nilai
    FROM tb_distribusi d
    JOIN tb_produk p ON d.id_produk = p.id_produk
    WHERE d.id_agen = $id_agen
    AND DATE(d.waktu_distribusi) BETWEEN '$tgl_awal' AND '$tgl_akhir'
");
$total = mysqli_fetch_assoc($query_total);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Distribusi - <?php echo APP_NAME; ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .info { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; }
        .ttd { float: right; text-align: center; width: 200px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()">🖨️ Cetak</button>
        <button onclick="window.close()">✖️ Tutup</button>
    </div>

    <div class="header">
        <h2><?php echo APP_NAME; ?></h2>
        <h3>LAPORAN DISTRIBUSI GAS</h3>
        <p>Periode: <?php echo date('d/m/Y', strtotime($tgl_awal)); ?> - <?php echo date('d/m/Y', strtotime($tgl_akhir)); ?></p>
    </div>

    <div class="info">
        <table style="border: none; width: auto;">
            <tr style="border: none;">
                <td style="border: none;">Nama Agen</td>
                <td style="border: none;">: <?php echo $nama_agen; ?></td>
            </tr>
            <tr style="border: none;">
                <td style="border: none;">Tanggal Cetak</td>
                <td style="border: none;">: <?php echo date('d/m/Y H:i'); ?></td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Admin/Pangkalan</th>
                <th>Produk</th>
                <th class="text-right">Jumlah</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = mysqli_fetch_assoc($query_distribusi)): 
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['waktu_distribusi'])); ?></td>
                <td><?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?></td>
                <td><?php echo $row['nama_produk']; ?></td>
                <td class="text-right"><?php echo $row['jumlah']; ?></td>
                <td class="text-right"><?php echo formatRupiah($row['harga']); ?></td>
                <td class="text-right"><?php echo formatRupiah($row['jumlah'] * $row['harga']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">TOTAL</th>
                <th class="text-right"><?php echo number_format($total['total_unit'] ?? 0); ?></th>
                <th></th>
                <th class="text-right"><?php echo formatRupiah($total['total_nilai'] ?? 0); ?></th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="ttd">
            <p>..................., <?php echo date('d/m/Y'); ?></p>
            <p>Agen</p>
            <br><br><br>
            <p><u><?php echo $nama_agen; ?></u></p>
        </div>
    </div>
</body>
</html>
