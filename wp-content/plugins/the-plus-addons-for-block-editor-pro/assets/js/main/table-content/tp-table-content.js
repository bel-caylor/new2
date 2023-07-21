( function( $ ) {
	"use strict";
		if($('.tpgb-table-content').length){
			$('.tpgb-table-content').each(function(){
				var settings = $(this).data('settings');
				if(settings.contentSelector != undefined && settings.contentSelector !='' && document.querySelector(settings.contentSelector)){
					tocbot.init({
						...settings
					});
				}else{
					$(this).append('<div class="tpgb-table-notice">Table of Content Class/Selector ID not found! Please Update "Content Selector" Option.</div>');
				}
			});
			if( $('.table-toggle-wrap').length ){
				$('.table-toggle-wrap').each(function(){
					var defaultToggle = $(this).data('default-toggle');
					var Width = window.innerWidth;
					if((Width>1200 && defaultToggle.md) || (Width<1201 && Width>=768 && defaultToggle.sm) || (Width<768 && defaultToggle.xs)){
						$( this ).addClass( "active" );
						$('.tpgb-toc',this ).slideDown(500);
						$('.table-toggle-icon',this).removeClass($(this).data("close"));
						$('.table-toggle-icon',this).addClass($(this).data("open"));
					}else{
						$( this ).removeClass( "active" );
						$('.tpgb-toc', this ).slideUp(500);
						$('.table-toggle-icon',this).removeClass($(this).data("open"));
						$('.table-toggle-icon',this).addClass($(this).data("close"));
					}
					
					$('.tpgb-toc-heading',this).on('click',function(){
						var togglewrap = $(this).closest('.table-toggle-wrap');
						if(togglewrap.hasClass('active')){
							togglewrap.removeClass( "active" );
							togglewrap.find('.tpgb-toc').slideUp(500);
							$('.table-toggle-icon',this).removeClass(togglewrap.data("open"));
							$('.table-toggle-icon',this).addClass(togglewrap.data("close"));
						}else{
							togglewrap.addClass( "active" );
							togglewrap.find('.tpgb-toc').slideDown(500);
							$('.table-toggle-icon',this).removeClass(togglewrap.data("close"));
							$('.table-toggle-icon',this).addClass(togglewrap.data("open"));
						}
					});
				});
			}
		}
})(jQuery);