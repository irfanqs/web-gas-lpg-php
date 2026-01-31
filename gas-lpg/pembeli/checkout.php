<?php
/**
 * =====================================================
 * File: pembeli/checkout.php
 * Halaman checkout untuk mengisi data pengantaran
 * dan melakukan pembayaran via Midtrans
 * =====================================================
 */

// Cek apakah ada data produk yang dikirim
if (!isset($_POST['beli']) && !isset($_SESSION['checkout'])) {
    // Redirect ke halaman produk jika tidak ada data
    echo "<script>
            alert('Silakan pilih produk terlebih dahulu!');
            window.location='index.php?page=produk';
          </script>";
    exit();
}

// Simpan data checkout ke session jika baru dari form produk
if (isset($_POST['beli'])) {
    $_SESSION['checkout'] = [
        'id_produk' => $_POST['id_produk'],
        'jumlah' => $_POST['jumlah']
    ];
}

// Ambil data dari session
$id_produk = $_SESSION['checkout']['id_produk'];
$jumlah = $_SESSION['checkout']['jumlah'];

// Ambil data produk dari database
$query_produk = mysqli_query($koneksi, "SELECT * FROM tb_produk WHERE id_produk = $id_produk");
$produk = mysqli_fetch_assoc($query_produk);

// Hitung total harga
$total_harga = $produk['harga'] * $jumlah;

// Ambil data user untuk pre-fill form
$id_user = $_SESSION['id_user'];
$query_user = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = $id_user");
$user = mysqli_fetch_assoc($query_user);

// =====================================================
// PROSES CHECKOUT
// =====================================================
if (isset($_POST['checkout'])) {
    // Ambil data dari form
    $nama_depan = escapeString($_POST['nama_depan']);
    $nama_belakang = escapeString($_POST['nama_belakang']);
    $telepon = escapeString($_POST['telepon']);
    $alamat_pengantaran = escapeString($_POST['alamat_pengantaran']);
    $catatan = escapeString($_POST['catatan']);
    
    // Generate kode pesanan unik
    $kode_pesanan = generateKodePesanan();
    
    // Insert pesanan ke database
    $query_insert = mysqli_query($koneksi, "INSERT INTO tb_pesanan 
        (kode_pesanan, id_user, id_produk, jumlah, total_harga, nama_depan, nama_belakang, telepon, alamat_pengantaran, catatan, status) 
        VALUES 
        ('$kode_pesanan', $id_user, $id_produk, $jumlah, $total_harga, '$nama_depan', '$nama_belakang', '$telepon', '$alamat_pengantaran', '$catatan', 'pending')");
    
    if ($query_insert) {
        // Ambil ID pesanan yang baru dibuat
        $id_pesanan = mysqli_insert_id($koneksi);
        
        // Simpan ke session untuk halaman pembayaran
        $_SESSION['id_pesanan'] = $id_pesanan;
        $_SESSION['kode_pesanan'] = $kode_pesanan;
        
        // Hapus data checkout dari session
        unset($_SESSION['checkout']);
        
        // Redirect ke halaman pembayaran menggunakan JavaScript
        echo "<script>window.location='index.php?page=pembayaran';</script>";
        exit();
    } else {
        $error_message = 'Gagal membuat pesanan. Silakan coba lagi!';
    }
}
?>

<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-shopping-cart mr-2" style="color: #f39c12;"></i>Checkout</h4>
            <p class="text-muted">Lengkapi data pengantaran untuk melanjutkan pesanan</p>
        </div>
    </div>

    <!-- Tampilkan error jika ada -->
    <?php if (isset($error_message)): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <?php echo $error_message; ?>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Kolom Form Data Pengantaran -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" style="background-color: #2c3e50; color: white;">
                    <h5 class="mb-0"><i class="fas fa-user mr-2"></i>Informasi Pengantaran</h5>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="row">
                            <!-- Nama Depan -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_depan">Nama Depan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_depan" name="nama_depan" 
                                           value="<?php echo $user['nama_depan']; ?>" readonly 
                                           style="background-color: #e9ecef;">
                                </div>
                            </div>
                            <!-- Nama Belakang -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_belakang">Nama Belakang</label>
                                    <input type="text" class="form-control" id="nama_belakang" name="nama_belakang" 
                                           value="<?php echo $user['nama_belakang']; ?>" readonly
                                           style="background-color: #e9ecef;">
                                </div>
                            </div>
                        </div>

                        <!-- Telepon -->
                        <div class="form-group">
                            <label for="telepon">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="telepon" name="telepon" 
                                   value="<?php echo $user['telepon']; ?>" readonly
                                   style="background-color: #e9ecef;">
                        </div>

                        <!-- Alamat Pengantaran -->
                        <div class="form-group">
                            <label for="alamat_pengantaran">Alamat Pengantaran <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="alamat_pengantaran" name="alamat_pengantaran" 
                                      rows="3" readonly style="background-color: #e9ecef;"><?php echo $user['alamat']; ?></textarea>
                            <small class="text-muted">Alamat sesuai data registrasi. <a href="index.php?page=profil">Ubah di Profil</a> jika perlu.</small>
                        </div>

                        <!-- Catatan -->
                        <div class="form-group">
                            <label for="catatan">Catatan (Opsional)</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="2" 
                                      placeholder="Contoh: Rumah warna biru, sebelah warung"></textarea>
                        </div>

                        <!-- Tombol -->
                        <div class="row mt-4">
                            <div class="col-6">
                                <a href="index.php?page=produk" class="btn btn-secondary btn-block">
                                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                                </a>
                            </div>
                            <div class="col-6">
                                <button type="submit" name="checkout" class="btn btn-block" 
                                        style="background-color: #e74c3c; color: white;">
                                    <i class="fas fa-credit-card mr-2"></i>Lanjut Bayar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Ringkasan Pesanan -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header" style="background-color: #f39c12; color: white;">
                    <h5 class="mb-0"><i class="fas fa-receipt mr-2"></i>Ringkasan Pesanan</h5>
                </div>
                <div class="card-body">
                    <!-- Detail Produk -->
                    <div class="d-flex align-items-center mb-3">
                        <img src="../uploads/produk/<?php echo $produk['gambar'] ?: 'default.png'; ?>" 
                             alt="<?php echo $produk['nama_produk']; ?>" 
                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;" class="mr-3">
                        <div>
                            <h6 class="mb-0"><?php echo $produk['nama_produk']; ?></h6>
                            <small class="text-muted"><?php echo formatRupiah($produk['harga']); ?> x <?php echo $jumlah; ?></small>
                        </div>
                    </div>

                    <hr>

                    <!-- Rincian Harga -->
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span><?php echo formatRupiah($total_harga); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Ongkos Kirim</span>
                        <span class="text-success">Gratis</span>
                    </div>

                    <hr>

                    <!-- Total -->
                    <div class="d-flex justify-content-between">
                        <strong>Total</strong>
                        <strong style="color: #e74c3c; font-size: 1.2em;">
                            <?php echo formatRupiah($total_harga); ?>
                        </strong>
                    </div>
                </div>
            </div>

            <!-- Info Pembayaran -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="fas fa-info-circle mr-2" style="color: #3498db;"></i>Informasi</h6>
                    <ul class="mb-0" style="padding-left: 20px; font-size: 14px;">
                        <li>Pembayaran via Transfer Bank / Virtual Account</li>
                        <li>Batas waktu pembayaran: <strong>30 menit</strong></li>
                        <li>Pesanan akan diantar setelah pembayaran dikonfirmasi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
