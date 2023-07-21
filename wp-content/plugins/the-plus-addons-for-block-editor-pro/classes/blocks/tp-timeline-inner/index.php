<?php
/* Block : TP Column
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_timeline_inner_render_callback( $attributes, $content) {

	$output = '';
	$index = ( !empty($attributes['index']) ) ? $attributes['index'] : '';
	$repetKey = ( !empty($attributes['repetKey']) ) ? $attributes['repetKey'] : '';
	$RSAlign = ( !empty($attributes['RSAlign']) ) ? $attributes['RSAlign'] : '';
	$RPosition = ( !empty($attributes['RPosition']) ) ? $attributes['RPosition'] : '';
   	$layout = ( !empty($attributes['layout']) ) ? $attributes['layout'] : '';
	$MLayout = ( !empty($attributes['MLayout']) ) ? $attributes['MLayout'] : '';
	$RcAlign = ( !empty($attributes['RcAlign']) ) ? $attributes['RcAlign'] : '';
	$style = ( !empty($attributes['style']) ) ? $attributes['style'] : '';
	$RcTitle = ( !empty($attributes['RcTitle']) ) ? $attributes['RcTitle'] : '';
	$description = ( !empty($attributes['description']) ) ? $attributes['description'] : '';
	$Rnone = ( !empty($attributes['Rnone']) ) ? $attributes['Rnone'] : '';
	$PinStyle = ( !empty($attributes['PinStyle']) ) ? $attributes['PinStyle'] : '';
	$RTitle = ( !empty($attributes['RTitle']) ) ? $attributes['RTitle'] : '';
	$Ricon = ( !empty($attributes['Ricon']) ) ? $attributes['Ricon'] : '';
	$CustomURL = (!empty($attributes['CustomURL']) && !empty($attributes['CustomURL']['url'])) ? $attributes['CustomURL'] : '';
	$titledivider = ( !empty($attributes['titledivider']) ) ? $attributes['titledivider'] : false ;
	$ImageSize = ( !empty($attributes['ImageSize']) ) ? $attributes['ImageSize'] : 'full';
	$RButton = ( !empty($attributes['RButton']) ) ? $attributes['RButton'] : false;
	$RBtnText = ( !empty($attributes['RBtnText']) ) ? $attributes['RBtnText'] : '';
	$url = ( !empty($CustomURL['url']) ) ? $CustomURL['url'] : '#';
	$Target = ( !empty($CustomURL['target'])) ? 'target=_blank' : "";
	$Nofollow = ( !empty($CustomURL['nofollow'])) ? 'rel=nofollow' : "";

	$title = '';
	if(!empty($RcTitle) && !empty($url)){
		$title .= '<a class="timeline-item-heading timeline-transition" href="'.esc_url($CustomURL).'" '.esc_attr($Target).' '.esc_attr($Nofollow).' >'. esc_html($RcTitle) .'</a>';
	}else if(!empty($RcTitle)){
		$title .= '<h3 class="timeline-item-heading timeline-transition">'.esc_html($RcTitle).'</h3>';
	}
	

	$output .= '<div class="grid-item timeline-item-wrap tp-repeater-item-'.esc_attr($repetKey).' '.esc_attr($layout).' timeline-'.esc_attr($RSAlign).'-content text-pin-position-'.esc_attr($RPosition).'">';
		$output .= '<div class="timeline-inner-block timeline-transition">';
			$output .= '<div class="timeline-item '.esc_attr($RcAlign).'">';
				$output .= '<div class="timeline-item-content timeline-transition '.esc_attr($RcAlign).' ">';
					$output .= '<div class="timeline-tl-before timeline-transition"></div>';
					$output .= $title;
					if( $style == 'style-2' && !empty($titledivider) ){
						$output .= '<div class="border-bottom '.esc_attr($RcAlign).'" ><hr/></div>';
					}
					
					$output .= '<div class="timeline-content-image">';
						$output .= $content;
					$output .= '</div>';

					if($description){
						$output .= '<div class="timeline-item-description timeline-transition">';
							$output .= $description;
						$output .= '</div>';
					}
					if( !empty($RButton) && !empty($RBtnText) ){
						$output .= '<div class="button-style-8">';
							$output .= '<a href="'.esc_url($CustomURL).'"  class="button-link-wrap" role="button" '.esc_attr($Target).' '.esc_attr($Nofollow).'>'.esc_html($RBtnText).'</a>';
						$output .= '</div>';
					}
				$output .= '</div>';
			$output .= '</div>';

			$output .= '<div class="point-icon '.esc_attr($PinStyle).'">';
				$output .= '<div class="timeline-tooltip-wrap">';
					$output .= '<div class="timeline-point-icon">';
						if( $Rnone == 'icon' && !empty($Ricon) ){
							$output .= '<i class="'.esc_attr($Ricon).' point-icon-inner"></i>';
						}elseif( $Rnone == 'image' && !empty($Rimg) ){
							$IconImgId = $index;												
							if( !empty($Content['Rimg']) && !empty($Content['Rimg']['id']) ){
								$IconImgId = $Content['Rimg']['id'];
								$AttImg = wp_get_attachment_image($IconImgId,$ImageSize, false, ['class' => 'point-icon-inner']);
								$output .= $AttImg;
							}
						}
					$output .= '</div>';
				$output .= '</div>';
				if( !empty($RTitle) ){
					$output .= '<div class="timeline-text-tooltip position-'.esc_attr($RPosition).' timeline-transition">';
						$output .= esc_html($RTitle);
						$output .= '<div class="tpgb-tooltip-arrow timeline-transition"></div>';
					$output .= '</div>';
				}
			$output .= '</div>';

		$output .= '</div>';
	$output .= '</div>';

	return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_timeline_inner() {
	
	$attributesOptions = [
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'className' => [
			'type' => 'string',
			'default' => '',
		],
		'index' => [
			'type' => 'number',
			'default' => '',
		],
		'repetKey' => [
			'type' => 'string',
			'default' => '',
		],
		'RSAlign' => [
			'type' => 'string',
			'default' => '',
		],
		'RPosition' => [
			'type' => 'string',
			'default' => '',	
		],
		'layout' => [
			'type' => 'string',
			'default' => '',
		],
		'MLayout' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'RcAlign' => [
			'type' => 'string',
			'default' => 'text-right',	
		],
		'style' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'RcTitle' => [
			'type' => 'string',
			'default' => '',
		],
		'description' => [
			'type' => 'string',
			'default' => '',
		],
		'Rnone' => [
			'type' => 'string',
			'default' => '',
		],
		'PinStyle' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'RTitle' => [
			'type' => 'string',
			'default' => '',	
		],
		'Ricon'  => [
			'type' => 'string',
			'default' => '',	
		],
		'RButton' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'RBtnText'  => [
			'type' => 'string',
			'default' => '',	
		],
	];
		
	$attributesOptions = array_merge( $attributesOptions );
	
	register_block_type( 'tpgb/tp-timeline-inner', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_timeline_inner_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_timeline_inner' );