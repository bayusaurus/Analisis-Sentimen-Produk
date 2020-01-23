<?php  
	include '../config/koneksi.php';
	if (isset($_POST['sesuai']) || isset($_POST['tidak'])) {
		if (isset($_POST['sesuai'])) {
			$kesesuaian=1;
		}elseif (isset($_POST['tidak'])) {
			$kesesuaian=0;
		}
		if ($_POST['hasil']=='Positif') {
			$sentimen=1;
		}elseif ($_POST['hasil']=='Negatif') {
			$sentimen=0;
		}
		$timestamp = date("Y-m-d H:i:s");
		$komentar=addslashes($_POST['komentar']);
		mysqli_query($koneksi, "INSERT INTO hasil_uji_pengguna VALUES ('','$komentar', $sentimen, $kesesuaian, '$timestamp');");
		header('Location: ../index.php?m=ty');
	}
?>