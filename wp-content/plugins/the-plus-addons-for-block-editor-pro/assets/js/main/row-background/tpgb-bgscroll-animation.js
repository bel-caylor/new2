(function($){

    $(".tpgb-section:not(.tpgb-section-editor),.tpgb-container-row:not(.tpgb-container-row-editor)").each(function() {
        if($(this).find('.tpgb-row-bg-gradient').length > 0){
            var container = $(this).find('.tpgb-row-bg-gradient'),
                position = container.data('position'),
                fullpage = container.data('full-page');
            if(fullpage == 'yes'){
                $(this).closest('.site-content').prepend(container);
                $(this).closest('.site-content').css("position",position);
            }
        }
        if($(this).find('.tpgb-row-scrollbg').length){
            var scrolldiv = $(this).find('.tpgb-row-scrollbg'),
                secdiv = scrolldiv.find('.tpgb-section-bg-scrolling');

            $(this).closest('.site-content').prepend(scrolldiv);
            $(this).closest('.site-content').css("position",scrolldiv);

            if(scrolldiv.data("scrolling-effect")=='yes'){
                var bgColors = scrolldiv.data('bgcolors');
                if(bgColors){
                    var paraent_node=scrolldiv.closest(".site-content"),
                        i=0,
                        arry_len=bgColors.length,
                        pareDiv = ( $(".tpgb-section").length ) ? paraent_node.find(".tpgb-section") : paraent_node.find(".tpgb-container-row") ;
                        
                    pareDiv.each(function(){
                        if(arry_len>i){
                            var FirstColor=i;
                            var SecondColor=i+1;
                        }else{
                            i=0;
                            var FirstColor=i;
                            var SecondColor=i+1;
                        }
                        if(bgColors[FirstColor]!='' && bgColors[FirstColor]!=undefined){
                            FirstColor=bgColors[FirstColor];
                        }
                        if(bgColors[SecondColor]!='' && bgColors[SecondColor]!=undefined){
                            SecondColor=bgColors[SecondColor];
                        }else{
                            i=0;
                            SecondColor=i;
                            SecondColor=bgColors[SecondColor];
                        }
                        rowTransitionalColor($(this), new $.Color(FirstColor),new $.Color(SecondColor));				
                        i++;
                    });
                }
            }
            else if(scrolldiv.length){
                var bgColors = scrolldiv.data('bgcolors');
                if(bgColors){

                    let secDiv = document.querySelectorAll('.tpgb-section'),
                    contentElems = '';
                    
                    if(secDiv.length){
                        contentElems = Array.from(document.querySelectorAll('.tpgb-section'));
                    }else{
                        contentElems = Array.from(document.querySelectorAll('.tpgb-container-row'));
                    }

                   
                    var loop_scroll = scrolldiv.find(".tpgb-section-bg-scrolling"),
                        totalEle=contentElems.length,
                        step=0,
                        position;
                        
                        
                        contentElems.forEach((el,pos) => {
                            const scrollElemToWatch = pos ? contentElems[pos] : contentElems[pos];
                            pos = pos ? pos : totalEle;
                            const watcher = scrollMonitor.create(scrollElemToWatch,{top:-300});
                        
                            watcher.enterViewport(function() {
                                step = pos;
                                if(totalEle >= loop_scroll.length && pos+1 > loop_scroll.length){
                                    position=0;
                                }else{
                                    position=pos;
                                }
                                scrolldiv.find(".tpgb-section-bg-scrolling").removeClass("active");
                                scrolldiv.find(".tpgb-section-bg-scrolling:nth-child("+(position+1)+")").addClass("active");
                            });
                            watcher.exitViewport(function() {
                                var idx = !watcher.isAboveViewport ? pos-1 : pos+1;
                                if( idx <= totalEle && step !== idx ) {							
                                    step = idx;
                                    if(totalEle > loop_scroll.length && idx+1 > loop_scroll.length){
                                        position=0;
                                    }else{
                                        position=idx;
                                    }
                                scrolldiv.find(".tpgb-section-bg-scrolling").removeClass("active");
                                scrolldiv.find(".tpgb-section-bg-scrolling:nth-child("+(position+1)+")").addClass("active");
                                }
                            });
                        });
                }
            }
        }
    });
    
}(jQuery));

function rowTransitionalColor($row, firstColor, secondColor) {
    "use strict";
    var $ = jQuery, scrollPos = 0, currentRow = $row, beginningColor = firstColor, endingColor = secondColor, percentScrolled, newRed, newGreen, newBlue, newColor;
    
    $(document).scroll(function() {
        var animationBeginPos = currentRow.offset().top
          , endPart = currentRow.outerHeight() < 800 ? currentRow.outerHeight() / 4 : $(window).height()
          , animationEndPos = animationBeginPos + currentRow.outerHeight() - endPart;
        scrollPos = $(this).scrollTop();
        if (scrollPos >= animationBeginPos && scrollPos <= animationEndPos) {
            percentScrolled = (scrollPos - animationBeginPos) / (currentRow.outerHeight() - endPart);
            newRed = Math.abs(beginningColor.red() + (endingColor.red() - beginningColor.red()) * percentScrolled);
            newGreen = Math.abs(beginningColor.green() + (endingColor.green() - beginningColor.green()) * percentScrolled);
            newBlue = Math.abs(beginningColor.blue() + (endingColor.blue() - beginningColor.blue()) * percentScrolled);
            newColor = new $.Color(newRed,newGreen,newBlue);
            $('.tpgb-row-scrollbg').animate({
                backgroundColor: newColor
            }, 0)
        } else if (scrollPos > animationEndPos) {
            $('.tpgb-row-scrollbg').animate({
                backgroundColor: endingColor
            }, 0)
        }
    })
}