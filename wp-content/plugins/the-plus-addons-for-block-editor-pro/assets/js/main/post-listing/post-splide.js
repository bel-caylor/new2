// Js For Splide Slider
let slideStore = new Map();

document.addEventListener('DOMContentLoaded', function() {
    var scope = document.querySelectorAll('.tpgb-carousel');
    if(scope){
        scope.forEach(function(obj) {
            splide_init(obj)
        });
    }
});

function splide_init(ele) {
    var connId = ele.getAttribute('data-connection'),
        slide = new Splide(ele).mount(),
        target = document.querySelectorAll('#' + connId);
    slideStore.set(ele, slide);

    //Carousel Remote
    if (target.length) {
        target.forEach(function(connDiv) {
            if (connDiv && connDiv.classList.contains('tpgb-carousel-remote')) {
                var remoteType = connDiv.getAttribute('data-remote'),
                    remote = connDiv.querySelectorAll('.slider-btn'),
                    dotDiv = connDiv.querySelectorAll('.tpgb-carousel-dots .tpgb-carodots-item');

                if (remote !== undefined && remote !== '') {
                    remote.forEach(function(btn) {
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            var jQuerythis = this,
                                carousel_slide = jQuerythis.getAttribute("data-nav");

                            if (remoteType == 'carousel') {
                                if (carousel_slide == 'next') {
                                    slide.go('+');
                                } else if (carousel_slide == 'prev') {
                                    slide.go('-');
                                }

                            }
                        });
                    });
                }
                if (dotDiv && dotDiv !== null) {
                    dotDiv.forEach(function(dot) {
                        dot.addEventListener('click', function() {
                            jQuery(connDiv).find(">.tpgb-carousel-dots .tpgb-carodots-item").removeClass('active default-active').addClass('inactive');
                            jQuery(this).addClass('active').removeClass('inactive');

                            var Connection = jQuery(dot).closest(".tpgb-carousel-remote").data('connection'),
                                tab_index = jQuery(dot).data("tab");
                            tpgb_carousel_conn(tab_index, Connection)

                        });
                    })
                }
            }

            //Accordion Connection
            if (connDiv && connDiv.classList.contains('tpgb-accor-wrap')) {
                var accordion = connDiv.querySelectorAll('.tpgb-accordion-header'),
                    dataconn = connDiv.getAttribute('data-connection'),
                    type = connDiv.getAttribute('data-type');

                if (dataconn && dataconn !== '') {
                    if ('accordion' == type) {
                        accordion.forEach(function(acc) {
                            acc.addEventListener('click', function() {
                                var tab_index = jQuery(acc).data("tab");
                                tpgb_carousel_conn(tab_index - 1, dataconn);
                            })
                        })
                    }
                    if ('hover' == type) {
                        accordion.forEach(function(acc) {
                            acc.addEventListener('mouseover', function() {
                                var tab_index = jQuery(acc).data("tab");
                                tpgb_carousel_conn(tab_index - 1, dataconn);
                            })
                        })
                    }
                }
            }

            //Tab Tours
            if (connDiv && connDiv.classList.contains('tpgb-tabs-wrapper')) {
                var Tab = connDiv.querySelectorAll('.tpgb-tab-header'),
                    tabdataconn = connDiv.getAttribute('data-connection'),
                    hover = connDiv.getAttribute('data-tab-hover');

                if (tabdataconn && tabdataconn !== '') {
                    if ('no' == hover) {
                        Tab.forEach(function(acc) {
                            acc.addEventListener('click', function() {
                                var tab_index = jQuery(acc).data("tab");
                                tpgb_carousel_conn(tab_index - 1, dataconn);
                            })
                        })
                    }
                    if ('yes' == hover) {
                        Tab.forEach(function(acc) {
                            acc.addEventListener('mouseover', function() {
                                var tab_index = jQuery(acc).data("tab");
                                tpgb_carousel_conn(tab_index - 1, dataconn);
                            })
                        })
                    }
                }
            }

            //Process Step
            if (connDiv && connDiv.classList.contains('tpgb-process-steps')) {
                var step = connDiv.querySelectorAll('.tpgb-p-s-wrap'),
                    stepdataconn = connDiv.getAttribute('data-connection'),
                    hover = connDiv.getAttribute('data-eventtype');

                if (stepdataconn && stepdataconn !== '') {
                    if ('con_pro_click' == hover) {
                        step.forEach(function(acc) {
                            acc.addEventListener('click', function() {
                                var tab_index = jQuery(acc).data("index");
                                tpgb_carousel_conn(tab_index - 1, dataconn);
                            })
                        })
                    }
                    if ('con_pro_hover' == hover) {
                        step.forEach(function(acc) {
                            acc.addEventListener('mouseover', function() {
                                var tab_index = jQuery(acc).data("index");
                                tpgb_carousel_conn(tab_index - 1, dataconn);
                            })
                        })
                    }
                }
            }
			
			//Interactive Circle Info
			if (connDiv && connDiv.classList.contains('tpgb-ia-circle-info')) {
                var step = connDiv.querySelectorAll('.tpgb-ia-circle-item'),
                    stepdataconn = connDiv.getAttribute('data-connection'),
                    hover = connDiv.getAttribute('data-eventtype');

                if (stepdataconn && stepdataconn !== '') {
                    if ('click' == hover) {
                        step.forEach(function(acc) {
                            acc.addEventListener('click', function() {
                                var tab_index = jQuery(acc).data("index");
                                tpgb_carousel_conn(tab_index - 1, stepdataconn);
                            })
                        })
                    }
                    if ('hover' == hover) {
                        step.forEach(function(acc) {
                            acc.addEventListener('mouseover', function() {
                                var tab_index = jQuery(acc).data("index");
                                tpgb_carousel_conn(tab_index - 1, stepdataconn);
                            })
                        })
                    }
                }
            }

            // Scroll Navigation connection
            if (connDiv && connDiv.classList.contains('tpgb-scroll-nav-inner')) {
                var step = connDiv.querySelectorAll('.tpgb-scroll-nav-item'),
                    stepdataconn = connDiv.getAttribute('data-connection');

                if (stepdataconn && stepdataconn !== '') {
                    step.forEach(function(acc) {
                        acc.addEventListener('click', function() {
                            var tab_index = jQuery(acc).data("tab");
                            tpgb_carousel_conn(tab_index, dataconn);
                        })
                    })

                }
            }

            function tpgb_carousel_conn(tab_index, Connection) {
                if (Connection != '') {
                    var current = slide.index;
                    if (current != (tab_index)) {
                        slide.go(tab_index);
                    }
                }
            }
        });

        // vice versa Connection    
        slide.on('move', function(e, currentSlide, nextSlide) {
            if (slide.length == nextSlide) {
                nextSlide = 0;
            }

            // carousel Custom Dots
            if (!jQuery("#" + connId).find('.tpgb-carodots-item[data-tab="' + parseInt(nextSlide) + '"]').hasClass("active")) {
                jQuery("#" + connId).find('.tpgb-carodots-item[data-tab="' + parseInt(nextSlide) + '"]').trigger("click");
            }

            // Pagination 
            if (jQuery("#" + connId).find('.carousel-pagination')) {
                jQuery("#" + connId).find('.carousel-pagination .pagination-list').html('<div class="active">' + ((nextSlide + 1) <= 9 ? '0' + (nextSlide + 1) : (nextSlide + 1)) + '</div>');
            }

            // Accordion Connection
            if (!jQuery("#" + connId).find('.tpgb-accordion-header[data-tab="' + parseInt(nextSlide + 1) + '"]').hasClass("active")) {
                jQuery("#" + connId).find('.tpgb-accordion-header[data-tab="' + parseInt(nextSlide + 1) + '"]').trigger("click");
            }

            //Tab Tours Connection
            if (!jQuery("#" + connId).find('.tpgb-tab-li .tpgb-tab-header[data-tab="' + parseInt(nextSlide + 1) + '"]').hasClass("active")) {
                jQuery("#" + connId).find('.tpgb-tab-li .tpgb-tab-header[data-tab="' + parseInt(nextSlide + 1) + '"]').trigger("click");
            }

            //Scroll Nav connection
            if (!jQuery("#" + connId).find('a.tpgb-scroll-nav-item[data-tab="' + parseInt(nextSlide) + '"]').hasClass("active")) {
                jQuery("#" + connId).find('.tpgb-scroll-nav-item[data-tab="' + parseInt(nextSlide) + '"]').click();
            }

            // Process Step Connection
            if (!jQuery("#" + connId).find('tpgb-p-s-wrap[data-index="' + parseInt(nextSlide + 1) + '"]').hasClass("active")) {
                jQuery("#" + connId).find('.tpgb-p-s-wrap').removeClass("active")
                jQuery("#" + connId).find('.tpgb-p-s-wrap[data-index="' + parseInt(nextSlide + 1) + '"]').addClass("active");
            }
			
			// Interactive Circle Info
            if (!jQuery("#" + connId).find('tpgb-ia-circle-item[data-index="' + parseInt(nextSlide + 1) + '"]').hasClass("active")) {
                jQuery("#" + connId).find('.tpgb-ia-circle-item').removeClass("active")
                jQuery("#" + connId).find('.tpgb-ia-circle-item[data-index="' + parseInt(nextSlide + 1) + '"]').addClass("active");
            }
        })
    }
}