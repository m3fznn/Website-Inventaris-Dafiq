<?php
include 'config.php';

$id = $_GET['id'];

$query = "DELETE FROM barang_keluar WHERE id = $id";
if (mysqli_query($koneksi, $query)) {
    echo "<script>alert('Barang keluar berhasil dihapus!');window.location='barang_keluar.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus barang keluar!');window.location='barang_keluar.php';</script>";
}
?>