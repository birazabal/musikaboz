<html>
<head></head><body>
<input type='button' value='hurrengoa' id='hurrengoa'  />
<input type='button' value='aurrekoa' id='aurrekoa'  />
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
		var tracks = playlist.find('li a');
		var len = tracks.length - 1;
		audio[0].volume = .50;
		audio[0].play();
		playlist.on('click','a', function(e){
			e.preventDefault();
			link = $(this);
			current = link.parent().index();
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
			run($(link),audio[0]);
		});
		hurrengoa.addEventListener('click',function(e){
			current++;
			if(current == len){
				current = 0;
				link = playlist.find('a')[0];
			}else{
				link = playlist.find('a')[current];    
			}
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
			run($(link),audio[0]);		
		});
		
	}
	function run(link, player){
			player.src = link.attr('href');
			par = link.parent();
			par.addClass('active').siblings().removeClass('active');
			player.load();
			player.play();
	}
});

</script>
