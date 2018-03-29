<html>
<head><style>
#zkia{
   border-radius:15px !important;
   padding:5px !important; 
   margin-top:10px;
   background-color:orange;
   color:white;
   float:left;
   margin-left:10px;
   margin-right:5px;
   min-width:20px;
   text-align:center;

	}
#info {
    float: left;
    vertical-align: middle;
    padding: 5px;
    background:orange;
    color:white;
    border:1px solid black;
    border-radius:20px;
    margin-right:5px;
    margin-left:5px;
    border:1px solid #333;
    box-shadow:1px 1px 2px black;
    min-width:20px;
    text-align:center;
}
#nirekontrolak {
    background: #333;
    width: 100%;
    color: white;
    padding:10px;
    


}
audio{
    width:100%;
}
#aurrekoa{
   background: #404040;
   padding: 5px;
   color:white;
   font-weight:bold;
}
#hurrengoa{
   background: #404040;
   padding:5px;
   color:white;
   font-weight:bold;
}
.active{
   background: #404040 !important;
   padding:5px;
   color:white;
   font-weight:bold;
   border:2px solid orange !important;
}
.nireliplay{
	background:#333;
	text-decoration:none !important;
	border-bottom:1px solid #404040;
   
}
.nireliplay a{
   color:white !important;
   text-decoration:none !important;
   margin-bottom:30px !important;
   padding-left:10px;
   font-size:12px;
   text-shadow:1px 1px 3px black;	
}
#bozkalist{
   float: right;
   color: white;
   text-shadow:1px 1px 1px black;
   background: orange;
   padding: 5px;
   border:1px solid black;
   box-shadow:1px 1px 2px black;
}
ul{
	list-style:none;
}
#edukigehiago{

float:right;
background:#333;
min-height:50px;
min-width:700px;
padding-bottom:-10px;
color:white;
text-shadow:1px 1px 1px black;
}

#taldea {
    float: left;
    vertical-align: middle !important;
    padding-top:18px;
    padding-left: 18px;
    padding-right: 10px;
    min-width:180px;
}
#abes {
    float: left;
    vertical-align: middle !important;
    padding-top: 18px;
    padding-left: 18px;
    padding-right:10px;
    color:orange;
    min-width:180px;
}
.iru{
    width:50px;
    height:50px;
    
}
#infoplay{

   border-radius:15px !important;
   padding:5px !important; 
   margin-top:2px;
   background-color:darkgrey;
   color:white;
   float:left;
   margin-left:20px;
   margin-right:20px;
   min-width:400px;
   text-align:center;

}
</style></head><body>
<div id="nirekontrolak"><div id="info">1</div>
<input type="button" value="[<]" id="aurrekoa"  />
<input type="button" value="[>]" id="hurrengoa"  /><div id="infoplay"></div></div>

<!--http://devblog.lastrose.com/html5-audio-video-playlist/-->
<audio id='audio' preload='auto' tabindex='0' controls='' >
  <div id="rz"></div>
</body>
<?php
include 'zerbitzari_funtzioak.php';

bistaratu_playlist();
?>
<script>
$(document).ready(function () {
	init();
	function init(){
		var current = 0;
		var audio = $('#audio');
		var playlist = $('#playlist');
		var info = $('#info');
		var infoplay =$('#infoplay');
		var tracks = playlist.find('li a');
		var len = tracks.length - 1;
		audio[0].volume = .50;
		audio[0].play();
		inpzkia = current + 1; 
		inprimatuplay(infoplay,inpzkia);
		playlist.on('click','a', function(e){
			e.preventDefault();
			link = $(this);
			current = link.parent().index();
			inpzkia = current + 1;
			info.html(inpzkia);
			inprimatuplay(infoplay, inpzkia);
			run(link, audio[0]);
		});

		audio[0].addEventListener('ended',function(e){
			current++;
			if(current == len){
				current = 0;
				link = playlist.find('a')[0];
			}else{
				link = playlist.find('a')[current];    
			}
			inpzkia = current + 1;
			info.html(inpzkia);
			inprimatuplay(infoplay, inpzkia);
			run($(link),audio[0]);
		});
		hurrengoa.addEventListener('click',function(e){
			current++;
			if(current == len + 1){
				current = 0;
				link = playlist.find('a')[0];
			}else{
				link = playlist.find('a')[current];    
			}
			inpzkia = current + 1;
			info.html(inpzkia);
			inprimatuplay(infoplay, inpzkia);
			run($(link),audio[0]);		
		});
		aurrekoa.addEventListener('click',function(e){
			current--;
			if(current == -1){
				current = len;
				link = playlist.find('a')[len];
			}else{
				link = playlist.find('a')[current];    
			}
			inpzkia = current + 1;
			info.html(inpzkia);
			inprimatuplay(infoplay, inpzkia);
			run($(link),audio[0]);		
		});
		
	}
	function run(link, player){
			player.src = link.attr('href');
			par = link.parent();
			par.addClass('active').siblings().removeClass('active');
			player.load();
			player.play();
			info.html("0");
	}

	//Orrialdetik abestiari dagokion errenkada eskuratzeko funtzioa
	function eraikiab(current){
		abestia = "abestia" + current;	
		return abestia;
	}
	function eraikiiru(current){
		irudia = "iru" + current;
		return irudia;
	}
	
	function inprimatuplay(infoplay, inpzkia){
			infoplay.html("");
			ab = eraikiab(inpzkia);
			ir = eraikiiru(inpzkia);
			nireinfo1 = $('#' + ab + ' a #taldea' ).html();
			nireinfo2 = $('#' + ab + ' a #abes' ).html();
			nireinfo3 = $('#' + ir).html();
			infoplay.html('<b>' + nireinfo2 + '</b> ' + nireinfo1 + ' ' + nireinfo3 );
	
	}

});

</script>
