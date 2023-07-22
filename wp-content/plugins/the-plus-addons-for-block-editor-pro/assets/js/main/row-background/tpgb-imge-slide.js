( function( $ ) {
    "use strict";
    
    $('.tpgb-section:not(.tpgb-section-editor),.tpgb-container-row:not(.tpgb-container-row-editor)').each(function(){
		var container = $(this).find('.tpgb-deep-layer');
        
        //Background Gallery Image
		if(container.hasClass("row-img-slide")){
            var gallery = container.find('.row-bg-slide').data('imgdata'),
            conId = container.attr('id'),
            option = container.find('.row-bg-slide').data('galleryopt');

            $('#'+conId+' .row-bg-slide').vegas({
                timer: false,
                transitionDuration: option.transduration,
                transition : option.transition,
                delay: option.duration,
                slides:   gallery ,
                animation: option.animation,
                overlay: option.textureoly,
            });
		}
    })
})(jQuery);