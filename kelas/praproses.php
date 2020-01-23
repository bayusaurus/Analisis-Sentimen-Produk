<?php
/**
 * 
 */
class praproses
{
	function addSlashes($kalimat){
	 	return addslashes($kalimat);
	}
	function lower($kalimat){
		return strtolower($kalimat);
	}
	function removeURL($kalimat){
		return preg_replace("@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@", '', $kalimat);
	}
	function replaceStrip($kalimat){
		return $kalimat=str_replace("-", " ", $kalimat);
	}
	function removeUsername($kalimat){
		return preg_replace("/@\w{1,15}/", '', $kalimat);
	}
	function removeNumber($kalimat){
		return preg_replace('/[0-9]+/', '', $kalimat);
	}
	function removeLetter($kalimat){
		$kalimat=explode(" ", $kalimat);
		$length=count($kalimat);
		for ($i=0; $i <$length ; $i++) { 
			if (strlen($kalimat[$i])==1) {
				unset($kalimat[$i]);
			}
		}
		return implode(" ", $kalimat);
	}
}

	// $praproses=new praproses();
	// include '../vendor/autoload.php';
	// $stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
	// $stemmer  = $stemmerFactory->createStemmer();
	// $StopWordRemoverFactory = new \Sastrawi\StopWordRemover\StopWordRemoverFactory();
	// $StopWordRemover  = $StopWordRemoverFactory->createStopWordRemover();

	// $kalimat="@TODD_aap iphone baterai di a a a a ke @bayu0099 https://bayu.com --==0 =0989776 |||\\\//.,;l  boros" ;
	// echo $kalimat;
	// echo "<br>";
	// $kalimat=$praproses->addSlashes($kalimat);
	// $kalimat=$praproses->lower($kalimat);
	// $kalimat=$praproses->removeURL($kalimat);
	// $kalimat=$praproses->replaceStrip($kalimat);
	// $kalimat=$praproses->removeUsername($kalimat);
	// $kalimat=$praproses->removeNumber($kalimat);
	// $kalimat=$praproses->removeLetter($kalimat);
	// $kalimat=$stemmer->stem($kalimat);
	// $kalimat=$praproses->removeLetter($kalimat);
	// $kalimat=$StopWordRemover->remove($kalimat);

	// $kalimat='di sana  bagus                                   pandang sekali banget';
	// $kalimat=$StopWordRemover->remove($kalimat);
	// echo $kalimat;
	// echo "<br>";
	// var_dump($kalimat);
?>