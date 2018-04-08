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

function konektatu(){
	$zerbitzaria = "localhost";
	$erabiltzailea = "root";
	$pasahitza = "";
	$dbizena = "MusikaBoz";
	// Create connection
	$conn = new mysqli($zerbitzaria, $erabiltzailea, $pasahitza, $dbizena);
	// Check connection
	if ($conn->connect_errno) {
    		printf("Konexioak huts egin du: %s\n", $conn->connect_error);
    		exit();
	}
	return $conn;

}
function deskonektatu($conn){
	$conn->close();
}


//***********************************JSON planteamentua --> amaitu barik = probak!!!*************************+
//0.- JSON fitxategia datubaseko datuekin sortu (behar bada...> kontzeptua)
function sortu_JSON_DB_tik(){
	$sql="select * from abestiak limit 20"; 
	$response = array();
	$posts = array();
	$result=mysql_query($sql);
	while($row=mysql_fetch_array($result)) { 
		$title=$row['title']; 
		$url=$row['url']; 
		$posts[] = array('title'=> $title, 'url'=> $url);
	}	 

	$response['posts'] = $posts;

	$fp = fopen('abestizerrenda.json', 'w');
	fwrite($fp, json_encode($response));
	fclose($fp);

}

//1.- JSON fitxategiak dekodifikatua itzuli $fitx-en pasatzen zaiona
function Json_fitxategia_irakurri($fitx){
        $zerrenda = file_get_contents($fitx);
	//echo $zerrenda;
	$json_zerrenda = json_decode($zerrenda, true);
	return $json_zerrenda;
}

//2.- JSON dekodifikatua zeharkatu gako-balioak inprimatuz 
function zerrenda_sortu_eta_inprimatu_check($fitx){
	$jsona = Json_fitxategia_irakurri($fitx);
	foreach ($jsona as $gakoa => $balioa){
  		echo "<form id='bidaliabestiak'>";
  		echo "<div id='abestia' value=" . $balioa["iturria"] . ">";
  		//echo  $key . ':' . $value["izenburua"];
  		echo "<input type='checkbox' name='" . $balioa["izenburua"] . "' value='" . $balioa["iturria"] . "><div id='izenburua'>" . $balioa["izenburua"] . " " . $balioa["taldea"] . " " . $balioa["irudia"] . "</br>";
  		//echo "<div id='taldea'>" . $value["taldea"]. "</div>";
  		//echo "<div id='irudia'>" . $value["irudia"] . "</div>";
  		//echo "<div id='iturria'>" . $value["iturria"] . "</div>";
  		//echo "</div>";
  		echo "</br>";
	}
	echo "<input type='button' name='bidali' value='bidali' onclick='alert(kaixo);'/>";
	echo "</form>";
	return ("kaixo karapaixo, hau da zerrenda");
}

//3.- Abesti guztien zerrenda eskuratzeko funtzioa
function itzuli_denak(){
	//zerrenda_osoa_inprimatu
	//zerrenda_sortu_eta_inprimatu_check("abestizerrenda.json");
	$erantzuna = Json_fitxategia_irakurri("abestizerrenda.json");
	return $erantzuna;
}

//4.- Hautatutako abestien zerrenda eskuratzeko funtzioa
function itzuli_hautatuak(){
	//hautatuak_inprimatu
	//zerrenda_sortu_eta_inprimatu_check("abestihautatuak.json");
	$erantzuna = Json_fitxategia_irakurri("abestihautatuak.json");
	return $erantzuna;
}
//5.- Datubasean hautatutako abestiak erregistratu
function hautatu_abestiak($erab, $abestiak){
	//kontrola, ia erabiltzaileak iada bozkatu duen edo ez
       $fp = fopen('abestihautatuak.json', 'w');
	fwrite($fp, json_encode($abestiak));
	fclose($fp);
	
}
//************************************datubase planteamentua****************hau dago martxan!***************************+
//eman_hautapen_zerrenda.php
//1.- datubasean dauden abesti guztiak inprimatzeko funtzioa
function begiratu_datubasean(){
        $bistaratu_zerrenda = " ";
	$conn = konektatu();
        //$bozkakodea = sortu_kodea();
	$sql = "SELECT * from abestiak";
        $result = $conn->query($sql);
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
	deskonektatu($conn);
}
//eman_hautatuen_zerrenda.php
//2.-datubasean hautatuta daudenak inprimatzeko funtzioa
function begiratu_datubasean_hautatuak(){
        $bistaratu_zerrenda = "<ul> ";
	$conn = konektatu();
        //$bozkakodea = sortu_kodea();
	$sql = "SELECT abestiak.abestia,abestiak.taldea,abestiak.irudia, count(bozkak.bozka_kop) as konta from abestiak, bozkak where abestiak.ab_id=bozkak.ab_id group by abestiak.abestia order by konta desc;";
        $result = $conn->query($sql);
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
	deskonektatu($conn);
}

//ikusi_erreprodukzio_zerrenda.php
//3.- playlist-a bistaratzeko funtzioa

function bistaratu_playlist2(){
        $bistaratu_zerrenda = "<ul id='playlist'>";
	$irudia = "https://openclipart.org/image/2400px/svg_to_png/130039/Music-icon.png";			
	$conn = konektatu();
        //$bozkakodea = sortu_kodea();
	$sql = "SELECT abestiak.abestia,abestiak.taldea,abestiak.irudia,abestiak.iturria, count(bozkak.bozka_kop) as konta from abestiak, bozkak where abestiak.ab_id=bozkak.ab_id group by abestiak.abestia order by konta desc;";
        $result = $conn->query($sql);
	if ($result->num_rows > 0){
        	while($row = mysqli_fetch_assoc($result)){
			$bistaratu_zerrenda = $bistaratu_zerrenda . "<li class='active'><img width='50px' height='50px' src='" . $row["irudia"] . "'/><a href='" . $row["iturria"] . "'><div id='testua'>" . $row["taldea"] . " " .  $row["abestia"] . "</a></div><div id='ab'> BOZKAK:" . $row["konta"] . "/div></li></br>"; //. "BOZKAK: " . (string)row["konta"] .
		}	
	}
	$bistaratu_zerrenda = $bistaratu_zerrenda . "</ul>";
	echo $bistaratu_zerrenda;
	/* free result set */
        $result->free();
	deskonektatu($conn);

}
function bistaratu_playlist(){
        $bistaratu_zerrenda = "<source src='";
	$irudia = "https://openclipart.org/image/2400px/svg_to_png/130039/Music-icon.png";			
	$conn = konektatu();
        //$bozkakodea = sortu_kodea();
	$sql = "SELECT abestiak.abestia,abestiak.taldea,abestiak.irudia,abestiak.iturria, count(bozkak.bozka_kop) as konta from abestiak, bozkak where abestiak.ab_id=bozkak.ab_id group by abestiak.abestia order by konta desc LIMIT 10;";
        $result = $conn->query($sql);
	$i = 1;
	if ($result->num_rows > 0){
        	while($row = mysqli_fetch_assoc($result)){
			if ($i == 1){
				$bistaratu_zerrenda = $bistaratu_zerrenda . $row["iturria"] . "'/></audio>";
			        $bistaratu_zerrenda = $bistaratu_zerrenda . "<ul id='playlist'>";
				
			}	
					
				$bistaratu_zerrenda = $bistaratu_zerrenda . "<li class='nireliplay' id='abestia" . $i . "'> <div id='zkia'> ". $i ."</div> <img class='iru' id='iru" . $i ."'  src='" . $row["irudia"] . "'/><a href='" . $row["iturria"] . "'><div id='taldea'>" . $row["taldea"] . "</div><div id='abes'> " .  $row["abestia"] . "</div></a> <div id='bozkalist'>" . $row["konta"] . "</div></li>";
			$i += 1;	
		}	
	}
	$bistaratu_zerrenda = $bistaratu_zerrenda . "</ul>";
	echo $bistaratu_zerrenda;
	/* free result set */
        $result->free();
	deskonektatu($conn);

}
//bidali_hautatuak.php
//4.- Aukerak gordetzeko funtzioak
//hautapen 1 gordetzeko funtzioa
function gorde_datubasean_hautapena($info,$conn){

	//$conn = konektatu();

	//$sql = "INSERT INTO bozkak (ab_id,bozka_kop) VALUES (" . $info[0] . ", 1)";
	$sql = "INSERT INTO bozkak (ab_id,bozka_kop) VALUES (" . $info . ", 1)";
	if ($conn->query($sql) == TRUE) {
    		echo "Zure bozka datubasean ondo erregistratu da";
	} else {
    		echo "Error: " . $sql . "<br>" . $conn->error;
	}

	//deskonektatu($conn);
}
//5 hautapen gordetzeko funtzioa
function gorde_datubasean_hautapenak($info){

	$conn = konektatu();
        //$bozkakodea = sortu_kodea();
	//begiratu iada erregistratuta dagoen edo ez
	foreach($info as $item) {
		$item = (string)$item;
		gorde_datubasean_hautapena($item,$conn);
	}
	deskonektatu($conn);	
}



//kargatu_datubasea_abestiz.php
//5.- Datubasea abestiz KARGATZEKO funtzioak
//https://desenvolupant.wordpress.com/2011/04/23/recorrer-carpetas-con-glob-en-php/
//https://ranacse05.wordpress.com/2010/03/15/read-mp3-tags-with-php/

function begiratu_info_mp3($file){
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

function kargatu_datubasea_abestiz(){
	//taulak husteko funtzioa probatuz
	hustu_taulak();
	$conn = konektatu();
	$dir = "/opt/lampp/htdocs/musikaboz/musika/*";

	// fitxategiak zerrendatu
	$i = 1;
	foreach(glob($dir) as $file) {
		//abestiaren informazioa inprimatu>
		echo "<b>[*] Fitxategia: $file : filetype: " . filetype($file) . "</b><br />";
		echo "[**] abestia db-an gordetzen";
		$abestia = begiratu_info_mp3($file)[0];
		$taldea = begiratu_info_mp3($file)[1];
		$fitxizena = basename($file);
		//irudia bilatu 1.0 > nahiko flojoa, xaukena xauk
		if (topatu_irudia_bing($taldea) != "" ){
			//echo "bilaketa1";
			$katea = $abestia . " " . $taldea;		
			$irudia = topatu_irudia_bing($katea);
		}else{
			//echo "bilaketa2";
			$katea = $taldea;
			$irudia = topatu_irudia_bing($katea);
		}
		if (stripos($irudia,".jpg") == false) {
			$irudia = "https://openclipart.org/image/2400px/svg_to_png/130039/Music-icon.png";
		}
		//hemen ere aldatu behar bidea!!
		$bidea = "http://192.168.0.158/musikaboz/musika/" . $fitxizena;
		$sql = "INSERT INTO abestiak (ab_id, mota, taldea, abestia, iturria, irudia) VALUES ("  . $i . ",'lokala','" . $taldea . "','" . $abestia . "','" . $bidea . "','" . $irudia . "');";
		$i = $i + 1 ;
		if ($conn->query($sql) == TRUE) {	
			echo "[>] ondo erregistratu da abestia datubasean</br>";	
		} else {
    			echo "[!]Errorea: " . $sql . "<br>" . $conn->error;
		}
   
	}
	deskonektatu($conn);
}
//DATUBASEA SORTZEKO FUNTZIOAK *** etorkizunean
//DATUBASEKO TAULAK HUSTEKO FUNTZIOAK 
function hustu_taulak(){
	$conn = konektatu();
	hustu_taula("bozkak",$conn);
	hustu_taula("abestiak",$conn);	
	deskonektatu($conn);
	echo "taulak ondo hustu dira";
	
}
//taula bat husteko funtzioa
function hustu_taula($taula, $conn){
	$sql = "DELETE from " . $taula . " WHERE 1;";
	if ($conn->query($sql) == TRUE) {
    		echo "Taula hustu egin da";
	} else {
    		echo "Error: " . $sql . "<br>" . $conn->error;
	}
}
//bozkak taula husteko funtzioa
function hustu_bozkak($conn){
	hustu_taula("bozkak",$conn);
}
function sortu_kodea(){
    return rand(1,10000);
}
//abestiei iruditxo bat topatzeko funtzioa 1.0
//https://stackoverflow.com/questions/16855957/return-google-image-search-results-in-html-using-php
function topatu_irudia_bing($testua){

	$url = "http://www.bing.com/images/search?".urlencode(strtolower($testua))."&count=1&q=".urlencode($testua);
	$data=file_get_contents($url);
	$emaitza = (string)$data;
	$emaitza = htmlspecialchars($emaitza);

	//topatu beharreko katea hasieran konplikatu gabe,  topatzen duen lehen .jpg -a!
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
		echo "<img src='" . $emaitza4 . "' witdh='100px' height='100px'/>";
	}else{
		echo 'False';
	}
	return $emaitza4;	
}
//************************ erabiltzaileen kontrolerako funtzioak******************************************/

function izena_eman($erab,$pass){
	//konektatu
	$conn = konektatu();
	//datubasean bilaketa egin
	$sql = "select erab from erabiltzaileak where erab='" . $erab . "' and pass='" . $pass . "';";
	$result = $conn->query($sql);
	$row = mysqli_fetch_assoc($result);
	$erantzuna = $row['erab'];
	//existitzen den edo ez frogatu	
	if ($erantzuna != ""){
		return $erantzuna;			
	}else{
		return "wtf";
	} 	
	$result->free();
	deskonektatu($conn);
}
//begiratu ia erabiltzailea existitzen den edo ez 
function begiratu_erab($erab){
	$conn = konektatu();
	//datubasean bilaketa egin
	$sql = "select erab from erabiltzaileak where erab='" . $erab . "';";
	$result = $conn->query($sql);
	$row = mysqli_fetch_assoc($result);
	$erantzuna = $row['erab'];
	//existitzen den edo ez frogatu	
	if ($erantzuna != ""){
		return $erantzuna;			
	}else{
		return "wtf";
	} 	
	$result->free();
	deskonektatu($conn);
}

//begiratu erabiltzaileak dagoeneko bozkatu duen edo ez TRUE: BOZKATU DU DAGOENEKO FALSE: EZ DU BOZKATU ORAINDIK
function begiratu_bozkatuta($erab){
	$conn = konektatu();
	$sql = "select bozkatua from bozkatuta where erab='" . $erab . "';"; 
	$result = $conn->query($sql);
	$row = mysqli_fetch_assoc($result);
	$erantzuna = $row['bozkatua'];
	//existitzen den edo ez frogatu	
	if ($erantzuna == ""){
		return 'False';			
	}else{
		return $erantzuna;
	} 	
	$result->free();
	deskonektatu($conn);
}
function erregistratu_bozkatuta($erab){
	$conn = konektatu();
	if (!begiratu_bozkatuta($erab)){
		$sql  = "Insert into bozkatuta(erab,bozkatua) values ('" . $erab . "', 'True')";		
		$result = $conn->query($sql);	
		return true;	
	}else{
		return false;
	}
	$result->free();
	deskonektatu($conn);
}

//$noiz ="2018-05-01";
//$denbora = 60;
//hasi_kontatzen($noiz, $denbora);
function hasi_kontatzen($noiz, $denbora){
	//mysql-formatua	
	$today = date("Y-m-d H:i:s");
	echo $today;

}

function eman_denbora($denbora){
	echo $denbora;
}
//************************ PHP funtzioen bukaera **********************************//

?>
