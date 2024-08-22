<?php
session_start();
include 'conn/database.php';

// Ambil ID pengguna dari session
$user_id = $_SESSION['user_id'];

// Ambil data dari form
$nama_lengkap = $_POST['nama_lengkap'];
$nama_pengguna = $_POST['nama_pengguna'];
$bio = $_POST['bio'];
$sekolah = $_POST['sekolah'];
$lokasi = $_POST['lokasi'];

// Handle upload gambar jika ada
if (!empty($_FILES['profile_picture']['name'])) {
    $profile_picture = $_FILES['profile_picture']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($profile_picture);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Cek jika file gambar
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File bukan gambar.";
        $uploadOk = 0;
    }

    // Cek ukuran file
    if ($_FILES["profile_picture"]["size"] > 2000000) { // 2MB
        echo "File terlalu besar.";
        $uploadOk = 0;
    }

    // Cek format gambar
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
        $uploadOk = 0;
    }

    // Cek jika $uploadOk diset ke 0 oleh kesalahan
    if ($uploadOk == 0) {
        echo "Maaf, file tidak dapat diupload.";
    } else {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Update data pengguna di database termasuk nama file gambar
            $sql = "UPDATE user SET nama_lengkap = ?, nama_pengguna = ?, bio = ?, sekolah = ?, lokasi = ?, profile_picture = ? WHERE id = ?";
            $stmt = $koneksi->prepare($sql);
            $stmt->bind_param("ssssssi", $nama_lengkap, $nama_pengguna, $bio, $sekolah, $lokasi, $profile_picture, $user_id);

            if ($stmt->execute()) {
                header('Location: profil.php');
                exit();
            } else {
                echo 'Gagal memperbarui profil: ' . $stmt->error;
            }
        } else {
            echo "Terjadi kesalahan saat mengupload file.";
        }
    }
} else {
    // Update data pengguna tanpa mengubah gambar
    $sql = "UPDATE user SET nama_lengkap = ?, nama_pengguna = ?, bio = ?, sekolah = ?, lokasi = ? WHERE id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("sssssi", $nama_lengkap, $nama_pengguna, $bio, $sekolah, $lokasi, $user_id);

    if ($stmt->execute()) {
        header('Location: profil.php');
        exit();
    } else {
        echo 'Gagal memperbarui profil: ' . $stmt->error;
    }
}

$stmt->close();
$koneksi->close();
?>
