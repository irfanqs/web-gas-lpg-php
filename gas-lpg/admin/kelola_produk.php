<?php
/**
 * =====================================================
 * File: admin/kelola_produk.php
 * Halaman untuk mengelola produk gas LPG
 * =====================================================
 */

// Proses hapus produk
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM tb_produk WHERE id_produk = $id");
    echo "<script>alert('Produk berhasil dihapus!'); window.location='index.php?page=kelola_produk';</script>";
}

// Ambil semua produk
$query_produk = mysqli_query($koneksi, "SELECT * FROM tb_produk ORDER BY id_produk DESC");
?>

<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-fire mr-2" style="color: #f39c12;"></i>Kelola Produk</h4>
            <p class="text-muted">Daftar produk gas LPG</p>
        </div>
    </div>

    <!-- Tombol Tambah -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="index.php?page=tambah_produk" class="btn" style="background-color: #27ae60; color: white;">
                <i class="fas fa-plus mr-2"></i>Tambah Produk
            </a>
        </div>
    </div>

    <!-- Tabel Produk -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead style="background-color: #2c3e50; color: white;">
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($query_produk)): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <img src="../uploads/produk/<?php echo $row['gambar'] ?: 'default.png'; ?>" 
                                     alt="<?php echo $row['nama_produk']; ?>" 
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td><?php echo $row['nama_produk']; ?></td>
                            <td><?php echo formatRupiah($row['harga']); ?></td>
                            <td><?php echo $row['stok']; ?></td>
                            <td>
                                <span class="badge <?php echo ($row['status'] == 'aktif') ? 'badge-success' : 'badge-danger'; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="index.php?page=ubah_produk&id=<?php echo $row['id_produk']; ?>" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="index.php?page=kelola_produk&hapus=<?php echo $row['id_produk']; ?>" 
                                   class="btn btn-sm btn-danger" title="Hapus"
                                   onclick="return confirm('Yakin ingin menghapus produk ini?');">
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
