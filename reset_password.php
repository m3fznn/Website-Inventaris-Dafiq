<?php
include 'config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Cek apakah token valid dan belum kadaluarsa
    $query = "SELECT * FROM users WHERE reset_token = '$token' AND token_expiry > NOW()";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password = mysqli_real_escape_string($koneksi, $_POST['password']);
            $confirm_password = mysqli_real_escape_string($koneksi, $_POST['confirm_password']);

            // Validasi password
            if (empty($password) || empty($confirm_password)) {
                echo "<script>alert('Semua field harus diisi!');</script>";
            } else if ($password !== $confirm_password) {
                echo "<script>alert('Password tidak cocok!');</script>";
            } else {
                // Hash password baru
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Update password di database
                $update_query = "UPDATE users SET password = '$hashed_password', reset_token = NULL, token_expiry = NULL WHERE reset_token = '$token'";
                if (mysqli_query($koneksi, $update_query)) {
                    echo "<script>alert('Password berhasil direset! Silakan login.');window.location='login.php';</script>";
                } else {
                    echo "<script>alert('Gagal mereset password! Silakan coba lagi.');</script>";
                }
            }
        }
    } else {
        echo "<script>alert('Token tidak valid atau sudah kadaluarsa!');window.location='forgot_password.php';</script>";
    }
} else {
    header('Location: forgot_password.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">
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
                            <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Atur Ulang Password Anda</h1>
                                    </div>
                                    <form class="user" method="post" action="">
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user"
                                                id="password" name="password" placeholder="Password Baru" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user"
                                                id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Reset Password
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="register.php">Buat Akun!</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="login.php">Sudah Punya Akun? Login!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="assets/js/sb-admin-2.min.js"></script>

</body>
</html>
