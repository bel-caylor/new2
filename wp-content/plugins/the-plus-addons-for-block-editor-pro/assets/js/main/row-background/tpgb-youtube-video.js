( function( $ ) {
    "use strict";
    
    $('.tpgb-section:not(.tpgb-section-editor),.tpgb-container-row:not(.tpgb-container-row-editor)').each(function(){
		var container = $(this).find('.tpgb-deep-layer');

        if(container.hasClass("tpgb-video-youtube")){

			var tag = document.createElement('script');
			tag.src = "//www.youtube.com/iframe_api";
			var firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
			
			var players = {};

			window.onYouTubeIframeAPIReady = function() {
				$('.tpgb-video-youtube iframe').each(function() {
					var $self = $(this),
						id = $self.attr('id');
						players[id] = new YT.Player(id, {   
							   playerVars: {autoplay:1},    
							events: {
							   onReady: function(e) {
								   
							  	if($self.data('muted') && $self.data('muted') == '1') {
								  e.target.mute();
							   	}
								  e.target.playVideo();
							   },
							   onStateChange: function(e) {
									if(e && e.data === 1){
										var videoHolder = document.getElementsByClassName('video-'+id);
										if(videoHolder){
											videoHolder[0].classList.remove('tp-loading');
										}
									}else if(e && e.data === 0){
										e.target.playVideo()
									}
								}
							},
							
						});
					
				});
			};
		}
    })
})(jQuery);