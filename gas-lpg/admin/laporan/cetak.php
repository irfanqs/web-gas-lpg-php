<table border="1">
    <tr>
        <th>No</th>
        <th>ID Barang</th>
        <th>Nama Barang</th>
        <th>Kategori</th>
        <th>Tahun</th>
        <th>Harga</th>
        <th>Tanggal</th>
    </tr>
    <?php
        require '../../koneksi/koneksi.php';
        $no = 1;
        $query = mysqli_query( $koneksi, "select * from laporan_penjualan");
        while($data = mysqli_fetch_assoc($query)) :
    ?>
    
    <tr>
        <td><?= $no++; ?></td>
        <td><?= $data['id_barang'] ?></td>
        <td><?= $data['nama_barang'] ?></td>
        <td><?= $data['kategori'] ?></td>
        <td><?= $data['tahun'] ?></td>
        <td><?= $data['harga'] ?></td>
        <td><?= $data['tanggal'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<script>
    window.print();
</script>