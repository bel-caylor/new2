/**Post Listing*/
document.addEventListener('DOMContentLoaded', (event) => {
    let dyHcntAll = document.querySelectorAll(".tpgb-post-listing.dynamic-style-1");
    if (dyHcntAll) {
        dyHcntAll.forEach(function(dyAll) {
            let dyListcnt = dyAll.querySelectorAll(".dynamic-list-content");
            if(dyListcnt){
                dyListcnt.forEach(function(el) {
                    el.addEventListener("mouseenter", function(e) {
                        let postHcnt = e.currentTarget.querySelector(".tpgb-post-hover-content")
                        slideDownP(postHcnt, 300)
                    });
                    el.addEventListener("mouseleave", function(e) {
                        let postHcnt = e.currentTarget.querySelector(".tpgb-post-hover-content")
                        slideUpP(postHcnt, 300)
                    });
                });
            }
            if(dyAll.classList.contains('tpgb-child-filter')){
                let filList = dyAll.querySelectorAll('.tpgb-filter-list');
                if(filList){
                    filList.forEach((fl)=>{
                        let fBtn = fl.querySelector('a');
                        if(fBtn){
                            fBtn.addEventListener('click', (e)=>{
                                e.preventDefault();
                                var get_filter = e.currentTarget.getAttribute("data-filter"),
                                get_filter_remove_dot = get_filter.split('.').join(""),  
                                get_sub_class = 'cate-parent-',
                                get_filter_add_class = get_sub_class.concat(get_filter_remove_dot);

                                let clostFil = e.currentTarget.closest('.tpgb-category-filter'),
                                    catFchild = clostFil.querySelectorAll('.category-filters-child');
                                if(get_filter_remove_dot=="*" && get_filter_remove_dot !=undefined){
                                    catFchild.forEach((catC)=>{
                                        if(catC.classList.contains('active')){
                                            catC.classList.remove('active');
                                        }
                                    })
                                }else{
                                    catFchild.forEach((catC)=>{
                                        if(catC.classList.contains('active')){
                                            catC.classList.remove('active');
                                        }
                                    })
                                    let childClass = clostFil.querySelector('.'+get_filter_add_class);
                                    if(childClass){
                                        childClass.classList.add('active');
                                    }
                                }
                            })
                        }
                    });
                }
             }

        });
    }
});