<?php
/**
 * =====================================================
 * File: pembeli/produk.php
 * Halaman untuk melihat dan memesan produk gas LPG
 * =====================================================
 */

// Ambil data produk dari database
$query_produk = mysqli_query($koneksi, "SELECT * FROM tb_produk WHERE status = 'aktif'");
?>

<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-fire mr-2" style="color: #f39c12;"></i>Pesan Gas LPG</h4>
            <p class="text-muted">Pilih produk dan jumlah yang ingin Anda pesan</p>
        </div>
    </div>

    <!-- Daftar Produk -->
    <div class="row">
        <?php while ($produk = mysqli_fetch_assoc($query_produk)): ?>
        <div class="col-md-6 col-lg-4 d-flex">
            <div class="card w-100 d-flex flex-column">
                <!-- Gambar Produk -->
                <div class="card-header text-center" style="background-color: #f39c12; height: 180px; display: flex; align-items: center; justify-content: center;">
                    <img src="../uploads/produk/<?php echo $produk['gambar'] ?: 'default.png'; ?>" 
                         alt="<?php echo $produk['nama_produk']; ?>" 
                         style="max-height: 150px; max-width: 100%;">
                </div>
                
                <div class="card-body d-flex flex-column">
                    <!-- Nama Produk -->
                    <h5 class="card-title" style="min-height: 48px;"><?php echo $produk['nama_produk']; ?></h5>
                    
                    <!-- Deskripsi -->
                    <p class="card-text text-muted" style="font-size: 14px; min-height: 42px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                        <?php echo $produk['deskripsi']; ?>
                    </p>
                    
                    <!-- Harga -->
                    <h4 style="color: #e74c3c;">
                        <?php echo formatRupiah($produk['harga']); ?>
                    </h4>
                    
                    <!-- Stok -->
                    <p class="mb-3">
                        <span class="badge <?php echo ($produk['stok'] > 0) ? 'badge-success' : 'badge-danger'; ?>">
                            Stok: <?php echo $produk['stok']; ?>
                        </span>
                    </p>
                    
                    <!-- Form Pesan -->
                    <div class="mt-auto">
                        <?php if ($produk['stok'] > 0): ?>
                        <form action="index.php?page=checkout" method="POST">
                            <input type="hidden" name="id_produk" value="<?php echo $produk['id_produk']; ?>">
                            
                            <!-- Input Jumlah -->
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Jumlah</span>
                                </div>
                                <input type="number" class="form-control" name="jumlah" 
                                       value="1" min="1" max="<?php echo $produk['stok']; ?>" required>
                            </div>
                            
                            <!-- Tombol Beli -->
                            <button type="submit" name="beli" class="btn btn-block" 
                                    style="background-color: #e74c3c; color: white;">
                                <i class="fas fa-shopping-cart mr-2"></i>Beli Sekarang
                            </button>
                        </form>
                        <?php else: ?>
                        <button class="btn btn-block btn-secondary" disabled>
                            <i class="fas fa-times-circle mr-2"></i>Stok Habis
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>
