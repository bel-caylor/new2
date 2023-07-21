/*--- animated background color ---*/
;(function($, window, document, undefined) {
	"use strict";
	$.fn.animatedBG = function(options){
		var defaults = {
				colorSet: ['#ef008c', '#00be59', '#654b9e', '#ff5432', '#00d8e6'],
				delay: 0,
				duration: 3,
			},
			settings = $.extend({}, defaults, options);
		var totalInterval = (settings.delay + settings.duration) * 1000;
		return this.each(function(){
			var $this = $(this);
			
			$this.each(function(){
				var $el = $(this),
					colors = settings.colorSet;
		
				function shiftColor() {
					var color = colors.shift();
					colors.push(color);
					return color.toString();
				}

				// initial color
				var initColor = shiftColor();
				$el.css('backgroundColor', initColor);
				
				setInterval(function(){
					var color = shiftColor();
					$el.animate({ backgroundColor : color }, (settings.duration * 1000));
				}, totalInterval);
			});
		});
	};
	$(".tpgb-section:not(.tpgb-section-editor) .row-animat-bg,.tpgb-container-row:not(.tpgb-container-row-editor) .row-animat-bg").each(function() {
		var maindiv = $(this),
			data_delay = maindiv.data('bg-delay'),
			data_dur = maindiv.data('bg-duration'),
			colors = maindiv.data('bg');

			maindiv.animatedBG({
				colorSet: colors,
				delay: data_delay,
				duration: data_dur,
			});
	});
}(jQuery, window, document));
/*--- animated background color ---*/