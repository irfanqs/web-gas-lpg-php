<?php
    $id = $_GET['id_user'];
    $query = mysqli_query($koneksi, "SELECT * FROM user where id_user ='$id'");
    $data  = mysqli_fetch_assoc($query); 
?>

<div class="container-fluid">
    <div class="card" style="width: 50rem; margin-left:12%;">
        <h4 class="card-header"><i class="fas fa-clipboard-list" style="color: #f39c12;"></i> <small>Form Edit</small> </h4>
        <div class="card-body">
            <form action="" method="post">
                <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-2 col-form-label">Nama</label>
                    <div class="col-sm-10">
                    <input type="hidden" name="id_user" value="<?= $data['id_user']; ?>">
                    <input type="text" class="form-control" name="nama" value="<?= $data['nama']; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-10">
                    <input type="text" class="form-control" name="password" value="<?= $data['password']; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Jenis Kelamin</label>
                    <div class="col-sm-10">
                    <select name="jk" class="form-control">
                        <option value="<?= $data['jk']; ?>"><?= $data['jk']; ?></option>
                        <option value="l">Laki - Laki</option>
                        <option value="p">Perempuan</option>
                    </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Posisi</label>
                    <div class="col-sm-10">
                    <select name="role" class="form-control">
                        <option value="<?= $data['jk']; ?>"><?= $data['jk']; ?></option>
                        <option value="Admin">Admin</option>
                        <option value="Pegawai">Pegawai</option>
                        <option value="Pelanggan">Pelanggan</option>
                    </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">No. Telpon</label>
                    <div class="col-sm-10">
                    <input type="number" class="form-control" name="no_telp" value="<?= $data['no_telp']; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Alamat</label>
                    <div class="col-sm-10">
                    <textarea name="alamat" cols="30" rows="10" class="form-control"><?= $data['alamat']; ?></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-10">
                    <button type="submit" class="btn btn-secondary" name="simpan">Simpan</button>
                    <a href="index.php?page=pegawai" class="btn" style="background-color:#e84118; color:white;">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php

    if(isset($_POST['simpan']))
    {   
        $id_user = $_POST['id_user'];
        $nama = $_POST['nama'];
        $password = $_POST['password'];
        $jk = $_POST['jk'];
        $role = $_POST['role'];
        $no_telp = $_POST['no_telp'];
        $alamat = $_POST['alamat'];

        $query = mysqli_query($koneksi, "UPDATE user SET id_user='$id_user', nama='$nama', password='$password', jk='$jk', role='$role', no_telp='$no_telp', alamat='$alamat' where id_user='$id_user'");
        if($query)
        {
            echo "<script>
                    alert('Data Berhasil Diubah');
                    window.location='index.php?page=pegawai'
                  </script>";
        } else {
            echo "<script>
            alert('Data Gagal Diubah');
            window.location='index.php?page=ubah_pegawai'
          </script>";
        }
    } 
?>    