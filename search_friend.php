<?php
session_start();
include 'conn/database.php';

// Ambil ID pengguna dari session
$user_id = $_SESSION['user_id'];

// Set variabel untuk hasil pencarian
$search_query = '';
$results = [];

// Ambil ID teman yang sudah dikirim
$sent_requests = isset($_SESSION['friend_request_sent']) ? $_SESSION['friend_request_sent'] : [];

// Periksa status persahabatan
$friends = [];
$sql = "SELECT friend_id FROM friends WHERE user_id = ? AND status = 'accepted'";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $friends[$row['friend_id']] = true;
}

if (isset($_GET['query'])) {
    // Ambil data pencarian dari form
    $search_query = $_GET['query'];

    // Validasi query pencarian
    if (!empty($search_query)) {
        // Cari pengguna berdasarkan query
        $sql = "SELECT id, nama_pengguna, profile_picture FROM user WHERE nama_pengguna LIKE ? AND id != ?";
        $stmt = $koneksi->prepare($sql);
        $search_query = '%' . $search_query . '%';
        $stmt->bind_param('si', $search_query, $user_id);
        $stmt->execute();
        $results = $stmt->get_result();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Cari Teman</title>
    <style>
        .profile-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        .navbar {
            position: fixed;
            bottom: 5px;
            width: 85%;
            text-align: center;
            z-index: 1000;
        }
    </style>
</head>
<body class="pt-0 pb-2 pe-3 ps-3 content">
    <div class="container col-md-5 mt-0 mb-5 pt-3">
        <div class="display-5 mb-3" >
                Pencarian
                </div>

        <!-- Form pencarian -->
        <form action="search_friend.php" method="get">
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">@</span>
                <input type="text" class="form-control" name="query" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Cari nama pengguna" required>
                <button class="btn btn-primary" type="submit">Cari</button>
            </div>
        </form>

        <?php if (!empty($search_query) && $results->num_rows > 0): ?>
            <ul class="list-group">
            <h2>Hasil Pencarian Teman</h2>
            <?php while ($user = $results->fetch_assoc()): ?>
                <li class="list-group-item d-flex align-items-center rounded">
                    <?php if (!empty($user['profile_picture'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Foto Profil" class="profile-img me-3">
                    <?php else: ?>
                        <img src="assets/default-profile.png" alt="Foto Profil" class="profile-img me-3">
                    <?php endif; ?>
                    <div class="pt-2">
                        @<?php echo htmlspecialchars($user['nama_pengguna']); ?>
                        <br>
                        <div class="mt-2 ">
                            <input type="hidden" name="friend_id" value="<?php echo $user['id']; ?>">
                            <a href="profile_friends.php?id=<?php echo $user['id']; ?>" class="btn btn-secondary btn-sm">Lihat Profil</a>
                            <?php
                            // Cek status persahabatan
                            $is_friend = isset($friends[$user['id']]);
                            ?>
                            <button class="btn <?php echo $is_friend ? 'btn-danger' : 'btn-primary'; ?> btn-sm" id="add-friend-<?php echo $user['id']; ?>" onclick="<?php echo $is_friend ? 'removeFriend(' . $user['id'] . ');' : 'sendFriendRequest(' . $user['id'] . ');'; ?>">
                                <?php echo $is_friend ? 'Hapus Teman' : 'Tambah Teman'; ?>
                            </button>
                            <span id="status-<?php echo $user['id']; ?>" class="ms-2"></span>
                        </div>
                    </div>
                </li>
            <?php endwhile; ?>
            </ul>
        <?php elseif (!empty($search_query)): ?>
            <p>Tidak ada hasil ditemukan.</p>
        <?php endif; ?>

        <!-- footer -->
        <div class="navbar d-flex justify-content-between pt-3 pb-3 pe-5 ps-5 mt-4 bg-secondary rounded-5 sticky-bottom">
                <div>
                    <a href="beranda.php" class="fill"><img src="assets/home.png" alt=""></a> 
                </div>
                <div>
                    <a href="post.php" class=""><img src="assets/plus.png" alt=""></a>
                </div>
                <div>
                    <a href="search_friend.php" class=""><img src="assets/search_fill.png" alt=""></a> 
                </div>
                <div class='text-center'>
                    <a href="profil.php?id=<?php echo $_SESSION['user_id']; ?>" class=""><img src="assets/profile.png" alt=""></a> 
                </div>
        </div>
    </div>

    <script>
function sendFriendRequest(friendId) {
    var button = document.getElementById('add-friend-' + friendId);
    var status = document.getElementById('status-' + friendId);

    button.textContent = 'Permintaan Pertemanan Terkirim';
    button.classList.remove('btn-primary');
    button.classList.add('btn-secondary');
    button.disabled = true;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'friend_request.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                status.textContent = 'Permintaan pertemanan terkirim!';
            } else {
                button.textContent = 'Gagal Kirim';
                button.disabled = false;
                button.classList.remove('btn-secondary');
                button.classList.add('btn-primary');
            }
        }
    };
    xhr.send('friend_id=' + friendId);
}

function removeFriend(friendId) {
    var button = document.getElementById('add-friend-' + friendId);
    var status = document.getElementById('status-' + friendId);

    button.textContent = 'Menghapus Teman...';
    button.classList.remove('btn-danger');
    button.classList.add('btn-secondary');
    button.disabled = true;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'delete_friend.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                button.textContent = 'Teman Dihapus';
                button.classList.remove('btn-secondary');
                button.classList.add('btn-primary');
                button.onclick = function() {
                    sendFriendRequest(friendId);
                };
            } else {
                button.textContent = 'Gagal Hapus';
                button.disabled = false;
                button.classList.remove('btn-secondary');
                button.classList.add('btn-danger');
            }
        }
    };
    xhr.send('friend_id=' + friendId);
}
</script>

</body>
</html>
