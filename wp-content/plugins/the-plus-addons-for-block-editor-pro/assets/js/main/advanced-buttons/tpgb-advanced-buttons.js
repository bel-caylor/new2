(function($) {
	"use strict";
	function tpgb_download_btn(link, name){
		var a = document.createElement("a");
			document.body.appendChild(a);
			a.style = "display: none";
			a.classList.add("tplus-download");
			a.href = link;
			a.download = name;
			a.click();
			window.URL.revokeObjectURL(link);
	}	
	$(".tpgb-advanced-buttons .adv_btn_ext_txt").on('click',function() {	
		$(this).closest(".tpgb-advanced-buttons").find( ".adv-button-link-wrap").trigger( "click" );
	});

	$(".adv-button-link-wrap").on('click',function() {
		//download button js start
		var $this = $(this),
			download_link = $(this).attr('href'),
			classDownload = $this.closest(".tpgb-adv-btn-inner.ab-download"),
			dfname = classDownload.data('dfname');

		if ($this.closest(".tpgb-adv-btn-inner").hasClass("ab-download")) {
			event.preventDefault();
		}
		//button 1 start
		if (classDownload.hasClass("tpgb-download-style-1")) {
			$this.toggleClass("downloaded");
			var a = document.createElement("a");
			tpgb_download_btn(download_link, dfname );
			setTimeout(function() {
				$this.removeClass("downloaded");
			}, 5000);
		}
		//button 1 end
		//button 2 start
		if (classDownload.hasClass("tpgb-download-style-2")) {
			$this.addClass("load");
			setTimeout(function() {
				$this.addClass("done");
			}, 1000);

			tpgb_download_btn(download_link, dfname );
			setTimeout(function() {
				$this.removeClass("load done");
			}, 5000);
		}
		//button 2 end

		//button 3 start
		if (classDownload.hasClass("tpgb-download-style-3")) {
			tpgb_download_btn(download_link, dfname );
		}
		//button 3 end

		//button 4 start
		if (classDownload.hasClass("tpgb-download-style-4")) {
			$this.addClass("loading");
			setTimeout(function() {
				$this.removeClass('loading');
				$this.addClass('success');

				tpgb_download_btn(download_link, dfname );

			}, 3000);

			setTimeout(function() {
				$this.removeClass("success");
			}, 8000);
		}
		//button 4 end

		//button 5 start
		if (classDownload.hasClass("tpgb-download-style-5")) {
			var style_5 = $this.closest(".tpgb-download-style-5");
			style_5.toggleClass('is-active');
			
			setTimeout(function() {
				style_5.find(".tp-meter").toggleClass('is-done');
				tpgb_download_btn(download_link, dfname );
			}, 4000);

			setTimeout(function() {
				style_5.removeClass("is-active");
				style_5.find(".tp-meter").removeClass("is-done");
			}, 5000);
		}
		//button 5 end

		//download button js end
	});
})(jQuery);