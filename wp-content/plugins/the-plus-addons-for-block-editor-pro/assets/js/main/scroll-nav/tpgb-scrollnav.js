( function( $ ) {
    "use strict";
	$('.tpgb-scroll-nav').each(function(){
		var scroll_nav = $(this);
		
        $(".tpgb-scroll-nav-item").mPageScroll2id({
            highlightSelector : ".tpgb-scroll-nav-item",
            highlightClass : "active",
        });

        $(".tpgb-scroll-nav-item").on('click',function(e){
            e.preventDefault();
            $(scroll_nav).find(">.tpgb-scroll-nav-inner .tpgb-scroll-nav-item").removeClass('active').addClass('inactive');
            $(this).addClass('active').removeClass('inactive');
        });
			

		var container = $('.tpgb-scroll-nav.scroll-view');
		var container_scroll_view = $('.tpgb-scroll-nav-inner');
		if(container.length > 0 && container_scroll_view){
			$(window).on('scroll', function() {
				var scroll = $(this).scrollTop();
				container.each(function () {
					var scroll_view_value = $(this).data("scroll-view");
					if (scroll > scroll_view_value) {
						container.addClass('show');
					}else {
						container.removeClass('show');
					}
					
				});
			});	
		}
		
	});
})(jQuery);