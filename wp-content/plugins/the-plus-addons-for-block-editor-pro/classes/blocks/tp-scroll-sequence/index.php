<?php
/* Block : TP Scroll Sequence
 * @since : 1.3.2
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_scroll_sequence_render_callback( $attributes ) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$scrollType = (!empty($attributes['scrollType'])) ? $attributes['scrollType'] : 'image';
	$imageUpldType = (!empty($attributes['imageUpldType'])) ? $attributes['imageUpldType'] : 'gallery';
	$imageGallery = (!empty($attributes['imageGallery'])) ? $attributes['imageGallery'] : '';
	$imagePath = (!empty($attributes['imagePath'])) ? $attributes['imagePath'] : '';
	$imagePrefix = (!empty($attributes['imagePrefix'])) ? $attributes['imagePrefix'] : '';
	$imageDigit = (!empty($attributes['imageDigit'])) ? $attributes['imageDigit'] : '1';
	$imageType = (!empty($attributes['imageType'])) ? $attributes['imageType'] : 'jpg';
	$totalImage = (!empty($attributes['totalImage'])) ? $attributes['totalImage'] : '20';
	$applyTo = (!empty($attributes['applyTo'])) ? $attributes['applyTo'] : 'body';
	$preloadImg = (!empty($attributes['preloadImg'])) ? (int)$attributes['preloadImg'] : 20;
	$canStartOffset = (!empty($attributes['canStartOffset'])) ? $attributes['canStartOffset'] : 0;
	$canEndOffset = (isset($attributes['canEndOffset'])) ? $attributes['canEndOffset'] : 0;

	$imgLazyLoad = (!empty($attributes['imgLazyLoad'])) ? $attributes['imgLazyLoad'] : false;
	$initialLoadImg = (!empty($attributes['initialLoadImg'])) ? $attributes['initialLoadImg'] : '';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$data_attr = $initialImg = '';

	if(!empty($imgLazyLoad) && !empty($initialLoadImg)){
		$initialImg = (int)$initialLoadImg;
	}
	$imgGlr = '';
	if($scrollType=='image'){
		if($imageUpldType=='gallery' && !empty($imageGallery)){
			$imgGlr = array_column($imageGallery, 'url');
		}else if(!empty($imagePath) && !empty($totalImage)){
			$imgGlr = array();
			for($i=1; $i<=$totalImage; $i++){
				$immm = str_pad($i, $imageDigit, '0', STR_PAD_LEFT);
				$ImgURL = $imagePath.'/'.$imagePrefix.$immm.'.'.$imageType;
				$URLexists = @file_get_contents($ImgURL);
				if( !empty($URLexists) ){
					$imgGlr[] = $ImgURL;
				}
			}
		}
		
		$data_attr = array(
			'block_id' => esc_attr($block_id),
			'scrollType' => esc_attr($scrollType),
			'imgGallery' => $imgGlr,
			'applyto' => esc_attr($applyTo),
			'imgUpdType' => esc_attr($imageUpldType),
			'preloadImg' => esc_attr($preloadImg),
			'imgLazyLoad' => esc_attr($initialImg),
			'startOffset' => esc_attr($canStartOffset),
			'endOffset' => esc_attr($canEndOffset),
		);
	}

	$data_attr = 'data-attr="'.htmlspecialchars(json_encode($data_attr, true), ENT_QUOTES, 'UTF-8').'"';

    $output .= '<div class="tpgb-scroll-sequence tpgb-relative-block tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' " '.$data_attr.'>';
    $output .= '</div>';
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_scroll_sequence() {
	$attributesOptions = array(
			'block_id' => array(
                'type' => 'string',
				'default' => '',
			),
			'scrollType' => [
				'type' => 'string',
				'default' => 'image',	
			],
			'imageGallery' => [
				'type' => 'array',
				'default' => [
					[ 
						'url' => '',
						'Id' => '',
					],
				],
			],
			'imagePath' => [
				'type' => 'string',
				'default' => '',	
			],
			'imageUpldType' => [
				'type' => 'string',
				'default' => 'gallery',	
			],
			'imageDigit' => [
				'type' => 'string',
				'default' => '1',	
			],
			'imagePrefix' => [
				'type' => 'string',
				'default' => '',	
			],
			'imageType' => [
				'type' => 'string',
				'default' => 'jpg',	
			],
			'totalImage' => [
				'type' => 'string',
				'default' => '20',	
			],
			'applyTo' => [
				'type' => 'string',
				'default' => 'body',	
			],
			'preloadImg' => [
				'type' => 'string',
				'default' => '20',	
			],
			'imgLazyLoad' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'initialLoadImg' => [
				'type' => 'string',
				'default' => '',	
			],
			
			'canAlign' => [
				'type' => 'object',
				'default' => [],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'applyTo', 'relation' => '==', 'value' => 'none' ], ['key' => 'canAlign', 'relation' => '==', 'value' => 'left' ]],
						'selector' => '{{PLUS_WRAP}} { margin-left: 0; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'applyTo', 'relation' => '==', 'value' => 'none' ], ['key' => 'canAlign', 'relation' => '==', 'value' => 'center' ]],
						'selector' => '{{PLUS_WRAP}} { margin: 0 auto; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'applyTo', 'relation' => '==', 'value' => 'none' ], ['key' => 'canAlign', 'relation' => '==', 'value' => 'right' ]],
						'selector' => '{{PLUS_WRAP}} { margin-right: 0; }',
					]
				],
				'scopy' => true,
			],
			'canVidPosition' => [
				'type' => 'object',
				'groupField' => [
					(object) [
						'posTop' => [
							'type' => 'object',
							'default' => [
								'md' => '',
								"unit" => '%',
							],
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'applyTo', 'relation' => '!=', 'value' => 'none' ]],
									'selector' => '{{PLUS_WRAP}}-canvas { top: {{posTop}}; }',
								],
							],
							'scopy' => true,
						],
						'posLeft' => [
							'type' => 'object',
							'default' => [ 
								'md' => '',
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'applyTo', 'relation' => '!=', 'value' => 'none' ]],
									'selector' => '{{PLUS_WRAP}}-canvas { left: {{posLeft}}; }',
								],
							],
							'scopy' => true,
						],
					],
				],
				'default' => [
					'posTop' => ['md' => '', 'unit'=> '%'],
					'posLeft' => ['md' => '', 'unit'=> '%'],
				],	
			],
			'canVidWidth' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'applyTo', 'relation' => '!=', 'value' => 'none' ]],
						'selector' => '{{PLUS_WRAP}}-canvas { width: {{canVidWidth}} !important; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'applyTo', 'relation' => '==', 'value' => 'none' ]],
						'selector' => '{{PLUS_WRAP}} {{PLUS_WRAP}}-canvas { width: {{canVidWidth}} !important; }',
					],
				],
				'scopy' => true,
			],
			'canVidHeight' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'applyTo', 'relation' => '!=', 'value' => 'none' ]],
						'selector' => '{{PLUS_WRAP}}-canvas { height: {{canVidHeight}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'applyTo', 'relation' => '==', 'value' => 'none' ]],
						'selector' => '{{PLUS_WRAP}} {{PLUS_WRAP}}-canvas { height: {{canVidHeight}}; }',
					],
				],
				'scopy' => true,
			],
			'canVidZIndex' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}-canvas { z-index: {{canVidZIndex}}; }',
					],
				],
				'scopy' => true,
			],
			'canStartOffset' => [
				'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'canEndOffset' => [
				'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
		);
	$attributesOptions = array_merge($attributesOptions);
	
	register_block_type( 'tpgb/tp-scroll-sequence', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_scroll_sequence_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_scroll_sequence' );