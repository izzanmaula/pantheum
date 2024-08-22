<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>Daftar</title>
</head>
<body class="pt-0 pb-2 pe-3 ps-3">
    <div class="text-start container col-md-5 mt-5">
        <img src="assets/logo.png" alt="" width="200" class="mb-1">
        <div class="display-6">Daftar untuk dapat terkoneksi dalam dunia internet</div>
    </div>
    <div class="container col-md-5 mt-5 mb-5">
    <form action="conn/register.php" method="post" id="daftarForm">
    <div>
      <!-- konfirmasi error -->
    <div id="error_message_container">
        <?php
        session_start();
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>
    </div>    
    <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp">
        </div>
        <div class="mb-3">
            <label for="namaLengkap" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="namaLengkap" name="namaLengkap" aria-describedby="emailHelp">
          </div>
          <div class="mb-3 input-group">
            <label for="namaPengguna" class="form-label">Nama Pengguna</label><br>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">@</span>
                <input type="text" class="form-control" id="namaPengguna" name="namaPengguna" aria-label="namaPengguna" aria-describedby="basic-addon1">    
            </div>
            <div class="form-text" id="basic-addon1">Orang lain akan melihat nama ini</div>
          </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" name="password" id="password">
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Konfirmasi Password</label>
            <input type="password" class="form-control" name="confirm_password" id="confirm_password">
            <div id="confirm_password" class="form-text">Pastikan password Anda telah sesuai di atas</div>
          </div> 
        <div class="d-grid gap-2 mt-5 mb-5">
            <button type="submit" class="btn btn-primary">Daftar</button>
            <a href="login.php" class="btn btn-outline-secondary">Kembali login</a>
        </div>
      </form>
      </div>
      <script>
        document.getElementById('daftarForm').addEventListener('submit', function (e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value; // perbarui id ini
            const errorMessageContainer = document.getElementById('error_message_container');
            
            // Reset pesan error
            errorMessageContainer.innerHTML = '';

            if (password !== confirmPassword) {
                e.preventDefault(); // Cegah form submit
                const errorMessage = '<div class="alert alert-danger" role="alert">Password dan konfirmasi password tidak sama.</div>';
                errorMessageContainer.innerHTML = errorMessage;
            }
        });
    </script>
    
  </body>
</html>