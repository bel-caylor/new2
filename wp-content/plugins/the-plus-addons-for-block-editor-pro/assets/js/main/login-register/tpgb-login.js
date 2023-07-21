var mainDiv = document.querySelectorAll('.tpgb-login-register');

mainDiv.forEach(function(item){
    
    var regisForm = item.querySelector('.tpgb-register-form'),
        rebutton =  (regisForm !== null) ? regisForm.querySelector('.tpgb-register-button') : '',
        msgJson = JSON.parse(item.getAttribute('data-registermsgHtml')),
        loginForm = item.querySelector('.tpgb-login-form'),
        forgetpassForm = item.querySelector('.tpgb-lostpass-form'),
        socialBtn = item.querySelectorAll('.tpgn-socialbtn-wrap'),
        lostpassForm = item.querySelector('.tpgb-rp-form'),
        magicForm = item.querySelector('.tpgb-magic-form');

    //Login & Register Tab Js
    if(item.querySelector('.tpgb-form-tabbtn') !== null){
        var ul = item.querySelector('.tpgb-form-tabbtn'),
            formTab = ul.querySelectorAll('.tpgb-ftab-btn');

            formTab.forEach(function(tab){
                tab.addEventListener("click", function(e) {
                    var Tab = jQuery(this).data('tab');
                    
                    jQuery(ul).find(".tpgb-ftab-btn").removeClass('active').addClass('inactive');
                    jQuery(this).addClass('active').removeClass('inactive');
                    jQuery(item).find('.tpgb-logintab-content').removeClass('active').addClass('inactive');
                    jQuery(this).closest('.tpgb-form-tab-wrap').find('.tpgb-logintab-content[data-tab="'+Tab+'"]').removeClass('inactive').addClass('active');
                })
            })
    }

    // Js For Button Click
    var btn = item.querySelector('.tpgb-show-button'),
        uperDiv = item.querySelector(".tpgb-formbtn-hover"),
        hoverdiv = (uperDiv !== null) ? uperDiv.querySelector('.tpgb-form-wrap') : '';
    
    if(btn !== null){
        if(btn.classList.contains('tpgb-form-click') || btn.classList.contains('tpgb-form-popup') ){
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                
                if(this.classList.contains('tpgb-form-click')){
                    jQuery(this).next(".tpgb-buform-layput").find('.tpgb-form-wrap').slideToggle();
                }else{
                    jQuery(this).next(".tpgb-model-wrap").addClass('model-open');
                }
            })
        }else{
            if(uperDiv !== null){
                jQuery(hoverdiv).hide("slow");
                btn.addEventListener("mouseover", function(e) {
                    jQuery(this).next(".tpgb-buform-layput").find('.tpgb-form-wrap').show("slow");
                })
                hoverdiv.addEventListener("mouseout", function(e) {
                    if (jQuery(hoverdiv).is(':hover')) {
                        jQuery(uperDiv).find('.tpgb-form-wrap').show("slow");
                    } else {
                        jQuery(uperDiv).find('.tpgb-form-wrap').hide("slow");
                    }
                })
            }
        }
    }

    //JS For Model close

    if(item.querySelector('.tpgb-model-close') !== null){
        var close = item.querySelector('.tpgb-model-close');

        close.addEventListener('click' , function(e) {
            if(jQuery(this).closest('.tpgb-model-wrap').hasClass('model-open')){
                jQuery(this).closest('.tpgb-model-wrap').removeClass('model-open')
            }
        })
    }

    //JS For Password toggle
    if(item.querySelector('.tpgb-password-show') !== null){
        var toggle = item.querySelector('.tpgb-password-show');

        toggle.addEventListener("click", function(e) {
            var clickDiv = e.currentTarget;
                id = clickDiv.getAttribute ('data-id'),
                sIcon = clickDiv.getAttribute ('data-sicon'),
                hIcon = clickDiv.getAttribute('data-hicon'),
                field = clickDiv.parentNode.querySelector('input#'+id);
                
            if (field.getAttribute("type") == "password") {
                field.setAttribute("type", "text");
                clickDiv.innerHTML = '<i class="'+hIcon+'"></i>' ;
            } else {
                field.setAttribute("type", "password");
                clickDiv.innerHTML = '<i class="'+sIcon+'"></i>';
            }
        })
    }

    //JS For Password Hint toggle

    if(item.querySelector('.tpgb-passHint') !== null){
        var hint = item.querySelector('.tpgb-passHint');
        hint.addEventListener('click' , function(e) {
            var par = e.currentTarget.parentNode.parentNode;
            jQuery(par).find('.tpgb-pass-indicator').slideToggle(400)
        })
    }

    

    if(item.querySelector('.tpgb-form-password') !== null){ 

        jQuery('.tpgb-form-password.focus').on("focus keyup", function (e) {
            var par = e.currentTarget.parentNode.parentNode;
            if(jQuery(par).find('.tpgb-pass-indicator').hasClass('inline')){
                jQuery(par).find('.tpgb-pass-indicator').css("display", "flex");
            }else{
                jQuery(par).find('.tpgb-pass-indicator').fadeIn(400);
            }
            
        });
        jQuery('.tpgb-form-password').focusout(function(e){
            var par = e.currentTarget.parentNode.parentNode;
            jQuery(par).find('.tpgb-pass-indicator').css("display", "none");
        });

        var passHint = item.querySelector('.tpgb-form-password'),
            cheSvg = '<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="check-circle" class="svg-inline--fa fa-check-circle fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 8C119.033 8 8 119.033 8 256s111.033 248 248 248 248-111.033 248-248S392.967 8 256 8zm0 48c110.532 0 200 89.451 200 200 0 110.532-89.451 200-200 200-110.532 0-200-89.451-200-200 0-110.532 89.451-200 200-200m140.204 130.267l-22.536-22.718c-4.667-4.705-12.265-4.736-16.97-.068L215.346 303.697l-59.792-60.277c-4.667-4.705-12.265-4.736-16.97-.069l-22.719 22.536c-4.705 4.667-4.736 12.265-.068 16.971l90.781 91.516c4.667 4.705 12.265 4.736 16.97.068l172.589-171.204c4.704-4.668 4.734-12.266.067-16.971z"></path></svg>';
        jQuery(passHint).on("focus keyup", function (e) {
            var password = jQuery(this).val(),
				cfindi = jQuery(this).closest(".tpgb-field-group").find(".tpgb-pass-indicator"),
				cfclicki = jQuery(this).closest(".tpgb-field-group").find(".tpgb-passHint"),
                strength = 0;

            if(cfindi.hasClass('pattern-1') || cfindi.hasClass('pattern-4') || cfindi.hasClass('pattern-5') ){
                if (password.length > 7) {
                    cfindi.find(".tp-min-eight-character").addClass("tp-pass-success-ind");
                    cfindi.find(".tp-min-eight-character.tp-pass-success-ind").html(cheSvg)				
                    strength++;
                } else {
                    cfindi.find(".tp-min-eight-character").removeClass("tp-pass-success-ind");
                    cfindi.find(".tp-min-eight-character.tp-pass-success-ind").html(cheSvg);
                }
            }
            if(cfindi.hasClass('pattern-1') || cfindi.hasClass('pattern-2') || cfindi.hasClass('pattern-3') ){
                if (password.match(/([0-9])/)) {
                    cfindi.find(".tp-one-number").addClass("tp-pass-success-ind");
                    cfindi.find(".tp-one-number.tp-pass-success-ind").html(cheSvg);					
                    strength++;
                } else {
                    cfindi.find(".tp-one-number").removeClass("tp-pass-success-ind");
                    cfindi.find(".tp-one-number.tp-pass-success-ind").html(cheSvg);						
                }
            }
            if(cfindi.hasClass('pattern-1') || cfindi.hasClass('pattern-3')){
                if (password.match(/([a-zA-Z])/)) {
                    cfindi.find(".tp-low-lat-case").addClass("tp-pass-success-ind");
                    cfindi.find(".tp-low-lat-case.tp-pass-success-ind").html(cheSvg);
                    strength++;
                } else {
                    cfindi.find(".tp-low-lat-case").removeClass("tp-pass-success-ind");
                    cfindi.find(".tp-low-lat-case.tp-pass-success-ind").html(cheSvg);
                }
            }
            if(cfindi.hasClass('pattern-1')){
                if (password.match(/([!,@,#,$,%,^,&,*,?,_,~,-,(,)])/)) {
                    cfindi.find(".tp-one-special-char").addClass("tp-pass-success-ind");
                    cfindi.find(".tp-one-special-char.tp-pass-success-ind").html(cheSvg);
                    strength++;
                } else {
                    cfindi.find(".tp-one-special-char").removeClass("tp-pass-success-ind");
                    cfindi.find(".tp-one-special-char.tp-pass-success-ind").html(cheSvg);
                }
            }
            if(cfindi.hasClass('pattern-2')){
                //min 4 and max 8 character
                if (password.length > 3 && password.length < 9) {
                    cfindi.find(".tp-four-eight-character").addClass("tp-pass-success-ind");
                    cfindi.find(".tp-four-eight-character.tp-pass-success-ind").html(cheSvg);
                    strength++;
                } else {
                    cfindi.find(".tp-four-eight-character").removeClass("tp-pass-success-ind");
                    cfindi.find(".tp-four-eight-character.tp-pass-success-ind").html(cheSvg);
                }
            }
            if(cfindi.hasClass('pattern-3')){
                //min 6 character
                if (password.length > 5) {
                    cfindi.find(".tp-min-six-character").addClass("tp-pass-success-ind");
                    cfindi.find(".tp-min-six-character.tp-pass-success-ind").html(cheSvg);
                    strength++;
                } else {
                    cfindi.find(".tp-min-six-character").removeClass("tp-pass-success-ind");
                    cfindi.find(".tp-min-six-character.tp-pass-success-ind").html(cheSvg);
                }
            }
            if(cfindi.hasClass('pattern-4') || cfindi.hasClass('pattern-5')){
                //lower and uppercase				
                if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) {				
                    cfindi.find(".tp-low-upper-case").addClass("tp-pass-success-ind");
                    cfindi.find(".tp-low-upper-case.tp-pass-success-ind").html(cheSvg);
                    strength++;
                } else {
                    cfindi.find(".tp-low-upper-case").removeClass("tp-pass-success-ind");
                    cfindi.find(".tp-low-upper-case.tp-pass-success-ind").html(cheSvg);
                }
            }
            if(cfindi.hasClass('pattern-4')){
                if (password.match(/([a-zA-Z])/) || password.match(/([0-9])/)) {
                    cfindi.find(".tp-digit-alpha").addClass("tp-pass-success-ind");
                    cfindi.find(".tp-digit-alpha.tp-pass-success-ind").html(cheSvg);
                    strength++;
                } else {
                    cfindi.find(".tp-digit-alpha").removeClass("tp-pass-success-ind");
                    cfindi.find(".tp-digit-alpha.tp-pass-success-ind").html(cheSvg);
                }
            }
            if(cfindi.hasClass('pattern-5')){
                //special character
                if (password.match(/([!,@,#,$,%,^,&,*,?,_,~,-,(,)])/) || password.match(/([0-9])/)) {
                    cfindi.find(".tp-number-special").addClass("tp-pass-success-ind");
                    cfindi.find(".tp-number-special.tp-pass-success-ind").html(cheSvg);
                    strength++;
                } else {
                    cfindi.find(".tp-number-special").removeClass("tp-pass-success-ind");
                    cfindi.find(".tp-number-special.tp-pass-success-ind").html(cheSvg);
                }
            }

            if((cfindi.hasClass('pattern-1') && strength ==4) || (cfindi.hasClass('pattern-2') && strength ==2) || (cfindi.hasClass('pattern-3') && strength ==3 || cfindi.hasClass('pattern-4') && strength ==3 || cfindi.hasClass('pattern-5') && strength ==3)){	
                cfclicki.addClass('tp-done');
                jQuery(this).closest(".tpgb-login-register").find(".tpgb-pass-indicator").addClass('tp-done');
                setTimeout(function(){ cfclicki.fadeOut(400); }, 1000);
                jQuery(this).closest(".tpgb-login-register").find("button.tpgb-button").removeAttr( "disabled" );
            }else{
                cfclicki.removeClass('tp-done');
                jQuery(this).closest(".tpgb-login-register ").find(".tpgb-pass-indicator").removeClass('tp-done');
                setTimeout(function(){ cfclicki.fadeIn(400); }, 1000);
                jQuery(this).closest(".tpgb-login-register").find("button.tpgb-button").attr("disabled", true);
            }
            
            if (password == false) {
                jQuery(this).closest(".tpgb-login-register").find("button.tpgb-button").attr("disabled", true);
            }

        })
    }

    // trigger the wdmChkPwdStrength
    jQuery( 'body' ).on( 'keyup', '.tpgb-register-form #repassword , .tpgb-register-form #confirm-password ', function( event ) {
        wdmChkPwdStrength( jQuery('#repassword'), jQuery('#confirm-password'), jQuery('#password-strength'), jQuery('.tpgb-register-form input[type=submit]') , ['admin', 'happy', 'hello', '1234'] );
    })
    
    if(regisForm !== null){
        //validate Error Msg
        let validate = true;
        if(regisForm.querySelector('#firstname') !== null){
            var fNameField = regisForm.querySelector('#firstname');
            keyUpValidate(fNameField,'firstname',validate);
        }
        if(regisForm.querySelector('#lastname') !== null){
            var lNameField = regisForm.querySelector('#lastname');
            keyUpValidate(lNameField,'lastname',validate);
        }
        if(regisForm.querySelector('#email') !== null){
            var emailField = regisForm.querySelector('#email');
            keyUpValidate(emailField,'email',validate);
        }
        if(regisForm.querySelector('#username') !== null){
            var uNameField = regisForm.querySelector('#username');
            keyUpValidate(uNameField,'username',validate);
        }
        if(regisForm.querySelector('#repassword') !== null){
            var pNameField = regisForm.querySelector('#repassword');
            keyUpValidate(pNameField,'repassword',validate);
        }
        if(regisForm.querySelector('#mobileno') !== null){
            var pNameField = regisForm.querySelector('#mobileno');
            keyUpValidate(pNameField,'mobileno',validate);
        }

        let cloSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M175 175C184.4 165.7 199.6 165.7 208.1 175L255.1 222.1L303 175C312.4 165.7 327.6 165.7 336.1 175C346.3 184.4 346.3 199.6 336.1 208.1L289.9 255.1L336.1 303C346.3 312.4 346.3 327.6 336.1 336.1C327.6 346.3 312.4 346.3 303 336.1L255.1 289.9L208.1 336.1C199.6 346.3 184.4 346.3 175 336.1C165.7 327.6 165.7 312.4 175 303L222.1 255.1L175 208.1C165.7 199.6 165.7 184.4 175 175V175zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"/></svg>';

        let data = {
            'regiAction': (msgJson.regaction) ? msgJson.regaction : '',
            'emailData': (msgJson.emailData) ? msgJson.emailData : '',
            'mailChmpData': (msgJson.mailChimpData) ? msgJson.mailChimpData : '',
            'recaptchEn': msgJson.recaptchEn
        };
        // Register Login ajax
        if(rebutton !== null){
            regisForm.addEventListener('submit' , function(e){
                e.preventDefault();
                e.stopPropagation();

                const formData = new FormData(this);
                jQuery.each(data, function(key, value) {
                    if(key == 'emailData' || key == 'mailChmpData'){
                        jQuery.each(value, function(type, val) {
                            formData.append(type, val);
                        })
                    }else{
                        formData.append(key, value);
                    }
                });

                //var File = document.querySelectorAll( 'input[type="file"]' );
                // if(File){
                //     File.forEach(function(obj){
                //         // console.log(obj.files[0])
                //         formData.append( obj.name, obj.files[0]);
                //     })
                // }
               
                var fName = jQuery(regisForm).find('#firstname').val(),
                    lName = jQuery(regisForm).find('#lastname').val(),
                    email = jQuery(regisForm).find('#email').val(),
                    uName = jQuery(regisForm).find('#username').val(),
                    password = jQuery(regisForm).find('#repassword').val(),
                    copassword = jQuery(regisForm).find('#confirm-password').val();
                if(fName == '' && fName !== undefined){
                    if(jQuery('#firstname').hasClass('tpgb-error-load')){
                        jQuery('#firstname').removeClass( "tpgb-error-load" );
                    }
                    jQuery('#firstname').after('<span class="tpgb-error-field">'+jQuery('#firstname').data('error')+'</span>');
                }

                if(lName == '' && lName !== undefined){
                    if(jQuery('#lastname').hasClass('tpgb-error-load')){
                        jQuery('#lastname').removeClass( "tpgb-error-load" );
                    }
                    jQuery('#lastname').after('<span class="tpgb-error-field">'+jQuery('#lastname').data('error')+'</span>');
                }

                if(email == '' && email !== undefined){
                    if(jQuery('#email').hasClass('tpgb-error-load')){
                        jQuery('#email').removeClass( "tpgb-error-load" );
                    }
                    jQuery('#email').after('<span class="tpgb-error-field">'+jQuery('#email').data('error')+'</span>');
                }

                if(uName == '' && uName !== undefined){
                    if(jQuery('#username').hasClass('tpgb-error-load')){
                        jQuery('#username').removeClass( "tpgb-error-load" );
                    }
                    jQuery('#username').after('<span class="tpgb-error-field">'+jQuery('#username').data('error')+'</span>');
                }

                if(password == '' && password !== undefined){
                    if(jQuery('#repassword').hasClass('tpgb-error-load')){
                        jQuery('#repassword').removeClass( "tpgb-error-load" );
                    }
                    jQuery('#repassword').after('<span class="tpgb-error-field">'+jQuery('#repassword').data('error')+'</span>');
                }

                jQuery.ajax({
                    type: 'POST',
                    dataType: 'json',
                    enctype: 'multipart/form-data',
                    url:tpgb_load.ajaxUrl,
                    processData: false,
                    contentType: false,
                    data : formData,
                    beforeSend: function(){
                        jQuery(regisForm).find('.tpgb-regis-noti').addClass("active");
                        jQuery(regisForm).find(".tpgb-regis-noti .tpgb-re-response").html(msgJson.regloadText);
                    },
                    success: function(data) {
                       
                        let response = data.data;
                       
                        if(response){
                            if( response.nonceCheck && response.nonceCheck.registered == false ){
                                jQuery(regisForm).find('.tpgb-regis-noti').addClass("active");
                                jQuery(regisForm).find(".tpgb-regis-noti .tpgb-re-response").html(cloSvg+response.nonceCheck.message);
                            } 
                            if(response.checkRegister && response.checkRegister.registered == false){
                                jQuery(regisForm).find('.tpgb-regis-noti').addClass("active");
                                jQuery(regisForm).find(".tpgb-regis-noti .tpgb-re-response").html(cloSvg+response.checkRegister.message);
                            }
                            if(response.recaptcha && response.recaptcha.registered == false){
                                jQuery(regisForm).find('.tpgb-regis-noti').addClass("active");
                                jQuery(regisForm).find(".tpgb-regis-noti .tpgb-re-response").html(cloSvg+response.nonceCheck.message);
                            }
                            
                            if(response.userRegi && response.userRegi.registered == false){
                                jQuery(regisForm).find('.tpgb-regis-noti').addClass("active");
                                jQuery(regisForm).find(".tpgb-regis-noti .tpgb-re-response").html(cloSvg+response.userRegi.message);
                            }else if( response.fieldmissing && response.fieldmissing.registered == false ){
                                jQuery(regisForm).find('.tpgb-regis-noti').addClass("active");
                                jQuery(regisForm).find(".tpgb-regis-noti .tpgb-re-response").html(cloSvg+response.fieldmissing.message);
                            }else if(response.userRegi && response.userRegi.registered == true){
                                jQuery(regisForm).find('.tpgb-regis-noti').addClass("active");
                                jQuery(regisForm).find(".tpgb-regis-noti .tpgb-re-response").html(msgJson.succMsg);
                            }

                            if(response && response.regiAction ){

                                response.regiAction.map( function(obj){
                                    if(obj.action == 'sendemail' || obj.action == 'autologin'){
                                        
                                        if(obj.registered == true){
                                            jQuery(regisForm).find('.tpgb-regis-noti').addClass("active");
                                            jQuery(regisForm).find(".tpgb-regis-noti .tpgb-re-response").html(obj.message);
                                        }else{
                                            location.reload(true);
                                        }
                                    }
    
                                    if(obj.action == 'redirect'){
                                        if(obj.registered == true){
                                            if( msgJson.regaction && msgJson.regaction.includes('redirect') && msgJson.regredireUrl && msgJson.regredireUrl!= undefined){
                                                document.location.href = msgJson.regredireUrl;
                                            }else{
                                                location.reload(true);
                                            }
                                        }
                                    }
                                })
                            }
                            if(response.mailscb){
                                jQuery(regisForm).find('.tpgb-regis-noti').addClass("active");
                                jQuery(regisForm).find(".tpgb-regis-noti .tpgb-re-response").html('Mail Subscribe Successfully');
                            }

                            if(response.register && response.register.registered == true){
                                jQuery(regisForm).find('.tpgb-regis-noti').addClass("active");
                                jQuery(regisForm).find(".tpgb-regis-noti .tpgb-re-response").html(response.redirect.message);
                            }
                        }
                    },
                    complete: function(){
                        setTimeout(function(){
                            jQuery(regisForm).find('.tpgb-regis-noti').removeClass("active");	
                        }, 3000);
                    }
                });
            })
        }

        //recaptcha 
        if( msgJson.recaptchaKey && msgJson.recaptchaKey !== undefined ){
            recapDiv = regisForm.querySelector('.tpgb-recaptch-key');
            tpgb_reCaptcha(msgJson,recapDiv)
        }
    }

    function tpgb_reCaptcha(reData,recapDiv){
        window.tpgb_onLoadReCaptcha = function() {
            var clientId = grecaptcha.render('tpgb-inline-badge-'+reData.blockId+'', {
                'sitekey': reData.recaptchaKey ,
                'badge': reData.recaptchaPos,
                'size': 'invisible'
            });
            grecaptcha.ready(function() {
                grecaptcha.execute(clientId, {
                action: 'register'
                })
                .then(function(token) {
                    recapDiv.innerHTML='<input type="hidden" name="g-recaptcha-response" class="g-recaptcha-response-'+reData.blockId+'" value="' + token + '">';
                });
            });
        }
    }

    if(loginForm !== null){
        var loginBtn = loginForm.querySelector('.tpgb-login-button'),
            lostpasDiv = loginForm.querySelector('.tpgb-lostpass-relink'),
            magicDiv = loginForm.querySelector('.tpgb-magic-active'),
            lomsgData = JSON.parse(item.getAttribute('data-loginmsgHtml')),
            forgetPaa = item.querySelector('.tpgb-login-lost');
           
            
        
        if(lostpasDiv !== null){
            var losBtn = lostpasDiv.querySelector('.tpgb-lost-password'),
                fobackbtn = (forgetPaa !== null ) ? forgetPaa.querySelector('.tpgb-lpu-back') : '';
            losBtn.addEventListener('click' , function(e){
                jQuery(forgetPaa).toggle();
            })
            
            if(fobackbtn !== null){
                fobackbtn.addEventListener('click' , function(e){
                    jQuery(forgetPaa).hide();
                })
            }
        }
        if(magicDiv !== null){
            var magicBtn = magicDiv.querySelector('.tpgb-magic-tag'),
                mbackbtn = (magicForm !== null ) ? magicForm.querySelector('.tpgb-lpu-back') : '';
            magicBtn.addEventListener('click' , function(e){
                jQuery(magicForm).toggle();
            })

            if(mbackbtn !== null){
                mbackbtn.addEventListener('click' , function(e){
                    jQuery(magicForm).hide();
                })
            }
        }
        
        if(loginBtn !== null){
            loginForm.addEventListener('submit' , function(ev){
                ev.preventDefault();
                ev.stopPropagation();

                jQuery.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url:tpgb_load.ajaxUrl,
                    data: { 
                        'action': 'tpgb_login_user',
                        'loformData' : jQuery(loginForm).serialize(),
                    },
                    beforeSend: function(){
                        jQuery(loginForm).find('.tpgb-regis-noti').addClass("active");
                        jQuery(loginForm).find(".tpgb-regis-noti .tpgb-re-response").html(lomsgData.loglodTxt);
                    },
                    success: function(data) {
                        if (data.loggedin == true){
                            jQuery(loginForm).find('.tpgb-regis-noti').addClass("active");
                            jQuery(loginForm).find(".tpgb-regis-noti .tpgb-re-response").html(lomsgData.losuTxt);
                            if( lomsgData.logdireUrl && lomsgData.logdireUrl!= undefined){
                                document.location.href = lomsgData.logdireUrl;
                            }else{
                                location.reload(true);
                            }
                        }else if(data.loggedin == false){								
                            jQuery(loginForm).find('.tpgb-regis-noti').addClass("active");
                            jQuery(loginForm).find(".tpgb-regis-noti .tpgb-re-response").html(data.message);
                        }
                    },
                    // error: function(){
                    //     jQuery(loginForm).find('.tpgb-regis-noti').addClass("active");
                    //     jQuery(loginForm).find(".tpgb-regis-noti .tpgb-re-response").html(lomsgData.loeroTxt);
                    // },
                    complete: function(){
                        setTimeout(function(){
                            jQuery(loginForm).find('.tpgb-regis-noti').removeClass("active");	
                        }, 1500);
                    }
                })
            })
        }
    }

    if(forgetpassForm !== null){
        let fpassBtn = forgetpassForm.querySelector('.tpgb-forgetpassword-button'),
        losmgJson = JSON.parse(item.getAttribute('data-lostPass'));
        forgetpassForm.addEventListener('submit' , function(e){
            jQuery.ajax({
                type: 'POST',
                dataType: 'json',
                url:tpgb_load.ajaxUrl,
                data: { 
                    'action': 'tpgb_ajax_forgot_password',
                    'lostpassData' : jQuery(forgetpassForm).serialize(),
                    'tpgbforgotdata' :  losmgJson.cumData
                },
                beforeSend: function(){							
                    jQuery(forgetpassForm).find('.tpgb-regis-noti').addClass("active");
                    jQuery(forgetpassForm).find(".tpgb-regis-noti .tpgb-re-response").html(losmgJson.msgHtml.lostlodTxt);
                },
                success: function(data) {

                    if(data.lost_pass == 'confirm'){
                        jQuery(forgetpassForm).find('.tpgb-regis-noti').addClass("active");
                        jQuery(forgetpassForm).find(".tpgb-regis-noti .tpgb-re-response").html(losmgJson.msgHtml.lostuTxt);
                    }else if(data.lost_pass == 'something_wrong'){
                        jQuery(forgetpassForm).find('.tpgb-regis-noti').addClass("active");
                        jQuery(forgetpassForm).find(".tpgb-regis-noti .tpgb-re-response").html(losmgJson.msgHtml.losteroTxt);
                    }else if(data.lost_pass == 'could_not_sent'){
                        jQuery(forgetpassForm).find('.tpgb-regis-noti').addClass("active");
                        jQuery(forgetpassForm).find(".tpgb-regis-noti .tpgb-re-response").html(data.message);
                    }
                },
                complete: function(){
                    setTimeout(function(){
                        jQuery(forgetpassForm).find('.tpgb-regis-noti').removeClass("active");	
                    }, 1500);
                }
            });
            e.preventDefault();
        })

         //recaptcha 
         if( losmgJson.recaptchaKey && losmgJson.recaptchaKey !== undefined ){
            recapDiv = forgetpassForm.querySelector('.tpgb-lorecaptch-key');
            tpgb_reCaptcha(losmgJson,recapDiv)
        }
    }

    if(lostpassForm !== null){
        let lostBtn = lostpassForm.querySelector('.tpgb-resetpassword-button'),
            lofData = JSON.parse(lostpassForm.getAttribute('data-lostpassdata'));
        if(lostBtn !== null){
            lostpassForm.addEventListener('submit' , function(e){
                jQuery.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url:tpgb_load.ajaxUrl,
                    data: {
                        'action': 'tpgb_ajax_reset_password',
                        'tpgbresetdata': lofData.resetpdata,
                        'lostformData' : jQuery(lostpassForm).serialize(),
                    },
                    beforeSend: function(){
                        jQuery(lostpassForm).find('.tpgb-regis-noti').addClass("active");
                        jQuery(lostpassForm).find(".tpgb-regis-noti .tpgb-re-response").html('Please Wait...');
                    },
                    success: function(data) {
                        jQuery(lostpassForm).find('.tpgb-regis-noti').addClass("active");
                        jQuery(lostpassForm).find(".tpgb-regis-noti .tpgb-re-response").html( lofData.resetHtml.loadingTxt + data.message);;
                        if(data.reset_pass=='success'){
                            if( lofData.resetHtml.redirUrl){
                                window.location = lofData.resetHtml.redirUrl;
                            }
                        }
                        if(data.reset_pass=='empty'){
                            jQuery(lostpassForm).find('#repassword').value='';
                            jQuery(lostpassForm).find('#reenpassword').value='';
                        }
                        if(data.reset_pass=='mismatch'){
                            if(jQuery(lostpassForm).find('#reenpassword')[0].value !== undefined){
                                jQuery(lostpassForm).find('#reenpassword')[0].value='';
                            }
                            
                        }
                        if(data.reset_pass=='expire'){
                            if( lofData.resetHtml.redirUrl){
                                window.location = lofData.resetHtml.redirUrl;
                            }
                        }
                        if(data.reset_pass=='invalid'){
                            if( lofData.resetHtml.redirUrl){
                                window.location = lofData.resetHtml.redirUrl;
                            }
                        }
                    },
                    complete: function(){
                        setTimeout(function(){
                            jQuery(lostpassForm).find('.tpgb-regis-noti').removeClass("active");	
                        }, 1500);
                    }
                });
                e.preventDefault();
            })
        }

        if( lofData.recaptchaKey && lofData.recaptchaKey !== undefined ){
            recapDiv = lostpassForm.querySelector('.tpgb-resrecaptch-key');
            tpgb_reCaptcha(lofData,recapDiv)
        }
    }
    
    if(socialBtn !== null){
        socialBtn.forEach(function(sbtn){
            let fbBtn = sbtn.querySelector('.tpgb-btn-fb'),
                socialData = JSON.parse(sbtn.getAttribute('data-socialIds')),
                goBtn = sbtn.querySelector('.tpgb-btn-goo-'+socialData.uniId);
                
            if(fbBtn !== null){
                (function(d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) return;
                    js = d.createElement(s); js.id = id;
                    js.src = "https://connect.facebook.net/en_US/sdk.js";
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk')); 
                
                window.fbAsyncInit = function() {
                    FB.init({
                        appId      : socialData.faceAppid ,
                        cookie     : true,
                        xfbml      : true,
                        version    : 'v7.0'
                    });
                }; 
                                        

                fbBtn.addEventListener('click' , function(){
                    FB.login(function(e){
                        e.authResponse && statusChangeCallback(e,socialData.formType,socialData);
                    }, {
                        scope: "email"
                    });
                });
            }
            if(goBtn !== null){
                (function(d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) return;
                    js = d.createElement(s); js.id = id;
                    js.src = "https://accounts.google.com/gsi/client";
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'google-js'));
				
				let attrBtn = {}

                if(socialData.gbtnType == 'standard'){
                    attrBtn = Object.assign( attrBtn, { type : socialData.gbtnType , theme: socialData.goolthem , size: ( socialData.gobtnSize == 'cutm' ? 'large' : socialData.gobtnSize ) , text : socialData.gobtnTxt , shape : socialData.gostandshape , width : socialData.gobctWidth } )
                }else{
                    attrBtn = Object.assign( attrBtn, { type : socialData.gbtnType , theme: socialData.goolthem , size: socialData.goioSize , shape : socialData.goioshape } )
                }
				
                window.onload = function () {
                    google.accounts.id.initialize({
                        client_id: socialData.googlId ,
                        callback: function(response){
                            tpgb_googleLoginEndpoint(response , socialData.googlId )
                        }
                    });
                    google.accounts.id.renderButton( goBtn, attrBtn );
                }
            }

            if(socialData.googlepic == 'yes'){
				
				
                (function(d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) return;
                    js = d.createElement(s); js.id = id;
                    js.src = "https://accounts.google.com/gsi/client";
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'google-pic-js'));
				
               sbtn.innerHTML = '<div id="g_id_onload" data-client_id="'+socialData.googlId+'" data-context="signin"  data-callback="tpgb_googleLoginEndpoint" data-nonce="'+socialData.nonce+'"> </div>';
            }
        })
    }

    if(magicForm !== null){
        var magicBtn = magicForm.querySelector('.forgetpassword')
        losmgJson = JSON.parse(item.getAttribute('data-lostPass')),
        magicData = JSON.parse(magicForm.getAttribute('data-magicdata'));

        magicForm.addEventListener('submit' , function(e){
            e.preventDefault();
            
            jQuery.ajax({
                type: 'POST',
                dataType: 'json',
                url:tpgb_load.ajaxUrl,
                data: {
                    'action': 'tpgb_send_magic_link',
                    'magicData' : jQuery(magicForm).serialize(),
                    'mailData' : magicData,
                },
                beforeSend: function(){							
                    jQuery(magicForm).find('.tpgb-regis-noti').addClass("active");
                    jQuery(magicForm).find(".tpgb-regis-noti .tpgb-re-response").html(losmgJson.msgHtml.lostlodTxt);
                },
                success: function(data) {
                    if(data.magicMsg){
                        location.reload(true);
                    }
                    jQuery(magicForm).find('.tpgb-regis-noti').addClass("active");
                    jQuery(magicForm).find(".tpgb-regis-noti .tpgb-re-response").html(data.message);
                },
                complete: function(){
                    setTimeout(function(){
                        jQuery(magicForm).find('.tpgb-regis-noti').removeClass("active");	
                    }, 1500);
                }
            });

        })
    }

});

// Password Strength Meter
function wdmChkPwdStrength(pwd, confirmPwd, strengthStatus, submitBtn, blacklistedWords){
    var pwd = pwd.val(),
        confirmPwd = confirmPwd.val(); 

    blacklistedWords = blacklistedWords.concat( wp.passwordStrength.userInputDisallowedList() )
    submitBtn.attr( 'disabled', 'disabled' );
    strengthStatus.removeClass( 'short bad good strong' );

    var pwdStrength = wp.passwordStrength.meter( pwd, blacklistedWords, confirmPwd );

    switch ( pwdStrength ) {
        case 2:
        strengthStatus.addClass( 'bad' ).html( pwsL10n.bad );
        strengthStatus.closest('.tpgb-login-register').find('.tpgb-password-strength-wrapper').addClass( 'show' );
        break;
        
        case 3:
        strengthStatus.addClass( 'good' ).html( pwsL10n.good );
        strengthStatus.closest('.tpgb-login-register').find('.tpgb-password-strength-wrapper').addClass( 'show' );
        break;

        case 4:
        strengthStatus.addClass( 'strong' ).html( pwsL10n.strong );
        strengthStatus.closest('.tpgb-login-register').find('.tpgb-password-strength-wrapper').addClass( 'show' );
        break;

        case 5:
        strengthStatus.addClass( 'short' ).html( pwsL10n.mismatch );
        strengthStatus.closest('.tpgb-login-register').find('.tpgb-password-strength-wrapper').addClass( 'show' );

        default:
        strengthStatus.addClass( 'short' ).html( pwsL10n.short );
        strengthStatus.closest('.tpgb-login-register').find('.tpgb-password-strength-wrapper').addClass( 'show' );

    }
    if ( (4 === pwdStrength && confirmPwd && '' !== confirmPwd.trim()) ) {
        submitBtn.removeAttr( 'disabled' );
    }
     
    return pwdStrength;
}

// validate Error Msg
function keyUpValidate(field,type,validate){
    jQuery(field).keyup(function() {	
        var value = jQuery(this).val();
           
        jQuery(".tpgb-error-field").remove();
        if(jQuery(this).hasClass('tpgb-error-load')){
            jQuery(this).removeClass( "tpgb-error-load" );
        }

        if ( type !== 'email' && ( value=='' || value== undefined ) ) {
            jQuery(this).after('<span class="tpgb-error-field">'+jQuery(this).data('error')+'</span>');
            validate = false;
        }
        if(type == 'email'){
            validate = false;
            var mailformat = /^w+([.-]?w+)*@w+([.-]?w+)*(.w{2,3})+$/;
            if(value=='' || value== undefined){
                jQuery(this).after('<span class="tpgb-error-field">'+jQuery(this).data('error')+'</span>');
            }else if (!value.match(mailformat)) {
                jQuery(this).after('<span class="tpgb-error-field">'+jQuery(this).data('error')+'</span>');
            }
        }
    });
}

// Facebook login 

function statusChangeCallback(response,type='',sData) {  			
    if (response.status === 'connected') { 
      facebook_fetch_info(response, type,sData);			  
    } else {}
}

function facebook_fetch_info(response,type,sData) {
    FB.api('/me',{ fields: 'id, name, first_name, last_name, email, link, gender, locale, picture' }, function(res) {			 
        if(response.authResponse.accessToken && res.id && res.email){
            var fbData = {							
                'action' : 'tpgb_facebook_login',
                'accessToken'  : response.authResponse.accessToken,
                'id'  : res.id,
                'name' : res.name,
                'first_name' : res.first_name,
                'last_name' : res.last_name,
                'email' : res.email,
                'link' : res.link,
                'nonce' : sData.nonce,
                'appId' : sData.faceAppid,
                'secrId' : sData.faceSecid,
            };
            jQuery.ajax( {
                    type: 'POST',
                    dataType: 'json',
                    url:tpgb_load.ajaxUrl,
                    data: fbData,
                    success: function( data ) {				
                        if( data.loggedin === true || data.registered === true) {
                            //$scope.find( '.status' ).addClass( 'success' ).text( 'Thanks for logging in, ' + res.name + '!' );
                            if(sData.redirUrl){
                                window.location = sData.redirUrl;
                            }else{
                                location.reload();
                            }	
                        }
                    }
            });
        }
    });
    
}

//google Picker Function
function tpgb_googleLoginEndpoint( googleUser , clientId ) {
	
	let gclientId = '';
	
	if(clientId){
		gclientId = clientId;
	}else{
        if(googleUser.clientId){
            gclientId = googleUser.clientId
        }
	}
	
    jQuery.ajax({
        url: tpgb_load.ajaxUrl,
        method: 'post',
        data: {
            action : 'tpgb_google_pic' ,
            googleCre : googleUser.credential,
            clientId : gclientId,
        },
        dataType: 'json',
        success: function(data) {
            if( data.loggedin == true ) {
               location.reload();							
            }						
        },
        complete: function(){

        }
    });
}