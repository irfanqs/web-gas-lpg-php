<div class="container-fluid">
<div class="card">
    <h4 class="card-header"><i class="fas fa-server" style="color: #f39c12;"></i> <small>Data Barang</small> </h4>
        <div class="card-body">
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Gambar</th>
                    <th>Option</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $no = 1;
                $query = mysqli_query( $koneksi, "select * from tb_barang");
                while($data = mysqli_fetch_assoc($query)) :
            ?>
                <tr>
                <td><?= $no++; ?></td>
                <td><?= $data['nama_barang'] ?></td>
                <td><?= $data['harga'] ?></td>
                <td><?= $data['jumlah'] ?></td>
                <td><img src="../assets/img/<?= $data['gambar'] ?>" style="width: 50px;"></td>
                    <td>
                        <a href="index.php?page=detail_barang&id_barang=<?= $data['id_barang']; ?>"><i class="fas fa-search-plus" style="color:#636e72;"></i></a>
                        <a href="index.php?page=hapus_barang&id_barang=<?= $data['id_barang'] ?>"><i class="fas fa-trash" style="color:#d35400;"></i></a>
                        <a href="index.php?page=ubah_barang&id_barang=<?= $data['id_barang'] ?>"><i class="fas fa-edit" style="color: #f39c12;"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>      
        </table>
        <a href="index.php?page=tambah_barang" class="btn btn-sm btn-warning"> <span style="color:#2f3640;"> <i class="fas fa-plus"></i> Tambah</span> </a>
        </div>
    </div>
</div>