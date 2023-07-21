<?php
/* Block : Before After
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_before_after_render_callback( $attr, $content) {
	$output = '';
	$block_id = (!empty($attr['block_id'])) ? $attr['block_id'] : uniqid("title");
	$style = (!empty($attr['style'])) ? $attr['style'] : 'horizontal';
	$beforeImg = (!empty($attr['beforeImg'])) ? $attr['beforeImg'] : '';
	$beforeLabel = (!empty($attr['beforeLabel'])) ? $attr['beforeLabel'] : '';
	$afterImg = (!empty($attr['afterImg'])) ? $attr['afterImg'] : '';
	$afterLabel = (!empty($attr['afterLabel'])) ? $attr['afterLabel'] : '';
	$imageSize = (!empty($attr['imageSize'])) ? $attr['imageSize'] : 'full';
	$fullWidth = (!empty($attr['fullWidth']) ? 'yes' : 'no');
	$onmouseHvr = (!empty($attr['onmouseHvr']) ? 'yes' : 'no');
	$sepLine = (!empty($attr['sepLine']) ? 'yes' : 'no');
	$sepStyle = (!empty($attr['sepStyle'])) ? $attr['sepStyle'] : '';
	$sepPosi = (!empty($attr['sepPosi'])) ? $attr['sepPosi'] : '25';
	$sepWidth = (!empty($attr['sepWidth'])) ? $attr['sepWidth'] : '15';
	$sepIcon = (!empty($attr['sepIcon'])) ? $attr['sepIcon'] : '';
	$alignment = (!empty($attr['alignment'])) ? $attr['alignment'] : [ 'md' => 'center', 'sm' =>  '', 'xs' =>  '' ];
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attr );

	$datattr=$mid_sep=$bottom_sep='';
	//Set Before Image Tag
	if(!empty($beforeImg)){
		$before_img = '';
		if(!empty($beforeImg['url']) && !empty($imageSize) && !empty($beforeImg['id'])){
			$before_image=$beforeImg['id'];
			$img = wp_get_attachment_image_src($before_image,$imageSize);
			$before_imgSrc = ( isset($img[0]) && !empty( $img[0]) ) ? $img[0] :  TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg';
		}else{
			$before_imgSrc = $beforeImg['url'];
		}
		$before_img='<img class="tpgb-beforeimg-wrap" src="'.esc_url($before_imgSrc).'" alt="'.esc_html__( 'Before_Image', 'tpgbp' ).'">';
	}
	if(!empty($afterImg)){
		$after_img = '';
		if(!empty($afterImg['url']) && !empty($imageSize) && !empty($afterImg['id'])){
			$after_image=$afterImg['id'];
			$img = wp_get_attachment_image_src($after_image,$imageSize);
			$after_imgSrc = ( isset($img[0]) && !empty( $img[0]) ) ? $img[0] :  TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg';
		}else{
			$after_imgSrc = $afterImg['url'];
		}
		$after_img='<img class="tpgb-afterimg-wrap" src="'.esc_url($after_imgSrc).'" alt="'.esc_html__( 'After', 'tpgbp' ).'">';
	}

	//Set Separator 
	if(!empty($style) && ($style=='horizontal' || $style=='vertical')){
		if($sepStyle=='middle'){
			$mid_sep='<div class="tpgb-beforeafter-sep"></div>';
		}else{
			$mid_sep='<div class="tpgb-beforeafter-sep"></div>';
			$bottom_sep='<div class="tpgb-bottom-sep"></div>';
		}
	} 
	//Set Separator Image
	$image_sep = '';
	if(!empty($sepIcon['url'])){
		$imgSrc = $sepIcon['url'];
		$image_sep= '<div class="tpgb-before-sepicon"><img src="'.esc_url($imgSrc).'" alt="'.esc_html__( 'tpgb_sepIcon', 'tpgbp' ).'"></div>';
	}
	//Set Data Attr
	$datattr .=' data-type="'.esc_attr($style).'" ';
	$datattr .=' data-id="tpgb-block-'.esc_attr($block_id).'" ';
	$datattr .=' data-click_hover_move="'.esc_attr($onmouseHvr).'" ';
	$datattr .=' data-separate_position="'.esc_attr($sepPosi).'" ';
	$datattr .=' data-full_width="'.esc_attr($fullWidth).'" ';
	$datattr .=' data-separator_style="'.esc_attr($sepStyle).'" ';
	$datattr .=' data-separate_width="'.esc_attr($sepWidth).'" ';
	$datattr .=' data-responsive="yes" ';
	$datattr .=' data-width="0" ';
	$datattr .=' data-max-width="0" ';
	$datattr .=' data-separate_switch="'.esc_attr($sepLine).'" ';
	$datattr .=' data-show="1" ';
	if(!empty($sepIcon['url'])){
		$datattr .=' data-separate_image="2" ';
	}else{
		$datattr .=' data-separate_image="1" ';
	}
	if( !empty($beforeImg['url']) && !empty($afterImg['url']) ){
		$output .= '<div  class="tpgb-before-after tpgb-block-'.esc_attr($block_id).' '.esc_attr( $blockClass ).'" '.$datattr.' >';
			$output .= '<div class="tpgb-beforeafter-inner">';
				
					$output .= '<div class="tpgb-beforeafter-img tpgb-before-img">';
						$output .= $before_img;
						if(!empty($beforeLabel)){
							$output .= '<div class="tpgb-beforeafter-label before-label '.esc_attr($style).'">'.wp_kses_post($beforeLabel).'</div>';
						}
					$output .= '</div>';
					$output .= '<div class="tpgb-beforeafter-img tpgb-after-img">';
						$output .= $after_img;
						if(!empty($afterLabel)){
							$output .= '<div class="tpgb-beforeafter-label after-label">'.wp_kses_post($afterLabel).'</div>';
						}
					$output .= '</div>';
					$output .= $mid_sep;
					$output .= $image_sep;
				
			$output .= '</div>';
			$output .= $bottom_sep;
		$output .= '</div>';
	}else{
		$output .= '<h3 class="tpgb-posts-not-found">'.esc_html__('Please select a Before images & After Image','tpgbp').'</h3>';
	}	
	
	$bestyle = '';
	// Alignment css 
	if(( isset($alignment['md']) && !empty($alignment['md']) && $alignment[ 'md'] == 'right' )) {
		$bestyle .= '@media (min-width: 1024px) { .tpgb-block-'.esc_attr($block_id).'.tpgb-before-after { margin-left:
		auto !important; margin-right: 0px !important  } } ';
	}
	if( isset($alignment['sm']) && !empty($alignment['sm']) && $alignment['sm'] == 'right'){
		$bestyle .= '@media (max-width: 1024px) and (min-width:768px){ .tpgb-block-'.esc_attr($block_id).'.tpgb-before-after{margin-left: auto !important; margin-right: 0px !important } } ';
	}
	if( isset($alignment['xs']) && !empty($alignment['xs']) && $alignment['xs'] == 'right'){
		$bestyle .= '@media (max-width: 767px) { .tpgb-block-'.esc_attr($block_id).'.tpgb-before-after{margin-left:
		auto !important; margin-right: 0px !important } } ';
	}

	// Left
	if(( isset($alignment['md']) && !empty($alignment['md']) && $alignment[ 'md'] == 'left' )) {
		$bestyle .= '@media (min-width: 1024px) { .tpgb-block-'.esc_attr($block_id).'.tpgb-before-after {margin-right:
		auto !important; margin-left: 0px !important; } } ';
	}
	if( isset($alignment['sm']) && !empty($alignment['sm']) && $alignment['sm'] == 'left'){
		$bestyle .= '@media (max-width: 1024px) and (min-width:768px){ .tpgb-block-'.esc_attr($block_id).'.tpgb-before-after{margin-right: auto !important; margin-left: 0px !important;} } ';
	}
	if( isset($alignment['xs']) && !empty($alignment['xs']) && $alignment['xs'] == 'left'){
		$bestyle .= '@media (max-width: 767px) { .tpgb-block-'.esc_attr($block_id).'.tpgb-before-after{margin-right:
		auto !important; margin-left: 0px !important;} } ';
	}

	//center
	if(( isset($alignment['md']) && !empty($alignment['md']) && $alignment['md'] == 'center' )){
		$bestyle .= '@media (min-width: 1024px) { .tpgb-block-'.esc_attr($block_id).'.tpgb-before-after {margin-left:
		auto!important;  margin-right: auto !important;} }';
	}
	if( isset($alignment['sm']) && !empty($alignment['sm']) && $alignment['sm'] == 'center' ) {
		$bestyle .= '@media (max-width: 1024px) and (min-width:768px) { .tpgb-block-'.esc_attr($block_id).'.tpgb-before-after
	{margin-left: auto !important; margin-right: auto !important;} } ';
	}
	if( isset($alignment['xs']) && !empty($alignment['xs']) && $alignment['xs'] == 'center' ) {
		$bestyle .= '@media (max-width: 767px){ .tpgb-block-'.esc_attr($block_id).'.tpgb-before-after{margin-left:
		auto !important; margin-right: auto !important;} } ';
	}
	
	$output .= '<style>'.$bestyle.'</style>';

	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attr, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_before_after_content() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'style' => [
				'type' => 'string',
				'default' => 'horizontal',
			],
			'beforeImg' => [
				'type' => 'object',
				'default' => [
					'url' => '',
				],
			],
			'beforeLabel' => [
				'type' => 'string',
				'default' => '',
			],
			'afterImg' => [
				'type' => 'object',
				'default' => [
					'url' => '',
				],
			],
			'afterLabel' => [
				'type' => 'string',
				'default' => '',
			],
			'imageSize' => [
				'type' => 'string',
				'default' => 'full',	
			],
			'alignment' => [
				'type' => 'object',
				'default' => [ 'md' => 'center', 'sm' =>  '', 'xs' =>  '' ],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-before-after { text-align: {{alignment}}; }',
					],
				],
				'scopy' => true,
			],
			'fullWidth' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'onmouseHvr' => [
				'type' => 'boolean',
				'default' => true,
				'scopy' => true,
			],
			'sepStyle' => [
				'type' => 'string',
				'default' => 'middle',
				'scopy' => true,
			],
			'sepLine' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'sepWidth' => [
				'type' => 'string',
				'default' => '5',
				'scopy' => true,
			],
			'sepPosi' => [
				'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'sepColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-beforeafter-sep{ background : {{sepColor}}; }',
					],
				],
				'scopy' => true,
			],
			'sepbotColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-bottom-sep{ background : {{sepbotColor}}; }',
					],
				],
				'scopy' => true,
			],
			'sepIcon' => [
				'type' => 'object',
				'default' => [
					'url' => '',
				],
				'scopy' => true,
			],
			'padding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-beforeafter-inner .tpgb-beforeafter-label{ padding: {{padding}}; }',
					],
				],
				'scopy' => true,
			],
			'labelTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-beforeafter-inner .tpgb-beforeafter-label',
					],
				],
				'scopy' => true,
			],
			'labelColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-beforeafter-inner .tpgb-beforeafter-label{ color : {{labelColor}}; }',
					],
				],
				'scopy' => true,
			],
			'labelBgtype' => [
				'type'=> 'object',
				'default'=> (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-beforeafter-inner .tpgb-beforeafter-label',
					],
				],
				'scopy' => true,
			],
			'labelBorder' => [
				'type'=> 'object',
				'default'=> (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-beforeafter-inner .tpgb-beforeafter-label',
					],
				],
				'scopy' => true,
			],
			'labelBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-beforeafter-inner .tpgb-beforeafter-label{ border-radius: {{labelBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'labelBshadow' => [
				'type'=> 'object',
				'default'=> (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-beforeafter-inner .tpgb-beforeafter-label',
					],
				],
				'scopy' => true,
			],
		);
	
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-before-after', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_before_after_render_callback'
    ) );
}
add_action( 'init', 'tpgb_before_after_content' );