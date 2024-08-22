<?php
session_start();
include 'conn/database.php';

// Ambil ID pengguna dan ID teman dari form
$user_id = $_POST['user_id'];
$friend_id = $_POST['friend_id'];

// Hapus pertemanan dari tabel friends
$sql = "DELETE FROM friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param('iiii', $user_id, $friend_id, $friend_id, $user_id);
$stmt->execute();

// Redirect kembali ke halaman daftar teman
header("Location: friend_list.php");
exit();
?>
