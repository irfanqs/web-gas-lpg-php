<?php
    require '../../koneksi/koneksi.php';

    $id = $_GET['id_pesanan'];
    $query = mysqli_query($koneksi, "SELECT * FROM tb_transaksi where id_pesanan ='$id'");
    $data  = mysqli_fetch_assoc($query);
    
    $total = $data['harga'] * $data['jumlah'];
?>

    <center><p>Terimakasih Telah Berbalanja Di Tempat Kami</p></center>

    <table>
        <tr>
            <td>No Transaksi</td>
            <td>:</td>
            <td>
                <?= $data['id_transaksi'] ?>
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
            <td>Total</td>
            <td>:</td>
            <td>
                <?= $total ?>
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