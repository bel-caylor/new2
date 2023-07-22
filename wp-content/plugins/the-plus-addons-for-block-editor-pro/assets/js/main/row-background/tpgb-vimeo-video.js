( function( $ ) {
    "use strict";
    
    $('.tpgb-section:not(.tpgb-section-editor),.tpgb-container-row:not(.tpgb-container-row-editor)').each(function(){
		var container = $(this).find('.tpgb-deep-layer');

		//Vimeo Video
		if(container.hasClass("tpgb-video-vimeo")){
			
			$('.tpgb-video-vimeo iframe').each(function() {
				var $self = $(this);

				if (window.addEventListener) {
					window.addEventListener('message', onMessageReceived, false);
				} else {
					window.attachEvent('onmessage', onMessageReceived, false);
				}
		
				function onMessageReceived(e) {
					if(e.origin==='https://player.vimeo.com'){
						var data = JSON.parse(e.data),
							id = $self.attr('id');
						
						switch (data.event) {
							case 'ready':
								$self[0].contentWindow.postMessage('{"method":"play", "value":1}', 'https://player.vimeo.com' );
								if($self.data('muted') && $self.data('muted') == '1') {
									$self[0].contentWindow.postMessage('{"method":"setVolume", "value":0}', 'https://player.vimeo.com' );
								}
								var videoHolder = document.getElementsByClassName('video-'+id);
								if(videoHolder){
									videoHolder[0].classList.remove('tp-loading');
								}
								break;
						}
					}
				}
			});
			
		}
    })
})(jQuery);