(function($) {
	"use strict";
		if(!$('.tpgb-preloader-editor').length){
		$('.tpgb-preloader').each(function(){
			var container = $(this),
			data = container.data('plec'),
			post_load_opt = container.data('post_load_opt'),
			post_load_exclude_class = data['post_load_exclude_class'];
			if(post_load_opt==='disablepostload'){
				$("body").removeClass("tpgb-body-preloader");
			}
			if($( ".tpgb-img-loader" ).length){
				var heightimg = $(".tpgb-img-loader .tpgb-preloader-logo-l-img").height(),
				widthimg = $(".tpgb-img-loader .tpgb-preloader-logo-l-img").width();
				$(".tpgb-img-loader-wrap .tpgb-img-loader-wrap-in").css("width",widthimg).css("height",heightimg);			
			}	
			if($('body').hasClass('tpgb-body-preloader')){
				if(post_load_exclude_class != undefined && post_load_exclude_class !=''){
					$(document).on("click", post_load_exclude_class, function(e) {
						if ((e.shiftKey || e.ctrlKey || e.metaKey || '_blank' == $.trim($(this).attr('target')))) {
							return;
						}					
						$('body').removeClass('tpgb-loaded').addClass('tpgb-out-loaded');
						
						if($('body.tpgb-out-loaded').find(".tpgb-preloader").hasClass("tpgb-preload-transion4")){
							$("body").find(".tpgb-preloader.tpgb-preload-transion4 .tpgb-preload-reveal-layer-box").css("transform","");
							var transform, direction='';
							if( $( ".tpgb-out-loaded .tpgb-4-postload-topleft" ).length || $( ".tpgb-out-loaded .tpgb-4-postload-topright" ).length  || $( ".tpgb-out-loaded .tpgb-4-postload-bottomleft" ).length  || $( ".tpgb-out-loaded .tpgb-4-postload-bottomright" ).length  ) {	
								
								var winsize = {width: window.innerWidth, height: window.innerHeight};
								var crosswh = Math.sqrt(Math.pow(winsize.width, 2) + Math.pow(winsize.height, 2));
								if( $( ".tpgb-out-loaded .tpgb-4-postload-topleft" ).length ) {
									transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,135deg) translate3d(0,' + crosswh + 'px,0)';
								}
								else if( $( ".tpgb-out-loaded .tpgb-4-postload-topright" ).length ) {
									transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,-135deg) translate3d(0,' + crosswh + 'px,0)';
								}
								else if( $( ".tpgb-out-loaded .tpgb-4-postload-bottomleft" ).length ) {
									transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,45deg) translate3d(0,' + crosswh + 'px,0)';
								}
								else if(  $( ".tpgb-out-loaded .tpgb-4-postload-bottomright" ).length  ) {
									transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,-45deg) translate3d(0,' + crosswh + 'px,0)';
								}
							}else if( $( ".tpgb-out-loaded .tpgb-4-postload-left" ).length || $( ".tpgb-4-postload-right" ).length ) {
								
								direction='right';
								if($( ".tpgb-out-loaded .tpgb-4-postload-left" ).length){
									direction='left';
								}
								transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,' + (direction === 'left' ? 90 : -90) + 'deg) translate3d(0,100%,0)';
							}else if( $( ".tpgb-out-loaded .tpgb-4-postload-top" ).length || $( ".tpgb-out-loaded .tpgb-4-postload-bottom" ).length ) {
								direction='bottom';
								if($( ".tpgb-out-loaded .tpgb-4-postload-top" ).length){
									direction='top';
								}
								transform = direction === 'top' ? 'rotate3d(0,0,1,180deg)' : 'none';
							}
							if( $( ".tpgb-out-loaded .tpgb-4-postload-topleft" ).length || $( ".tpgb-out-loaded .tpgb-4-postload-topright" ).length  || $( ".tpgb-out-loaded .tpgb-4-postload-bottomleft" ).length  || $( ".tpgb-out-loaded .tpgb-4-postload-bottomright" ).length  || $( ".tpgb-out-loaded .tpgb-4-postload-left" ).length || $( ".tpgb-out-loaded .tpgb-4-postload-right" ).length || $( ".tpgb-out-loaded .tpgb-4-postload-top" ).length || $( ".tpgb-out-loaded .tpgb-4-postload-bottom" ).length ) {
								$( ".tpgb-out-loaded .tpgb-preloader .tpgb-preload-reveal-layer-box" ).css("transform",transform).css("-webkit-transform",transform);
							}
						}
					});
				}else{
					$(document).on("click", 'a:not(.coupon-btn-link,.ajax_add_to_cart,.button-toggle-link,.noajax,.post-load-more,.slick-slide, .woocommerce a, .btn, .button,[data-slick-index],[data-month], .popup-gallery, .popup-video, [href$=".png"], [href$=".jpg"], [href$=".jpeg"], [href$=".svg"], [href$=".mp4"], [href$=".webm"], [href$=".ogg"], [href$=".mp3"], [href^="#"],[href*="#"], [href^="mailto:"],[data-lity=""], [href=""], [href*="wp-login"], [href*="wp-admin"], .dot-nav-noajax, .pix-dropdown-arrow,[data-toggle="dropdown"],[role="tab"]),button:not(.subscribe-btn-submit,.lity-close,[type="button"],.single_add_to_cart_button,.pswp__button.pswp__button--close,.pswp__button--fs,.pswp__button--zoom,.pswp__button--arrow--left,.pswp__button--arrow--right)', function(e) {
						if ((e.shiftKey || e.ctrlKey || e.metaKey || '_blank' == $.trim($(this).attr('target')))) {
							return;
						}
						$('body').removeClass('tpgb-loaded').addClass('tpgb-out-loaded');
						if($('body.tpgb-out-loaded').find(".tpgb-preloader").hasClass("tpgb-preload-transion4")){
							$("body").find(".tpgb-preloader.tpgb-preload-transion4 .tpgb-preload-reveal-layer-box").css("transform","");
							var transform, direction='';
							if( $( ".tpgb-out-loaded .tpgb-4-postload-topleft" ).length || $( ".tpgb-out-loaded .tpgb-4-postload-topright" ).length  || $( ".tpgb-out-loaded .tpgb-4-postload-bottomleft" ).length  || $( ".tpgb-out-loaded .tpgb-4-postload-bottomright" ).length  ) {	
								var winsize = {width: window.innerWidth, height: window.innerHeight};
								var crosswh = Math.sqrt(Math.pow(winsize.width, 2) + Math.pow(winsize.height, 2));
								
								if( $( ".tpgb-out-loaded .tpgb-4-postload-topleft" ).length ) {
									transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,135deg) translate3d(0,' + crosswh + 'px,0)';
								}
								else if( $( ".tpgb-out-loaded .tpgb-4-postload-topright" ).length ) {
									transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,-135deg) translate3d(0,' + crosswh + 'px,0)';
								}
								else if( $( ".tpgb-out-loaded .tpgb-4-postload-bottomleft" ).length ) {
									transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,45deg) translate3d(0,' + crosswh + 'px,0)';
								}
								else if(  $( ".tpgb-out-loaded .tpgb-4-postload-bottomright" ).length  ) {
									transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,-45deg) translate3d(0,' + crosswh + 'px,0)';
								}
							}else if( $( ".tpgb-out-loaded .tpgb-4-postload-left" ).length || $( ".tpgb-4-postload-right" ).length ) {
								direction='right';
								if($( ".tpgb-out-loaded .tpgb-4-postload-left" ).length){
									direction='left';
								}
								transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,' + (direction === 'left' ? 90 : -90) + 'deg) translate3d(0,100%,0)';
							}else if( $( ".tpgb-out-loaded .tpgb-4-postload-top" ).length || $( ".tpgb-out-loaded .tpgb-4-postload-bottom" ).length ) {
								direction='bottom';
								if($( ".tpgb-out-loaded .tpgb-4-postload-top" ).length){
									direction='top';
								}
								transform = direction === 'top' ? 'rotate3d(0,0,1,180deg)' : 'none';
							}
							if( $( ".tpgb-out-loaded .tpgb-4-postload-topleft" ).length || $( ".tpgb-out-loaded .tpgb-4-postload-topright" ).length  || $( ".tpgb-out-loaded .tpgb-4-postload-bottomleft" ).length  || $( ".tpgb-out-loaded .tpgb-4-postload-bottomright" ).length  || $( ".tpgb-out-loaded .tpgb-4-postload-left" ).length || $( ".tpgb-out-loaded .tpgb-4-postload-right" ).length || $( ".tpgb-out-loaded .tpgb-4-postload-top" ).length || $( ".tpgb-out-loaded .tpgb-4-postload-bottom" ).length ) {
								$( ".tpgb-out-loaded .tpgb-preloader .tpgb-preload-reveal-layer-box" ).css("transform",transform).css("-webkit-transform",transform);
							}
						}
					});
				}
			}
		});
	}
})(jQuery);





jQuery( window ).on('load',function() {
	var width = 100,
    performancedata = window.performance.timing,
    estimatedloadtime = -(performancedata.loadEventEnd - performancedata.navigationStart),
    time = parseInt((estimatedloadtime/1000)%60)*100;
	
	var containerload = jQuery('.tpgb-preloader');
	if(containerload.length){
		var data = containerload.data('plec'),		
		loadtime = data['loadtime'],
		loadmaxtime = data['loadmaxtime'],
		loadmintime = data['loadmintime'],			
		csttimemax1000 = loadmaxtime*1000,
		csttimemin1000 = loadmintime*1000;
		if(csttimemax1000 != undefined && csttimemax1000 < time && loadtime!=undefined && loadtime=='loadtimemax'){
			time = csttimemax1000;
		}
		
		if(csttimemin1000 != undefined && csttimemin1000 > time && loadtime!=undefined && loadtime=='loadtimemin'){
			time = csttimemin1000;
		}
	}	
	if(width > 1){
		jQuery(".tpgb-percentage").addClass("tpgb-percentage-load");
	}
	var tp_preloader = 'tpgb-progress-loader',
		tp_loadbar = 'tpgb-loadbar',
		tp_percentagelayout = 'percentagelayout',
		tp_plcper = 'layout-',
		tp_logo_width = 'tpgb-preloader .tpgb-img-loader-wrap',
		tp_text_loader = 'tpgb-preloader .tpgb-text-loader .tpgb-text-loader-inner',
		tp_pre_5 ='tpgb-pre-5-in';
		
	if( jQuery("."+tp_loadbar).length || jQuery("."+tp_percentagelayout).length || jQuery("."+tp_preloader+"4-in").length || jQuery("."+tp_preloader+"5."+tp_plcper+"5 ."+tp_pre_5+"3").length || jQuery("."+tp_preloader+"5."+tp_plcper+"5 ."+tp_pre_5+"4").length ||  jQuery("."+tp_logo_width).length ||  jQuery("."+tp_text_loader).length){
		jQuery("."+tp_loadbar+",."+tp_percentagelayout+",."+tp_preloader+"4-in,."+tp_preloader+"5."+tp_plcper+"5 ."+tp_pre_5+"3, ."+tp_preloader+"5."+tp_plcper+"5 ."+tp_pre_5+"4,."+tp_logo_width+",."+tp_text_loader).animate({
		  width: width + "%"
		}, time);
	}
	
	if( jQuery("."+tp_preloader+"5."+tp_plcper+"5 ."+tp_pre_5+"1").length || jQuery("."+tp_preloader+"5."+tp_plcper+"5 ."+tp_pre_5+"2").length){
		jQuery("."+tp_preloader+"5."+tp_plcper+"5 ."+tp_pre_5+"1, ."+tp_preloader+"5."+tp_plcper+"5 ."+tp_pre_5+"2").animate({
			height : width + "%"
		}, time);
	}

var percwrap = jQuery(".tpgb-precent,.tpgb-precent3,.tpgb-precent4"),
		start = 0,
		end = 100,
		durataion = time;
		if(percwrap){
			animationoutput(percwrap, start, end, durataion);
		}		
		
function animationoutput(id, start, end, duration) {
  
	var range = end - start,
      current = start,
      increment = end > start? 1 : -1,
      stepfortime = Math.abs(Math.floor(duration / range)),
      obj = jQuery(id);
    
	var timer = setInterval(function() {
		current += increment;
		jQuery(obj).text(current + "%");
		setProgress(current);
		if (current == end) {
			clearInterval(timer);
		}
	}, stepfortime);
}

var circle = document.querySelector('.progress-ring1');
if(circle){
	var radius = circle.r.baseVal.value;
	var circumference = radius * 2 * Math.PI;

	circle.style.strokeDasharray = `${circumference} ${circumference}`;
	circle.style.strokeDashoffset = `${circumference}`;
}
function setProgress(percent) {
	if(circle){
		const offset = circumference - percent / 100 * circumference;
		circle.style.strokeDashoffset = offset;
	}
}

setTimeout(function(){
  jQuery('body').addClass('tpgb-loaded');
	if(jQuery('body').find(".tpgb-preloader").hasClass("tpgb-preload-transion4")){
		setTimeout(function(){
			jQuery("body").find(".tpgb-preloader.tpgb-preload-transion4").addClass("tpprein");
			jQuery("body").find(".tpgb-preloader.tpgb-preload-transion4").addClass("tppreinout");
			setTimeout(function(){
				jQuery("body").find(".tpgb-preloader.tpgb-preload-transion4").removeClass("tpprein");
				jQuery("body").find(".tpgb-preloader.tpgb-preload-transion4").addClass("tppreout");
			}, 1500);
		}, 20);
		
	}
	jQuery('.tpgb-progress-loader,.percentagelayout,.tpgb-progress-loader4.layout-4,.tpgb-progress-loader6').fadeOut(300);
}, time+1000);

});