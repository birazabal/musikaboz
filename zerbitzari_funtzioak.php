<?php
//zerbitzariaren funtzio nagusiak biltzen
//************************************PHP FUNTZIOEN HASIERA**********************************
//datubaserako funtzio orokorrak 1.0 konektatu eta deskonektatu
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

function cors_hasieratu(){
//goiburuen hasieratzea, informazio trukatzea ahalbideratzeko (nire ordenagailuan gutxienez xD)
header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");

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
			$lerroa = "<input type='checkbox' name='" . $row["ab_id"] . "' id='" . $row["ab_id"] . "' value ='" . $row["ab_id"]  . "' ><b>" . $row["abestia"] . "</b> " . $row["taldea"] . "</br>";  
			$bistaratu_zerrenda = $bistaratu_zerrenda . $lerroa;
		}	
	}
	$bistaratu_zerrenda = $bistaratu_zerrenda . "<input type='button' name='bidali' value='bidali' onclick='bidali_hautatutakoak()'/></form>";
	echo $bistaratu_zerrenda;
	/* free result set */
        $result->free();
	deskonektatu($conn);
}
//eman_hautatuen_zerrenda.php
//2.-datubasean hautatuta daudenak inprimatzeko funtzioa
function begiratu_datubasean_hautatuak(){
        $bistaratu_zerrenda = " ";
	$conn = konektatu();
        //$bozkakodea = sortu_kodea();
	$sql = "SELECT abestiak.abestia,abestiak.taldea,abestiak.irudia, count(bozkak.bozka_kop) as konta from abestiak, bozkak where abestiak.ab_id=bozkak.ab_id group by abestiak.abestia;";
        $result = $conn->query($sql);
	if ($result->num_rows > 0){
        	while($row = mysqli_fetch_assoc($result)){
			$bistaratu_zerrenda = $bistaratu_zerrenda . "<div id='lerroa'><b>" . $row["abestia"] . "</b> " . $row["taldea"] . "<b> BOZKAK: " .  $row["konta"] . "</b> <div id='lerroirudia'><img width='50px' height='50px' src='" . $row["irudia"] . "'/></div></div></br>";
		}	
	}

	echo $bistaratu_zerrenda;
	/* free result set */
        $result->free();
	deskonektatu($conn);
}

//ikusi_erreprodukzio_zerrenda.php
//3.- playlist-a bistaratzeko funtzioa

function bistaratu_playlist(){
        $bistaratu_zerrenda = "<ul id='playlist'>";
	$conn = konektatu();
        //$bozkakodea = sortu_kodea();
	$sql = "SELECT abestiak.abestia,abestiak.taldea,abestiak.irudia,abestiak.iturria, count(bozkak.bozka_kop) as konta from abestiak, bozkak where abestiak.ab_id=bozkak.ab_id group by abestiak.abestia;";
        $result = $conn->query($sql);
	if ($result->num_rows > 0){
        	while($row = mysqli_fetch_assoc($result)){
			$bistaratu_zerrenda = $bistaratu_zerrenda . "<li class='active'><a href='" . $row["iturria"] . "'>" . $row["taldea"] . " " .  $row["abestia"] . "</a> BOZKAK:" . $row["konta"] . "</li>"; //. "BOZKAK: " . (string)row["konta"] .
		}	
	}
	$bistaratu_zerrenda = $bistaratu_zerrenda . "</ul>";
	echo $bistaratu_zerrenda;
	/* free result set */
        $result->free();
	deskonektatu($conn);

}
//bidali_hautatuak.php
//4.- Aukerak gordetzeko funtzioa
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
		$bidea = "http://localhost/musikaboz/musika/" . $fitxizena;
		$sql = "INSERT INTO abestiak (ab_id, mota, taldea, abestia, iturria, irudia) VALUES ("  . $i . ",'lokala','" . $taldea . "','" . $abestia . "','" . $bidea . "','irudiarenURL');";
		$i = $i + 1 ;
		if ($conn->query($sql) == TRUE) {	
			echo "[>] ondo erregistratu da abestia datubasean</br>";	
		} else {
    			echo "[!]Errorea: " . $sql . "<br>" . $conn->error;
		}
   
	}
	deskonektatu($conn);
}
function sortu_kodea(){
    return rand(1,10000);
}
//************************ PHP funtzioen bukaera **********************************//

?>
