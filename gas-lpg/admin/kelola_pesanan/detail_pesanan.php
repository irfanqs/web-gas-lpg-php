<div class="container-fluid">
<div class="card">
    <h4 class="card-header"><i class="fas fa-server" style="color: #f39c12;"></i> <small>Detail Pesanan</small> </h4>
        <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID Barang</th>
                    <th>No.Pesanan</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th>Kode</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
            <?php
              $id = $_GET['id_pesanan'];
              $query = mysqli_query( $koneksi, "select * from tb_pesanan where id_pesanan='$id'");
              $data = mysqli_fetch_assoc($query)
            ?>
                <tr>
                    <td><?= $data['id_barang'] ?></td>
                    <td><?= $data['id_pesanan'] ?></td>
                    <td><?= $data['nama_barang'] ?></td>
                    <td><?= $data['jumlah'] ?></td>
                    <td>Rp. <?= number_format($data['harga'],0,',',','); ?></td>
                    <td><?= $data['kode'] ?></td>
                    <td><?= $data['tanggal'] ?></td>
                </tr>
            </tbody>      
        </table>
        <a href="index.php?page=pesanan" class="btn btn-sm btn-warning mt-2"> <span> Kembali</span> </a>
        </div>
    </div>
</div>
