<?php
/**
 * =====================================================
 * File: admin/home.php
 * Halaman dashboard untuk admin
 * =====================================================
 */

// Hitung statistik
$query_pesanan_baru = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_pesanan WHERE status = 'paid'");
$pesanan_baru = mysqli_fetch_assoc($query_pesanan_baru)['total'];

$query_total_pesanan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_pesanan WHERE status NOT IN ('cancelled', 'expired')");
$total_pesanan = mysqli_fetch_assoc($query_total_pesanan)['total'];

$query_pesanan_selesai = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_pesanan WHERE status = 'completed'");
$pesanan_selesai = mysqli_fetch_assoc($query_pesanan_selesai)['total'];

$query_total_pembeli = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM user WHERE role = 'Pembeli'");
$total_pembeli = mysqli_fetch_assoc($query_total_pembeli)['total'];

$query_total_kurir = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM user WHERE role = 'Kurir'");
$total_kurir = mysqli_fetch_assoc($query_total_kurir)['total'];

// Hitung pendapatan bulan ini
$bulan_ini = date('Y-m');
$query_pendapatan = mysqli_query($koneksi, "SELECT SUM(total_harga) as total FROM tb_pesanan WHERE status = 'completed' AND DATE_FORMAT(waktu_selesai, '%Y-%m') = '$bulan_ini'");
$pendapatan_bulan = mysqli_fetch_assoc($query_pendapatan)['total'] ?? 0;
?>

<div class="container-fluid">
    <!-- Card Selamat Datang -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <!-- Kolom Teks -->
                <div class="col-md-7" style="margin-top:3%;">
                    <h2 class="ml-4">Dashboard Admin</h2>
                    <h5 style="margin-left:5.1%;">
                        <span>Selamat datang, <?php echo $_SESSION['nama']; ?>!</span>
                    </h5>
                    <p class="ml-4 text-muted">
                        Kelola pesanan, produk, dan pengguna dari panel admin ini.
                    </p>
                </div>
                <!-- Kolom Gambar -->
                <div class="col-md-5 text-center">
                    <img src="../assets/dist/img/CTO_Monochromatic.png" class="img-fluid" style="max-height: 200px;" alt="Admin Dashboard">
                </div>
            </div>
        </div>
    </div>

    <!-- Info Cards Row 1 -->
    <div class="row">
        <!-- Card: Pesanan Menunggu Konfirmasi -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?php echo $pesanan_baru; ?></h3>
                    <p>Menunggu Konfirmasi</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="index.php?page=konfirmasi_pesanan" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card: Total Pesanan -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?php echo $total_pesanan; ?></h3>
                    <p>Total Pesanan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="index.php?page=kelola_pesanan" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card: Pesanan Selesai -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?php echo $pesanan_selesai; ?></h3>
                    <p>Pesanan Selesai</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="index.php?page=kelola_pesanan" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card: Pendapatan Bulan Ini -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 style="font-size: 1.5rem;"><?php echo formatRupiah($pendapatan_bulan); ?></h3>
                    <p>Pendapatan Bulan Ini</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <a href="index.php?page=laporan" class="small-box-footer">
                    Lihat Laporan <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Info Cards Row 2 -->
    <div class="row">
        <!-- Card: Total Pembeli -->
        <div class="col-lg-4 col-6">
            <div class="small-box" style="background-color: #9b59b6; color: white;">
                <div class="inner">
                    <h3><?php echo $total_pembeli; ?></h3>
                    <p>Total Pembeli</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="index.php?page=kelola_pembeli" class="small-box-footer" style="color: white;">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card: Total Kurir -->
        <div class="col-lg-4 col-6">
            <div class="small-box" style="background-color: #1abc9c; color: white;">
                <div class="inner">
                    <h3><?php echo $total_kurir; ?></h3>
                    <p>Total Kurir</p>
                </div>
                <div class="icon">
                    <i class="fas fa-motorcycle"></i>
                </div>
                <a href="index.php?page=kelola_kurir" class="small-box-footer" style="color: white;">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card: Jenis Produk -->
        <div class="col-lg-4 col-6">
            <?php
            $query_jenis = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_produk WHERE status = 'aktif'");
            $total_jenis = mysqli_fetch_assoc($query_jenis)['total'] ?? 0;
            ?>
            <div class="small-box" style="background-color: #e67e22; color: white;">
                <div class="inner">
                    <h3><?php echo $total_jenis; ?></h3>
                    <p>Jenis Produk</p>
                </div>
                <div class="icon">
                    <i class="fas fa-fire"></i>
                </div>
                <a href="index.php?page=kelola_produk" class="small-box-footer" style="color: white;">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Pesanan Terbaru -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background-color: #2c3e50; color: white;">
                    <h5 class="mb-0"><i class="fas fa-list mr-2"></i>Pesanan Terbaru</h5>
                </div>
                <div class="card-body">
                    <?php
                    $query_terbaru = mysqli_query($koneksi, "
                        SELECT p.*, u.nama_depan, u.nama_belakang, pr.nama_produk 
                        FROM tb_pesanan p 
                        JOIN user u ON p.id_user = u.id_user 
                        JOIN tb_produk pr ON p.id_produk = pr.id_produk 
                        ORDER BY p.waktu_pesan DESC 
                        LIMIT 5
                    ");
                    ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Pembeli</th>
                                    <th>Produk</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($query_terbaru)): ?>
                                <tr>
                                    <td><?php echo $row['kode_pesanan']; ?></td>
                                    <td><?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?></td>
                                    <td><?php echo $row['nama_produk']; ?> (<?php echo $row['jumlah']; ?>)</td>
                                    <td><?php echo formatRupiah($row['total_harga']); ?></td>
                                    <td>
                                        <?php
                                        $badges = [
                                            'pending' => 'badge-warning',
                                            'paid' => 'badge-info',
                                            'confirmed' => 'badge-primary',
                                            'delivering' => 'badge-secondary',
                                            'completed' => 'badge-success',
                                            'cancelled' => 'badge-danger',
                                            'expired' => 'badge-dark'
                                        ];
                                        $badge_class = $badges[$row['status']] ?? 'badge-secondary';
                                        ?>
                                        <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($row['status']); ?></span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($row['waktu_pesan'])); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
