/*post Masonry*/( function( $ ) {
	"use strict";
	if( $('.tpgb-isotope').length > 0 ){
		var b = window.theplus || {};
		b.window = $(window),
		b.document = $(document),
		b.windowHeight = b.window.height(),
		b.windowWidth = b.window.width();	
		b.tpgb_isotope_Posts = function() {
			var c = function(c) {
				var rtlVal = true;
				if(document.dir== 'rtl'){
					rtlVal = false;
				}
				$('.tpgb-isotope').each(function() {
					
					var e, c = $(this), d = c.data("layout"),f = {
						itemSelector: ".grid-item",
						resizable: !0,
						sortBy: "original-order",
						originLeft : rtlVal
					};
					var uid=c.data("id");
					var inner_c=$('.tpgb-block-'+uid).find(".post-loop-inner");
					e = "masonry" === d  ? "masonry" : "fitRows",
					f.layoutMode = e,
					function() {
						//b.initMetroIsotope(),
						inner_c.isotope(f)
					}();
				});
			};

			tpgb_filter('tpgb-isotope');

			b.window.on("load resize", function() {
				c('.tpgb-isotope')
			}),
			window.addEventListener('DOMContentLoaded', (event) => {
				c('.tpgb-isotope')
			})
			$(document).ready(function() {
				c('.tpgb-isotope')
			}),
			$("body").on("post-load resort-isotope", function() {
				setTimeout(function() {
					c('.tpgb-isotope')
				}, 800)
			}),
			$("body").on("tabs-reinited", function() {
				setTimeout(function() {
					c('.tpgb-isotope')
				}, 800)
			});
			
		},
		b.init = function() {
			b.tpgb_isotope_Posts();
		},
		b.init();
	}

	if($('.tpgb-metro').length > 0 ){
        $( document ).ready(function() {
            tpgb_metro_layout('all');
            $('.tpgb-metro').find('.post-loop-inner').isotope('layout').isotope("reloadItems");
        });

        $(window).on("resize", function() {
            tpgb_metro_layout('all');
            $('.tpgb-metro .post-loop-inner').isotope('layout').isotope("reloadItems");
        });
        
        $("body").on("post-load resort-isotope", function() {
            setTimeout(function() {
                tpgb_metro_layout('all');
                $('.tpgb-metro .post-loop-inner').isotope('layout');
            }, 800)

        });
		
        $("body").on("tabs-reinited", function() {
            setTimeout(function() {
                tpgb_metro_layout('all');
                $('.tpgb-metro .post-loop-inner').isotope('layout');
            }, 800)
        });

		tpgb_filter('tpgb-metro');
	}



})(jQuery);

function tpgb_metro_layout(arg){
    var $=jQuery;
	$('.tpgb-metro').each(function(){
		var Id = $(this).data('id'),
			metroAttr = JSON.parse($(this).attr('data-metroAttr')),
			innerWidth = window.innerWidth,
			decWidth = innerWidth >= 1024,
			tabWidth = innerWidth >= 768 && innerWidth < 1024,
			mobileWidth  = innerWidth < 768,
			metroCOl = '',
			setPad = 0,
			myWindow=$(window);

		if( decWidth && metroAttr.metro_col ){
			metroCOl = metroAttr.metro_col
		}
		if( tabWidth && metroAttr.tab_metro_col ){
			metroCOl = metroAttr.tab_metro_col
		}
		if( mobileWidth && metroAttr.mobile_metro_col ){
			metroCOl = metroAttr.mobile_metro_col
		}

		if( metroCOl == '3' ){
			var	norm_size = Math.floor(($(this).width() - setPad*2)/3),
			double_size = norm_size*2;
			$(this).find('.grid-item').each(function(){	
				var set_w = norm_size,
				set_h = norm_size;
				if( ( decWidth && metroAttr.metro_style=='style-1') || ( tabWidth && metroAttr.tab_metro_style=='style-1') || ( mobileWidth && metroAttr.mobile_metro_style=='style-1' ) ){
					if ( ( decWidth && $(this).hasClass('tpgb-metro-1') || $(this).hasClass('tpgb-metro-7') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-1') || $(this).hasClass('tpgb-tab-metro-7')) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-1') || $(this).hasClass('tpgb-mobile-metro-7')) ) {
						set_w = double_size,
						set_h = double_size;
					}
					if ( ( decWidth && $(this).hasClass('tpgb-metro-4') || $(this).hasClass('tpgb-metro-9')) || (  tabWidth && $(this).hasClass('tpgb-tab-metro-4') || $(this).hasClass('tpgb-tab-metro-9')) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-4') || $(this).hasClass('tpgb-mobile-metro-9')) ) {
						set_w = double_size,
						set_h = norm_size;
					}
				}else if( ( decWidth && metroAttr.metro_style=='style-2') || ( tabWidth && metroAttr.tab_metro_style=='style-2') || ( mobileWidth && metroAttr.mobile_metro_style=='style-2' ) ){
					if ( ( decWidth && $(this).hasClass('tpgb-metro-2') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-2') ) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-2') ) ) {
						set_w = double_size,
						set_h = norm_size;
					}
					
					if ( ( decWidth && $(this).hasClass('tpgb-metro-4') || $(this).hasClass('tpgb-metro-8') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-4') || $(this).hasClass('tpgb-tab-metro-8')) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-4') || $(this).hasClass('tpgb-mobile-metro-8')) ) {
						set_w = norm_size,
						set_h = double_size;
					}
					if ( ( decWidth && $(this).hasClass('tpgb-metro-7') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-7') ) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-7') ) ) {
						set_w = double_size,
						set_h = double_size;
					}
				}else if( ( decWidth && metroAttr.metro_style=='style-3') || ( tabWidth && metroAttr.tab_metro_style=='style-3') || ( mobileWidth && metroAttr.mobile_metro_style=='style-3' ) ){
					
					if ( ( decWidth && $(this).hasClass('tpgb-metro-4') || $(this).hasClass('tpgb-metro-15') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-4') || $(this).hasClass('tpgb-tab-metro-15')) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-4') || $(this).hasClass('tpgb-mobile-metro-15')) ) {
						set_w = double_size,
						set_h = norm_size;
					}
					if ( ( decWidth && $(this).hasClass('tpgb-metro-9') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-9') ) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-9') ) ) {
						set_w = norm_size,
						set_h = double_size;
					}
					if ( ( decWidth && $(this).hasClass('tpgb-metro-10') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-10') ) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-10') ) ) {
						set_w = double_size,
						set_h = double_size;
					}
				}else if( ( decWidth && metroAttr.metro_style=='style-4') || ( tabWidth && metroAttr.tab_metro_style=='style-4') || ( mobileWidth && metroAttr.mobile_metro_style=='style-4' ) ){
					
					if ( ( decWidth && $(this).hasClass('tpgb-metro-1') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-1') ) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-1') ) ) {
						set_w = double_size,
						set_h = double_size;
					}
					if ( ( decWidth && $(this).hasClass('tpgb-metro-7') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-7') ) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-7') ) ) {
						set_w = double_size,
						set_h = norm_size;
					}
					
				}
                // if ( innerWidth < 760) {
				// 	set_w = set_w - 30;
				// 	set_h = set_h - 30;
				// }
				$(this).css({
					'width' : set_w+'px',
					'height' : set_h+'px'
				});							
			});
		}

		if( metroCOl == '4' ){
			var	norm_size = Math.floor(($(this).width() - setPad*2)/4),
			double_size = norm_size*2;

			$(this).find('.grid-item').each(function(){
				var set_w = norm_size,
					set_h = norm_size;
					if( ( decWidth && metroAttr.metro_style=='style-1') || ( tabWidth && metroAttr.tab_metro_style=='style-1') || ( mobileWidth && metroAttr.mobile_metro_style=='style-1' ) ){
						if ( ( decWidth && $(this).hasClass('tpgb-metro-3') || $(this).hasClass('tpgb-metro-9') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-3') || $(this).hasClass('tpgb-tab-metro-9')) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-3') || $(this).hasClass('tpgb-mobile-metro-9')) ) {
						set_w = double_size,
						set_h = double_size;
					}
					if ( ( decWidth && $(this).hasClass('tpgb-metro-4') || $(this).hasClass('tpgb-metro-10') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-4') || $(this).hasClass('tpgb-tab-metro-10')) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-4') || $(this).hasClass('tpgb-mobile-metro-10')) ) {
						set_w = double_size,
						set_h = norm_size;
					}
				}				
				if( ( decWidth && metroAttr.metro_style=='style-2') || ( tabWidth && metroAttr.tab_metro_style=='style-2') || ( mobileWidth && metroAttr.mobile_metro_style=='style-2' ) ){
					if ( ( decWidth && $(this).hasClass('tpgb-metro-1') || $(this).hasClass('tpgb-metro-5') || $(this).hasClass('tpgb-metro-9') || $(this).hasClass('tpgb-metro-10') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-1') || $(this).hasClass('tpgb-tab-metro-5') || $(this).hasClass('tpgb-tab-metro-9') || $(this).hasClass('tpgb-tab-metro-10') ) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-1') || $(this).hasClass('tpgb-mobile-metro-5') || $(this).hasClass('tpgb-mobile-metro-9') || $(this).hasClass('tpgb-mobile-metro-10') ) ) {
						set_w = double_size,
						set_h = double_size;
					}
					if ( ( decWidth && $(this).hasClass('tpgb-metro-2') || $(this).hasClass('tpgb-metro-8') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-2') || $(this).hasClass('tpgb-tab-metro-8')) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-2') || $(this).hasClass('tpgb-mobile-metro-8')) ) {
						set_w = double_size,
						set_h = norm_size;
					}
				}
				if( ( decWidth && metroAttr.metro_style=='style-3') || ( tabWidth && metroAttr.tab_metro_style=='style-3') || ( mobileWidth && metroAttr.mobile_metro_style=='style-3' ) ){
					if ( ( decWidth && $(this).hasClass('tpgb-metro-5') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-5') ) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-5') ) ) {
						set_w = double_size,
						set_h = norm_size;
					}
					if ( ( decWidth && $(this).hasClass('tpgb-metro-1') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-1') ) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-1') ) ) {
						set_w = norm_size,
						set_h = double_size;
					}
					if ( ( decWidth && $(this).hasClass('tpgb-metro-3') || $(this).hasClass('tpgb-metro-6') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-3') || $(this).hasClass('tpgb-tab-metro-6')) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-3') || $(this).hasClass('tpgb-mobile-metro-6')) ) {
						set_w = double_size,
						set_h = double_size;
					}
				}

				$(this).css({
					'width' : set_w+'px',
					'height' : set_h+'px'
				});	
			})
		}
		
		if( metroCOl == '5' ){
			var	norm_size = Math.floor(($(this).width() - setPad*2)/5),
			double_size = norm_size*2;
			$(this).find('.grid-item').each(function(){
				var set_w = norm_size,
				set_h = norm_size;
				
				if ( ( decWidth && $(this).hasClass('tpgb-metro-5') || $(this).hasClass('tpgb-metro-15') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-5') || $(this).hasClass('tpgb-tab-metro-15')) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-5') || $(this).hasClass('tpgb-mobile-metro-15')) ) {
					set_w = double_size,
					set_h = double_size;
				}
				if ( ( decWidth && $(this).hasClass('tpgb-metro-1') || $(this).hasClass('tpgb-metro-2') || $(this).hasClass('tpgb-metro-9') || $(this).hasClass('tpgb-metro-10') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-1') || $(this).hasClass('tpgb-tab-metro-2') || $(this).hasClass('tpgb-tab-metro-9') || $(this).hasClass('tpgb-tab-metro-10') ) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-1') || $(this).hasClass('tpgb-mobile-metro-2') || $(this).hasClass('tpgb-mobile-metro-9') || $(this).hasClass('tpgb-mobile-metro-10') ) ) {
					set_w = double_size,
					set_h = norm_size;
				}
				if ( ( decWidth && $(this).hasClass('tpgb-metro-3') || $(this).hasClass('tpgb-metro-6') || $(this).hasClass('tpgb-metro-14') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-3') || $(this).hasClass('tpgb-tab-metro-6') || $(this).hasClass('tpgb-tab-metro-14') ) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-3') || $(this).hasClass('tpgb-mobile-metro-6') || $(this).hasClass('tpgb-mobile-metro-14') ) ) {
					set_w = norm_size,
					set_h = double_size;
				}

				$(this).css({
					'width' : set_w+'px',
					'height' : set_h+'px'
				});	
			});
		}

		if( metroCOl == '6' ){
			var	norm_size = Math.floor(($(this).width() - setPad*2)/6),
			double_size = norm_size*2;

			$(this).find('.grid-item').each(function(){
				var set_w = norm_size,
				set_h = norm_size;
		
				if ( ( decWidth && $(this).hasClass('tpgb-metro-1') || $(this).hasClass('tpgb-metro-5') || $(this).hasClass('tpgb-metro-9') || $(this).hasClass('tpgb-metro-10') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-1') || $(this).hasClass('tpgb-tab-metro-5') || $(this).hasClass('tpgb-tab-metro-9') || $(this).hasClass('tpgb-tab-metro-10') ) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-1') || $(this).hasClass('tpgb-mobile-metro-5') || $(this).hasClass('tpgb-mobile-metro-9') || $(this).hasClass('tpgb-mobile-metro-10') ) ) {
					set_w = double_size,
					set_h = double_size;
				}
				if ( ( decWidth && $(this).hasClass('tpgb-metro-2') || $(this).hasClass('tpgb-metro-7') || $(this).hasClass('tpgb-metro-14') || $(this).hasClass('tpgb-metro-15') || $(this).hasClass('tpgb-metro-16') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-2') || $(this).hasClass('tpgb-tab-metro-7') || $(this).hasClass('tpgb-tab-metro-14') || $(this).hasClass('tpgb-tab-metro-15') || $(this).hasClass('tpgb-tab-metro-16') ) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-2') || $(this).hasClass('tpgb-mobile-metro-7') || $(this).hasClass('tpgb-mobile-metro-14') || $(this).hasClass('tpgb-mobile-metro-15') || $(this).hasClass('tpgb-mobile-metro-16') ) ) {
					set_w = double_size,
					set_h = norm_size;
				}
				if ( ( decWidth && $(this).hasClass('tpgb-metro-4') || $(this).hasClass('tpgb-metro-6') || $(this).hasClass('tpgb-metro-8') ) || ( tabWidth && $(this).hasClass('tpgb-tab-metro-4') || $(this).hasClass('tpgb-tab-metro-6') || $(this).hasClass('tpgb-tab-metro-8') ) || ( mobileWidth && $(this).hasClass('tpgb-mobile-metro-4') || $(this).hasClass('tpgb-mobile-metro-6') || $(this).hasClass('tpgb-mobile-metro-8') ) ) {
					set_w = norm_size,
					set_h = double_size;
				}
				

				$(this).css({
					'width' : set_w+'px',
					'height' : set_h+'px'
				});	
			});
		}

		if($(this).hasClass('tpgb-metro')){
			if (myWindow.innerWidth() > 767) {
				$('.tpgb-block-'+Id).find(".post-loop-inner").isotope({
					itemSelector: '.grid-item',
					layoutMode: 'masonry',
					masonry: {
						columnWidth: norm_size
					}
				});
			}else{
				$('.tpgb-block-'+Id).find(".post-loop-inner").isotope({
					itemSelector: '.grid-item',
					layoutMode: 'masonry',
					masonry: {
						columnWidth: norm_size
					}
				});
			}
		}else{
			$('.tpgb-block-'+Id).find(".post-loop-inner").isotope({
				layoutMode: 'masonry',
				masonry: {
					columnWidth: norm_size
				}
			});
		}
		$('.tpgb-block-'+Id).find(".post-loop-inner").isotope('layout');
		
		$('.tpgb-block-'+Id).find(".post-loop-inner").imagesLoaded( function(){
			$('.tpgb-block-'+Id).find(".post-loop-inner").isotope('layout').isotope( 'reloadItems' );		
		});
    })
}

function tpgb_filter(selector){
	var $=jQuery;
	if($('.'+selector+' .tpgb-filter-data').length>0){
		$('.'+selector+' .tpgb-filter-data').each(function() {
			//List Isotope Filter Item					
			$(this).find(".tpgb-category-list").on('click',function(event) {
				event.preventDefault();
				var p_list = $(this).closest("."+selector+""),
					uid = p_list.data("id");

				var d = $(this).attr("data-filter");
						$(this).parent().parent().find(".active").removeClass("active"),
						$(this).addClass("active"),
						$('#'+uid).find(".post-loop-inner").isotope({
							layoutMode: 'masonry',
							filter: d
						}),
						$("body").trigger("isotope-sorted");
			});
		});
	}
}