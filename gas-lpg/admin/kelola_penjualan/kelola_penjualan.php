<div class="container-fluid">
<div class="card">
    <h4 class="card-header"><i class="fas fa-server" style="color: #f39c12;"></i> <small>Data Penjualan</small> </h4>
        <div class="card-body">
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Pesanan</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Option</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $no = 1;
                $query = mysqli_query( $koneksi, "select * from tb_transaksi");
                while($data = mysqli_fetch_assoc($query)) :
            ?>
                <tr>
                <td><?= $no++; ?></td>
                <td><?= $data['id_pesanan'] ?></td>
                <td><?= $data['harga'] ?></td>
                <td><?= $data['jumlah'] ?></td>
                <td><?= $data['tanggal'] ?></td>
                    <td>
                        <a href="index.php?page=hapus_penjualan&id_transaksi=<?= $data['id_transaksi'] ?>"><i class="fas fa-trash" style="color:#d35400;"></i></a>
                        <a href="index.php?page=ubah_penjualan&id_transaksi=<?= $data['id_transaksi'] ?>"><i class="fas fa-edit" style="color: #f39c12;"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>      
        </table>
        <a href="index.php?page=tambah_penjualan" class="btn btn-sm btn-warning"> <span style="color:#2f3640;"> <i class="fas fa-plus"></i> Tambah</span> </a>
        </div>
    </div>
</div>