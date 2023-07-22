( function( $ ) {
    "use strict";
    
    $('.tpgb-section:not(.tpgb-section-editor),.tpgb-container-row:not(.tpgb-container-row-editor)').each(function(){
		var container = $(this).find('.tpgb-deep-layer');

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
    })
})(jQuery);