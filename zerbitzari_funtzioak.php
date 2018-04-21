<?php
//zerbitzariaren funtzio nagusiak biltzen
//************************************PHP FUNTZIOEN HASIERA**********************************
//datubaserako funtzio orokorrak 1.0 konektatu eta deskonektatu
//CORS HASIERATU DENAK ONARTU! https://stackoverflow.com/questions/18382740/cors-not-working-php#18399709

function cors_hasieratu(){
if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

    // Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
       header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
       header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}
}
cors_hasieratu();
class Datubasea{
	
	//URL nabigatzaile bitartez abestiak non eta DIR fitxategi sisteman abestiak non
	private $URL = "http://192.168.0.157/musikaboz/musika/";
	private $DIR = "/opt/lampp/htdocs/musikaboz/musika/*";
	private $zerbitzaria;
	private $erabiltzailea;
	private $pasahitza;
	private $dbizena;
	private $conn;

	//sortzailea -> defektuzko datuak
	public function __construct(){
		$this->zerbitzaria = "localhost";
		$this->erabiltzailea = "root";
		$this->pasahitza = "";
		$this->dbizena = "MusikaBoz";	
	}
	
	//konektatzeko funtzioa
	private function konektatu(){
		try{
			// Create connection
			$this->conn = new mysqli($this->zerbitzaria, $this->erabiltzailea, $this->pasahitza, $this->dbizena);
			// Check connection
			if ($this->conn->connect_errno) {
    				printf("[!] Konexioak huts egin du: %s\n", $this->conn->connect_error);
    				exit();
			}
			//return $this->$conn;
		}
		catch(Exception $e) {
  			printf("[!] Mezua: %s\n", $e->getMessage());
		}
	}

	//deskonektatzeko funtzioa
	private function deskonektatu(){
		try{
			$this->conn->close();
			if ($this->conn->connect_errno) {
    				printf("[!] Deskonexioak huts egin du: %s\n", $this->conn->connect_error);
    				exit();
			}
		}
		catch(Exception $e) {
  			printf("[!] Mezua: %s\n", $e->getMessage());
		}
	}

	//Taula denak husteko funtzioa
	private function hustu_taulak(){
		try{
			$this->konektatu();
			$this->hustu_taula("bozkak");
			$this->hustu_taula("abestiak");
			$this->hustu_taula("bozkatuta");
			$this->deskonektatu();
			printf("[>] Taulak ondo hustu dira </br>");
		}
		catch(Exception $e) {
  			printf("[!] Mezua: %s\n", $e->getMessage());
		}
	}
	//Taula bat husteko funtzioa
	private function hustu_taula($taula){
		try{
			//taula izena ezin lotu/bind sententziari beraz append
			//DELETE edo TRUNCATE
			$sql = "DELETE FROM " . $taula . " WHERE 1";
			$sententzia = $this->conn->prepare($sql);
			if ($sententzia->execute()){
				printf("[>] %s taula ONDO EZABATU DA \n </br>", $taula);
				//echo "[>] " . $taula . " ONDO EZABATU DA.</br>" ;
			}else{
				printf("[!] AKATSA \n", $this->conn->error);
			}
			$sententzia->close();
		}
		catch(Exception $e) {
  			printf("[!] Mezua: %s\n", $e->getMessage());
		}
		
	}
	//bozkak taula husteko funtzioa
	public function hustu_bozkak(){
		try{
			$this->hustu_taula("bozkak");
		}
		catch(Exception $e) {
  			printf("[!] Mezua: %s\n", $e->getMessage());
		}
	}
		
	
	//datubasea abestiz kargatzeko funtzioa 1.0 
	public function kargatu_datubasea_abestiz(){
		try{
			//taulak husteko funtzioa probatuz
			$this->hustu_taulak();
			$this->konektatu();
			$dir = $this->DIR;

			// fitxategiak zerrendatu
			$i = 1;
			foreach(glob($dir) as $file) {
				//abestiaren informazioa inprimatu>
				echo "<b>[*] Fitxategia: $file : filetype: " . filetype($file) . "</b><br/>";
				echo "[**] abestia db-an gordetzen ";
				$abestia = $this->begiratu_info_mp3($file)[0];
				$taldea = $this->begiratu_info_mp3($file)[1];
				$fitxizena = basename($file);
				//irudia bilatu 1.0 > 
				if ($this->topatu_irudia_bing($taldea) != "" ){
					//echo "bilaketa1";
					$katea = $abestia . " " . $taldea;		
					$irudia = $this->topatu_irudia_bing($katea);
				}else{
					//echo "bilaketa2 nahi izanez gero... :( hobetuu";
					$katea = $taldea;
					$irudia = $this->topatu_irudia_bing($katea);
				}
				//optimizatu irudien pisua etb. etorkizunean>bilaketa zehaztu?
				if (stripos($irudia,".jpg") == false) {
					$irudia = "https://openclipart.org/image/2400px/svg_to_png/130039/Music-icon.png";
				}
				//hemen ere aldatu behar bidea!!
				$bidea = $this->URL . $fitxizena;
				$sententzia = $this->conn->prepare( "INSERT INTO abestiak (ab_id, mota, taldea, abestia, iturria, irudia) VALUES (?,'lokala',?,?,?,?)");
				$sententzia->bind_param('issss',$i,$taldea,$abestia,$bidea,$irudia);
				$i = $i + 1 ;
				if ($sententzia->execute() == TRUE) {	
					echo "[>] ondo erregistratu da abestia datubasean</br>";	
				} else {
    					echo "[!]Errorea: " . $sql . "<br>" . $this->conn->error;
				}
   
			}
			$sententzia->close();
			$this->deskonektatu();
		}
		catch(Exception $e) {
  			printf("[!] Mezua: %s\n", $e->getMessage());
		}
	}

	//begiratu ia erabiltzailea existitzen den edo ez sinpleki...
	public function begiratu_erab($erab,$pass){
		try{
			$this->konektatu();
			//datubasean bilaketa egin
			$sententzia = $this->conn->prepare("SELECT erab FROM erabiltzaileak WHERE erab = ? and pass = ? ");
			$sententzia->bind_param('ss',$erab,$pass);		
			$erantzuna = $sententzia->execute();
			//existitzen den edo ez frogatu, ez bada existitzen => wtf
			if ($erantzuna != ""){	
				$sententzia->store_result();
				$lerroak = $sententzia->num_rows;
				//lerrorik bada, existitzen da
				if ($lerroak > 0) {
					return $erantzuna;			
				}else{
					return "wtf";
				}
	 		}else{
				return "wtf";
			}
			$sententzia->close();
			$this->deskonektatu();
		}
		catch(Exception $e) {
  			printf("[!] Mezua: %s\n", $e->getMessage());
		}
	}
	//begiratu erabiltzaileak dagoeneko bozkatu duen edo ez TRUE: BOZKATU DU DAGOENEKO FALSE: EZ DU BOZKATU ORAINDIK
	public function begiratu_bozkatuta($erab){
		try{
			$this->konektatu();
			$sententzia = $this->conn->prepare("SELECT bozkatuta FROM bozkakon WHERE erab = ? AND bozkatuta = True "); 
			$sententzia->bind_param('s',$erab);
			//inprimatu datubasetik irakurtzen den balioa
			$sententzia->execute();
			$sententzia->store_result();
			$lerroa = $sententzia->num_rows;
			if ($lerroa > 0){
				return True;
			}else{
				return False;
			}
			$this->deskonektatu();
			$sententzia->close();
		}
		catch(Exception $e) {
  			printf("[!] Mezua: %s\n", $e->getMessage());
		}
	}
	//erabiltzaileak dagoeneko bozkatu duela erregistratu // ez da beharrezkoa
	//INSERT ordez UPDATE hobe agian...
	public function erregistratu_bozkatuta($erab){
		try{
			$this->konektatu();
			if ($this->begiratu_bozkatuta($erab)){
				$sententzia = $this->conn->prepare("INSERT INTO bozkakon(erab,bozkatuta) VALUES (? , True)");
				$sententzia->bind_param('s',$erab);		
				if ($sententzia->execute() == TRUE){
					echo "[>] Erabiltzaileak bozkatu du </br>";
				}else{
					echo "[!]Errorea: " . $sententzia . "<br>" . $this->conn->error;
				}
				$sententzia->close();
			}
			$this->deskonektatu();
			
		}
		catch(Exception $e) {
  			printf("[!] Mezua: %s\n", $e->getMessage());
		}
			
			
	}

	//hautapen 1 gordetzeko funtzioa
	private function gorde_datubasean_hautapena($info){
		try{
			$sententzia = $this->conn->prepare("INSERT INTO bozkak(ab_id,bozka_kop) VALUES (?, 1)");
			$sententzia->bind_param('s',$info);
			if ($sententzia->execute() == TRUE){
				echo "[>] Zure bozka datubasean ondo erregistratu da </br>";
			}else{
				echo "[!] Akatsa: " . $sententzia . "<br>" . $this->conn->error;
			}
			
		}		
		catch(Exception $e) {
  			printf("[!] Mezua: %s\n", $e->getMessage());
		}
	}
	
	//5 hautapen gordetzeko funtzioa
	public function gorde_datubasean_hautapenak($info){
		try{
			//kontrolatu 3 bakarrik sartzen
			$this->konektatu();
        		//$bozkakodea = sortu_kodea();
			//begiratu iada erregistratuta dagoen edo ez
			foreach($info as $item) {
				$item = (string)$item;
				$this->gorde_datubasean_hautapena($item);
			}
			$this->deskonektatu();	
		}
		catch(Exception $e) {
  			printf("[!] Mezua: %s\n", $e->getMessage());
		}
	}


	//https://desenvolupant.wordpress.com/2011/04/23/recorrer-carpetas-con-glob-en-php/
	//https://ranacse05.wordpress.com/2010/03/15/read-mp3-tags-with-php/

	//mp3 fitxategien barneko tag-ak irakurtzeko funtzioa goiko esteketatik kopiatua
	private function begiratu_info_mp3($file){
		try{	
	        	$mp3 = $file; //The mp3 file.
	        	$filesize = filesize($mp3);
	        	$fitx = fopen($mp3, "r");
			fseek($fitx, -128, SEEK_END); 
	     		$tag = fread($fitx, 3);
	     		if($tag == "TAG"){
	        		$data["song"] = trim(fread($fitx, 30));
	         		$data["artist"] = trim(fread($fitx, 30));
	         		//$data["album"] = trim(fread($fitx, 30));
	         		//$data["year"] = trim(fread($fitx, 4));
	         		//$data["comment"] = trim(fread($fitx, 30));
	         		//$data["genre"] = trim(fread($fitx, 1));
         
	     		} else {
	         		echo("MP3 file does not have any ID3 tag!</br>");
	     		}
     	
	     		while(list($key, $value) = each($data)){
	         		print("$key: $value<br>\r\n");    
	     		}
			fclose($fitx);
			return array($data["song"], $data["artist"]);
		}
		catch(Exception $e) {
  			printf("[!] Mezua: %s\n", $e->getMessage());
		}
	}
	//bing-en irudi bilatzaile xumea 1.0
	private function topatu_irudia_bing($testua){
		try{
			$url = "http://www.bing.com/images/search?".urlencode(strtolower($testua))."&count=1&q=".urlencode($testua);
			$data=file_get_contents($url);
			$emaitza = (string)$data;
			$emaitza = htmlspecialchars($emaitza);

			//topatu beharreko katea hasieran konplikatu gabe,  topatzen duen lehen .jpg -a! batzuetan fail :(
			//eta noski, geldooa 
    			$topatu = '.jpg';
	
			if ( stripos($emaitza, $topatu) !== false){
				echo "**********************************************************************************";
				//txapuzeroki iruditxo bat lortu BING-etik ;) kateen tratamentua askooo hobetu daiteke... edo DOM-a errekorritzen saiatu...
				$non = stripos($emaitza,$topatu);
				$emaitza2 = substr($emaitza, $non-300,600);
				//echo $emaitza2;
				$topatu2 = "https:";
				$non2 = stripos($emaitza2,$topatu2);
				$emaitza3 = substr($emaitza2, $non2, 300);
				//echo $emaitza3; //http:dfdfasfds iada lortuta, baina soberakina kendu behar.
				//echo "*********depuratuta>>***";
				$topatu3 = "&quot;";
				$non3 = stripos($emaitza3,$topatu3);
				//echo $non2 . "-" . $non3 . " >>>>>>>>>>";
				//$zenbat = $non2 - $non3;
				//echo $zenbat;
				$emaitza4 = substr($emaitza3, 0, $non3);
				//echo $emaitza4;
				echo "<img src='" . $emaitza4 . "' witdh='100px' height='100px'/></br>";
			}else{
				echo 'False';
			}
			return $emaitza4;	
		}
		catch(Exception $e) {
  			printf("[!] Mezua: %s\n", $e->getMessage());
		}
	}

	function begiratu_datubasean_hautatuak(){
        	$bistaratu_zerrenda = "<ul> ";
		$this->konektatu();
        	//$bozkakodea = sortu_kodea();
		$sql = "SELECT abestiak.abestia,abestiak.taldea,abestiak.irudia, count(bozkak.bozka_kop) as konta from abestiak, bozkak where abestiak.ab_id=bozkak.ab_id group by abestiak.abestia order by konta desc;";
        	$result = $this->conn->query($sql);
		if ($result->num_rows > 0){
        		while($row = mysqli_fetch_assoc($result)){			
				//$irudia = $row["irudia"]
				$bistaratu_zerrenda = $bistaratu_zerrenda . "<li class='nireli'><div id='ab'><div id='bozkak'><b>" .  $row["konta"] . "</b></div><img id='iruditxoa' width='50px' height='50px' src='" . $row["irudia"] . "'/><div id='testua'><b>" . $row["abestia"] . "</b> " . $row["taldea"] . "</div></div></li>";
			}	
		}
		$bistaratu_zerrenda = $bistaratu_zerrenda . "</ul>";
		echo $bistaratu_zerrenda;
		/* free result set */
        	$result->free();
		$this->deskonektatu();
	}

	function begiratu_datubasean(){
        	$bistaratu_zerrenda = " ";
		$this->konektatu();
        	//$bozkakodea = sortu_kodea();
		$sql = "SELECT * from abestiak";
        	$result = $this->conn->query($sql);
		$bistaratu_zerrenda = "<form id='bidaliabestiak'>";
		if ($result->num_rows > 0){
        		while($row = mysqli_fetch_assoc($result)){
				$lerroa = "<input type='checkbox' name='" . $row["ab_id"] . "' id='" . $row["ab_id"] . "' value ='" . $row["ab_id"]  . "' ><b>" . $row["abestia"] . "</b> " . $row["taldea"] . "<hr color='lightgrey'>";  
				$bistaratu_zerrenda = $bistaratu_zerrenda . $lerroa;
			}	
		}
		$bistaratu_zerrenda = $bistaratu_zerrenda . "<input type='button' id='bidali' name='bidali' value='bidali' onclick='bidali_hautatutakoak()'/></form>";
		echo $bistaratu_zerrenda;
		/* free result set */
        	$result->free();
		$this->deskonektatu();
	}
}

class Musikaboz{
	
	private $bozmartxan;
	private $hasiera_ordua;
	private $bukaera_ordua;
	private $iraupena;
	private $mezua;
	private $erabiltzaileak;
		
	
	public function __construct($iraupena){

		$this->bozkaketa_hasi();
		//echo "iraupena" . $iraupena . " ";
		$this->iraupena = $iraupena;
		$this->hasiera_ordua = time();
		$this->bukaera_ordua = $this->hasiera_ordua + $this->iraupena * 1000 * 60; //minutuak gehitu milisegundutan 
		//$this->iraupena = $iraupena;
		$this->mezua = "Bozkaketa sortu berri : IRAUPENA " . $this->iraupena . " HASIERA ORDUA " .  $this->hasiera_ordua . " " . $this->bukaera_ordua ;
		//session_start();		
		//$_SESSION("hasiera_ordua") = $bestebat;
		$this->inprimatu_mezua();
	
	}

	//bozkaketa kontrolatzeko funtzioak
	private function bozkaketa_hasi(){
		
		$this->bozmartxan = True;
		
	}
	
	private function bozkaketa_hasita_ote(){
		return $this->bozmartxan;	
	}
	
	private function eman_bozkaketa_ordua(){
		return $this->bukaera_ordua;
	} 

	private function bozkaketa_ordua_ezarri($iraupena,$hasiera_ordua){
		//time  UNIXen timestamp a jaso  gero bezeroan konparatzeko
		$this->hasiera_ordua = $hasiera_ordua;
		$this->iraupena = $iraupena;
		echo $this->hasiera_ordua;
		//return $this->hasiera_ordua;
	
	}

	public function bozkaketa_bukatu(){
		$this->bozmartxan = False;
		$this->mezua = "Bozkaketa bukatu berri";
		$this->inprimatu_mezua();	
	}

	public function bozkaketa_egoera_ikusi(){
		return $this->bozmartxan;
	}	
	
	private function inprimatu_mezua(){
		echo $this->mezua;
	}
	
	function cors_hasieratu(){
		if (isset($_SERVER['HTTP_ORIGIN'])) {
        		header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        		header('Access-Control-Allow-Credentials: true');
        		header('Access-Control-Max-Age: 86400');    // cache for 1 day
		}

    		// Access-Control headers are received during OPTIONS requests
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
       				header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

    			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
       				header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    			exit(0);
		}
	}

	//zki bat sinpleki sortzeko-jasotzeko funtzioa
	private function sortu_kodea(){
    		return rand(1,10000);
	}
	public function gehitu_erabiltzailea($erab){
		//array batean hobe
		$this->erabiltzaileak = $this->erabiltzaileak . $erab;

	}
	public function eman_erabiltzaileak($erab){
		return $this->erabiltzaileak;
	}
	public function gorde_emaitzak(){
		echo "bozkaketa emaitzak datubasean gordetzeko?";
	}
}
	
//************************ PHP funtzioen bukaera **********************************//

?>
