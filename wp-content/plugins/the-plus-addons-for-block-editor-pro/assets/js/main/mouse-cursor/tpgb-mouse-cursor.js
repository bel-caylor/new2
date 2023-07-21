/*Mouse Cursor*/ 
(function($) {
	"use strict";
	var doc = document;
	const elements = doc.querySelectorAll('.tpgb-mouse-cursor');

	elements.forEach( el => {
		var data = JSON.parse(el.getAttribute('data-tpgb_mc_settings')),
			leftoffset = data.mc_cursor_adjust_left,
			topoffset = data.mc_cursor_adjust_top;
	  if(data.effect!='' && data.effect!=undefined){
		if(data.effect=='mc-column'){
			var effectclass = el.closest(".tpgb-column,.tpgb-container-col"),
				ClassId = $(effectclass).data('id');
		}else if(data.effect == 'mc-row'){
			var effectclass = el.closest('.tpgb-section,.tpgb-container-row'),
				ClassId = $(effectclass).data('id');
		}else if(data.effect=='mc-block'){
			let bl_id = data.block_id;
			var blockSel = $(el).prev(),
				effectclass = blockSel[0];
				effectclass.setAttribute("data-id",'tpgb-mc-eff-'+bl_id);
				effectclass.classList.add('tpgb-mc-eff-'+bl_id);
				ClassId = $(effectclass).data('id');
				
			if(data.type != undefined && data.type == 'mouse-follow-image'){
				if(data.mc_cursor_icon !=undefined && data.mc_cursor_icon !=''){
					$(effectclass).append( "<img src='"+data.mc_cursor_icon+"' alt='Cursor Icon' class='tpgb-cursor-pointer-follow'>" );	
					if(data.mc_cursor_adjust_width && data.mc_cursor_adjust_width!=undefined){
						$('.tpgb-cursor-pointer-follow').css('max-width',data.mc_cursor_adjust_width);
					}
				}
			}
			if(data.type != undefined && data.type == 'mouse-follow-text'){
				if(data.mc_cursor_text !=undefined && data.mc_cursor_text !=''){
				  $(effectclass).append( "<div class='tpgb-cursor-pointer-follow-text'>"+data.mc_cursor_text+"</div>" );
				  if(data.mc_cursor_text_size!=undefined && data.mc_cursor_text_size!=''){
					  $('.tpgb-cursor-pointer-follow-text').css('font-size',data.mc_cursor_text_size+"px");
				  }
				  if(data.mc_cursor_text_color!=undefined && data.mc_cursor_text_color!=''){
					  $('.tpgb-cursor-pointer-follow-text').css('color',data.mc_cursor_text_color);
				  }
				  if(data.mc_cursor_text_width!=undefined && data.mc_cursor_text_width!=''){
					  $('.tpgb-cursor-pointer-follow-text').css('max-width',data.mc_cursor_text_width+"px");
				  }
				}
			}
			
			if(data.type != undefined && data.type == 'mouse-follow-circle'){
				if(data.circle_type != undefined && data.circle_type == 'cursor-predefine' || data.circle_type == 'cursor-custom' ){
				  $(effectclass).append( "<div class='tpgb-cursor-follow-circle'></div>" );	
					$('.tpgb-cursor-follow-circle').css({cursor: data.mc_cursor_adjust_symbol, zIndex: data.mc_circle_zindex, width: data.mc_cursor_adjust_width, maxWidth: data.mc_cursor_adjust_width, height: data.mc_cursor_adjust_height, maxHeight: data.mc_cursor_adjust_height});
					if (data.mc_cursor_adjust_style != undefined && data.mc_cursor_adjust_style == 'mc-cs3') {
					  $('.tpgb-cursor-follow-circle').css('mix-blend-mode',data.style_two_blend_mode);
					}
					
					if (data.mc_cursor_adjust_style != undefined) {
						var cursor = $('.tpgb-cursor-follow-circle');
						var selctcircletag = data.circle_tag_selector;
						var crcltransferNml = data.mc_circle_transformNml;						
						var crcltransferHvr = data.mc_circle_transformHvr;
						var crcltransitionNml = data.mc_circle_transitionNml;			
						var crcltransitionHvr = data.mc_circle_transitionHvr;	
						var stlcustmbgall = data.style_two_bg,
							stlcustmbgallh = data.style_two_bgh;
						$(selctcircletag).hover(function(){ 
						  cursor.css({ transform: crcltransferHvr}).css({ transition: 'transform '+ crcltransitionHvr +'s ease'}).css({ backgroundColor: stlcustmbgallh});
						  }, function(){
							cursor.css({ transform: crcltransferNml}).css({ transition: 'transform '+ crcltransitionNml +'s ease'}).css({ backgroundColor: stlcustmbgall});						      
						});
					}
					
				}
			}
		}else if(data.effect=='mc-body'){
			var aaa = doc.querySelector('body');
			aaa.setAttribute("data-id","tpmcbody");
			aaa.classList.add('tpgb-block-tpmcbody');
			var effectclass = el.closest('.tpgb-block-tpmcbody'),
			ClassId = $(effectclass).data('id');
		}
		
		if(screen.width > 991){
			if(data.type=='mouse-cursor-icon' && data.mc_cursor_icon!=''){
				var cssClassId = '';
					cssClassId = 'tpgb-block-'+ClassId;
					if(data.effect=='mc-block'){
						cssClassId = ClassId;
					}
					
				var is_hover = '';
				if(data.icon_type !=undefined && data.icon_type =='icon-predefine'){
					if(data.mc_cursor_icon !=undefined && data.mc_cursor_icon !=''){
						$('head').append('<style type="text/css">.'+cssClassId+',.'+cssClassId+' *,.'+cssClassId+' *:hover{cursor: '+data.mc_cursor_icon+';}</style>');						
					}
				}
					
				if(data.icon_type !=undefined && data.icon_type =='icon-custom'){
					if(data.mc_cursor_see_more!=undefined && data.mc_cursor_see_more=='yes' && data.mc_cursor_see_icon!=''){
						is_hover = '.'+cssClassId+' a,.'+cssClassId+' a *,.'+cssClassId+' a *:hover{cursor: -webkit-image-set(url('+data.mc_cursor_see_icon+') 2x) 0 0,pointer !important;cursor: url('+data.mc_cursor_see_icon+'),auto !important;}';
					}
					if(data.mc_cursor_icon !=undefined && data.mc_cursor_icon !=''){
						$('head').append('<style type="text/css">.'+cssClassId+',.'+cssClassId+' *,.'+cssClassId+' *:hover{cursor: -webkit-image-set(url('+data.mc_cursor_icon+') 2x) 0 0,pointer;cursor: url('+data.mc_cursor_icon+'),auto ;}'+is_hover+'</style>');
					}
				}
			}else if(data.type=='mouse-follow-text' && data.mc_cursor_text!='' && effectclass && effectclass!=undefined){
				effectclass.addEventListener("mouseenter", function(){
					effectclass.style.cursor = 'auto';
					effectclass.classList.add('cursor-active');
					var wdh = doc.querySelector('.tpgb-cursor-pointer-follow-text').offsetWidth;
					var wdhVal = wdh/2;
					var hgt = doc.querySelector('.tpgb-cursor-pointer-follow-text').offsetHeight;
					var hgtVal = hgt/2;
					$(document).mousemove(function(e){
						$('.tpgb-cursor-pointer-follow-text',this).offset({
							left: e.pageX + Number(leftoffset) - Number(wdhVal),
							top: e.pageY + Number(topoffset) - Number(hgtVal)
						});
					});
					
					if(data.mc_cursor_see_more=='yes' && data.mc_cursor_see_text!=''){
						let alla = effectclass.querySelectorAll('a');
						if(alla && alla!=undefined){
							alla.forEach( a => {
								a.addEventListener("mouseenter", function(){
									effectclass.querySelector('.tpgb-cursor-pointer-follow-text').textContent = data.mc_cursor_see_text;
								})
								a.addEventListener("mouseleave", function(){
									effectclass.querySelector('.tpgb-cursor-pointer-follow-text').textContent = data.mc_cursor_text;
								})
							})
						}
					}
				});
				effectclass.addEventListener("mouseleave", function(){
					effectclass.classList.remove('cursor-active');
				});
			}else if(data.type=='mouse-follow-image' && data.mc_cursor_icon !=undefined && data.mc_cursor_icon !='' && effectclass && effectclass!=undefined){
				effectclass.addEventListener("mouseenter", function(){
					effectclass.style.cursor = 'auto';
					effectclass.classList.add('cursor-active');
					
					$(document).mousemove(function(e){
						$('.tpgb-cursor-pointer-follow',this).offset({
							left: e.pageX + Number(leftoffset),
							top: e.pageY + Number(topoffset)
						});
					});
					
					if(data.mc_cursor_see_more=='yes' && data.mc_cursor_see_icon!=''){
						let alla = effectclass.querySelectorAll('a');
						if(alla && alla!=undefined){
							alla.forEach( a => {
								a.addEventListener("mouseenter", function(){
									effectclass.querySelector('.tpgb-cursor-pointer-follow').src = data.mc_cursor_see_icon;
								})
								a.addEventListener("mouseleave", function(){
									effectclass.querySelector('.tpgb-cursor-pointer-follow').src = data.mc_cursor_icon;
								})
							})
						}
					}
				});
				effectclass.addEventListener("mouseleave", function(){
					effectclass.classList.remove('cursor-active');
				});
			}else if(data.type=='mouse-follow-circle' && effectclass && effectclass!=undefined){
				//Last Edit
				if (data.mc_cursor_adjust_style != undefined && data.mc_cursor_adjust_style == 'mc-cs1' || data.mc_cursor_adjust_style == 'mc-cs2' || data.mc_cursor_adjust_style == 'mc-cs3') {
					$(data.circle_tag_selector).on("mouseenter",function(){
						$(effectclass).addClass("tpgb-mouse-hover-active");
					});

					$(data.circle_tag_selector).on("mouseleave",function(){					
						$(effectclass).removeClass("tpgb-mouse-hover-active");
					});
				}
				
				if(data.effect=='mc-body'){		     
					if (data.mc_cursor_adjust_style != undefined && (data.mc_cursor_adjust_style == 'mc-cs2' || data.mc_cursor_adjust_style == 'mc-cs2' || data.mc_cursor_adjust_style == 'mc-cs2')) { 
						window.onload = function() {  
						  const crclcursor = document.querySelector('.tpgb-cursor-follow-circle');
						  const svg = document.querySelector('.tpgb-mc-svg-circle'); //svg
						  const progressBar = document.querySelector('.tpgb-mc-circle-progress-bar');
						  const totalLength = progressBar.getTotalLength();
							setTopValue(svg);  
							progressBar.style.strokeDasharray = totalLength;
							progressBar.style.strokeDashoffset = totalLength;
							window.addEventListener('scroll', () => {
								 setProgress(crclcursor, progressBar, totalLength);
							}); 
							window.addEventListener('resize', () => {
							  setTopValue(svg);
							});
						}
						
						function setTopValue(svg) {
						  svg.style.top = document.documentElement.clientHeight * 0.5 - (svg.getBoundingClientRect().height * 0.5) + 'px';
						}
						 function setProgress(crclcursor, progressBar, totalLength) {
							const clientHeight = document.documentElement.clientHeight;
							const scrollHeight = document.documentElement.scrollHeight;
							const scrollTop = document.documentElement.scrollTop;
							let percentage = scrollTop / (scrollHeight - clientHeight);
                            percentage = percentage.toFixed(2)
							if(percentage == 1 || percentage == 1.00) {
								crclcursor.classList.add('mc-circle-process-done');
							} else {
								crclcursor.classList.remove('mc-circle-process-done');
							}
							progressBar.style.strokeDashoffset = totalLength - totalLength * percentage;
						}
					}
				}
				
				effectclass.addEventListener("mouseenter", function(){
					if(data.circle_type=='cursor-custom' && data.mc_cursor_icon!=''){
						effectclass.querySelector('.tpgb-cursor-follow-circle').style.cursor = "url('"+data.mc_cursor_icon+"'), auto";
					}
					
					if(data.circle_type=='cursor-custom'){
						var cssClassId = '';
						cssClassId = 'tpgb-block-'+ClassId;
						if(data.effect=='mc-block'){
							cssClassId = ClassId;
						}
                       effectclass.querySelector('.tpgb-cursor-follow-circle').style.pointerEvents = 'none';
                        if(data.mc_cursor_icon !=undefined && data.mc_cursor_icon !=''){
	                    	var is_circle = '';
							if(data.mc_cursor_see_more!=undefined && data.mc_cursor_see_more =='yes' && data.mc_cursor_see_icon!=''){
								var is_circle = '.'+cssClassId+' a,.'+cssClassId+' a *,.'+cssClassId+' a *:hover{cursor: -webkit-image-set(url('+data.mc_cursor_see_icon+') 2x) 0 0,pointer !important;cursor: url('+data.mc_cursor_see_icon+'),auto !important;}';
							}
							$('head').append('<style type="text/css">.'+cssClassId+',.'+cssClassId+' *,.'+cssClassId+' *:hover{cursor: -webkit-image-set(url('+data.mc_cursor_icon+') 2x) 0 0,pointer ;cursor: url('+data.mc_cursor_icon+'),auto;}'+is_circle+'</style>');
		                }       
					}	
					
					effectclass.style.cursor = 'auto';
					effectclass.classList.add('cursor-active');
					var wdh = effectclass.querySelector('.tpgb-cursor-follow-circle').offsetWidth;
					var wdhVal = wdh/2;
					var hgt = effectclass.querySelector('.tpgb-cursor-follow-circle').offsetHeight;
					var hgtVal = hgt/2;
					
					$(document).mousemove(function(e){
						$('.tpgb-cursor-follow-circle',this).offset({
							left: e.pageX + Number(leftoffset) - Number(wdhVal),
							top: e.pageY + Number(topoffset) - Number(hgtVal)
						});
					});
					
					if(data.mc_cursor_see_more=='yes' && data.mc_cursor_see_icon!='' && data.circle_type=='cursor-custom'){
						let alla = effectclass.querySelectorAll('a');
						if(alla && alla!=undefined){
							alla.forEach( a => {
								a.addEventListener("mouseenter", function(){
									effectclass.querySelector('.tpgb-cursor-follow-circle').style.cursor = 'url('+data.mc_cursor_see_icon+')';
								})
								a.addEventListener("mouseleave", function(){
									effectclass.querySelector('.tpgb-cursor-follow-circle').style.cursor = 'url('+data.mc_cursor_icon+')';
								})
							})
						}
					}
					effectclass.addEventListener("mouseleave", function(){
						effectclass.classList.remove('cursor-active');
					});
					
				});
				
			}
		}
	  }
	});
})(jQuery);