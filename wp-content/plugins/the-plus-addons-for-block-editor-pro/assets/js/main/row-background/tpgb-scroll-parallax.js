( function( $ ) {
    "use strict";
    
    $('.tpgb-section:not(.tpgb-section-editor),.tpgb-container-row:not(.tpgb-container-row-editor)').each(function(){

        if($(this).hasClass("tpgb-scroll-parallax")){

            var controller = new ScrollMagic.Controller();
            $('.tpgb-scroll-parallax').each(function(index, elem){
                
                var $bcg =  $(elem).find('.img-scroll-parallax');
                
                var slideParallaxScene = new ScrollMagic.Scene({
                    triggerElement: elem,
                    triggerHook: 1,
                    duration: "200%"
                })
                .setTween(TweenMax.fromTo($bcg, 1, {backgroundPositionY: '15%', ease: "Power0.easeNone"},{backgroundPositionY: '85%', ease:"Power0.easeNone"}))
                .addTo(controller);
            })
        }
    });
})(jQuery);