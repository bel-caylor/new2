/* Progress Tracker */
document.addEventListener('DOMContentLoaded',()=>{
    let allPT = document.querySelectorAll('.tpgb-progress-tracker');
    allPT.forEach((pt)=>{
        let getDataA = pt.getAttribute('data-attr');
        getDataA =  JSON.parse(getDataA);
        let ptfill = pt.querySelector('.progress-track-fill'),
            ptPtext = pt.querySelector('.progress-track-percentage');

        let winScroll = '',height = '',  scrolled = '', ptSelVal = {};
        if(getDataA.apply_to=='entire'){
            winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            height = document.body.scrollHeight - window.innerHeight;;
            scrolled = (winScroll / height) * 100;
            progresstracker(pt, ptfill, ptPtext, winScroll, height);
            window.addEventListener('scroll', ()=>{ 
                winScroll = document.body.scrollTop || document.documentElement.scrollTop;
                progresstracker(pt, ptfill, ptPtext, winScroll, height) 
            });
        }else if(getDataA.apply_to=='selector' && getDataA.selector){
            winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            let getSelector = document.querySelector(getDataA.selector);
            if(getSelector){
                ptSelVal.start = getSelector.offsetTop;
                ptSelVal.end = ptSelVal.start + getSelector.clientHeight - window.innerHeight;
                ptSelVal.between = getSelector.clientHeight - window.innerHeight;
                let xScroll = Number(winScroll - ptSelVal.start);
                height = ptSelVal.between;
                if(winScroll >= ptSelVal.start && ptSelVal.end >= winScroll){
                    progresstracker(pt, ptfill, ptPtext, xScroll, height);
                }else if(winScroll <  ptSelVal.start){
                    progresstracker(pt, ptfill, ptPtext, xScroll = 0, height);
                }else if(ptSelVal.end < winScroll){
                    setTimeout(()=>{
                        progresstracker(pt, ptfill, ptPtext, height, height);
                    }, 500);
                }
                window.addEventListener('scroll', ()=>{ 
                    winScroll = document.body.scrollTop || document.documentElement.scrollTop;
                    let xScroll = Number(winScroll - ptSelVal.start);
                    if(winScroll >= ptSelVal.start && ptSelVal.end >= winScroll){
                        progresstracker(pt, ptfill, ptPtext, xScroll, height);
                    }else if(winScroll <  ptSelVal.start){
                        progresstracker(pt, ptfill, ptPtext, xScroll = 0, height);
                    }else if(ptSelVal.end < winScroll){
                        progresstracker(pt, ptfill, ptPtext, height, height);
                    }
                })  
            }
        }
    })
});

function progresstracker(pt, ptfill, ptPtext, winScroll, height){
    let scrolled = (winScroll / height) * 100;
    if(pt.classList.contains('type-horizontal')){
        ptfill.style.width = scrolled + "%";
        if(ptPtext){
            ptPtext.textContent = Math.round(scrolled)+"%";
        }
    }else if(pt.classList.contains('type-vertical')){
        ptfill.style.height = scrolled + "%";
        if(ptPtext){
            ptPtext.textContent = Math.round(scrolled)+"%";
        }
    }else if(pt.classList.contains('type-circular')){
        let circle2 =  pt.querySelector('.tpgb-pt-circle-st2'),
            totalLength = circle2.getTotalLength();

        circle2.style.strokeDasharray = totalLength;
        circle2.style.strokeDashoffset = totalLength;

        let percentage = winScroll / height;
        percentage = percentage.toFixed(2);
        circle2.style.strokeDashoffset = totalLength - totalLength * percentage;
        if(ptPtext){
            ptPtext.textContent = Math.round(scrolled)+"%";
        }
    }
}