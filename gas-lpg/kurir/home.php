<?php
/**
 * =====================================================
 * File: kurir/home.php
 * Halaman dashboard untuk kurir
 * =====================================================
 */

$id_kurir = $_SESSION['id_user'];

// Hitung statistik
$query_menunggu = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_pesanan WHERE id_kurir = $id_kurir AND status = 'confirmed'");
$pesanan_menunggu = mysqli_fetch_assoc($query_menunggu)['total'];

$query_sedang_antar = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_pesanan WHERE id_kurir = $id_kurir AND status = 'delivering'");
$sedang_antar = mysqli_fetch_assoc($query_sedang_antar)['total'];

$query_selesai = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_pesanan WHERE id_kurir = $id_kurir AND status = 'completed'");
$total_selesai = mysqli_fetch_assoc($query_selesai)['total'];

// Hitung pengantaran hari ini
$hari_ini = date('Y-m-d');
$query_hari_ini = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_pesanan WHERE id_kurir = $id_kurir AND status = 'completed' AND DATE(waktu_selesai) = '$hari_ini'");
$selesai_hari_ini = mysqli_fetch_assoc($query_hari_ini)['total'];
?>

<div class="container-fluid">
    <!-- Card Selamat Datang -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <!-- Kolom Teks -->
                <div class="col-md-7" style="margin-top:3%;">
                    <h2 class="ml-4">Dashboard Kurir</h2>
                    <h5 style="margin-left:5.1%;">
                        <span>Selamat datang, <?php echo $_SESSION['nama']; ?>!</span>
                    </h5>
                    <p class="ml-4 text-muted">
                        Lihat dan kelola pesanan yang harus diantar dari sini.
                    </p>
                </div>
                <!-- Kolom Gambar -->
                <div class="col-md-5 text-center">
                    <img src="../assets/dist/img/Progress _Monochromatic.png" class="img-fluid" style="max-height: 200px;" alt="Kurir Dashboard">
                </div>
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="row">
        <!-- Card: Pesanan Menunggu -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?php echo $pesanan_menunggu; ?></h3>
                    <p>Menunggu Diantar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="index.php?page=pesanan_antar" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card: Sedang Diantar -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?php echo $sedang_antar; ?></h3>
                    <p>Sedang Diantar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-motorcycle"></i>
                </div>
                <a href="index.php?page=sedang_antar" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card: Selesai Hari Ini -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?php echo $selesai_hari_ini; ?></h3>
                    <p>Selesai Hari Ini</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="index.php?page=riwayat" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card: Total Pengantaran -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><?php echo $total_selesai; ?></h3>
                    <p>Total Pengantaran</p>
                </div>
                <div class="icon">
                    <i class="fas fa-truck"></i>
                </div>
                <a href="index.php?page=riwayat" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Pesanan yang Harus Diantar -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background-color: #f39c12; color: white;">
                    <h5 class="mb-0"><i class="fas fa-truck mr-2"></i>Pesanan yang Harus Diantar</h5>
                </div>
                <div class="card-body">
                    <?php
                    $query_antar = mysqli_query($koneksi, "
                        SELECT p.*, pr.nama_produk 
                        FROM tb_pesanan p 
                        JOIN tb_produk pr ON p.id_produk = pr.id_produk 
                        WHERE p.id_kurir = $id_kurir AND p.status = 'confirmed'
                        ORDER BY p.waktu_konfirmasi ASC
                    ");
                    ?>
                    <?php if (mysqli_num_rows($query_antar) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Waktu Pesan</th>
                                    <th>Penerima</th>
                                    <th>Alamat</th>
                                    <th>Produk</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($query_antar)): ?>
                                <tr>
                                    <td><?php echo $row['kode_pesanan']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($row['waktu_pesan'])); ?></td>
                                    <td>
                                        <?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?><br>
                                        <small><?php echo $row['telepon']; ?></small>
                                    </td>
                                    <td><?php echo $row['alamat_pengantaran']; ?></td>
                                    <td><?php echo $row['nama_produk']; ?> (<?php echo $row['jumlah']; ?>)</td>
                                    <td>
                                        <a href="index.php?page=detail_antar&id=<?php echo $row['id_pesanan']; ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-motorcycle mr-1"></i>Antar
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5 class="text-muted">Tidak ada pesanan yang menunggu</h5>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
