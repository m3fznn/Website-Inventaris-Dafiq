<?php
require_once 'config.php';
session_start(); // Inisialisasi sesi

// Cek jika form login di-submit
if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // Cek database untuk mendapatkan data user
    $query = "SELECT * FROM users WHERE email='$email'";
    $cekdatabase = mysqli_query($koneksi, $query);
    
    // Jika pengguna ditemukan
    if($hitung = mysqli_num_rows($cekdatabase) > 0){
        $user = mysqli_fetch_assoc($cekdatabase);

        // Verifikasi password
        if(password_verify($password, $user['password'])){
            $_SESSION['log'] = true;
            $_SESSION['user_id'] = $user['id']; // Menyimpan ID pengguna ke sesi
            $_SESSION['email'] = $user['email']; // Menyimpan email pengguna ke sesi
            $alert = "Login Berhasil Wak";
            header('location:index.php');
            exit(); // Pastikan script berhenti setelah redirect
        } else {
            $alert = "Login Gagal Wak T_T. Password salah.";
        }
    } else {
        $alert = "Login Gagal Wak T_T. Email tidak ditemukan.";
    }
}

// Cek apakah user sudah login
if(isset($_SESSION['log'])){
    header('location:index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dafiq</title>
    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .bg-login-image {
            background: url('assets/img/bg-login-image.svg');
            background-size: cover;
        }
    </style>
</head>
<body class="bg-gradient-primary">

<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Selamat Datang!</h1>
                                </div>
                                <!-- Tampilkan pesan kesalahan jika ada -->
                                <?php if(isset($alert)): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $alert; ?>
                                    </div>
                                <?php endif; ?>

                                <form method="post">
                                    <div class="form-floating mb-3">
                                        <label for="inputEmail">Email address</label>
                                        <input class="form-control" name="email" id="inputEmail" type="email" placeholder="name@example.com" required />
                                    </div>
                                    <div class="form-floating mb-3">
                                        <label for="inputPassword">Password</label>
                                        <input class="form-control" name="password" id="inputPassword" type="password" placeholder="Password" required />
                                    </div>
                                    
                                    <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <button class="btn btn-primary" name="login">Login</button>
                                            <a class="btn btn-secondary" href="register.php">Daftar</a>
                                    </div>
                                </form>
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="forgot_password.php">Lupa Password?</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Bootstrap core JavaScript-->
<script src="assets/vendor/jquery/jquery.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="assets/js/sb-admin-2.min.js"></script>

</body>
</html>
