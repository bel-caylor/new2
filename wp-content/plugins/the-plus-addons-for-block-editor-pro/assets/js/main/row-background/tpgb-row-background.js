( function( $ ) {
    "use strict";
    
    $('.tpgb-section:not(.tpgb-section-editor),.tpgb-container-row:not(.tpgb-container-row-editor)').each(function(){
		var container = $(this).find('.tpgb-deep-layer'),
			middlecls = $(this).find('.tpgb-middle-layer');
		
        if(container.hasClass('tpgb-img-parallax-hover')){
            var dopacity= container.attr('data-opacity'),
                damount= container.attr('data-amount'),
                dperspective= container.attr('data-perspective'),
                dscale= container.attr('data-scale'),
                dtype= container.attr('data-type');
            
            container.css('opacity',dopacity);		
            var offset = 0;
            if ( dtype === 'tilt' ) {
                offset = - parseInt( damount ) * .6 + '%';
                } else {
                offset = - parseInt( damount ) + 'px';
            }
            container.css({'top' : offset, 'left' : offset, 'right' : offset, 'bottom' : offset, 'transform' : 'scale('+dscale+') perspective('+dperspective+'px)'});
			
            var elements = document.querySelectorAll('.tpgb-img-parallax-mouse');
			
			Array.prototype.forEach.call(elements, function(el, i) {
				// find Row
                var row = el.parentNode;
				//row.style.overflow = 'hidden';
				row.classList.add('image_parallax_row');
				
			});
			
			
			// Bind to mousemove so animate the hover row
			var elements = document.querySelectorAll('.image_parallax_row');
			Array.prototype.forEach.call(elements, function(row, i) {
				
				row.addEventListener('mousemove', function(e) {
					
					// Get the parent row
					var parentRow = e.target.parentNode;
					while ( ! parentRow.classList.contains('image_parallax_row') ) {
						
						if ( parentRow.tagName === 'HTML' ) {
							return;
						}
						
						parentRow = parentRow.parentNode;
					}
					
					// Get the % location of the mouse position inside the row
					var rect = parentRow.getBoundingClientRect();
					var top = e.pageY - ( rect.top + window.pageYOffset );
					var left = e.pageX  - ( rect.left + window.pageXOffset );
					top /= parentRow.clientHeight;
					left /= parentRow.clientWidth;
					
					// Move all the hover inner divs
					var hoverRows = parentRow.querySelectorAll('.tpgb-img-parallax-hover');
					Array.prototype.forEach.call(hoverRows, function(hoverBg, i) {
						
						// Parameters
						var amount = parseFloat( hoverBg.getAttribute( 'data-amount' ) );
						var dperspective = parseFloat( hoverBg.getAttribute( 'data-perspective' ) );
						var dscale = parseFloat( hoverBg.getAttribute( 'data-scale' ) );
						var inverted = hoverBg.getAttribute( 'data-inverted' ) === 'true';
						var transform;
						
						if ( hoverBg.getAttribute( 'data-type' ) === 'tilt' ) {
							var rotateY = left * amount - amount / 2;
							var rotateX = ( 1 - top ) * amount - amount / 2;
							if ( inverted ) {
								rotateY = ( 1 - left ) * amount - amount / 2;
								rotateX = top * amount - amount / 2;
								
							}
							
							transform = 'scale('+dscale+') perspective('+dperspective+'px) ';
							transform += 'rotateY(' + rotateY + 'deg) ';
							transform += 'rotateX(' + rotateX + 'deg) ';
							
							hoverBg.style.transition = 'all 0s';
							hoverBg.style.webkitTransform = transform;
							hoverBg.style.transform = transform;
							
						} else {
							
							var moveX = left * amount - amount / 2;
							var moveY = top * amount - amount / 2;
							if ( inverted ) {
								moveX *= -1;
								moveY *= -1;
							}
							transform = 'scale('+dscale+') translate3D(' + moveX + 'px, ' + moveY + 'px, 0) ';
							
							hoverBg.style.transition = 'all 0s';
							hoverBg.style.webkitTransform = transform;
							hoverBg.style.transform = transform;
						}
						
					});
				});
				
				
				// Bind to mousemove so animate the hover
				row.addEventListener('mouseout', function(e) {
					
					// Get the parent row
					var parentRow = e.target.parentNode;
					while ( ! parentRow.classList.contains('image_parallax_row') ) {
						
						if ( parentRow.tagName === 'HTML' ) {
							return;
						}
						
						parentRow = parentRow.parentNode;
					}
					
					// Reset all the animations
					var hoverRows = parentRow.querySelectorAll('.tpgb-img-parallax-hover');
					Array.prototype.forEach.call(hoverRows, function(hoverBg, i) {
						
						var amount = parseFloat( hoverBg.getAttribute( 'data-amount' ) );
						var scale = parseFloat( hoverBg.getAttribute( 'data-scale' ) );
						var perspective = parseFloat( hoverBg.getAttribute( 'data-perspective' ) );
						
						hoverBg.style.transition = 'all 3s ease-in-out';
						if ( hoverBg.getAttribute( 'data-type' ) === 'tilt' ) {
							hoverBg.style.webkitTransform = 'scale('+scale+') perspective('+perspective+'px) rotateY(0) rotateX(0)';
							hoverBg.style.transform = 'scale('+scale+') perspective('+perspective+'px) rotateY(0) rotateX(0)';
							} else {
							hoverBg.style.webkitTransform = 'scale('+scale+') translate3D(0, 0, 0)';
							hoverBg.style.transform = 'scale('+scale+') translate3D(0, 0, 0)';
						}
						
					});
				});
			});
		}
		if($(this).hasClass("tpgb-scroll-parallax")){

			var controller = new ScrollMagic.Controller();
			$('.tpgb-scroll-parallax').each(function(index, elem){
				
				var $bcg =  $(elem).find('.img-scroll-parallax');
				
				var slideParallaxScene = new ScrollMagic.Scene({
					triggerElement: elem,
					triggerHook: 1,
					duration: "200%"
				})
				.setTween(TweenMax.fromTo($bcg, 1, {backgroundPositionY: '15%', ease: "Power0.easeNone"},{backgroundPositionY: '85%', ease: "Power0.easeNone"}))
				.addTo(controller);
			})
		}
		if(container.hasClass("columns_animated_bg") || middlecls.hasClass("tpgb-automove-img") ){
			$('.columns_animated_bg,.tpgb-automove-img .tpgb-parlximg-wrap').each(function() {
				var $self = $(this),
				dir = $self.data('direction'),
				speed = 100 - $self.data('trasition'),
				coords = 0;
				
				setInterval(function() {
					if(dir == 'left' || dir == 'bottom')
					coords -= 1;
					else
					coords += 1;
					if(dir == 'left' || dir == 'right')
					$self.css('backgroundPosition', coords +'px 50%');
					else
					$self.css('backgroundPosition', '50% '+ coords + 'px');
				}, speed);
			});
		}
		if($(this).hasClass('tpgb-magic-scroll')){
			var controller = new ScrollMagic.Controller();
				$('.tpgb-magic-scroll').each(function(index, elem){
					var tween = 'tween-'+index;
					tween = new TimelineMax();
					var lengthBox = $(elem).find('.tpgb-parallax-scroll').length;
					var scroll_offset=$(elem).find('.tpgb-parallax-scroll').data("scroll_offset");
					var scroll_duration=$(elem).find('.tpgb-parallax-scroll').data("scroll_duration");
				for(var i=0; i < lengthBox; i++){
					var speed = 0.5;
					var scroll_type=$(elem).find('.tpgb-parallax-scroll').data("scroll_type");
					var scroll_x_from=$(elem).find('.tpgb-parallax-scroll').data("scroll_x_from");
					var scroll_x_to=$(elem).find('.tpgb-parallax-scroll').data("scroll_x_to");				
					var scroll_y_from=$(elem).find('.tpgb-parallax-scroll').data("scroll_y_from");
					var scroll_y_to=$(elem).find('.tpgb-parallax-scroll').data("scroll_y_to");
					var scroll_opacity_from=$(elem).find('.tpgb-parallax-scroll').data("scroll_opacity_from");
					var scroll_opacity_to=$(elem).find('.tpgb-parallax-scroll').data("scroll_opacity_to");
					var scroll_rotate_from=$(elem).find('.tpgb-parallax-scroll').data("scroll_rotate_from");
					var scroll_rotate_to=$(elem).find('.tpgb-parallax-scroll').data("scroll_rotate_to");
					var scroll_scale_from=$(elem).find('.tpgb-parallax-scroll').data("scroll_scale_from");
					var scroll_scale_to=$(elem).find('.tpgb-parallax-scroll').data("scroll_scale_to");
					
					var j1 = 0.2*(i+1);
					var k1 = 0.5*i;
					if(scroll_type=='position'){
						if(i==0) {
							
							tween.fromTo($(elem).find('.tpgb-parallax-scroll:eq('+i+')'), 1, {scale:scroll_scale_from,rotation:scroll_rotate_from,opacity:scroll_opacity_from,x:-(scroll_x_from*speed),y:-(scroll_y_from*speed), ease: Linear.easeNone},{scale:scroll_scale_to,rotation:scroll_rotate_to,opacity:scroll_opacity_to,x:-(scroll_x_to*speed),y:-(scroll_y_to*speed), ease: Linear.easeNone})
						}else {
							tween.to($(elem).find('.tpgb-parallax-scroll:eq('+i+')'), 1, {scale:scroll_scale_to,y:-(scroll_y_to*speed), ease: Linear.easeNone}, '-=1')
						}
					}
				}				
				new ScrollMagic.Scene({triggerElement: elem, duration: scroll_duration, triggerHook:.5,offset: scroll_offset})
					.setTween(tween)
					.addTo(controller);
			})
		}
		
		if(container.find("video.self-hosted-video, .tpgb-iframe").length){
			setTimeout(function(){
			$('video.self-hosted-video, .tpgb-iframe').tpgb_VideoBgInit();
				$('.self-hosted-video').each(function() {
					var $self=$(this);
                    const promise = $self[0].play();
                    if(promise !== undefined){
                        promise.then(() => {
                        }).catch(() => {
                            $self[0].muted = true;
                            $self[0].play()
                        });
                    }
				});
			}, 100);
			$.fn.tpgb_VideoBgInit = function() {
				return this.each(function() {
					var $self = $(this),
						ratio = 1.778,
						pWidth = $self.parent().width(),
						pHeight = $self.parent().height(),
						selfWidth,
						selfHeight;
					var setSizes = function() {
						if(pWidth / ratio < pHeight) {
							selfWidth = Math.ceil(pHeight * ratio);
							selfHeight = pHeight;
							$self.css({
								'width': selfWidth,
								'height': selfHeight
							});
						} else {
							selfWidth = pWidth;
							selfHeight = Math.ceil(pWidth / ratio);
							$self.css({
								'width': selfWidth,
								'height': selfHeight
							});
						}
					};				
					setSizes();
					$(window).on('resize', setSizes);
				});
			}
		}
		//You tube Video
		if(container.hasClass("tpgb-video-youtube")){

			var tag = document.createElement('script');
			tag.src = "//www.youtube.com/iframe_api";
			var firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
			
			var players = {};

			window.onYouTubeIframeAPIReady = function() {
				$('.tpgb-video-youtube iframe').each(function() {
					var $self = $(this),
						id = $self.attr('id');
						players[id] = new YT.Player(id, {   
							   playerVars: {autoplay:1},    
							events: {
							   onReady: function(e) {
								   
							  	if($self.data('muted') && $self.data('muted') == '1') {
								  e.target.mute();
							   	}
								  e.target.playVideo();
							   },
							   onStateChange: function(e) {
									if(e && e.data === 1){
										var videoHolder = document.getElementsByClassName('video-'+id);
										if(videoHolder){
											videoHolder[0].classList.remove('tp-loading');
										}
									}else if(e && e.data === 0){
										e.target.playVideo()
									}
								}
							},
							
						});
					
				});
			};
		}
		//Vimeo Video
		if(container.hasClass("tpgb-video-vimeo")){
			
			$('.tpgb-video-vimeo iframe').each(function() {
				var $self = $(this);

				if (window.addEventListener) {
					window.addEventListener('message', onMessageReceived, false);
				} else {
					window.attachEvent('onmessage', onMessageReceived, false);
				}
		
				function onMessageReceived(e) {
					if(e.origin==='https://player.vimeo.com'){
						var data = JSON.parse(e.data),
							id = $self.attr('id');
						
						switch (data.event) {
							case 'ready':
								$self[0].contentWindow.postMessage('{"method":"play", "value":1}', 'https://player.vimeo.com' );
								if($self.data('muted') && $self.data('muted') == '1') {
									$self[0].contentWindow.postMessage('{"method":"setVolume", "value":0}', 'https://player.vimeo.com' );
								}
								var videoHolder = document.getElementsByClassName('video-'+id);
								if(videoHolder){
									videoHolder[0].classList.remove('tp-loading');
								}
								break;
						}
					}
				}
			});
			
		}
		//Self Hosted Video 
		if(container.hasClass("tpgb-video-self-hosted")){
			$(".tpgb-video-self-hosted .self-hosted-video").each(function() {
				
				var dk_mp4=$(this).data("dk-mp4"),
					width = window.innerWidth,
					dk_webm=$(this).data("dk-webm");

				$(this).css('width' , width );
				if(dk_mp4!=undefined && dk_mp4!=''){
					var mp4_video='<source src="'+dk_mp4+'" type="video/mp4">';
					$(this).append(mp4_video);
				}
				if(dk_webm!=undefined && dk_webm!=''){
					var webm_video='<source src="'+dk_webm+'" type="video/webm">';
					$(this).append(webm_video);
				}
				//}
			});
		}

		//Background Gallery Image
		if(container.hasClass("row-img-slide")){
            var gallery = container.find('.row-bg-slide').data('imgdata'),
            conId = container.attr('id'),
            option = container.find('.row-bg-slide').data('galleryopt');

            $('#'+conId+' .row-bg-slide').vegas({
                timer: false,
                transitionDuration: option.transduration,
                transition : option.transition,
                delay: option.duration,
                slides:   gallery ,
                animation: option.animation,
                overlay: option.textureoly,
            });
		}

		//Particle js 
		if(middlecls.hasClass('canvas-style-2')){
			var can2_color = middlecls.attr('data-color');
			if($('#canvas-style-2').length){
					particlesJS("canvas-style-2",{particles:{number:{value:80,density:{enable:!0,value_area:800}},color:{value:can2_color},shape:{type:"circle",stroke:{width:0,color:"#000000"},polygon:{nb_sides:5},image:{src:"img/github.svg",width:100,height:100}},opacity:{value:.5,random:!1,anim:{enable:!1,speed:1,opacity_min:.1,sync:!1}},size:{value:2,random:!0,anim:{enable:!1,speed:40,size_min:.1,sync:!1}},line_linked:{enable:!0,distance:150,color:can2_color,opacity:.4,width:1},move:{enable:!0,speed:2,direction:"none",random:!1,straight:!1,out_mode:"out",bounce:!1,attract:{enable:!1,rotateX:600,rotateY:1200}}},interactivity:{detect_on:"canvas",events:{onhover:{enable:!0,mode:"grab"},onclick:{enable:!0,mode:"push"},resize:!0},modes:{grab:{distance:150,line_linked:{opacity:1}},bubble:{distance:400,size:40,duration:2,opacity:8,speed:3},repulse:{distance:200,duration:.4},push:{particles_nb:4},remove:{particles_nb:2}}},retina_detect:!0});
				
			}			
		}
		if(middlecls.hasClass('canvas-style-3')){
			if($('#canvas-style-3').length){
				var can3_color = middlecls.attr('data-color');

				particlesJS("canvas-style-3",{particles:{number:{value:600,density:{enable:!0,value_area:800}},color:{value:can3_color},shape:{type:"circle",stroke:{width:0,color:"#000000"},polygon:{nb_sides:5},image:{src:"",width:100,height:100}},opacity:{value:0,random:!1,anim:{enable:!1,speed:0,opacity_min:0,sync:!1}},size:{value:3,random:!0,anim:{enable:!1,speed:40,size_min:.1,sync:!1}},line_linked:{enable:!0,distance:32.068241,color:can3_color,opacity:.8,width:1},move:{enable:!0,speed:4,direction:"none",random:!1,straight:!1,out_mode:"out",bounce:!1,attract:{enable:!1,rotateX:600,rotateY:1200}}},interactivity:{detect_on:"canvas",events:{onhover:{enable:true,mode:"repulse"},onclick:{enable:!1,mode:"push"},resize:!0},modes:{grab:{distance:400,line_linked:{opacity:1}},bubble:{distance:200,size:140,duration:2,opacity:8,speed:2},repulse:{distance:100,duration:.4},push:{particles_nb:4},remove:{particles_nb:2}}},retina_detect:!0});
				
			}			
		}
		if(middlecls.hasClass('canvas-style-4')){
			if($('#canvas-style-4').length){
				var can4_color = middlecls.attr('data-color');
				
				$(".canvas-style-4").particleground({
					minSpeedX: 0.1,
					maxSpeedX: 0.3,
					minSpeedY: 0.1,
					maxSpeedY: 0.3,
					directionX: "center",
					directionY: "up",
					density: 10000,
					dotColor: can4_color,
					lineColor: can4_color,
					particleRadius: 7,
					lineWidth: 1,
					curvedLines: false,
					proximity: 100,
					parallax: true,
					parallaxMultiplier: 5,
					onInit: function() {},
					onDestroy: function() {}
				  });
				
			}			
		}
		if(middlecls.hasClass('canvas-style-5')){
			if($('#canvas-style-5').length){
				var can5color = middlecls.attr('data-color'),
					can5type=middlecls.attr('data-type');
				particlesJS("canvas-style-5",{ particles:{number:{value:80,density:{enable:true,value_area:800}},color:{value:can5color},shape:{type:can5type,stroke:{width:4,color:can5color},polygon:{nb_sides:8},image:{src:"img/github.svg",width:100,height:100}},opacity:{value:0.5,random:false,anim:{enable:false,speed:1,opacity_min:0.1,sync:false}},size:{value:2,random:true,anim:{enable:false,speed:102.321728,size_min:25.174393,sync:true}},line_linked:{enable:true,distance:150,color:can5color,opacity:0.4,width:1},move:{enable:true,speed:6,direction:"none",random:false,straight:false,out_mode:"out",bounce:false,attract:{enable:false,rotateX:600,rotateY:1200}}},interactivity:{detect_on:"canvas",events:{onhover:{enable:false,mode:"grab"},onclick:{enable:true,mode:"push"},resize:true},modes:{grab:{distance:923.076923,line_linked:{opacity:1}},bubble:{distance:287.712287,size:40,duration:3.916083,opacity:1,speed:3},repulse:{distance:200,duration:0.4},push:{particles_nb:4},remove:{particles_nb:2}}},retina_detect:true } );
				
			}			
		}
		if(middlecls.hasClass('canvas-style-6')){
			if($('#canvas-style-6').length){
				var can6color = middlecls.attr('data-color'),
					can6type=middlecls.attr('data-type');

				particlesJS("canvas-style-6", { particles:{number:{value:10,density:{enable:true,value_area:800}},color:{value:can6color},shape:{type:can6type,stroke:{width:0,color:can6color},polygon:{nb_sides:5},image:{src:"img/github.svg",width:100,height:100}},opacity:{value:0.505074,random:true,anim:{enable:false,speed:1,opacity_min:0.1,sync:false}},size:{value:100.213253,random:true,anim:{enable:true,speed:10,size_min:40,sync:false}},line_linked:{enable:false,distance:481.023618,color:can6color,opacity:1,width:2},move:{enable:true,speed:8,direction:"none",random:false,straight:false,out_mode:"out",bounce:false,attract:{enable:false,rotateX:600,rotateY:1200}}},interactivity:{detect_on:"canvas",events:{onhover:{enable:false,mode:"bubble"},onclick:{enable:false,mode:"push"},resize:true},modes:{grab:{distance:431.568431,line_linked:{opacity:0.364281}},bubble:{distance:263.73626373626377,size:55.944055,duration:2.157842,opacity:0.335664,speed:3},repulse:{distance:239.760239,duration:0.4},push:{particles_nb:4},remove:{particles_nb:2}}},retina_detect:true} );
				
			}			
		}
		if(middlecls.hasClass('canvas-style-7')){
			if($('#canvas-style-7').length){
				var can7color = middlecls.attr('data-color'),
					can7type=middlecls.attr('data-type');

				particlesJS("canvas-style-7", { particles:{number:{value:400,density:{enable:true,value_area:2840.9315098761817}},color:{value:can7color},shape:{type:can7type,stroke:{width:0,color:can7color},polygon:{nb_sides:5},image:{src:"img/github.svg",width:100,height:100}},opacity:{value:0.5,random:true,anim:{enable:false,speed:1,opacity_min:0.1,sync:false}},size:{value:11,random:true,anim:{enable:false,speed:40,size_min:0.1,sync:false}},line_linked:{enable:false,distance:224.4776885211732,color:can7color,opacity:0.1683582663908799,width:1.2827296486924182},move:{enable:true,speed:3,direction:"bottom",random:true,straight:false,out_mode:"bounce",bounce:false,attract:{enable:false,rotateX:881.8766334760375,rotateY:1200}}},interactivity:{detect_on:"canvas",events:{onhover:{enable:true,mode:"bubble"},onclick:{enable:true,mode:"repulse"},resize:true},modes:{grab:{distance:400,line_linked:{opacity:0.5}},bubble:{distance:400,size:4,duration:0.3,opacity:1,speed:3},repulse:{distance:200,duration:0.4},push:{particles_nb:4},remove:{particles_nb:2}}},retina_detect:true} );
				
			}			
		}
		if(middlecls.hasClass('canvas-style-8')){
            var show_parti = document.querySelector('.tpgb-snow-particles');
            
			let circles, target, animateHeader = true,
			 	canvas = show_parti,
			 	width = middlecls.innerWidth(),
			 	height = middlecls.innerHeight(),
			 	canvas_header = middlecls,
			 	ctx = canvas.getContext('2d');

			initHeader();
			addListeners();

			function initHeader() {
				canvas.width = width;
				canvas.height = height;
				target = {
					x: 0,
					y: height
				};
				canvas_header.css({
					'height': height + 'px'
				});
				circles = [];
				for (let x = 0; x < width * 0.5; x++) {
					let c = new Circle();
					circles.push(c);
				}
				animate();
			}

			function addListeners() {
				window.addEventListener('scroll', scrollCheck);
				window.addEventListener('resize', resize);
			}

			function scrollCheck() {
				if (document.body.scrollTop > height) animateHeader = false;
				else animateHeader = true;
			}

			function resize() {
				width = window.innerWidth;
				height = window.innerHeight;
				canvas_header.css({
					'height': height + 'px'
				});
				canvas.width = width;
				canvas.height = height;
			}

			function animate() {
				if (animateHeader) {
					ctx.clearRect(0, 0, width, height);
					for (let i in circles) {
						circles[i].draw();
					}
				}
				requestAnimationFrame(animate);
			}


			function Circle() {
                let $this = this;
                
				(function () {
					$this.pos = {};
					init();
				})();

				function init() {
					$this.pos.x = Math.random() * width;
					$this.pos.y = height + Math.random() * 100;
					$this.alpha = 0.1 + Math.random() * 0.4;
					$this.scale = 0.1 + Math.random() * 0.3;
					$this.velocity = Math.random();
				}

				this.draw = function () {
					if ($this.alpha <= 0) {
						init();
					}
					$this.pos.y -= $this.velocity;
					$this.alpha -= 0.0003;
					ctx.beginPath();
					ctx.arc($this.pos.x, $this.pos.y, $this.scale * 10, 0, 2 * Math.PI, false);
					ctx.fillStyle = 'rgba(255,255,255,' + $this.alpha + ')';
					ctx.fill();
				};
			}
		}
		if(middlecls.hasClass('canvas-custom')){
			if($('#canvas-custom').length){
				var cutJson = middlecls.data('patijson');
				
				particlesJS("canvas-custom", cutJson  );
			}
        }
       
		/* mouse hover parallax image*/
		if(middlecls.hasClass("tpgb-mordern-parallax")){
			var elements = document.querySelectorAll('.tpgb-parlximg-wrap')
			Array.prototype.forEach.call(elements, function(el, i) {
				// find Row
				var row = el.parentNode;
				row.parentElement.parentElement.classList.add('tpgb-image-parallax-row');		
			});
			//Bind to mousemove so animate the hover row
			var elements = document.querySelectorAll('.tpgb-image-parallax-row');
			Array.prototype.forEach.call(elements, function(row, i) {
				
				row.addEventListener('mousemove', function(e) {
					// Get the parent row
					var parentRow = e.target.parentNode;
					while ( ! parentRow.classList.contains('tpgb-image-parallax-row') ) {
						if ( parentRow.tagName === 'HTML' ) {
							return;
						}
						parentRow = parentRow.parentNode;
					}
					
					// Get the % location of the mouse position inside the row
					var rect = parentRow.getBoundingClientRect();
					var top = e.pageY - ( rect.top + window.pageYOffset );
					var left = e.pageX  - ( rect.left + window.pageXOffset );
					top /= parentRow.clientHeight;
					left /= parentRow.clientWidth;
					
					// Move all the hover inner divs
					var hoverRows = parentRow.querySelectorAll('.tpgb-parlximg');
					Array.prototype.forEach.call(hoverRows, function(hoverBg, i) {
						// Parameters
						var amount = parseFloat( hoverBg.getAttribute( 'data-parallax' ) );
						TweenLite.to( hoverBg, 0.2, {x : -(( e.clientX - (window.innerWidth/2) ) / amount ),y : -(( e.clientY - (window.innerHeight/2) ) / amount ) });
					});
				});

				// Bind to mousemove so animate the hover row to it's default state
				row.addEventListener('mouseout', function(e) {
					// Get the parent row
					var parentRow = e.target.parentNode;
					while ( ! parentRow.classList.contains('tpgb-image-parallax-row') ) {
						if ( parentRow.tagName === 'HTML' ) {
							return;
						}
						parentRow = parentRow.parentNode;
					}
					// Reset all the animations
					var hoverRows = parentRow.querySelectorAll('.tpgb-parlximg');
					Array.prototype.forEach.call(hoverRows, function(hoverBg, i) {
						var amount = parseFloat( hoverBg.getAttribute( 'data-parallax' ) );
						TweenLite.to( hoverBg, 0.2, {x : -(( e.clientX - (window.innerWidth/2) ) / amount ),y : -(( e.clientY - (window.innerHeight/2) ) / amount ) });
					});
				});
			});
		}
		
    })
})(jQuery);