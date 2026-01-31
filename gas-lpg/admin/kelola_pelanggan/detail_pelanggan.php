<div class="container-fluid">
<div class="card">
    <h4 class="card-header"><i class="fas fa-info-circle" style="color: #f39c12;"></i> <small>Detail Pelanggan</small> </h4>
        <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Password</th>
                    <th>Jenis Kelamin</th>
                    <th>Role</th>
                    <th>No.Telpon</th>
                    <th>Alamat</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $id = $_GET['id_user'];
                $query = mysqli_query( $koneksi, "select * from user where id_user='$id'");
                $data = mysqli_fetch_assoc($query)
            ?>
                <tr>
                    <td><?= $data['nama'] ?></td>
                    <td><?= $data['password'] ?></td>
                    <td><?= $data['jk'] ?></td>
                    <td><?= $data['role'] ?></td>
                    <td><?= $data['no_telp'] ?></td>
                    <td><?= $data['alamat'] ?></td>
                </tr>
            </tbody>      
        </table>
        <a href="index.php?page=pelanggan" class="btn btn-sm btn-warning mt-2"> <span> Kembali</span> </a>
        </div>
    </div>
</div>