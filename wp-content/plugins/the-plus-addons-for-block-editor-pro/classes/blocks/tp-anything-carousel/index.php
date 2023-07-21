<?php
/* Block : Anything Carousel
 * @since : 1.0.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_anything_carousel_render_callback( $attributes, $content) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$carouselList = (!empty($attributes['carouselList'])) ? $attributes['carouselList'] : [];
	$rmdOrder = (!empty($attributes['rmdOrder'])) ? $attributes['rmdOrder'] : false;
	$OverflowHid = (!empty($attributes['OverflowHid'])) ? $attributes['OverflowHid'] : false;
	$carouselId  = (!empty($attributes['carouselId'])) ? $attributes['carouselId'] : '';

	$showDots = (!empty($attributes['showDots'])) ? $attributes['showDots'] : [ 'md' => false ];
	$dotsStyle = (!empty($attributes['dotsStyle'])) ? $attributes['dotsStyle'] : false;
	$slideHoverDots = (!empty($attributes['slideHoverDots'])) ? $attributes['slideHoverDots'] : false;
	$showArrows = (!empty($attributes['showArrows'])) ? $attributes['showArrows'] : [ 'md' => false ];
	$arrowsStyle = (!empty($attributes['arrowsStyle'])) ? $attributes['arrowsStyle'] : 'style-1';
	$arrowsPosition = (!empty($attributes['arrowsPosition'])) ? $attributes['arrowsPosition'] : 'top-right';
	$outerArrows = (!empty($attributes['outerArrows'])) ? $attributes['outerArrows'] : false;
	$slideHoverArrows = (!empty($attributes['slideHoverArrows'])) ? $attributes['slideHoverArrows'] : false;
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	//Carousel Options
	$carousel_settings = Tp_Blocks_Helper::carousel_settings( $attributes );

	$equalHeightAtt = Tpgbp_Pro_Blocks_Helper::global_equal_height( $attributes );
	$equalHclass = '';
	if(!empty($equalHeightAtt)){
		$equalHclass = ' tpgb-equal-height';
	}
	
	$Sliderclass = '';
	if($slideHoverDots==true && ( ( isset($showDots['md']) && !empty($showDots['md']) ) || ( isset($showDots['sm']) && !empty($showDots['sm']) ) || ( isset($showDots['xs']) && !empty($showDots['xs']) )) ){
		$Sliderclass .= ' hover-slider-dots';
	}
	if($outerArrows==true && ( ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ) ){
		$Sliderclass .= ' outer-slider-arrow';
	}
	if($slideHoverArrows==true && ( ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ) ){
		$Sliderclass .= ' hover-slider-arrow';
	}
	
	if( ( isset($showDots['md']) && !empty($showDots['md']) ) || ( isset($showDots['sm']) && !empty($showDots['sm']) ) || ( isset($showDots['xs']) && !empty($showDots['xs']) ) ){
		$Sliderclass .= ' dots-'.esc_attr($dotsStyle);
	}
	
	$dataAttr = '';
	if(!empty($carouselId)){
		$dataAttr .=' id="tpca-'.esc_attr($carouselId).'"';
		$dataAttr .=' data-id="tpca-'.esc_attr($carouselId).'"';
		$dataAttr .=' data-connection="tptab_'.esc_attr($carouselId).'"';
	}
	
	$output .= '<div class="tpgb-any-carousel tpgb-relative-block tpgb-carousel splide tpgb-block-'.esc_attr($block_id).' '.esc_attr($Sliderclass).' '.esc_attr($blockClass).' '.esc_attr($equalHclass).'" data-splide=\'' . json_encode($carousel_settings) . '\' '.$dataAttr.' '.$equalHeightAtt.'>';
		if( ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ){
			$output .= Tp_Blocks_Helper::tpgb_carousel_arrow($arrowsStyle,$arrowsPosition);
		}
		$output .= '<div class="tpgb-carousel-wrap tpgb-relative-block tpgb-trans-easeinout post-loop-inner splide__track" >';
			$output .= '<div class="splide__list">';
				if( !empty( $carouselList ) ){
					if(!empty($rmdOrder) ){
						shuffle($carouselList);
					}
					foreach ( $carouselList as $index => $item ) :
						$output .= '<div class="splide__slide tpgb-slide-content '.(!empty($OverflowHid) ? 'slide-overflow-hidden' :'' ).'">';
							if(!empty($item['blockTemp']) && $item['blockTemp']!='none' ){
								ob_start();
									if(!empty($item['blockTemp'])) {
										echo Tpgb_Library()->plus_do_block($item['blockTemp']);
									}
								$output .= ob_get_contents();
								ob_end_clean();
							}
						$output .= "</div>";
					endforeach;
				}
			$output .= "</div>";
		$output .= "</div>";
    $output .= "</div>";
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
	$arrowCss = Tp_Blocks_Helper::tpgb_carousel_arrow_css( $showArrows , $block_id );
	if( !empty($arrowCss) ){
		$output .= $arrowCss;
	}
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_anything_carousel() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$carousel_options = Tpgb_Blocks_Global_Options::carousel_options();
	$globalEqualHeightOptions = Tpgbp_Plus_Extras_Opt::load_plusEqualHeight_options();
	
	$attributesOptions = [
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'carouselList' => [
				'type'=> 'array',
				'repeaterField' => [
					(object) [
						'title' => [
							'type' => 'string',
							'default' => 'Slide'
						],
						'blockTemp' => [
							'type' => 'string',
							'default' => 'none'
						],
						'backendVisi' => [
							'type' => 'boolean',
							'default' => false,	
						],
					],
				],
				'default' => [
					[
						'_key' => 0,
						'title' => 'Slide',
						'blockTemp' => 'none',
						'backendVisi' => false
 					],
				],
			],
			'carouselId' => [
				'type' => 'string',
				'default' => '',
			],
			'OverflowHid' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'rmdOrder' => [
				'type' => 'boolean',
				'default' => false,	
			],
		];
		
	$attributesOptions = array_merge($attributesOptions, $carousel_options, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption, $globalEqualHeightOptions);
	
	register_block_type( 'tpgb/tp-anything-carousel', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_anything_carousel_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_anything_carousel' );