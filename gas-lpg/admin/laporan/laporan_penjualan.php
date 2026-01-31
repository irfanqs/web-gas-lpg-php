<div class="container-fluid">
<div class="card">
    <h4 class="card-header"><i class="fas fa-server" style="color: #f39c12;"></i> <small>Laporan Penjualan</small> </h4>
        <div class="card-body">
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Tahun</th>
                    <th>Harga</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
            <?php
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
                    <td>Rp. <?= number_format($data['harga'],0,',',','); ?></td>
                    <td><?= $data['tanggal'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>      
        </table>
        <a href="laporan/cetak.php" target="_blank" class="btn btn-sm btn-warning"> <span style="color:#2f3640;"> <i class="fas fa-print"></i> Cetak</span> </a>
        </div>
    </div>
</div>
