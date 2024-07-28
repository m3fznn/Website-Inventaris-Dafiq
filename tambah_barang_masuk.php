<?php
include 'config.php';

// Memulai session
session_start();

// Memeriksa apakah pengguna telah login
if (!isset($_SESSION['log']) || $_SESSION['log'] !== true) {
    header('Location: login.php');
    exit();
}

// Dapatkan email pengguna dari sesi
$email_user = $_SESSION['email'];

// Ambil data pengguna berdasarkan email
$query_user = "SELECT * FROM users WHERE email='$email_user'";
$result_user = mysqli_query($koneksi, $query_user);
$user = mysqli_fetch_assoc($result_user);

// Ambil nama pengguna untuk ditampilkan di navbar
$username = $user['username'];

// Ambil data barang dari tabel barang untuk dropdown
$query_barang = "SELECT id, nama_barang FROM barang";
$result_barang = mysqli_query($koneksi, $query_barang);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $barang_id = $_POST['barang_id'];
    $stok_masuk = $_POST['stok'];
    $tanggal = $_POST['tanggal'];

    // Validasi input
    if (empty($barang_id) || empty($stok_masuk) || empty($tanggal)) {
        echo "<script>alert('Semua field harus diisi!');</script>";
    } else if ($stok_masuk <= 0) {
        echo "<script>alert('Stok harus lebih dari 0!');</script>";
    } else {
        // Mulai transaksi
        mysqli_begin_transaction($koneksi);

        try {
            // Tambahkan barang masuk ke tabel barang_masuk
            $query_masuk = "INSERT INTO barang_masuk (barang_id, stok, tanggal) VALUES ('$barang_id', '$stok_masuk', '$tanggal')";
            if (!mysqli_query($koneksi, $query_masuk)) {
                throw new Exception('Gagal menambahkan barang masuk!');
            }

            // Update stok barang di tabel barang
            $query_update_stok = "UPDATE barang SET stok = stok + '$stok_masuk' WHERE id = '$barang_id'";
            if (!mysqli_query($koneksi, $query_update_stok)) {
                throw new Exception('Gagal mengupdate stok barang!');
            }

            // Commit transaksi jika semua query berhasil
            mysqli_commit($koneksi);

            // Redirect dengan status sukses
            header('Location: tambah_barang_masuk.php?status=success');
            exit();
        } catch (Exception $e) {
            // Rollback transaksi jika ada query yang gagal
            mysqli_rollback($koneksi);

            echo "<script>alert('{$e->getMessage()}');window.location='tambah_barang_masuk.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang Masuk</title>
    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <img src="assets/img/logo.png" alt="" width='50px'>
                </div>
                <div class="sidebar-brand-text mx-3">Inventaris</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Nav Item - Barang Masuk -->
            <li class="nav-item active">
                <a class="nav-link" href="barang_masuk.php">
                    <i class="fas fa-fw fa-arrow-circle-down"></i>
                    <span>Barang Masuk</span>
                </a>
            </li>

            <!-- Nav Item - Barang Keluar -->
            <li class="nav-item">
                <a class="nav-link" href="barang_keluar.php">
                    <i class="fas fa-fw fa-arrow-circle-up"></i>
                    <span>Barang Keluar</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $username; ?></span>
                                <img class="img-profile rounded-circle"
                                    src="assets/img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Notifikasi Sukses -->
                    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Barang berhasil dimasukkan!
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Tambah Barang Masuk</h1>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Barang Masuk</h6>
                        </div>
                        <div class="card-body">
                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="barang_id">Nama Barang</label>
                                    <select name="barang_id" id="barang_id" class="form-control" required>
                                        <option value="">Pilih Barang</option>
                                        <?php while ($row = mysqli_fetch_assoc($result_barang)) : ?>
                                            <option value="<?= $row['id']; ?>"><?= $row['nama_barang']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="stok">Stok</label>
                                    <input type="number" name="stok" id="stok" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Tambah</button>
                                <a href="barang_masuk.php" class="btn btn-secondary">Batal</a>
                            </form>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; by mFz4nN</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="assets/js/sb-admin-2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Menghilangkan alert setelah 3 detik
            setTimeout(function() {
                $(".alert").alert('close');
            }, 3000);
        });
    </script>

</body>

</html>