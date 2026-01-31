<?php
/**
 * =====================================================
 * File: logout.php
 * Proses logout user - menghapus semua session
 * =====================================================
 */

// Mulai session
session_start();

// Hapus semua data session
session_unset();

// Hancurkan session
session_destroy();

// Redirect ke halaman login
header('location:login.php');
exit();
?>
