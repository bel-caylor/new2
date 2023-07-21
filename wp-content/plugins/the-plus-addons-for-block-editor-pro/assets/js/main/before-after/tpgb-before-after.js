(function($) {
	"use strict";
    $(document).ready(function() {
        $('.tpgb-before-after').on('dragstart', function(event) {
            event.preventDefault()
        });
        var contId = 0,configType='',sep_Size='',indSize='',show='',initRatio='';
        $(".tpgb-before-after").each(function() {
            var $container = $(this);
            container= $container.data("id");
            var container= $('.'+container);
            container.css("visibility", "hidden");
            contId++;
            container.attr("data-before-after-id", contId);
            configType = container.data("type");
            sep_Size = container.data("separate_width");
            indSize = container.data("bottom-separator-size");
            show = container.data("show");

            if (!show) return;
            else if (configType == "cursor") {
                initRatio = container.data("separate_position");
                container.find(".tpgb-beforeafter-img").css("position", "absolute");
                container.find(".tpgb-before-sepicon").css("left", "" + initRatio + "%");
                container.find(".tpgb-beforeafter-img").css("position", "absolute");
                container.find(".tpgb-before-sepicon").hide();
                container.find(".tpgb-beforeafter-inner").on("mouseout", function(event) {
                    show_separator_image();
                    position_changing(event.pageX, event.pageY)
                })
            } else {
                initRatio = container.data("separate_position");
                if (configType == "horizontal") {
                    container.find(".tpgb-before-sepicon").css("left", "" + initRatio + "%")
                } else if (configType == "vertical") {
                    container.find(".tpgb-before-sepicon").css("top", "" + initRatio + "%")
                }
                container.find(".tpgb-beforeafter-img").css("position", "absolute");
                container.find(".tpgb-before-sepicon").show();
                container.find(".tpgb-beforeafter-inner").on("mouseout", function(event) {
                    show_separator_image();
                    position_changing(event.pageX, event.pageY)
                })
            }
            if (container.data("separate_switch") == 'no') {
                container.find(".tpgb-bottom-sep").css("display", "none");
                container.find(".tpgb-beforeafter-sep").css("display", "none");
                container.find(".tpgb-before-sepicon").css("display", "none")
            }
            if (configType != "show") {
                container.on("touchstart", function(event) {
                    setba_Container(this);
                    TouchDevice = !0;
                    changing_this = !0
                });
                container.on("touchend", function() {
                    var sep = $(".tpgb-before-after").find(".tpgb-beforeafter-sep");
                    changing_this = !1;
                    show_separator_image()
                });
                container.on("touchcancel", function() {
                    var sep = $(".tpgb-before-after").find(".tpgb-beforeafter-sep");
                    changing_this = !1;
                    show_separator_image()
                })
            }
            if (container.data("click_hover_move") == 'yes') {
                container.find(".tpgb-beforeafter-inner , .tpgb-bottom-sep").on("mouseover", function(event) {
                    setba_Container(this);
                    if (Playing_this) return;
                    changing_this = !0;
                    var pos;
                    if (ba_type == "horizontal") {
                        pos = event.pageX - ba_Container.offset().left;
                        $(this).find(".tpgb-beforeafter-sep").css("left", pos - (sep_Size / 2));
                        $(this).find(".tpgb-before-img").width(pos);
                        container.find(".tpgb-beforeafter-inner").css("cursor", "ew-resize")
                    } else if (ba_type == "vertical") {
                        pos = event.pageY - ba_Container.offset().top - (sep_Size / 2);
                        $(this).find(".tpgb-beforeafter-sep").css("top", pos);
                        $(this).find(".tpgb-before-img").height(pos);
                        container.find(".tpgb-beforeafter-inner").css("cursor", "ns-resize")
                    } else if (ba_type == "cursor") {
                        pos = event.pageX - ba_Container.offset().left;
                        $(this).find(".tpgb-beforeafter-sep").css("left", pos - (indSize / 2))
                    }
                });
                container.find(".tpgb-beforeafter-inner, .tpgb-bottom-sep").on("mouseout", function(event) {
                    position_changing(event.pageX, event.pageY);
                    changing_this = !1
                })
            } else {
                container.find(".tpgb-beforeafter-inner, .tpgb-bottom-sep").on("mousedown", function(event) {
                    if (TouchDevice) return;
                    if (ba_sep_obj && ba_sep_show) ba_sep_obj.show();
                    setba_Container(this);
                    var parent_class=$(this).parent(".tpgb-before-after");
            var parent_attr=parent_class.data("id");
                    onMouseMove(event.pageX, event.pageY,parent_attr)
                });
                container.find(".tpgb-beforeafter-inner, .tpgb-bottom-sep").on("mouseenter", function(event) {
                    if (ba_sep_obj && ba_sep_show) ba_sep_obj.show();
                    setba_Container(this);
                    if (!event.which) {
                        changing_this = !1;
                        return
                    }
                });
                container.find(".tpgb-beforeafter-sep,.tpgb-before-sepicon").on("mousedown", function(event) {
                    if (TouchDevice) return;
                    setba_Container(this);
                    changing_this = !0
                });
                container.find(".tpgb-beforeafter-sep,.tpgb-before-sepicon").on("mouseover", function(event) {
                    setba_Container(this)
                });
                container.find(".tpgb-beforeafter-sep,.tpgb-before-sepicon").on("mouseup", function() {
                    changing_this = !1
                });
                container.find(".tpgb-beforeafter-inner, .tpgb-bottom-sep").on("mouseup", function() {
                    changing_this = !1
                })
            }
            container.find(".tpgb-beforeafter-inner").on("mousedown", function(event) {
                if (TouchDevice) return;
                setba_Container(this);
                stop_animation()
            })
        });
        size_Elements();
        
        $(".tpgb-beforeafter-inner").on("mousemove", function(event) {
            var parent_class=$(this).parent(".tpgb-before-after");
            var parent_attr=parent_class.data("id");
            if (changing_this && !Playing_this) onMouseMove(event.pageX, event.pageY,parent_attr)
        });
        $(".tpgb-bottom-sep").on("mousemove", function(event) {
        var parent_class=$(this).parent(".tpgb-before-after");
            var parent_attr=parent_class.data("id");
            if (changing_this && !Playing_this) onMouseMove(event.pageX, event.pageY,parent_attr)
        });
        $(".tpgb-beforeafter-inner").on("touchmove", function(event) {
            event.preventDefault();
            var touch = event.originalEvent.touches[0] || event.originalEvent.changedTouches[0];
            var parent_class=$(this).parent(".tpgb-before-after");
            var parent_attr=parent_class.data("id");
            if (changing_this && !Playing_this) onMouseMove(touch.pageX, touch.pageY,parent_attr)
        });
        $(".tpgb-bottom-sep").on("touchmove", function(event) {
            event.preventDefault();
            var touch = event.originalEvent.touches[0] || event.originalEvent.changedTouches[0];
            var parent_class=$(this).parent(".tpgb-before-after");
            var parent_attr=parent_class.data("id");
            if (changing_this && !Playing_this) onMouseMove(touch.pageX, touch.pageY,parent_attr)
        });
        $(window).on('resize', function() {
            size_Elements()
        })
    })

})(jQuery);

var ba_Container;
var ba_ContainerId = 0;
var ba_obj;
var ba_sep_obj;
var ba_sep_Image;
var before_obj;
var after_obj;
var beforeImage;
var afterImage;
var ba_type;
var ba_sep_show;
var ba_show_mode;
var changing_this = !1;
var Playing_this = !1;
var sep_Size;
var indSize = 10;
var fpsPlay = 60;
var TouchDevice = !1;

function setba_Container(objFrom) {
    container = jQuery(objFrom).closest(".tpgb-before-after");
    containerId = container.data("id");
    hide_separator_image(container);
    if (ba_Container && ba_ContainerId == containerId) return;
    if (Playing_this) {
        stop_animation();
        if (ba_sep_show) ba_sep_obj.show()
    }
    ba_Container = container;
    ba_ContainerId = containerId;
    ba_sep_obj = ba_Container.find(".tpgb-beforeafter-sep");
    ba_obj = ba_Container.find(".tpgb-beforeafter-inner");
    before_obj = ba_Container.find(".tpgb-before-img");
    after_obj = ba_Container.find(".tpgb-after-img");
    beforeImage = ba_Container.find(".tpgb-before-img > img");
    afterImage = ba_Container.find(".tpgb-after-img > img");
    ba_type = ba_Container.data("type");
    ba_sep_show = !0;
    if (ba_Container.data("separate_switch") == 'no')
        ba_sep_show = !1;
    if (ba_sep_show == !0) ba_sep_obj.show();
    else ba_sep_obj.hide()
}

function play_animation(curPx, deltaPx, x, y, sizePx, frameDelay) {
    if (!Playing_this) return;
    curPx += deltaPx;
    if (ba_type == "vertical")
        onMouseMove(x, y + curPx);
    else onMouseMove(x + curPx, y);
    if (curPx <= sizePx + 1) setTimeout(play_animation, frameDelay, curPx, deltaPx, x, y, sizePx, frameDelay);
    else stop_animation()
}

function stop_animation() {
    if (!Playing_this) return;
    Playing_this = !1;
    changing_this = !0;
    ba_sep_obj.hide()
}

function onMouseMove(x, y,parent_attr) {
    if (changing_this && ba_sep_show) ba_sep_obj.show();
    if (position_changing(x, y)) return;
	var container_class=jQuery("."+parent_attr);
	sep_Size=container_class.data("separate_width");
    if (ba_type == "horizontal") {
	var Id = jQuery("."+containerId).offset();
		var abc = Id.left;
        //pos = x - ba_Container.offset().left;
        pos = x - abc;
		
        if (pos >= ba_obj.width()) pos = ba_obj.width();
		
	//ba_sep_obj.css("left", pos - (sep_Size / 2));
        jQuery("."+containerId+" .tpgb-beforeafter-sep").css("left", pos - (sep_Size / 2));
        if (ba_show_mode != 0) {
            ba_sep_Image.css("left", pos)
        }
        //before_obj.width(pos);
        jQuery("."+containerId+" .tpgb-beforeafter-img.tpgb-before-img").width(pos);
		var before_label=before_obj.find('.tpgb-beforeafter-label.before-label');
		if((before_label.width()+50) < pos){
			before_obj.find('.tpgb-beforeafter-label.before-label').css("opacity",'1');		
		}else{
			before_obj.find('.tpgb-beforeafter-label.before-label').css("opacity",'0');
		}
    } else if (ba_type == "vertical") {
		var Id = jQuery("."+containerId).offset();
		var abc = Id.top;
		 //pos = y - ba_Container.offset().top;
         pos = y - abc;
		
        if (pos >= ba_obj.height()) pos = ba_obj.height();
		
		//ba_sep_obj.css("top", pos - (sep_Size / 2));        
		jQuery("."+containerId+" .tpgb-beforeafter-sep").css("top", pos - (sep_Size / 2));
        if (ba_show_mode != 0) {
            ba_sep_Image.css("top", pos)
        }
		
        //before_obj.height(pos);
		//var before_label=before_obj.find('.tpgb-beforeafter-label.before-label');
		//var bf_obj=ba_obj.height();
		//bf_obj=bf_obj/2;
		jQuery("."+containerId+" .tpgb-beforeafter-img.tpgb-before-img").height(pos);
		var before_label=before_obj.find('.tpgb-beforeafter-label.before-label');
        //pos >= ba_obj.height()   before_label.height()+250) < pos
        rat =  ba_obj.height()/pos;

		if(rat < 2){
			before_obj.find('.tpgb-beforeafter-label.before-label').css("opacity",'1');		
		}else{
			before_obj.find('.tpgb-beforeafter-label.before-label').css("opacity",'0');
		}
    } else if (ba_type == "cursor") {
		pos = x - ba_Container.offset().left;
        if (pos >= ba_obj.width()) pos = ba_obj.width();
        ba_sep_obj.css("left", pos - (indSize / 2));
        rat = pos / ba_obj.width();
        beforeImage.css("opacity", 1 - rat);
		var before_label=before_obj.find('.tpgb-beforeafter-label.before-label');
		if((1 - rat) > 0.3 ){
			before_obj.find('.tpgb-beforeafter-label.before-label').css("opacity",'1');		
		}else{
			before_obj.find('.tpgb-beforeafter-label.before-label').css("opacity",'0');
		}
    }
}

function size_Elements() {
    jQuery(".tpgb-beforeafter-img > img").imgLoad(function() {
        img = jQuery(this);
       
        container = img.closest(".tpgb-before-after");
		container=container.data("id");
		container=jQuery("."+container);
        style = container.data("type");
        if (style == "show") return;
        img.css("min-width", "none");
        img.css("max-width", "none");
        sbsShrinked = !1;
        if (container.data("responsive") == "yes") {
            p = container.parent();
            parentWidth = p.width();
            while (!p || parentWidth == 0) {
                p = p.parent();
                parentWidth = p.width()
            }
            if ((!img.css("max-width") || img.css("max-width") == "none") && container.data("full_width") == "yes") {
                img.css("max-width", parentWidth)
                
            }else if(!img.css("max-width") || img.css("max-width") == "none"){
				img.css("max-width", img.width())
			}
			if(container.data("full_width") == "yes"){
				img.css("width", parentWidth)
			}else{
				img.css("width", img.width())
			}
        }
        if (container.data("width")) {
            img.css("width", container.data("width"))
        }
        if (container.data("max-width")) {
            img.css("max-width", container.data("max-width"))
        }
        initRatio = container.data("separate_position") / 100;
		if((img.hasClass('tpgb-beforeimg-wrap') && style != "vertical") || img.hasClass('tpgb-afterimg-wrap')){
			img.css("height", "100%");
		}
        if (img.hasClass('tpgb-beforeimg-wrap')) {
            container.css("visibility", "visible");
            width = img.width();
            height = img.height();
            container.find(".tpgb-beforeafter-img").width("auto").css('height','100%');
            container.find(".tpgb-beforeafter-inner").width(width).height(height);
            container.find(".tpgb-bottom-sep").width(width);
            container.width(width);
            if (style == "horizontal"){
                container.find(".tpgb-before-img").width(img.width() * initRatio);
            }else if (style == "vertical"){
                container.find(".tpgb-before-img").height(img.height() * initRatio);
            }
            separator = container.find(".tpgb-beforeafter-sep");
            
            if (style == "horizontal") {
                if (container.data("separator_style") == 'middle') {
					sep_Size = container.data("separate_width");
                    separator.width(sep_Size);
                    separator.height(img.height());
                    sp = container.find(".tpgb-before-img").width() - sep_Size / 2;
                    separator.css("left", sp);
                    separator.css("cursor", "ew-resize")
                } else {
                    sep_Size = container.data("separate_width");
                    separator.height(sep_Size);
                    separator.width(15);
                    separator.css("left", (img.width() * initRatio) + 'px');
                    var h = container.find(".tpgb-before-img").height();
                    separator.css("top", h);
                    separator.css("cursor", "ew-resize");
                    container.find(".tpgb-bottom-sep").height(sep_Size);
                    container.find(".tpgb-bottom-sep").show()
                }
            } else if (style == "vertical") {
                if (container.data("separator_style") == 'middle') {
					sep_Size = container.data("separate_width");
                    separator.height(sep_Size);
                    separator.width(img.width());
                    sp = container.find(".tpgb-before-img").height() - sep_Size / 2;
                    separator.css("top", sp);
                    separator.css("cursor", "ns-resize")
                }
            } else if (style == "cursor") {
                if (container.data("separate_switch") == 'yes') {
                    sep_Size = container.data("separate_width");
                    var h = container.find(".tpgb-before-img").height();
                    container.find(".tpgb-bottom-sep").show()
                }
            }
        }
    })
}

function hide_separator_image(container) {
    ba_sep_Image = container.find(".tpgb-before-sepicon");
    ba_show_mode = 0;
    if (container.data("separate_image"))
        ba_show_mode = container.data("separate_image")
}

function show_separator_image() {
    if (ba_show_mode == 1) {
        ba_sep_Image.show()
    }
}

function full_After() {
    w = ba_obj.width() - indSize;
    ba_sep_obj.css("left", w);
    beforeImage.css("opacity", "0")
}

function zero_After() {
    ba_sep_obj.css("left", 0);
    beforeImage.css("opacity", "1")
}

function position_changing(pageX, pageY) {
    if (!(changing_this || Playing_this)) return !1;
    if (Playing_this) return !1;
    aligned = !1;
    if (ba_type == "horizontal") {
		//alert(ba_obj.width());
		var Id = jQuery("."+containerId).offset();
		var abc = Id.left;
        //if (pageX >= ba_obj.width() + ba_Container.offset().left) {
        if (pageX >= ba_obj.width() + abc) {
            sep_Right();
            aligned = !0
        } else if (pageX <= abc) {
            sep_Left();
            aligned = !0
        }
    } else if (ba_type == "vertical") {
		var Id = jQuery("."+containerId).offset();
		var abc = Id.top;
        if (pageY >= ba_obj.height() + abc) {
            sep_Bottom();
            aligned = !0
        } else if (pageY <= abc) {
            sep_Top();
            aligned = !0
        }
    } else if (ba_type == "cursor") {
		 if (pageX + indSize / 2 >= ba_obj.width() + ba_Container.offset().left) {
            full_After();
            aligned = !0
        } else if (pageX - indSize / 2 <= ba_Container.offset().left) {
            zero_After();
            aligned = !0
        }
    }
    if (aligned && ba_type != "cursor") {
        if (!Playing_this)
            ba_sep_obj.hide()
    }
    return aligned
}(function($) {
    $.fn.imgLoad = function(callback) {
        return this.each(function() {
            if (callback) {
                if (this.complete) {
                    callback.apply(this)
                } else {
                    $(this).on('load', function() {
                        callback.apply(this)
                    })
                }
            }
        })
    }
})(jQuery);

function sep_Right() {
    w = ba_obj.width();
    ba_sep_obj.css("left", w - sep_Size / 2);
    if (ba_show_mode != 0) {
        ba_sep_Image.css("left", w)
    }
    before_obj.width(w)
}

function sep_Left() {
    ba_sep_obj.css("left", 0);
    if (ba_show_mode != 0) {
        ba_sep_Image.css("left", 0)
    }
    before_obj.width(0)
}

function sep_Top() {
    ba_sep_obj.css("top", -sep_Size / 2);
    if (ba_show_mode != 0) {
        ba_sep_Image.css("top", 0)
    }
    before_obj.height(0)
}

function sep_Bottom() {
    h = ba_obj.height();
    ba_sep_obj.css("top", h - sep_Size / 2);
    if (ba_show_mode != 0) {
        ba_sep_Image.css("top", h)
    }
    before_obj.height(h)
}