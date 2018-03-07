<?php
include 'zerbitzari_funtzioak.php';

//informazioa POST moduan jaso :(
$nirealdagai = $_POST["informazioa"];
//$inprimatzeko = (string)$nirealdagai;
//echo "kaixo zerbitzaritik, bidalitako aldagaia: " . $inprimatzeko;
gorde_datubasean_hautapenak($nirealdagai);

?> 

