(function($) {
    "use strict";
        $('.tpgb-gallery-list.gallery-style-3 .grid-item').each( function() {  $(this).hoverdir(); } );
       
        $('.tpgb-gallery-list').each( function() {
            var e = $(this),
                BoxID = e.data("id"),
                Setting = e.data("fancy-option");
                                
            $('[data-fancybox="'+BoxID+'"]').fancybox({
                buttons : Setting.button,
                image: {
                    preload: true
                },

                loop: Setting.loop,
                infobar: Setting.infobar,
                animationEffect:  Setting.animationEffect,
                animationDuration: Setting.animationDuration,
                transitionEffect: Setting.transitionEffect,
                transitionDuration: Setting.transitionDuration,
                arrows: Setting.arrows,

                //false, close, next, nextOrClose, toggleControls, zoom
                clickContent:'next',
                clickSlide:'close',
                dblclickContent: false,
                dblclickSlide: false,

            });
        
            if(e.hasClass('gallery-style-2')){
                $('.gallery-style-2 .tpgb-gallery-list-content').on('mouseenter',function() {
                    $(this).find(".post-hover-content").slideDown(300)
                });
                $('.gallery-style-2 .tpgb-gallery-list-content').on('mouseleave',function() {
                    $(this).find(".post-hover-content").slideUp(300)
                });
            }

        });
})(jQuery);