<?php
session_start();
include 'conn/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'];
$action = $_POST['action'];

// Validasi
if (!filter_var($post_id, FILTER_VALIDATE_INT) || !in_array($action, ['like', 'unlike'])) {
    echo json_encode(['error' => 'Invalid input']);
    exit();
}

if ($action === 'like') {
    $sql = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";
} else {
    $sql = "DELETE FROM likes WHERE post_id = ? AND user_id = ?";
}

$stmt = $koneksi->prepare($sql);
if ($action === 'like') {
    $stmt->bind_param('ii', $post_id, $user_id);
} else {
    $stmt->bind_param('ii', $post_id, $user_id);
}
$stmt->execute();

// Ambil jumlah like terbaru dan apakah pengguna sudah menyukai
$sql = "SELECT COUNT(*) AS like_count FROM likes WHERE post_id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param('i', $post_id);
$stmt->execute();
$like_count_result = $stmt->get_result()->fetch_assoc();
$like_count = $like_count_result['like_count'];

$sql = "SELECT COUNT(*) AS user_liked FROM likes WHERE post_id = ? AND user_id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param('ii', $post_id, $user_id);
$stmt->execute();
$user_liked_result = $stmt->get_result()->fetch_assoc();
$user_liked = $user_liked_result['user_liked'] ? 1 : 0;

echo json_encode([
    'like_count' => $like_count,
    'user_liked' => $user_liked
]);
