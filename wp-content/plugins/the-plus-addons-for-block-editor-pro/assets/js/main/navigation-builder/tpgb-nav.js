( function( $ ) {
	'use strict';
		/*Url based on Active Menu*/
		if($(".tpgb-navbuilder").length){
			$(".tpgb-navbuilder a").each(function(){
				var pathname = location.pathname;
					pathname = pathname.substr(pathname.indexOf('/') + 1);
					if ($(this).attr("href") == window.location.href.replace(/\/$/, '')){
						$(this).closest("li").addClass("active");
					}else if(pathname && $(this).attr("href") && $(this).attr("href").indexOf(pathname) > -1){
						$(this).closest("li").addClass('active');
					}
			});
		}
        /* Js For Show SubMenu on Click  */
        if($(".tpgb-nav-wrap .tpgb-nav-inner.menu-click").length>=1){
            $('.tpgb-nav-wrap .menu-click .tpgb-nav-item .navbar-nav li.menu-item-has-children > a').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if($(this).closest(".tpgb-nav-inner.menu-click")){
                    var navSideBut = $(this), 
                    navSideItem = navSideBut.parent(),
                    navSideUl = navSideBut.parent().parent(),
                    navSideItemSub = navSideItem.find('> ul.dropdown-menu');

                    if (navSideItem.hasClass('open')) {
                        navSideItemSub.slideUp(400);
                        navSideItemSub.removeClass('open-menu')			
                        navSideItem.removeClass('open');
                    } else {
                        navSideUl.css("height","auto");
                        navSideUl.find('li.dropdown.open ul.dropdown-menu').slideUp(400);
                        navSideUl.find('li.dropdown-submenu.open ul.dropdown-menu').slideUp(400);
                        navSideUl.find('li.dropdown,li.dropdown-submenu.open').removeClass('open');
                        navSideItemSub.slideDown(400);
                        navSideItemSub.addClass('open-menu');
                        navSideItem.addClass('open');
                    }
                }
                if(navSideUl.find('li.dropdown.open ul.dropdown-menu .tpgb-carousel').length){
                    let splideDiv = navSideUl.find('li.dropdown.open ul.dropdown-menu'),
                        scope = splideDiv[0].querySelectorAll('.tpgb-carousel');
                        navsplider(scope)
                }

                // Grid Layout For Mega Menu
                if( navSideUl.find('li.dropdown.open ul.dropdown-menu .tpgb-isotope .post-loop-inner').length){
                   navSideUl.find('li.dropdown.open ul.dropdown-menu .tpgb-isotope .post-loop-inner').isotope('layout');
                }

            });
            $(document).on('mouseup' , function (e) {
                var menu = $('li.dropdown');
                if (!menu.is(e.target) && menu.has(e.target).length === 0){
                    menu.find('ul.dropdown-menu').slideUp(400);
                    menu.find('li.dropdown-submenu.open ul.dropdown-menu').slideUp(400);
                    menu.removeClass('open');			
            }
            });
        }
        /* Js For Show SubMenu on Hover  */

        if($(".tpgb-nav-inner").hasClass("menu-hover")){
            $(".tpgb-nav-wrap .menu-hover .navbar-nav .dropdown").on('mouseenter',function() {
                var container =$(this).closest(".tpgb-nav-inner");
                var transition_style=container.data("menu_transition");
        
                if(transition_style=='' || transition_style=='style-1'){
                    $(this).find("> .dropdown-menu").stop().slideDown();
                }else if(transition_style=='style-2'){
                    $(this).find("> .dropdown-menu").fadeIn(600);
                    if($(this).find(".dropdown-menu .tpgb-carousel").length){
                        let splideDiv = $(this).find('.dropdown-menu'),
                        scope = splideDiv[0].querySelectorAll('.tpgb-carousel');
                        navsplider(scope)
                    }
                }else if(transition_style=='style-3' || transition_style=='style-4'){
                    $(this).find("> .dropdown-menu").addClass("open-menu");			
                }

                if($(this).find(".dropdown-menu .tpgb-carousel").length){
                    let splideDiv = $(this).find('.dropdown-menu'),
                    scope = splideDiv[0].querySelectorAll('.tpgb-carousel');
                    navsplider(scope)
                }

                // Grid Layout For Mega Menu
                if($(this).find("> .dropdown-menu .tpgb-isotope .post-loop-inner").length){
                    $(this).find("> .dropdown-menu .tpgb-isotope .post-loop-inner").isotope('layout');
                }
                
            }).on('mouseleave', function() {
                var container =$(this).closest(".tpgb-nav-inner");
                var transition_style=container.data("menu_transition");
                
                if(transition_style=='' || transition_style=='style-1'){
                    $(this).find("> .dropdown-menu").stop().slideUp();
                }else if(transition_style=='style-2'){
                    $(this).find("> .dropdown-menu").stop(true, true).delay(100).fadeOut(400);
                }else if(transition_style=='style-3' || transition_style=='style-4'){
                    $(this).find("> .dropdown-menu").removeClass("open-menu");			
                }
                
            });
            $(".tpgb-nav-wrap .menu-hover .navbar-nav .dropdown-submenu").on('mouseenter',function() {
                
                var container =$(this).closest(".tpgb-nav-inner");
                var transition_style=container.data("menu_transition");
                
                if(transition_style=='' || transition_style=='style-1'){
                    $(this).find("> .dropdown-menu").stop().slideDown();
                }else if(transition_style=='style-2'){
                $(this).find("> .dropdown-menu").stop(true, true).delay(100).fadeIn(600);
                }else if(transition_style=='style-3' || transition_style=='style-4'){
                    $(this).find("> .dropdown-menu").addClass("open-menu");
                }

                if($(this).find("> .dropdown-menu .tpgb-carousel").length){
                    let splideDiv = $(this).find('.dropdown-menu'),
                    scope = splideDiv[0].querySelectorAll('.tpgb-carousel');
                    navsplider(scope)
                }
                
            }).on('mouseleave', function() {
                var container =$(this).closest(".tpgb-nav-inner");
                var transition_style=container.data("menu_transition");
                
                if(transition_style=='' || transition_style=='style-1'){
                    $(this).find("> .dropdown-menu").stop().slideUp();
                }else if(transition_style=='style-2'){
                    $(this).find("> .dropdown-menu").stop(true, true).delay(100).fadeOut(400);
                }else if(transition_style=='style-3' || transition_style=='style-4'){
                    $(this).find("> .dropdown-menu").removeClass("open-menu");
                }
            });	
        }

        /* Js For Show Menu on Hover& Click In Vertical Sidebar  */


        if($(".tpgb-nav-item.vertical-side.toggle-click").length > 0){
            $(".vertical-side.toggle-click .vertical-side-toggle").on("click",function(a){
                a.preventDefault(),
                a.stopPropagation();
                $(this).closest(".toggle-click").toggleClass("tp-click");
            });
        }
        if($(".tpgb-nav-item.vertical-side.toggle-hover").length > 0){
            $(".vertical-side.toggle-hover").on('mouseenter',function() {
                $(this).addClass("tp-hover");
            }).on('mouseleave', function() {
                $(this).removeClass("tp-hover");
            });
        }
        /* Js For Show Menu on Hover& Click In Vertical Sidebar  */

      /* Js For Toggle Class On Click on the toggle icon  */

        if($(".tpgb-toggle-menu.hamburger-toggle").length > 0){
            $(".tpgb-toggle-menu.hamburger-toggle ").on('click',function() {
                var target = $(this).data("target");
                $(this).toggleClass("open-menu");
                if ($(target +'.collapse:not(".in")').length) {
                    $(target +'.collapse:not(".in")').slideDown(400);
                    $(target +'.collapse:not(".in")').addClass('in');
                } else {
                    $(target + '.collapse.in').slideUp(400);
                    $(target +'.collapse.in').removeClass('in');
                }
            });
        }
        if($('.tpgb-mobile-menu.tpgb-menu-toggle:not(.navigation-custom)').length){
			$(".tpgb-mobile-menu.tpgb-menu-toggle:not(.navigation-custom)").each(function(){
				var offeset=$(this).closest(".tpgb-nav-wrap");
				var window_width = $(window).width();
				var menu_content=$(this);
				var offset_left = 0 - offeset.offset().left;
				
					if($('body').hasClass("rtl")){
						menu_content.css({
								right: offset_left,
								"box-sizing": "border-box",
								width: window_width
						});
					}else{
						menu_content.css({
								left: offset_left,
								"box-sizing": "border-box",
								width: window_width
						});
					}
					
			});
		}

        $(".tpgb-mobile-menu.tpgb-menu-toggle .navbar-nav li.menu-item-has-children > a , .tpgb-mobile-menu.tpgb-menu-off-canvas .navbar-nav li.menu-item-has-children > a").on("click", function(a) {
            a.preventDefault(),
            a.stopPropagation();
            var b = $(this)
            , c = b.parent()
            , d = b.parent().parent()
            , e = c.find("> ul.dropdown-menu");
            c.hasClass("open") ? (e.slideUp(400),
            c.removeClass("open")) : (d.css("height", "auto"),
            d.find("li.dropdown.open ul.dropdown-menu").slideUp(400),
            d.find("li.dropdown-submenu.open ul.dropdown-menu").slideUp(400),
            d.find("li.dropdown,li.dropdown-submenu.open").removeClass("open"),
            e.slideDown(400),
            c.addClass("open"))

            if( d.find("li.dropdown.open ul.dropdown-menu .tpgb-carousel").length){
                let splideDiv = d.find("li.dropdown.open ul.dropdown-menu"),
                scope = splideDiv[0].querySelectorAll('.tpgb-carousel');
                navsplider(scope)
            }
        });
        /* Js For Toggle Class On Click on the toggle icon  */

        /* Js For Off Canvas  */
        if($(".tpgb-toggle-menu.hamburger-off-canvas").length > 0){
            var mouse_click = $(document).find(".tpgb-nav-inner").data("mobile-menu-click"),
            bodyselect = '';
            $(document).on('click','.tpgb-toggle-menu.hamburger-off-canvas ',function(){ 
                $(this).addClass("open-menu");
                $('body').addClass('mobile-menu-open');
                var b = $(this),
                c = b.parent().parent();
                c.find('.tpgb-mobile-menu.tpgb-menu-off-canvas').addClass('is-active')
            })
            

            if(mouse_click == 'yes'){
                $(document).on('click','.mobile-menu-open ,.tpgb-mobile-menu.tpgb-menu-off-canvas .close-menu',function(e){
                ///$('.mobile-menu-open:not(.tpgb-search-bar),.tpgb-mobile-menu.tpgb-menu-off-canvas .close-menu').click(function(){
                    var p = $(this),
                    c = p.find('.tpgb-mobile-menu.tpgb-menu-off-canvas'),
                    d = c.parent();

                    if(!$('.tpgb-search-input').is(e.target) && $('.tpgb-search-input').has(e.target).length === 0){
                        if(c.hasClass('is-active')) {
                            c.removeClass('is-active');
                            p.removeClass('mobile-menu-open');
                            d.find(".tpgb-toggle-menu.hamburger-off-canvas").removeClass('open-menu');
                        }
                    }
                })
            }else{
                $(document).on('click','.tpgb-mobile-menu.tpgb-menu-off-canvas .close-menu',function(){
                    var p = $(this).parent();
                    $("body").removeClass("mobile-menu-open");
                    if(p.hasClass('is-active')) {
                        p.removeClass('is-active');
                        p.prev().find(".tpgb-toggle-menu.hamburger-off-canvas").removeClass('open-menu');
                    }
                   
                });
            }
           
           
        }
        /* Js For Off Canvas  */

        /*Toggle class For Opacity in Main Menu & sub Menu */
 
        if($('.tpgb-nav-item').find(".hover-inverse-effect").length >0){
            $(".tpgb-nav-item .nav > li").on({
            mouseenter: function() {
                $( this ).closest(".hover-inverse-effect").addClass("is-hover-inverse");
                $( this ).addClass( "is-hover" );
            }, mouseleave: function() {
                $( this ).closest(".hover-inverse-effect").removeClass("is-hover-inverse");
                $( this ).removeClass( "is-hover" );
            }
            });
        }
        if($('.tpgb-nav-item ').find(".submenu-hover-inverse-effect").length >0){
            $(".tpgb-nav-item .nav li.dropdown .dropdown-menu > li").on({
            mouseenter: function() {
                $( this ).closest(".submenu-hover-inverse-effect").addClass("is-submenu-hover-inverse");
                $( this ).addClass( "is-hover" );
            }, mouseleave: function() {
                $( this ).closest(".submenu-hover-inverse-effect").removeClass("is-submenu-hover-inverse");
                $( this ).removeClass( "is-hover" );
            }
            });
        }
     /*Toggle class For Opacity in Main Menu & sub Menu */

    /* Js For Mega Menu */
    if($('.tpgb-nav-item .tpgb-dropdown-container').length > 0 || $('.tpgb-nav-item .tpgb-dropdown-full-width').length > 0){
	
		var left_offset=0;
	
		if( $('.tpgb-nav-item .tpgb-dropdown-container').length > 0 ) {
			$('.tpgb-nav-item .tpgb-dropdown-container').each(function(){
				var cthis =$(this);
				//Horizontal Menu
				var vertical_menu = cthis.closest('.vertical-side');
				if(vertical_menu.length>0){
                    var full_width = (cthis.closest(".tpgb-section-wrap").length > 0) ? cthis.closest(".tpgb-section-wrap").width() : cthis.closest(".tpgb-container-row").width() ;
					var menu_width = vertical_menu.find(".navbar-nav").width();
					var con_width = full_width - menu_width - 20;
					var container_megamenu=$(">.dropdown-menu",cthis);
					container_megamenu.css({
							"box-sizing": "border-box",
							width: con_width
					});
				}
				if(!vertical_menu.length){
                    var cont_width= (cthis.closest(".tpgb-section-wrap").length > 0) ? cthis.closest(".tpgb-section-wrap").width() : cthis.closest(".tpgb-container-row").width() ;
                    var window_width = window.innerWidth;
                    window_width=window_width-cont_width;
					var left_offset=window_width/2;
					var offeset=cthis.closest(".tpgb-nav-wrap");
					var container_megamenu=$(">.dropdown-menu",cthis);  
                    if($('body').hasClass("rtl")){
                        var offset_right = 0 - offeset.offset().left+(left_offset);
                            container_megamenu.css({
                                right: offset_right,
                                "box-sizing": "border-box",
                                width: cont_width
                            });
                    }else{
                        var offset_left = 0 - offeset.offset().left+(left_offset);
                        container_megamenu.css({
                            left: offset_left,
                            "box-sizing": "border-box",
                            width: cont_width
                        });
                    }
				}
			});
        }
        if( $('.tpgb-nav-item .tpgb-dropdown-full-width').length > 0 ) {
			$('.tpgb-nav-item .tpgb-dropdown-full-width').each(function(){
                var cthis =$(this);
				var vertical_menu = cthis.closest('.menu-vertical-side');
				if(vertical_menu.length>0){
					var full_width= (cthis.closest(".tpgb-container").length > 0) ? cthis.closest(".tpgb-container").width() : cthis.closest(".tpgb-container-row").width() ;
					var menu_width = vertical_menu.find(".navbar-nav").width();
					var con_width = full_width - menu_width - 20;
					var container_megamenu=$(">.dropdown-menu",cthis);
					container_megamenu.css({
							"box-sizing": "border-box",
							width: con_width
					});
				}
				if(!vertical_menu.length){
					var full_width = (cthis.closest(".tpgb-container").length > 0) ? cthis.closest(".tpgb-container").width() : cthis.closest(".tpgb-container-row").width() ;
					var window_width = $(window).width();
					var offeset=cthis.closest(".tpgb-nav-wrap");
					if(offeset.length > 0){
						var offset_left = 0 - offeset.offset().left-(left_offset);
					}else{
						var offset_left = 0 - 0+(left_offset);
					}
					if($('body').hasClass("rtl")){
						var offset_left = 0 - (window_width - (offeset.offset().left + offeset.width()));
                    }
					var container_megamenu=$(">.dropdown-menu",cthis);
					
					if($('body').hasClass("rtl")){
						container_megamenu.css({
								right: offset_left,
								"box-sizing": "border-box",
								width: window_width
						});
					}else{
						container_megamenu.css({
								left: offset_left,
								"box-sizing": "border-box",
								width: window_width
						});
					}
				}
			});
		}
    }

    /* Js For Swiper Container */
    if($('.tpgb-mobile-menu.tpgb-menu-swiper').length > 0){
        new Swiper(".tpgb-mobile-menu.tpgb-menu-swiper",{
            slidesPerView: "auto",
            mousewheelControl: !0,
            freeMode: !0
        });
    }
     /* Js For Swiper Container */

    // Slider Init

    function navsplider(ele){
        ele.forEach(function(obj){
            var splideInit = slideStore.get(obj);
            splideInit.refresh();
        });
    }
    
    //close moblie menu on click on body
    var inner_width = window.innerWidth;
    if(inner_width <= 991 && $('.tpgb-mobile-menu').length){
        $(document).mouseup(function (e) {
            var container = $(".tpgb-toggle-menu");
            var mouse_click = $(e.target).find(".tpgb-nav-inner").data("mobile-menu-click");
            if(mouse_click=='yes'){					
                if (!container.is(e.target) && container.has(e.target).length === 0){
                    $(e.target).find(".tpgb-mobile-menu:not(.tpgb-menu-off-canvas)").slideUp(400);
                    $(e.target).find(".tpgb-mobile-menu").removeClass('in');
                    $(e.target).find(".tpgb-mobile-menu.tpgb-menu-off-canvas").removeClass('is-active');
                    $(e.target).find(".tpgb-nav-inner").find(".tpgb-toggle-menu").removeClass("open-menu");
                }
            }
        });
        
    }
})(jQuery);