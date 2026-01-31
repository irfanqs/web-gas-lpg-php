<?php
    $id = $_GET['id_barang'];
    $query = mysqli_query($koneksi, "SELECT * FROM tb_barang where id_barang ='$id'");
    $data  = mysqli_fetch_assoc($query); 
?>

<div class="container-fluid">
    <div class="card" style="width: 50rem; margin-left:12%;">
        <h4 class="card-header"><i class="fas fa-clipboard-list" style="color: #f39c12;"></i> <small>Form Ubah</small> </h4>
        <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-2 col-form-label">Nama Barang</label>
                    <div class="col-sm-10">
                    <input type="hidden" name="id_barang" value="<?= $data['id_barang'] ?>">
                    <input type="text" class="form-control" name="nama_barang" value="<?= $data['nama_barang'] ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Kategori</label>
                    <div class="col-sm-10">
                    <input type="text" class="form-control" name="kategori" value="<?= $data['kategori'] ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Tahun</label>
                    <div class="col-sm-10">
                    <input type="number" class="form-control" name="tahun" value="<?= $data['tahun'] ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Jumlah</label>
                    <div class="col-sm-10">
                    <input type="number" class="form-control" name="jumlah" value="<?= $data['jumlah'] ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Harga</label>
                    <div class="col-sm-10">
                    <input type="number" class="form-control" name="harga" value="<?= $data['harga'] ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Gambar Barang</label>
                    <div class="col-sm-10">
                    <input type="file" name="gambar">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-10">
                    <img src="../assets/img/<?= $data['gambar'] ?>" style="width: 150px; margin-left:20%;">
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
    </div>
</div>

<?php

    if(isset($_POST['simpan']))
    {   
        $id_barang = $_POST['id_barang'];
        $nama_barang = $_POST['nama_barang'];
        $kategori = $_POST['kategori'];
        $tahun = $_POST['tahun'];
        $jumlah = $_POST['jumlah'];
        $harga = $_POST['harga'];
        $gambar = $_FILES['gambar']['name'];

        if($gambar !="") {
            $ekstensi_diperbolehkan = array('png','jpg');
            $x = explode('.', $gambar); 
            $ekstensi = strtolower(end($x));
            $file_tmp = $_FILES['gambar']['tmp_name'];   
            $angka_acak     = rand(1,999);
            $nama_gambar_baru = $angka_acak.'-'.$gambar;

            if(in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
                move_uploaded_file($file_tmp, '../assets/img/'.$nama_gambar_baru);
                $query = mysqli_query($koneksi, "UPDATE tb_barang SET id_barang='$id_barang', nama_barang='$nama_barang', kategori='$kategori', tahun='$tahun', jumlah='$jumlah', harga='$harga', gambar='$nama_gambar_baru' where id_barang='$id_barang'");
                if($query)
                {
                    echo "<script>
                            alert('Data Berhasil Diubah');
                            window.location='index.php?page=barang'
                        </script>";
                } else {
                    echo "<script>
                            alert('Data Gagal Diubah');
                            window.location='index.php?page=barang'
                        </script>";
                }
            } else {
                echo "<script>
                            alert('Extensi yang di perbolehkan jpg atau png');
                            window.location='index.php?page=barang'
                      </script>";
            }
        } else {
            $query = mysqli_query($koneksi, "UPDATE tb_barang SET id_barang='$id_barang', nama_barang='$nama_barang', kategori='$kategori', tahun='$tahun', jumlah='$jumlah', harga='$harga' where id_barang='$id_barang'");
            if($query)
            {
                echo "<script>
                        alert('Data Berhasil Diubah);
                        window.location='index.php?page=barang'
                    </script>";
            } else {
                echo "<script>
                        alert('Data Gagal Diubah');
                        window.location='index.php?page=barang'
                     </script>";
            }
        }
    } 
?>    