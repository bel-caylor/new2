( function( $ ) {
    "use strict";
    
    $('.tpgb-section:not(.tpgb-section-editor),.tpgb-container-row:not(.tpgb-container-row-editor)').each(function(){
		var container = $(this).find('.tpgb-deep-layer');

		if(container.find("video.self-hosted-video, .tpgb-iframe").length){
			setTimeout(function(){
			$('video.self-hosted-video, .tpgb-iframe').tpgb_VideoBgInit();
				$('.self-hosted-video').each(function() {
					var $self=$(this);
                    const promise = $self[0].play();
                    if(promise !== undefined){
                        promise.then(() => {
                        }).catch(() => {
                            $self[0].muted = true;
                            $self[0].play()
                        });
                    }
				});
			}, 100);
			$.fn.tpgb_VideoBgInit = function() {
				return this.each(function() {
					var $self = $(this),
						ratio = 1.778,
						pWidth = $self.parent().width(),
						pHeight = $self.parent().height(),
						selfWidth,
						selfHeight;
					var setSizes = function() {
						if(pWidth / ratio < pHeight) {
							selfWidth = Math.ceil(pHeight * ratio);
							selfHeight = pHeight;
							$self.css({
								'width': selfWidth,
								'height': selfHeight
							});
						} else {
							selfWidth = pWidth;
							selfHeight = Math.ceil(pWidth / ratio);
							$self.css({
								'width': selfWidth,
								'height': selfHeight
							});
						}
					};				
					setSizes();
					$(window).on('resize', setSizes);
				});
			}
		}
    })
})(jQuery);