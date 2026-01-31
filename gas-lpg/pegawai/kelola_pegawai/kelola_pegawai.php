<div class="container-fluid">
<div class="card">
    <h4 class="card-header"><i class="fas fa-server" style="color: #f39c12;"></i> <small>Data Pegawai</small> </h4>
        <div class="card-body">
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>No.Telpon</th>
                    <th>Alamat</th>
                    <th>Option</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $no = 1;
                $query = mysqli_query( $koneksi, "select * from user where not role='Pelanggan' AND not role='Admin'");
                while($data = mysqli_fetch_assoc($query)) :
            ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $data['nama'] ?></td>
                    <td><?= $data['no_telp'] ?></td>
                    <td><?= $data['alamat'] ?></td>
                    <td>
                        <a href="index.php?page=detail_pegawai&id_user=<?= $data['id_user']; ?>"><i class="fas fa-search-plus" style="color:#636e72;"></i></a>
                        <a href="index.php?page=hapus_pegawai&id_user=<?= $data['id_user'] ?>"><i class="fas fa-trash" style="color:#d35400;"></i></a>
                        <a href="index.php?page=ubah_pegawai&id_user=<?= $data['id_user'] ?>"><i class="fas fa-edit" style="color: #f39c12;"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>      
        </table>
        <a href="index.php?page=tambah_pegawai" class="btn btn-sm btn-warning"> <span style="color:#2f3640;"> <i class="fas fa-plus"></i> Tambah</span> </a>
        </div>
    </div>
</div>