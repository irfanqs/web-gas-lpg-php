<?php
/**
 * =====================================================
 * File: admin/kelola_pembeli.php
 * Halaman untuk melihat data pembeli
 * =====================================================
 */

// Ambil semua pembeli
$query_pembeli = mysqli_query($koneksi, "SELECT * FROM user WHERE role = 'Pembeli' ORDER BY created_at DESC");
?>

<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-user-tag mr-2" style="color: #f39c12;"></i>Data Pembeli</h4>
            <p class="text-muted">Daftar semua pembeli terdaftar</p>
        </div>
    </div>

    <!-- Tabel Pembeli -->
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
                            <th>Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($query_pembeli)): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row['nama_depan'] . ' ' . $row['nama_belakang']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['telepon']; ?></td>
                            <td><?php echo $row['alamat']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
