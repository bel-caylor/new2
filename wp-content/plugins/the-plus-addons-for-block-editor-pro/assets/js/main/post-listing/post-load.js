/*----load more post ajax----------------*/
( function($) {
	'use strict';

    if( $(".tpgb-load-more").length && ( $('.tpgb-isotope').length || $('.tpgb-metro').length ) ){
        $('.post-load-more').each(function(){

            var current = $(this);
            current.on('click', function(e) {
                e.preventDefault();
                var current_click= $(this),
                    option = current_click.data('dypost'),
                    stoption = current_click.data('post-option'),
                    current_text = current_click.text();

                    if ( current_click.data('requestRunning') ) {
                        return;
                    }
                    if(option.offset_posts==undefined || option.offset_posts==""){
                        option.offset_posts=0;
                    }
                    current_click.data('requestRunning', true);
                    if(option.total_page >= option.page){			
                        option.offset=(parseInt(option.page-1)*parseInt(option.load_more))+parseInt(option.display_post)+parseInt(option.offset_posts);
                        
                        $.ajax({
                            type:'POST',
                            data: { 
                                action : 'tpgb_post_load',
                                option : stoption,
                                dyOpt : option,
                            },
                            url: ( tpgb_config && tpgb_config.ajax_url ) ? tpgb_config.ajax_url : tpgb_load.ajaxUrl,
                            beforeSend: function() {
                                $(current_click).text(option.loadingtxt);
                            },
                            success: function(data) {  
                                if(data==''){
                                    $(current_click).addClass("hide");
                                }else{
                                    $("#"+option.load_class+' > .post-loop-inner').append( data );
                                    if(option.layout=='grid' || option.layout=='masonry'){
                                        if($("#"+option.load_class).hasClass("tpgb-isotope")){
                                            var $newItems = $('');
                                            $("#"+option.load_class+' > .post-loop-inner').isotope( 'insert', $newItems );
                                            $("#"+option.load_class+' > .post-loop-inner').isotope( 'layout' ).isotope( 'reloadItems' ); 
                                        }
                                    }
                                }
                                option.page++;
                                if(option.page==option.total_page){
                                    $(current_click).addClass("hide");
                                    $(current_click).attr('data-page', option.page);
                                    $(current_click).parent(".tpgb-load-more").append('<div class="tpgb-post-loaded">'+option.loaded_posts+'</div>');
                                }else{
                                    $(current_click).text(current_text);
                                    $(current_click).attr('data-page', option.page);
                                }
                                
                            },
                            complete: function() {
                                if($("#"+option.load_class+' .tpgb-category-filter').length){
                                    $("#"+option.load_class+' .tpgb-filter-data .tpgb-categories > .tpgb-filter-list > a').each(function(){
                                        var filter = $(this).data("filter");
                                        if(filter!='' && filter!=undefined && filter==='*'){
                                            var totle_count = $("#"+option.load_class+' .post-loop-inner .grid-item').length;
                                        }else if(filter!='' && filter!=undefined){
                                            var totle_count = $("#"+option.load_class+' .post-loop-inner .grid-item'+filter).length;
                                        }
                                        if(totle_count){
                                            $(this).find(".tpgb-category-count").text(totle_count);
                                        }
                                    });
                                }
                                if(option.style == 'style-1') {
                                    var container = $("#"+option.load_class);
                                    if(container.hasClass('dynamic-style-1')){
                                        $('.dynamic-style-1 .grid-item .dynamic-list-content').on('mouseenter',function() {
                                            $(this).find(".tpgb-post-hover-content").slideDown(300)				
                                        });
                                        $('.dynamic-style-1 .grid-item .dynamic-list-content ').on('mouseleave',function() {
                                            $(this).find(".tpgb-post-hover-content").slideUp(300)				
                                        });
                                    }
                                }
                                
                                if($("#"+option.load_class).hasClass("tpgb-isotope")){
                                    if(option.layout=='grid' || option.layout=='masonry'){
                                        let container = $("#"+option.load_class),
                                            innerDiv = container.find('.post-loop-inner');
                                            
                                            innerDiv.isotope({
                                                itemSelector: ".grid-item",
                                                resizable: !0,
                                                sortBy: "original-order",
                                                transitionDuration:  (option.disableAnim) ? 0 : '0.4s' ,
                                                resizesContainer: true,
                                            });
                                            innerDiv.isotope('layout');
                                            setTimeout(function(){
                                                innerDiv.isotope('layout');
                                            }, 30);
                                    }
                                }

                                if($("#"+option.load_class).hasClass("tpgb-metro")){
                                    tpgb_metro_layout('all');
                                    $('.tpgb-metro .post-loop-inner').isotope({
                                        itemSelector: ".grid-item",
                                        resizable: !0,
                                        sortBy: "original-order",
                                        transitionDuration:  (option.disableAnim) ? 0 : '0.4s' ,
                                    })
                                    $('.tpgb-metro .post-loop-inner').isotope('layout').isotope( 'reloadItems' );
                                }

                                if($("#"+option.load_class).hasClass("tpgb-equal-height")){
                                    if(typeof equalHeightFun == 'function'){
                                        var eDiv = document.getElementById(option.load_class);
                                        equalHeightFun(eDiv)
                                    }
                                }

                                if($("#"+option.load_class).find('.tpgb-messagebox').length ){
                                    let msgbDiv = $("#"+option.load_class).find('.tpgb-messagebox');
                                    msgbDiv.each(function(obj){
                                        let $this = $(this),
                                            disBtn = $this.find('.msg-dismiss-content');
                                        if(disBtn.length > 0){
                                            disBtn.on('click', function(e) {
                                                $this.slideUp(500);
                                            })
                                        }
                                    })
                                }
                                current_click.data('requestRunning', false);

                                if($("#"+option.load_class).find('.tpgb-fancy-popup').length ){
                                    let fancyDiv = $("#"+option.load_class).find('.tpgb-fancy-popup');
                                    fancyDiv.each(function(){
                                        tpgb_fancy_popup($(this))
                                    })
                                }
                            }
                            }).then(function(){
                                if($("#"+option.load_class).hasClass("tpgb-isotope")){
                                    if(option.layout=='grid' || option.layout=='masonry'){
                                        var container = $("#"+option.load_class+' > .post-loop-inner');

                                        container.isotope({
                                            itemSelector: ".grid-item",
                                            resizable: !0,
                                            sortBy: "original-order",
                                            resizesContainer: true,
                                            transitionDuration:  (option.disableAnim) ? 0 : '0.4s' ,
                                        });						
                                    }
                                    container.isotope('layout');
                                    setTimeout(function(){
                                        container.isotope('layout');
                                    }, 30);
                                }
                                if($("#"+option.load_class).hasClass("tpgb-metro")){
                                    tpgb_metro_layout('all');
                                    $('.tpgb-metro .post-loop-inner').isotope({
                                        itemSelector: ".grid-item",
                                        resizable: !0,
                                        sortBy: "original-order",
                                        transitionDuration:  (option.disableAnim) ? 0 : '0.4s' ,
                                    })
                                    $('.tpgb-metro .post-loop-inner').isotope('layout').isotope( 'reloadItems' );
                                }

                                if($("#"+option.load_class).hasClass("tpgb-equal-height")){
                                    if(typeof equalHeightFun == 'function'){
                                        var eDiv = document.getElementById(option.load_class);
                                        equalHeightFun(eDiv)
                                    }
                                }
                                if($("#"+option.load_class).find('.tpgb-messagebox').length ){
                                    let msgbDiv = $("#"+option.load_class).find('.tpgb-messagebox');
                                    msgbDiv.each(function(obj){
                                        let $this = $(this),
                                            disBtn = $this.find('.msg-dismiss-content');
                                        if(disBtn.length > 0){
                                            disBtn.on('click', function(e) {
                                                $this.slideUp(500);
                                            })
                                        }
                                    })
                                }
                                if($("#"+option.load_class).find('.tpgb-accordion').length ){
                                    if(typeof accordionJS == 'function'){
                                        accordionJS();
                                    }
                                }
                            })
                    }else{
                        $(current_click).addClass("hide");
                    }
            });
        });
    }

    if($('body').find('.tpgb-lazy-load').length && ( $('.tpgb-isotope').length || $('.tpgb-metro').length )){
        
        var selector = '';
        if($('.tpgb-isotope').length){
            selector = $('.tpgb-isotope');
        }else if($('.tpgb-metro').length){
            selector = $('.tpgb-metro');
        }

        var windowWidth, windowHeight, documentHeight, scrollTop, containerHeight, containerOffset, $window = $(window);
        var recalcValues = function() {
            windowWidth = $window.width();
            windowHeight = $window.height();
            documentHeight = $('body').height();
            containerHeight = selector.height();
            containerOffset = selector.offset().top+50;
            setTimeout(function(){
                containerHeight = selector.height();
                containerOffset = selector.offset().top+50;
            }, 50);
        };
        recalcValues();
        $window.resize(recalcValues);
        $window.bind('scroll load', function(e) {
            e.preventDefault();
            recalcValues();
            scrollTop = $window.scrollTop();
            selector.each(function() {
                containerHeight = $(this).height();
                containerOffset = $(this).offset().top;


                if($(this).find(".post-lazy-load").length && scrollTop < documentHeight && scrollTop > (containerHeight + containerOffset - windowHeight) && typeof tpgb_lazy_load_ajax === "function" ){
                    tpgb_lazy_load_ajax($(this));                    
                }
            });
        });
       
    }
    
})( jQuery );

function tpgb_lazy_load_ajax($this = ''){
    if(!$this){
        return;
    }
	
	var tabCnt = $this.closest('.tpgb-tab-content');
                
	var parentOfParent1 = $this.closest('.tpgb-tab-content').parent().closest('.tpgb-tab-content').parent().closest('.tpgb-tab-content');
	if( parentOfParent1 && parentOfParent1.length && !parentOfParent1.hasClass('active')){
		return;
	}

	var parentOfParent = $this.closest('.tpgb-tab-content').parent().closest('.tpgb-tab-content');
	if( parentOfParent && parentOfParent.length && !parentOfParent.hasClass('active')){
		return;
	}

	if( tabCnt && tabCnt.length > 0 && !tabCnt.hasClass('active')){
		return;
	}
	
    var $ = jQuery,
    current_click= $this.find(".post-lazy-load"),
    option = current_click.data('dypost'),
    stoption = current_click.data('post-option'),
    current_text = current_click.text();
    
    if ( current_click.data('requestRunning') ) {
        return;
    }
    if(option.offset_posts==undefined || option.offset_posts==""){
        option.offset_posts=0;
    }

    current_click.data('requestRunning', true);
    if(option.total_page >= option.page){
        option.offset=(parseInt(option.page-1)*parseInt(option.load_more))+parseInt(option.display_post)+parseInt(option.offset_posts);

        $.ajax({
            type:'POST',
            data: {
                action : 'tpgb_post_load',
                option : stoption,
                dyOpt : option,
            },
             url: ( tpgb_config && tpgb_config.ajax_url ) ? tpgb_config.ajax_url : tpgb_load.ajaxUrl,
            beforeSend: function() {
                $(current_click).text(option.loadingtxt);
            },
            success: function(data) {  
                if(data==''){
                    $(current_click).addClass("hide");
                }else{
                    $("#"+option.load_class+' > .post-loop-inner').append( data );
                    if(option.layout=='grid' || option.layout=='masonry'){
                        if($("#"+option.load_class).hasClass("tpgb-isotope")){
                            var $newItems = $('');
                            $("#"+option.load_class+' > .post-loop-inner').isotope( 'insert', $newItems );
                            $("#"+option.load_class+' > .post-loop-inner').isotope( 'layout' ).isotope( 'reloadItems' ); 
                        }
                    }
                }
                option.page++;
                if(option.page==option.total_page){
                    $(current_click).addClass("hide");
                    $(current_click).attr('data-page', option.page);
                    $(current_click).parent(".tpgb-lazy-load").append('<div class="tpgb-post-loaded">'+option.loaded_posts+'</div>');
                }else{
                    $(current_click).text(current_text);
                    $(current_click).attr('data-page', option.page);
                }
                
            },
            complete: function() {
                if($("#"+option.load_class+' .tpgb-category-filter').length){
                    $("#"+option.load_class+' .tpgb-filter-data .tpgb-categories > .tpgb-filter-list > a').each(function(){
                        var filter = $this.data("filter");
                        if(filter!='' && filter!=undefined && filter==='*'){
                            var totle_count = $("#"+option.load_class+' .post-loop-inner .grid-item').length;
                        }else if(filter!='' && filter!=undefined){
                            var totle_count = $("#"+option.load_class+' .post-loop-inner .grid-item'+filter).length;
                        }
                        if(totle_count){
                            $this.find(".tpgb-category-count").text(totle_count);
                        }
                    });
                }
                if(option.style == 'style-1') {
                    var container = $("#"+option.load_class);
                    if(container.hasClass('dynamic-style-1')){
                        $('.dynamic-style-1 .grid-item .dynamic-list-content').on('mouseenter',function() {
                            $this.find(".tpgb-post-hover-content").slideDown(300)				
                        });
                        $('.dynamic-style-1 .grid-item .dynamic-list-content ').on('mouseleave',function() {
                            $this.find(".tpgb-post-hover-content").slideUp(300)				
                        });
                    }
                }
                if($("#"+option.load_class).hasClass("tpgb-isotope")){
                    if(option.layout=='grid' || option.layout=='masonry'){
                        let container = $("#"+option.load_class),
                            innerDiv = container.find('.post-loop-inner');
                            innerDiv.isotope({
                                itemSelector: ".grid-item",
                                resizable: true,
                                sortBy: "original-order",
                                resizesContainer: true,
                                initLayout : false
                            });		
                            innerDiv.isotope('layout');
                            setTimeout( function() {
                                innerDiv.isotope('layout');
                            }, 50 );       
                    }
                }

                //  Metro Re Layout
                if($("#"+option.load_class).hasClass("tpgb-metro")){
                    tpgb_metro_layout('all');
                    $('.tpgb-metro .post-loop-inner').isotope({
                        itemSelector: ".grid-item",
                        resizable: !0,
                        sortBy: "original-order",
                        transitionDuration:  (option.disableAnim) ? 0 : '0.4s' ,
                    })
                    $('.tpgb-metro .post-loop-inner').isotope('layout').isotope( 'reloadItems' );
                }
                
                if($("#"+option.load_class).hasClass("tpgb-equal-height")){
                    if(typeof equalHeightFun == 'function'){
                        var eDiv = document.getElementById(option.load_class);
                        equalHeightFun(eDiv)
                    }
                }

                if($("#"+option.load_class).find('.tpgb-heading-animation').length ){
                    $("#"+option.load_class).find('.tpgb-heading-animation').each(function(obj){
                        var settings = obj.data('settings');
                        if('textAnim' === settings.style){
                            if(typeof tpgbHeadingAnimation == 'function'){
                                tpgbHeadingAnimation( $this, settings.animStyle )
                            }
                        }
                    })
                }
                current_click.data('requestRunning', false);
                if($("#"+option.load_class).find('.tpgb-fancy-popup').length ){
                    let fancyDiv = $("#"+option.load_class).find('.tpgb-fancy-popup');
                    fancyDiv.each(function(){
                        tpgb_fancy_popup($(this))
                    })
                }
            }
            }).then(function(){
                if($("#"+option.load_class).hasClass("tpgb-isotope")){
                    if(option.layout=='grid' || option.layout=='masonry'){
                        var container = $("#"+option.load_class+' > .post-loop-inner');
                        container.isotope({
                            itemSelector: ".grid-item",
                            resizable: !0,
                            sortBy: "original-order",
                            resizesContainer: true,
                            transitionDuration:  (option.disableAnim) ? 0 : '0.4s' ,
                        });						
                    }
                    container.isotope( 'layout' );
                    setTimeout(function(){
                        container.isotope( 'layout' );
                    }, 30);
                }

                if($("#"+option.load_class).hasClass("tpgb-metro")){
                    tpgb_metro_layout('all');
                    $('.tpgb-metro .post-loop-inner').isotope({
                        itemSelector: ".grid-item",
                        resizable: !0,
                        sortBy: "original-order",
                        transitionDuration:  (option.disableAnim) ? 0 : '0.4s' ,
                    })
                    $('.tpgb-metro .post-loop-inner').isotope('layout').isotope( 'reloadItems' );
                }

                if($("#"+option.load_class).hasClass("tpgb-equal-height")){
                    if(typeof equalHeightFun == 'function'){
                        var eDiv = document.getElementById(option.load_class);
                        equalHeightFun(eDiv)
                    }
                }
                if($("#"+option.load_class).find('.tpgb-heading-animation').length ){
                    $("#"+option.load_class).find('.tpgb-heading-animation').each(function(obj){
                        var settings = obj.data('settings');
                        if('textAnim' === settings.style){
                            if(typeof tpgbHeadingAnimation == 'function'){
                                tpgbHeadingAnimation( $this, settings.animStyle )
                            }
                        }
                    })
                }

                if($("#"+option.load_class).find('.tpgb-accordion').length ){
                    if(typeof accordionJS == 'function'){
                        accordionJS();
                    }
                }
        })
    }else{
        $(current_click).addClass("hide");
    }
}