(function($) {
	"use strict";
		$('.tpgb-process-steps').each(function() {
		   var container = $(this),
				loop_item = container.find(".tpgb-p-s-wrap"),
				data_conn = container.data("connection"),
				data_eventtype = container.data("eventtype");

			if (container.hasClass('style-2')) {
				let tabletRes = container.hasClass('verticle-tablet');
				var w = $(window).innerWidth();
				if ((w >= 768 && !tabletRes) || (tabletRes && w>=1024)) {
					var total_item = loop_item.length;
					var divWidth = container.width();
					var margin = total_item * 20;

					var new_divWidth = divWidth - margin;
					var per_box_width = new_divWidth / total_item;
					loop_item.css('width', per_box_width);
						
					$(window).on('resize', function() {                    
						var total_item = loop_item.length;
						var divWidth = container.width();
						var margin = total_item * 20;

						new_divWidth = divWidth - margin;
						per_box_width = new_divWidth / total_item;
						loop_item.css('width', per_box_width);

					});
				}
			}
			if(data_conn!='' && data_conn!=undefined){
				if(data_conn!='' && data_eventtype=='con_pro_hover'){
					loop_item.on("mouseenter", function() {
						$(this ).closest('.tpgb-process-steps').find(".tpgb-p-s-wrap").removeClass("active");
                        $(this).addClass("active");
					});
				}else if(data_conn!='' && data_eventtype=='con_pro_click'){
					loop_item.on('click',function(){
						$(this ).closest('.tpgb-process-steps').find(".tpgb-p-s-wrap").removeClass("active");
                        $(this).addClass("active");
					});
				}
			}
        });
})(jQuery);