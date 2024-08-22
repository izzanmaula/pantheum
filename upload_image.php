<?php
session_start();
include 'conn/database.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo 'Anda belum login. Silakan login terlebih dahulu.';
    exit();
}

$user_id = $_SESSION['user_id'];

// Cek apakah file diunggah
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['profile_picture']['tmp_name'];
    $file_ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);

    // Buat nama file unik berdasarkan ID pengguna dan timestamp
    $file_name = $user_id . '_' . time() . '.' . $file_ext;
    $upload_dir = 'uploads/';
    $file_path = $upload_dir . $file_name;

    // Pindahkan file ke folder tujuan
    if (move_uploaded_file($file_tmp, $file_path)) {
        // Crop gambar
        $image_info = getimagesize($file_path);
        $width = $image_info[0];
        $height = $image_info[1];

        // Tentukan ukuran crop
        $new_width = 100;
        $new_height = 100;

        // Load gambar sesuai format
        switch ($image_info['mime']) {
            case 'image/jpeg':
                $src_image = imagecreatefromjpeg($file_path);
                break;
            case 'image/png':
                $src_image = imagecreatefrompng($file_path);
                break;
            case 'image/gif':
                $src_image = imagecreatefromgif($file_path);
                break;
            default:
                echo "Format gambar tidak didukung.";
                exit();
        }

        // Buat gambar kosong dengan ukuran baru
        $dst_image = imagecreatetruecolor($new_width, $new_height);

        // Crop gambar (mengambil bagian tengah)
        $src_x = ($width - $new_width) / 2;
        $src_y = ($height - $new_height) / 2;
        imagecopyresampled($dst_image, $src_image, 0, 0, $src_x, $src_y, $new_width, $new_height, $new_width, $new_height);

        // Simpan gambar yang sudah di-crop
        switch ($image_info['mime']) {
            case 'image/jpeg':
                imagejpeg($dst_image, $file_path);
                break;
            case 'image/png':
                imagepng($dst_image, $file_path);
                break;
            case 'image/gif':
                imagegif($dst_image, $file_path);
                break;
        }

        // Hapus gambar dari memori
        imagedestroy($src_image);
        imagedestroy($dst_image);

        // Perbarui path foto profil di database
        $sql = "UPDATE user SET profile_picture = ? WHERE id = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param('si', $file_name, $user_id);
        if ($stmt->execute()) {
            echo 'Gambar berhasil diunggah';
        } else {
            echo 'Gagal memperbarui foto profil.';
        }
        $stmt->close();
    } else {
        echo 'Gagal mengunggah file.';
    }
} else {
    echo 'Tidak ada file yang diunggah atau terjadi kesalahan.';
}

$koneksi->close();
?>
