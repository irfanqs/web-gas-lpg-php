<div class="container-fluid">
    <div class="card" style="width: 50rem; margin-left:12%;">
        <h4 class="card-header"><i class="fas fa-clipboard-list" style="color: #f39c12;"></i> <small>Form Tambah</small> </h4>
        <div class="card-body">
            <form action="" method="post">
                <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-2 col-form-label">ID Barang</label>
                    <div class="col-sm-10">
                    <input type="hidden" name="id_pesanan">
                    <select name="id_barang" class="form-control">
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
                    <input type="text" class="form-control" name="nama_barang">
                    </div>
                </div>            
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Harga</label>
                    <div class="col-sm-10">
                    <input type="number" class="form-control" name="harga">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Jumlah</label>
                    <div class="col-sm-10">
                    <input type="number" class="form-control" name="jumlah">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-10">
                    <button type="submit" class="btn btn-secondary" name="simpan">Simpan</button>
                    <a href="index.php?page=kelola_pesanan" class="btn" style="background-color:#e84118; color:white;">Cancel</a>
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

        $query = mysqli_query($koneksi, "INSERT INTO tb_pesanan VALUES('$id_pesanan', '$id_barang', '$nama_barang', '$harga', '$jumlah', '$kode', '$tanggal')");
        if($query)
        {
            echo "<script>
                    alert('Data Berhasil Ditambahkan');
                    window.location='index.php?page=kelola_pesanan'
                  </script>";
        } else {
            echo "<script>
            alert('Data Gagal Dimasukan');
            window.location='index.php?page=tambah_pesanan'
          </script>";
        }
    } 
?>    