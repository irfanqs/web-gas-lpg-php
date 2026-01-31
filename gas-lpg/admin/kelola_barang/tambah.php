<div class="container-fluid">
    <div class="card" style="width: 50rem; margin-left:12%;">
        <h4 class="card-header"><i class="fas fa-clipboard-list" style="color: #f39c12;"></i> <small>Form Tambah</small> </h4>
        <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-2 col-form-label">Nama Barang</label>
                    <div class="col-sm-10">
                    <input type="hidden" name="id_barang">
                    <input type="text" class="form-control" name="nama_barang">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Kategori</label>
                    <div class="col-sm-10">
                    <input type="text" class="form-control" name="kategori">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Tahun</label>
                    <div class="col-sm-10">
                    <input type="number" class="form-control" name="tahun">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Jumlah</label>
                    <div class="col-sm-10">
                    <input type="number" class="form-control" name="jumlah">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Harga</label>
                    <div class="col-sm-10">
                    <input type="number" class="form-control" name="harga">
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
                $query = mysqli_query($koneksi, "INSERT INTO tb_barang VALUES('$id_barang', '$nama_barang', '$kategori', '$tahun', '$jumlah', '$harga', '$nama_gambar_baru')");
                if($query)
                {
                    echo "<script>
                            alert('Data Berhasil Ditambahkan');
                            window.location='index.php?page=barang'
                        </script>";
                } else {
                    echo "<script>
                            alert('Data Gagal Dimasukan');
                            window.location='index.php?page=tambah_barang'
                        </script>";
                }
            } else {
                echo "<script>
                            alert('Extensi yang di perbolehkan jpg atau png');
                            window.location='index.php?page=tambah_barang'
                      </script>";
            }
        } else {
            $query = mysqli_query($koneksi, "INSERT INTO tb_barang VALUES('$id_barang', '$nama_barang', '$kategori', '$tahun', '$jumlah', '$harga', null)");
            if($query)
            {
                echo "<script>
                        alert('Data Berhasil Ditambahkan');
                        window.location='index.php?page=barang'
                    </script>";
            } else {
                echo "<script>
                        alert('Data Gagal Dimasukan');
                        window.location='index.php?page=tambah_barang'
                     </script>";
            }
        }       
    } 
?>    