/** Expand */
window.addEventListener('DOMContentLoaded', (event) => {
    let allExpand = document.querySelectorAll('.tpgb-unfold-wrapper');
    if(allExpand){
        allExpand.forEach((ex)=>{
            let settings = ex.getAttribute("data-settings"),
                wrapDescInn = ex.querySelector(".tpgb-unfold-description-inner"),
                wrapToggle = ex.querySelector(".tpgb-unfold-last-toggle"),
                DescHeight = 0,
                unfoldWarpper = ex.querySelector('.tpgb-unfold-description');
                if(wrapDescInn){
                    DescHeight = wrapDescInn.offsetHeight;
                }
            settings = JSON.parse(settings);

            if(DescHeight <= settings.maxHeight ){
                let descEclass = 'less-desc-height';
                if(unfoldWarpper){
                    unfoldWarpper.classList.add(descEclass);
                }
                wrapToggle.style.display = "none";
                const btnHideCss = document.createElement('style');
                btnHideCss.textContent = `.tpgb-unfold-wrapper.${settings.id} .tpgb-unfold-description.${descEclass}:after{min-height:0;background: transparent;}.${settings.id} .tpgb-unfold-description.${descEclass}{height:auto;}`;

                document.head.appendChild(btnHideCss);
            }

            let tglBtn = ex.querySelector('.tpgb-unfold-toggle');

            if(unfoldWarpper){
                unfoldWarpper.style.transition = settings.duration+"ms ease-in-out height";
            }

            if(tglBtn){
                tglBtn.addEventListener('click', (e)=>{
                    if(ex.classList.contains('fullview')){
                        ex.classList.remove('fullview');
        
                        if(settings.iconPos=='before' && settings.iconPos !=undefined) {						
                            e.currentTarget.innerHTML = settings.readmoreIcon + settings.readmore;
                        } else if(settings.iconPos=='after' && settings.iconPos !=undefined) {
                            e.currentTarget.innerHTML = settings.readmore + settings.readmoreIcon;
                        }
        
                        var inw = window.innerWidth;
                        if(inw >= 1025){
                            unfoldWarpper.style.height = settings.maxHeight+"px";
                        }else if(inw <= 1024 && inw >= 768){	
                            unfoldWarpper.style.height = settings.maxHeightT+"px";			
                        }else if(inw <= 767 ){	
                            unfoldWarpper.style.height = settings.maxHeightM+"px";			
                        }
        
                    }else{
                        ex.classList.add('fullview');
                        var outerhight = parseInt(ex.querySelector(".tpgb-unfold-description-inner").offsetHeight);
                        if(settings.iconPos=='before' && settings.iconPos != undefined) {
                            e.currentTarget.innerHTML = settings.readlessIcon + settings.readless;
                        } else if(settings.iconPos=='after' && settings.iconPos != undefined) {
                            e.currentTarget.innerHTML = settings.readless + settings.readlessIcon;
                        }
                        unfoldWarpper.style.height = outerhight+"px";
        
                    }
                });
            }
        });
    }
});