<?php  
	session_start();
	$id_admin=$_SESSION['id'];

	include '../config/koneksi.php';
	include "../vendor/autoload.php";
	include "../kelas/praproses.php";
	include "../kelas/tfidf.php";
	$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
	$stemmer  = $stemmerFactory->createStemmer();
	$StopWordRemoverFactory = new \Sastrawi\StopWordRemover\StopWordRemoverFactory();
	$StopWordRemover  = $StopWordRemoverFactory->createStopWordRemover();
	$praproses=new praproses();
	$tfidf=new tfidf();
	
	if (isset($_POST['tambahKomentar'])) {
		$komentar1=addslashes($_POST['komentar']);
		$sentimen=$_POST['sentimen'];

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

	    // var_dump($komentar);
	    // echo "<br><br>";
	    //jika hasil praproses tidak kosong maka dilanjutkan simpan ke database
	    if ($komentar!=='') {
	    	//menambahkan komentar ke database
	    	$query=mysqli_query($koneksi, "INSERT INTO komentar_latih VALUES ('', '$komentar1', $sentimen, $id_admin);");
	    	$id=mysqli_insert_id($koneksi);
	    	$komentar=explode(" ", $komentar);
	    	foreach ($komentar as $key => $value) {
	    		//menambahkan kata dalam komentar ke database
	    		$query=mysqli_query($koneksi, "INSERT INTO kata VALUES ('', '$value', $id);");
	    	}

	    	//ambil semua kata untuk perhitungan bobot IDF
		    $query=mysqli_query($koneksi, "SELECT kata FROM kata;");
		    $vocabulary=array();
		    while ($data=mysqli_fetch_array($query)) {
		      $vocabulary[]=$data['kata'];
		    }
		    $vocabulary=array_unique($vocabulary);

		    //ambil semua bobot idf
		    $query=mysqli_query($koneksi, "SELECT kata_bobot, bobot FROM bobot_idf;");
		  	$idfkata=array();
		  	while ($data=mysqli_fetch_array($query)) {
		    	$idfkata[]=$data['kata_bobot'];
		  	}


		  	//jika ada kata unik baru maka dimasukkan ke array kata baru
		  	$kata_baru=array();
		    foreach ($vocabulary as $kata) {
		      if (in_array($kata, $idfkata)) {
		      }else{
		        $kata_baru[]=$kata;
		      }
		    }

		    //ambil semua komentar dan melakukan preprocessing sehingga didapatkan kalimat yang bersih
		    $query=mysqli_query($koneksi,"SELECT komentar FROM komentar_latih");
		    $komentarBersih=array();
		    while ($data=mysqli_fetch_array($query)) {
		      	$k=$data['komentar'];
				$k=$praproses->addSlashes($k);
			    $k=$praproses->lower($k);
			    $k=$praproses->removeURL($k);
			    $k=$praproses->replaceStrip($k);
			    $k=$praproses->removeUsername($k);
			    $k=$praproses->removeNumber($k);
			    $k=$stemmer->stem($k);
			    $k=$praproses->removeLetter($k);
			    $k=$StopWordRemover->remove($k);
		      	$komentarBersih[]=$k;
		    }

		    //MENGHITUNG NILAI IDF KATA LAMA
		    $idfbobot=array();
		    $D=count($komentarBersih);
		    $idfbobot=$tfidf->hitungIDF($komentarBersih, $idfkata, $D);
		    $idfkata=array_combine($idfkata, $idfbobot);
		    foreach ($idfkata as $kata => $bobot) {
		      mysqli_query($koneksi, "UPDATE bobot_idf SET bobot=$bobot WHERE kata_bobot='$kata'");
		    }

		    //MENGHITUNG NILAI IDF KATA BARU JIKA ADA
		    if (count($kata_baru)>0) {
		    	$idfbobot=$tfidf->hitungIDF($komentarBersih, $kata_baru, $D);
			    $kata_baru=array_combine($kata_baru, $idfbobot);
			    foreach ($kata_baru as $kata => $bobot) {
			      mysqli_query($koneksi, "INSERT INTO bobot_idf VALUES ('', '$kata', $bobot);");
			    }
		    }

		    header('Location: ../sentimen_analisis/data_latih.php?m=TKS');
	    }else {
	    	//jika hasil praproses kosong maka akan diarahkan ke halaman awal dan minta inputkan ulang
	    	header('Location: ../sentimen_analisis/data_latih.php?m=TKG');
	    }
	}elseif (isset($_POST['tambahXLS'])) {

		//mempersiapkan file xls dan array untuk menyimpan data dari xls
		$tmpfname = basename($_FILES['datalatih']['name']);
		move_uploaded_file($_FILES['datalatih']['tmp_name'], $tmpfname);
		$excelReader = PHPExcel_IOFactory::createReaderForFile($tmpfname);
		$excelObj = $excelReader->load($tmpfname);
		$worksheet = $excelObj->getSheet(0);
		$lastRow = $worksheet->getHighestRow();
		$komentarXLS=array();
		$komentarBersih=array();
		$sentimenXLS=array();

		for ($row = 2; $row <= $lastRow; $row++) { 
			$komentar1 = addslashes($worksheet->getCell('B'.$row)->getValue());
			$sentimen = $worksheet->getCell('C'.$row)->getValue();

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
		    if ($komentar!=='' && $sentimen!=='' && ($sentimen=='1' || $sentimen=='0')) {
		    	echo $sentimen;
		    	if ($sentimen=='1') {
		    		$sentimen='1';
		    	}elseif ($sentimen=='0') {
		    		$sentimen='0';
		    	}

		    	$komentarXLS[]=$komentar1;
		    	$komentarBersih[]=$komentar;
		    	$sentimenXLS[]=$sentimen;
		    }
		}

		var_dump($komentarXLS);
		echo "<br>";
		echo "<br>";
		echo "<br>";
		var_dump($komentarBersih);
		echo "<br>";
		echo "<br>";
		echo "<br>";
		var_dump($sentimenXLS);
		echo "<br>";
		echo "<br>";
		echo "<br>";
		if (count($komentarXLS)>0 && count($sentimenXLS)>0 && count($komentarBersih)>0) {
			$panjang=count($komentarXLS);
			for ($i=0; $i < $panjang ; $i++) { 
				//menambahkan komentar ke database
		    	$query=mysqli_query($koneksi, "INSERT INTO komentar_latih VALUES ('', '$komentarXLS[$i]', $sentimenXLS[$i], $id_admin);");
		    	$id=mysqli_insert_id($koneksi);
		    	$komentar=explode(" ", $komentarBersih[$i]);
		    	foreach ($komentar as $key => $value) {
		    		//menambahkan kata dalam komentar ke database
		    		$query=mysqli_query($koneksi, "INSERT INTO kata VALUES ('', '$value', $id);");
		    	}
			}
			//ambil semua kata untuk perhitungan bobot IDF
		    $query=mysqli_query($koneksi, "SELECT kata FROM kata;");
		    $vocabulary=array();
		    while ($data=mysqli_fetch_array($query)) {
		      $vocabulary[]=$data['kata'];
		    }
		    $vocabulary=array_unique($vocabulary);

		    //ambil semua bobot idf
		    $query=mysqli_query($koneksi, "SELECT kata_bobot, bobot FROM bobot_idf;");
		  	$idfkata=array();
		  	while ($data=mysqli_fetch_array($query)) {
		    	$idfkata[]=$data['kata_bobot'];
		  	}


		  	//jika ada kata unik baru maka dimasukkan ke array kata baru
		  	$kata_baru=array();
		    foreach ($vocabulary as $kata) {
		      if (in_array($kata, $idfkata)) {
		      }else{
		        $kata_baru[]=$kata;
		      }
		    }

		    //ambil semua komentar dan melakukan preprocessing sehingga didapatkan kalimat yang bersih
		    $query=mysqli_query($koneksi,"SELECT komentar FROM komentar_latih");
		    $komentarBersih=array();
		    while ($data=mysqli_fetch_array($query)) {
		      	$k=$data['komentar'];
				$k=$praproses->addSlashes($k);
			    $k=$praproses->lower($k);
			    $k=$praproses->removeURL($k);
			    $k=$praproses->replaceStrip($k);
			    $k=$praproses->removeUsername($k);
			    $k=$praproses->removeNumber($k);
			    $k=$stemmer->stem($k);
			    $k=$praproses->removeLetter($k);
			    $k=$StopWordRemover->remove($k);
		      	$komentarBersih[]=$k;
		    }

		    //MENGHITUNG NILAI IDF KATA LAMA
		    $idfbobot=array();
		    $D=count($komentarBersih);
		    $idfbobot=$tfidf->hitungIDF($komentarBersih, $idfkata, $D);
		    $idfkata=array_combine($idfkata, $idfbobot);
		    foreach ($idfkata as $kata => $bobot) {
		      mysqli_query($koneksi, "UPDATE bobot_idf SET bobot=$bobot WHERE kata_bobot='$kata'");
		    }
		    //MENGHITUNG NILAI IDF KATA BARU JIKA ADA
		    if (count($kata_baru)>0) {
		    	$idfbobot=$tfidf->hitungIDF($komentarBersih, $kata_baru, $D);
			    $kata_baru=array_combine($kata_baru, $idfbobot);
			    foreach ($kata_baru as $kata => $bobot) {
			      mysqli_query($koneksi, "INSERT INTO bobot_idf VALUES ('', '$kata', $bobot);");
			    }
		    }
		    unlink($_FILES['datalatih']['name']);
		    header('Location: ../sentimen_analisis/data_latih.php?m=TKS');
		}else{
			unlink($_FILES['datalatih']['name']);
			header('Location: ../sentimen_analisis/data_latih.php?m=TKG');
		}
	}elseif (isset($_POST['hapusKomentar'])) {
		$id=$_POST['id'];
		mysqli_query($koneksi, "DELETE FROM kata WHERE id_komentar=$id;");
		mysqli_query($koneksi, "DELETE FROM komentar_latih WHERE id_komentar=$id;");

		//ambil semua kata untuk perhitungan bobot IDF
	    $query=mysqli_query($koneksi, "SELECT kata FROM kata;");
	    $vocabulary=array();
	    while ($data=mysqli_fetch_array($query)) {
	      $vocabulary[]=$data['kata'];
	    }
	    $vocabulary=array_unique($vocabulary);

	    //ambil semua bobot idf
	    $query=mysqli_query($koneksi, "SELECT kata_bobot FROM bobot_idf;");
	  	$idfkata=array();
	  	while ($data=mysqli_fetch_array($query)) {
	    	$idfkata[]=$data['kata_bobot'];
	  	}
	  	//JIKA LEBIH BANYAK KATA DI BOBOT IDF MAKA ADA YANG HARUS DIHAPUS. JIKA TIDAK CUKUP DIUPDATE.
	  	if (count($idfkata)>count($vocabulary)) {
	  		$kata_hapus=array();
	  		foreach ($idfkata as $kata) {
	  			if (in_array($kata, $vocabulary)) {	
	  			}else{
	  				$kata_hapus[]=$kata;
	  			}
	  		}
	  		// var_dump($kata_hapus);
	  		foreach ($kata_hapus as $key => $value) {
	  			$query=mysqli_query($koneksi, "DELETE FROM bobot_idf WHERE kata_bobot='$value';");
	  		}

	  		$query=mysqli_query($koneksi, "SELECT kata_bobot, bobot FROM bobot_idf;");
		  	$idfkata=array();
		  	while ($data=mysqli_fetch_array($query)) {
		    	$idfkata[]=$data['kata_bobot'];
		  	}
	  	}
	  	 //ambil semua komentar dan melakukan preprocessing sehingga didapatkan kalimat yang bersih
	    $query=mysqli_query($koneksi,"SELECT komentar FROM komentar_latih");
	    $komentarBersih=array();
	    while ($data=mysqli_fetch_array($query)) {
	      	$k=$data['komentar'];
			$k=$praproses->addSlashes($k);
		    $k=$praproses->lower($k);
		    $k=$praproses->removeURL($k);
		    $k=$praproses->replaceStrip($k);
		    $k=$praproses->removeUsername($k);
		    $k=$praproses->removeNumber($k);
		    $k=$stemmer->stem($k);
		    $k=$praproses->removeLetter($k);
		    $k=$StopWordRemover->remove($k);
	      	$komentarBersih[]=$k;
	    }

	    //MENGHITUNG NILAI IDF KATA LAMA
	    $idfbobot=array();
	    $D=count($komentarBersih);
	    $idfbobot=$tfidf->hitungIDF($komentarBersih, $idfkata, $D);
	    $idfkata=array_combine($idfkata, $idfbobot);
	    foreach ($idfkata as $kata => $bobot) {
	      mysqli_query($koneksi, "UPDATE bobot_idf SET bobot=$bobot WHERE kata_bobot='$kata'");
	    }
	    header('Location: ../sentimen_analisis/data_latih.php?m=HKS');
	}elseif (isset($_POST['editKomentar'])) {
		$id=$_POST['id'];
		$komentar1=$_POST['komentar'];
		$sentimen=$_POST['sentimen'];
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

	    if ($komentar!=='') {
	    	mysqli_query($koneksi, "DELETE FROM kata WHERE id_komentar=$id;");
	    	mysqli_query($koneksi, "UPDATE komentar_latih SET komentar='$komentar1', sentimen=$sentimen, id_admin=$id_admin WHERE id_komentar=$id;");
	    	$komentar=explode(" ", $komentar);
	    	foreach ($komentar as $key => $value) {
	    		mysqli_query($koneksi, "INSERT INTO kata VALUES ('', '$value', $id);");
	    	}

	    	//ambil semua komentar dan melakukan preprocessing sehingga didapatkan kalimat yang bersih
		    $query=mysqli_query($koneksi,"SELECT komentar FROM komentar_latih");
		    $komentarBersih=array();
		    while ($data=mysqli_fetch_array($query)) {
		      	$k=$data['komentar'];
				$k=$praproses->addSlashes($k);
			    $k=$praproses->lower($k);
			    $k=$praproses->removeURL($k);
			    $k=$praproses->replaceStrip($k);
			    $k=$praproses->removeUsername($k);
			    $k=$praproses->removeNumber($k);
			    $k=$stemmer->stem($k);
			    $k=$praproses->removeLetter($k);
			    $k=$StopWordRemover->remove($k);
		      	$komentarBersih[]=$k;
		    }
		    $D=count($komentarBersih);
		    //ambil semua kata untuk perhitungan bobot IDF
		    $query=mysqli_query($koneksi, "SELECT kata FROM kata;");
		    $vocabulary=array();
		    while ($data=mysqli_fetch_array($query)) {
		      $vocabulary[]=$data['kata'];
		    }
		    $vocabulary=array_unique($vocabulary);

		    //ambil semua bobot idf
		    $query=mysqli_query($koneksi, "SELECT kata_bobot, bobot FROM bobot_idf;");
		  	$idfkata=array();
		  	while ($data=mysqli_fetch_array($query)) {
		    	$idfkata[]=$data['kata_bobot'];
		  	}

		  	if(count($vocabulary)>count($idfkata)){
		  		//cek kata baru
		  		$kata_baru=array();
			    foreach ($vocabulary as $kata) {
			      if (in_array($kata, $idfkata)) {
			      }else{
			        $kata_baru[]=$kata;
			      }
			    }
			    //MENGHITUNG NILAI IDF KATA LAMA
			    $idfbobot=array();
			    $idfbobot=$tfidf->hitungIDF($komentarBersih, $idfkata, $D);
			    $idfkata=array_combine($idfkata, $idfbobot);
			    foreach ($idfkata as $kata => $bobot) {
			      mysqli_query($koneksi, "UPDATE bobot_idf SET bobot=$bobot WHERE kata_bobot='$kata'");
			    }
			    //masukkan kata beru dan nilai bobotnya ke bobot idf
			    if (count($kata_baru)>0) {
			    	$idfbobot=$tfidf->hitungIDF($komentarBersih, $kata_baru, $D);
				    $kata_baru=array_combine($kata_baru, $idfbobot);
				    foreach ($kata_baru as $kata => $bobot) {
				      mysqli_query($koneksi, "INSERT INTO bobot_idf VALUES ('', '$kata', $bobot);");
				    }
			    }

		  	}elseif (count($vocabulary)<count($idfkata)) {
		  		//cek kata di idf kata yang lebih banyak
		  		$kata_hapus=array();
		  		foreach ($idfkata as $kata) {
		  			if (in_array($kata, $vocabulary)) {	
		  			}else{
		  				$kata_hapus[]=$kata;
		  			}
		  		}
		  		// var_dump($kata_hapus);
		  		//hapus kata yag lebih banyak di bobot idf
		  		foreach ($kata_hapus as $key => $value) {
		  			$query=mysqli_query($koneksi, "DELETE FROM bobot_idf WHERE kata_bobot='$value';");
		  		}
		  		//AMBIL ULANG KATA BOBOT IDF KARENA ADA YANG TERHAPUS
		  		$query=mysqli_query($koneksi, "SELECT kata_bobot, bobot FROM bobot_idf;");
			  	$idfkata=array();
			  	while ($data=mysqli_fetch_array($query)) {
			    	$idfkata[]=$data['kata_bobot'];
			  	}
			  	//MENGHITUNG NILAI IDF
			    $idfbobot=array();
			    $idfbobot=$tfidf->hitungIDF($komentarBersih, $idfkata, $D);
			    $idfkata=array_combine($idfkata, $idfbobot);
			    foreach ($idfkata as $kata => $bobot) {
			      mysqli_query($koneksi, "UPDATE bobot_idf SET bobot=$bobot WHERE kata_bobot='$kata'");
			    }
		  	}elseif (count($vocabulary)==count($idfkata)) {
		  		//MENGHITUNG NILAI IDF KATA LAMA
			    $idfbobot=array();
			    $idfbobot=$tfidf->hitungIDF($komentarBersih, $idfkata, $D);
			    $idfkata=array_combine($idfkata, $idfbobot);
			    foreach ($idfkata as $kata => $bobot) {
			      mysqli_query($koneksi, "UPDATE bobot_idf SET bobot=$bobot WHERE kata_bobot='$kata'");
			    }
		  	}
		    header('Location: ../sentimen_analisis/data_latih.php?m=EKS');
	    }else{
	    	header('Location: ../sentimen_analisis/data_latih.php?m=EKG');
	    }
	}elseif (isset($_POST['hapusSemua'])) {
		mysqli_query($koneksi, "DELETE FROM kata;");
		mysqli_query($koneksi, "DELETE FROM komentar_latih;");
		mysqli_query($koneksi, "DELETE FROM bobot_idf;");
		header('Location: ../sentimen_analisis/data_latih.php?m=HSKS');
	}
?>