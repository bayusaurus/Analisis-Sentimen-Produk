<?php 
/**
 * 
 */
class user
{

	function sanitize($_value, $_koneksi){
		return mysqli_real_escape_string($_koneksi, $_value);
	}

	function login($_email, $_pass, $_koneksi){
		$email=$this->sanitize($_email, $_koneksi);
		$pass=$this->sanitize($_pass, $_koneksi);
		$pass=md5($pass);
		$login=mysqli_query($_koneksi,"SELECT * FROM admin WHERE email='$email' and password='$pass'");
		$cek=mysqli_num_rows($login);
		if ($cek>0) {
			session_start();
			$data=mysqli_fetch_array($login);
			$_SESSION['id']=$data['id_admin'];
			$_SESSION['email']=$data['email'];
			$_SESSION['nama']=$data['nama'];
			$_SESSION['status']=$data['status'];
			if ($_SESSION['status']==1) {
				header("location:../sentimen_analisis/index.php?m=LS");
			}else{
				header("location:../login.php?m=AN");
			}
		}else{
			header("location:../login.php?m=LG");
		}
	}

	function logout(){
		session_start();
		session_destroy();
		header("location:../login.php");
	}

	function gantiPassword($_passwordLama, $_passwordBaru, $_passwordBaru2, $_email, $_koneksi){
		$passL=$this->sanitize($_passwordLama,$_koneksi);
		$passB=$this->sanitize($_passwordBaru, $_koneksi);
		$passB2=$this->sanitize($_passwordBaru2, $_koneksi);
		$email=$this->sanitize($_email, $_koneksi);
		$passDB=$this->getPassword($_passwordLama, $_email, $_koneksi);
		$passL=md5($_passwordLama);
		if ($passL == $passDB && $passB==$passB2) {
			$passB=md5($passB);
			mysqli_query($_koneksi,"UPDATE admin SET password='$passB' WHERE email='$_email'");
			header("location:../sentimen_analisis/admin.php?m=GPS");
		}else{
			header("location:../sentimen_analisis/admin.php?m=GPG");
		}
	}

	function gantiNama($email, $nama, $_koneksi){
		$email=$this->sanitize($email,$_koneksi);
		$nama=$this->sanitize($nama, $_koneksi);
		mysqli_query($_koneksi,"UPDATE admin SET nama='$nama' WHERE email='$email';");
		session_start();
		$_SESSION['nama']=$nama;
		header("location:../sentimen_analisis/admin.php?m=GNS");
	}

	function nonaktifAkun($id, $_koneksi){
		$id=$this->sanitize($id,$_koneksi);
		mysqli_query($_koneksi,"UPDATE admin SET status=0 WHERE id_admin=$id;");
		session_start();
		session_destroy();
		header("location:../login.php?m=NS");
	}

	function gantiEmail($email, $emailBaru, $_koneksi){
		$email=$this->sanitize($email,$_koneksi);
		$emailBaru=$this->sanitize($emailBaru, $_koneksi);
		mysqli_query($_koneksi,"UPDATE admin SET email='$emailBaru' WHERE email='$email';");
		session_start();
		$_SESSION['email']=$emailBaru;
		header("location:../sentimen_analisis/admin.php?m=GES");
	}

	function tambahAdmin($email, $nama, $_koneksi){
		$email=$this->sanitize($email,$_koneksi);
		$nama=$this->sanitize($nama, $_koneksi);
		$pass=md5('admin');
		mysqli_query($_koneksi,"INSERT INTO admin VALUES ('', '$nama', '$email', '$pass');");
		header('Location: ../sentimen_analisis/admin.php?m=TAS');
	}

	function lupaPassword($_email, $_koneksi, $_mail){
		// echo "registrasi berhasil";
		if (isset($_email, $_koneksi)) {;
		$email=$this->sanitize($_email, $_koneksi);
		}else{
		header('location:../index.php?m=LPG');
		}
		
		$cek=$this->cekEmail($email, $_koneksi);
		if ($cek==0) {
			header('location:../login.php?m=ETA');
		}else{
			$data=mysqli_query($_koneksi, "SELECT * FROM user where email='$email'");
			$data=mysqli_fetch_array($data);
			$pass=rand(100000,99999999);
			$pass2=md5($pass);
			$isi='Hai '. $data['nama'] .'<br> Password anda telah diperbarui dengan password yang baru <br> Gunakan Email dan Password dibawah ini untuk melakukan Login: <br> Email = '. $email.'<br> Password = '.$pass.' <br>Anda bisa mengganti password anda setelah anda login <br>Menuju website : localhost/sentiment_analyzer';
		// Load Composer's autoloader
			// require '../vendor/autoload.php';
		// Instantiation and passing `true` enables exceptions
			// $mail = new PHPMailer(true);
			$mail=$_mail;
			try {
			    //Server settings
			    $mail->SMTPDebug = 2;                                       // Enable verbose debug output
			    $mail->isSMTP();                                            // Set mailer to use SMTP
			    $mail->Host       = 'smtp.gmail.com';  // Specify main and backup SMTP servers
			    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
			    $mail->Username   = 'sentimentanalyzer18@gmail.com';                     // SMTP username
			    $mail->Password   = 'sentiment18analyzer';                               // SMTP password
			    $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
			    $mail->Port       = 587;                                    // TCP port to connect to
			    //Recipients
			    $mail->setFrom('sentimentanalyzer18@gmail.com', 'Sentiment Analyzer');
			    $mail->addAddress($email, $nama);     // Add a recipient
			    // $mail->addAddress('ellen@example.com');               // Name is optional
			    $mail->addReplyTo('sentimentanalyzer18@gmail.com', 'Sentiment Analyzer');
			    // $mail->addCC('cc@example.com');
			    // $mail->addBCC('bcc@example.com');
			    // Attachments
			    // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
			    // Content
			    $mail->isHTML(true);                                  // Set email format to HTML
			    $mail->Subject = 'Lupa Password Akun Sentiment Analyzer';
			    $mail->Body    = $isi;
			    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			    $mail->send();
			    mysqli_query($_koneksi, "UPDATE admin SET password='$pass2' where email='$email'");
			    header('location:../login.php?m=LPS');
			} catch (Exception $e) {
				header('location:../login.php?m=LPG');
			}
		}

	}

	function cekEmail($_email, $_koneksi){
		$email=$this->sanitize($_koneksi, $_email);
		$cekemail=mysqli_query($_koneksi, "SELECT email FROM admin WHERE email = '$_email'");
		return mysqli_num_rows($cekemail);
	}

	function getPassword($_pass, $_email, $_koneksi){
		$email=$this->sanitize($_email, $_koneksi);
		$pass=$this->sanitize($_pass, $_koneksi);
		$pass=md5($pass);
		$cekPassword=mysqli_query($_koneksi, "SELECT * FROM admin WHERE email = '$email'");
		$data=mysqli_fetch_array($cekPassword);
		return $data['password'];
	}
}
?>