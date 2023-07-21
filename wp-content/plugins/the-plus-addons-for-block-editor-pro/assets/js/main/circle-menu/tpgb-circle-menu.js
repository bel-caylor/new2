/*circle menu*/
(function($) {
	"use strict";
		$('.tpgb-circle-menu').each(function(){
			var container = $(this),
			menuopt = container.data('cirmenu-opt');
			if(container.hasClass('layout-straight')){			
				$(".tpgb-circle-main-menu-list .main_menu_icon",container).on('click',function(e){
					e.preventDefault();
					var block_id=$(this).closest(".tpgb-circle-menu").data("block-id");
					if($('#'+block_id+ ' .tpgb-circle-menu-wrap').hasClass("circleMenu-closed")){
						$(this).closest(".tpgb-circle-menu-wrap").removeClass("circleMenu-closed");					
						$(this).closest(".tpgb-circle-menu-wrap").addClass("circleMenu-open");					
						$(this).closest(".tpgb-circle-menu").find('.show-bg-overlay').addClass("activebg");					
					}else if($('#'+block_id+ ' .tpgb-circle-menu-wrap').hasClass("circleMenu-open")){
						$(this).closest(".tpgb-circle-menu-wrap").removeClass("circleMenu-open");
						$(this).closest(".tpgb-circle-menu-wrap").addClass("circleMenu-closed");
						$(this).closest(".tpgb-circle-menu").find('.show-bg-overlay').removeClass("activebg");										
					}
				});
			}
		
			if(container.hasClass('layout-circle')){
				
				container.find('ul.tpgb-circle-menu-wrap').circleMenu({			
					direction: menuopt.direction,
					angle:{start:menuopt.anglestart, end:menuopt.angleend},
					circle_radius: menuopt.circle_radius,
					circle_radius_tablet: menuopt.circle_radius_tablet,
					circle_radius_mobile: menuopt.circle_radius_mobile,
					delay: menuopt.delay,			
					item_diameter: menuopt.item_diameter,
					speed:  menuopt.speed,
					step_in:  menuopt.step_in,
					step_out:  menuopt.step_out,
					transition_function:  menuopt.transition_function,
					trigger:  menuopt.trigger
				});
			
				$(this).find(".main_menu_icon").on( 'click',function(e){
					e.preventDefault();
					var block_id=$(this).closest(".tpgb-circle-menu").data("block-id");
					if($('#'+block_id+ ' .tpgb-circle-menu-wrap').hasClass("circleMenu-closed")){
						$(this).closest(".tpgb-circle-menu-inner-wrapper").find('.show-bg-overlay').addClass("activebg");					
					}else if($('#'+block_id+ ' .tpgb-circle-menu-wrap').hasClass("circleMenu-open")){
						$(this).closest(".tpgb-circle-menu-inner-wrapper").find('.show-bg-overlay').removeClass("activebg");					
					}				
				});
			}		
			if(container.hasClass('scroll-view')){
				$(window).on('scroll', function() {
					var scroll = $(this).scrollTop();
					container.each(function () {
						var scroll_view_value = $(this).data("scroll-view");
						var block_id=$(this).data("block-id"),
							$scroll_top = $("#"+block_id );
						if (scroll > scroll_view_value) {
							$scroll_top.addClass('show');
						}else {
							$scroll_top.removeClass('show');
						}
						
					});
				});	
			}
		
			$(".tpgb-circle-menu-list").each(function() {
				var current = $(this),
				id = current.attr('id'),
				settings = current.data("tooltip-opt");
				if(settings!='' && settings!=undefined && settings.content != ''){
					tippy( '#'+id , {
						allowHTML : true,
						content: settings.content,
						trigger : settings.trigger,
						appendTo: document.querySelector('#'+id),
					});
				}
			});
		
		});
	
		$(".show-bg-overlay").on('click',function(){
			$(this).closest(".tpgb-circle-menu").find(".main_menu_icon").trigger( "click" );
		});
})(jQuery);