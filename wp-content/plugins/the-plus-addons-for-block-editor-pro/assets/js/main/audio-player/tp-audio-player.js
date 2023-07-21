(function ($) {
	"use strict";
	$('.tpgb-audio-player').each(function(){
		
		var $this = $(this);
		var id = $this.data("id");
		var song,cutime;
		var tracker = $('.'+id+' .tracker');
		var volume = $('.'+id+' .volume');
		var durationtime = $('.'+id+' .durationtime');
		var currenttime = $('.'+id+' .currenttime');
		var style = $this.data('style');
		var apvolume = $this.data('apvolume');

		function initAudio(elem,id) {
		
			var url = elem.attr('audiourl');
			var title = elem.text();
			var artist = elem.attr('artist');
			var thumb = elem.attr('data-thumb');
			if(style=='style-3'){
				$('.'+id+' .tpgb-player .trackimage img').attr('src',thumb);
			}			
			if(style=='style-4'){
				$('.'+id+' .tpgb-player').css('background','url('+ thumb +')').css('transition', 'background 0.5s linear');				
			}
			if(style=='style-5'){
				$('.'+id+' .tpgb-player .ap-st5-img').css('background','url('+ thumb +')').css('transition', 'background 0.5s linear');				
			}
			if(style=='style-6'){
				$('.'+id+' .tpgb-player .ap-st5-content').css('background','url('+ thumb +')').css('transition', 'background 0.5s linear');				
			}
			if(style=='style-8'){
				$('.'+id+' .tpgb-player-bg-img').css('background','url('+ thumb +')').css('background-size','cover').css('background-position','center center');
				$('.'+id+' .tpgb-player .trackimage img').attr('src',thumb);
			}
			if(style=='style-9'){
				$('.'+id+' .tpgb-player-bg-img').css('background','url('+ thumb +')').css('background-size','cover').css('background-position','center center');				
			}
			
			$('.'+id+' .tpgb-player .title').text(title);
			$('.'+id+' .tpgb-player .artist').text(artist);

			// song = new Audio('media/'+url);
			song = new Audio(url);
			// timeupdate event listener
			song.addEventListener('timeupdate', function() {
				var curtime = parseInt(song.currentTime,10);
				tracker.slider('value', curtime);
				cutime = curtime;
				UpdateSeek(cutime);
			});
			song.addEventListener('loadeddata', function playerLoadeddata(){
				durationtime.html(formatTime(song.duration));				
			}, true);
			
			$('.'+id+' .playlist .tpgb-playlist').removeClass('active');
			elem.addClass('active');
			songEnded();
		}

		function playAudio(id) {
			song.play();

			tracker.slider("option", "max", song.duration);

			$('.'+id+' .play').addClass('hidden');
			$('.'+id+' .pause').addClass('visible');
		}

		function stopAudio(id) {
			song.pause();

			$('.'+id+' .play').removeClass('hidden');
			$('.'+id+' .pause').removeClass('visible');
		}
		function UpdateSeek(a){
			currenttime.html(formatTime(a));
		}
		
		function songEnded(){
			song.addEventListener('ended', function() {
				var next = $('.'+id+' .playlist .tpgb-playlist.active').next();
				if (next.length == 0) {
					next = $('.'+id+' .playlist .tpgb-playlist:first-child');
				}
				initAudio(next,id);

				song.addEventListener('loadedmetadata', function() {
					playAudio(id);
					
				});

			}, false);
		}
		
		// play click
		$('.'+id+' .play').on('click',function(e) {
			e.preventDefault();
			// playAudio(id);

			tracker.slider("option", "max", song.duration);	
			
			song.play();
			$('.'+id+' .play').addClass('hidden');
			$('.'+id+' .pause').addClass('visible');

		});

		// pause click
		$('.'+id+' .pause').on('click',function(e) {
			e.preventDefault();
			stopAudio(id);
		});

		// next track
		$('.'+id+' .fwd').on('click',function(e) {
			e.preventDefault();
			stopAudio(id);

			var next = $('.'+id+' .playlist .tpgb-playlist.active').next();
			if (next.length === 0) {
				next = $('.'+id+' .playlist .tpgb-playlist:first-child');
			}
			initAudio(next,id);
			song.addEventListener('loadedmetadata', function() {
				playAudio(id);
			});
		});

		// prev track
		$('.'+id+' .rew').on('click',function(e) {
			e.preventDefault();
			stopAudio(id);

			var prev = $('.'+id+' .playlist .tpgb-playlist.active').prev();
			if (prev.length === 0) {
				prev = $('.'+id+' .playlist .tpgb-playlist:last-child');
			}
			initAudio(prev,id);

			song.addEventListener('loadedmetadata', function() {
				playAudio(id);
			});
		});

		// show playlist
		$('.'+id+' .playlistIcon').on('click',function(e) {
			e.preventDefault();
			$('.'+id+' .playlist').toggleClass('show');
		});

		$('.'+id+' .volumeIcon').on('click',function(e) {
			e.preventDefault();
			$('.'+id+' .tpgb-volume-bg').toggleClass('show');
		});

		// playlist elements - click
		$('.'+id+' .playlist .tpgb-playlist').on('click',function() {
			stopAudio(id);
			initAudio($(this),id);
			song.addEventListener('loadedmetadata', function() {
				playAudio(id);				
			});
		});

		// initialization - first element in playlist
		initAudio($('.'+id+' .playlist .tpgb-playlist:first-child'),id);

		//song.volume = 0.8;

		volume.slider({
			orientation: 'vertical',
			range: 'max',
			max: 100,
			min: 1,
			value: apvolume,
			start: function(event, ui) {},
			slide: function(event, ui) {
				song.volume = ui.value / 100;
			},
			stop: function(event, ui) {},
		});

		// empty tracker slider
		tracker.slider({
			range: 'min',
			min: 0,
			max: 100,
			value: 0,
			start: function(event, ui) {},
			slide: function(event, ui) {
				song.currentTime = ui.value;
			},
			stop: function(event, ui) {}
		});
		
		function formatTime(val) {
			var h = 0, m = 0, s;
			val = parseInt(val, 10);
			if (val > 60 * 60) {
				h = parseInt(val / (60 * 60), 10);
				val -= h * 60 * 60;
			}
			if (val > 60) {
				m = parseInt(val / 60, 10);
				val -= m * 60;
			}
			s = val;
			val = (h > 0)? h + ':' : '';
			val += (m > 0)? ((m < 10 && h > 0)? '0' : '') + m + ':' : '0:';
			val += ((s < 10)? '0' : '') + s;
			return val;
		}
	});
})(jQuery);