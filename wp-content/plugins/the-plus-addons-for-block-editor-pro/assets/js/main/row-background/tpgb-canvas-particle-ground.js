( function( $ ) {
    "use strict";
    
    $('.tpgb-section:not(.tpgb-section-editor),.tpgb-container-row:not(.tpgb-container-row-editor)').each(function(){
		var middlecls = $(this).find('.tpgb-middle-layer'),
            uid = $(this).data('id');

        if(middlecls.hasClass('canvas-style-4'+uid+'')){
            if($('#canvas-style-4'+uid+'').length){
                var can4_color = middlecls.attr('data-color');
                
                $(".canvas-style-4"+uid+"").particleground({
                    minSpeedX: 0.1,
                    maxSpeedX: 0.3,
                    minSpeedY: 0.1,
                    maxSpeedY: 0.3,
                    directionX: "center",
                    directionY: "up",
                    density: 10000,
                    dotColor: can4_color,
                    lineColor: can4_color,
                    particleRadius: 7,
                    lineWidth: 1,
                    curvedLines: false,
                    proximity: 100,
                    parallax: true,
                    parallaxMultiplier: 5,
                    onInit: function() {},
                    onDestroy: function() {}
                });
                
            }			
        }
    })
})(jQuery);