
(function($) {
    "use strict";    
        $('.tpgb-social-feed').each( function() {
            let e = $(this),
                BoxID = e.data("id"),
                Setting = e.data("fancy-option"),
                Get_SN = e.data("scroll-normal");

            $('[data-fancybox="'+BoxID+'"]', this).fancybox({
                buttons : Setting.button,
                image: { 
					preload: 0 
				},
                loop: Setting.loop,
				infobar: Setting.infobar,
				animationEffect: Setting.animationEffect,
				animationDuration: Setting.animationDuration,
				transitionEffect: Setting.transitionEffect,
				transitionDuration: Setting.transitionDuration,
				arrows: Setting.arrows,
				clickContent: Setting.clickContent,
				clickSlide: Setting.slideclick,
				dblclickContent: false,
				dblclickSlide: false,
				smallBtn: false,
				iframe : {
					preload : 0
				},
				youtube : {
					autoplay : 0
				},
				vimeo : {
					autoplay : 0
				},
				mp4 : {
					autoplay : 0
				},                    
				video: {
					autoStart: 0
				},
            });

            $('.grid-item.feed-Facebook', this).each( function() {
                var itemindex = $(this).data("index");

                $('[data-fancybox="album-'+itemindex+'-'+BoxID+'"]',this).fancybox({
                    buttons : Setting.button,
                    image: { preload: true },
                    loop: Setting.loop,
                    infobar: Setting.infobar,
                    animationEffect:  Setting.animationEffect,
                    animationDuration: Setting.animationDuration,
                    transitionEffect: Setting.transitionEffect,
                    transitionDuration: Setting.transitionDuration,
                    arrows: Setting.arrows,
                    clickContent: Setting.clickContent,
                    clickSlide: Setting.slideclick,
                    dblclickContent: false,
                    dblclickSlide: false,
                });
            });

            if(Get_SN.ScrollOn == true && Get_SN.TextLimit == false){
                let SF_Text = e.find('.tpgb-sf-Description .tpgb-message');
                    SF_Text.each( function() {
                        if($(this)[0].clientHeight >= Get_SN.Height){
                            $(this).addClass(Get_SN.className);
                            $(this).css('height', Get_SN.Height);
                        }
                    });
            }

            if(Get_SN.FancyScroll == true && Get_SN.TextLimit == false){
                let SF_FyText = e.find('.fancybox-si .tpgb-message');
                    $(SF_FyText).addClass(Get_SN.Fancyclass);
                    $(SF_FyText).css('height', Get_SN.FancyHeight);
            }

        });        

        $(document).on( 'click' , ".tpgb-social-feed .readbtn", function() {
            let div = $(this).closest('.tpgb-message'),
                container = div.closest('.tpgb-isotope .post-loop-inner'),
                Scroll = div.closest('.tpgb-social-feed').data("scroll-normal"),
                S = div.find('.showtext');

            if(div.hasClass('show-text')){
                div.removeClass('show-text show-less');
                    $(this).html('Show More');
                div.find('.sf-dots').css('display' , 'inline');

                if(Scroll.ScrollOn == true && Scroll.TextLimit == true){
                    S.removeClass(Scroll.className);
                    S.removeAttr('style');
                }
            }else{
                div.addClass('show-text show-less');
                    $(this).html('Show Less');
                div.find('.sf-dots').css('display' , 'none');

                let SF_Text = $('.tpgb-social-feed').find(S);
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

        $(document).on( 'click' , ".fancybox-si .readbtn", function() {
            let div = $(this).closest('.fancybox-si .tpgb-message'),
                Scroll = $('.tpgb-social-feed').data("scroll-normal"),
                FcyMsg = $(this).closest('.fancybox-si .tpgb-message');

            if(div.hasClass('show-text')){
                div.removeClass('show-text show-less');
                $(this).html('Show More')
                div.find('.sf-dots').css('display' , 'inline');

                if(Scroll.FancyScroll == true && Scroll.TextLimit == true){
                    FcyMsg.removeClass(Scroll.Fancyclass);
                    FcyMsg.removeAttr('style');
                }
            }else{
                div.addClass('show-text show-less');
                $(this).html('Show Less')
                div.find('.sf-dots').css('display' , 'none');

                if(Scroll.FancyScroll == true && Scroll.TextLimit == true){
                    FcyMsg.each( function() {
                        let $this = $(this);
                        if($this[0].clientHeight >= Scroll.FancyHeight){
                            $this.addClass(Scroll.Fancyclass);
                            $this.css('height', Scroll.FancyHeight);
                        }
                    });
                }
            }
        });
        
		
		//Load More
        var i = 0;
        if($('.tpgb-social-feed').find('.tpgb-feed-load-more').length > 0){
            $('.feed-load-more').on('click', function(e) {
                e.preventDefault();
                var loadFeed_click = $(this),
					loadFeed = loadFeed_click.data('loadattr'),
					display = loadFeed_click.attr('data-display'),
					loadFview = loadFeed_click.data('loadview'),
					loadclass = loadFeed_click.data('loadclass'),
					loadlayout = loadFeed_click.data('layout'),
					loadloadingtxt = loadFeed_click.data('loadingtxt'),
					current_text = loadFeed_click.text();
					
					if (loadFeed_click.data('requestRunning')) { 
                        return; 
                    }
					loadFeed_click.data('requestRunning', true); 
                        $.ajax({
                            type:'POST',
							data: 'action=tpgb_feed_load&view='+display+'&loadFview='+loadFview+'&loadattr='+loadFeed,
                            url:tpgb_load.ajaxUrl,
                            beforeSend: function() {
                                $(loadFeed_click).text(loadloadingtxt);
                            },
                            success: function(data) { 
                                let HtmlData = (data && data.HTMLContent) ? data.HTMLContent : '',
                                    totalFeed = (data && data.totalFeed) ? data.totalFeed : '',
                                    FilterStyle = (data && data.FilterStyle) ? data.FilterStyle : '',
                                    Allposttext = (data && data.allposttext) ? data.allposttext : '';
                                if (data == '') {
                                    $(loadFeed_click).addClass("hide");
                                } else {
                                    let BlockClass = '.tpgb-block-'+loadclass,
                                        CategoryClass = $(BlockClass + " .all .tpgb-category-count"),
                                        PostLoopClass = $(BlockClass+" .post-loop-inner");
                                        PostLoopClass.append(HtmlData);
										
                                    let Totalcount = $(BlockClass).find('.grid-item').length;
                                        $(CategoryClass).html("").append(Totalcount);

                                    if (FilterStyle == 'style-2' || FilterStyle == 'style-3') {
                                        let Categoryload = $(BlockClass + ' .tpgb-filter-list .tpgb-category-list').not('.all');

                                        $.each(Categoryload, function(key, value) {
                                            let span2 = $(value).find('span:nth-child(2)').data('hover'),
                                                Toatal2 = $(BlockClass).find('.grid-item.' + span2).length;
                                            $(value).find('span:nth-child(1).tpgb-category-count').html("").append(Toatal2);
                                        });
                                    }
                                    if (loadlayout == 'grid' || loadlayout == 'masonry') {
                                        if ($(BlockClass).hasClass("tpgb-isotope")) {
                                            $(PostLoopClass).isotope('layout').isotope('reloadItems');
                                        }
                                    }

                                    if (Totalcount >= totalFeed) {
                                        $(loadFeed_click).addClass("hide");
                                        $(loadFeed_click).parent(".tpgb-feed-load-more").append('<div class="tpgb-feed-loaded">' + Allposttext + '</div>');
                                    } else {
                                        $(loadFeed_click).text(current_text);
                                    }
                                }
                                display=Number(display)+Number(loadFview);
								loadFeed_click.attr("data-display", display);
                            },
                            complete: function() {
                                loadFeed_click.data('requestRunning', false);
                                if (loadlayout == 'grid' || loadlayout == 'masonry') {
                                    if (!$("." + loadFeed.load_class).hasClass("list-grid-client")) {
                                        setTimeout(function() {
                                            $(".tpgb-block-" + loadclass + ' .post-loop-inner').isotope('layout').isotope('reloadItems');
                                        }, 500);
                                    }
                                }
                            }
                        }).then(function(){
                            if ($(".tpgb-block-" + loadclass).hasClass("tpgb-isotope")) {
                                if (loadlayout == 'grid' || loadlayout == 'masonry') {
                                    var container = $(".tpgb-block-" + loadclass + ' .post-loop-inner')
                                    container.isotope({
                                        itemSelector: ".grid-item",
                                        resizable: !0,
                                        sortBy: "original-order"
                                    });
                                }

                                var fancySplide = document.querySelectorAll(".tpgb-block-" + loadclass+" .tpgb-carousel:not(.is-initialized)");
                                if(fancySplide){
                                    fancySplide.forEach(function(obj) {
                                        if (typeof splide_init === "function") { 
                                            splide_init(obj)
                                        }
                                    });
                                }
                            }
                            
                        })
						
            });
        }

        
        if($('body').find('.tpgb-feed-lazy-load').length){
            var windowWidth, windowHeight, documentHeight, scrollTop, containerHeight, containerOffset, $window = $(window);
            var recalcValues = function() {
                windowWidth = $window.width();
                windowHeight = $window.height();
                documentHeight = $('body').height();
                containerHeight = $('.tpgb-isotope').height();
                containerOffset = $('.tpgb-isotope').offset().top+50;
                setTimeout(function(){
                    containerHeight = $('.tpgb-isotope').height();
                    containerOffset = $('.tpgb-isotope').offset().top+50;
                }, 50);
            };
            
            recalcValues();
            $window.resize(recalcValues);
            $window.bind('scroll', function(e) {
                e.preventDefault();
                recalcValues();
                scrollTop = $window.scrollTop();
                $('.tpgb-isotope').each(function() {
                    containerHeight = $(this).height();
                    containerOffset = $(this).offset().top+50;
                    if($(this).find(".tpgb-feed-lazy-load").length && scrollTop < documentHeight && (scrollTop+60 > (containerHeight + containerOffset - windowHeight)) ){

                        var lazyFeed_click = $(this).find(".feed-lazy-load"),
							lazyFeed = lazyFeed_click.data('lazyattr'),
							totalfeed = lazyFeed_click.data('totalfeed'),
							display = lazyFeed_click.attr('data-display'),
							loadFview = lazyFeed_click.data('lazyview'),
							loadclass = lazyFeed_click.data('lazyclass'),
							loadlayout = lazyFeed_click.data('lazylayout'),
							loadloadingtxt = lazyFeed_click.data('loadingtxt'),
							current_text = lazyFeed_click.text();
							
							if (lazyFeed_click.data('requestRunning')) {
                                return;
                            }
							lazyFeed_click.data('requestRunning', true);
							if(totalfeed>=display){
								$.ajax({
									type:'POST',
									data:'action=tpgb_feed_load&view='+display+'&loadFview='+loadFview+'&loadattr='+lazyFeed,
									url:tpgb_load.ajaxUrl,
									beforeSend: function() {
										$(lazyFeed_click).text(loadloadingtxt);
									},
									success: function(data) {
										let HtmlData = (data && data.HTMLContent) ? data.HTMLContent : '',
											totalFeed = (data && data.totalFeed) ? data.totalFeed : '',
											FilterStyle = (data && data.FilterStyle) ? data.FilterStyle : '',
											Allposttext = (data && data.allposttext) ? data.allposttext : '';
										
										if(data==''){
											$(lazyFeed_click).addClass("hide");
										}else{
											let BlockClass = '.tpgb-block-' + loadclass,
												CategoryClass = $(BlockClass + " .all .tpgb-category-count"),
												PostLoopClass = $(BlockClass + " .post-loop-inner");
												PostLoopClass.append(HtmlData);
												
											let Totalcount = $(BlockClass).find('.grid-item').length;
												$(CategoryClass).html("").append(Totalcount);
		
											if (FilterStyle == 'style-2' || FilterStyle == 'style-3') {
												let Categoryload = $(BlockClass+' .tpgb-filter-list .tpgb-category-list').not('.all');
												
												$.each(Categoryload, function(key, value) {
													let span2 = $(value).find('span:nth-child(2)').data('hover'),
														Toatal2 = $(BlockClass).find('.grid-item.' + span2).length;
														$(value).find('span:nth-child(1).tpgb-category-count').html("").append(Toatal2);
												});
											}
											if (loadlayout == 'grid' || loadlayout == 'masonry') {
												if ($(BlockClass).hasClass("tpgb-isotope")) {
													$(PostLoopClass).isotope('layout').isotope('reloadItems');
												}
											}
											
											if (Totalcount >= totalFeed) {
												if(lazyFeed_click.next('.tpgb-feed-loaded').length==0){
													$(lazyFeed_click).addClass("hide");
													$(lazyFeed_click).parent(".tpgb-feed-lazy-load").append('<div class="tpgb-feed-loaded">' + Allposttext + '</div>');
												}
												
											} else {
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
									if ($(".tpgb-block-" + loadclass).hasClass("tpgb-isotope")) {
										if (loadlayout == 'grid' || loadlayout == 'masonry') {
											var container = $(".tpgb-block-" + loadclass + ' .post-loop-inner')
                                            container.isotope({
                                                itemSelector: ".grid-item",
                                                resizable: !0,
                                                sortBy: "original-order"
                                            });
										}

                                        var fancySplide = document.querySelectorAll(".tpgb-block-" + loadclass+" .tpgb-carousel:not(.is-initialized)");
                                        if(fancySplide){
                                            fancySplide.forEach(function(obj) {
                                                if (typeof splide_init === "function") { 
                                                    splide_init(obj)
                                                }
                                            });
                                        }
									}
								});
							}
                    }
                });
            });
        }

})(jQuery);