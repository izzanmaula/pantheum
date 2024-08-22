<?php
session_start();

// Cek apakah form login disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Koneksi ke database
    require 'database.php';

    // Query untuk mengambil data pengguna berdasarkan email
    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Password benar, set session dan redirect ke halaman home
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            header('Location: ../beranda.php');
            exit();
        } else {
            // Password salah
            $_SESSION['error_message'] = 'Email atau password salah.';
            header('Location: ../login.php');
            exit();
        }
    } else {
        // Email tidak ditemukan
        $_SESSION['error_message'] = 'Email atau password salah.';
        header('Location: ../login.php');
        exit();
    }

    // Tutup koneksi
    $stmt->close();
    $koneksi->close();
} else {
    // Jika tidak ada POST request, redirect ke halaman login
    header('Location: ../login.php');
    exit();
}
?>
