<?php
include 'config.php';

$id = $_GET['id'];

$query = "DELETE FROM barang_masuk WHERE id = $id";
if (mysqli_query($koneksi, $query)) {
    echo "<script>alert('Barang masuk berhasil dihapus!');window.location='barang_masuk.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus barang masuk!');window.location='barang_masuk.php';</script>";
}
?>
