<?php
session_start();
include 'conn/database.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ambil ID pengguna dari session
if (!isset($_SESSION['user_id'])) {
    die('User ID tidak ditemukan.');
}
$user_id = $_SESSION['user_id'];

// Cek apakah friend_id ada di POST
if (!isset($_POST['friend_id'])) {
    die('friend_id tidak ditemukan.');
}
$friend_id = $_POST['friend_id'];

// Pastikan ID teman bukan ID pengguna sendiri
if ($user_id == $friend_id) {
    die('Anda tidak dapat mengirim permintaan pertemanan ke diri sendiri.');
}

// Update status pertemanan menjadi 'accepted'
$sql_update = "UPDATE friends SET status = 'accepted' WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)";
$stmt_update = $koneksi->prepare($sql_update);
$stmt_update->bind_param('iiii', $user_id, $friend_id, $friend_id, $user_id);

if ($stmt_update->execute()) {
    echo 'Permintaan pertemanan diterima.';
} else {
    echo 'Gagal memperbarui status pertemanan: ' . $stmt_update->error;
}

$stmt_update->close();
$koneksi->close();
