function tpgbBgImgScrollParallax() {
	"use strict";
	var $ = jQuery,
		imgParallax = $('.tpgb-creative-img-parallax');
	if(imgParallax.length > 0) {
		var controller = new ScrollMagic.Controller();
		imgParallax.each(function(index, elem) {
			var data_parallax_x =$(this).data("scroll-parallax-x");
			var data_parallax_y =$(this).data("scroll-parallax-y");
			data_parallax_x = -(data_parallax_x);
			var parallax_image = $('.tpgb-simple-parallax-img',this),
				tween = 'tween-'+index;
			tween = new TimelineMax();
			new ScrollMagic.Scene({
                triggerElement: elem,
				duration: '150%'
			}).setTween(tween.from(parallax_image, 1, {x:data_parallax_x,y:data_parallax_y,ease: Linear.easeNone})).addTo(controller);
		});
	}
}
(function($) {
	'use strict';
		tpgbBgImgScrollParallax();
		var bgImgAnimate = $('.tpgb-animate-image.tpgb-bg-img-animated');
		if(bgImgAnimate.length > 0) {
			bgImgAnimate.each(function() {
				var b = $(this);
				b.waypoint(function(direction) {
					if( direction === 'down') {
						if(b.hasClass("tpgb-creative-animated")) {
							b.hasClass("tpgb-creative-animated");
							} else {
							b.addClass("tpgb-creative-animated");
						}
					}
				}, {triggerOnce: true,  offset: '90%' } );
			});
		}

        let tpImg = document.querySelectorAll('.tpgb-animate-image.tpgb-fancy-add');
		if(tpImg.length > 0) {
                tpImg.forEach(function(obj){
                var wrap = obj,
                 BoxID = wrap.dataset.id,
                 Setting = JSON.parse(wrap.dataset.fancyOption);
				$('[data-fancybox="fancyImg-'+BoxID+'"]').fancybox({
					buttons : Setting && Setting.button ?  Setting.button : '',
					image: {
						preload: true
					},
					loop: true,
					animationEffect:  (Setting.animationEffect=='none' ? false : Setting.animationEffect),
					animationDuration: Setting.animationDuration,

					clickContent:'next',
					clickSlide:'close',
					dblclickContent: false,
					dblclickSlide: false,

				});
			});
		}
} ( jQuery ) );