<?php
session_start();
include 'conn/database.php';

// Ambil ID pengguna dari session
$user_id = $_SESSION['user_id'];

// Ambil daftar teman tanpa duplikasi
$sql = "SELECT u.* 
        FROM user u 
        JOIN friends f ON u.id = CASE 
                                   WHEN f.user_id = ? THEN f.friend_id 
                                   WHEN f.friend_id = ? THEN f.user_id 
                                 END
        WHERE ((f.user_id = ? AND f.status = 'accepted') 
            OR (f.friend_id = ? AND f.status = 'accepted'))
        AND u.id != ?";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param('iiiii', $user_id, $user_id, $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Daftar Teman</title>
</head>
<body class="pt-0 pb-2 pe-3 ps-3">
    <div class="container col-md-5 mt-4 mb-5">
        <div class="mb-5">
            <a href="profil.php" class="bi bi-arrow-left-circle-fill text-black fs-3 text-decoration-none">  Pertemanan Anda</a>
        </div>

        <div class="">
        <ul class="list-group">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <li class="list-group-item border-white mb-2 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <?php if (!empty($user['profile_picture'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Foto Profil" class="img-fluid rounded-circle border border-secondary me-3" style="width: 60px; height: 60px;">
                            <?php else: ?>
                                <img src="assets/default-profile.png" alt="Foto Profil" class="img-fluid rounded-circle mb-3" style="width: 60px; height: 60px;">
                            <?php endif; ?>
                            <div>
                                <h5 class="mb-0"><?php echo htmlspecialchars($user['nama_lengkap']); ?></h5>
                                <p class="mb-0">@<?php echo htmlspecialchars($user['nama_pengguna']); ?></p>
                            </div>
                        </div>
                        <br><br>
                        <div class="">
                        <div class="btn-group d-flex gap-2 justify-content-between">
                            <!-- Button ke profil teman -->
                            <a href="profil.php?id=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-outline-secondary btn-sm">Profil</a>
                            
                            <!-- Form untuk menghapus pertemanan -->
                            <form action="delete_friend.php" method="post" class="d-inline">
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                <input type="hidden" name="friend_id" value="<?php echo htmlspecialchars($user_id); ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                        </div>
                        
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <li class="list-group-item">Tidak ada teman yang ditemukan.</li>
            <?php endif; ?>
        </ul>

        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$koneksi->close();
?>
