<div class="container-fluid">
<div class="card">
    <h4 class="card-header"><i class="fas fa-server" style="color: #f39c12;"></i> <small>Data Pesanan</small> </h4>
        <div class="card-body">
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                        <th>No.Pesanan</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Kode</th>
                        <th>Option</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $no = 1;
                $query = mysqli_query( $koneksi, "select * from tb_pesanan");
                while($data = mysqli_fetch_assoc($query)) :
            ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $data['id_pesanan'] ?></td>
                    <td><?= $data['nama_barang'] ?></td>
                    <td><?= $data['jumlah'] ?></td>
                    <td>Rp. <?= number_format($data['harga'],0,',',','); ?></td>
                    <td><?= $data['kode'] ?></td>
                    <td>
                        <a href="index.php?page=detail_pesanan&id_pesanan=<?= $data['id_pesanan']; ?>"><i class="fas fa-search-plus" style="color:#636e72;"></i></a>
                        <a href="index.php?page=hapus_pesanan&id_pesanan=<?= $data['id_pesanan'] ?>"><i class="fas fa-trash" style="color:#d35400;"></i></a>
                        <a href="index.php?page=ubah_pesanan&id_pesanan=<?= $data['id_pesanan'] ?>"><i class="fas fa-edit" style="color: #f39c12;"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>      
        </table>
        <a href="index.php?page=tambah_pesanan" class="btn btn-sm btn-warning"> <span style="color:#2f3640;"> <i class="fas fa-plus"></i> Tambah</span> </a>
        </div>
    </div>
</div>