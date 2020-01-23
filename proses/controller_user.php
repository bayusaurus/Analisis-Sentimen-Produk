<?php 

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	include '../config/koneksi.php';
	include '../kelas/user.php';

	if (isset($_POST['login'])) { //Login
		$login=new user();
		$login->login($_POST['email'], $_POST['password'], $koneksi);
	}elseif (isset($_POST['logout'])) { //Logout
		$logout=new user();
		$logout->logout();
	}elseif (isset($_POST['gantiPassword'])) { //Ganti Password
		$cekPassword= new user();
		$cekPassword->gantiPassword($_POST['passwordLama'],$_POST['passwordBaru'],$_POST['passwordBaru2'], $_POST['email'], $koneksi);	
	}elseif (isset($_POST['lupaPassword'])) { //Lupa Password
		require '../vendor/autoload.php';
		$mail = new PHPMailer(true);
		$lupaPassword=new user();
		$lupaPassword->lupaPassword($_POST['email'], $koneksi, $mail);
	}elseif (isset($_POST['gantiNama'])) {
		$gantiNama=new user();
		$gantiNama->gantiNama($_POST['email'], $_POST['nama'], $koneksi);
	}elseif (isset($_POST['gantiEmail'])) {
		$gantiNama=new user();
		$gantiNama->gantiEmail($_POST['email'], $_POST['emailBaru'], $koneksi);
	}elseif (isset($_POST['tambahAdmin'])) {
		$gantiNama=new user();
		$email=$_POST['email'];
		$query=mysqli_query($koneksi, "SELECT * FROM admin WHERE email='$email';");
		if (mysqli_num_rows($query)==0) {
			$gantiNama->tambahAdmin($_POST['email'], $_POST['nama'], $koneksi);
		}else{
			header('Location: ../sentimen_analisis/admin.php?m=TAG');
		}
	}elseif (isset($_POST['nonaktif'])) {
		$id=$_POST['id'];
		$query=mysqli_query($koneksi, "SELECT * FROM admin;");
		if (mysqli_num_rows($query)>1) {
			$nonaktif=new user();
			$nonaktif->nonaktifAkun($_POST['id'], $koneksi);
		}else{
			header('Location: ../sentimen_analisis/admin.php?m=NG');
		}
	}

?>