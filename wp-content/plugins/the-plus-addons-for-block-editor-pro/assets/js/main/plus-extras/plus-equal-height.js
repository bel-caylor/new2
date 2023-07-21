/* Equal Height Start */
let equalHeightClass = document.querySelectorAll('.tpgb-equal-height');
if(equalHeightClass){
    addEventListener('DOMContentLoaded', () => {
        equalHeightClass.forEach( el => {
            equalHeightFun(el)         
        });
    });
}


function equalHeightFun(el){
    let eHDiv = el.getAttribute('data-tpgb-equal-height'),
        getMlClass = eHDiv.split(',');

    getMlClass.forEach(gm =>{
        if(gm){
            let gmc = el.querySelectorAll(gm);
            var highest = null;
            var hi = 0;
            if(gmc){
                gmc.forEach( ell => {
                    var h = ell.offsetHeight;
                    if(h > hi){
                        hi = h;
                        highest = ell.offsetHeight;  
                    }
                });
    
                if(highest){
                    gmc.forEach( ell => {
                        ell.style.height = highest+"px";
                    });
                }
            }
        }
    })
}
/* Equal Height End */