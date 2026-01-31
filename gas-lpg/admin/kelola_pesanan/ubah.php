<?php
    $id = $_GET['id_pesanan'];
    $query = mysqli_query($koneksi, "SELECT * FROM tb_pesanan where id_pesanan ='$id'");
    $data  = mysqli_fetch_assoc($query); 
?>

<div class="container-fluid">
    <div class="card" style="width: 50rem; margin-left:12%;">
        <h4 class="card-header"><i class="fas fa-clipboard-list" style="color: #f39c12;"></i> <small>Form Ubah</small> </h4>
        <div class="card-body">
            <form action="" method="post">
                <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-2 col-form-label">ID Barang</label>
                    <div class="col-sm-10">
                    <input type="hidden" name="id_pesanan" value="<?= $data['id_pesanan']; ?>">
                    <select name="id_barang" class="form-control">
                    <option value="<?= $data['id_barang']; ?>"><?= $data['id_barang']; ?> -  <?= $data['nama_barang']; ?></option>
                        <?php 
                            $ambil = mysqli_query($koneksi, "SELECT id_barang, nama_barang FROM tb_barang");
                            while ($data1=mysqli_fetch_assoc($ambil)) : ?>
                            <option value="<?= $data1['id_barang']; ?>"><?= $data1['id_barang']; ?> -  <?= $data1['nama_barang']; ?></option>
                        <?php endwhile; ?>
                    </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-2 col-form-label">Nama Barang</label>
                    <div class="col-sm-10">
                    <input type="text" class="form-control" name="nama_barang" value="<?= $data['nama_barang']; ?>">
                    </div>
                </div>            
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Harga</label>
                    <div class="col-sm-10">
                    <input type="number" class="form-control" name="harga" value="<?= $data['harga']; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Jumlah</label>
                    <div class="col-sm-10">
                    <input type="number" class="form-control" name="jumlah" value="<?= $data['jumlah']; ?>">
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
    </div>
</div>


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

        $query = mysqli_query($koneksi, "UPDATE tb_pesanan SET id_pesanan='$id_pesanan', id_barang='$id_barang', nama_barang='$nama_barang', harga='$harga', jumlah='$jumlah', kode='$kode', tanggal='$tanggal' where id_pesanan='$id_pesanan'");
        if($query)
        {
            echo "<script>
                    alert('Data Berhasil Diubah');
                    window.location='index.php?page=pesanan'
                  </script>";
        } else {
            echo "<script>
            alert('Data Gagal Diubah');
            window.location='index.php?page=ubah_pesanan'
          </script>";
        }
    } 
?>    