<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    
    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Email tidak valid!');</script>";
    } else {
        // Cek apakah email terdaftar
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($koneksi, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $token = bin2hex(random_bytes(32)); // Generate token reset
            $expFormat = mktime(
                date("H"), date("i"), date("s"), date("m") ,date("d")+1, date("Y")
            );
            $expDate = date("Y-m-d H:i:s", $expFormat);
            
            // Simpan token dan expiry di database
            $query = "UPDATE users SET reset_token = '$token', token_expiry = '$expDate' WHERE email = '$email'";
            if (mysqli_query($koneksi, $query)) {
                // Kirim email ke user
                $resetLink = "http://yourwebsite.com/reset_password.php?token=$token";
                $subject = "Reset Password";
                $message = "Klik link berikut untuk reset password: $resetLink";
                $headers = "From: no-reply@yourwebsite.com";

                // Menggunakan fungsi mail() untuk mengirim email
                if (mail($email, $subject, $message, $headers)) {
                    echo "<script>alert('Link reset password telah dikirim ke email Anda.');window.location='login.php';</script>";
                } else {
                    echo "<script>alert('Gagal mengirim email! Silakan coba lagi.');</script>";
                }
            } else {
                echo "<script>alert('Gagal menyimpan token! Silakan coba lagi.');</script>";
            }
        } else {
            echo "<script>alert('Email tidak terdaftar!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
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
                                        <h1 class="h4 text-gray-900 mb-4">Lupa Password Anda?</h1>
                                        <p class="mb-4">Kami paham, hal-hal terjadi. Masukkan email Anda di bawah ini dan kami akan mengirimkan tautan untuk mereset kata sandi Anda!</p>
                                    </div>
                                    <form class="user" method="post" action="">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user"
                                                id="email" name="email" aria-describedby="emailHelp"
                                                placeholder="Masukkan alamat email..." required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Kirim Tautan Reset Password
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
