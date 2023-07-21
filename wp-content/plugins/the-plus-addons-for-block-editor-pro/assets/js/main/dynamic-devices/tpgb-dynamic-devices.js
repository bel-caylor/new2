/* Dynamic Devices Start */
let allDD = document.querySelectorAll('.tpgb-dynamic-device');
if(allDD){
    allDD.forEach( el => {

        let blockId = el.getAttribute("data-id"),
            dataAtt = el.getAttribute('data-fancy-option');

        if(dataAtt){
            dataAtt = JSON.parse(dataAtt);
            jQuery('[data-fancybox="'+blockId+'"]').fancybox({
                buttons : dataAtt.button,
                type : 'iframe',
                image: {
                    preload: true
                },
                loop: dataAtt.loop,
                infobar: dataAtt.infobar,
                animationEffect:  dataAtt.animationEffect,
                animationDuration: dataAtt.animationDuration,
                transitionEffect: dataAtt.transitionEffect,
                transitionDuration: dataAtt.transitionDuration,
                arrows: dataAtt.arrows,

                //false, close, next, nextOrClose, toggleControls, zoom
                clickContent:'next',
                clickSlide:'close',
                dblclickContent: false,
                dblclickSlide: false,

            });
        }

        let ddMul = el.classList.contains('tpgb-dd-multi-connect'),
            deviceContent = el.querySelector('.tpgb-device-content');
        if(ddMul){
            let conId = el.getAttribute("data-connectdd"),
                conIdClass = document.querySelectorAll('.'+conId);

            deviceContent.addEventListener("mouseenter", function(){
                conIdClass.forEach( cId => {
                    let scrlImg = cId.querySelector('.tpgb-scroll-img-js');
                    scrlImg.classList.add('active_on_scroll');
                });
                
            })
            deviceContent.addEventListener("mouseleave", function(){
                conIdClass.forEach( cId => {
                    let scrlImg = cId.querySelector('.tpgb-scroll-img-js');
                    scrlImg.classList.remove('active_on_scroll');
                });
            })
        }

        let rebHover = el.classList.contains('reusable-block-hover-scroll');
        if(rebHover){
            deviceContent.addEventListener("mouseenter", function(de){
                de.currentTarget.classList.add('active_on_scroll');
                
            })
            deviceContent.addEventListener("mouseleave", function(de){
                de.currentTarget.classList.remove('active_on_scroll');
            })
        }

        let rebMul = el.classList.contains('tpgb-mul-reb-connect');
        if(rebMul){
            let rebConId = el.getAttribute("data-connectdd"),
                rConIdClass = document.querySelectorAll('.'+rebConId);

            deviceContent.addEventListener("mouseenter", function(){
                rConIdClass.forEach( cId => {
                    let devCon = cId.querySelector('.tpgb-device-content');
                    devCon.classList.add('active_on_scroll');
                });
                
            })
            deviceContent.addEventListener("mouseleave", function(){
                rConIdClass.forEach( cId => {
                    let devCon = cId.querySelector('.tpgb-device-content');
                    devCon.classList.remove('active_on_scroll');
                });
            })
        }

    });
}
/* Dynamic Devices End */