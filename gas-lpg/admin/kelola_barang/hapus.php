<?php
    $id = $_GET['id_barang'];
    $query = mysqli_query($koneksi, "DELETE FROM tb_barang WHERE id_barang = '$id'");
    if($query)
        {
            echo "<script>
                    alert('Data Berhasil Dihapus');
                    window.location='index.php?page=barang'
                  </script>";
        } else {
            echo "<script>
            alert('Data Gagal Dihapus');
            window.location='index.php?page=barang'
          </script>";
        }
?>