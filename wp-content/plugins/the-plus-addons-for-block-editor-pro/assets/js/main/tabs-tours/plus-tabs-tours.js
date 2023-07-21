( function( $ ) {
	"use strict";
		$('.tpgb-tabs-wrapper').each(function(){
			var $currentTab = $(this),
			$TabHover = $currentTab.data('tab-hover'),
			$tabheader = $currentTab.find('.tpgb-tab-header');

			if('no' == $TabHover){
				if(/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream){
					$tabheader.on('touchstart', function(){
						var currentTabIndex = $(this).data("tab");
						var tabsContainer = $(this).closest('.tpgb-tabs-wrapper');
						var tabsNav = $(tabsContainer).children('.tpgb-tabs-nav').children('.tpgb-tab-li').children('.tpgb-tab-header');
						var tabsContent = $(tabsContainer).children('.tpgb-tabs-content-wrapper').children('.tpgb-tab-content');
					
						$(tabsContainer).find(">.tpgb-tabs-nav-wrapper .tpgb-tab-header").removeClass('active default-active').addClass('inactive');
						$(this).addClass('active').removeClass('inactive');
						
						$(tabsContainer).find(">.tpgb-tabs-content-wrapper>.tpgb-tab-content").removeClass('active').addClass('inactive');
						$(">.tpgb-tabs-content-wrapper>.tpgb-tab-content[data-tab='"+currentTabIndex+"']",tabsContainer).addClass('active').removeClass('inactive');
						
						//Init Splide Slider
						if($(".tpgb-tab-content[data-tab='"+currentTabIndex+"']").find(".tpgb-carousel").length){
							var scope = document.querySelectorAll(".tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-carousel");
							scope.forEach(function(obj){
								var splideInit = slideStore.get(obj);
								splideInit.refresh();
							});
						}
			
						if($(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-isotope .post-loop-inner", $currentTab).length){
                            
                            //lazy load call function document height small
                            if($('body').height() <= $(window).height()){
                                $(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-isotope").each(function(){
                                    if($(this).find(".post-lazy-load").length && typeof tpgb_lazy_load_ajax === "function"){
                                        tpgb_lazy_load_ajax($(this));
                                    }
                                });
                            }

							let curr = $(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-isotope .post-loop-inner", $currentTab);

							if(curr.data('anim') == 'no'){
								curr.isotope({ transitionDuration : 0 })
							}
							if(curr.height() == 0){
								curr.css( 'opacity' , 0 )

								curr.isotope('once', 'layoutComplete',
								function (isoInstance, laidOutItems) {
									curr.css( 'opacity' , 1 )
								});
							}
							setTimeout(function(){
								curr.isotope('layout');
							}, 10);
							
						}
						
						if($(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-metro .post-loop-inner", $currentTab).length){
                            //lazy load call function document height small
                            if($('body').height() <= $(window).height()){
                                $(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-metro").each(function(){
                                    if($(this).find(".post-lazy-load").length && typeof tpgb_lazy_load_ajax === "function"){
                                        tpgb_lazy_load_ajax($(this));
                                    }
                                });
                            }
							setTimeout(function(){						
								tpgb_metro_layout('');
							}, 30);
						}
			
						// Equal Height
						if($(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-equal-height", $currentTab).length){
							setTimeout(function(){
								var cont = $(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-equal-height", $currentTab);
								if(typeof equalHeightFun == 'function'){
									var eDiv = ( cont[0] ? cont[0] : '' );
									equalHeightFun(eDiv)
								}
							}, 30);
						}
			
						$(tabsContent).each( function(index) {
							$(this).removeClass('default-active');
						});
						if($(">.tpgb-tabs-content-wrapper>.tpgb-tab-content[data-tab='"+currentTabIndex+"'] .pt_tpgb_before_after",tabsContainer).length){
							size_Elements()
						}
						
						
					});
				}else{
					$tabheader.on('click', function(){
                        
						var currentTabIndex = $(this).data("tab");
						var tabsContainer = $(this).closest('.tpgb-tabs-wrapper');
						var tabsNav = $(tabsContainer).children('.tpgb-tabs-nav').children('.tpgb-tab-li').children('.tpgb-tab-header');
						var tabsContent = $(tabsContainer).children('.tpgb-tabs-content-wrapper').children('.tpgb-tab-content');
					
						$(tabsContainer).find(">.tpgb-tabs-nav-wrapper .tpgb-tab-header").removeClass('active default-active').addClass('inactive');
						$(this).addClass('active').removeClass('inactive');
						
						$(tabsContainer).find(">.tpgb-tabs-content-wrapper>.tpgb-tab-content").removeClass('active').addClass('inactive');
						$(">.tpgb-tabs-content-wrapper>.tpgb-tab-content[data-tab='"+currentTabIndex+"']",tabsContainer).addClass('active').removeClass('inactive');
						
						//Init Splide Slider
						if($(".tpgb-tab-content[data-tab='"+currentTabIndex+"']").find(".tpgb-carousel").length){
							var scope = document.querySelectorAll(".tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-carousel");
							scope.forEach(function(obj){
								var splideInit = slideStore.get(obj);
								splideInit.refresh();
							});
						}
			
						if($(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-isotope .post-loop-inner", $currentTab).length){
                            
                            //lazy load call function document height small
                            if($('body').height() <= $(window).height()){
                                $(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-isotope").each(function(){
                                    if($(this).find(".post-lazy-load").length && typeof tpgb_lazy_load_ajax === "function"){
                                        tpgb_lazy_load_ajax($(this));
                                    }
                                });
                            }

							let curr = $(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-isotope .post-loop-inner", $currentTab);	

							if(curr.data('anim') == 'no'){
								curr.isotope({ transitionDuration : 0 })
							}

							if(curr.height() == 0){
								curr.css( 'opacity' , 0 )

								curr.isotope('once', 'layoutComplete',
								function (isoInstance, laidOutItems) {
									curr.css( 'opacity' , 1 )
								});
							}

							setTimeout(function(){
								curr.isotope('layout');
							}, 10);
							
						}
						
						if($(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-metro .post-loop-inner", $currentTab).length){
                            //lazy load call function document height small
                            if($('body').height() <= $(window).height()){
                                $(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-metro").each(function(){
                                    if($(this).find(".post-lazy-load").length && typeof tpgb_lazy_load_ajax === "function"){
                                        tpgb_lazy_load_ajax($(this));
                                    }
                                });
                            }
							setTimeout(function(){						
								tpgb_metro_layout('');
							}, 30);
						}
			
						// Equal Height
						if($(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-equal-height", $currentTab).length){
							setTimeout(function(){
								var cont = $(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-equal-height", $currentTab);
								if(typeof equalHeightFun == 'function'){
									var eDiv = ( cont[0] ? cont[0] : '' );
									equalHeightFun(eDiv)
								}
							}, 30);
						}
			
						$(tabsContent).each( function(index) {
							$(this).removeClass('default-active');
						});
						if($(">.tpgb-tabs-content-wrapper>.tpgb-tab-content[data-tab='"+currentTabIndex+"'] .pt_tpgb_before_after",tabsContainer).length){
							size_Elements()
						}
						
						
					});
				}
				
			}
			if('yes' == $TabHover){
				$tabheader.on('mouseover',function(){
					var currentTabIndex = $(this).data("tab");
					var tabsContainer = $(this).closest('.tpgb-tabs-wrapper');
					var tabsNav = $(tabsContainer).children('.tpgb-tabs-nav').children('.tpgb-tab-li').children('.tpgb-tab-header');
					var tabsContent = $(tabsContainer).children('.tpgb-tabs-content-wrapper').children('.tpgb-tab-content');
				
					$(tabsContainer).find(">.tpgb-tabs-nav-wrapper .tpgb-tab-header").removeClass('active default-active').addClass('inactive');
					$(this).addClass('active').removeClass('inactive');
				
					$(tabsContainer).find(">.tpgb-tabs-content-wrapper>.tpgb-tab-content").removeClass('active').addClass('inactive');
					$(">.tpgb-tabs-content-wrapper>.tpgb-tab-content[data-tab='"+currentTabIndex+"']",tabsContainer).addClass('active').removeClass('inactive');
					
					//Init Splide Slider
					if($(".tpgb-tab-content[data-tab='"+currentTabIndex+"']").find(".tpgb-carousel").length){
						var scope = document.querySelectorAll(".tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-carousel");
                        scope.forEach(function(obj){
                            var splideInit = slideStore.get(obj);
                            splideInit.refresh();
                        });
					}

					if($(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-isotope .post-loop-inner", $currentTab).length){
                        
                        //lazy load call function document height small
                        if($('body').height() <= $(window).height()){
                            $(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-isotope").each(function(){
                                if($(this).find(".post-lazy-load").length && typeof tpgb_lazy_load_ajax === "function"){
                                    tpgb_lazy_load_ajax($(this));
                                }
                            });
                        }
                        
						let curr = $(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-isotope .post-loop-inner", $currentTab);

						if(curr.data('anim') == 'no'){
							curr.isotope({ transitionDuration : 0 })
						}

						if(curr.height() == 0){
							curr.css( 'opacity' , 0 )

							curr.isotope('once', 'layoutComplete',
							function (isoInstance, laidOutItems) {
								curr.css( 'opacity' , 1 )
							});
						}
						
						setTimeout(function(){
							curr.isotope('layout');
						}, 10);
						
					}
					
                    if($(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-metro .post-loop-inner", $currentTab).length){
                        //lazy load call function document height small
                        if($('body').height() <= $(window).height()){
                            $(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-metro").each(function(){
                                if($(this).find(".post-lazy-load").length && typeof tpgb_lazy_load_ajax === "function"){
                                    tpgb_lazy_load_ajax($(this));
                                }
                            });
                        }
						setTimeout(function(){						
							tpgb_metro_layout('');
						}, 30);
					}

					$(tabsContent).each( function(index) {
						$(this).removeClass('default-active');
					});
					if($(">.tpgb-tabs-content-wrapper>.tpgb-tab-content[data-tab='"+currentTabIndex+"'] .pt_tpgb_before_after",tabsContainer).length){
						size_Elements()
					}
					
					// Equal Height
					if($(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-equal-height", $currentTab).length){
						setTimeout(function(){
							var cont = $(" .tpgb-tab-content[data-tab='"+currentTabIndex+"'] .tpgb-equal-height", $currentTab);
							if(typeof equalHeightFun == 'function'){
								var eDiv = ( cont[0] ? cont[0] : '' );
								equalHeightFun(eDiv)
							}
						}, 30);
					}
					
				});
			}

			var hash = window.location.hash;
			if(hash!='' && hash!=undefined && !$(hash).hasClass("active") && $(hash).length){
				$('html, body').animate({
					scrollTop: $(hash).offset().top
				}, 1500);
				$(hash+".tpgb-tab-header").trigger("click");
				var tab_index=$(hash).data("tab");
				$(".tab-mobile-title[data-tab='"+tab_index+"']").trigger("click");
			}

		});
		/*
		 *	Swiper Tabbing
		*/
		if($('.tpgb-tabs-wrapper.swiper-container').length > 0){
			new Swiper(".tpgb-tabs-wrapper.swiper-container",{
				slidesPerView: "auto",
				mousewheelControl: !0,
				freeMode: !0,
			});
		}
		
		if($('.tpgb-tabs-wrapper').hasClass("mobile-accordion")){
			$(window).on('resize',function(){
				if($(window).innerWidth() <= 600){
					$('.tpgb-tabs-wrapper').addClass("mobile-accordion-tab");
				}
			});
			$('.tpgb-tabs-content-wrapper .tab-mobile-title').on('click',function(){
				var currentTabIndex = $(this).data("tab");
				var tabsContainer = $(this).closest('.tpgb-tabs-wrapper');
				var tabsNav = $(tabsContainer).children('.tpgb-tabs-content-wrapper').children('.tab-mobile-title');
				var tabsContent = $(tabsContainer).children('.tpgb-tabs-content-wrapper').children('.tpgb-tab-content');
			
				$(tabsContainer).find(">.tpgb-tabs-content-wrapper .tab-mobile-title").removeClass('active default-active').addClass('inactive');
				$(this).addClass('active').removeClass('inactive');
			
				$(tabsContainer).find(">.tpgb-tabs-content-wrapper>.tpgb-tab-content").removeClass('active').addClass('inactive');
				$(">.tpgb-tabs-content-wrapper>.tpgb-tab-content[data-tab='"+currentTabIndex+"']",tabsContainer).addClass('active').removeClass('inactive');
			
				$(tabsContent).each( function(index) {
					$(this).removeClass('default-active');
				});
			});
		}

})(jQuery);