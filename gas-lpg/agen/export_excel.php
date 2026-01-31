<?php
/**
 * =====================================================
 * File: agen/export_excel.php
 * Export laporan ke Excel
 * =====================================================
 */

include '../koneksi/koneksi.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Agen') {
    exit('Akses ditolak');
}

$id_agen = $_SESSION['id_user'];
$nama_agen = $_SESSION['nama'];
$tipe = isset($_GET['tipe']) ? $_GET['tipe'] : 'distribusi';
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');

// Set header untuk download Excel
$filename = "laporan_{$tipe}_{$tgl_awal}_{$tgl_akhir}.xls";
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
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-right { text-align: right; }
        .header { font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>

<?php if ($tipe == 'distribusi'): ?>
    <?php
    $query = mysqli_query($koneksi, "
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
    
    <table>
        <tr><td colspan="7" class="header">LAPORAN DISTRIBUSI GAS</td></tr>
        <tr><td colspan="7">Agen: <?php echo $nama_agen; ?></td></tr>
        <tr><td colspan="7">Periode: <?php echo date('d/m/Y', strtotime($tgl_awal)); ?> - <?php echo date('d/m/Y', strtotime($tgl_akhir)); ?></td></tr>
        <tr><td colspan="7"></td></tr>
    </table>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Admin/Pangkalan</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = mysqli_fetch_assoc($query)): 
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($row['waktu_distribusi'])); ?></td>
                <td><?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?></td>
                <td><?php echo $row['nama_produk']; ?></td>
                <td class="text-right"><?php echo $row['jumlah']; ?></td>
                <td class="text-right"><?php echo $row['harga']; ?></td>
                <td class="text-right"><?php echo $row['jumlah'] * $row['harga']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4">TOTAL</th>
                <th class="text-right"><?php echo $total['total_unit'] ?? 0; ?></th>
                <th></th>
                <th class="text-right"><?php echo $total['total_nilai'] ?? 0; ?></th>
            </tr>
        </tfoot>
    </table>

<?php elseif ($tipe == 'stok'): ?>
    <?php
    $query = mysqli_query($koneksi, "
        SELECT rs.*, p.nama_produk
        FROM tb_riwayat_stok rs
        JOIN tb_produk p ON rs.id_produk = p.id_produk
        WHERE rs.id_agen = $id_agen
        AND DATE(rs.created_at) BETWEEN '$tgl_awal' AND '$tgl_akhir'
        ORDER BY rs.created_at DESC
    ");
    
    $query_summary = mysqli_query($koneksi, "
        SELECT 
            SUM(CASE WHEN tipe = 'masuk' THEN jumlah ELSE 0 END) as total_masuk,
            SUM(CASE WHEN tipe = 'keluar' THEN jumlah ELSE 0 END) as total_keluar
        FROM tb_riwayat_stok
        WHERE id_agen = $id_agen
        AND DATE(created_at) BETWEEN '$tgl_awal' AND '$tgl_akhir'
    ");
    $summary = mysqli_fetch_assoc($query_summary);
    ?>
    
    <table>
        <tr><td colspan="8" class="header">LAPORAN RIWAYAT STOK</td></tr>
        <tr><td colspan="8">Agen: <?php echo $nama_agen; ?></td></tr>
        <tr><td colspan="8">Periode: <?php echo date('d/m/Y', strtotime($tgl_awal)); ?> - <?php echo date('d/m/Y', strtotime($tgl_akhir)); ?></td></tr>
        <tr><td colspan="8"></td></tr>
    </table>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>Stok Sebelum</th>
                <th>Stok Sesudah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = mysqli_fetch_assoc($query)): 
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                <td><?php echo $row['nama_produk']; ?></td>
                <td><?php echo strtoupper($row['tipe']); ?></td>
                <td class="text-right"><?php echo ($row['tipe'] == 'masuk' ? '+' : '-') . $row['jumlah']; ?></td>
                <td class="text-right"><?php echo $row['stok_sebelum']; ?></td>
                <td class="text-right"><?php echo $row['stok_sesudah']; ?></td>
                <td><?php echo $row['keterangan']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4">TOTAL MASUK</th>
                <th colspan="4"><?php echo $summary['total_masuk'] ?? 0; ?> unit</th>
            </tr>
            <tr>
                <th colspan="4">TOTAL KELUAR</th>
                <th colspan="4"><?php echo $summary['total_keluar'] ?? 0; ?> unit</th>
            </tr>
            <tr>
                <th colspan="4">SELISIH</th>
                <th colspan="4"><?php echo ($summary['total_masuk'] ?? 0) - ($summary['total_keluar'] ?? 0); ?> unit</th>
            </tr>
        </tfoot>
    </table>

<?php endif; ?>

</body>
</html>
