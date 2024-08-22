<?php
session_start();
include 'conn/database.php';

// Ambil ID pengguna dari session
$user_id = $_SESSION['user_id'] ?? null;

// Hapus semua sesi dan redirect ke halaman login jika user menekan tombol logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}


$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Pengaturan Akun</title>
</head>
<body class="pt-0 pb-2 pe-3 ps-3">
    <div class="container col-md-5 mt-0 mb-5 mt-3">
        <div class="mb-5">
            <a href="profil.php" class="bi bi-arrow-left-circle-fill text-black fs-3 text-decoration-none">  Pengaturan Lanjutan</a>
        </div>

                <!-- pengaturan akun -->
                <div class=" p-4 rounded mb-3">
                <h4>Akun Anda</h4>
                <p></p>
                <div class="d-grid gap-2">
                    <a href="settings/ganti_akun.php" class=" btn btn-light text-start bi-person-fill text-black">     Pengaturan Akun</a>
                    <a href="settings/hapus_akun.php" class="btn btn-light text-start bi-x-circle-fill text-black">     Hapus Akun</a>
            </div>
            </div>

                <!-- kebijakan privasi -->
            <div class=" p-4 rounded mb-3">
                <h4>Bantuan</h4>
                <p></p>
                <div class="d-grid gap-2">
                    <a href='settings/kebijakan.php' class=" btn btn-light text-start bi-shield-fill text-black">     Kebijakan Privasi</a>
                    <a href="settings/bug_report.php"  class=" btn btn-light text-start bi-bug-fill text-black">     Laporkan Masalah</a>
                    <a href="settings/tentang_kami.php" class="btn btn-light text-start bi-person-badge-fill text-black">     Tentang Kami</a href="settings/tentang_kami.php">
                    
            </div>
            </div>

                <!-- session -->
                <div class=" p-4 rounded mb-3">
                <h4>Session</h4>
                <p></p>
                    <form action="" method="post">
                    <div class="d-grid gap-1">
                    <button type="submit" name="logout" id="logout"  class=" btn btn-light text-start bi-door-open-fill text-black">     Keluar</button>
                    </form>
                    </div>

            </div>



    </div>
</body>
</html>
