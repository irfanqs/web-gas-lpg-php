<?php
    require '../koneksi/koneksi.php';

    $id = $_GET['id_barang'];
    $query = mysqli_query($koneksi, "SELECT * FROM tb_pesanan where id_barang ='$id'");
    $data  = mysqli_fetch_assoc($query); 
?>

    <center><p>Simpan Bukti Pemesananan Sebagai Pembayaran, Berikan Struk Ini Kepada Petugas</p></center>

    <table>
        <tr>
            <td>No Pesanan</td>
            <td>:</td>
            <td>
                <?= $data['id_pesanan'] ?>
            </td>
        </tr>
        <tr>
            <td>Nama Barang</td>
            <td>:</td>
            <td>
                <?= $data['nama_barang'] ?>
            </td>
        </tr>
        <tr>
            <td>Harga</td>
            <td>:</td>
            <td>
                Rp. <?= number_format($data['harga'],0,',',','); ?>
            </td>
        </tr>
        <tr>
            <td>Jumlah Barang</td>
            <td>:</td>
            <td>
                <?= $data['jumlah'] ?>
            </td>
        </tr>
        <tr>
            <td>Kode</td>
            <td>:</td>
            <td>
                <?= $data['kode'] ?>
            </td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>
                <?= $data['tanggal'] ?>
            </td>
        </tr>
    </table> 
    
    <script>
		window.print();
	</script>