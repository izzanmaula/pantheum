<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>Masuk</title>
</head>
<body class="pt-0 pb-2 pe-3 ps-3">
    <div class="text-start container col-md-5 mt-5" style="padding-bottom: 100px;">
        <img src="assets/logo.png" alt="" width="200" class="mb-1">
        <div">
          <div class="display-5">Sederhana, bersih <br> dan ringan. <br></div>
        </div>

    </div>
    <div class="container col-md-5 mt-5">
    <form action="conn/login.php" method="post">
      <div>
        <?php
        session_start();
        if (isset($_SESSION['success_message'])) {
          echo '<div class="alert alert-success" role="alert">'. $_SESSION['success_message'] .'</div>';
          unset($_SESSION['success_message']);
        }
        
        if (isset($_SESSION['error_message'])) {
          echo '<div class="alert alert-danger" role="start">'. $_SESSION['error_message'].'</div>';
          unset($_SESSION['error_message']);
        };
        ?>
      </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input type="email" class="form-control" name="email" id="email" aria-describedby="emailHelp">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" name="password" id="password">
          <div id="password" class="form-text">Perhatikan <strong>Caps Lock</strong> dalam memasukkan Password.</div>
        </div>
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Masuk</button>
            <a href="daftar.php" class="btn btn-outline-secondary">Daftar</a>
        </div>
      </form>
      </div>
</body>
</html>