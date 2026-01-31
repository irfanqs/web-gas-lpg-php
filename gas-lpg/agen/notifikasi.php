<?php
/**
 * =====================================================
 * File: agen/notifikasi.php
 * Halaman notifikasi agen
 * =====================================================
 */

$id_user = $_SESSION['id_user'];

// Tandai semua sebagai dibaca
mysqli_query($koneksi, "UPDATE tb_notifikasi SET is_read = 1 WHERE id_user = $id_user");

// Ambil notifikasi
$query = mysqli_query($koneksi, "
    SELECT * FROM tb_notifikasi 
    WHERE id_user = $id_user 
    ORDER BY created_at DESC 
    LIMIT 50
");
?>

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Notifikasi</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <?php if (mysqli_num_rows($query) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($query)): ?>
                        <div class="callout callout-info">
                            <h5><?php echo $row['judul']; ?></h5>
                            <p><?php echo $row['pesan']; ?></p>
                            <small class="text-muted">
                                <i class="fas fa-clock mr-1"></i>
                                <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                            </small>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada notifikasi</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
