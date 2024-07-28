<?php
require_once 'config.php';

// session_start(); // Inisialisasi sesi

// Cek jika form registrasi di-submit
if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']); // Tambahkan username
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Validasi input
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) { // Validasi username
        $alert = "Semua field harus diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $alert = "Format email tidak valid.";
    } elseif ($password !== $confirmPassword) {
        $alert = "Password dan konfirmasi password tidak cocok.";
    } else {
        // Cek apakah email sudah terdaftar
        $query = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($koneksi, $query);

        if (mysqli_num_rows($result) > 0) {
            $alert = "Email sudah terdaftar.";
        } else {
            // Cek apakah username sudah terdaftar
            $query_username = "SELECT * FROM users WHERE username='$username'";
            $result_username = mysqli_query($koneksi, $query_username);

            if (mysqli_num_rows($result_username) > 0) {
                $alert = "Username sudah terdaftar.";
            } else {
                // Hash password
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                // Simpan data pengguna ke database
                $insertQuery = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password_hash')";
                $insertResult = mysqli_query($koneksi, $insertQuery);

                if ($insertResult) {
                    $_SESSION['log'] = true;
                    $_SESSION['email'] = $email;
                    $_SESSION['user_id'] = mysqli_insert_id($koneksi); // Menyimpan ID pengguna ke sesi
                    header('location:index.php');
                    exit();
                } else {
                    $alert = "Registrasi gagal. Silakan coba lagi.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Dafiq</title>
    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .bg-register-image {
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
                        <div class="col-lg-6 d-none d-lg-block bg-register-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Buat Akun Baru!</h1>
                                </div>

                                <!-- Tampilkan pesan kesalahan jika ada -->
                                <?php if (isset($alert)): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $alert; ?>
                                    </div>
                                <?php endif; ?>

                                <form method="post">
                                    <div class="form-floating mb-3">
                                        <label for="inputUsername">Username</label> <!-- Tambahkan input username -->
                                        <input class="form-control" name="username" id="inputUsername" type="text" placeholder="Username" required />
                                    </div>
                                    <div class="form-floating mb-3">
                                        <label for="inputEmail">Email address</label>
                                        <input class="form-control" name="email" id="inputEmail" type="email" placeholder="name@example.com" required />
                                    </div>
                                    <div class="form-floating mb-3">
                                        <label for="inputPassword">Password</label>
                                        <input class="form-control" name="password" id="inputPassword" type="password" placeholder="Password" required />
                                    </div>
                                    <div class="form-floating mb-3">
                                        <label for="confirmPassword">Konfirmasi Password</label>
                                        <input class="form-control" name="confirmPassword" id="confirmPassword" type="password" placeholder="Konfirmasi Password" required />
                                    </div>
                                    
                                    <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                        <center>
                                            <button class="btn btn-primary" name="register">Register</button>
                                        </center>
                                    </div>
                                </form>
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="login.php">Sudah punya akun? Login!</a>
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
