<?php
/**
 * =====================================================
 * File: pembeli/pembayaran.php
 * Halaman pembayaran menggunakan Midtrans Snap
 * =====================================================
 */

// Cek apakah ada data pesanan
if (!isset($_SESSION['id_pesanan'])) {
    echo "<script>
            alert('Data pesanan tidak ditemukan!');
            window.location='index.php?page=produk';
          </script>";
    exit();
}

$id_pesanan = $_SESSION['id_pesanan'];
$kode_pesanan = $_SESSION['kode_pesanan'];

// =====================================================
// PROSES SIMULASI BAYAR (untuk testing tanpa Midtrans)
// =====================================================
if (isset($_POST['simulasi_bayar'])) {
    // Update status pesanan menjadi paid
    mysqli_query($koneksi, "UPDATE tb_pesanan SET status = 'paid', waktu_bayar = NOW() WHERE id_pesanan = $id_pesanan");
    
    // Update status pembayaran (pakai 'success' sesuai ENUM di database)
    mysqli_query($koneksi, "UPDATE tb_pembayaran SET status_pembayaran = 'success', metode_pembayaran = 'Simulasi' WHERE id_pesanan = $id_pesanan");
    
    // Hapus session checkout
    unset($_SESSION['id_pesanan']);
    unset($_SESSION['kode_pesanan']);
    
    echo "<script>
            alert('Simulasi pembayaran berhasil!');
            window.location='index.php?page=riwayat';
          </script>";
    exit();
}

// Ambil data pesanan dari database
$query_pesanan = mysqli_query($koneksi, "
    SELECT p.*, pr.nama_produk, pr.gambar, u.email as user_email
    FROM tb_pesanan p 
    JOIN tb_produk pr ON p.id_produk = pr.id_produk 
    JOIN user u ON p.id_user = u.id_user
    WHERE p.id_pesanan = $id_pesanan
");
$pesanan = mysqli_fetch_assoc($query_pesanan);

// Cek apakah pesanan masih pending
if ($pesanan['status'] != 'pending') {
    unset($_SESSION['id_pesanan']);
    unset($_SESSION['kode_pesanan']);
    echo "<script>
            alert('Pesanan sudah diproses sebelumnya!');
            window.location='index.php?page=riwayat';
          </script>";
    exit();
}

// =====================================================
// GENERATE SNAP TOKEN MIDTRANS
// =====================================================

// URL Midtrans API (Sandbox atau Production)
$midtrans_url = MIDTRANS_IS_PRODUCTION 
    ? 'https://app.midtrans.com/snap/v1/transactions' 
    : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

// Data transaksi untuk Midtrans
$transaction_data = [
    'transaction_details' => [
        'order_id' => $kode_pesanan,
        'gross_amount' => (int) $pesanan['total_harga']
    ],
    'customer_details' => [
        'first_name' => $pesanan['nama_depan'],
        'last_name' => $pesanan['nama_belakang'],
        'email' => $pesanan['user_email'],
        'phone' => $pesanan['telepon']
    ],
    'item_details' => [
        [
            'id' => $pesanan['id_produk'],
            'price' => (int) ($pesanan['total_harga'] / $pesanan['jumlah']),
            'quantity' => (int) $pesanan['jumlah'],
            'name' => $pesanan['nama_produk']
        ]
    ],
    'expiry' => [
        'unit' => 'minutes',
        'duration' => PAYMENT_EXPIRY_MINUTES // 30 menit
    ]
];

// Request ke Midtrans untuk mendapatkan Snap Token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $midtrans_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transaction_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Basic ' . base64_encode(MIDTRANS_SERVER_KEY . ':')
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// curl_close() deprecated di PHP 8.0+, tidak perlu dipanggil lagi

$snap_response = json_decode($response, true);
$snap_token = $snap_response['token'] ?? null;

// Jika gagal mendapatkan token, tampilkan error
if (!$snap_token) {
    $error_message = 'Gagal menghubungi payment gateway. Silakan coba lagi.';
    if (isset($snap_response['error_messages'])) {
        $error_message .= ' Error: ' . implode(', ', $snap_response['error_messages']);
    }
}

// Simpan snap token ke database untuk referensi
if ($snap_token) {
    // Cek apakah sudah ada data pembayaran
    $cek_pembayaran = mysqli_query($koneksi, "SELECT * FROM tb_pembayaran WHERE id_pesanan = $id_pesanan");
    
    if (mysqli_num_rows($cek_pembayaran) == 0) {
        // Insert data pembayaran baru
        $waktu_expired = date('Y-m-d H:i:s', strtotime('+' . PAYMENT_EXPIRY_MINUTES . ' minutes'));
        mysqli_query($koneksi, "
            INSERT INTO tb_pembayaran (id_pesanan, metode_pembayaran, midtrans_order_id, status_pembayaran, jumlah_bayar, waktu_expired) 
            VALUES ($id_pesanan, 'Midtrans', '$kode_pesanan', 'pending', {$pesanan['total_harga']}, '$waktu_expired')
        ");
    }
}
?>

<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-credit-card mr-2" style="color: #f39c12;"></i>Pembayaran</h4>
            <p class="text-muted">Selesaikan pembayaran untuk pesanan Anda</p>
        </div>
    </div>

    <!-- Tampilkan error jika ada -->
    <?php if (isset($error_message)): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <?php echo $error_message; ?>
        <br><br>
        <a href="index.php?page=produk" class="btn btn-secondary">Kembali ke Produk</a>
    </div>
    <?php else: ?>

    <div class="row">
        <!-- Kolom Detail Pesanan -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header" style="background-color: #2c3e50; color: white;">
                    <h5 class="mb-0"><i class="fas fa-receipt mr-2"></i>Detail Pesanan</h5>
                </div>
                <div class="card-body">
                    <!-- Kode Pesanan -->
                    <div class="alert alert-info">
                        <strong>Kode Pesanan:</strong> <?php echo $kode_pesanan; ?>
                    </div>

                    <!-- Detail Produk -->
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%">Produk</td>
                            <td>: <?php echo $pesanan['nama_produk']; ?></td>
                        </tr>
                        <tr>
                            <td>Jumlah</td>
                            <td>: <?php echo $pesanan['jumlah']; ?> unit</td>
                        </tr>
                        <tr>
                            <td>Penerima</td>
                            <td>: <?php echo $pesanan['nama_depan'] . ' ' . $pesanan['nama_belakang']; ?></td>
                        </tr>
                        <tr>
                            <td>Telepon</td>
                            <td>: <?php echo $pesanan['telepon']; ?></td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>: <?php echo $pesanan['alamat_pengantaran']; ?></td>
                        </tr>
                    </table>

                    <hr>

                    <!-- Total Pembayaran -->
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Total Pembayaran</h5>
                        <h4 class="mb-0" style="color: #e74c3c;">
                            <?php echo formatRupiah($pesanan['total_harga']); ?>
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Pembayaran -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header" style="background-color: #f39c12; color: white;">
                    <h5 class="mb-0"><i class="fas fa-credit-card mr-2"></i>Pembayaran</h5>
                </div>
                <div class="card-body text-center">
                    <!-- Info Batas Waktu -->
                    <div class="alert alert-warning">
                        <i class="fas fa-clock mr-2"></i>
                        <strong>Batas Waktu Pembayaran:</strong> <?php echo PAYMENT_EXPIRY_MINUTES; ?> menit
                        <p class="mb-0 mt-2" style="font-size: 14px;">
                            Pesanan akan otomatis dibatalkan jika tidak melakukan pembayaran dalam waktu yang ditentukan.
                        </p>
                    </div>

                    <p class="mb-4">Klik tombol di bawah untuk melanjutkan ke halaman pembayaran</p>

                    <!-- Tombol Bayar dengan Midtrans -->
                    <button id="pay-button" class="btn btn-lg btn-block" 
                            style="background-color: #27ae60; color: white;">
                        <i class="fas fa-credit-card mr-2"></i>Bayar Sekarang
                    </button>

                    <!-- Tombol Simulasi Bayar (untuk testing) -->
                    <form action="" method="POST" class="mt-2">
                        <button type="submit" name="simulasi_bayar" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-check-circle mr-2"></i>Simulasi Bayar (Testing)
                        </button>
                    </form>

                    <hr>

                    <p class="text-muted" style="font-size: 14px;">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Pembayaran diproses secara aman oleh Midtrans
                    </p>

                    <!-- Logo Metode Pembayaran -->
                    <div class="mt-3">
                        <img src="https://docs.midtrans.com/asset/image/main/midtrans-logo.png" 
                             alt="Midtrans" style="height: 30px;">
                    </div>
                </div>
            </div>

            <!-- Info Metode Pembayaran -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="fas fa-info-circle mr-2" style="color: #3498db;"></i>Metode Pembayaran Tersedia</h6>
                    <ul class="mb-0" style="padding-left: 20px; font-size: 14px;">
                        <li>Transfer Bank (BCA, Mandiri, BNI, BRI, Permata)</li>
                        <li>Virtual Account</li>
                        <li>E-Wallet (GoPay, ShopeePay, dll)</li>
                        <li>Kartu Kredit/Debit</li>
                        <li>Gerai Retail (Alfamart, Indomaret)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

<?php if ($snap_token): ?>
<!-- Midtrans Snap JS -->
<script src="<?php echo MIDTRANS_IS_PRODUCTION ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js'; ?>" 
        data-client-key="<?php echo MIDTRANS_CLIENT_KEY; ?>"></script>

<script type="text/javascript">
    // Tombol bayar
    document.getElementById('pay-button').addEventListener('click', function() {
        // Panggil Midtrans Snap
        snap.pay('<?php echo $snap_token; ?>', {
            // Callback ketika pembayaran berhasil
            onSuccess: function(result) {
                // Redirect ke halaman sukses
                window.location.href = 'proses_pembayaran.php?status=success&order_id=<?php echo $kode_pesanan; ?>';
            },
            // Callback ketika pembayaran pending
            onPending: function(result) {
                // Redirect ke halaman pending
                window.location.href = 'proses_pembayaran.php?status=pending&order_id=<?php echo $kode_pesanan; ?>';
            },
            // Callback ketika pembayaran error
            onError: function(result) {
                alert('Pembayaran gagal! Silakan coba lagi.');
            },
            // Callback ketika user menutup popup
            onClose: function() {
                // Tidak melakukan apa-apa, biarkan user di halaman ini
            }
        });
    });
</script>
<?php endif; ?>
