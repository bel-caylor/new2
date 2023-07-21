var doc = document;
const elements = doc.querySelectorAll('.tpgb-coupon-code');
if(elements){
    elements.forEach( el => {
        var data = JSON.parse(el.getAttribute('data-tpgb_cc_settings')),
            coupen = el.querySelector('.coupon-front-side'),
            fillPercent = data.fillPercent,
            couponType = data.couponType;
        if(couponType=='standard') {
            
            let UniqCls = '.tpgb-block-' + data.id,
                UniqId = '#tpgb-block-' + data.id,
                UniqId1 = '\#tpgb-block-' + data.id,
                Btnlink = '.tpgb-block-'+data.id+' .coupon-btn-link';
            
            if(data.actionType=='popup') {
                jQuery(Btnlink).on("click", function(e){
                    if(jQuery(this).attr('href')=='#' || jQuery(this).attr('href')==''){
                        e.preventDefault();							
                    }
                    jQuery(UniqCls+' .copy-code-wrappar').addClass("active");
                
                    jQuery(UniqCls+' .copy-code-btn').on("click", function(e){
                        let copyText = el.querySelector('.full-code-text');
                        tpgbCopyCode(copyText, data, UniqCls, this);
                    });
                });
                
                if(jQuery(el).hasClass("tpgb-tab-cop-rev")){
                    jQuery(document).on("click",".copy-code-btn", function() {
                        let copyText = el.querySelector('.full-code-text');
                        tpgbCopyCode(copyText, data, UniqCls, this);
                    });
                    jQuery(Btnlink).on("click", function(e){		
                        if(jQuery(this).attr('href')=='#' || jQuery(this).attr('href')==''){
                            e.preventDefault();							
                        }					
                        window.open(window.location+UniqId1,"_blank'");
                        setTimeout(function(){
                            window.location.href = data.extlink;
                        }, 100);
                    });
                    var hash = window.location.hash;						
                    if(hash==UniqId){
                        jQuery(UniqCls +" .copy-code-wrappar").addClass("active");
                        jQuery(UniqCls +" .full-code-text").addClass("tpgb-code-popup");
                    }						
                }
                
            }else if(data.actionType=='click'){
                var replaceCode = '<span class="full-code-text '+data.code_arrow+'">'+data.coupon_code+'</span>';
                el.querySelector('.coupon-btn-link').addEventListener("click", function(e) {
                    jQuery(e.currentTarget).closest('.tpgb-block-'+data.id+' .coupon-code-inner').replaceWith(replaceCode);
                    if(el.dataset.saveCookies){
                        window.localStorage.setItem('tpgb_coupon_cookie', true);
                    }
                    if(e.currentTarget.classList.contains('tpgb-hl-links')){
                        e.preventDefault();
                        var $this= jQuery(this);
                        var hlset = $this.data('hlset');							
                        for (var i = 0, length = hlset.length; i < length; ++i) {
                            var url = hlset[i];								
                            window.open(url, '_blank');
                        }
                    }
                });
                if(el.dataset.saveCookies && window.localStorage.getItem('tpgb_coupon_cookie')){
                    jQuery(el).find('.coupon-code-inner').replaceWith(replaceCode);
                }
            }
        }else if(couponType=='scratch') {
            html2canvas( el.querySelector('.coupon-front-side'), {
                allowTaint: true,
                backgroundColor: null,
                windowWidth: jQuery( window ).width(),
                windowHeight: jQuery( window ).height(),
                scrollX: 0,
                scrollY: -window.scrollY,
            } ).then( function( canvas ) {
                canvas.setAttribute( 'class', 'coupon-front-side-canvas' );
                el.prepend( canvas );

                jQuery( '#front-side-'+data.id, jQuery(el)).fadeOut( 300, function() {
                    jQuery( this ).remove();
                });
                
                var container    = el,
                    canvas       = el.querySelector('canvas'),
                    canvasWidth  = canvas.width,
                    canvasHeight = canvas.height,
                    ctx          = canvas.getContext('2d'),
                    brush        = new Image(),
                    isDrawing	 = false,
                    lastPin;

                    brush.src = 'data:image/png;base64,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';

                    canvas.addEventListener( 'mousedown', checkMouseDown, false );
                    canvas.addEventListener( 'mousemove', checkMouseMove, false );
                    canvas.addEventListener( 'mouseup', checkMouseUp, false );

                    canvas.addEventListener( 'touchstart', checkMouseDown, false );
                    canvas.addEventListener( 'touchmove', checkMouseMove, false );
                    canvas.addEventListener( 'touchend', checkMouseUp, false );

                function distanceBetween( pin1, pin2 ) {
                    return Math.sqrt( Math.pow( pin2.x - pin1.x, 2 ) + Math.pow( pin2.y - pin1.y, 2 ) );
                }

                function angleBetween( pin1, pin2 ) {
                    return Math.atan2( pin2.x - pin1.x, pin2.y - pin1.y );
                }

                function getFillPx( step ) {

                    if ( ! step || step < 1 ) {
                        step = 1;
                    }

                    var pixels   = ctx.getImageData(0, 0, canvasWidth, canvasHeight),
                        pdata    = pixels.data,
                        l        = pdata.length,
                        total    = ( l / step ),
                        count    = 0;

                    for( var i = count = 0; i < l; i += step ) {
                        if ( parseInt( pdata[i] ) === 0 ) {
                            count++;
                        }
                    }

                    return Math.round( ( count / total ) * 100 );
                }

                function getMouse( e, canvas ) {
                    var offsetX = 0,
                        offsetY = 0,
                        mx,
                        my;
                    mx = ( e.pageX || e.touches[0].clientX ) - offsetX;
                    my = ( e.pageY || e.touches[0].clientY ) - offsetY;

                    return { x: mx, y: my };
                }

                function checkPercentage( filledPx ) {
                    filledPx = filledPx || 0;

                    if ( filledPx > fillPercent) {
                        canvas.parentNode.removeChild(canvas);
                    }
                }

                function checkMouseDown( e ) {
                    isDrawing = true;
                    lastPin = getMouse( e, canvas );
                }

                function checkMouseMove( e ) {

                    if ( ! isDrawing ) {
                        return;
                    }

                    e.preventDefault();

                    var currentPin = getMouse( e, canvas ),
                        distance         = distanceBetween( lastPin, currentPin ),
                        angle        = angleBetween( lastPin, currentPin ),
                        x            = 0,
                        y            = 0;

                    for ( var i = 0; i < distance; i++ ) {
                        x = lastPin.x + ( Math.sin( angle ) * i ) - 40;
                        y = lastPin.y + ( Math.cos( angle ) * i ) - 40;
                        ctx.globalCompositeOperation = 'destination-out';
                        ctx.drawImage( brush, x, y, 80, 80 );
                    }

                    lastPin = currentPin;

                    checkPercentage( getFillPx( 32 ) );
                }

                function checkMouseUp( e ) {
                    isDrawing = false;
                }
                
            } );
        } else if(couponType=='slideOut') {
            var dire = data.slideDirection;
            var frontSide    = jQuery( '.coupon-front-side', el ),
                backSide     = jQuery( '.coupon-back-side', el ),
                targetWidth  = jQuery(el.target).width(),
                targetWidth  = jQuery(el.target).height(),
                axis          = ( 'left' === data.slideDirection || 'right' === data.slideDirection ) ? 'x' : 'y';
            
            frontSide.draggable( {
                axis: axis,
                drag: function( event, ui ) {
                    var dragAttr = ui.position;
                    switch( data.slideDirection ) {
                        case 'left':
                            if ( dragAttr.left >= 0 ) {
                                ui.position.left = 0;
                            }
                            break;
                        case 'right':
                            if ( dragAttr.left <= 0 ) {
                                ui.position.left = 0;
                            }
                            break;
                        case 'top':
                            if ( dragAttr.top >= 0 ) {
                                ui.position.top = 0;
                            }
                            break;
                        case 'bottom':
                            if ( dragAttr.top <= 0 ) {
                                ui.position.top = 0;
                            }
                            break;
                    }

                },
            } );
        } else if(couponType=='peel') {
            el.classList.add('peel-ready');
            var front = jQuery(coupen, jQuery(el)),
                frontClone = front.clone();
            jQuery(coupen, jQuery(el)).addClass( 'peel-top' );
            frontClone.removeAttr('id');
            frontClone.addClass('peel-back');
            frontClone.insertAfter('#front-side-'+data.id);
            jQuery('.coupon-back-side', jQuery(el)).addClass( 'peel-bottom' );
            var peel = new Peel( ".tpgb-block-"+data.id , {
                corner: Peel.Corners.TOP_RIGHT
            } );
            var targetWidth = el.clientWidth,
                targetHeight = el.clientHeight;
            peel.setPeelPosition( targetWidth - 30, 40 );
            peel.setFadeThreshold(.8);
            peel.handleDrag( function( evt, x, y ) {
                var targetOffset = jQuery(el).offset(),
                    offsetX      = targetOffset.left,
                    offsetY      = targetOffset.top,
                    deltaX       = x - offsetX,
                    deltaY       = y - offsetY;

                deltaX = deltaX < 0 ? deltaX*=3 : deltaX;
                deltaY = deltaY < 0 ? deltaY*=3 : deltaY;

                if ( 0.98 < this.getAmountClipped() ) {
                    this.removeEvents();
                    jQuery( '.peel-top, .peel-back, .peel-bottom-shadow', jQuery(el) ).remove();
                }
                peel.setPeelPosition( Math.round( deltaX ), Math.round( deltaY ) );
            });
        }
    });

    // Function for close the Modal
    function closeModal(){
        jQuery(".copy-code-wrappar").removeClass("active");
    }
    // Call the closeModal function on the clicks/keyboard
    jQuery(".tpgb-ccd-closebtn, .copy-code-wrappar").on("click", function(){
        closeModal();
    });

}
function tpgbCopyCode(copyText, data,UniqCls, el){
    let text = copyText.innerText,
        textArea  = document.createElement('textarea');
    textArea.opacity =  "0" ;
    textArea.value = text;
    document.body.append(textArea);
    textArea.select();
    document.execCommand('copy');
    document.body.removeChild(textArea);

    jQuery(el).text(data.after_copy_text);
    setTimeout((function() {
        jQuery(UniqCls+' .copy-code-btn').text(data.copy_btn_text);
    }), 2000)
}