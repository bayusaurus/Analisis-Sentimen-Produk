<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="vendor/bootstrap/css/stylex.css">
  <link rel="stylesheet" href="vendor/bootstrap/datatables/css/dataTables.bootstrap.css">
  <link rel="stylesheet" href="vendor/bootstrap/datatables/css/jquery.dataTables.css">
  <style type="text/css">
  .bg-gradasi{
    background: rgb(224,192,207); 
    background: linear-gradient(55deg, rgba(224,192,207,1) 2%, rgba(198,241,217,1) 85%);
  }

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
  <link rel="icon" href="favicon.ico" type="image/x-icon">

  <!-- TITLE -->
  <title>HASIL PELABELAN | Sentiment Analyzer</title>
</head>

<body class="bg-gradasi"> 
  
  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-sm navbar-dark bg-dark" data-toggle="affix" style="padding-top: 20px; padding-bottom: 20px;">
    <div class="mx-auto d-sm-flex d-block flex-sm-nowrap">
      <a class="navbar-brand" href="index.php">Sentiment Analyzer</a>
    </div>
  </nav>
  <!-- NAVBAR -->
  <!-- CONTENT -->
  <div class="container-fluid mt-4">
    <div class="card"> 
      <div class="card-body bg-info">    
      <div class="row">
          <div class="col-md-12 bg-info">
            <h3 class="mt-4 text-center text-white">Selamat Datang di Sentiment Analyzer DEMO</h3>
          </div>
          
          <?php  
            include 'config/koneksi.php';
            $query=mysqli_query($koneksi, "SELECT id_komentar FROM komentar_latih where sentimen=1;");
            $query2=mysqli_query($koneksi, "SELECT id_komentar FROM komentar_latih where sentimen=0;");
            if (mysqli_num_rows($query)>0 && mysqli_num_rows($query2)>0) { ?>
              <div class="col-md-2 bg-info"></div>
              <div class="col-md-8 bg-info">
                <div class="card mt-3">
                  <div class="card-header bg-info text-white text-center">Analisis Sentimen dengan Komentar</div>
                  <div class="card-body bg-info">
                    <?php 
                      if (isset($_POST['komentar'])) {
                        $komentar1=$_POST['komentar'];
                        include 'kelas/naive_bayes.php'; //funsi naive bayes
                        include 'kelas/praproses.php'; //fungsi preprocessing
                        include 'kelas/tfidf.php'; //fungsi tfidf
                        include 'vendor/autoload.php'; //sastrawi
                        include 'vendor/excelreader/excel_reader2.php'; //excel reader
                        //pembuatan stemmer dan stopword removal
                        $stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory(); //deklarasi sastrawi stemmer
                        $stemmer  = $stemmerFactory->createStemmer(); //deklarasi stemmer
                        $StopWordRemoverFactory = new \Sastrawi\StopWordRemover\StopWordRemoverFactory(); //deklarasi sastrawi stopword remover
                        $StopWordRemover  = $StopWordRemoverFactory->createStopWordRemover(); //deklarasi stopword remover
                        $praproses=new praproses();
                        $mnb=new naive_bayes();
                        $tfidf=new tfidf();
                        //praproses komentar
                        $komentar=$praproses->addSlashes($komentar1);
                        $komentar=$praproses->lower($komentar);
                        $komentar=$praproses->removeURL($komentar);
                        $komentar=$praproses->replaceStrip($komentar);
                        $komentar=$praproses->removeUsername($komentar);
                        $komentar=$praproses->removeNumber($komentar);
                        $komentar=$stemmer->stem($komentar);
                        $komentar=$praproses->removeLetter($komentar);
                        $komentar=$StopWordRemover->remove($komentar);

                        $BOWpositif=array();
                        $BOWnegatif=array();
                        $idf=array();  
                        $BOWpositif=$mnb->BagOfWords(1, $BOWpositif, $koneksi);
                        $BOWnegatif=$mnb->BagOfWords(0, $BOWnegatif, $koneksi);
                        $idf=$tfidf->idf($idf, $koneksi);
                        $total_positif=$mnb->jumlahData(1, $koneksi);
                        $total_negatif=$mnb->jumlahData(0, $koneksi);
                        $prior_positif=$mnb->prior($total_positif,$total_negatif);
                        $prior_negatif=$mnb->prior($total_negatif, $total_positif);
                        $total_idf=$mnb->totalBobot($idf);
                        foreach ($BOWpositif as $key => $value) {
                          $BOWpositif[$key]=$idf[$key]*$value;
                        }
                        foreach ($BOWnegatif as $key => $value) {
                          $BOWnegatif[$key]=$idf[$key]*$value;
                        }
                        $total_bobot_kata_negatif=$mnb->totalBobot($BOWnegatif);
                        $total_bobot_kata_positif=$mnb->totalBobot($BOWpositif);
                        
                        $conditional=array();
                        //memecah komentar yg sudah bersih ke kata per kata
                        $komentar=explode(" ", $komentar);
                        //menghitung probabilitas kondisional kata per kata
                        $conditional=$mnb->conditionalProb1($komentar, $conditional, $BOWpositif, $total_bobot_kata_positif, $total_idf, 'Positif');
                        //menghitung total kondisional
                        $likelihood=$mnb-> totalConditional($conditional);
                        //menghiutng posterior probabilitas
                        $posterior_pos=$mnb->posteriorProb($likelihood,$prior_positif);

                        $conditional=array();
                        //menghitung probabilitas kondisional kata per kata
                        $conditional=$mnb->conditionalProb1($komentar, $conditional, $BOWnegatif, $total_bobot_kata_negatif, $total_idf, 'Negatif');
                        //menghitung total kondisional
                        $likelihood=$mnb-> totalConditional($conditional);
                        //menghiutng posterior probabilitas
                        $posterior_neg=$mnb->posteriorProb($likelihood,$prior_negatif); 
                        $hasil=$mnb->sentimenAkhir($posterior_pos, $posterior_neg);
                        if ($hasil=='Positif') {
                          $warna='alert-success';
                        }elseif($hasil=='Negatif'){
                          $warna='alert-danger';
                        }
                      ?>
                      <div class="my-2 mx-2">
                        <div class="alert <?= $warna; ?> alert-dismissible fade show  " style="padding-top: 15px;">
                          <!-- <button type="button" class="close" data-dismiss="alert">&times;</button> -->
                          Komentar = <?= $komentar1; ?><br> 
                          Sentimen = <strong><?= $hasil; ?></strong>
                        </div>
                      </div>
                      <center>
                      <form class="form-inline mx-2" action="proses/proses_simpan_hasil.php" method="post">
                        <div class="form-group mb-2 text-white">Apakah hasil analisis sesuai dengan pendapat anda?
                        </div>
                        <input type="hidden" name="komentar" value="<?= $komentar1; ?>">
                        <input type="hidden" name="hasil" value="<?= $hasil; ?>">
                        <button type="submit" class="btn btn-success ml-1 mb-2" name="sesuai">Sesuai</button>
                        <button type="submit" class="btn btn-danger ml-1 mb-2" name="tidak">Tidak</button>
                      </form>
                      </center>
                    <?php }
                    ?>
                    <?php  
                    if (isset($_GET['m'])) {
                      if ($_GET['m']=='ty') { ?>
                        <div class="alert alert-success alert-dismissible fade show mb-1 text-center " style="padding-top: 15px;">
                          <button type="button" class="close" data-dismiss="alert">&times;</button>
                          <strong>Terima kasih sudah memberikan feedback pendapat anda.</strong>
                        </div>
                    <?php  }
                    }
                    ?>
                    <form class="my-2 mx-2" action="index.php" method="post">
                      <div class="form-group">
                        <label for="exampleFormControlTextarea1" class="text-white">Masukkan komentar anda mengenai Iphone :</label>
                        <textarea class="form-control" rows="5" name="komentar" minlength="10" maxlength="280" required placeholder="contoh: Menurut saya iphone sangat bagus"></textarea>
                      </div>
                      <button type="submit" class="btn btn-light mb-2" name="submit" value="1">Proses Analisis Sentimen</button>
                    </form>  
                  </div>
                </div>
              </div>
              <div class="col-md-2 bg-info"></div>

              <div class="col-md-2 bg-info"></div>
              <div class="col-md-8 bg-info">
                <div class="card mt-3 mb-3">
                  <div class="card-header bg-info text-white text-center">Analisis Sentimen dengan File .xls</div>
                  <div class="card-body bg-info">
                    <form action="hasil.php" method="post" enctype="multipart/form-data" id="myForm" class="mx-3 my-3">
                      <div class="form-group">
                        
                        <p class="text-white text-center">Anda bisa melakukan analisis sentimen dengan banyak komentar sekaligus, pertama unduh <kbd><a class="text-white" href="uploads/template_data_pelabelan.xls" download="Template Pelabelan">Template</a></kbd>, kemudian isikan komentar di dalam file tersebut sesuai format</p> 
                        <label for="uji" class=" text-white">Masukkan File Anda : </label>
                        <input class="form-control-file text-white" name="uji" type="file" placeholder="File" id="filexls" onchange="fileValidation()" required="">
                      </div>
                      <button type="submit" class="btn btn-light mb-2" name="ujiXLS">Proses Analisis Sentimen</button>
                    </form>  
                  </div>
                </div>
              </div>
              <div class="col-md-2 bg-info"></div>  
          <?php }else{ ?>
              <div class="col-md-1 bg-info"></div>
              <div class="col-md-10 bg-info">
                <div class="alert alert-danger alert-dismissible fade show mb-4 mt-4 text-center " style="padding-top: 15px;">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <strong>MAAF TIDAK BISA MELAKUKAN ANALISIS SENTIMEN PADA SAAT INI, COBA LAGI DI LAIN WAKTU</strong>
                </div>
              </div>
              <div class="col-md-1 bg-info"></div>
          <?php  }
          ?>             
      </div>
    </div>
  </div><!-- END CONTAINER CONTENT -->
</div>
  <!-- CONTENT -->
  
  <!-- FOOTER -->
  <footer class="footer">
    <div class="container-fluid bg-dark text-center mt-5" id="foot">
      <span class="text-white">By Bayu Damar Jati. 2019.</span>
    </div>
  </footer>
  <!-- FOOTER -->

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <!--   <script src="vendor/bootstrap/js/jquery-3.2.1.slim.min.js"></script> -->
  <script src="vendor/bootstrap/js/jquery-3.2.1.js"></script>
  <script src="vendor/bootstrap/js/popper.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

  <script type="text/javascript">
    function fileValidation(){
      var fileInput = document.getElementById('filexls');
      var filePath = fileInput.value;
      var allowedExtensions = /(\.xls)$/i;
      if(!allowedExtensions.exec(filePath)){
        alert('Tolong upload file yang berjenis .xls dan sesuai dengan template yang disediakan.');
        fileInput.value = '';
        return false;
      }
    }
  </script>

</body>
</html>