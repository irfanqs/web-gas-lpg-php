<div class="container-fluid">
    <div class="card">
        <h4 class="card-header"><i class="fas fa-user-tag" style="color: #f39c12;"></i> <small>Pesanan Pelanggan</small></h4>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-3">
                    <form action="" method="post">
                    <input type="text" name="kode" placeholder="Masukan Kode" class="form-control">
                </div>
                <div class="col-sm-4">
                    <button type="submit" name="hitung" class="btn btn-sm btn-warning mt-1"><span style="color:#2f3640;"><i class="fas fa-search"></i> Tampilkan</span></button>
                    </form>
                </div>
            </div>
            <table class="table table-bordered table-striped mt-4">
                <thead>
                    <tr>
                        <th>No.Pesanan</th>
                        <th>Nama Barang</th>
                        <th>Tanggal</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Option</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if(isset($_POST['hitung'])){
                            $kode = $_POST['kode'];
                            
                            $query = mysqli_query( $koneksi, "select * from tb_pesanan where kode like '%".$kode."%'");
                            
                            $count = mysqli_num_rows($query);
                            if($count > 0) {
                                $data = mysqli_fetch_assoc($query);
                    ?>
                    <tr>
                        <td><?= $data['id_pesanan'] ?></td>
                        <td><?= $data['nama_barang'] ?></td>
                        <td><?= $data['tanggal'] ?></td>
                        <td><?= $data['jumlah'] ?></td>
                        <td>Rp. <?= number_format($data['harga'],0,',',','); ?></td>
                        <td>
                            <a href="index.php?page=pembayaran&id_pesanan=<?= $data['id_pesanan'] ?>" class="btn" style="background-color:#e84118; color:white;">Bayar</a>
                        </td>
                    </tr>
                    <?php } else { echo"Kode tidak ditemukan"; }} ?>
                </tbody>
            </table>
        </div>
    </div>
</div>