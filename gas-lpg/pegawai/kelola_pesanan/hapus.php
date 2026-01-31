<?php
    $id = $_GET['id_pesanan'];
    $query = mysqli_query($koneksi, "DELETE FROM tb_pesanan WHERE id_pesanan = '$id'");
    if($query)
        {
            echo "<script>
                    alert('Data Berhasil Dihapus');
                    window.location='index.php?page=kelola_pesanan'
                  </script>";
        } else {
            echo "<script>
            alert('Data Gagal Dihapus');
            window.location='index.php?page=kelola_pesanan'
          </script>";
        }
?>