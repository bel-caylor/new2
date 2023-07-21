/* Mobile Menu JS */
(function($) {
	"use strict";
	$(document).ready(function(){
		$('.tpgb-mobile-menu').each(function(){	
			let $container = $(this),
				mmOpt = $container.data('mm-option'),
				screenWidth = screen.width;
			if($('.tpgb-mm-wrapper.swiper-container,.tpgb-mm-l-wrapper.swiper-container,.tpgb-mm-r-wrapper.swiper-container').length > 0){
				new Swiper(".tpgb-mm-wrapper.swiper-container,.tpgb-mm-l-wrapper.swiper-container,.tpgb-mm-r-wrapper.swiper-container",{
					slidesPerView: "auto",
					mousewheelControl: !0,
					freeMode: !0
				});
			}
			if($('.tpgb-mobile-menu.tpet-on').length > 0){
				$(".tpgb-mm-et-link").on( "click", function(e) {
					e.preventDefault();
					$(this).closest(".tpgb-mobile-menu").find('.header-extra-toggle-content').addClass("open");
					$(this).closest(".tpgb-mobile-menu").find('.extra-toggle-content-overlay').addClass('open');
				});
				$('.extra-toggle-close-menu').on("click", function(e) {
					e.preventDefault();
					$(this).closest(".tpgb-mobile-menu").find('.header-extra-toggle-content').removeClass("open");
					$(this).closest(".tpgb-mobile-menu").find('.extra-toggle-content-overlay').removeClass('open');
				});
				$('.extra-toggle-content-overlay').on( "click", function(e) {
					e.preventDefault();
					$(this).closest(".tpgb-mobile-menu").find('.header-extra-toggle-content').removeClass("open");
					$(this).removeClass('open');
				});
			}	
			if($('.tpgb-mobile-menu .extra-toggle-close-menu.mm-ci-auto').length > 0){
				$(".tpgb-mm-et-link").on( "click", function(e) {
					e.preventDefault();
					$(this).closest(".tpgb-loop-inner").find('.tpgb-mm-et-link').addClass("tpgb-mm-ca");
					$(this).closest(".tpgb-loop-inner").find('.extra-toggle-close-menu-auto').addClass('tpgb-mm-ca');
				});
				$(".extra-toggle-close-menu-auto").on("click",function(){				
					$(this).closest(".tpgb-mobile-menu").find('.header-extra-toggle-content').removeClass("open");
					$(this).closest(".tpgb-mobile-menu").find('.extra-toggle-content-overlay').removeClass('open');
					$(this).closest(".tpgb-mobile-menu").find('.tpgb-loop-inner .tpgb-mm-et-link').removeClass("tpgb-mm-ca");
					$(this).closest(".tpgb-mobile-menu").find('.tpgb-loop-inner .extra-toggle-close-menu-auto').removeClass('tpgb-mm-ca');
					
				});
				$(".extra-toggle-content-overlay").on("click",function(){				
					$(this).closest(".tpgb-loop-inner").find( ".extra-toggle-close-menu-auto.tpgb-mm-ca").trigger( "click" );
				});
			}
			
			var container_scroll_view = $container.hasClass('scroll-view');
			if($container.length > 0 && container_scroll_view){
				if ((screenWidth >= 1201 && mmOpt.DeskTopHide!==true) || (screenWidth <= 1200 && screenWidth >= 768 && mmOpt.TabletHide!==true) || (screenWidth <= 767 && mmOpt.MobileHide!==true) ) {
					var uid = mmOpt.uid,
						$scroll_top = $("."+uid );
					$(window).on('scroll', function() {
						var scroll = $(this).scrollTop();
						if (scroll > mmOpt.ScrollVal) {
							$scroll_top.addClass('show');
						}else {
							$scroll_top.removeClass('show');
						}
					});
				}
			}
			
			$container.find("a.tpgb-menu-link").each(function(){
				var pathname = location.pathname;
					pathname = pathname.substr(pathname.indexOf('/') + 1);
					if ($(this).attr("href") == window.location.href.replace(/\/$/, '')){
						$(this).closest(".tpgb-mm-li").addClass("active");
					}else if(pathname && $(this).attr("href") && $(this).attr("href").indexOf(pathname) > -1){
						$(this).closest(".tpgb-mm-li").addClass('active');
					}
			});
		});
	});
})(jQuery);