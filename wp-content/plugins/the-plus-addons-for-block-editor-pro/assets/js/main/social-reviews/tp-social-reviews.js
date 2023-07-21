(function($) {
    "use strict";
    $('.tpgb-social-reviews').each( function() {
        var e = $(this);
        var i = 0,
		BoxID = e.data("id"),
		scroll_nrml = e.data("scroll-normal"),
		Get_TL = e.data("textlimit");
		
		if(scroll_nrml.ScrollOn == true && scroll_nrml.TextLimit == false){
			let SF_Text = e.find('.showtext');
			SF_Text.each( function() {
				if($(this)[0].clientHeight >= scroll_nrml.Height){
					$(this).addClass(scroll_nrml.className);
					$(this).css('height', scroll_nrml.Height);
				}
			});
		}
		
		$(document).on( 'click', ".tpgb-block-"+BoxID+".tpgb-social-reviews .readbtn", function() {
			var div = $(this).closest('.tpgb-message'),
				container = div.closest('.tpgb-isotope .post-loop-inner'),
				Scroll = div.closest('.tpgb-social-reviews').data("scroll-normal"),
				S = div.find('.showtext');   

				if(div.hasClass('show-text')){
					div.removeClass('show-text show-less');
					$(this).html(Get_TL.showmoretxt)
					div.find('.sf-dots').css('display','inline');

					if(Scroll.ScrollOn == true && Scroll.TextLimit == true){
						S.removeClass(Scroll.className);
						S.removeAttr('style');
					}
				}else{
					div.addClass('show-text show-less');
					$(this).html(Get_TL.showlesstxt)
					div.find('.sf-dots').css('display','none');

					let SF_Text = $('.tpgb-social-reviews').find(S);
					if(Scroll.ScrollOn == true && Scroll.TextLimit == true){
						SF_Text.each( function() {
							if($(this)[0].clientHeight >= Scroll.Height){
								S.addClass(Scroll.className);
								S.css('height', Scroll.Height);
							}
						});
					}
				}
				container.isotope({
					itemSelector: ".grid-item",
					resizable: !0,
					sortBy: "original-order"
				});
		});

		$( ".tpgb-block-"+BoxID ).on( "click", ".batch-btn-no", function(p) {
			p.preventDefault();
			this.closest(".tpgb-batch-recommend").style.display = "none";
		});
		
		if($(".tpgb-block-"+BoxID+".tpgb-social-reviews").find('.tpgb-review-load-more').length > 0){
			$(".tpgb-block-"+BoxID+" .review-load-more").on('click', function(e) {
				e.preventDefault();
				var loadFeed_click = $(this),
					loadFeed = loadFeed_click.data('loadattr'),
					display = loadFeed_click.attr('data-display'),
					loadFview = loadFeed_click.data('loadview'),
					loadclass = loadFeed_click.data('loadclass'),
					loadlayout = loadFeed_click.data('layout'),
					loadloadingtxt = loadFeed_click.data('loadingtxt'),
					current_text = loadFeed_click.text();
				if ( loadFeed_click.data('requestRunning') ) {
					return;
				}
				loadFeed_click.data('requestRunning', true);
				$.ajax({
					type:'POST',
					data:'action=tpgb_reviews_load&view='+display+'&loadFview='+loadFview+'&loadattr='+loadFeed,
					url:tpgb_load.ajaxUrl,
					beforeSend: function() {
						$(loadFeed_click).text(loadloadingtxt);
					},
					success: function(data) {
						let HtmlData = (data && data.HTMLContent) ? data.HTMLContent : '',
							TotalReview = (data && data.TotalReview) ? data.TotalReview : '',
							FilterStyle = (data && data.FilterStyle) ? data.FilterStyle : '',
							Allposttext = (data && data.allposttext) ? data.allposttext : '';
						if(data == ''){
							$(loadFeed_click).addClass("hide");
						}else{
							let BlockClass = '.tpgb-block-'+loadclass,
								CategoryClass = $(BlockClass + " .all .tpgb-category-count"),
								PostLoopClass = $(BlockClass + " .post-loop-inner");
							PostLoopClass.append(HtmlData);
							
							let Totalcount = $(BlockClass).find('.grid-item').length;
							$(CategoryClass).html("").append(Totalcount);
							
							if(FilterStyle == 'style-2' || FilterStyle == 'style-3'){
								let Categoryload = $(BlockClass +' .tpgb-filter-list .tpgb-category-list').not('.all');

									$.each( Categoryload, function( key, value ) {
										let span2 = $(value).find('span:nth-child(2)').data('hover'),
											Toatal2 = $(BlockClass).find('.grid-item.' + span2).length;      
										$(value).find('span:nth-child(1).tpgb-category-count').html("").append(Toatal2);
									});
							}
							if(loadlayout == 'grid' || loadlayout == 'masonry'){
								if($(BlockClass).hasClass("tpgb-isotope")){
									$(PostLoopClass).isotope( 'layout' ).isotope( 'reloadItems' ); 
								}
							}
							if(Totalcount >= TotalReview){ 
								$(loadFeed_click).addClass("hide");
								$(loadFeed_click).parent(".tpgb-review-load-more").append('<div class="tpgb-review-loaded">'+Allposttext+'</div>');
							}else{
								$(loadFeed_click).text(current_text);
							}
						}
						display=Number(display)+Number(loadFview);
						loadFeed_click.attr("data-display", display);
					},
					complete: function() {
						loadFeed_click.data('requestRunning', false);
					}
				}).then(function(){ 
					if ($(".tpgb-block-" + loadclass).hasClass("tpgb-isotope")) {
						if (loadlayout == 'grid' || loadlayout == 'masonry') {
							var container = $(".tpgb-block-" + loadclass + ' .post-loop-inner');
							container.isotope({
								itemSelector: ".grid-item",
								resizable: !0,
								sortBy: "original-order"
							});
						}
					}
				});
			});
		}
		
		//Lazy Load
		if($('body').find(".tpgb-block-"+BoxID+" .tpgb-review-lazy-load").length){
			var windowWidth, windowHeight, documentHeight, scrollTop, containerHeight, containerOffset, $window = $(window);
			var recalcValues = function() {
				windowWidth = $window.width();
				windowHeight = $window.height();
				documentHeight = $('body').height();
				containerHeight = $(".tpgb-block-"+BoxID+".tpgb-isotope").height();
				containerOffset = $(".tpgb-block-"+BoxID+".tpgb-isotope").offset().top+50;
				setTimeout(function(){
					containerHeight = $(".tpgb-block-"+BoxID+".tpgb-isotope").height();
					containerOffset = $(".tpgb-block-"+BoxID+".tpgb-isotope").offset().top+50;
				}, 50);
			};      
			recalcValues();
			$window.resize(recalcValues);
			$window.bind('scroll', function(e) {
				e.preventDefault();
				recalcValues();
				scrollTop = $window.scrollTop();
				$(".tpgb-block-"+BoxID+".tpgb-isotope").each(function() {
					containerHeight = $(this).height();
					containerOffset = $(this).offset().top+50;
					
					if($(this).find(".review-lazy-load").length && scrollTop < documentHeight && (scrollTop+60 > (containerHeight + containerOffset - windowHeight)) ){
						var lazyFeed_click = $(this).find(".review-lazy-load"),
							lazyFeed = lazyFeed_click.data('lazyattr'),
                            totalreviews = lazyFeed_click.data('totalreviews'),
                            display = lazyFeed_click.attr('data-display'),
							loadFview = lazyFeed_click.data('lazyview'),
							loadclass = lazyFeed_click.data('lazyclass'),
							loadlayout = lazyFeed_click.data('lazylayout'),
							loadloadingtxt = lazyFeed_click.data('loadingtxt'),
							current_text = lazyFeed_click.text();
							
							
						if ( lazyFeed_click.data('requestRunning') ) {
							return;
						}
						lazyFeed_click.data('requestRunning', true);
						if(totalreviews >= display){
							$.ajax({
								type:'POST',
								data:'action=tpgb_reviews_load&view='+display+'&loadFview='+loadFview+'&loadattr='+lazyFeed,
								url:tpgb_load.ajaxUrl,
								beforeSend: function() {
									$(lazyFeed_click).text(loadloadingtxt);
								},
								success: function(data) {
									let HtmlData = (data && data.HTMLContent) ? data.HTMLContent : '',
										TotalReview = (data && data.TotalReview) ? data.TotalReview : '',
										FilterStyle = (data && data.FilterStyle) ? data.FilterStyle : '',
										Allposttext = (data && data.allposttext) ? data.allposttext : '';
										
									if(data == ''){
										$(lazyFeed_click).addClass("hide");
									}else{
										let BlockClass = '.tpgb-block-'+loadclass,
											CategoryClass = $(BlockClass + " .all .tpgb-category-count"),
											PostLoopClass = $(BlockClass + " .post-loop-inner");
											PostLoopClass.append(HtmlData);
											
										let Totalcount = $(BlockClass).find('.grid-item').length;
										$(CategoryClass).html("").append(Totalcount);
										
										if(FilterStyle == 'style-2' || FilterStyle == 'style-3'){
											let Categoryload = $(BlockClass +' .tpgb-filter-list .tpgb-category-list').not('.all');

											$.each( Categoryload, function( key, value ) {
												let span2 = $(value).find('span:nth-child(2)').data('hover'),
													Toatal2 = $(BlockClass).find('.grid-item.' + span2).length;      
													$(value).find('span:nth-child(1).tpgb-category-count').html("").append(Toatal2);
											});
										}
										if(loadlayout == 'grid' || loadlayout == 'masonry'){
											if($(BlockClass).hasClass("tpgb-isotope")){
												$(PostLoopClass).isotope( 'layout' ).isotope( 'reloadItems' ); 
											}
										}
										if(Totalcount >= TotalReview){ 
											if(lazyFeed_click.next('.tpgb-review-loaded').length==0){
												$(lazyFeed_click).addClass("hide");
												$(lazyFeed_click).parent(".tpgb-review-lazy-load").append('<div class="tpgb-review-loaded">'+Allposttext+'</div>');
											}
										}else{
											$(lazyFeed_click).text(current_text);
										}
									}
									display=Number(display)+Number(loadFview);
									lazyFeed_click.attr("data-display", display);
								},
								complete: function() {
									lazyFeed_click.data('requestRunning', false);
								}
							}).then(function(){
								if($(".tpgb-block-"+loadclass).hasClass("tpgb-isotope")){
									if(loadlayout == 'grid' || loadlayout == 'masonry'){
										var container = $(".tpgb-block-"+loadclass+' .post-loop-inner')
										container.isotope({
											itemSelector: ".grid-item",
											resizable: !0,
											sortBy: "original-order"
										});
									}
								}
							});
						}
					}
				});
			});
		}
        e.find('.batch-btn-no').on('click', function(p) {
            p.preventDefault();
            $(this).closest('.tpgb-batch-recommend').slideToggle();
        })
    });

})(jQuery);