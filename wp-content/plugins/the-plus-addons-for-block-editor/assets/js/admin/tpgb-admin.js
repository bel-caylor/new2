(function ($) {
	"use strict";
	// Clear purge cache files styles and scripts
		var performace_cont = $('#cmb2-metabox-tpgb_performance');
		if(performace_cont.length > 0){
			var ids="tpgb-remove-smart-cache";
			var ids_dynamic="tpgb-remove-dynamic-style";
			var smart_action = '';
			var dynamic_action ='';
			
			if(performace_cont.length > 0){
				var selected = 'combine';
				if($("body").hasClass("perf-separate")){
					selected = 'separate';
				}

				var ondelayVal = false;
				if($("body").hasClass("perf-delay-true")){
					ondelayVal = true;
				}
				var delay_js = '<div class="cmb-th tpgb-smart-loader-delay tpgb-remove-dynamic-style"><label>Delay Extra JS <span class="tpgb-tooltip-dynamic">!</span></label><div class="block-check-wrap"><input type="checkbox" class="block-list-checkbox" name="tpgb-smart-loader" id="tp-delay-js-opt" value="'+ondelayVal+'" '+(ondelayVal == true ? 'checked="checked"' : '')+' /><label for="tp-delay-js-opt"></label></div></div>';

				var onDeferVal = false;
				if($("body").hasClass("perf-defer-true")){
					onDeferVal = true;
				}
				var defer_js = '<div class="cmb-th tpgb-smart-loader-delay tpgb-remove-dynamic-style tpgb-load-defer"><label>Defer CSS & JS <span class="tpgb-tooltip-dynamic">!</span></label><div class="block-check-wrap"><input type="checkbox" class="block-list-checkbox" name="tpgb-smart-loader" id="tp-defer-js-opt" value="'+onDeferVal+'" '+(onDeferVal == true ? 'checked="checked"' : '')+' /><label for="tp-defer-js-opt"></label></div></div>';

				performace_cont.append('<div class="cmb-row tpgb-remove-plus-cache"><div class="cmb-th"><label for="plus_smart_performance">CSS & JS Delivery System</label></div><div class="tpgb-select-caching-type"><select id="tpgb-cache-opt-performance"><option value="combine" '+(selected=='combine' ? 'selected="selected"' : '')+'>Smart Optimized Mode</option><option value="separate" '+(selected=='separate' ? 'selected="selected"' : '')+'>On Demand Assets</option></select><div class="tpgb-perf-save-msg"></div></div><div class="cmb-td performance-combine"><p class="cmb2-metabox-description">On First Load, CSS delivery start from header in minified-combined format and One combined-minified JS in the footer. On Second load, All CSS will be combined-minified in just one single CSS file and loaded on header and JS will be delivered from footer same as First load. The optimized asset is stored at "/wp-content/uploads/theplus_gutenberg"</p><a href="#" id="'+ids+'" class="tpgb-purge-cache-btn">Purge All Assets</a><div class="smart-performace-desc-btn">*Above button will remove all Combined-minified files from "/wp-content/uploads/theplus_gutenberg" and It will regenerate on your page visit.</div></div><div class="cmb-td performance-separate"><p class="cmb2-metabox-description">Based on blocks used on web page, Individual CSS for each block will be loaded from header. All JS will be loaded in footer individually based on blocks used on that page.</p></div>'+delay_js+defer_js+'</div>');

				performace_cont.append('<div class="cmb-row tpgb-default-block-page"><div class="cmb-th"><label>Gutenberg Default Blocks Manager</label></div><div class="cmb-td"><p class="cmb2-metabox-description">You can enable/disable Blocks of Default Gutenberg aka Block Editor. It also having scan feature to auto find used blocks on website and disable rest blocks.</p><a href="'+window.location.pathname+'?page=tpgb_default_load_blocks" class="tpgb-block-manager-btn">Visit Block Manager</a><div class="smart-performace-desc-btn">Note : This is a beta feature. You may enable/disable any blocks as well as scan blocks to auto disable all at once. But, Make sure to have complete backup of site before using this.</div></div></div>');

				smart_action = "tpgb_all_perf_clear_cache";
				
				performace_cont.append('<div class="cmb-row tpgb-remove-dynamic-style"><div class="cmb-th"><label for="plus_smart_performance">Regenerate Assets <span class="tpgb-tooltip-dynamic">!</span></label><p class="cmb2-metabox-description">Note : If you find any discrepancy in frontend and backend design, then we would suggest you to click on above button to regenerate the assets dynamically again based on design changes.</p></div><div class="cmb-td"><a href="#" id="'+ids_dynamic+'" class="tpgb-smart-cache-btn">REGENERATE ALL ASSETS</a></div></div>');
				
				dynamic_action = "tpgb_all_dynamic_clear_style";
				var delayJs = document.getElementById('tp-delay-js-opt');
				var deferJs = document.getElementById('tp-defer-js-opt');
				var delayJsVal = false;
				if(delayJs.checked){
					delayJsVal = true;
				}
				var deferJsVal = false;
				if(deferJs.checked){
					deferJsVal = true;
				}
				tpgb_perf_opt_change(false, $('#tpgb-cache-opt-performance').val(), delayJsVal, deferJsVal);
				function tpgb_perf_opt_change(onajax=false, cacheVal, delayJsVal, deferJsVal){
					if(cacheVal=='combine'){
						$('.cmb-td.performance-combine').show(100);
						$('.cmb-td.performance-separate').hide(100);
					}else if(cacheVal=='separate'){
						$('.cmb-td.performance-combine').hide(100);
						$('.cmb-td.performance-separate').show(100);
					}
					var $this = $(".tpgb-perf-save-msg");
					if(onajax && cacheVal){
						$this.show(50);
						$.ajax({
							url: tpgb_admin.ajax_url,
							type: "post",
							data: {
								action: 'tpgb_performance_opt_cache',
								security: tpgb_admin.tpgb_nonce,
								perf_caching: cacheVal,
								delay_js: delayJsVal,
								defer_js: deferJsVal,
							},
							beforeSend: function() {
								 $this.html(
									'<svg id="plus-spinner" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48"><circle cx="24" cy="4" r="4" fill="#fff"/><circle cx="12.19" cy="7.86" r="3.7" fill="#fffbf2"/><circle cx="5.02" cy="17.68" r="3.4" fill="#fef7e4"/><circle cx="5.02" cy="30.32" r="3.1" fill="#fef3d7"/><circle cx="12.19" cy="40.14" r="2.8" fill="#feefc9"/><circle cx="24" cy="44" r="2.5" fill="#feebbc"/><circle cx="35.81" cy="40.14" r="2.2" fill="#fde7af"/><circle cx="42.98" cy="30.32" r="1.9" fill="#fde3a1"/><circle cx="42.98" cy="17.68" r="1.6" fill="#fddf94"/><circle cx="35.81" cy="7.86" r="1.3" fill="#fcdb86"/></svg>'
								);
							},
							success: function(response) {
								if(response && response.success){
									$this.addClass('success')
									$this.html("Saved..");
									setTimeout(function() {
										$this.hide(100);
										$this.removeClass('success')
									}, 2000);
								}else{
									$this.addClass('error')
									$this.html("Server Error..");
									setTimeout(function() {
										$this.hide(100);
										$this.removeClass('error')
									}, 2000);
								}
							},
							error: function() {
							}
						});
					}
				}
    			$("#tpgb-cache-opt-performance").on('change',function(){
					var delayJs = document.getElementById('tp-delay-js-opt');
					var delayVal = false;
					if(delayJs.checked){
						delayVal = true;
					}
					var deferJs = document.getElementById('tp-defer-js-opt');
					var deferVal = false;
					if(deferJs.checked){
						deferVal = true;
					}
					tpgb_perf_opt_change( true, $(this).val(), delayVal, deferVal );
				})
				
				if(delayJs){
					delayJs.addEventListener('change', (event) => {
						var chkValue = false;
						if (event.currentTarget.checked) {
							chkValue = true;
						}
						var deferJs = document.getElementById('tp-defer-js-opt');
						var deferVal = false;
						if(deferJs.checked){
							deferVal = true;
						}
						tpgb_perf_opt_change(true,$('#tpgb-cache-opt-performance').val(), chkValue, deferVal);
					})
				}
				if(deferJs){
					deferJs.addEventListener('change', (event) => {
						var chkValue = false;
						if (event.currentTarget.checked) {
							chkValue = true;
						}
						var delayJs = document.getElementById('tp-delay-js-opt');
						var delayVal = false;
						if(delayJs.checked){
							delayVal = true;
						}
						tpgb_perf_opt_change(true,$('#tpgb-cache-opt-performance').val(), delayVal, chkValue);
					})
				}
			}
			
			$(".tpgb-purge-cache-btn").on("click", function(e) {
				e.preventDefault();
				if(performace_cont.length > 0){
					var confirmation = confirm("Are you sure want to remove all cache files? It will remove all cached JS and CSS files from your server. It will generate automatically on your next visit of page.?");
				}
				if (confirmation) {
					var $this = $(this);
					$.ajax({
						url: tpgb_admin.ajax_url,
						type: "post",
						data: {
							action: smart_action,
							security: tpgb_admin.tpgb_nonce
						},
						beforeSend: function() {
							$this.html(
								'<svg id="plus-spinner" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48"><circle cx="24" cy="4" r="4" fill="#fff"/><circle cx="12.19" cy="7.86" r="3.7" fill="#fffbf2"/><circle cx="5.02" cy="17.68" r="3.4" fill="#fef7e4"/><circle cx="5.02" cy="30.32" r="3.1" fill="#fef3d7"/><circle cx="12.19" cy="40.14" r="2.8" fill="#feefc9"/><circle cx="24" cy="44" r="2.5" fill="#feebbc"/><circle cx="35.81" cy="40.14" r="2.2" fill="#fde7af"/><circle cx="42.98" cy="30.32" r="1.9" fill="#fde3a1"/><circle cx="42.98" cy="17.68" r="1.6" fill="#fddf94"/><circle cx="35.81" cy="7.86" r="1.3" fill="#fcdb86"/></svg><span style="margin-left: 5px;">Removing Purge...</span>'
							);
						},
						success: function(response) {
							if(performace_cont.length > 0){
								setTimeout(function() {
									$this.html("Purge All Cache");
								}, 100);
							}
						},
						error: function() {
						}
					});
				}
			});
			
			$("#"+ids_dynamic).on("click", function(e) {
				e.preventDefault();
				if(performace_cont.length > 0){
					var confirmation = confirm("Are you sure want to remove all cache files? It will remove all cached JS and CSS files from your server. It will generate automatically on your next visit of page.?");
				}
				if (confirmation) {
					var $this = $(this);
					$.ajax({
						url: tpgb_admin.ajax_url,
						type: "post",
						data: {
							action: dynamic_action,
							security: tpgb_admin.tpgb_nonce
						},
						beforeSend: function() {
							$this.html(
								'<svg id="plus-spinner" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48"><circle cx="24" cy="4" r="4" fill="#fff"/><circle cx="12.19" cy="7.86" r="3.7" fill="#fffbf2"/><circle cx="5.02" cy="17.68" r="3.4" fill="#fef7e4"/><circle cx="5.02" cy="30.32" r="3.1" fill="#fef3d7"/><circle cx="12.19" cy="40.14" r="2.8" fill="#feefc9"/><circle cx="24" cy="44" r="2.5" fill="#feebbc"/><circle cx="35.81" cy="40.14" r="2.2" fill="#fde7af"/><circle cx="42.98" cy="30.32" r="1.9" fill="#fde3a1"/><circle cx="42.98" cy="17.68" r="1.6" fill="#fddf94"/><circle cx="35.81" cy="7.86" r="1.3" fill="#fcdb86"/></svg><span style="margin-left: 5px;">Removing Assets...</span>'
							);
						},
						success: function(response) {
							if(performace_cont.length > 0){
								setTimeout(function() {
									$this.html("REGENERATE ALL ASSETS");
								}, 100);
							}
						},
						error: function() {
						}
					});
				}
			});
		}
		
		/*Welcome page FAQ*/
		$('.tpgb-welcome-faq .tpgb-faq-section .faq-title').on('click',function() {
			var $parent = $(this).closest('.tpgb-faq-section');
			var $btn = $parent.find('.faq-icon-toggle')
			$parent.find('.faq-content').slideToggle();
			$parent.toggleClass('faq-active');
		});
		/*Welcome page FAQ*/
		/*Plus block Listing*/
		$('#block_check_all').on('click', function() {
				$('.plus-block-list input:checkbox:enabled').prop('checked', $(this).prop('checked'));
			if(this.checked){
				$(this).closest(".panel-block-check-all").addClass("active-all");
			}else{
				$(this).closest(".panel-block-check-all").removeClass("active-all");
			}
		});
		$( ".panel-block-filters .blocks-filter" ).on('change',function () {
			var selected = $( this ).val();
			var block_filter = $(".plus-block-list .tpgb-panel-col");
			if(selected!='all'){
				block_filter.removeClass('is-animated')
					.fadeOut(5).promise().done(function() {
					  block_filter.filter(".block-"+selected)
						.addClass('is-animated').fadeIn();
					});
			}else if(selected=='all'){
				block_filter.removeClass('is-animated')
					.fadeOut(5).promise().done(function() {
						block_filter.addClass('is-animated').fadeIn();
					});
			}
		});
		
		var timeoutID = null;
		
		function tpgb_block_filter(search) {
			$.ajax({
				url: tpgb_admin.ajax_url,
				type: "post",
				data: {
					action: 'tpgb_block_search',
					filter: search,
					security: tpgb_admin.tpgb_nonce
				},
				beforeSend: function() {
					
				},
				success: function(response) {
					if(response!=''){
						$(".plus-block-list").empty();
						$(".plus-block-list").append(response);
					}
					$( ".panel-block-filters .blocks-filter" ).change();
				}
			});
		}
		$( ".tpgb-block-filters-search .block-search" ).on('keyup',function( e ) {
			clearTimeout(timeoutID);
			timeoutID = setTimeout(tpgb_block_filter.bind(undefined, e.target.value), 350);
			//var search = $(this).val();
		});
		/*Plus block Listing*/
		/* Rollback */
		if($('.tpgb-rollback-inner').length){
			$('.tpgb-rollback-inner').each(function(){
				var $this = $(this),
				rb_btn = $this.find('.tpgb-rollback-button'),
				data_btn_text = rb_btn.data('rv-text'),
				data_btn_url = rb_btn.data('rv-url'),
				rb_select = $this.find('.tpgb-rollback-list').val();
				if(rb_select){
					rb_btn.html(data_btn_text.replace('{TPGB_VERSION}', rb_select));
					rb_btn.attr('href', data_btn_url.replace('TPGB_VERSION', rb_select));
				}
				$this.find('.tpgb-rollback-list').on("change",function(){
					rb_btn.html(data_btn_text.replace('{TPGB_VERSION}', $(this).val()));
					rb_btn.attr('href', data_btn_url.replace('TPGB_VERSION', $(this).val()));
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
		/* Rollback */

		/** On-boarding Process Start */

		var boardPop = document.querySelector('.tpgb-boarding-pop');

		var closePop = (boardPop !== null) ? boardPop.querySelector('.tpgb-close-button') :  null ;
			if(closePop !== null){
				closePop.addEventListener("click", event => {
					event.preventDefault();
					boardPop.style.display = "none";
				});
			}
				
		if(boardPop !== null){
			var proceedBtn = boardPop.querySelector('.tpgb-boarding-proceed'),
			backBtn = boardPop.querySelector('.tpgb-boarding-back'),
			step1 = boardPop.querySelector(`[data-step="1"]`),
			step7 = boardPop.querySelector(`[data-step="7"]`),
			step8 = boardPop.querySelector(`[data-step="8"]`),
			step6 = boardPop.querySelector(`[data-step="6"]`),
			step5 = boardPop.querySelector(`[data-step="5"]`),
			pagination = boardPop.querySelector('.tpgb-pagination'),
			boardProcess = boardPop.querySelector('.tpgb-boarding-progress'),
			processWidth = 100/8;
			

			var webcompTypes = boardPop.querySelector('.tpgb-select-3');
			var selectWebcomp = webcompTypes.querySelectorAll('.select-box');
				selectWebcomp.forEach((self) => {
					self.addEventListener("click", event => {
						event.preventDefault();

						var allTypes = webcompTypes.querySelectorAll('.select-box');
							allTypes.forEach((self) => {
								if(self.classList.contains('active')){
									self.classList.remove('active');
								}
							});
						event.currentTarget.classList.add('active');
					});
				});

			var webTypes = boardPop.querySelector('.tpgb-select-8');
			var selectWeb = webTypes.querySelectorAll('.select-box');
			selectWeb.forEach((self) => {
				self.addEventListener("click", event => {
					event.preventDefault();

					var allTypes = webTypes.querySelectorAll('.select-box');
						allTypes.forEach((self) => {
							if(self.classList.contains('active')){
								self.classList.remove('active');
							}
						});
					event.currentTarget.classList.add('active');
				});
			});
			
			proceedBtn.addEventListener("click", event => {
				event.preventDefault();

				var activeSection = boardPop.querySelector('.tpgb-on-boarding.active'),
					getstepVal = activeSection.getAttribute('data-step');
				var nextstepVal = Number(getstepVal) + 1;
				if(nextstepVal <= 8){
					var nextSection = boardPop.querySelector(`[data-step="${nextstepVal}"]`);
					activeSection.classList.remove('active');
					nextSection.classList.add('active');

					if(!step1.classList.contains('active')){
						backBtn.classList.add('active');
					}

					// Copy Offer Code
					if(step7.classList.contains('active')){
						
						var copyClick = boardPop.querySelector('.code-img');

						copyClick.addEventListener("click", e => {
							e.preventDefault();
							let copytxtDiv = boardPop.querySelector('.offer-code').textContent;
							
							if(copytxtDiv){
								navigator.clipboard.writeText(copytxtDiv).then(() => {
									let copyIcon = boardPop.querySelector('.tpgb-copy-icon');
									jQuery(e.target).remove();
										copyIcon.style.display = 'inline-block';
									
								}).catch(() => {
									console.log("something went wrong");
								});
							}
							
						})
					}

					// Send Email
					if(step6.classList.contains('active')){
						tpgb_send_mail();
					}

					// Store Onboarding Data
					if(step8.classList.contains('active')){
						event.stopPropagation();
						proceedBtn.innerHTML = "Visit Dashboard";
						proceedBtn.classList.add('tpgb-onbor-last')

						var getdetBtn =  boardPop.querySelector('.tpgb-show-details');
						if(getdetBtn != null){
							getdetBtn.addEventListener("click", function(){
								var getdeDiv = this.parentNode.parentNode.querySelector('.tpgb-details');

								if(getdeDiv.classList.contains('show')){
									getdeDiv.classList.remove("show");
								}else{
									getdeDiv.classList.add("show");
								}
							})
						}

						if(nextstepVal === 8){
							tpgb_boarding_store(selectWebcomp , selectWeb , step8 , nextstepVal );
						}


					}
					
					// Install Nexter Theme
					if(step5.classList.contains('active')){
						tpgb_add_nexter(proceedBtn);
					}
					progessBar(nextstepVal);
				}
			});

			backBtn.addEventListener("click", event => {
				event.preventDefault();

				var activeSection = boardPop.querySelector('.tpgb-on-boarding.active'),
					getstepVal = activeSection.getAttribute('data-step');
				var nextstepVal = Number(getstepVal) - 1;
				var prevSection = boardPop.querySelector(`[data-step="${nextstepVal}"]`),
					getdetBtn =  boardPop.querySelector('.tpgb-show-details');
					activeSection.classList.remove('active');
					prevSection.classList.add('active');

				if(step1.classList.contains('active')){
					backBtn.classList.remove('active');
				}
				if(!step8.classList.contains('active')){
					proceedBtn.innerHTML = "Proceed";
				}
				if(proceedBtn.classList.contains('tpgb-onbor-last')){
					proceedBtn.onclick = ''; 
					proceedBtn.classList.remove('tpgb-onbor-last');
				}

				if(getdetBtn.parentNode.parentNode.querySelector('.tpgb-details').classList.contains('show')){
					getdetBtn.parentNode.parentNode.querySelector('.tpgb-details').classList.remove("show");
				}
				progessBar(nextstepVal);
			});

			function progessBar(nextstepVal){
				var progress = processWidth*nextstepVal;
				boardProcess.style.width = progress + '%';
				pagination.innerHTML = `${nextstepVal}/8`;
			}
			
			// Stey Update Email
			function tpgb_send_mail(){
				var tpgbSendEmail= document.querySelector('.submit-btn');
				tpgbSendEmail.addEventListener('click', event => {
					event.preventDefault();
					var tpgboName= document.querySelector('#tpgb-onb-name'),
						tpgboEmail= document.querySelector('#tpgb-onb-email'),
						errorDiv = document.querySelector('.input-note');

						if(tpgboName && tpgboName.value ==''){
							tpgb_on_validation( errorDiv , 'Name field is required.' )
						}else{
							if(tpgboEmail && tpgboEmail.value!=''){
								const validateEmail = (email) => {
									return email.match(
										/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
									);
									};
								if (validateEmail(tpgboEmail.value)) {
									const webhookBody = {
										full_name : tpgboName.value,
										email: tpgboEmail.value,
									};
									const welcomeEmailUrl = 'https://store.posimyth.com/?fluentcrm=1&route=contact&hash=30275c78-0cf5-42f1-adb0-32901bb25b90';
									fetch( welcomeEmailUrl, {
										method: 'POST',
										headers: {
											'Content-Type': 'application/json',
											'Access-Control-Allow-Origin' : 'http://localhost/',
										},
										mode: 'no-cors',
										body: JSON.stringify(webhookBody),
									}).then((response) => {
										console.log(response);
										if (response.ok) {
											tpgb_on_validation( errorDiv , 'Successfully send mail.' )
										}else{
											tpgb_on_validation( errorDiv , 'There was an error! Try again later!' )
										}
									});

								}else{;
									tpgb_on_validation( errorDiv , 'Invalid email. Double-check your entry.' )
								}
							}else{
								tpgb_on_validation( errorDiv , 'Please Enter a Valid Email Address.' )
							}
						}
				})
			}

			// Form Field Vaildation
			function tpgb_on_validation( selector ,msg ){
				selector.innerHTML = msg;
				jQuery(selector).slideDown()

				setTimeout(function(){
					jQuery(selector).slideUp()
				},5000);
			}

			// Store On Borading Data in DB
			function tpgb_boarding_store(select1 , select2 , stepno , current){
				if(current === 8){
					var onDonebtn = document.querySelector('.tpgb-onbor-last'),
					tpgb_ondata = document.getElementById('tpgb_ondata');

					if(onDonebtn != null ){
						onDonebtn.onclick = function(event) {
							event.preventDefault();
							var tpgbonData = { tpgb_web_com : '' , tpgb_web_Type : '' , tpgb_get_data : false , tpgb_onboarding : false };
							select1.forEach((obj) => {
								if(obj.classList.contains('active')){
									let webCom = obj.querySelector('.select-title')
									tpgbonData['tpgb_web_com'] = webCom.innerHTML;
								}
							})
							select2.forEach((obj) => {
								if(obj.classList.contains('active')){
									let webtype = obj.querySelector('.select-title')
									tpgbonData['tpgb_web_Type'] = webtype.innerHTML;
								}
							})

							if(tpgb_ondata){
								tpgbonData['tpgb_get_data'] = true;
							}

							if(tpgbonData){
								tpgbonData['tpgb_onboarding'] = true;	
								
								$.ajax({
									url: tpgb_admin.ajax_url,
									type: "post",
									data: {
										action: 'tpgb_boarding_store',
										boardingData : tpgbonData,
										security: tpgb_admin.tpgb_nonce
									},
									beforeSend: function() {
										
									},
									success: function(response) {
										if(response && response.onBoarding){
											document.querySelector('.tpgb-boarding-pop').style.display = "none";
										}
									}
								});
							}
						}
					}
				}
			}

			// Install & Active Nexter Theme
			function tpgb_add_nexter(btnscope){
				let addnxt = document.getElementById('in-nexter'),
					loder = document.querySelector('.tpgb-nxt-load'),
					notice = document.querySelector('.tpgb-wrong-msg-notice');

				addnxt.addEventListener( "change", function(){
					if(this.checked){
						btnscope.setAttribute('disabled' , true)

						$.ajax({
							url: tpgb_admin.ajax_url,
							type: "post",
							data: {
								action: 'tpgb_install_nexter',
								security: tpgb_admin.tpgb_nonce
							},
							beforeSend: function() {
								loder.style.display = 'flex';
							},
							success: function(response) {
								loder.style.display = 'none';

								if(response.nexter){
									setTimeout(function(){
										notice.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"  viewBox="0 0 512 512"><path fill="#27ae60" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>'+response.message
										notice.classList.add('active');
									},50);
								}else{
									setTimeout(function(){
										notice.innerHTML = '<svg width="20" height="20" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="16" cy="16" r="15.75" stroke="#FC4032" stroke-width="0.5"></circle><circle cx="16" cy="16" r="12" fill="#FC4032"></circle><rect x="15" y="9" width="2" height="10" rx="1" fill="white"></rect><rect x="15" y="20" width="2" height="2" rx="1" fill="white"></rect></svg>'+response.message
										notice.classList.add('active');
									},50);
								}
								setTimeout(function(){
									notice.remove();
								},3500);

								btnscope. removeAttribute('disabled')
							}
						});

					}else{
						btnscope. removeAttribute('disabled')
					}
				})
			}
		}
		/** On-boarding Process End */
})(window.jQuery);

/** On-boarding Process End */
var slidePage = 1;
showDivs(slidePage);

function plusPage(n){
	showDivs(slidePage += n);
}
function currentPage(n){
	showDivs(slidePage = n);
}
function showDivs(n){
	var i;
	var x = document.querySelectorAll(".tpgb-onboarding-details.slider");
	var sliderDots = document.querySelector('.slider-btns');
	var dots = (sliderDots != null) ? sliderDots.querySelectorAll(".slider-btn") : '';

	if(n > x.length){
		slidePage = 1;
	}
	if(n < 1){
		slidePage = x.length;
	}
	for(i=0; i<x.length; i++){
		x[i].style.display = "none";
	}
	for(i=0; i<dots.length; i++){
		dots[i].className = dots[i].className.replace(" active", "");
	}

	if(x[slidePage-1] != null){
		x[slidePage-1].style.display = "block";
	}
	if(dots[slidePage-1] != null){
		dots[slidePage-1].className += " active";
	}
}

/** On-boarding Process End */