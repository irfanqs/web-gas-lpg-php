<?php
    $id = $_GET['id_transaksi'];
    $query = mysqli_query($koneksi, "DELETE FROM tb_transaksi WHERE id_transaksi = '$id'");
    if($query)
        {
            echo "<script>
                    alert('Data Berhasil Dihapus');
                    window.location='index.php?page=penjualan'
                  </script>";
        } else {
            echo "<script>
            alert('Data Gagal Dihapus');
            window.location='index.php?page=penjualan'
          </script>";
        }
?>