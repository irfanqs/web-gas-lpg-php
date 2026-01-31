<?php
/**
 * =====================================================
 * File: pembeli/home.php
 * Halaman dashboard/home untuk pembeli
 * =====================================================
 */
?>

<div class="container-fluid">
    <!-- Card Selamat Datang -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- Kolom Teks -->
                <div class="col-md-6" style="margin-top:8%;">
                    <h2 class="ml-4">Selamat Datang, <?php echo $_SESSION['nama']; ?>!</h2>
                    <h5 style="margin-left:5.1%;">
                        <span>Pesan Gas LPG 3 Kg dengan mudah dan cepat</span>
                    </h5>
                    <p class="ml-4 text-muted">
                        Nikmati kemudahan memesan gas LPG langsung dari rumah. 
                        Pesanan Anda akan diantar oleh kurir kami.
                    </p>
                    <!-- Tombol Pesan Sekarang -->
                    <a href="index.php?page=produk" class="btn ml-4 mt-1" style="background-color: #e74c3c; color:#ecf0f1; border-radius:9px;">
                        <i class="fas fa-shopping-cart mr-2"></i>Pesan Sekarang
                    </a>
                </div>
                <!-- Kolom Gambar -->
                <div class="col-md-6 text-center">
                    <img src="../assets/dist/img/Online shopping _Monochromatic.png" class="img-fluid" style="max-height: 300px;" alt="Gas Delivery">
                </div>
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="row mt-4">
        <!-- Card: Total Pesanan -->
        <div class="col-md-4">
            <?php
            // Hitung total pesanan user (tidak termasuk cancelled dan expired)
            $id_user = $_SESSION['id_user'];
            $query_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_pesanan WHERE id_user = $id_user AND status NOT IN ('cancelled', 'expired')");
            $total_pesanan = mysqli_fetch_assoc($query_total)['total'];
            ?>
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?php echo $total_pesanan; ?></h3>
                    <p>Total Pesanan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <a href="index.php?page=riwayat" class="small-box-footer">
                    Lihat Riwayat <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card: Pesanan Aktif -->
        <div class="col-md-4">
            <?php
            // Hitung pesanan yang sedang aktif (pending, paid, confirmed, delivering)
            $query_aktif = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_pesanan WHERE id_user = $id_user AND status IN ('pending', 'paid', 'confirmed', 'delivering')");
            $pesanan_aktif = mysqli_fetch_assoc($query_aktif)['total'];
            ?>
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?php echo $pesanan_aktif; ?></h3>
                    <p>Pesanan Aktif</p>
                </div>
                <div class="icon">
                    <i class="fas fa-truck"></i>
                </div>
                <a href="index.php?page=riwayat" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card: Pesanan Selesai -->
        <div class="col-md-4">
            <?php
            // Hitung pesanan yang sudah selesai
            $query_selesai = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_pesanan WHERE id_user = $id_user AND status = 'completed'");
            $pesanan_selesai = mysqli_fetch_assoc($query_selesai)['total'];
            ?>
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?php echo $pesanan_selesai; ?></h3>
                    <p>Pesanan Selesai</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="index.php?page=riwayat" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
