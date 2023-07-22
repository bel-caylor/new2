( function( $ ) {
    "use strict";
    
    $('.tpgb-section:not(.tpgb-section-editor),.tpgb-container-row:not(.tpgb-container-row-editor)').each(function(){
		var container = $(this).find('.tpgb-deep-layer'),
			middlecls = $(this).find('.tpgb-middle-layer');

        if(container.hasClass("columns_animated_bg") || middlecls.hasClass("tpgb-automove-img") ){
            $('.columns_animated_bg,.tpgb-automove-img .tpgb-parlximg-wrap').each(function() {
                var $self = $(this),
                dir = $self.data('direction'),
                speed = 100 - $self.data('trasition'),
                coords = 0;
                
                setInterval(function() {
                    if(dir == 'left' || dir == 'bottom')
                    coords -= 1;
                    else
                    coords += 1;
                    if(dir == 'left' || dir == 'right')
                    $self.css('backgroundPosition', coords +'px 50%');
                    else
                    $self.css('backgroundPosition', '50% '+ coords + 'px');
                }, speed);
            });
        }
    })
})(jQuery);