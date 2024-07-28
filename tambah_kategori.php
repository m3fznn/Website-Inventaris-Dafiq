<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kategori = $_POST['nama_kategori'];

    // Cek apakah kategori sudah ada
    $query_cek = "SELECT * FROM kategori WHERE nama_kategori = '$nama_kategori'";
    $result_cek = mysqli_query($koneksi, $query_cek);

    if (mysqli_num_rows($result_cek) > 0) {
        echo "<script>alert('Kategori sudah ada!'); window.location.href='tambah_barang.php';</script>";
    } else {
        $query = "INSERT INTO kategori (nama_kategori) VALUES ('$nama_kategori')";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Kategori berhasil ditambahkan!'); window.location.href='tambah_barang.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan kategori!'); window.location.href='tambah_barang.php';</script>";
        }
    }
}
?>
