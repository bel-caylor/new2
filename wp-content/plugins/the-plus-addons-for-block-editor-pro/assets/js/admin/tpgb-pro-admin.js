(function ($) {
	"use strict";
		$(".tpgb-fontawesome-kit-pro").on("click", function(e) {
			var $this= $(this), innerspan = $(this).find('>span'),fawesome = $("#fontawesome_pro_kit").val();
			if(fawesome!='' && fawesome!=undefined){
				$this.addClass("check-loading");
				innerspan.removeClass("dashicons dashicons-image-rotate").addClass("inner-load");
				var fontawesome = "https://kit.fontawesome.com/"+fawesome+".js"
				$.get(fontawesome, function() {
					$this.removeClass("check-loading");
					$this.addClass("check-sucess");
					innerspan.addClass("dashicons dashicons-yes").removeClass("inner-load");
				}).fail(function() {
					$this.removeClass("check-loading");
					$this.addClass("check-failed");
					innerspan.addClass("dashicons dashicons-no-alt").removeClass("inner-load");
				}).done(function() {
					$this.removeClass("check-loading");
					setTimeout(function(){
						$this.removeClass("check-failed check-sucess");
						innerspan.removeClass("inner-load dashicons dashicons-saved dashicons-yes dashicons-no-alt");
						innerspan.addClass("dashicons dashicons-image-rotate");
					}, 5000);
				});
			}
		})
		
		//Woocommerce Custom Color Picker
		if($('.tpgb-color-picker').length > 0 ){
			$('input.tpgb-color-picker').wpColorPicker();
		}

		//Woocommerce Custom Image Uploader
		$(document).on('click', 'button.tpgb_upload_image_button', function(e){
				e.preventDefault();
                e.stopPropagation();

                var file_frame = void 0;
				var _this = this;

                if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {

                    // If the media frame already exists, reopen it.
                    if (file_frame) {
                        file_frame.open();
                        return;
                    }

                    // Create the media frame.
                    file_frame = wp.media.frames.select_image = wp.media({
                        title: 'Choose an Image',
                        button: {
                            text: 'Use Image'
                        },
                        multiple: false
                    });

                    // When an image is selected, run a callback.
                    file_frame.on('select', function () {
                        var attachment = file_frame.state().get('selection').first().toJSON();

                        if ($.trim(attachment.id) !== '') {

                            var url = typeof attachment.sizes.thumbnail === 'undefined' ? attachment.sizes.full.url : attachment.sizes.thumbnail.url;

                            $(_this).prev().val(attachment.id);
                            $(_this).closest('.tpgb-meta-image-field-wrapper').find('img').attr('src', url);
                            $(_this).next().show();
                        }
                        //file_frame.close();
                    });

                    // When open select selected
                    file_frame.on('open', function () {

                        // Grab our attachment selection and construct a JSON representation of the model.
                        var selection = file_frame.state().get('selection');
                        var current = $(_this).prev().val();
                        var attachment = wp.media.attachment(current);
                        attachment.fetch();
                        selection.add(attachment ? [attachment] : []);
                    });

                    // Finally, open the modal.
                    file_frame.open();
                }
		});

		$(document).on('click', 'button.tpgb_remove_image_button', function(e){
			e.preventDefault();
			e.stopPropagation();
			var placeholder = $(this).closest('.tpgb-meta-image-field-wrapper').find('img').data('placeholder');
			$(this).closest('.tpgb-meta-image-field-wrapper').find('img').attr('src', placeholder);
			$(this).prev().prev().val('');
			$(this).hide();
			return false;
		});
		/* Pro Rollback */
		if($('.tpgb-rollback-pro-wrapper').length){
			$('.tpgb-rollback-pro-wrapper').each(function(){
				var $this = $(this),
				rb_btn = $this.find('.tpgb-pro-rollback-button'),
				data_btn_text = rb_btn.data('rv-pro-text'),
				data_btn_url = rb_btn.data('rv-url'),
				rb_select = $this.find('.tpgb-rollback-pro-list').val();
				if(rb_select){
					rb_btn.html(data_btn_text.replace('{TPGBP_VERSION}', rb_select));
					rb_btn.attr('href', data_btn_url.replace('TPGBP_VERSION', rb_select));
				}
				$this.find('.tpgb-rollback-pro-list').on("change",function(){
					rb_btn.html(data_btn_text.replace('{TPGBP_VERSION}', $(this).val()));
					rb_btn.attr('href', data_btn_url.replace('TPGBP_VERSION', $(this).val()));
				});
				rb_btn.on('click', function (e) {
					e.preventDefault();
					var $btn_this = $(this);
					if(confirm("Are you sure you want to reinstall previous version?")){
						location.href = $btn_this.attr('href');
					}
				});
			});
		}
		/* Pro Rollback */
})(window.jQuery); 