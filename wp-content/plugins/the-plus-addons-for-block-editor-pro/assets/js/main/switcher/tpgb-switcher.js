/*Switcher*/
let switchAll = document.querySelectorAll('.tpgb-switch-wrap');

if(switchAll){
    switchAll.forEach((sw)=>{
        let switch_toggle = sw.querySelector('.switch-toggle'),
            switch_1_toggle = sw.querySelector('.switch-1'),
            switch_2_toggle = sw.querySelector('.switch-2'),
            sc1 = sw.querySelector(".switch-content-1"),
            sc2 = sw.querySelector(".switch-content-2"),
            sTgl = sw.querySelector(".switch-toggle-wrap"),
            inpSwi = sw.querySelector(".switch-toggle");
        
        if(/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream){
            if(switch_toggle !== null){
                switch_toggle.addEventListener('touchstart',function(e) {
                    let sc1Display = window.getComputedStyle(sc1).display,
                        sc2Display = window.getComputedStyle(sc2).display;
            
                    if(sc1Display == 'block'){
                        sc1.style.display = 'none';
                    }else{
                        sc1.style.display = 'block';
                    }
            
                    if(sc2Display == 'block'){
                        sc2.style.display = 'none';
                    }else{
                        sc2.style.display = 'block';
                    }
            
            
                    if(sTgl.classList.contains('active')){
                        sTgl.classList.remove('active');
                        sTgl.classList.add('inactive');
                    }else{
                        sTgl.classList.add('active');
                        sTgl.classList.remove('inactive');
                    }
            
                    switcerSlider(sw)
                });
            }
        }else{
            if(switch_toggle !== null){
                switch_toggle.addEventListener('click',function(e) {
                    let sc1Display = window.getComputedStyle(sc1).display,
                        sc2Display = window.getComputedStyle(sc2).display;
            
                    if(sc1Display == 'block'){
                        sc1.style.display = 'none';
                    }else{
                        sc1.style.display = 'block';
                    }
            
                    if(sc2Display == 'block'){
                        sc2.style.display = 'none';
                    }else{
                        sc2.style.display = 'block';
                    }
            
            
                    if(sTgl.classList.contains('active')){
                        sTgl.classList.remove('active');
                        sTgl.classList.add('inactive');
                    }else{
                        sTgl.classList.add('active');
                        sTgl.classList.remove('inactive');
                    }
            
                    switcerSlider(sw)
                });
            }
        }

        if(/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream){
            if(switch_1_toggle !== null){
                switch_1_toggle.addEventListener('touchstart', ()=>{
                    sc1.style.display = 'block';
                    sc2.style.display = 'none';

                    if(inpSwi){ inpSwi.checked = false;  }
                    
                    if(sTgl.classList.contains('active')){
                        sTgl.classList.remove('active');
                        sTgl.classList.add('inactive');
                    }
            
                    switcerSlider(sw)
                });
            }
        }else{
            if(switch_1_toggle !== null){
                switch_1_toggle.addEventListener('click', ()=>{
                    sc1.style.display = 'block';
                    sc2.style.display = 'none';
            
                    if(inpSwi){ inpSwi.checked = false;  }
                    if(sTgl.classList.contains('active')){
                        sTgl.classList.remove('active');
                        sTgl.classList.add('inactive');
                    }
            
                    switcerSlider(sw)
                });
            }
        }

        if(/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream){
            if(switch_2_toggle !== null){
                switch_2_toggle.addEventListener('touchstart', ()=>{
                    sc1.style.display = 'none';
                    sc2.style.display = 'block';
            
                    if(inpSwi){ inpSwi.checked = true;  }
                    if(sTgl.classList.contains('inactive')){
                        sTgl.classList.remove('inactive');
                        sTgl.classList.add('active');
                    }
            
                    switcerSlider(sw)
                });
            }
        }else{
            if(switch_2_toggle !== null){
                switch_2_toggle.addEventListener('click', ()=>{
                    sc1.style.display = 'none';
                    sc2.style.display = 'block';
            
                    if(inpSwi){ inpSwi.checked = true;  }
                    if(sTgl.classList.contains('inactive')){
                        sTgl.classList.remove('inactive');
                        sTgl.classList.add('active');
                    }
            
                    switcerSlider(sw)
                });
            }
        }

    })
    
    
    function switcerSlider(el){
        let swTgCnt = el.querySelector('.switch-toggle-content'),
            cnt1 = swTgCnt.querySelector('.switch-content-1'),
            cnt2 = swTgCnt.querySelector('.switch-content-2');

        if(swTgCnt.querySelector(".tpgb-carousel")){
            let scope = swTgCnt.querySelectorAll('.tpgb-carousel');
            scope.forEach(function(obj){
                var splideInit = slideStore.get(obj);
                splideInit.refresh();
            });
        }
        if(cnt1.querySelector(".tpgb-metro")){
            tpgb_metro_layout('');
        }
        if(cnt2.querySelector(".tpgb-metro")){
            tpgb_metro_layout('');
        }

        if( cnt1 !== null && cnt1.querySelectorAll(".tpgb-isotope .post-loop-inner") !== null ){
            let postList1 = cnt1.querySelectorAll(".tpgb-isotope .post-loop-inner");
			postList1.forEach((plist1)=>{
				setTimeout(function(){				
					jQuery(plist1).isotope('layout');
				}, 50);
			})
        }
        if( cnt2 !== null && cnt2.querySelectorAll(".tpgb-isotope .post-loop-inner") !== null ){
			let postList2 = cnt2.querySelectorAll(".tpgb-isotope .post-loop-inner");
			postList2.forEach((plist2)=>{
				setTimeout(function(){				
					jQuery(plist2).isotope('layout');
				}, 50);
			})
            
        }
    }
}

