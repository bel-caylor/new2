/*MouseMove Paralalx*/
( function ( $ ) {
	'use strict';
	if($(".tpgb-mouse-parallax").length){
		$(".tpgb-mouse-parallax").each(function(){
			tpgbMouseParallax($(this));
		});
	}
	
	function tpgbMouseParallax(ele){
		"use strict";
		var $=jQuery,
			$parallaxItems= ele.find(".tpgb-parallax-move"),
			fixer  = 0.0008;
				
			ele.on("mouseleave", function(event){
				var pageX =  event.pageX - ($(this).width() * 0.5);
				var pageY =  event.pageY - ($(this).height() * 0.5);
				$(this).find(".tpgb-parallax-move").each(function(){
					var item 	= $(this);
					var speedX	= item.data("speedx");  				
					var speedY	= item.data("speedy");
					TweenLite.to(item,0.9,{
						x: (0)*fixer,
						y: (0)*fixer
					});
				});
			});
			
			ele.on('mousemove', function(e){
				$(this).find(".tpgb-parallax-move").each(function(){
					var item 	= $(this);
					var speedX	= item.data("speedx");
					var speedY	= item.data("speedy");
					$(this).plusMouseParallax(speedX,speedY, e);
				});
			});
			
			$.fn.plusMouseParallax = function (resistancex, resistancey, mouse ) {
				var $el = $( this );
				TweenLite.to( $el, 0.5, {
					x : -(( mouse.clientX - (window.innerWidth/2) ) / resistancex),
					y : -(( mouse.clientY - (window.innerHeight/2) ) / resistancey)
				});
			};
	}
}( jQuery ));
