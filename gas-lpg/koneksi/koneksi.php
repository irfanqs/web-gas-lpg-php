<?php
/**
 * =====================================================
 * File: koneksi.php
 * Konfigurasi koneksi database dan setting aplikasi
 * =====================================================
 */


// KONFIGURASI DATABASE
$db_host = 'localhost';      // Host database
$db_user = 'root';           // Username database
$db_pass = '';               // Password database (kosong jika tidak ada)
$db_name = 'gas_lpg';        // Nama database

// Membuat koneksi ke database
$koneksi = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi berhasil atau tidak
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8mb4");

// KONFIGURASI GOOGLE reCAPTCHA v2
define('RECAPTCHA_SITE_KEY', '6Le9sj8sAAAAAMICogcnfgtXROB0maPoVW5GWg_B');     // Site Key dari Google
define('RECAPTCHA_SECRET_KEY', '6Le9sj8sAAAAADbW2gslUCjhiSceWNJ4nBo8ylq0');  // Secret Key dari Google

// KONFIGURASI MIDTRANS
define('MIDTRANS_SERVER_KEY', 'Mid-server-pLV8_G1k2eZqDzxIcizb_I7S');   // Server Key dari Midtrans
define('MIDTRANS_CLIENT_KEY', 'Mid-client-OYecEO180SgpZf-8');           // Client Key dari Midtrans
define('MIDTRANS_IS_PRODUCTION', false);                                // false = Sandbox, true = Production
define('MIDTRANS_IS_SANITIZED', true);                                  // Sanitize input
define('MIDTRANS_IS_3DS', true);                                        // Enable 3D Secure

// KONFIGURASI APLIKASI
define('APP_NAME', 'Gas LPG Website');                        // Nama aplikasi
define('APP_URL', 'http://localhost:8000');                   // URL aplikasi
define('UPLOAD_PATH', __DIR__ . '/../uploads/');              // Path untuk upload file
define('PAYMENT_EXPIRY_MINUTES', 30);                         // Waktu expired pembayaran (menit)

// HELPER

/**
 * Generate kode pesanan unik
 * Format: LPG-YYYYMMDD-XXXXX (contoh: LPG-20240115-A1B2C)
 */
function generateKodePesanan() {
    $tanggal = date('Ymd');
    $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
    return "LPG-{$tanggal}-{$random}";
}

/**
 * Generate kode permintaan gas unik
 * Format: REQ-YYYYMMDD-XXXXX
 */
function generateKodePermintaan() {
    $tanggal = date('Ymd');
    $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
    return "REQ-{$tanggal}-{$random}";
}

/**
 * Format angka ke format Rupiah
 * @param float $angka - Angka yang akan diformat
 * @return string - Format: Rp 18.000
 */
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/**
 * Validasi reCAPTCHA v2
 * @param string $response - Response dari reCAPTCHA
 * @return bool - true jika valid, false jika tidak
 */
function validateRecaptcha($response) {
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => RECAPTCHA_SECRET_KEY,
        'response' => $response
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $resultJson = json_decode($result);
    
    return $resultJson->success;
}

/**
 * Escape string untuk mencegah SQL Injection
 * @param string $string - String yang akan di-escape
 * @return string - String yang sudah di-escape
 */
function escapeString($string) {
    global $koneksi;
    return mysqli_real_escape_string($koneksi, $string);
}

/**
 * Redirect ke halaman lain
 * @param string $url - URL tujuan
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Menampilkan alert JavaScript dan redirect
 * @param string $message - Pesan alert
 * @param string $redirectUrl - URL redirect (opsional)
 */
function alertRedirect($message, $redirectUrl = null) {
    echo "<script>alert('$message');";
    if ($redirectUrl) {
        echo "window.location='$redirectUrl';";
    }
    echo "</script>";
}
?>
