<div class="container-fluid">
    <form action="" method="post">
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="cari" placeholder="Masukan Pencarian">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-warning" style="color: #f1f2f6; background-color: #F79F1F;"> <i class="fas fa-search"></i> Cari </button>
                </div>
            </div>
        </div>
    </form>
    <div class="row">
        <?php
            
            if(isset($_POST['cari'])) {
                $cari = $_POST['cari'];
                $query = mysqli_query($koneksi, "SELECT * FROM tb_barang where nama_barang like '%".$cari."%'");
            } else {
                $query = mysqli_query($koneksi, "SELECT * FROM tb_barang");
            }
              $cek = mysqli_num_rows($query);
              if($cek > 0 ) 
              {
                while($data = mysqli_fetch_assoc($query)) {
        ?>
        <div class="col-sm-4">
            <div class="card">
                <div class="card-header" style="border:none;">
                    <img src="../assets/img/<?= $data['gambar'] ?>" class="card-img-top img-thumbnail">
                </div>
                <div class="card-body">
                    <h2 class="card-title"><?= $data['nama_barang']; ?></h2>
                    <p class="card-text">Rp. <?= number_format($data['harga'],0,',',''); ?></p>
                    <p class="card-text"><span class="text-muted">Stok <?= $data['jumlah']; ?></span></p>
                    <a href="index.php?page=pembelian&id_barang=<?= $data['id_barang'] ?>" class="btn social-auth-links text-center mb-3 btn-block" style="background-color: #e74c3c; color:#ecf0f1; border-radius:9px;"> <i class="fas fa-cart-plus"></i> Beli</a>
                </div>
            </div>
        </div>
        <?php }} else {  ?>
            <div class="alert alert-danger alert-dismissible fade show ml-2" role="alert">
            Maaf data Tidak Ditemukan
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color: #f1f2f6;">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
        <?php } ?>
    </div>
</div>