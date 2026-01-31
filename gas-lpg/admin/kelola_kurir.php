<?php
/**
 * =====================================================
 * File: admin/kelola_kurir.php
 * Halaman untuk mengelola data kurir
 * =====================================================
 */

// Proses hapus kurir
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM user WHERE id_user = $id AND role = 'Kurir'");
    echo "<script>alert('Kurir berhasil dihapus!'); window.location='index.php?page=kelola_kurir';</script>";
}

// Ambil semua kurir
$query_kurir = mysqli_query($koneksi, "SELECT * FROM user WHERE role = 'Kurir' ORDER BY created_at DESC");
?>

<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-motorcycle mr-2" style="color: #f39c12;"></i>Data Kurir</h4>
            <p class="text-muted">Daftar semua kurir</p>
        </div>
    </div>

    <!-- Tombol Tambah -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="index.php?page=tambah_kurir" class="btn" style="background-color: #27ae60; color: white;">
                <i class="fas fa-plus mr-2"></i>Tambah Kurir
            </a>
        </div>
    </div>

    <!-- Tabel Kurir -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead style="background-color: #2c3e50; color: white;">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($query_kurir)): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['telepon']; ?></td>
                            <td><?php echo $row['alamat']; ?></td>
                            <td>
                                <a href="index.php?page=ubah_kurir&id=<?php echo $row['id_user']; ?>" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="index.php?page=kelola_kurir&hapus=<?php echo $row['id_user']; ?>" 
                                   class="btn btn-sm btn-danger" title="Hapus"
                                   onclick="return confirm('Yakin ingin menghapus kurir ini?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
