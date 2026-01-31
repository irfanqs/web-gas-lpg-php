<?php
    $id = $_GET['id_user'];
    $query = mysqli_query($koneksi, "DELETE FROM user WHERE id_user = '$id'");
    if($query)
        {
            echo "<script>
                    alert('Data Berhasil Dihapus');
                    window.location='index.php?page=pegawai'
                  </script>";
        } else {
            echo "<script>
            alert('Data Gagal Dihapus');
            window.location='index.php?page=pegawai'
          </script>";
        }
?>