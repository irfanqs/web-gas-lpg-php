<?php
/**
 * =====================================================
 * File: midtrans_notification.php
 * Webhook untuk menerima notifikasi dari Midtrans
 * URL ini harus didaftarkan di Midtrans Dashboard
 * Settings > Configuration > Payment Notification URL
 * =====================================================
 */

// Include koneksi
require 'koneksi/koneksi.php';

// Ambil notifikasi dari Midtrans
$json = file_get_contents('php://input');
$notification = json_decode($json, true);

// Log notifikasi untuk debugging (opsional)
file_put_contents('midtrans_log.txt', date('Y-m-d H:i:s') . ' - ' . $json . "\n", FILE_APPEND);

// Validasi notifikasi
if (!$notification) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid notification']);
    exit();
}

// Ambil data dari notifikasi
$order_id = $notification['order_id'] ?? '';
$transaction_status = $notification['transaction_status'] ?? '';
$fraud_status = $notification['fraud_status'] ?? '';
$payment_type = $notification['payment_type'] ?? '';
$transaction_id = $notification['transaction_id'] ?? '';

// Verifikasi signature (opsional tapi recommended)
$signature_key = $notification['signature_key'] ?? '';
$expected_signature = hash('sha512', 
    $order_id . 
    $notification['status_code'] . 
    $notification['gross_amount'] . 
    MIDTRANS_SERVER_KEY
);

if ($signature_key !== $expected_signature) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Invalid signature']);
    exit();
}

// Cari pesanan berdasarkan order_id (kode_pesanan)
$order_id_escaped = mysqli_real_escape_string($koneksi, $order_id);
$query = mysqli_query($koneksi, "SELECT * FROM tb_pesanan WHERE kode_pesanan = '$order_id_escaped'");

if (mysqli_num_rows($query) == 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Order not found']);
    exit();
}

$pesanan = mysqli_fetch_assoc($query);
$id_pesanan = $pesanan['id_pesanan'];

// Update status berdasarkan transaction_status
$new_status = '';
$payment_status = '';

if ($transaction_status == 'capture') {
    // Untuk kartu kredit
    if ($fraud_status == 'accept') {
        $new_status = 'paid';
        $payment_status = 'success';
    } elseif ($fraud_status == 'challenge') {
        $new_status = 'pending';
        $payment_status = 'pending';
    }
} elseif ($transaction_status == 'settlement') {
    // Pembayaran berhasil
    $new_status = 'paid';
    $payment_status = 'success';
} elseif ($transaction_status == 'pending') {
    // Menunggu pembayaran
    $new_status = 'pending';
    $payment_status = 'pending';
} elseif ($transaction_status == 'deny' || $transaction_status == 'cancel' || $transaction_status == 'expire') {
    // Pembayaran gagal/dibatalkan/expired
    $new_status = 'cancelled';
    $payment_status = 'failed';
    
    if ($transaction_status == 'expire') {
        $new_status = 'expired';
        $payment_status = 'expired';
    }
}

// Update database jika ada perubahan status
if ($new_status && $pesanan['status'] != $new_status) {
    // Update status pesanan
    $waktu_bayar = ($new_status == 'paid') ? ", waktu_bayar = NOW()" : "";
    mysqli_query($koneksi, "UPDATE tb_pesanan SET status = '$new_status' $waktu_bayar WHERE id_pesanan = $id_pesanan");
    
    // Update status pembayaran
    $payment_type_escaped = mysqli_real_escape_string($koneksi, $payment_type);
    $transaction_id_escaped = mysqli_real_escape_string($koneksi, $transaction_id);
    
    mysqli_query($koneksi, "
        UPDATE tb_pembayaran 
        SET status_pembayaran = '$payment_status', 
            metode_pembayaran = '$payment_type_escaped',
            midtrans_transaction_id = '$transaction_id_escaped',
            waktu_pembayaran = NOW()
        WHERE id_pesanan = $id_pesanan
    ");
    
    // Kirim notifikasi ke admin jika pembayaran berhasil
    if ($new_status == 'paid') {
        $pesan_notif = "Pembayaran untuk pesanan {$pesanan['kode_pesanan']} berhasil via $payment_type.";
        
        $query_admin = mysqli_query($koneksi, "SELECT id_user FROM user WHERE role = 'Admin'");
        while ($admin = mysqli_fetch_assoc($query_admin)) {
            mysqli_query($koneksi, "
                INSERT INTO tb_notifikasi (id_user, id_pesanan, judul, pesan, tipe) 
                VALUES ({$admin['id_user']}, $id_pesanan, 'Pembayaran Berhasil', '$pesan_notif', 'pembayaran')
            ");
        }
    }
}

// Response sukses ke Midtrans
http_response_code(200);
echo json_encode(['status' => 'success']);
?>
