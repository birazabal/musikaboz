<?php
//erabiltzailearen datuak bistaratzeko PHP funtzioa
function bistaratu_erabiltzaile_datuak(){
	session_start();
	echo $_SESSION["izena"];
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/jquery.mobile-1.4.5.min.css">
<script src="js/jquery-1.11.3.min.js"></script>
<script src="js/jquery.mobile-1.4.5.min.js"></script>
<script src="js/musikaboz.js"></script>

</head>
<body>

<div data-role="page" id="nagusia">
<!-- PANELA: Ezkerreko menua ikusteko (revel et left ;) ) -->
    <div data-role="panel" id="ezkerMenua" data-position="left" data-display="reveal" data-theme="b">
        <ul data-role="listview" data-inset="true">
            <!-- esteka bakoitzak bere funtzioari deitu behar dio eta bista-n kargatu, hau da, bista bakarra -->
            <li><a href="#" onclick="eskatu_hautapen_zerrenda()">
                <img src="css/images/icons-png/bafle.png" background="black">
                <h2>Bozkatu</h2>
                <p>3 abesti</p></a>
            </li>
            <li><a href="#" onclick="eskatu_hautatuen_zerrenda()">
                <img src="css/images/icons-png/viniloa.png">
                <h2>Bozkatuak</h2>
                <p>zerrenda</p></a>
            </li>
            <li><a href="#" onclick="playerra_bistaratu()">
                <img src="css/images/icons-png/play.png">
                <h2>Top 10</h2>
                <p>10 bozkatuenak</p></a>
            </li>
	   
        
        </ul>
        
    </div>
<!-- PANELAren bukaera -->
  <div data-role="header" data-theme="b">

    <h1>Musikaboz</h1><div id="denbora" class="ui-btn-right"></div>
   <a  data-icon="gear" class="ui-btn-left" href="#ezkerMenua">Menua</a>
    </div>

  <div data-role="main" class="ui-content">
   
       <div id="bistaraketaEremua">
<center><img src="css/images/icons-png/Music-Equalizer.png"/></center>
	</div>
  </div>

  <div data-role="footer" data-position="fixed" data-theme="b">
    <h1>Erabiltzailea: <div id="erabinfo">
<?php 
bistaratu_erabiltzaile_datuak();
?></div></h1>
  </div>
</div> 

</body>
</html>
