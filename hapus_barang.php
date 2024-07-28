<?php
include 'config.php';

// Mengambil ID barang dari parameter GET
$id = $_GET['id'];

// Query untuk menghapus barang berdasarkan ID
$query = "DELETE FROM barang WHERE id = $id";
if (mysqli_query($koneksi, $query)) {
    // Jika penghapusan berhasil, arahkan kembali ke index dengan pesan sukses
    echo "<script>alert('Barang berhasil dihapus!'); window.location='index.php';</script>";
} else {
    // Jika penghapusan gagal, tampilkan pesan error
    echo "<script>alert('Gagal menghapus barang!'); window.location='index.php';</script>";
}
?>
