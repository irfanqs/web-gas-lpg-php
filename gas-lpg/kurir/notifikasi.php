<?php
/**
 * =====================================================
 * File: kurir/notifikasi.php
 * Halaman notifikasi untuk kurir
 * =====================================================
 */

$id_user = $_SESSION['id_user'];

// Tandai semua notifikasi sebagai sudah dibaca
mysqli_query($koneksi, "UPDATE tb_notifikasi SET is_read = 1 WHERE id_user = $id_user");

// Ambil semua notifikasi
$query_notif = mysqli_query($koneksi, "
    SELECT * FROM tb_notifikasi 
    WHERE id_user = $id_user 
    ORDER BY created_at DESC
");
?>

<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-bell mr-2" style="color: #f39c12;"></i>Notifikasi</h4>
            <p class="text-muted">Daftar semua notifikasi</p>
        </div>
    </div>

    <!-- Daftar Notifikasi -->
    <div class="card">
        <div class="card-body">
            <?php if (mysqli_num_rows($query_notif) > 0): ?>
                <?php while ($notif = mysqli_fetch_assoc($query_notif)): ?>
                <div class="callout callout-info">
                    <h5><?php echo $notif['judul']; ?></h5>
                    <p><?php echo $notif['pesan']; ?></p>
                    <small class="text-muted">
                        <i class="fas fa-clock mr-1"></i>
                        <?php echo date('d/m/Y H:i', strtotime($notif['created_at'])); ?>
                    </small>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada notifikasi</h5>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
