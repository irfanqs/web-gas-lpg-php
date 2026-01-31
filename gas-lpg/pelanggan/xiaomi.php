<div class="container-fluid">
    <div class="row">
        <?php 
            $query = mysqli_query($koneksi, "SELECT * FROM tb_barang where kategori='Xiaomi'");
            while($data = mysqli_fetch_assoc($query)) :
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
        <?php endwhile; ?>
    </div>
</div>