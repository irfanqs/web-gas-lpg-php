<?php
/**
 * =====================================================
 * File: pembeli/notifikasi.php
 * Halaman notifikasi untuk pembeli
 * =====================================================
 */

$id_user = $_SESSION['id_user'];

// Tandai semua notifikasi sebagai sudah dibaca
if (isset($_GET['mark_read'])) {
    mysqli_query($koneksi, "UPDATE tb_notifikasi SET is_read = 1 WHERE id_user = $id_user");
    echo "<script>window.location='index.php?page=notifikasi';</script>";
    exit();
}

// Ambil semua notifikasi untuk user ini
$query_notifikasi = mysqli_query($koneksi, "
    SELECT n.*, p.kode_pesanan 
    FROM tb_notifikasi n 
    LEFT JOIN tb_pesanan p ON n.id_pesanan = p.id_pesanan 
    WHERE n.id_user = $id_user 
    ORDER BY n.created_at DESC
");

// Hitung notifikasi belum dibaca
$query_unread = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_notifikasi WHERE id_user = $id_user AND is_read = 0");
$unread_count = mysqli_fetch_assoc($query_unread)['total'];
?>

<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4><i class="fas fa-bell mr-2" style="color: #f39c12;"></i>Notifikasi</h4>
                <p class="text-muted">Pemberitahuan terkait pesanan Anda</p>
            </div>
            <?php if ($unread_count > 0): ?>
            <a href="index.php?page=notifikasi&mark_read=1" class="btn btn-outline-primary">
                <i class="fas fa-check-double mr-2"></i>Tandai Semua Dibaca
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Daftar Notifikasi -->
    <div class="card">
        <div class="card-body">
            <?php if (mysqli_num_rows($query_notifikasi) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($query_notifikasi)): ?>
                <div class="card mb-3 <?php echo $row['is_read'] ? '' : 'border-warning'; ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-1">
                                    <?php if (!$row['is_read']): ?>
                                    <span class="badge badge-warning mr-2">Baru</span>
                                    <?php endif; ?>
                                    <?php echo $row['judul']; ?>
                                </h5>
                            </div>
                            <small class="text-muted">
                                <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                            </small>
                        </div>
                        <hr>
                        <p class="mb-0"><?php echo $row['pesan']; ?></p>
                        <?php if ($row['id_pesanan']): ?>
                        <a href="index.php?page=detail_pesanan&id=<?php echo $row['id_pesanan']; ?>" class="btn btn-sm btn-info mt-2">
                            <i class="fas fa-eye mr-1"></i>Lihat Pesanan
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada notifikasi</h5>
                <p class="text-muted">Notifikasi akan muncul di sini saat ada update pesanan</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
