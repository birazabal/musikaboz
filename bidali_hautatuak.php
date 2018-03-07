<?php
include 'zerbitzari_funtzioak.php';

//informazioa POST moduan jaso :(
$nirealdagai = $_POST["informazioa"];

//kontrolak
//3 bozka izan behar dira derrigorrez
if (count($nirealdagai) == 3){
	gorde_datubasean_hautapenak($nirealdagai);
	echo "Zerbitzaritik, aukeratutako abestiak: " . $nirealdagai[0] . ", " . $nirealdagai[1] . ", " . $nirealdagai[2] . " dira";		
}else{
	echo "3 abesti aukeratu behar dira derrigorrez";

}
?> 

