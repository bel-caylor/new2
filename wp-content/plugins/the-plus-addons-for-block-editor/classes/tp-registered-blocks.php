<?php 
/**
 * The Plus Blocks Registered Lists
 *
 * @since   1.0.0
 * @package TPGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function tpgb_registered_blocks(){
	$tpgb_free = TPGB_PATH . DIRECTORY_SEPARATOR;
	// Blocks class map
	$load_blocks_css_js = [
		TPGB_CATEGORY.'/tp-row' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-row/style.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/main/tp-row/tpgb-row.min.js',
			],
		],
		TPGB_CATEGORY.'/tp-column' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-column/style.css',
			],
		],
		TPGB_CATEGORY.'/tp-container' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-container/style.css',
			],
		],
		TPGB_CATEGORY.'/tp-container-inner' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-container-inner/style.css',
			],
		],
		TPGB_CATEGORY.'/tp-code-highlighter' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-code-highlighter/style.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/main/code-highlighter/tpgb-code-highlighter.js',
			],
		],
		'tpx-prism-default' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-code-highlighter/theme/default.css',
			],
			'js' => [
				$tpgb_free .'assets/js/extra/code-highlighter/prism-default.js',
			],
		],
		'tpx-prism-coy' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-code-highlighter/theme/coy.css',
			],
			'js' => [
				$tpgb_free .'assets/js/extra/code-highlighter/prism-coy.js',
			],
		],
		'tpx-prism-dark' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-code-highlighter/theme/dark.css',
			],
			'js' => [
				$tpgb_free .'assets/js/extra/code-highlighter/prism-dark.js',
			],
		],
		'tpx-prism-funky' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-code-highlighter/theme/funky.css',
			],
			'js' => [
				$tpgb_free .'assets/js/extra/code-highlighter/prism-funky.js',
			],
		],
		'tpx-prism-okaidia' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-code-highlighter/theme/okaidia.css',
			],
			'js' => [
				$tpgb_free .'assets/js/extra/code-highlighter/prism-okaidia.js',
			],
		],
		'tpx-prism-solarizedlight' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-code-highlighter/theme/solarizedlight.css',
			],
			'js' => [
				$tpgb_free .'assets/js/extra/code-highlighter/prism-solarizedlight.js',
			],
		],
		'tpx-prism-tomorrownight' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-code-highlighter/theme/tomorrownight.css',
			],
			'js' => [
				$tpgb_free .'assets/js/extra/code-highlighter/prism-tomorrownight.js',
			],
		],
		'tpx-prism-twilight' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-code-highlighter/theme/twilight.css',
			],
			'js' => [
				$tpgb_free .'assets/js/extra/code-highlighter/prism-twilight.js',
			],
		],
		TPGB_CATEGORY.'/tp-pro-paragraph' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-pro-paragraph/style.css',
			],
		],
		TPGB_CATEGORY.'/tp-accordion' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-accordion/style.css',
			],
			'js' => [
				$tpgb_free .'assets/js/main/common-created/tpgb-slidetoggle-block.min.js',
				$tpgb_free .'assets/js/main/accordion/tpgb-accordion.min.js',
			],
		],
		TPGB_CATEGORY.'/tp-accordion-inner' => [],
		TPGB_CATEGORY.'/tp-button' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style.css',
			],
		],
		'tpx-button-style-1' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-1.css',
			],
		],
		'tpx-button-style-2' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-2.css',
			],
		],
		'tpx-button-style-3' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-3.css',
			],
		],
		'tpx-button-style-4' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-4.css',
			],
		],
		'tpx-button-style-5' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-5.css',
			],
		],
		'tpx-button-style-6' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-6.css',
			],
		],
		'tpx-button-style-7' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-7.css',
			],
		],
		'tpx-button-style-8' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-8.css',
			],
		],
		'tpx-button-style-9' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-9.css',
			],
		],
		'tpx-button-style-10' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-10.css',
			],
		],
		'tpx-button-style-11' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-11.css',
			],
		],
		'tpx-button-style-12' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-12.css',
			],
		],
		'tpx-button-style-13' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-13.css',
			],
		],
		'tpx-button-style-14' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-14.css',
			],
		],
		'tpx-button-style-15' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-15.css',
			],
		],
		'tpx-button-style-16' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-16.css',
			],
		],
		'tpx-button-style-17' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-17.css',
			],
		],
		'tpx-button-style-18' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-18.css',
			],
		],
		'tpx-button-style-19' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-19.css',
			],
		],
		'tpx-button-style-20' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-20.css',
			],
		],
		'tpx-button-style-21' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-21.css',
			],
		],
		'tpx-button-style-22' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-22.css',
			],
		],
		'tpx-button-style-23' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/style/style-23.css',
			],
		],
		'tpx-button-shake-ani' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-button/shake-ani.css',
			],
		],
		TPGB_CATEGORY.'/tp-countdown' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-countdown/style.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/main/countdown/countdown.min.js',
			],
		],
		'countdown-style-1' => [
			'js' => [
				$tpgb_free . 'assets/js/extra/jquery.downCount.js',
			],
		],
		
		TPGB_CATEGORY.'/tp-data-table' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-data-table/style.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/extra/jquery.datatables.min.js',
				$tpgb_free .'assets/js/main/data-table/tpgb-data-table.min.js',
			],
		],
		TPGB_CATEGORY.'/tp-dark-mode' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-dark-mode/style.css',
			],
			'js' => [
				$tpgb_free .'assets/js/main/dark-mode/tpgb-dark-mode.min.js',
			],
		],
		'tpx-dark-mode-style-1' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-dark-mode/style-1.css',
			],
		],
		'tpx-dark-mode-style-2' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-dark-mode/style-2.css',
			],
		],
		'tpx-dark-mode-style-3' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-dark-mode/style-3.css',
			],
		],
		TPGB_CATEGORY.'/tp-draw-svg' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-draw-svg/style.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/extra/vivus.min.js',
				$tpgb_free . 'assets/js/main/draw-svg/tpgb-draw-svg.min.js',
			],
		],
		TPGB_CATEGORY.'/tp-messagebox' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-messagebox/style.css',
			],
			'js' => [
				$tpgb_free .'assets/js/main/common-created/tpgb-slidetoggle-block.min.js',
				$tpgb_free . 'assets/js/main/messagebox/tpgb-messagebox.min.js',
			],
		],
		
		TPGB_CATEGORY.'/tp-testimonials' => [
			'css' => [
				$tpgb_free .'assets/css/extra/bootstrap-grid.min.css',
				$tpgb_free .'assets/css/extra/splide.min.css',
				$tpgb_free .'assets/css/main/post-listing/splide-carousel.min.css',
				$tpgb_free .'classes/blocks/tp-testimonials/style.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/extra/splide.min.js',
				$tpgb_free . 'assets/js/main/post-listing/post-splide.min.js',
				$tpgb_free . 'assets/js/main/testimonial/tpgb-testimonial.min.js',
			],
		],
		'tpx-testimonials-style-1' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-testimonials/tp-testimonials-style-1.css',
			],
		],
		'tpx-testimonials-style-2' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-testimonials/tp-testimonials-style-2.css',
			],
		],
		'tpx-testimonials-scroll' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-testimonials/tp-testimonials-scroll.css',
			],
		],
		TPGB_CATEGORY.'/tp-stylist-list' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-stylist-list/style.css',
			],
			'js' => [
				$tpgb_free .'assets/js/main/stylist-list/tp-stylist-list.min.js',
			],
		],
		'tpx-stylist-list-hover-inverse' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-stylist-list/style-hover-inverse.css',
			],
		],
		TPGB_CATEGORY.'/tp-empty-space' => [],
		TPGB_CATEGORY.'/tp-external-form-styler' => [
			'css' => [
				$tpgb_free .'assets/css/extra/bootstrap-grid.min.css',
				$tpgb_free .'classes/blocks/tp-external-form-styler/style.css',
			],
		],
		'tpx-contact-form-7' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-external-form-styler/forms/cf7.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/main/external-form-styler/tpgb-cf7.min.js',
			],
		],
		'tpx-everest-form' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-external-form-styler/forms/everest.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/main/external-form-styler/tpgb-everest-form.min.js',
			],
		],
		'tpx-gravity-form' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-external-form-styler/forms/gravity.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/main/external-form-styler/tpgb-gravity-form.min.js',
			],
		],
		'tpx-ninja-form' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-external-form-styler/forms/ninja.css',
			],
		],
		'tpx-wp-form' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-external-form-styler/forms/wp.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/main/external-form-styler/tpgb-wp-form.min.js',
			],
		],
		TPGB_CATEGORY.'/tp-hovercard' => [],
		TPGB_CATEGORY.'/tp-heading-title' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-heading-title/style.css',
			],
		],
		'tpx-heading-title-style-1' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-heading-title/style/style-1.css',
			],
		],
		'tpx-heading-title-style-3' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-heading-title/style/style-3.css',
			],
		],
		'tpx-heading-title-style-4' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-heading-title/style/style-4.css',
			],
		],
		'tpx-heading-title-style-5' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-heading-title/style/style-5.css',
			],
		],
		'tpx-heading-title-style-6' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-heading-title/style/style-6.css',
			],
		],
		'tpx-heading-title-style-8' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-heading-title/style/style-8.css',
			],
		],
		'tpx-heading-title-style-9' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-heading-title/style/style-9.css',
			],
			'js' => [
				$tpgb_free .'assets/js/extra/jquery.waypoints.min.js',
				$tpgb_free .'assets/js/extra/tweenmax/gsap.min.js',
				$tpgb_free .'assets/js/extra/splittext.min.js',
				$tpgb_free .'assets/js/main/heading-title/tpgb-heading-title.min.js',
			],
		],
		TPGB_CATEGORY.'/tp-progress-bar' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-progress-bar/style.css',
			],
			'js' => [
				$tpgb_free .'assets/js/extra/jquery.waypoints.min.js',
			],
		],
		'tpx-progressbar' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-progress-bar/progressbar.css',
			],
			'js' => [
				$tpgb_free .'assets/js/main/progress-bar/plus-progressbar.min.js',
			],
		],
		'tpx-piechart' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-progress-bar/piechart.css',
			],
			'js' => [
				$tpgb_free .'assets/js/extra/circle-progress.js',
				$tpgb_free .'assets/js/main/progress-bar/plus-piechart.min.js',
			],
		],
		TPGB_CATEGORY.'/tp-progress-tracker' => [
			'css' => [		
				$tpgb_free .'classes/blocks/tp-progress-tracker/style.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/main/progress-tracker/tpgb-progress-tracker.min.js',
			],
		],
		TPGB_CATEGORY.'/tp-pricing-list' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-pricing-list/style.css',
			],
		],
		'tpx-pricing-list-style-1' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-pricing-list/style-1.css',
			],
		],
		'tpx-pricing-list-style-2' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-pricing-list/style-2.css',
			],
		],
		'tpx-pricing-list-style-3' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-pricing-list/style-3.css',
			],
		],
		'tpx-pricing-list-masking' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-pricing-list/masking.css',
			],
		],
		TPGB_CATEGORY.'/tp-number-counter' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-number-counter/style.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/extra/numscroller.js',
			],
		],
		'tpx-number-counter-s2' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-number-counter/style-2.css',
			],
		],
		TPGB_CATEGORY.'/tp-infobox' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-infobox/style.css',
			],
		],
		'tpx-infobox-style-1' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-infobox/style-1.css',
			],
		],
		'tpx-infobox-style-2' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-infobox/style-2.css',
			],
		],
		'tpx-infobox-style-3' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-infobox/style-3.css',
			],
		],
		'tpx-infobox-style-5' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-infobox/style-5.css',
			],
		],
		'tpx-infobox-style-6' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-infobox/style-6.css',
			],
		],
		TPGB_CATEGORY.'/tp-interactive-circle-info' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-interactive-circle-info/style.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/main/interactive-circle-info/tpgb-interactive-circle-info.min.js',
			],
		],
		TPGB_CATEGORY.'/tp-flipbox' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-flipbox/style.css',
			],
		],
		TPGB_CATEGORY.'/tp-google-map' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-google-map/style.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/main/google-map/tpgb-google-map.min.js',
			],
		],
		
		TPGB_CATEGORY.'/tp-tabs-tours'  => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-tabs-tours/style.css',
			],
			'js' => [
				$tpgb_free .'assets/js/main/tabs-tours/plus-tabs-tours.min.js',
			],
		],
		'tpx-tabs-tours-vertical' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-tabs-tours/style-vertical.css',
			],
		],
		TPGB_CATEGORY.'/tp-tab-item'  => [],
		TPGB_CATEGORY.'/tp-blockquote' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-blockquote/style.css',
			],
		],
		'tpx-blockquote-style-2' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-blockquote/style-2.css',
			],
		],
		TPGB_CATEGORY.'/tp-breadcrumbs' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-breadcrumbs/style.css',
			],
		],
		'tpx-breadcrumbs-style-1' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-breadcrumbs/style-1.css',
			],
		],
		'tpx-breadcrumbs-style-2' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-breadcrumbs/style-2.css',
			],
		],
		TPGB_CATEGORY.'/tp-video' => [
			'css' => [
				$tpgb_free .'assets/css/extra/jquery.fancybox.min.css',
				$tpgb_free .'classes/blocks/tp-video/style.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/extra/jquery.fancybox.min.js',
				$tpgb_free . 'assets/js/main/video/plus-video-player.min.js',
			],
		],
		TPGB_CATEGORY.'/tp-creative-image' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-creative-image/style.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/main/creative-image/plus-image-factory.min.js',
			],
		],
		'tpx-tp-image-mask-img' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-creative-image/style-mask-image.css',
			],
		],
		TPGB_CATEGORY.'/tp-social-icons' => [
			'css' => [
				$tpgb_free .'assets/css/extra/tippy.css',
				$tpgb_free .'classes/blocks/tp-social-icons/style.css',
			],
			'js' => [
				$tpgb_free .'assets/js/extra/popper.min.js',
				$tpgb_free .'assets/js/extra/tippy.min.js',
				$tpgb_free .'assets/js/main/social-icons/tpgb-social-icons.min.js',
			],
		],
		'tpx-social-icons-style-1' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-icons/style/style-1.css',
			],
		],
		'tpx-social-icons-style-2' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-icons/style/style-2.css',
			],
		],
		'tpx-social-icons-style-3' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-icons/style/style-3.css',
			],
		],
		'tpx-social-icons-style-4' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-icons/style/style-4.css',
			],
		],
		'tpx-social-icons-style-5' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-icons/style/style-5.css',
			],
		],
		'tpx-social-icons-style-6' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-icons/style/style-6.css',
			],
		],
		'tpx-social-icons-style-7' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-icons/style/style-7.css',
			],
		],
		'tpx-social-icons-style-8' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-icons/style/style-8.css',
			],
		],
		'tpx-social-icons-style-9' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-icons/style/style-9.css',
			],
		],
		'tpx-social-icons-style-10' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-icons/style/style-10.css',
			],
		],
		'tpx-social-icons-style-11' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-icons/style/style-11.css',
			],
		],
		'tpx-social-icons-style-12' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-icons/style/style-12.css',
			],
		],
		'tpx-social-icons-style-13' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-icons/style/style-13.css',
			],
		],
		'tpx-social-icons-style-14' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-icons/style/style-14.css',
			],
		],
		'tpx-social-icons-style-15' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-icons/style/style-15.css',
			],
		],
		'tpx-social-icons-style-16' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-icons/style/style-16.css',
			],
		],
		TPGB_CATEGORY.'/tp-social-feed' => [
			'css' => [
				$tpgb_free .'assets/css/extra/jquery.fancybox.min.css',
				$tpgb_free .'assets/css/extra/bootstrap-grid.min.css',
				$tpgb_free .'classes/blocks/tp-social-feed/style.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/extra/jquery.fancybox.min.js',
				$tpgb_free . 'assets/js/extra/isotope.pkgd.min.js',
				$tpgb_free . 'assets/js/main/post-listing/post-masonry.min.js',
				$tpgb_free . 'assets/js/main/social-feed/tp-social-feed.min.js',
			],
		],
		'tpx-social-feed-style-3' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-feed/style-3.css',
			],
		],
		'tpx-social-feed-style-4' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-feed/style-4.css',
			],
		],
		TPGB_CATEGORY.'/tp-social-reviews' => [
			'css' => [
				$tpgb_free .'assets/css/extra/bootstrap-grid.min.css',
				$tpgb_free .'classes/blocks/tp-social-reviews/style.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/extra/isotope.pkgd.min.js',
				$tpgb_free . 'assets/js/main/post-listing/post-masonry.min.js',
				$tpgb_free . 'assets/js/main/social-reviews/tp-social-reviews.min.js',
			],
		],
		'tpx-review-style-1' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-reviews/review/style-1.css',
			],
		],
		'tpx-review-style-2' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-reviews/review/style-2.css',
			],
		],
		'tpx-review-style-3' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-reviews/review/style-3.css',
			],
		],
		TPGB_CATEGORY.'/tp-post-title' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-post-title/style.css',
			],
		],
		TPGB_CATEGORY.'/tp-post-content' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-post-content/style.css',
			],
		],
		TPGB_CATEGORY.'/tp-post-image' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-post-image/style.css',
			],
			'js' => [
				$tpgb_free .'assets/js/main/tp-post-image/tpgb-post-image.min.js',
			],
		],
		TPGB_CATEGORY.'/tp-post-listing' => [
			'css' => [
				$tpgb_free .'assets/css/extra/bootstrap-grid.min.css',
				$tpgb_free .'classes/blocks/tp-post-listing/style.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/extra/isotope.pkgd.min.js',
				$tpgb_free .'assets/js/main/common-created/tpgb-slidetoggle-block.min.js',
				$tpgb_free . 'assets/js/main/post-listing/post-masonry.min.js',
				$tpgb_free . 'assets/js/main/post-listing/post-listing.min.js',
			],
		],
		'tpx-post-listing-style-1' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-post-listing/tp-post-listing-style-1.css',
			],
		],
		'tpx-post-listing-style-2' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-post-listing/tp-post-listing-style-2.css',
			],
		],
		'tpgb-pagination' => [
			'css' => [
				$tpgb_free .'assets/css/main/post-listing/tpgb-pagination.css',
			],
		],
		TPGB_CATEGORY.'/tp-post-author' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-post-author/style.css',
			],
		],
		'tpx-tp-post-author-style-2' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-post-author/style-author-style-2.css',
			],
		],
		'tpx-tp-post-author-avatar' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-post-author/style-author-avatar.css',
			],
		],
		'tpx-tp-post-author-social' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-post-author/style-author-social.css',
			],
		],
		'tpx-tp-post-author-role' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-post-author/style-author-role.css',
			],
		],
		TPGB_CATEGORY.'/tp-post-meta' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-post-meta/style.css',
			],
		],
		'tpx-tp-post-meta-category' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-post-meta/style-category.css',
			],
		],
		'tpx-tp-post-layout-2' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-post-meta/style-layout-2.css',
			],
		],
		TPGB_CATEGORY.'/tp-post-comment' => [
			'css' => [
				$tpgb_free .'assets/css/extra/bootstrap-grid.min.css',
				$tpgb_free .'classes/blocks/tp-post-comment/style.css',
			],
		],
		TPGB_CATEGORY.'/tp-search-bar' => [
			'css' => [
				$tpgb_free .'assets/css/extra/bootstrap-grid.min.css',
				$tpgb_free .'assets/css/main/post-listing/tpgb-post-load.css',
				$tpgb_free .'classes/blocks/tp-search-bar/style.css',
			],
			'js' => [
				$tpgb_free .'assets/js/main/common-created/tpgb-slidetoggle-block.min.js',
				$tpgb_free . 'assets/js/main/search-bar/tpgb-search-bar.min.js', 
			],
		],
		TPGB_CATEGORY.'/tp-site-logo' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-site-logo/style.css',
			],
		],
		TPGB_CATEGORY.'/tp-smooth-scroll' => [
			'js' => [
				$tpgb_free .'assets/js/extra/smoothscroll.min.js',
				$tpgb_free .'assets/js/main/smooth-scroll/tpgb-smooth-scroll.min.js',
			],
		],
		TPGB_CATEGORY.'/tp-social-embed' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-social-embed/style.css',
			],
		],
		TPGB_CATEGORY.'/tp-pricing-table' => [
			'css' => [
				$tpgb_free .'classes/blocks/tp-pricing-table/style.css',
			],
		],
		'carouselSlider' => [
			'css' => [
				$tpgb_free .'assets/css/extra/splide.min.css',
				$tpgb_free .'assets/css/main/post-listing/splide-carousel.min.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/extra/splide.min.js',
				$tpgb_free . 'assets/js/main/post-listing/post-splide.min.js',
			],
		],
		
		'content-hover-effect' => [
			'css' => [
				$tpgb_free .'assets/css/main/plus-extras/plus-content-hover-effect.css',
			],
		],
		'tpgb-group-button' => [
			'css' => [
				$tpgb_free .'assets/css/main/plus-extras/plus-group-button.css',
			],
		],
		'tpgb-animation' => [
			'css' => [
				$tpgb_free .'assets/css/extra/animate.min.css',
			],
			'js' => [
				$tpgb_free .'assets/js/extra/jquery.waypoints.min.js',
				$tpgb_free .'assets/js/main/plus-extras/plus-animation.min.js',
			],
		],
		'tpgb-animation-fadeIn' => [
			'css' => [
				$tpgb_free .'assets/css/extra/animate-fadeIn.css',
			],
		],
		'tpgb-animation-fadeOut' => [
			'css' => [
				$tpgb_free .'assets/css/extra/animate-fadeOut.css',
			],
		],
		'tpgb-animation-flipIn' => [
			'css' => [
				$tpgb_free .'assets/css/extra/animate-flipIn.css',
			],
		],
		'tpgb-animation-flipOut' => [
			'css' => [
				$tpgb_free .'assets/css/extra/animate-flipOut.css',
			],
		],
		'tpgb-animation-lightSpeedIn' => [
			'css' => [
				$tpgb_free .'assets/css/extra/animate-lightSpeedIn.css',
			],
		],
		'tpgb-animation-lightSpeedOut' => [
			'css' => [
				$tpgb_free .'assets/css/extra/animate-lightSpeedOut.css',
			],
		],
		'tpgb-animation-rollIn' => [
			'css' => [
				$tpgb_free .'assets/css/extra/animate-rollIn.css',
			],
		],
		'tpgb-animation-rollOut' => [
			'css' => [
				$tpgb_free .'assets/css/extra/animate-rollOut.css',
			],
		],
		'tpgb-animation-rotateIn' => [
			'css' => [
				$tpgb_free .'assets/css/extra/animate-rotateIn.css',
			],
		],
		'tpgb-animation-rotateOut' => [
			'css' => [
				$tpgb_free .'assets/css/extra/animate-rotateOut.css',
			],
		],
		'tpgb-animation-seekers' => [
			'css' => [
				$tpgb_free .'assets/css/extra/animate-seekers.css',
			],
		],
		'tpgb-animation-slideIn' => [
			'css' => [
				$tpgb_free .'assets/css/extra/animate-slideIn.css',
			],
		],
		'tpgb-animation-slideOut' => [
			'css' => [
				$tpgb_free .'assets/css/extra/animate-slideOut.css',
			],
		],
		'tpgb-animation-zoomIn' => [
			'css' => [
				$tpgb_free .'assets/css/extra/animate-zoomIn.css',
			],
		],
		'tpgb-animation-zoomOut' => [
			'css' => [
				$tpgb_free .'assets/css/extra/animate-zoomOut.css',
			],
		],
		'tpgb-draw-svg' => [
			'js' => [
				$tpgb_free . 'assets/js/extra/vivus.min.js',
				$tpgb_free . 'assets/js/main/draw-svg/tpgb-draw-svg.min.js',
			],
		],
		'tpgb-plus-hover-effect' => [
			'css' => [
				$tpgb_free . 'assets/css/main/plus-extras/plus-hover-effect.css',
			],
		],
		'tpgb-fancy-box' => [
			'css' => [
				$tpgb_free .'assets/css/extra/jquery.fancybox.min.css',
			],
			'js' => [
				$tpgb_free . 'assets/js/extra/jquery.fancybox.min.js',
			],
		],
		'tpgb-fancy-custom' => [
			'js' => [
				$tpgb_free . 'assets/js/main/fancy-box/tpgb-fancy-box.min.js',
			],
		],
		'tpgb-row-column-link' => [
			'js' => [
				$tpgb_free . 'assets/js/main/tp-row/tpgb-link.min.js'
			],
		],
		'tpgb_grid_layout' => [
			'js' => [
				$tpgb_free . 'assets/js/extra/isotope.pkgd.min.js',
				$tpgb_free . 'assets/js/main/post-listing/post-masonry.min.js',
			],
		],
	];
	
	if(has_filter('tpgb_blocks_register')) {
		$load_blocks_css_js = apply_filters('tpgb_blocks_register', $load_blocks_css_js);
	}
	
	return $load_blocks_css_js;
}


Class Tpgb_Library {
	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */	
	private static $instance = null;
	
	public $tpgb_registered_blocks;
	public $plus_template_blocks;
	
	public $transient_blocks = [];

	public $post_assets_object = [];
	public $block_ids = [];
	
	public $plus_uid = null;
	public $requires_update;
	public $tpgb_cache = null;
	public $tpgb_delay_perf = null;
	public $tpgb_defer_perf = null;

	private static $blocks_list = [];
	private static $blocks_render = [];

	private static $tpgb_post_type = '';
	private static $tpgb_post_id = '';
	public $dependency = false;
	public $localize = '';
	public $learn_view_item = false;
	
    /**
     *  Return array of registered elements.
     *
     * @todo filter output
     */	 
    public function get_registered_blocks(){
        return $this->tpgb_registered_blocks = tpgb_registered_blocks();
    }

	/*
	 * Get Caching System Load TPAG
	 * @since 1.4.0
	 */
	public function get_caching_option(){
		if($this->tpgb_cache != null ){
			return $this->tpgb_cache;
		}
		$caching_opt = get_option( 'tpgb_performance_cache' );
		
		if( !empty($caching_opt) && $caching_opt=='separate' ){
			$this->tpgb_cache = 'separate';
			return 'separate';
		}else{
			$this->tpgb_cache = false;
			return false;
		}
	}

	/*
	 * Get Delay Css Js Best Performance
	 * @since 2.0.0
	 */
	public function get_delay_css_js(){
		if($this->tpgb_delay_perf != null ){
			return $this->tpgb_delay_perf;
		}
		$delay_opt = get_option( 'tpgb_delay_css_js' );
		if( isset($delay_opt) && $delay_opt=='true'){
			$this->tpgb_delay_perf = true;
			return true;
		}else{
			$this->tpgb_delay_perf = false;
			return false;
		}
	}

	/*
	 * Get Defer Css Js Best Performance
	 * @since 2.0.0
	 */
	public function get_defer_css_js(){
		if($this->tpgb_defer_perf != null ){
			return $this->tpgb_defer_perf;
		}
		$defer_opt = get_option( 'tpgb_defer_css_js' );
		if( isset($defer_opt) && $defer_opt=='true' ){
			$this->tpgb_defer_perf = true;
			return true;
		}else{
			$this->tpgb_defer_perf = false;
			return false;
		}
	}

    /**
     * Return saved settings
     *
     * @since 1.1.1
     */
    public function get_plus_block_settings($block = null){
		
		$replace = [
			TPGB_CATEGORY.'/tp-accordion' => TPGB_CATEGORY.'/tp-accordion',
			TPGB_CATEGORY.'/tp-accordion-inner' => TPGB_CATEGORY.'/tp-accordion-inner',
			TPGB_CATEGORY.'/tp-blockquote' => TPGB_CATEGORY.'/tp-blockquote',
			TPGB_CATEGORY.'/tp-breadcrumbs' => TPGB_CATEGORY.'/tp-breadcrumbs',
			TPGB_CATEGORY.'/tp-button' => TPGB_CATEGORY.'/tp-button',
			TPGB_CATEGORY.'/tp-column' => TPGB_CATEGORY.'/tp-column',
			TPGB_CATEGORY.'/tp-code-highlighter' => TPGB_CATEGORY.'/tp-code-highlighter',
			TPGB_CATEGORY.'/tp-countdown' => TPGB_CATEGORY.'/tp-countdown',
			TPGB_CATEGORY.'/tp-creative-image' => TPGB_CATEGORY.'/tp-creative-image',
			TPGB_CATEGORY.'/tp-data-table' => TPGB_CATEGORY.'/tp-data-table',
			TPGB_CATEGORY.'/tp-dark-mode' => TPGB_CATEGORY.'/tp-dark-mode',
			TPGB_CATEGORY.'/tp-draw-svg' => TPGB_CATEGORY.'/tp-draw-svg',
			TPGB_CATEGORY.'/tp-empty-space' => TPGB_CATEGORY.'/tp-empty-space',
			TPGB_CATEGORY.'/tp-external-form-styler' => TPGB_CATEGORY.'/tp-external-form-styler',
			TPGB_CATEGORY.'/tp-flipbox' => TPGB_CATEGORY.'/tp-flipbox',
			TPGB_CATEGORY.'/tp-google-map' => TPGB_CATEGORY.'/tp-google-map',
			TPGB_CATEGORY.'/tp-heading-title' => TPGB_CATEGORY.'/tp-heading-title',
			TPGB_CATEGORY.'/tp-hovercard' => TPGB_CATEGORY.'/tp-hovercard',
			TPGB_CATEGORY.'/tp-infobox' => TPGB_CATEGORY.'/tp-infobox',
			TPGB_CATEGORY.'/tp-interactive-circle-info' => TPGB_CATEGORY.'/tp-interactive-circle-info',
			TPGB_CATEGORY.'/tp-messagebox' => TPGB_CATEGORY.'/tp-messagebox',
			TPGB_CATEGORY.'/tp-number-counter' => TPGB_CATEGORY.'/tp-number-counter',
			TPGB_CATEGORY.'/tp-pricing-list' => TPGB_CATEGORY.'/tp-pricing-list',
			TPGB_CATEGORY.'/tp-pricing-table' => TPGB_CATEGORY.'/tp-pricing-table',
			TPGB_CATEGORY.'/tp-pro-paragraph' => TPGB_CATEGORY.'/tp-pro-paragraph',
			TPGB_CATEGORY.'/tp-progress-bar' => TPGB_CATEGORY.'/tp-progress-bar',
			TPGB_CATEGORY.'/tp-progress-tracker' => TPGB_CATEGORY.'/tp-progress-tracker',
			TPGB_CATEGORY.'/tp-row' => TPGB_CATEGORY.'/tp-row',
			TPGB_CATEGORY.'/tp-search-bar' => TPGB_CATEGORY.'/tp-search-bar',
			TPGB_CATEGORY.'/tp-stylist-list' => TPGB_CATEGORY.'/tp-stylist-list',
			TPGB_CATEGORY.'/tp-social-icons' => TPGB_CATEGORY.'/tp-social-icons',
			TPGB_CATEGORY.'/tp-social-feed' => TPGB_CATEGORY.'/tp-social-feed',
			TPGB_CATEGORY.'/tp-social-reviews' => TPGB_CATEGORY.'/tp-social-reviews',
			TPGB_CATEGORY.'/tp-tabs-tours' => TPGB_CATEGORY.'/tp-tabs-tours',
			TPGB_CATEGORY.'/tp-tab-item' => TPGB_CATEGORY.'/tp-tab-item',
			TPGB_CATEGORY.'/tp-testimonials' => TPGB_CATEGORY.'/tp-testimonials',
			TPGB_CATEGORY.'/tp-video' => TPGB_CATEGORY.'/tp-video',
			TPGB_CATEGORY.'/tp-post-title' => TPGB_CATEGORY.'/tp-post-title',
			TPGB_CATEGORY.'/tp-post-content' => TPGB_CATEGORY.'/tp-post-content',
			TPGB_CATEGORY.'/tp-post-image' => TPGB_CATEGORY.'/tp-post-image',
			TPGB_CATEGORY.'/tp-post-author' => TPGB_CATEGORY.'/tp-post-author',
			TPGB_CATEGORY.'/tp-post-listing' => TPGB_CATEGORY.'/tp-post-listing',
			TPGB_CATEGORY.'/tp-post-meta' => TPGB_CATEGORY.'/tp-post-meta',
			TPGB_CATEGORY.'/tp-post-comment' => TPGB_CATEGORY.'/tp-post-comment',
			TPGB_CATEGORY.'/tp-site-logo' => TPGB_CATEGORY.'/tp-site-logo',
			TPGB_CATEGORY.'/tp-smooth-scroll' => TPGB_CATEGORY.'/tp-smooth-scroll',
			TPGB_CATEGORY.'/tp-social-embed' => TPGB_CATEGORY.'/tp-social-embed',
			TPGB_CATEGORY.'/tp-container' => TPGB_CATEGORY.'/tp-container',
			TPGB_CATEGORY.'/tp-container-inner' => TPGB_CATEGORY.'/tp-container-inner',
		];
		
		if(has_filter('tpgb_blocks_register_render')) {
			$replace = apply_filters('tpgb_blocks_register_render', $replace);
		}
		
		$blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();
		
		//Plus Extras Options Array
		$all_tpgb_blocks = $this->tpgb_registered_blocks;
		$plus_extras = array_filter($all_tpgb_blocks, function ($key) {
			return strpos($key, 'tpgb/') === 0 ? '' : $key;
		}, ARRAY_FILTER_USE_KEY);
		
		$plus_extras =array_keys($plus_extras);
		if(has_filter('tpgb_exrta_conditions_blocks_register')) {
			$plus_extras = apply_filters('tpgb_exrta_conditions_blocks_register', $plus_extras);
		}
		$plus_extras =array_unique($plus_extras);

		if(empty($blocks)){
			$blocks = array_keys($replace);
		}else{
			$blocks = array_keys($blocks);
		}

		$blocks = array_map(function ($val) use ($replace) {
			return (array_key_exists($val, $replace) ? $replace[$val] : '');
        }, $blocks);

		$blocks =array_merge( $plus_extras, $blocks );
		
		$load_blocks = (isset($block) ? (isset($blocks[$block]) ? $blocks[$block] : 0) : array_filter($blocks));

		asort($load_blocks);

		return $load_blocks;
	}
	
	/**
     * Check if block editor preview mode or not 
	 * @since 1.0.0
     */
    public function is_preview_mode() {
        require_once(ABSPATH . 'wp-admin/includes/screen.php');
        if (! is_null ( get_current_screen() ) && get_current_screen()->is_block_editor()){
            return true;
        }
		
        return false;
    }
	
	/**
     * Check if cache files exists
     *
     * @since 1.0.0
     */
    public function check_cache_files($post_type = null, $post_id = null, $extension = 'both', $preload = false){
		$filename = '';
		if(!empty($preload)){
			$filename = 'preload-';
		}
        $css_url = TPGB_ASSET_PATH . DIRECTORY_SEPARATOR . ($post_type ? 'theplus-'.$filename. $post_type : 'theplus') . ($post_id ? '-' . $post_id : '') . '.min.css';
        $js_url = TPGB_ASSET_PATH . DIRECTORY_SEPARATOR . ($post_type ? 'theplus-'.$filename . $post_type : 'theplus') . ($post_id ? '-' . $post_id : '') . '.min.js';

		if($extension == 'css' && is_readable( $this->secure_path_url($css_url) )){
			return true;
		}else if($extension == 'js' && is_readable( $this->secure_path_url($js_url) )){
			return true;
		}else if ($extension == 'both' && is_readable( $this->secure_path_url($css_url) ) && is_readable( $this->secure_path_url($js_url) )) {
			return true;
        }
        return false;
    }
	
	/**
     * Check if cache files exists
     *
     * @since 1.3.1
     */
    public function check_css_js_cache_files($post_type = null, $post_id = null, $type = 'css', $preload = false){
		if( empty( $type) ){
			return false;
		}
		$filename = '';
		if(!empty($preload)){
			$filename = 'preload-';
		}
		if($type == 'css'){
			$css_url = TPGB_ASSET_PATH . DIRECTORY_SEPARATOR . ($post_type ? 'theplus-'.$filename. $post_type : 'theplus') . (isset($post_id) ? '-' . $post_id : '') . '.min.css';
			if ( is_readable( $this->secure_path_url($css_url) ) ) {
				return true;
			}
		}else if($type == 'js'){
			$js_url = TPGB_ASSET_PATH . DIRECTORY_SEPARATOR . ($post_type ? 'theplus-'.$filename . $post_type : 'theplus') . (isset($post_id) ? '-' . $post_id : '') . '.min.js';
			if ( is_readable( $this->secure_path_url($js_url) ) ) {
				return true;
			}
		}
		return false;
	}

	/**
     * Generate Separate style and scripts.
     *
     * @since 1.4.0
     */
    public function load_separate_file($elements, $file_name = null) {
		
        if (empty($elements)) {
            return;
        }
		
        // default load js and css
        $js_url = array();
        $css_url = array(
			TPGB_PATH . DIRECTORY_SEPARATOR . "/assets/css/main/general/tpgb-common.css",
		);
		
        // collect library scripts & styles
        $js_url = array_merge($js_url, $this->plus_dependency_widgets($elements, 'js'));
        $css_url = array_merge($css_url, $this->plus_dependency_widgets($elements, 'css'));
		
		return ['css' => $css_url, 'js' => $js_url ];
    }

    /**
     * Generate scripts and combine minify.
     *
     * @since 1.0.0
     */
    public function plus_generate_scripts($elements, $file_name = null, $extension = ['css', 'js'], $common = true){

        if (empty($elements)) {
            return;
        }
		
        if (!file_exists(TPGB_ASSET_PATH)) {
            wp_mkdir_p(TPGB_ASSET_PATH);
        }

        // default load js and css
        $js_url = array();
		if($common === false){
			$css_url = array();
		}else{
			$css_url = array(
				TPGB_PATH . DIRECTORY_SEPARATOR . "/assets/css/main/general/tpgb-common.css",
			);
		}
       
		
        // collect library scripts & styles And Merge
		if( in_array('js', $extension) ){
			$js_url = array_merge($js_url, $this->plus_dependency_widgets($elements, 'js'));
			if( !empty($js_url) ){
				$this->plus_merge_files($js_url, ($file_name ? $file_name : 'theplus') . '.min.js','js');
			}
		}
        
		if( in_array('css', $extension) ){
        	$css_url = array_merge($css_url, $this->plus_dependency_widgets($elements, 'css'));
			if( !empty($css_url) ){
				$this->plus_merge_files($css_url, ($file_name ? $file_name : 'theplus') . '.min.css','css');
			}
		}
    }
	
	
	/**
     * Widgets dependency for modules
     *
     * @since 1.0.0
     */
    public function plus_dependency_widgets(array $elements, $type)
    {
        $paths = [];

        foreach ($elements as $element) {
            if (isset($this->tpgb_registered_blocks[$element])) {
                if (!empty($this->tpgb_registered_blocks[$element][$type])) {
                    foreach ($this->tpgb_registered_blocks[$element][$type] as $path) {
                        $paths[] = $path;
                    }
                }
            }
        }
        return array_unique($paths);
    }
	
	/**
     * Merge all Files Load
     *
     * @since 1.0.0
     */
    public function plus_merge_files($paths = array(), $file = 'theplus-style.min.css',$type='') {
        $output = '';

        if (!empty($paths)) {
            foreach ($paths as $path) {
                $output .= file_get_contents($this->secure_path_url($path));
            }
        }
		if(!empty($type) && $type=='css'){
			$output = preg_replace( '/\s+/', ' ', $output );
			$output = preg_replace( '/\/\*[^\!](.*?)\*\//', '', $output );
			$output = preg_replace( '/(,|:|;|\{|}) /', '$1', $output );
			$output = preg_replace( '/ (,|;|\{|})/', '$1', $output );
			$output = preg_replace( '/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $output );
		}
		if( !empty( $output ) ){
			return file_put_contents( $this->secure_path_url(TPGB_ASSET_PATH . DIRECTORY_SEPARATOR . $file), $output);
		}
		return false;
    }
	
	public function plus_template_load_parse_blocks($blocks){
		
		$parse_content_template =[];
		if(!$this->is_preview_mode() && !empty($blocks) && isset($blocks) && !empty($this->plus_template_blocks)){
			if($this->plus_template_blocks){
				$template_post_id = array_unique($this->plus_template_blocks);
				
				foreach($template_post_id as $id){
					$block_post = get_post( $id );
					$parse_content_template[] = parse_blocks( $block_post->post_content );
				}
			}
			$blocks = array_merge($parse_content_template, $blocks);
			
			return $blocks;
		}else{
			return $blocks; 
		}
	}
	
	public function tpgb_get_load_template_id(){
		return $this->plus_template_blocks;
	}
	
	/*
	 * Template Post Do block
	 * @since 1.1.2
	 */
	public function plus_do_block($post_id=''){
		if(!$this->is_preview_mode() && !empty($post_id) && isset($post_id)){
			$block_post = get_post( $post_id );
			if ( ! is_wp_error( $block_post ) ) {
				$this->plus_template_blocks[] = $post_id;
				$content =  (isset($block_post->post_content)) ? $block_post->post_content : '';
				if(isset($content)){
					return do_blocks( $content );
				}else{
					return;
				}
			}
		}else{
			return; 
		}
	}
	
	/*
	 * Save Load Template Post Id And Dynamic Enqueue Css
	 * @since 2.0.0
	 */
	public function save_load_templates( $template_post_ids = [], $in_footer = false ){
		$optionName = 'tpgb-load-templates-list';
		$get_opt = get_option($optionName);

		if( empty(self::$tpgb_post_type) ){
			$this->get_post_type_post_id();
		}

		if( !empty( $in_footer) ){
			if( $get_opt === false ){
				add_option($optionName, [ self::$tpgb_post_id => $template_post_ids ] );
			}else if( $get_opt !== false && !empty($get_opt) ){
				if( !isset( $get_opt[ self::$tpgb_post_id ] ) ){
					$get_opt[ self::$tpgb_post_id ] = $template_post_ids;
					update_option($optionName, $get_opt, false );
				}else{
					if($get_opt[ self::$tpgb_post_id ] != $template_post_ids){
						$get_opt[ self::$tpgb_post_id ] = $template_post_ids;
						update_option($optionName, $get_opt , false );
					}
				}
			}
		}else if(!empty($get_opt) && isset($get_opt[self::$tpgb_post_id]) && !empty($get_opt[self::$tpgb_post_id]) ){
			$template_post_ids = $get_opt[ self::$tpgb_post_id ];
		}

		if( !empty($template_post_ids) ){
			$post_type = (is_singular() ? 'post' : 'term');
			foreach($template_post_ids as $post_id){
				$this->header_init_css_js($post_type, $post_id );
				$this->enqueue_dynamic_css_post_id( $post_id, $in_footer );
			}
		}
	}
	
	/*
	 * Dynamic Enqueue Css by Post Id
	 * @since 2.0.0
	 */
	public function enqueue_dynamic_css_post_id( $post_id = '', $in_footer = false){
		if( !empty($post_id) ){
			$upload_dir			= wp_get_upload_dir();
			$upload_base_dir 	= trailingslashit($upload_dir['basedir']);
			$css_path			= $upload_base_dir . "theplus_gutenberg/plus-css-{$post_id}.css";
			$preview_css_path	= $upload_base_dir . "theplus_gutenberg/plus-preview-{$post_id}.css";
			
			$plus_version=get_post_meta( $post_id, '_block_css', true );
			if(empty($plus_version)){
				$plus_version = TPGB_VERSION;
			}
			$plus_css=get_post_meta( $post_id, '_tpgb_css', true );
			if( !empty($plus_css) && isset($plus_css['font_link']) && !empty($plus_css['font_link']) && class_exists('Tp_Core_Init_Blocks')){
				$tp_core = Tp_Core_Init_Blocks::get_instance();
				$tp_core->tpgb_load_google_fonts($post_id, $plus_css['font_link']);
			}
			if( isset($_GET['preview']) && $_GET['preview'] == true && file_exists($preview_css_path)){
				$css_file_url = trailingslashit($upload_dir['baseurl']);
				$css_url     = $css_file_url . "theplus_gutenberg/plus-preview-{$post_id}.css";
				if (!$this->is_editor_screen()) {
					wp_enqueue_style("plus-preview-{$post_id}", $this->pathurl_security($css_url), false, $plus_version.time());
				}
			}else if (file_exists($css_path)) {
				$deps = ['tpgb-plus-block-front-css'];
				if($in_footer === true && $this->get_caching_option() == 'separate' ){
					$deps = [];
				}
				$css_file_url = trailingslashit($upload_dir['baseurl']);
				$css_url     = $css_file_url . "theplus_gutenberg/plus-css-{$post_id}.css";
				wp_enqueue_style("plus-post-{$post_id}", $this->pathurl_security($css_url), $deps, $plus_version);
			}else if(!file_exists($css_path) && class_exists('Tp_Core_Init_Blocks')){
				$tp_core = Tp_Core_Init_Blocks::get_instance();
				$tp_core->make_block_css_by_post_id($post_id);
			}
		}
	}

	/**
     * Check generate Style and Scripts
     *
     * @since 2.0.0
     */
	public function check_generate_script(){
		if ($this->is_background_running()) {
            return false;
        }
		
        if ($this->plus_uid === null) {
            return false;
        }

        if ($this->is_preview_mode()) {
            return false;
        }
		
        if (!$this->requires_update) {
            return false;
        }
		
		if(get_option('tpgb_save_updated_at') === false){
			update_option('tpgb_save_updated_at', strtotime('now'),false);
		}
		
		if (get_option('tpgb_save_updated_at') == get_option($this->plus_uid . '_updated_at')) {
            return false;
        }

		return true;
	}

	/**
     * Get Post Type and Post ID
     *
     * @since 2.0.0
     */
	public function get_post_type_post_id(){

		if(!empty(self::$tpgb_post_type) && self::$tpgb_post_id != '' ){
			return true;
		}

		global $wp_query;
		if( is_home() || is_singular() || is_archive() || is_search() || (isset( $wp_query ) && (bool) $wp_query->is_posts_page) || is_404() ){
			$queried_obj = get_queried_object_id();
			if(isset( $wp_query ) && isset($wp_query->is_post_type_archive) && !empty($wp_query->is_post_type_archive)){
				$queried_obj = $wp_query->query['post_type'];
			}
			if(is_search()){
				$queried_obj = 'search';
			}
			if(is_404()){
				$queried_obj = '404';
			}
			$post_type = (is_singular() ? 'post' : 'term');
			
			self::$tpgb_post_type = $post_type;
			self::$tpgb_post_id = $queried_obj;
			
			return true;
		}

		return false;
	}

	/*
	 * Frontend load css js by Cache Manager
	 * @since 2.0.0
	 */
	public function enqueue_css_js( $elements = [], $in_footer = false, $load_depend = ['jquery']){

		$tpgb_path = TPGB_PATH . DIRECTORY_SEPARATOR;
		$tpgb_url = TPGB_URL;
		$plus_version = get_post_meta( self::$tpgb_post_id, '_block_css', true );
		if( empty($plus_version) ){
			$plus_version = time();
		}

		$scripts_loader = array();

		//Google Map
		$GoogleMap_Enable = Tp_Blocks_Helper::get_extra_option('gmap_api_switch');
		if(!empty($GoogleMap_Enable) && (($GoogleMap_Enable=='enable' && has_block( 'tpgb/tp-google-map' )) || ($GoogleMap_Enable=='enable' && class_exists( 'GeneratePress_Elements_Helper' )) || (!empty($elements) && in_array('tpgb/tp-google-map',$elements)) )){
			$GoogleMap_Api = Tp_Blocks_Helper::get_extra_option('googlemap_api');
			if(!empty($GoogleMap_Api) && !$this->get_delay_css_js()){
				$depend = ['jquery'];
				if($this->get_caching_option() == 'separate'){
					array_push($depend,'theplus-tpgb-google-map');
				}else{
					array_push($depend, 'tpgb-plus-block-front-js');
				}
				wp_enqueue_script( 'gmaps-js','//maps.googleapis.com/maps/api/js?key='.esc_attr($GoogleMap_Api).'&sensor=false&callback=tpgbInitMap', $depend, null, false, true);
			}else if(!empty($GoogleMap_Api) && $this->get_delay_css_js()){
				$scripts_loader['google_api'] = $GoogleMap_Api;
			}
		}
		
		$load_localize ='jquery';
		
		//caching type load
		if( $this->get_caching_option() == 'separate' && !empty($elements) && $in_footer == false ){
			$separate_path = $this->load_separate_file($elements);
			
			if(isset($separate_path['css']) && !empty($separate_path['css'])){
				$iji = 1;
				$total_eng = count($separate_path['css']);
				foreach( $separate_path['css'] as $key => $path ){
					if(is_readable($this->secure_path_url($path))){
						$css_sep_url = str_replace( $tpgb_path, $tpgb_url, $path);
						if(defined('TPGBP_VERSION') && defined('TPGBP_URL')){
							$css_sep_url = str_replace( TPGBP_PATH . DIRECTORY_SEPARATOR, TPGBP_URL, $css_sep_url);
						}
						$css_file_key = basename($css_sep_url, ".css");
						$css_file_key = basename($css_file_key, ".min");
						$lastFolder = basename(dirname($css_sep_url));
						$enq_name = 'theplus-'.$css_file_key.'-'.$lastFolder;
						if($iji === $total_eng){
							$enq_name = 'tpgb-plus-block-front-css';
						}
						wp_enqueue_style( $enq_name , $this->pathurl_security($css_sep_url), false,$plus_version);
						$iji++;
					}
				}
			}

			if(isset($separate_path['js']) && !empty($separate_path['js'])){
				$iji = 0;
				foreach( $separate_path['js'] as $key => $path ){
					if(is_readable($this->secure_path_url($path))){
						$js_sep_url = str_replace( $tpgb_path, $tpgb_url, $path);
						if(defined('TPGBP_VERSION') && defined('TPGBP_URL')){
							$js_sep_url = str_replace( TPGBP_PATH . DIRECTORY_SEPARATOR, TPGBP_URL, $js_sep_url);
						}
						$js_file_key = basename($js_sep_url, ".js");
						$js_file_key = basename($js_file_key, ".min");
						if($iji === 0){
							$load_localize = 'theplus-'.$js_file_key;
						}
						wp_enqueue_script( 'theplus-'.$js_file_key, $this->pathurl_security($js_sep_url), $load_depend, $plus_version, true);
						$iji++;
					}
				}
			}
		}else if($this->get_caching_option() == false){
			if ( $this->check_css_js_cache_files( self::$tpgb_post_type, self::$tpgb_post_id, 'css' ) && ($this->get_caching_option() == false && $in_footer == false )) {
				$enq = 'tpgb-plus-block-front-css';
				//LearnPress
				if($this->learn_view_item===true && $this->dependency === true){
					$enq = 'tpgb-plus-'.self::$tpgb_post_type;
				}
				$css_file = TPGB_ASSET_URL . '/theplus-' . self::$tpgb_post_type . '-' . self::$tpgb_post_id . '.min.css';
				wp_enqueue_style( $enq, $this->pathurl_security($css_file), false, $plus_version );
			}

			$load_localize = 'tpgb-purge-js';
			if( $this->check_css_js_cache_files(self::$tpgb_post_type, self::$tpgb_post_id, 'js') ){
				$js_file = TPGB_ASSET_URL . '/theplus-' . self::$tpgb_post_type . '-' . self::$tpgb_post_id . '.min.js';

				wp_enqueue_script('tpgb-plus-block-front-js', $this->pathurl_security($js_file), ['jquery'], $plus_version, true);
				$load_localize = 'tpgb-plus-block-front-js';
			}
		}

		//First time load in footer
		if ( $this->get_caching_option() == 'separate' && !empty($in_footer) && class_exists( 'Tp_Core_Init_Blocks' ) ) {
			$load_enqueue = Tp_Core_Init_Blocks::get_instance();
			
			if ( !empty($load_enqueue) && is_callable( array( $load_enqueue, 'tpgb_compatibility_plugins' ) ) ) {
				$load_enqueue->tpgb_compatibility_plugins();
			}
		}
		
		//delay js like fontawesome,google map, lottiefiles
		if(has_filter('tpgb_onloader_scripts')) {
			$scripts_loader = apply_filters('tpgb_onloader_scripts', $scripts_loader, $elements, $plus_version);
		}

		if($this->get_delay_css_js() && !empty($scripts_loader)){
			wp_enqueue_script( 'tpgb-delay', TPGB_URL.'/assets/js/main/general/tpgb-loader.min.js', [], '', true );
			wp_localize_script(
				'tpgb-delay', 'tpgbLoadScripts', $scripts_loader
			);
		}

		//Load Templates Dynamic css
		$template_post_ids = [];
		if( !empty($in_footer) && !empty($this->plus_template_blocks) ){
			$template_post_ids = array_unique($this->plus_template_blocks);
		}
		$this->save_load_templates( $template_post_ids, $in_footer );

		if( ($this->get_caching_option() == 'separate' && $in_footer == false) || $this->get_caching_option() == false ){
			$this->load_localize_data( $load_localize );
		}
	}

	public function load_localize_data( $load_localize = ''){
		wp_localize_script(
			$load_localize, 'tpgb_config', array(
				'ajax_url' => esc_url( admin_url('admin-ajax.php') ),
				'tpgb_nonce' => wp_create_nonce("tpgb-addons"),
			)
		);
	}

	public function check_templates_ids( $post_id = ''){
		if(!empty($post_id)){
			$optionName = 'tpgb-load-templates-list';
			$get_opt = get_option($optionName);
			
			if( empty(self::$tpgb_post_type) ){
				$this->get_post_type_post_id();
			}
			
			if(!empty($get_opt) && isset($get_opt[self::$tpgb_post_id]) && $post_id!=self::$tpgb_post_id ){
				if( !in_array($post_id, $get_opt[ self::$tpgb_post_id ]) ){
					$get_opt[ self::$tpgb_post_id ] = array_merge($get_opt[ self::$tpgb_post_id ], [$post_id]);
					$this->requires_update = $this->requires_update();
					update_option($optionName, $get_opt, false );
					update_option('tpgb_save_updated_at', strtotime('now'),false);
				}
			}else if( (empty($get_opt) || !isset($get_opt[self::$tpgb_post_id])) &&  $post_id!=self::$tpgb_post_id ){
				$get_opt[ self::$tpgb_post_id ] = [ $post_id ];
				$this->requires_update = $this->requires_update();
				update_option($optionName, $get_opt, false );
				update_option('tpgb_save_updated_at', strtotime('now'),false);
			}
		}
	}

	public function header_init_css_js($post_type = null, $post_id = null){
		
		if(!empty($post_id) && !in_array($post_id, $this->plus_template_blocks)){
			$this->check_templates_ids($post_id);
			$this->plus_template_blocks[] = $post_id;
			if(isset(self::$tpgb_post_id) && !empty($this->post_assets_object[ self::$tpgb_post_id ])){
				$this->dependency = true;
			}
		}
		
		if($this->check_generate_script()===false){
			return;
		}
		
		/* if($this->get_caching_option() != false){
			return;
		} */
		
		if(!$this->get_post_type_post_id()){
			return;
		}
		$first_time = false;
		if(is_object($post_type) && empty($post_id)){
			
			if ( ! self::$tpgb_post_id ) {
				return;
			}
			$first_time = true;
			$post_type = self::$tpgb_post_type;
			$post_id = self::$tpgb_post_id;
		}

		if ( ! isset( $this->post_assets_object[ $post_id ] ) && class_exists('Tpgb_Get_Blocks') ) {
			
			$load_enqueue = tpgb_get_post_assets( $post_type, $post_id );
			if(!empty($load_enqueue)){
				if($this->get_caching_option() == 'separate' && !empty($first_time)){
					$this->enqueue_global_css();
				}
				if(isset($load_enqueue->transient_blocks) && !empty($load_enqueue->transient_blocks)){
					if(has_filter( 'tpgb_load_blocks_css_js')){
						$load_extra = apply_filters( 'tpgb_load_blocks_css_js', [], $load_enqueue->transient_blocks );
					}
					$load_localize = $load_enqueue->enqueue_scripts( $this->dependency );
					$this->dependency = true;
					if($this->get_caching_option() == 'separate'){
						if(!empty($load_localize) && empty($this->localize)){
							$this->localize = $load_localize;
							$this->load_localize_data( $load_localize );
						}
						if ( class_exists( 'Tp_Core_Init_Blocks' ) ) {
							$load_init = Tp_Core_Init_Blocks::get_instance();
							if ( !empty($load_init) && is_callable( array( $load_init, 'enqueue_post_css' ) ) ) {
								$load_init->enqueue_post_css( $post_id, [] );
							}
						}
					}
					
				}
				//template load css
				if ( isset($load_enqueue->templates_ids) && ! empty( $load_enqueue->templates_ids ) && is_array( $load_enqueue->templates_ids ) ) {
					$res_id = array_unique( $load_enqueue->templates_ids );
					$post_type = (is_singular() ? 'post' : 'term');
					foreach ( $res_id as $value ) {
						$this->header_init_css_js( $post_type, $value );
						$this->plus_template_blocks[] = $value;
						$this->enqueue_dynamic_css_post_id( $value );
					}
				}
				$block_lists = array_unique($load_enqueue->transient_blocks);
				$this->post_assets_object[ $post_id ] = $block_lists;
			}
			
		}
		return $this->post_assets_object[ $post_id ];
	}

	/*
	 * Get Blocks Elements
	 * @since 2.0.0
	 */
	public function get_blocks_elements( $block_lists = []){
		$elements = [];
		if(!empty($block_lists)){
			$replace = array();
			$elements = array_map(function ($val) use ($replace) {
				return (array_key_exists($val, $replace) ? $replace[$val] : $val);
			}, $block_lists);
			$elements = array_intersect(array_keys($this->tpgb_registered_blocks), $elements);
	
			$elements = array_unique($elements);
			sort($elements);
		}
		return $elements;
	}

	
	/**
     * Generate single post scripts
     *
     * @since 2.0.0
     */
    public function generate_scripts_frontend() {
		
		if($this->check_generate_script()===false){
			return;
		}
		
		if(has_filter('tpgb_extra_load_css_js')) {
			$this->transient_blocks = apply_filters('tpgb_extra_load_css_js', $this->transient_blocks );
		}
		
		if(!empty($this->transient_blocks)){
			$this->transient_blocks = array_unique($this->transient_blocks);
		}
		
		$elements = $this->get_blocks_elements( $this->transient_blocks );
       	
		if ( $this->get_post_type_post_id() ) {
			
			//LearnPress
			if(class_exists('LP_Global') && $this->learn_view_item==false){
				$course_item = LP_Global::course_item();
				if(!empty($course_item)){
					$this->learn_view_item = true;
					self::$tpgb_post_id .= '-'.$course_item->get_id();
					$this->plus_uid .= '-'.$course_item->get_id();
				}
			}

			$this->remove_files_unlink(self::$tpgb_post_type, self::$tpgb_post_id);

			// if no cache files, generate new
			if ( !$this->check_cache_files( self::$tpgb_post_type, self::$tpgb_post_id ) && $this->get_caching_option() == false ) {
				$this->plus_generate_scripts( $elements, 'theplus-' . self::$tpgb_post_type . '-' . self::$tpgb_post_id );
			}

			//First time load footer css and js
			$this->enqueue_css_js( $elements, true );

			update_option( $this->plus_uid . '_blocks', $elements, false );
			update_option( $this->plus_uid . '_updated_at', get_option('tpgb_save_updated_at'), false );
		}
    }
	
	/**
	 * Enqueue FSE Theme
	 *
	 * @since 2.0.3
	 */
	public function assets_fse_theme() {
		global $_wp_current_template_content;
		if ( $_wp_current_template_content ) {
			$dynamic_id	= get_queried_object_id();
			$blocks = parse_blocks( $_wp_current_template_content );
			if(!empty($blocks) && class_exists('Tpgb_Get_Blocks')){
				$post_obj = new Tpgb_Get_Blocks( 'post', $dynamic_id );
				$templates = $post_obj->block_reference_id($blocks);
				$post_obj->tpgb_post_block_css('post', $dynamic_id);
				if(isset($post_obj->transient_blocks) && !empty($post_obj->transient_blocks)){
					$post_obj->enqueue_scripts( $this->dependency );
				}
				if(!empty($templates)){
					foreach($templates as $temp_id){
						$this->header_init_css_js('post', $temp_id );
					}
				}
			}
		}
	}

	/*
	 * Frontend Load Plus Styles and Scripts
	 * @since 2.0.0
	 */
	public function enqueue_frontend_load() {

		if ( $this->get_post_type_post_id() ) {

			if (!$this->is_preview_mode()) {
				
				if(class_exists('LP_Global') && $this->learn_view_item==false){
					$course_item = LP_Global::course_item();
					if(!empty($course_item)){
						$this->learn_view_item = true;
						self::$tpgb_post_id .= '-'.$course_item->get_id();
						$this->plus_uid .= '-'.$course_item->get_id();
						if( $this->requires_update ){
							$this->requires_update = $this->requires_update();
						}
					}
				}
				
				$elements = [];
				if(!$this->requires_update){
					$elements = get_option($this->plus_uid . '_blocks');
				
					if($this->get_caching_option() == 'separate'){ //separate file			
						$this->remove_files_unlink(self::$tpgb_post_type, self::$tpgb_post_id);
					}else if( $this->get_caching_option() == false ){ //combine
						if (!$this->check_css_js_cache_files(self::$tpgb_post_type, self::$tpgb_post_id,'css') && !$this->check_css_js_cache_files(self::$tpgb_post_type, self::$tpgb_post_id,'js') && !empty($elements)) {
							update_post_meta( self::$tpgb_post_id, '_block_css', time() );
							$this->plus_generate_scripts( $elements, 'theplus-' . self::$tpgb_post_type . '-' . self::$tpgb_post_id );
						}
					}
					// if no widget in page, return
					if (empty($elements)) {
						return;
					}
				}else if ( wp_is_block_theme() ) {
					$this->assets_fse_theme();
				}
				

				if((!$this->requires_update && $this->get_caching_option() == 'separate') || $this->get_caching_option() == false){
					$this->enqueue_global_css();
				}

				if ( is_admin_bar_showing() && $this->get_caching_option() == false ) {
					wp_enqueue_script(
						'tpgb-purge-js',
						TPGB_URL."assets/js/main/general/tpgb-purge.js",
						['jquery'],
						TPGB_VERSION,
						true
					);
				}

				if(!$this->requires_update && !empty($elements)){
					//generate css & js load
					$this->enqueue_css_js( $elements, false );
				}
			}
		}
	}
	
	/*
	 * Enqueue Global Css
	 * @since 2.0.0
	 */
	public function enqueue_global_css(){

		$plus_version=get_post_meta( self::$tpgb_post_id, '_block_css', true );
		if(empty($plus_version) && gettype(self::$tpgb_post_id)){
			$plus_version = TPGB_VERSION;
		}else if(empty($plus_version) ){
			$plus_version=time();
		}
		
		//fontawesome icon load frontend
		$fontawesome_load = Tp_Blocks_Helper::get_extra_option('fontawesome_load');
		$fontawesome_pro = Tp_Blocks_Helper::get_extra_option('fontawesome_pro_kit');
		if((empty($fontawesome_load) || $fontawesome_load=='enable' || empty($fontawesome_pro) || !defined('TPGBP_VERSION')) && $fontawesome_load!='disable'){
			wp_enqueue_style('tpgb-fontawesome', TPGB_URL.'assets/css/extra/fontawesome.min.css', array());
		}

		$check_gfontandcss_load = get_option( 'tpgb_connection_data' );

		/** Disable Global CSS Here */
		if(!empty($check_gfontandcss_load) && isset($check_gfontandcss_load['gbl_css']) && $check_gfontandcss_load['gbl_css']==='disable'){
			return;
		}
		
		$upload_dir			= wp_get_upload_dir();
		$upload_base_dir 	= trailingslashit($upload_dir['basedir']);
		$global_path = $upload_base_dir . "theplus_gutenberg/plus-global.css";
		$css_file_url = trailingslashit($upload_dir['baseurl']);
		$global_css = get_option( '_tpgb_global_css' );

		if(!file_exists($global_path)){
			if(!empty($global_css) && isset($global_css['css'])){
				$dir = $upload_base_dir . 'theplus_gutenberg/';
				$file_name = 'plus-global.css';
				global $wp_filesystem;
				if (!$wp_filesystem) {
					require_once(ABSPATH . 'wp-admin/includes/file.php');
				}

				WP_Filesystem(false, $upload_dir['basedir'], true);

				if (!$wp_filesystem->is_dir($dir)) {
					$wp_filesystem->mkdir($dir);
				}
				if( !empty($check_gfontandcss_load) && isset($check_gfontandcss_load['gfont_load']) && $check_gfontandcss_load['gfont_load']=== 'disable' ){
					if (preg_match_all('/@import[^;]+;/', $global_css['css'], $matches)) {
						if(!empty($matches) && isset($matches[0])){
							$fonts = $matches[0];
							foreach ($fonts as $key => $val) {
								$global_css['css'] = str_replace($val,"", $global_css['css']);
							}
						}
					}
				}
				$wp_filesystem->put_contents($dir . $file_name, $global_css['css']);
			}

		}
		if( !empty($global_css) && isset($global_css['font_link']) && !empty($global_css['font_link']) ){
			if ( class_exists( 'Tp_Core_Init_Blocks' ) ) {
				$Tp_Core = Tp_Core_Init_Blocks::get_instance();
				$Tp_Core->tpgb_load_google_fonts( 'global', $global_css['font_link']);
			}
		}
		if( isset($_GET['preview']) && $_GET['preview'] == true){
			$global_path = $upload_base_dir . "theplus_gutenberg/plus-global-preview.css";
			if (file_exists($global_path) && !$this->is_editor_screen()) {
				$global_url = $css_file_url . "theplus_gutenberg/plus-global-preview.css";
				wp_enqueue_style("plus-global", $this->pathurl_security($global_url), false, $plus_version);
			}else if (file_exists($upload_base_dir . "theplus_gutenberg/plus-global.css") && !$this->is_editor_screen()) {
				$global_url = $css_file_url . "theplus_gutenberg/plus-global.css";
				wp_enqueue_style("plus-global", $this->pathurl_security($global_url), false, $plus_version);
			}
		}else if (file_exists($global_path) && !$this->is_editor_screen()) {
			$global_url = $css_file_url . "theplus_gutenberg/plus-global.css";
			wp_enqueue_style("plus-global", $this->pathurl_security($global_url), false, $plus_version);
		}
	}

	private function is_editor_screen(){
		if (!empty($_GET['action']) &&  $_GET['action'] === 'wppb_editor') {
			return true;
		}
		return false;
	}
	
	/**
     * Generate secure path url
     * @since 1.0.0
     */
    public function secure_path_url($path_url){
        $path_url = str_replace(['//', '\\\\'], ['/', '\\'], $path_url);

        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path_url);
    }
	
	/**
	 * Generate secure path url
	 *
	 * @since 1.0.0
	 */
	public function pathurl_security($url) {
		return preg_replace(['/^http:/', '/^https:/', '/(?!^)\/\//'], ['', '', '/'], $url);
    }
	
	/**
		* Add menu in admin bar.
	 *
	 * Adds "Plus Clear Cache" items to the WordPress admin bar.
	 *
	 * Fired by `admin_bar_menu` filter.
	 *
	 * @since 1.0.0
	 */
	public function add_tpgb_clear_cache_admin_bar( \WP_Admin_Bar $wp_admin_bar ) {
		
		global $wp_admin_bar;

		if ( ! is_super_admin()
			 || ! is_object( $wp_admin_bar ) 
			 || ! function_exists( 'is_admin_bar_showing' ) 
			 || ! is_admin_bar_showing() ) {
			return;
		}

		if( empty(self::$tpgb_post_type) ){
			$this->get_post_type_post_id();
		}
		
		if (file_exists(TPGB_ASSET_PATH . '/theplus-' . self::$tpgb_post_type . '-' . self::$tpgb_post_id . '.min.css') || file_exists(TPGB_ASSET_PATH . '/theplus-' . self::$tpgb_post_type . '-' . self::$tpgb_post_id . '.min.js')) {
			$show_perf_bar = get_option( 'tpgb_connection_data' );
			if(!empty($show_perf_bar) && isset($show_perf_bar['assets_performance']) && $show_perf_bar['assets_performance']==='enable'){
				//Parent
				$wp_admin_bar->add_node( [
					'id'	=> 'tpda-purge-clear',
					'meta'	=> array(
						'class' => 'tpda-purge-clear',
					),
					'title' => esc_html__( 'TPAG Performance', 'tpgb' ),
				] );
				
				//Child Item
				$args = array();
				array_push($args,array(
					'id'		=>	'tpda-purge-all-pages',
					'title'		=>	esc_html__( 'Purge All Pages', 'tpgb' ),
					'href'		=> 	'#tpda-clear-gutenberg-all',
					'parent'	=>	'tpda-purge-clear',
					'meta'   	=> 	array( 'class' => 'tpda-purge-all-pages' ),
				));

				array_push($args,array(
					'id'     	=>	'tpda-purge-current-page',
					'title'		=>	esc_html__( 'Purge Current Page', 'tpgb' ),
					'href'		=>	'#tpda-clear-theplus-' . self::$tpgb_post_type . '-' . self::$tpgb_post_id,
					'parent' 	=>	'tpda-purge-clear',
					'meta'   	=>	array( 'class' => 'tpda-purge-current-page' ),
				));
				
				sort($args);
				foreach( $args as $each_arg) {
					$wp_admin_bar->add_node($each_arg);
				}
			}
			if(!defined('NEXTER_EXT')){
				//Parent
				$wp_admin_bar->add_node( [
					'id'	=> 'tpgb_edit_template',
					'meta'	=> array(
						'class' => 'tpgb_edit_template',
					),
					'title' => esc_html__( 'Edit Template', 'tpgb' ),
				] );
			}
		}
	}
	
	/**
     * Remove all Clear cache files
     *
     * @since 1.0.0
     */
    public function tpgb_smart_perf_clear_cache() {
		check_ajax_referer('tpgb-addons', 'security');

        // clear cache files
		$this->remove_dir_files(TPGB_ASSET_PATH);

		wp_send_json(true);
    }
	
	/**
     * Remove all Dynamic Style files
     *
     * @since 1.1.3
     */
    public function tpgb_dynamic_style_cache() {
		check_ajax_referer('tpgb-addons', 'security');

        // clear cache files
		$this->remove_dir_dynamic_style_files(TPGB_ASSET_PATH);

		wp_send_json(true);
    }
	
	/**
     * After Save Block Remove Clear cache files
     *
     * @since 1.0.0
     */
    public function tpgb_backend_clear_cache()
    {
		check_ajax_referer('tpgb-addons', 'security');

        // clear cache files
		$this->remove_backend_dir_files();

		wp_send_json(true);
    }
	
	/**
     * Current Page Clear cache files
     *
     * @since 1.0.0
     */
    public function tpgb_current_page_clear_cache()
    {
		check_ajax_referer('tpgb-addons', 'security');
		
		$plus_name='';
		if(isset($_POST['plus_name']) && !empty($_POST['plus_name'])){
			$plus_name = sanitize_text_field(wp_unslash($_POST['plus_name']));
		}
		if($plus_name== 'gutenberg-all') {
			// All clear cache files
			$this->remove_dir_files(TPGB_ASSET_PATH);		
		}else {
			// Current Page cache files
			if($this->plus_uid){
				delete_option( $this->plus_uid . '_blocks' );
			}
			$this->remove_current_page_dir_files( TPGB_ASSET_PATH, $plus_name );
		}
		wp_send_json(true);
    }
	
	/**
     * Remove files
     * @since 1.0.0
     */
    public function remove_files_unlink($post_type = null, $post_id = null, $extension = ['css', 'js'], $preload = false){
		$filename = '';
		if(!empty($preload)){
			$filename = 'preload-';
		}
        $css_path_url = $this->secure_path_url(TPGB_ASSET_PATH . DIRECTORY_SEPARATOR . ($post_type ? 'theplus-'.$filename . $post_type : 'theplus') . ($post_id ? '-' . $post_id : '') . '.min.css');
        $js_path_url = $this->secure_path_url(TPGB_ASSET_PATH . DIRECTORY_SEPARATOR . ($post_type ? 'theplus-'.$filename . $post_type : 'theplus') . ($post_id ? '-' . $post_id : '') . '.min.js');

        if (file_exists($css_path_url) && in_array( 'css', $extension ) ) {
            unlink($css_path_url);
        }

        if (file_exists($js_path_url) && in_array( 'js', $extension )) {
            unlink($js_path_url);
        }
    }
	
	/**
     * Remove in Dynamic Styles files
     * @since 1.1.3
     */
    public function remove_dir_dynamic_style_files($path_url) {
        if (!is_dir($path_url) || !file_exists($path_url)) {
            return;
        }

        foreach (scandir($path_url) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

			if (strpos($item, 'plus-global') !== false || strpos($item, 'theplus') !== false || strpos($item, 'plus-json-') !== false) {
			}else{
				unlink($this->secure_path_url($path_url . DIRECTORY_SEPARATOR . $item));
			}
        }
    }
	
	/**
     * Remove in directory files
     * @since 1.0.0
     */
    public function remove_dir_files($path_url) {
        if (!is_dir($path_url) || !file_exists($path_url)) {
            return;
        }
		if(get_option('tpgb_backend_cache_at') === false){
			add_option('tpgb_backend_cache_at', strtotime('now'),false);
		}else{
			update_option('tpgb_backend_cache_at', strtotime('now'),false);
		}
        foreach (scandir($path_url) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

			if (strpos($item, 'plus-global') !== false || strpos($item, 'plus-css-') !== false || strpos($item, 'plus-json-') !== false) {
			}else{
				unlink($this->secure_path_url($path_url . DIRECTORY_SEPARATOR . $item));
			}
        }
    }
	
	/**
     * Remove backend in directory files
     * @since 1.0.0
     */
    public function remove_backend_dir_files() {
		if (file_exists(TPGB_ASSET_PATH . '/theplus.min.css')) {
			unlink($this->secure_path_url(TPGB_ASSET_PATH . DIRECTORY_SEPARATOR . '/theplus.min.css'));
		}
		if(file_exists(TPGB_ASSET_PATH . '/theplus.min.js')){
			unlink($this->secure_path_url(TPGB_ASSET_PATH . DIRECTORY_SEPARATOR . '/theplus.min.js'));
		}
		if(get_option('tpgb_backend_cache_at') === false){
			add_option('tpgb_backend_cache_at', strtotime('now'),false);
		}else{
			update_option('tpgb_backend_cache_at', strtotime('now'),false);
		}
    }
	
	/**
     * Remove current Page in directory files
     * @since 1.0.0
     */
    public function remove_current_page_dir_files( $path_url, $plus_name = '' ) {
	
		if ((!is_dir($path_url) || !file_exists($path_url)) && empty($plus_name)) {
            return;
        }
		
		if (file_exists($path_url . '/'. $plus_name. '.min.css')) {
			unlink($this->secure_path_url($path_url . DIRECTORY_SEPARATOR . '/'. $plus_name . '.min.css'));
		}
		if (file_exists($path_url . '/'. str_replace("theplus","theplus-preload",$plus_name). '.min.css')) {
			unlink($this->secure_path_url($path_url . DIRECTORY_SEPARATOR . '/'. str_replace("theplus","theplus-preload",$plus_name) .'.min.css'));
			array_map('unlink', glob($this->secure_path_url($path_url . DIRECTORY_SEPARATOR . '/'. str_replace("theplus","theplus-preload",$plus_name.'-') .'*.*')));
		}
		if(file_exists($path_url . '/'. $plus_name. '.min.js')){
			unlink($this->secure_path_url($path_url. DIRECTORY_SEPARATOR . '/'. $plus_name . '.min.js'));
		}
		
    }
	
	/*
	 * Dynamic Style Forcely Remove In Version
	 * @since 1.1.3
	 */
	public function dynamic_style_version_clear_cache() {
		$option_name = 'tpgb_version_dynamic_cache';
		$get_version = get_option( $option_name );
		$versions = [ TPGB_VERSION ];
			
		if($get_version === false){
			add_option( $option_name, $versions );
			$this->remove_dir_files(TPGB_ASSET_PATH);
			$this->remove_dir_dynamic_style_files(TPGB_ASSET_PATH);
		}
		if( $get_version !== false ){
			
			//1.4.1
			if( version_compare( TPGB_VERSION, '1.4.1', '==' ) && !in_array( '1.4.1', $get_version ) ){
				$this->remove_dir_files(TPGB_ASSET_PATH);
				$versions = array_unique( array_merge( $get_version, $versions ) );
				update_option( $option_name, $versions );

				$get_blocks_list = get_option('tpgb_normal_blocks_opts');
				if(isset($get_blocks_list) && !empty($get_blocks_list)){
					if(isset($get_blocks_list['enable_normal_blocks']) && !empty($get_blocks_list['enable_normal_blocks']) && !in_array('tp-container', $get_blocks_list['enable_normal_blocks'])){
						$get_blocks_list['enable_normal_blocks'] = array_merge($get_blocks_list['enable_normal_blocks'],['tp-container']);
						update_option( 'tpgb_normal_blocks_opts', $get_blocks_list );
					}
				}
			}
			//1.4.6
			if( version_compare( TPGB_VERSION, '1.4.6', '==' ) && !in_array( '1.4.6', $get_version ) ){
				$this->remove_dir_files(TPGB_ASSET_PATH);
				$versions = array_unique( array_merge( $get_version, $versions ) );
				update_option( $option_name, $versions );
			}
			//1.4.9
			if( version_compare( TPGB_VERSION, '1.4.9', '==' ) && !in_array( '1.4.9', $get_version ) ){
				$this->remove_dir_files(TPGB_ASSET_PATH);
				$versions = array_unique( array_merge( $get_version, $versions ) );
				update_option( $option_name, $versions );
			}

			//2.0.7
			if( version_compare( TPGB_VERSION, '2.0.7', '==' ) && !in_array( '2.0.4', $get_version ) && !in_array( '2.0.5', $get_version ) && !in_array( '2.0.6', $get_version ) && !in_array( '2.0.7', $get_version ) ){
				$this->remove_dir_files(TPGB_ASSET_PATH);
				$this->remove_dir_dynamic_style_files(TPGB_ASSET_PATH);
				$versions = array_unique( array_merge( $get_version, $versions ) );
				update_option('tpgb_save_updated_at', strtotime('now'),false);
				update_option( $option_name, $versions );
				
				// Clear Litespeed cache
				if(method_exists('LiteSpeed_Cache_API', 'purge_all')){
					LiteSpeed_Cache_API::purge_all();
				}

				// W3 Total Cache.
				if ( function_exists( 'w3tc_flush_all' ) ) {
					w3tc_flush_all();
				}

				// WP Fastest Cache.
				if ( ! empty( $GLOBALS['wp_fastest_cache'] ) && method_exists( $GLOBALS['wp_fastest_cache'], 'deleteCache' ) ) {
					$GLOBALS['wp_fastest_cache']->deleteCache(true);
				}

				// WP Super Cache
				if ( function_exists( 'wp_cache_clean_cache' ) ) {
					global $file_prefix;
					wp_cache_clean_cache( $file_prefix, true );
				}
			}
			
		}
	}
	
	/**
	 * Returns the instance.
	 * @since  1.0.0
	 */
	public static function get_instance( $shortcodes = array() ) {
		
		if ( null == self::$instance ) {
			self::$instance = new self( $shortcodes );
		}
		return self::$instance;
	}
	
	/**
	 * Filters the content of a single block.
	 *
	 * @since 1.3.0.2
	 * @access public
	 * @param string $block_content The block content about to be appended.
	 * @param array  $block         The full block, including name and attributes.
	 * @return string               Returns $block_content unaltered.
	 */
	public function render_block( $block_content, $block ) {
		
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return $block_content;
		}
		if ( $block['blockName'] ) {
			$options = (!empty($block['attrs'])) ? $block['attrs'] : '';
			if(!empty($options) && class_exists('Tpgb_Get_Blocks') && $this->check_generate_script()===true){
				if( isset($options['block_id']) && !in_array($options['block_id'], $this->block_ids) ){
					$new_blocks = Tpgb_Get_Blocks::get_instance()->plus_blocks_options( $options, $block['blockName'] );
					$this->transient_blocks = array_merge($this->transient_blocks, $new_blocks);
					$this->block_ids[] = $options['block_id'];
				}
			}
			
			$this->transient_blocks[] = $block['blockName'];

			if( !preg_match('/\btpgb\/\b/', $block['blockName']) && isset($options['tpgbDisrule']) && !empty($options[ 'tpgbDisrule' ]) ){
				$global_blocks = Tpgb_Blocks_Global_Options::get_instance();
				$options = array_merge($global_blocks::render_block_default_attributes(), $options);
				$block_content = $global_blocks::block_row_conditional_render($options,$block_content);
			}

			//check content shortcode
			if (  !empty($block_content) && preg_match_all( '/'. get_shortcode_regex() .'/s', $block_content, $matches ) ) {
				if(!empty($matches) && array_key_exists( 2, $matches ) && has_shortcode( $block_content, $matches[2][0] )){
					$attrs=shortcode_parse_atts($matches[3][0]);
					if(!empty($attrs) && isset($attrs['id']) && !empty($attrs['id'])){
						if ( class_exists( 'Tp_Core_Init_Blocks' ) ) {
							$css_file = Tp_Core_Init_Blocks::get_instance();
							if ( !empty($css_file) && is_callable( array( $css_file, 'enqueue_post_css' ) ) ) {
								$css_file->enqueue_post_css( $attrs['id'] );
							}
						}
					}
				}
			}

			// Get Dynamic Content
			if(class_exists('Tpgbp_Pro_Blocks_Helper')){
				$blocks_helper = Tpgbp_Pro_Blocks_Helper::get_instance();
				if ( !empty($blocks_helper) && is_callable( array( $blocks_helper, 'tpgb_dynamic_val' ) ) ) {
					$block_content = $blocks_helper::tpgb_dynamic_val($block_content, $block);
				}
			}
			
			//Full site editing compatibility
			if(preg_match('/\btpgb\/\b/', $block['blockName']) && !empty($block) && !empty($block['innerHTML'])){
				$styletag = '/<style>(.*?)<\/style>/m';
				if(preg_match_all( $styletag , $block['innerHTML'], $style_matches )){
					$style = ($style_matches[0] && $style_matches[0][0]) ? $style_matches[0][0] : '';
					$block_content = $block_content.$style;
				}
			}
		}
		return $block_content;
	}
	
	public function plus_post_save_transient( $post_id, $post, $update ){
		update_option('tpgb_save_updated_at', strtotime('now'),false);
	}
	
	public function init_post_request_data(){
		
		if (is_admin()) {
            return;
        }
		
		if ($this->is_background_running()) {
            return;
        }
		
		$uid = null;
		
		if (!$this->is_preview_mode()) {
			if( $this->get_post_type_post_id() ){
				$uid = 'theplus-' . self::$tpgb_post_type . '-' . self::$tpgb_post_id;
			}
		}else{
			$uid = 'theplus';
		}
		
		if ($uid && $this->plus_uid == null) {
			$this->plus_uid = $uid;
            $this->requires_update = $this->requires_update();
        }
	}
	
	public function requires_update(){
		
		$blocks = get_option($this->plus_uid . '_blocks');
        $save_updated_at = get_option('tpgb_save_updated_at');
        $post_updated_at = get_option($this->plus_uid . '_updated_at');

        if ($blocks === false) {
            return true;
        }
        if ($save_updated_at === false) {
            return true;
        }
        if ($post_updated_at === false) {
            return true;
        }
        if ($save_updated_at != $post_updated_at) {
            return true;
        }

		return false;
	}
	
	/**
     * Check if wp running in background
     *
     * @since 1.0.0
     */
    public function is_background_running() {
        if (wp_doing_cron()) {
            return true;
        }

        if (wp_doing_ajax()) {
            return true;
        }
        
       /* if (isset($_REQUEST['action'])) {
            return true;
        }*/

        return false;
    }
	
	/*
	 * Toolset blocks Compatibility enqueue templates
	 * @since 2.0.0
	 * */
	public function toolset_blocks_compatibility_enqueue_wpa( $content ) {
		
		if ( ! is_archive() && ! is_home() && !	is_search()	) {
			return;
		}
		$wpa_id = apply_filters( 'wpv_filter_wpv_get_current_archive', null );

		if ( ! $wpa_id ) {
			return;
		}

		$maybe_wpa_helper_id = apply_filters( 'wpv_filter_wpv_get_wpa_helper_post', $wpa_id );

		if ( !empty($maybe_wpa_helper_id) && class_exists( 'Tp_Core_Init_Blocks' ) ) {
			$load_enqueue = Tp_Core_Init_Blocks::get_instance();
			
			if ( !empty($load_enqueue) && is_callable( array( $load_enqueue, 'enqueue_post_css' ) ) ) {
				$load_enqueue->enqueue_post_css( $maybe_wpa_helper_id );
			}
		}
	}

	/*
	 * Toolset blocks Compatibility enqueue assets
	 * @since 2.0.0
	 * */
	public function toolset_blocks_compatibility_ct_assets() {
		if ( ! is_single() ) {
			return;
		}

		global $post;

		$maybe_ct_selected = apply_filters( 'wpv_content_template_for_post', 0, $post );

		if ( 0 === (int) $maybe_ct_selected ) {
			return;
		}

		if ( !empty($maybe_ct_selected) && class_exists( 'Tp_Core_Init_Blocks' ) ) {
			$load_enqueue = Tp_Core_Init_Blocks::get_instance();
			
			if ( !empty($load_enqueue) && is_callable( array( $load_enqueue, 'enqueue_post_css' ) ) ) {
				$load_enqueue->enqueue_post_css( $maybe_ct_selected );
			}
		}
	}


	public function tpgb_onload_style_css( $html, $handle, $href, $media ){
		$handles = array( 'dashicons', 'wp-block-library', 'tpgb-fontawesome' );
		if( ( substr( $handle, 0, 10 ) === 'plus-post-') || in_array( $handle, $handles ) ){
			$html = '<link rel="preload" href="' . $href . '" as="style" id="' . $handle . '" media="' . $media . '" onload="this.onload=null;this.rel=\'stylesheet\'">'
            . '<noscript>' . $html . '</noscript>';
		}

		return $html;
	}

	public function tpgb_onload_defer_js($html, $handle){
		$handles = array( 'nexter-frontend-js', 'tpgb-delay', 'tpgb-plus-block-front-js', 'tpgb-lottie-extra', 'tpgb-lottie', 'gmaps-js', 'tpgb-font-awesome-pro' );
		if ( in_array( $handle, $handles ) || substr( $handle, 0, 7 ) === 'theplus') {
			$html = str_replace( '></script>', ' defer></script>', $html );
		}
		return $html;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->get_registered_blocks();
		$this->plus_template_blocks = [];
		add_filter( 'render_block', [ $this, 'render_block' ], 1000, 2 );
		
		add_filter('plus_template_parse_blocks', [ $this, 'plus_template_load_parse_blocks']);
		add_action('wp_footer', array($this, 'generate_scripts_frontend')); //wp_print_footer_scripts

		add_action( 'save_post', array($this,'plus_post_save_transient'), 10,3 );
		
		add_action('wp', [$this, 'init_post_request_data']);

		if($this->get_caching_option() != 'separate'){
			add_action( 'admin_bar_menu', [ $this, 'add_tpgb_clear_cache_admin_bar' ], 300 );
			add_action('wp_ajax_tpda_purge_current_clear', array($this, 'tpgb_current_page_clear_cache'));
		}
		
		if (is_admin()) {
			add_action('admin_init', array($this, 'dynamic_style_version_clear_cache'));
			add_action('wp_ajax_tpgb_all_perf_clear_cache', array($this, 'tpgb_smart_perf_clear_cache'));
			add_action('wp_ajax_tpgb_all_dynamic_clear_style', array($this, 'tpgb_dynamic_style_cache'));
			add_action('wp_ajax_tpgb_backend_clear_cache', array($this, 'tpgb_backend_clear_cache'));
		}
		
		add_action('wp', [$this, 'header_init_css_js'], 10, 2);
		if(!is_admin() && $this->get_defer_css_js()){
			add_filter( 'style_loader_tag', [$this, 'tpgb_onload_style_css'], 10, 4 );
			add_filter( 'script_loader_tag', [$this,'tpgb_onload_defer_js'], 10, 2 );
		}

		//Toolset Compatibility
		if ( defined( 'WPV_VERSION' ) ) {
			add_filter( 'wp', array( $this, 'toolset_blocks_compatibility_enqueue_wpa' ) );
			add_action( 'wp', array( $this, 'toolset_blocks_compatibility_ct_assets' ) );
		}
	}
}

/**
 * Returns instance of Tpgb_Library
 */
function tpgb_library() {
	return Tpgb_Library::get_instance();
}
?>