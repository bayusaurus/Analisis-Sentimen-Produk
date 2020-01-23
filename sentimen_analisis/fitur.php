<?php 
session_start();
if ($_SESSION['id'] =='' || $_SESSION['nama']=='' || $_SESSION['email']=='') {
  session_destroy();
  header('location:../index.php');
}
?>
<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="../vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../vendor/bootstrap/css/stylex.css">
  <link rel="stylesheet" href="../vendor/bootstrap/datatables/css/dataTables.bootstrap.css">
  <link rel="stylesheet" href="../vendor/bootstrap/datatables/css/jquery.dataTables.css">
  <style type="text/css">
  .bg-gradasi{background: rgb(224,192,207); background: linear-gradient(55deg, rgba(224,192,207,1) 2%, rgba(198,241,217,1) 85%);}
  .backtop {
    display: none;
    position: fixed;
    bottom: 20px;
    right: 30px;
    z-index: 99;
    font-size: 18px;
    border: none;
    outline: none;
    color: white;
    cursor: pointer;
    padding: 15px;
    border-radius: 4px;
  }

  .backtop:hover {
    background-color: #555;
  }
  </style>
  <link rel="icon" href="../favicon.ico" type="image/x-icon">


  <!-- TITLE -->
  <title>FITUR | Sentiment Analyzer</title>
</head>

<body class="bg-gradasi">
  <button class="backtop bg-success" id="backtop" onclick="topFunction()" title="Go to top">&#8679; Kembali Ke Atas</button>
  <!-- LOADER -->
  <?php include 'loader.php'; ?>
  <!-- LOADER -->
 
  <!-- NAVBAR -->
  <?php include 'navbar.php'; ?>
  <!-- NAVBAR -->

  <!-- CONTENT -->
  <div class="container-fluid mt-5">
  <!-- ALERT -->
  <!-- END ALERT -->

  <!-- INI BAGIAN DAFTAR DATA LATIH -->
    <div class="card mb-5">
      <div class="card-header text-center bg-info text-white">
        FITUR YANG DIDAPAT DARI DATA LATIH
      </div>
      <div class="card-body">

        <?php //===============================CEK APAKAH ADA DATA LATIH==========================
        include '../config/koneksi.php';
        include '../kelas/naive_bayes.php';
        include '../kelas/tfidf.php';
        $mnb = new naive_bayes();
        $tfidf = new tfidf();
        $query=mysqli_query($koneksi, "SELECT * FROM komentar_latih where sentimen=1");
        $p=mysqli_num_rows($query);
        $query=mysqli_query($koneksi, "SELECT * FROM komentar_latih where sentimen=0");
        $n=mysqli_num_rows($query);
        if ($p>0 && $n>0) { //============================KALAU ADA TAMPILKAN TABEL=========== ?> 
        
        <div class="row">
          <div class="col-md-3"></div>
          <div class="col-md-6 mb-3">
            <div class="card">
              <div class="card-header text-white text-center bg-info">
                KATA UNIK DAN NILAI IDF
              </div>
              <div class="card-body">
                <table class="table table-striped table-bordered data">
                  <caption>Kata Unik dan Bobotnya</caption>
                  <thead>
                    <tr>
                      <th scope="col">No</th>
                      <th scope="col">Kata</th>
                      <th scope="col">Bobot</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $idf = array();
                      $idf = $tfidf->idf($idf, $koneksi);
                      $no=0;
                      foreach ($idf as $key => $value) { 
                    ?>
                      <tr>
                        <th scope="row"><?= $no+1; ?></th>
                        <td><?= $key; ?></td>
                        <td><?= $value; ?></td>
                      </tr>
                    <?php $no++;
                      }  
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="col-md-3"></div>

          <div class="col-md-6">
            <div class="card">
              <div class="card-header text-center bg-success text-white">
                KATA POSITIF, KEMUNCULAN DAN BOBOT TF IDF
              </div>
              <div class="card-body">
                <table class="table table-striped table-bordered data">
                  <caption>Kata Unik dan Bobotnya</caption>
                  <thead>
                    <tr>
                      <th scope="col">No</th>
                      <th scope="col">Kata</th>
                      <th scope="col">Kemunculan</th>
                      <th scope="col">TF-IDF</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $BoW_Positif = array();
                      $BoW_Positif = $mnb->BagOfWords(1, $BoW_Positif, $koneksi);
                      $no=0;
                      foreach ($BoW_Positif as $key => $value) { 
                    ?>
                      <tr>
                        <th scope="row"><?= $no+1; ?></th>
                        <td><?= $key; ?></td>
                        <td><?= $value; ?></td>
                        <td><?= $value*$idf[$key]; ?></td>
                      </tr>
                    <?php $no++;
                      }  
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-md-6 mb-3">
            <div class="card">
              <div class="card-header text-center bg-warning text-white">
                KATA NEGATIF, KEMUNCULAN DAN BOBOT TF IDF
              </div>
              <div class="card-body">
                <table class="table table-striped table-bordered data">
                  <caption>Kata Unik dan Bobotnya</caption>
                  <thead>
                    <tr>
                      <th scope="col">No</th>
                      <th scope="col">Kata</th>
                      <th scope="col">Kemunculan</th>
                      <th scope="col">TF-IDF</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $BoW_Negatif = array();
                      $BoW_Negatif = $mnb->BagOfWords(0, $BoW_Negatif, $koneksi);
                      $no=0;
                      foreach ($BoW_Negatif as $key => $value) { 
                    ?>
                      <tr>
                        <th scope="row"><?= $no+1; ?></th>
                        <td><?= $key; ?></td>
                        <td><?= $value; ?></td>
                        <td><?= $value*$idf[$key]; ?></td>
                      </tr>
                    <?php $no++;
                      }  
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>

        <?php }else{ //===================== KONDISI BELUM PUNYA DATA LATIH MUNCUL SINI
        if ($n==0 && $p==0) {
          $alert='positif dan negatif';
        }elseif ($n==0) {
          $alert='negatif';
        }elseif ($p==0) {
          $alert='positif';
        }
        ?> 
      <div class="alert alert-danger alert-dismissible fade show mb-0 text-center " style="padding-top: 15px;">
        <strong>Belum ada fitur karena data latih anda belum memiliki komentar besentimen <?= $alert; ?>. Harap tambahkan data latih terlebih dahulu.</strong>
      </div>
      <?php //================== PENUTUP ELSE DAN PERULANGNA
        }
      //=====================PENUTUP ESLE DAN PERULANGAN?>

      </div>
    </div>
    <!-- INI BAGIAN END DAFTAR DATA LATIH -->
  </div>
  <!-- CONTENT -->

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container-fluid bg-dark text-center mt-5" id="foot">
      <span class="text-white">By Bayu Damar Jati. 2019.</span>
    </div>
  </footer>
  <!-- FOOTER -->

  <!-- MODAL -->
  <!-- MODAL LOGOUT -->
  <div class="modal" tabindex="-1" role="dialog" id="modalLogout">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="col-12 modal-title text-center text-white">
            LOG OUT
            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </h5>
        </div>
        <div class="modal-body">
          <p>Apakah anda yakin ingin keluar?</p>
        </div>
        <div class="modal-footer">
          <form action="../proses/controller_user.php" method="post">
            <button type="submit" name="logout" class="btn btn-danger">Logout</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL -->

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <!--   <script src="vendor/bootstrap/js/jquery-3.2.1.slim.min.js"></script> -->
  <script src="../vendor/bootstrap/js/jquery-3.2.1.js"></script>
  <script src="../vendor/bootstrap/js/popper.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="../vendor/bootstrap/datatables/js/jquery.dataTables.js"></script>
  
  <!-- JavaScript -->
  <script type="text/javascript">
  $(document).ready(function(){
    $('.data').DataTable({
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
        "language":{
          "decimal":        "",
          "emptyTable":     "Tidak ada data pada tabel ini",
          "info":           "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
          "infoEmpty":      "Menampilkan 0 hingga 0 dari 0 data",
          "infoFiltered":   "( Disaring dari _MAX_ total data)",
          "infoPostFix":    "",
          "thousands":      ",",
          "lengthMenu":     "Tampilkan _MENU_ data",
          "loadingRecords": "Memuat...",
          "processing":     "Memproses...",
          "search":         "Cari:",
          "zeroRecords":    "Tidak ada data yang ditemukan",
          "paginate": {
              "first":      "Pertama",
              "last":       "Terakhir",
              "next":       "Selanjutnya",
              "previous":   "Sebelumnya"
          },
          "aria": {
              "sortAscending":  ": activate to sort column ascending",
              "sortDescending": ": activate to sort column descending"
          }
      }
      });
  });
  </script>

  <script type="text/javascript">
    $(document).on("click","#ujiXls",function(){
      var idj = $(this).data('id');
      var nama = $(this).data('nama');
      $("#modal-uji #id_dl").val(idj);
      $("#modal-uji #nama_dl").val(nama);
    })
  </script>

  <script>
  // When the user scrolls down 20px from the top of the document, show the button
  window.onscroll = function() {scrollFunction()};

  function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
      document.getElementById("backtop").style.display = "block";
    } else {
      document.getElementById("backtop").style.display = "none";
    }
  }

  // When the user clicks on the button, scroll to the top of the document
  function topFunction() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
  }
  </script>

</body>
</html>