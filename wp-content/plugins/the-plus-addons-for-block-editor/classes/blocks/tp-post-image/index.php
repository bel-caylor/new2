<?php
/* Tp Block : Post Image
 * @since	: 1.2.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_post_image_render_callback( $attr, $content) {
	$output = '';
	$post_id = get_the_ID();

    $block_id = (!empty($attr['block_id'])) ? $attr['block_id'] : uniqid("title");
	$imageType = (!empty($attr['imageType'])) ? $attr['imageType'] : 'default';
	$bgLocation = (!empty($attr['bgLocation'])) ? $attr['bgLocation'] : 'section';
	$imageSize = (!empty($attr['imageSize'])) ? $attr['imageSize'] : 'full';
    $fancyBox = (!empty($attr['fancyBox'])) ? $attr['fancyBox'] : false;
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attr );
	
	$data_attr = [];
	if(!empty($imageType) && $imageType=='background'){
		$blockClass .= ' post-img-bg';
		$data_attr['id'] = $block_id;
		$data_attr['imgType'] = $imageType;
		$data_attr['imgLocation'] = $bgLocation;
	}
	
	$data_attr = json_encode($data_attr);

    $image_content ='';
	if (has_post_thumbnail( $post_id ) ){
		$image_content = get_the_post_thumbnail_url($post_id,$imageSize);
		$fancy_content = get_the_post_thumbnail_url($post_id, 'full' );
		$image_content = (!empty($image_content)) ? $image_content : TPGB_ASSETS_URL. 'assets/images/tpgb-placeholder.jpg';
		
		// Set Fancy Box Option
		$data_settings = $data_fancy = $href = '';
		if(!empty($fancyBox)){
			$FancyData = (!empty($attr['FancyOption'])) ? json_decode($attr['FancyOption']) : [];

			$button = array();
			if (is_array($FancyData) || is_object($FancyData)) {
				foreach ($FancyData as $value) {
					$button[] = $value->value;
				}
			}
			$href = $fancy_content;
			$fancybox = array();
			$fancybox['button'] = $button;
			$fancybox['animationEffect'] = $attr['AnimationFancy'];
			$fancybox['animationDuration'] = $attr['DurationFancy'];
			$data_settings .= ' data-fancy-option=\''.json_encode($fancybox).'\'';
			$data_settings .= ' data-id="'.esc_attr($block_id).'" ';
			$data_fancy = 'data-fancybox="postImg-'.esc_attr($block_id).'"';

		}else{
			$href = get_the_permalink();
		}
		
		$output .= '<div class="tpgb-post-image tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' '.(!empty($fancyBox) ? 'tpgb-fancy-add' : '').'" data-setting=\'' . $data_attr . '\' '.$data_settings.'>';
			
				if(!empty($imageType) && $imageType!='background'){
					$output .= '<div class="tpgb-featured-image">';
						$output .= '<a href="'.esc_url($href).'" '.$data_fancy.'>';
							$output .= get_the_post_thumbnail($post_id,$imageSize,[ 'class' => 'tpgb-featured-img']);
						$output .= '</a>';
					$output .= '</div>';
				}else if(!empty($imageType) && $imageType=='background'){
					$output .= '<div class="tpgb-featured-image" style="background-image: url('.esc_url($image_content).')"></div>';
				}
			
		$output .= "</div>";
	}
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attr, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_post_image_content() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
    $globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
    $globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'imageType' => [
                'type' => 'string',
				'default' => 'default',
			],
			'bgLocation' => [
                'type' => 'string',
				'default' => 'section',
			],
			'imageSize' => [
                'type' => 'string',
				'default' => 'full',
			],
			'imageAlign' => [
				'type' => 'object',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'imageType', 'relation' => '==', 'value' => 'default' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-image {text-align: {{imageAlign}};}',
					],
				],
				'scopy' => true,
			],
			'maxWidth' => [
				'type' => 'object',
				'default' => ['md' => '', 'unit' => 'px'],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'imageType', 'relation' => '==', 'value' => 'default' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-featured-image img{max-width: {{maxWidth}};width: 100%;}',
					],
				],
				'scopy' => true,
			],
			'bgPosition' => [
				'type' => 'string',
				'default' => 'center center',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'imageType', 'relation' => '==', 'value' => 'background' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-image.post-img-bg .tpgb-featured-image{background-position : {{bgPosition}} }',
					],
				],
				'scopy' => true,
			],
			'bgAttachment' => [
				'type' => 'string',
				'default' => 'scroll',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'imageType', 'relation' => '==', 'value' => 'background' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-image.post-img-bg .tpgb-featured-image{background-attachment : {{bgAttachment}} }',
					],
				],
				'scopy' => true,
			],
			'bgRepeat' => [
				'type' => 'string',
				'default' => 'no-repeat',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'imageType', 'relation' => '==', 'value' => 'background' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-image.post-img-bg .tpgb-featured-image{background-repeat : {{bgRepeat}} }',
					],
				],
				'scopy' => true,
			],
			'bgSize' => [
				'type' => 'string',
				'default' => 'cover',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'imageType', 'relation' => '==', 'value' => 'background' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-image.post-img-bg .tpgb-featured-image{background-size : {{bgSize}} }',
					],
				],
				'scopy' => true,
			],
			
			'postimgBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => '',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-featured-image a:after,{{PLUS_WRAP}}.tpgb-post-image.post-img-bg .tpgb-featured-image:after',
					],
				],
				'scopy' => true,
			],
			
			'postimgHvrBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => '',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-featured-image:hover a:after,{{PLUS_WRAP}}.tpgb-post-image.post-img-bg .tpgb-featured-image:after',
					],
				],
				'scopy' => true,
			],
			'postimgbor' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
					'type' => '',	
					'color' => '',
					'width' => (object) [
						'md' => (object)[
							'top' => '',
							'left' => '',
							'bottom' => '',
							'right' => '',
						],
						"unit" => "",
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-featured-image .tpgb-featured-img',
					],
				],
				'scopy' => true,
			],
			'postimgHvrbor' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
					'type' => '',	
					'color' => '',
					'width' => (object) [
						'md' => (object)[
							'top' => '',
							'left' => '',
							'bottom' => '',
							'right' => '',
						],
						"unit" => "",
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-featured-image:hover .tpgb-featured-img',
					],
				],
				'scopy' => true,
			],
			'postimgbRad' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-featured-image .tpgb-featured-img,{{PLUS_WRAP}} .tpgb-featured-image a:after{ border-radius : {{postimgbRad}}; }',
					],
				],
				'scopy' => true,
			],
			'postimgbhvrRad' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-featured-image:hover .tpgb-featured-img,{{PLUS_WRAP}} .tpgb-featured-image:hover a:after{ border-radius : {{postimgbhvrRad}}; }',
					],
				],
				'scopy' => true,
			],
			'postimgBshad' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-featured-image .tpgb-featured-img',
					],
				],
				'scopy' => true,
			],
			'postimghvrBshad' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-featured-image:hover .tpgb-featured-img',
					],
				],
				'scopy' => true,
			],
			'fancyBox' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'FancyOption' => [
				'type' => 'string',
        		'default' => '[]',
				'scopy' => true,
			],
			'AnimationFancy' => [
				'type' => 'string',
				'default' => 'zoom',
				'scopy' => true,
			],
			'DurationFancy' => [
				'type' => 'string',
				'default' => 300,
				'scopy' => true,
			],
		);
	
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-post-image', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_post_image_render_callback'
    ) );
}
add_action( 'init', 'tpgb_post_image_content' );