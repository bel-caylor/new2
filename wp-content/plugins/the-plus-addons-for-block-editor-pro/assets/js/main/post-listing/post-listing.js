(function($) {
    "use strict";
		/*Dynamic Listing Block Js*/
        var dynamic_hover_content = $(".tpgb-post-listing.dynamic-style-1");
		
        if(dynamic_hover_content.length){
			$(dynamic_hover_content).find(".dynamic-list-content").on("mouseenter", function() {
				$(this).find(".tpgb-post-hover-content").slideDown(300)
			}),
			$(dynamic_hover_content).find(".dynamic-list-content").on("mouseleave", function() {
				$(this).find(".tpgb-post-hover-content").slideUp(300)
			})
		}
		/*Dynamic Listing Block Js*/
		if($('.tpgb-child-filter').length){
			$( ".tpgb-child-filter.tpgb-filters .tpgb-categories .tpgb-filter-list a" ).on( "click", function(event) {
				event.preventDefault();
				var get_filter = $(this).data("filter"),
				get_filter_remove_dot = get_filter.split('.').join(""),  
				get_sub_class = 'cate-parent-',
				get_filter_add_class = get_sub_class.concat(get_filter_remove_dot);
				
				if(get_filter_remove_dot=="*" && get_filter_remove_dot !=undefined){
					$(this).closest(".tpgb-category-filter").find(".category-filters-child").removeClass( "active");
				}else{
					$(this).closest(".tpgb-category-filter").find(".category-filters-child").removeClass( "active");
					$(this).closest(".tpgb-category-filter").find(".category-filters-child."+get_filter_add_class).addClass( "active");
				}
			});
	 	}
})(jQuery);