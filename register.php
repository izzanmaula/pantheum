<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $nama_lengkap = $_POST['namaLengkap'];
    $nama_pengguna = $_POST['namaPengguna'];

    // Validasi password dan konfirmasi password
    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = 'Password dan konfirmasi password tidak sama.';
        header('Location: ../daftar.php'); // Redirect kembali ke halaman daftar
        exit();
    }

    // Koneksi ke database
    require 'database.php';

    // Periksa apakah email sudah terdaftar
    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email sudah terdaftar
        $_SESSION['error_message'] = 'Email sudah terdaftar, silakan gunakan email lain.';
        header('Location: ../daftar.php'); // Redirect kembali ke halaman daftar
        exit();
    }

    // Hash password sebelum disimpan
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Query untuk menyimpan data ke database
    $sql = "INSERT INTO user (email, nama_lengkap, nama_pengguna, password) VALUES (?, ?, ?, ?)";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ssss", $email, $nama_lengkap, $nama_pengguna, $hashed_password);

    if ($stmt->execute() === TRUE) {
        // Ambil ID dari pengguna baru
        $new_user_id = $koneksi->insert_id;

        // Ambil ID dari akun resmi
        $official_user_email = 'pantheum@gmail.com';
        $sql = "SELECT id FROM user WHERE email = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("s", $official_user_email);
        $stmt->execute();
        $result = $stmt->get_result();
        $official_user = $result->fetch_assoc();
        $official_user_id = $official_user['id'];

        // Tambahkan relasi teman jika belum ada
        // Periksa apakah relasi sudah ada untuk user_id dan friend_id
        $sql = "INSERT IGNORE INTO friends (user_id, friend_id, status) VALUES (?, ?, 'accepted')";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("ii", $new_user_id, $official_user_id);
        $stmt->execute();
        
        $stmt->bind_param("ii", $official_user_id, $new_user_id);
        $stmt->execute();

        $_SESSION['success_message'] = 'Pendaftaran berhasil. Silakan login.';
        header('Location: ../login.php'); // Redirect ke halaman login
        exit();
    } else {
        $_SESSION['error_message'] = 'Terjadi kesalahan: ' . $stmt->error;
        header('Location: ../daftar.php'); // Redirect kembali ke halaman daftar
        exit();
    }

    // Tutup koneksi
    $stmt->close();
    $koneksi->close();
} else {
    // Jika tidak ada POST request, redirect ke halaman daftar
    header('Location: ../daftar.php');
    exit();
}
?>
