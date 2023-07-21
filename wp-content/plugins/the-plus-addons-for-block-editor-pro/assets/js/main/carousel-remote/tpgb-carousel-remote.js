(function($) {
	"use strict";
    $('.tpgb-carousel-remote').each(function(){
		var $this = $(this),
        dotdiv = $this.find('.tpgb-carousel-dots .tpgb-carodots-item'),
        exid = $this.data('extra-conn'),
        acttab = $('.'+exid+'.tpgb-tabs-wrapper').find('.tpgb-tab-li .tpgb-tab-header.active'),
        activetab = acttab.data('tab');
       
			$(".slider-btn",this).on("click", function(e){
				e.preventDefault();
				
				var remote_uid=$(this).data("id"),
                    remote_type = $(this).closest(".tpgb-carousel-remote").data("remote"),
                    carousel_slide=$(this).data("nav"),
                    extrconn = $(this).closest(".tpgb-carousel-remote").data("extra-conn");

			    if(remote_uid!='' && remote_uid!=undefined && remote_type=='switcher'){
					
					var switcher_toggle=$(this).data("nav");
					
					var switch_toggle = $('#'+remote_uid).find('.switch-toggle');
					var switch_1_toggle = $('#'+remote_uid).find('.switch-1');
					var switch_2_toggle = $('#'+remote_uid).find('.switch-2');
					
					$(".tpgb-carousel-remote .slider-btn").removeClass("active");
					$(this).addClass("active");
					
					if(switcher_toggle=='next'){
						switch_2_toggle.trigger("click");							
					} else if(switcher_toggle=='prev'){	
						switch_1_toggle.trigger("click");
					}
                }
                
                if(extrconn !== '' && remote_type=='tab'){
                    var tab_exid = $('.'+extrconn+'.tpgb-tabs-wrapper').data('extra-conn') ,
                        tab =$('.'+extrconn+'.tpgb-tabs-wrapper').find('.tpgb-tabs-nav').children().length;
                        
                    
                    if(tab_exid != ''){
                        if(carousel_slide=='next'){
                            activetab++;
                            if (activetab > tab){  activetab = 1; }
                            $('.'+extrconn+'.tpgb-tabs-wrapper').find('.tpgb-tab-li .tpgb-tab-header[data-tab="'+parseInt(activetab)+'"]').trigger("click");
                        } else if(carousel_slide=='prev') {
                            activetab--;
                            if (activetab < 1 ){
                                activetab = tab;
                            }
                            $('.'+extrconn+'.tpgb-tabs-wrapper').find('.tpgb-tab-li .tpgb-tab-header[data-tab="'+parseInt(activetab)+'"]').trigger("click");
                        }
                    }
                }

			});
		});
})(jQuery);