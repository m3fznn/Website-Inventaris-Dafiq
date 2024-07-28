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
$query_barang = "SELECT id, nama_barang, stok FROM barang";
$result_barang = mysqli_query($koneksi, $query_barang);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $barang_id = $_POST['barang_id'];
    $stok_keluar = $_POST['stok'];
    $tanggal = $_POST['tanggal'];
    $kondisi = $_POST['kondisi'];

    // Cek stok barang sebelum mengurangi
    $query_cek_stok = "SELECT stok FROM barang WHERE id = ?";
    $stmt_cek_stok = mysqli_prepare($koneksi, $query_cek_stok);
    mysqli_stmt_bind_param($stmt_cek_stok, 'i', $barang_id);
    mysqli_stmt_execute($stmt_cek_stok);
    mysqli_stmt_bind_result($stmt_cek_stok, $stok_tersedia);
    mysqli_stmt_fetch($stmt_cek_stok);
    mysqli_stmt_close($stmt_cek_stok);

    if ($stok_keluar > $stok_tersedia) {
        echo "<script>alert('Stok tidak cukup! Stok tersedia: $stok_tersedia');window.location='tambah_barang_keluar.php';</script>";
    } else {
        // Mulai transaksi
        mysqli_begin_transaction($koneksi);

        try {
            // Tambahkan barang keluar ke tabel barang_keluar
            $query_keluar = "INSERT INTO barang_keluar (barang_id, stok, tanggal, kondisi) VALUES (?, ?, ?, ?)";
            $stmt_keluar = mysqli_prepare($koneksi, $query_keluar);
            mysqli_stmt_bind_param($stmt_keluar, 'iiss', $barang_id, $stok_keluar, $tanggal, $kondisi);

            if (!mysqli_stmt_execute($stmt_keluar)) {
                throw new Exception('Gagal menambahkan barang keluar!');
            }
            mysqli_stmt_close($stmt_keluar);

            // Update stok barang di tabel barang
            $query_update_stok = "UPDATE barang SET stok = stok - ? WHERE id = ?";
            $stmt_update_stok = mysqli_prepare($koneksi, $query_update_stok);
            mysqli_stmt_bind_param($stmt_update_stok, 'ii', $stok_keluar, $barang_id);

            if (!mysqli_stmt_execute($stmt_update_stok)) {
                throw new Exception('Gagal mengupdate stok barang!');
            }
            mysqli_stmt_close($stmt_update_stok);

            // Commit transaksi jika semua query berhasil
            mysqli_commit($koneksi);

            echo "<script>alert('Barang keluar berhasil ditambahkan dan stok terupdate!');window.location='barang_keluar.php';</script>";
        } catch (Exception $e) {
            // Rollback transaksi jika ada query yang gagal
            mysqli_rollback($koneksi);

            echo "<script>alert('{$e->getMessage()}');window.location='tambah_barang_keluar.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang Keluar</title>
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
            <li class="nav-item">
                <a class="nav-link" href="barang_masuk.php">
                    <i class="fas fa-fw fa-arrow-circle-down"></i>
                    <span>Barang Masuk</span>
                </a>
            </li>

            <!-- Nav Item - Barang Keluar -->
            <li class="nav-item active">
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

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Tambah Barang Keluar</h1>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Barang Keluar</h6>
                        </div>
                        <div class="card-body">
                            <form action="" method="post" id="barangKeluarForm">
                                <div class="form-group">
                                    <label for="barang_id">Nama Barang</label>
                                    <select name="barang_id" id="barang_id" class="form-control" required>
                                        <option value="">Pilih Barang</option>
                                        <?php while ($row = mysqli_fetch_assoc($result_barang)) : ?>
                                            <option value="<?= $row['id']; ?>"><?= $row['nama_barang']; ?> - Stok: <?= $row['stok']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="stok">Stok</label>
                                    <input type="number" name="stok" id="stok" class="form-control" min="1" required>
                                </div>
                                <div class="form-group">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="kondisi">Kondisi</label>
                                    <select name="kondisi" id="kondisi" class="form-control" required>
                                        <option value="baik">Baik</option>
                                        <option value="rusak">Rusak</option>
                                        <option value="digunakan">Digunakan</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Tambah</button>
                                <a href="barang_keluar.php" class="btn btn-secondary">Batal</a>
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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('barangKeluarForm').addEventListener('submit', function (event) {
            const stokInput = document.getElementById('stok');
            const stokValue = parseInt(stokInput.value);
            const selectedOption = document.getElementById('barang_id').selectedOptions[0];
            const stokTersedia = parseInt(selectedOption.text.split('Stok: ')[1]);

            if (stokValue > stokTersedia) {
                event.preventDefault(); // Mencegah form terkirim
                Swal.fire({
                    icon: 'error',
                    title: 'Stok tidak cukup',
                    text: `Stok tersedia hanya ${stokTersedia} barang.`,
                });
            }
        });
    </script>

</body>

</html>
