(function($) {
    "use strict";
    $(document).ready(function() {
        $('.tpgb-fancy-popup').each(function(){
            tpgb_fancy_popup($(this));
        });
    });
})(jQuery);

function tpgb_fancy_popup(obj) {
    jQuery(obj).off('click');
    jQuery(obj).on('click', function() {
        let src = jQuery(this).data('src');
        jQuery.fancybox.open({
            src  : src,
            afterShow : function( instance, current ) {
                if(instance.current && instance.current.$content){
                    let reDiv = instance.current.$content.find('.tpgb-gallery-list').find('.post-loop-inner');
                    
                    reDiv.isotope({
                        itemSelector: ".grid-item",
                        resizable: !0,
                        sortBy: "original-order",
                        resizesContainer: true,
                    });
                    reDiv.isotope('layout');
                    setTimeout(function(){
                        reDiv.isotope('layout');
                    }, 10);
                     
                }
            }
        } , { baseClass: "tpgb-button-fancy" , live: false } );
    });
}