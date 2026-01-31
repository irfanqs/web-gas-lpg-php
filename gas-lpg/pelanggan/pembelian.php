<?php
    require '../koneksi/koneksi.php';

    $id = $_GET['id_barang'];
    $query = mysqli_query($koneksi, "SELECT * FROM tb_barang where id_barang ='$id'");
    $data  = mysqli_fetch_assoc($query); 
?>

<div class="container-fluid">
    <div class="card" style="width: 50rem; margin-left:12%;">
        <h4 class="card-header"><i class="fas fa-clipboard-list" style="color: #f39c12;"></i> <small>Form Tambah</small> </h4>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form action="" method="post">
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 col-form-label">Nama Barang</label>
                            <div class="col-sm-6">
                            <input type="hidden" name="id_pesanan">
                            <input type="hidden" name="id_barang" value="<?= $data['id_barang'] ?>">
                            <input type="text" class="form-control" name="nama_barang" value="<?= $data['nama_barang'] ?>">
                            </div>
                        </div>            
                        <div class="form-group row">
                            <label for="inputPassword3" class="col-sm-4 col-form-label">Harga</label>
                            <div class="col-sm-6">
                            <input type="number" class="form-control" name="harga" value="<?= $data['harga'] ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputPassword3" class="col-sm-4 col-form-label">Jumlah</label>
                            <div class="col-sm-6">
                            <input type="number" class="form-control" name="jumlah">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-10">
                            <button type="submit" class="btn btn-secondary" name="simpan">Simpan</button>
                            <a href="index.php?page=barang" class="btn" style="background-color:#e84118; color:white;">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                <?php

                    if(isset($_POST['simpan']))
                    {
                        $kode_unik = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890zyxwvutsrqponmlkjihgfedcba"), 14, 6);

                        $id_pesanan = $_POST['id_pesanan'];
                        $id_barang = $_POST['id_barang'];
                        $nama_barang = $_POST['nama_barang'];
                        $harga = $_POST['harga'];
                        $jumlah = $_POST['jumlah'];
                        $kode = $kode_unik;
                        $tanggal = date('y-m-d h:i:s');

                        $query = mysqli_query($koneksi, "INSERT INTO tb_pesanan VALUES('$id_pesanan', '$id_barang', '$nama_barang', '$harga', '$jumlah', '$kode', '$tanggal')");
                        if($query)
                        {
                            echo "<script>
                                    alert('Data Berhasil Dipesan Silahkan Cetak Struk Di Bawah Ini dan Berikan Kepada Petugas');
                                  </script>";
                        } else {
                            echo "<script>
                            alert('Data Gagal Dimasukan');
                            window.location='barang.php'
                          </script>";
                        } 
                ?>                   
                
                <table>
                    <tr>
                        <td>ID Barang</td>
                        <td>:</td>
                        <td><?= $id_barang; ?></td>
                    </tr>
                    <tr>
                        <td>Nama Barang</td>
                        <td>:</td>
                        <td><?= $nama_barang; ?></td>
                    </tr>
                    <tr>
                        <td>Harga</td>
                        <td>:</td>
                        <td><?= number_format($harga,0,',',''); ?></td>
                    </tr>
                    <tr>
                        <td>Jumlah</td>
                        <td>:</td>
                        <td><?= $jumlah; ?></td>
                    </tr>
                    <tr>
                        <td>Kode</td>
                        <td>:</td>
                        <td><?= $kode; ?></td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td><?= $tanggal; ?></td>
                    </tr>
                </table>
                <a href="cetak.php?id_barang=<?= $data['id_barang']; ?>" target="_blank" class="btn btn-secondary mt-1"> <i class="fas fa-print"></i> Cetak</a>

            <?php } ?>
                </div>
            </div>
            
        </div>
    </div>
</div>

