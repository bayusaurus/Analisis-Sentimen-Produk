<?php  
session_start();
session_destroy();
?>
<!doctype html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="vendor/bootstrap/css/stylex.css">
  <link rel="stylesheet" href="vendor/bootstrap/css/warna.css">
  <style type="text/css">
  .bg-landing{background: rgb(224,192,207); background: linear-gradient(55deg, rgba(224,192,207,1) 2%, rgba(198,241,217,1) 85%);}
  .bg-image{
    background-image: url('dark.jpg');
    background-size: cover;
    background-position: center;
  }
</style>
<link rel="icon" href="favicon.ico" type="image/x-icon">

<title>Sentiment Analyzer</title>
</head>
<body class="bg-landing">
  <div class="container mt-5 mb-5">
    <div class="row">
      <div class="col-md-1"></div>
      <div class="col-md-10">
        <div class="row">
          <div class="col-md-8 bg-image">
            <br><br><br><br><br>
            <h3 class="mt-5 text-center text-white">Selamat Datang di Sentiment Analyzer</h3>
            <br><br><br><br><br>
          </div>
          <div class="col-md-4 bg-white">
            <center><img src="logo.png" width="190" class="mt-3" alt="Logo"></center>                
            <form action="proses/controller_user.php" method="post" class="mt-3 mb-5 mr-2 ml-2">
              <?php if (isset($_GET['m'])) {
                if ($_GET['m']=='RS') { ?>
                  <div class="alert alert-success alert-dismissible fade show mb-2" style="padding-top: 15px;">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Pendaftaran Sukses!</strong> Silahkan cek email anda dan lakukan <kbd><a class="text-light" href="#" data-toggle="modal" data-target="#modalLogin">Login</a></kbd>.
                  </div>
                  <?php
                }elseif ($_GET['m']=='DG') { ?>
                  <div class="alert alert-danger alert-dismissible fade show mb-2" style="padding-top: 15px;">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Pendaftaran Gagal!</strong> Terjadi kesalahan saat dengan pendaftaran, terjadi masalah dengan jaringan internet.
                  </div>
                  <?php  
                }elseif ($_GET['m']=='NS') { ?>
                  <div class="alert alert-danger alert-dismissible fade show mb-2" style="padding-top: 15px;">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>AKUN ANDA TELAH DINONAKTIFKAN</strong>
                  </div>
                  <?php
                }elseif ($_GET['m']=='LG') { ?>
                  <div class="alert alert-danger alert-dismissible fade show mb-2" style="padding-top: 15px;">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Login Gagal!</strong> Email atau password anda salah.
                  </div>
                  <?php
                }elseif ($_GET['m']=='LPG') { ?>
                  <div class="alert alert-danger alert-dismissible fade show mb-2" style="padding-top: 15px;">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Ganti Password Gagal!</strong> Silakan Coba Lagi.
                  </div>
                  <?php
                }elseif ($_GET['m']=='ETA') { ?>
                  <div class="alert alert-danger alert-dismissible fade show mb-2" style="padding-top: 15px;">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Ganti Password Gagal!</strong> Email Tidak Terdaftar.
                  </div>
                  <?php
                }elseif ($_GET['m']=='AN') { ?>
                  <div class="alert alert-danger alert-dismissible fade show mb-2" style="padding-top: 15px;">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Akun Anda Telah Nonaktif!</strong> Tidak Bisa Login.
                  </div>
                  <?php
                }elseif ($_GET['m']=='LPS') { ?>
                  <div class="alert alert-success alert-dismissible fade show mb-2" style="padding-top: 15px;">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Password Baru Terkirim!</strong> Silahkan cek email anda dan lakukan login.
                  </div>
                  <?php
                }
              } ?>
              <div class="text-center">Silakan Log In Terlebih Dahulu.</div>
              <div class="form-label-group mt-3">
                <input class="form-control" type="email" name="email" placeholder="Email" id="email" required autofocus>
              </div>
              <div class="form-label-group mt-3">
                <input class="form-control" type="password" name="password" placeholder="Password" id="password" required>
              </div>
              <button class="btn btn-lg btn-primary btn-block btn-login text-uppercase font-weight-bold mt-3 mb-3" type="submit" name="login">Log In</button>
              <div class="text-center">
                <a href="#" data-toggle="modal" data-target="#modalLupaPassword">Lupa Password?</a>
              </div>
            </form>
          </div>
          
          <div class="col-md-12" style="background-color: #222224">
            <center><p class="mt-3 mb-3 text-white">By Bayusaurus</p></center>
          </div>
        </div>

      </div>
      <div class="col-md-2"></div>
    </div> 
  </div>

  <div class="modal" tabindex="-1" role="dialog" id="modalLupaPassword">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="col-12 modal-title text-center text-white">
            LUPA PASSWORD
            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </h5>
        </div>
        <div class="modal-body">
          <p>Masukkan email anda, kami akan mengirim password baru melalui email.</p>
          <form action="proses/controller_user.php" method="post">
            <label for="email">Masukkan Email Anda :</label>
            <input class="form-control" name="email" type="email" placeholder="Email" id="cekemail" required="">
            <div class="text-danger" id="warning"></div>
          </div>
          <div class="modal-footer">
            <button type="submit" name="lupaPassword" class="btn btn-primary" id="btnDaftar">Kirim Password</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- END MODAL -->

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <!--   <script src="vendor/bootstrap/js/jquery-3.2.1.slim.min.js"></script> -->
  <script src="vendor/bootstrap/js/jquery-3.2.1.js"></script>
  <script src="vendor/bootstrap/js/popper.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

  <!-- JavaScript -->
  <script type="text/javascript">
    $(document).ready(function(){
      $("#cekemail").keyup(function(){
        var email=$(this).val();
        var cekemail="cekemail";

        $.ajax({
          url: 'model/cekemail.php',
          method: "POST",
          data: {email: email,
            cekemail: cekemail},
            success: function(data){
            // $(".warning").html(response);
            // $("#btnDaftar").attr("disabled", true);
            if(data > '0') {
              $("#warning").html('<span class="text-danger">&bull;Email sudah terdaftar oleh pengguna lain</span>');
              $("#btnDaftar").attr("disabled", true);
            } else {
              $("#warning").html('<span class="text-danger"></span>');
              $("#btnDaftar").attr("disabled", false);
            }
          }
        });
      });
    });
  </script>

</body>
</html>