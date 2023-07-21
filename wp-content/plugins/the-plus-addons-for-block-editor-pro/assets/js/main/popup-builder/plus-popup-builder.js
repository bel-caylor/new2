document.addEventListener('DOMContentLoaded', function() {
	if(!jQuery('body').hasClass('block-editor-page')) {
		jQuery('.tpgb-offcanvas-wrapper').each(function() {
			var $this = jQuery(this);
			new PlusOffcanvas($this);
			
			var container = $this.find('.scroll-view');
			var container_scroll_view = $this.find('.offcanvas-toggle-btn.position-fixed');
			if($this.hasClass('scroll-view') && container_scroll_view) {
				jQuery(window).on('scroll', function() {
					var scroll = jQuery(this).scrollTop();
					container.each(function () {
						var scroll_view_value = jQuery(this).data("scroll-view"),
							uid = jQuery(this).data("canvas-id"),
							$scroll_top = jQuery("."+uid );
						if (scroll > scroll_view_value) {
							$scroll_top.addClass('show');
						} else {
							$scroll_top.removeClass('show');
						}
					});
				});	
			}
		});
	}
});
function PlusOffcanvas(a) {
    "use strict";
    (this.wrap = a),
    (this.content = a.find(".tpgb-canvas-content-wrap")),
    (this.button = a.find(".offcanvas-toggle-btn")),
    (this.settings = this.wrap.data("settings")),
    (this.id = this.settings.content_id),
    (this.transition = this.settings.transition),
    (this.esc_close = this.settings.esc_close),
    (this.body_click_close = this.settings.body_click_close),
    (this.direction = this.settings.direction),
    (this.trigger = this.settings.trigger),
    (this.onpageLoad = this.settings.onpageLoad),
    (this.onpageloadDelay = this.settings.onpageloadDelay),
    (this.onScroll = this.settings.onScroll),
    (this.onpageviews = this.settings.onpageviews),
    (this.exitinlet = this.settings.exitInlet),
    (this.inactivity = this.settings.inactivity),
    (this.extraclick = this.settings.extraclick),
    (this.prevurl = this.settings.prevurl),
    (this.scrollHeight = this.settings.scrollHeight),
    (this.previousUrl = this.settings.previousUrl),
    (this.extraId = this.settings.extraId),
    (this.inactivitySec = this.settings.inactivitySec),
    (this.duration = 500),
    (this.time = 0),
    (this.flag = true),
    (this.ele = jQuery(".tpgb-block-" + this.id +"-canvas")),
    (this.animSetting = this.ele.data('animationsetting')) ,
    this.destroy(),
    this.init();
}
    
(PlusOffcanvas.prototype = {
    id: "",
    wrap: "",
    content: "",
    button: "",
    settings: {},
    transition: "",
    delaytimeout: "",
    ele : "",
    duration: 400,
    initialized: !1,
    animSetting : {},
    animations: ["slide", "slide-along", "reveal", "push", "popup"],
    init: function () {
        var outerClose = ("yes" === this.body_click_close ) ? '' : 'tpgb-pop-outer-none';
        this.wrap.length &&
            (jQuery("html").addClass("tpgb-offcanvas-content-widget"),
            0 === jQuery(".tpgb-offcanvas-container").length && (jQuery("body").wrapInner('<div class="tpgb-offcanvas-container '+outerClose+'"/>'), this.content.insertBefore(".tpgb-offcanvas-container")),
            0 < this.wrap.find(".tpgb-canvas-content-wrap").length &&
                (0 < jQuery(".tpgb-offcanvas-container > .tpgb-block-" + this.id +"-canvas").length && jQuery(".tpgb-offcanvas-container > .tpgb-block-" + this.id +"-canvas").remove(),
                0 < jQuery("body > .tpgb-block-" + this.id +"-canvas").length && jQuery("body > .tpgb-block-" + this.id +"-canvas").remove(),
                jQuery("body").prepend(this.wrap.find(".tpgb-canvas-content-wrap"))),
            this.bindEvents()
        );
    },
    destroy: function () {
        this.close(),
        this.animations.forEach(function (b) {
            jQuery("html").hasClass("tpgb-" + b) && jQuery("html").removeClass("tpgb-" + b);
        }),
        jQuery("body > .tpgb-block-" + this.id +"-canvas").length;
    },
    bindEvents: function () {
        (this.trigger && this.trigger == 'yes') && this.button.on("click", jQuery.proxy(this.toggleContent, this)),
        (this.extraclick == "yes" && this.extraId && this.extraId != '') && jQuery("."+this.extraId).on("click", jQuery.proxy(this.toggleContent, this)),
        (this.onpageLoad == "yes" || this.inactivity == "yes" || this.prevurl == "yes" || this.onpageviews == 'yes') && this.loadShow(),
        jQuery(window).on("scroll", jQuery.proxy(this.scrollShow, this)),
        jQuery(document).on("mouseleave", jQuery.proxy(this.exitInlet, this)),
        jQuery("body").delegate(".tpgb-canvas-content-wrap .tpgb-offcanvas-close", "click", jQuery.proxy(this.close, this)),
        "yes" === this.esc_close && this.closeESC(),
        "yes" === this.body_click_close && this.closeClick();
    },
    triggerClick: function () {
        if(this.extraclick == "yes" && this.extraId && this.extraId != '' && this.flag) {
            let extersele = jQuery(document).find("."+this.extraId);
            extersele.on("click", jQuery.proxy(this.toggleContent, this));
        }
    },
    toggleContent: function (e) {
        if(this.extraclick == "yes" && this.extraId && this.extraId != '') {
            e.preventDefault();
        }
        jQuery("html").hasClass("tpgb-open") ? this.close() : ((this.flag) ? this.show() : '');
    },
    exitInlet: function () {
        (this.exitinlet == "yes" && this.flag) ? (this.show(), this.flag = false) : "";
    },
    loadShow: function () {
        if((this.onpageLoad == "yes" || this.onpageviews == 'yes') && this.flag) {
            setTimeout(() => {
                this.show(), this.flag = false
            }, ( this.onpageLoad == "yes" ? this.onpageloadDelay : 500 ) );
        }
        if(this.inactivity == "yes" && this.flag && this.inactivitySec && this.inactivitySec != '') {
            var timeout;
            if(this.flag) {
                function resetTimer(el) {
                    clearTimeout(timeout);
                    timeout = setTimeout(function() {
                        el.show(); el.flag = false;
                    }, el.inactivitySec * 1000);
                }
            }
            document.onmousemove = resetTimer(this);
            document.onkeypress = resetTimer(this);
        }
        if((this.prevurl == "yes" && this.previousUrl && document.referrer) && this.previousUrl == document.referrer && this.flag) {
            setTimeout(() => {
                this.show();
            }, 500);
        }
    },
    scrollShow: function () {
        var scrollHeight = this.scrollHeight;
        var scroll = jQuery(window).scrollTop();
        (this.onScroll == "yes" && this.flag && (scroll >= scrollHeight)) ? (this.show(), this.flag = false) : "";
    },
    AnimIn : function(){
        if( !this.ele.hasClass("tpgb_animated") && this.animSetting && this.animSetting.anime !== undefined ){
            this.ele.removeClass("tpgb-view-animation-out tpgb_animated_out tpgb_"+this.animSetting.animeOut).addClass("tpgb_animated").addClass('tpgb_'+this.animSetting.anime)
        }
    },
    AnimOut : function(){
        if( jQuery(".tpgb-block-" + this.id +"-canvas").hasClass("tpgb-visible") && !this.ele.hasClass("tpgb_animated_out") && this.animSetting && this.animSetting.animeOut !== undefined ){
            this.ele.removeClass('tpgb_animated tpgb_'+this.animSetting.anime).addClass("tpgb-view-animation-out tpgb_animated_out").addClass('tpgb_'+this.animSetting.animeOut)
        }
    },
    show: function () {
        jQuery(".tpgb-block-" + this.id +"-canvas").addClass("tpgb-visible"),
        jQuery("html").addClass("tpgb-" + this.transition),
        jQuery("html").addClass("tpgb-" + this.direction),
        jQuery("html").addClass("tpgb-open"),
        jQuery("html").addClass("tpgb-block-" + this.id +"-canvas" + "-open"),
        jQuery("html").addClass("tpgb-reset"),
        this.button.addClass("tpgb-is-active");
        this.AnimIn();
    },
    close: function () {
        jQuery(".tpgb-block-" + this.id +"-canvas").hasClass("tpgb-slide-along") ? ((this.delaytimeout = 0), jQuery(".tpgb-block-" + this.id +"-canvas").removeClass("tpgb-visible")) : (this.delaytimeout = 0),
        jQuery("html").removeClass("tpgb-open"),
        jQuery("html").removeClass("tpgb-block-" + this.id +"-canvas" + "-open"),
        setTimeout(
            jQuery.proxy(function () {
                jQuery("html").removeClass("tpgb-reset"),
                    jQuery("html").removeClass("tpgb-" + this.transition),
                    jQuery("html").removeClass("tpgb-" + this.direction),
                    jQuery(".tpgb-block-" + this.id +"-canvas").hasClass("tpgb-slide-along") || jQuery(".tpgb-block-" + this.id +"-canvas").removeClass("tpgb-visible");
            }, this),
            this.delaytimeout
        ),
        this.button.removeClass("tpgb-is-active");
        this.AnimOut();
    },
    closeESC: function () {
        var a = this;
        "" !== a.settings.esc_close &&
            jQuery(document).on("keydown", function (c) {
                27 === c.keyCode && a.close();
            });
    },
    closeClick: function () {
        var c = this;

        var exccl = '';
		if((this.extraclick && this.extraclick == 'yes') && this.extraId && this.extraId != '' && this.flag) {
			exccl = '.'+this.extraId;
		}else{
			exccl ='.offcanvas-toggle-btn';
		}

        jQuery(document).on("click", function (a) {
        jQuery(a.target).is(".tpgb-canvas-content-wrap") ||
            0 < jQuery(a.target).parents(".tpgb-canvas-content-wrap").length ||
            jQuery(a.target).is(".offcanvas-toggle-btn") ||
            0 < jQuery(a.target).is(exccl) || jQuery(a.target).parents(exccl).length ||
            c.close();
        });
    },
});