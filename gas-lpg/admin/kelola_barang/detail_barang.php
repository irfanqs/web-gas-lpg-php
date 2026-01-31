<div class="container-fluid">
<div class="card">
    <h4 class="card-header"><i class="fas fa-server" style="color: #f39c12;"></i> <small>Data Barang</small> </h4>
        <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Tahun</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th>Gambar</th>
                </tr>
            </thead>
            <tbody>
            <?php
               $id = $_GET['id_barang'];
               $query = mysqli_query( $koneksi, "select * from tb_barang where id_barang='$id'");
               $data = mysqli_fetch_assoc($query)
            ?>
                <tr>
                    <td><?= $data['nama_barang'] ?></td>
                    <td><?= $data['kategori'] ?></td>
                    <td><?= $data['tahun'] ?></td>
                    <td><?= $data['jumlah'] ?></td>
                    <td><?= $data['harga'] ?></td>
                    <td><img src="../assets/img/<?= $data['gambar'] ?>" style="width: 50px;"></td>
                </tr>
            </tbody>      
        </table>
        <a href="index.php?page=barang" class="btn btn-sm btn-warning mt-2"> <span> Kembali</span> </a>
        </div>
    </div>
</div>
