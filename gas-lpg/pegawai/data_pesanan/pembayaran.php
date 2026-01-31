<?php
    $id = $_GET['id_pesanan'];
    $query = mysqli_query($koneksi, "SELECT * FROM tb_pesanan where id_pesanan ='$id'");
    $data  = mysqli_fetch_assoc($query); 
?>

<div class="container-fluid">
    <div class="card">
        <h4 class="card-header"><i class="fas fa-credit-card" style="color: #f39c12;"></i> <small>Form Pembayaran</small> </h4>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6">
                    <form action="" method="post">
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">ID Pesanan</label>
                            <div class="col-sm-10">
                            <input type="hidden" name="id_transaksi">
                            <input type="text" name="id_pesanan" value="<?= $data['id_pesanan'] ?>" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputPassword3" class="col-sm-2 col-form-label">Payment</label>
                            <div class="col-sm-10">
                            <input type="number" class="form-control" name="payment">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputPassword3" class="col-sm-2 col-form-label">Harga</label>
                            <div class="col-sm-10">
                            <input type="number" class="form-control" name="harga" value="<?= $data['harga'] ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputPassword3" class="col-sm-2 col-form-label">Jumlah</label>
                            <div class="col-sm-10">
                            <input type="number" class="form-control" name="jumlah" value="<?= $data['jumlah'] ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-10">
                            <button type="submit" class="btn btn-secondary" name="simpan">Simpan</button>
                            <a href="index.php?page=pesanan" class="btn" style="background-color:#e84118; color:white;">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>

                <?php

                if(isset($_POST['simpan']))
                {
                    $id_transaksi = $_POST['id_transaksi'];
                    $id_pesanan = $_POST['id_pesanan'];
                    $payment = $_POST['payment'];
                    $harga = $_POST['harga'];
                    $jumlah = $_POST['jumlah'];
                    $tanggal = date('y-m-d h:i:s');

                    $total = $harga * $jumlah;
                    $sisa = $payment - $total;

                    $query = mysqli_query($koneksi, "INSERT INTO tb_transaksi VALUES('$id_transaksi', '$id_pesanan', '$harga', '$jumlah', '$tanggal')");
                    if($query)
                    {
                        echo "<script>
                                alert('Pembayaran Sukses');
                              </script>";
                    } else {
                        echo "<script>
                        alert('Data Gagal Dimasukan');
                        window.location='index.php?page=pesanan'
                      </script>";
                    } 
                ?>  

                <div class="col-sm-5">
                    <table class="table table-bordered table-striped ml-4">
                    <tbody>
                        <tr>
                            <td>ID Pesanan</td>
                            <td>:</td>
                            <td><?= $id_pesanan; ?></td>
                        </tr>
                        <tr>
                            <td>Payment</td>
                            <td>:</td>
                            <td>Rp. <?= number_format($payment,0,',',','); ?></td>
                        </tr>
                        <tr>
                            <td>Harga</td>
                            <td>:</td>
                            <td>Rp. <?= number_format($harga,0,',',','); ?></td>
                        </tr>
                        <tr>
                            <td>Jumlah</td>
                            <td>:</td>
                            <td><?= $jumlah; ?></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td><?= $tanggal; ?></td>
                        </tr>
                        <tr>
                            <td>Harga</td>
                            <td>:</td>
                            <td>Rp. <?= number_format($total,0,',',','); ?></td>
                        </tr>
                        <tr>
                            <td>Harga</td>
                            <td>:</td>
                            <td>Rp. <?= number_format($sisa,0,',',','); ?></td>
                        </tr>
                    </tbody>
                    </table>
                    <a href="data_pesanan/cetak.php?id_pesanan=<?= $data['id_pesanan']; ?>" target="_blank" class="btn ml-4" style="background-color:#e84118; color:white;"> <i class="fas fa-print"></i> Cetak</a>
                    <a href="index.php?page=pesanan" class="btn btn-warning ml-4">Kembali</a>
                <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
