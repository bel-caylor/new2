( function( $ ) {
    if($(".tpgb-navbuilder.tpgb-web-access").length){
		$(".tpgb-navbuilder.tpgb-web-access a").each(function(){
			$(this)[0].addEventListener( 'focus', ToggleFocus, true );
			$(this)[0].addEventListener( 'blur', ToggleFocus, true );
		});
	}
    function ToggleFocus(){
        var navaTag = this,
            self = this.parentElement,
            hoversty= $(navaTag).closest(".tpgb-nav-inner").data("menu_transition");
        
		while ( -1 === self.className.indexOf( 'navbar-nav' ) ) {
		   var dmenu = self.querySelector('ul.dropdown-menu');
			if ( 'li' === self.tagName.toLowerCase() ) {
			   
				if ( -1 !== self.className.indexOf( 'open' ) ) {
					self.className = self.className.replace( ' open', '' );
					if(dmenu) {
						if(hoversty == 'style-1'){
							$(dmenu).stop().slideUp(400);
						}else if(hoversty == 'style-2'){
							$(dmenu).stop(true, true).delay(100).fadeOut(400);
						}else if(hoversty == 'style-3' || hoversty == 'style-4'){
							dmenu.classList.remove('open-menu');
						}
					}
				} else {    
					self.classList.add('open');
					if(dmenu) {
						if(hoversty == 'style-1'){
							$(dmenu).stop().slideDown(400);
						}else if(hoversty == 'style-2'){
							$(dmenu).stop(true, true).delay(100).fadeIn(400);
						}else if(hoversty == 'style-3' || hoversty == 'style-4'){
							dmenu.classList.add('open-menu');
						}
					}
				}
				
			}
			self = self.parentElement;
		}
    }
})(jQuery);