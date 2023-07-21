<?php
/* Block : Expand
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_expand_callback($attributes, $desc) {
    
    $output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");

    $title  = !empty($attributes['title']) ? $attributes['title'] : '';
    $loop_content = '';
    
    $iconPosition = !empty($attributes['iconPosition']) ? $attributes['iconPosition'] : 'before';
    
    $expandText = !empty($attributes['expandText']) ? $attributes['expandText'] : '';
    $collapseText = !empty($attributes['collapseText']) ? $attributes['collapseText'] : '';
    $transDuration = !empty($attributes['transDuration']) ? $attributes['transDuration'] : '200';
    $expandContent = !empty($attributes['content']) ? $attributes['content'] : '';
    $titleTag = !empty($attributes['titleTag']) ? $attributes['titleTag'] : '';
    $readMoreIcon = $collapseIcon = $extraButtonIcon = '';
    
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

    if(!empty($attributes["readMoreIcon"])) {
        $readMoreIcon = "<span><i class='".esc_attr($attributes["readMoreIcon"])." toggle-button-icon'></i></span>";
    }
    if(!empty($attributes["collapseIcon"])) {
        $collapseIcon = "<span><i class='".esc_attr($attributes["collapseIcon"])." toggle-button-icon'></i></span>";
    }
    if(!empty($attributes["extraButtonIcon"])) {
        $extraButtonIcon = "<span><i class='".esc_attr($attributes["extraButtonIcon"])." extra-button-icon'></i></span>";
    }

    $contMaxHeightD = !empty($attributes['contentMaxHeight']['md']) ? $attributes['contentMaxHeight']['md'] : "0";
    $contMaxHeightT = !empty($attributes['contentMaxHeight']['sm']) ? $attributes['contentMaxHeight']['sm'] : $contMaxHeightD;
    $contMaxHeightM = !empty($attributes['contentMaxHeight']['xs']) ? $attributes['contentMaxHeight']['xs'] : $contMaxHeightT;

    $content = '';
    if(!empty($attributes['contentSource']) && $attributes['contentSource'] == 'customContent') {
        $content .= '<div class="tpgb-unfold-description" ><div class="tpgb-unfold-description-inner">'.wp_kses_post($expandContent).'</div></div>';
    }
    if((!empty($attributes['contentSource']) && $attributes['contentSource']=='template') && !empty($attributes['templates'])) {
        $content .= '<div class="tpgb-unfold-description">';
			$content .= '<div class="tpgb-unfold-description-inner">';
				ob_start();
					if(!empty($attributes['templates'])) {
						echo Tpgb_Library()->plus_do_block($attributes['templates']);
					}
				$loop_content .= ob_get_contents();
				ob_end_clean();
				$content .= $loop_content;
			$content .= '</div>';
        $content .= '</div>';
    }
    if(!empty($attributes['contentSource']) && $attributes['contentSource'] == 'editor') {
        $content .= '<div class="tpgb-unfold-description">';
            $content .= '<div class="tpgb-unfold-description-inner">';
                $content .= $desc;
            $content .= '</div>';
        $content .= '</div>';
    }
    
    $toggleAlignmentClass='';
    if(!empty($attributes['toggleAlignment']) && $attributes['toggleAlignment'] == 'center') {
        $toggleAlignmentClass .= 'tpgb-ca-center';
    }
    if(!empty($attributes['contentSource']) && $attributes['contentSource']=='editor'){
        $toggleAlignmentClass .= ' tpgb-unfold-editor';
    }
	
    $dataSetting =[];
	$dataSetting['id'] = 'tpgb-block-'.esc_attr($block_id);
	$dataSetting['iconPos'] = esc_attr($iconPosition);
	$dataSetting['readmore'] = (!empty($expandText)) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($expandText) : '';
	$dataSetting['readless'] = (!empty($collapseText)) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($collapseText) : '';
	$dataSetting['readmoreIcon'] = $readMoreIcon;
	$dataSetting['readlessIcon'] = $collapseIcon;
	$dataSetting['duration'] = esc_attr($transDuration);
	$dataSetting['maxHeight'] = esc_attr($contMaxHeightD);
	$dataSetting['maxHeightT'] = esc_attr($contMaxHeightT);
	$dataSetting['maxHeightM'] = esc_attr($contMaxHeightM);
	
	$dataSetting = htmlspecialchars(json_encode($dataSetting), ENT_QUOTES, 'UTF-8');
	
    $output = '<div class="tpgb-block-'.esc_attr($block_id).' tp-expand tpgb-unfold-wrapper '.esc_attr($toggleAlignmentClass).' '.esc_attr($blockClass).' tpgb-rel-flex" data-settings= \'' .$dataSetting. '\'>';
            
    if(!empty($title)){
        $output .= '<'.Tp_Blocks_Helper::validate_html_tag($titleTag).' class="tpgb-unfold-title">'.wp_kses_post($title).'</'.Tp_Blocks_Helper::validate_html_tag($titleTag).'>';
    }

    if(!empty($attributes['contentExpandDir']) && $attributes['contentExpandDir'] == 'above') {
        $output .= $content;
    }

    $contReadmoreIconBefore = $contReadmoreIconAfter = $contExtraIconBefore = $contExtraIconAfter = '';
    if(!empty($iconPosition) && $iconPosition == 'before') {
        $contReadmoreIconBefore = $readMoreIcon;
        $contExtraIconBefore = $extraButtonIcon;
    } else if(!empty($iconPosition) && $iconPosition == 'after') {
        $contReadmoreIconAfter = $readMoreIcon;
        $contExtraIconAfter = $extraButtonIcon;
    }

    $output .= '<div class="tpgb-unfold-last-toggle '.$attributes['toggleAlignment'].'">';
        if(!empty($attributes['toggleAlignment']) && $attributes['toggleAlignment'] != 'right') {
            $ariaLabel = (!empty($attributes['ariaLabel'])) ? esc_attr($attributes['ariaLabel']) : ((!empty($expandText)) ? esc_attr($expandText) : esc_attr__("Button", 'tpgbp'));
            $output .= '<button class="tpgb-unfold-toggle" aria-label="'.$ariaLabel.'">'.$contReadmoreIconBefore.' '.wp_kses_post($expandText).' '.$contReadmoreIconAfter.'</button>';
        }
        if(!empty($attributes['extraButton']) && $attributes['extraButton'] == true) {
            $ebText = !empty($attributes['extraButtonText']) ? $attributes['extraButtonText'] : '';
            $target = $attributes['extraButtonLink']['target'] ? ' target="_blank"' : '';
            $nofollow = $attributes['extraButtonLink']['nofollow'] ? ' rel="nofollow"' : '';
            $ariaLabelEb = (!empty($attributes['ariaLabelEb'])) ? esc_attr($attributes['ariaLabelEb']) : ((!empty($ebText)) ? esc_attr($ebText) : esc_attr__("Extra Button", 'tpgbp'));
			$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($attributes['extraButtonLink']);
            $output .= '<a class="tpgb-unfold-toggle-link" href="'.esc_url($attributes['extraButtonLink']['url']).'"' . $target . $nofollow .' '. $link_attr.' aria-label="'.$ariaLabelEb.'">'.$contExtraIconBefore.' '.wp_kses_post($ebText).' '.$contExtraIconAfter.'</a>';
        }
        if(!empty($attributes['toggleAlignment']) && $attributes['toggleAlignment'] == 'right') {
            $ariaLabel = (!empty($attributes['ariaLabel'])) ? esc_attr($attributes['ariaLabel']) : ((!empty($expandText)) ? esc_attr($expandText) : esc_attr__("Button", 'tpgbp'));
            $output .= '<button class="tpgb-unfold-toggle" aria-label="'.$ariaLabel.'">'.$contReadmoreIconBefore.' '.wp_kses_post($expandText).' '.$contReadmoreIconAfter.'</button>';
        }
    $output .= '</div>';

    if(!empty($attributes['contentExpandDir']) && $attributes['contentExpandDir'] == 'below') {
        $output .= $content;
    }
        
    $output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

function tpgb_tp_expand_render() {
    $globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
    $globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
    $globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
    $attributesOptions = [
        'block_id' => [
            'type' => 'string',
            'default' => '',
        ],
        'title' => [
            'type'=> 'string',
	        'default'=> 'About Earth',
        ],
        'contentSource' => [
            'type' => 'string',
            'default' => 'customContent',
        ],
        'content' => [
            'type' => 'string',
            'default' => 'Earth is the third planet from the Sun and the only astronomical object known to harbour life. According to radiometric dating estimation and other evidence, Earth formed over 4.5 billion years ago. Earth\'s gravity interacts with other objects in space, especially the Sun and the Moon, which is Earth\'s only natural satellite. Earth orbits around the Sun in about 365.25 days.<br/><br/>Earth\'s axis of rotation is tilted with respect to its orbital plane, producing seasons on Earth. The gravitational interaction between Earth and the Moon causes tides, stabilises Earth\'s orientation on its axis, and gradually slows its rotation. Earth is the densest planet in the Solar System and the largest and most massive of the four rocky planets.'
        ],
        'templates' => [
            'type' => 'string',
            'default' => '',	
        ],
		'backendVisi' => [
            'type' => 'boolean',
            'default' => false,
        ],
        'titleTag' => [
            'type' => 'string',
            'default' => 'div',
        ],
        'iconPosition' => [
            'type' => 'string',
            'default' => 'before',
        ],
        'expandText' => [
            'type'=> 'string',
	        'default'=> 'Read More',
        ],
        'ariaLabel' => [
            'type' => 'string',
            'default' => '',	
        ],
        'readMoreIcon' => [
            'type'=> 'string',
            'default'=> 'fas fa-angle-down',
        ],
        'collapseText' => [
            'type'=> 'string',
	        'default'=> 'Read Less',
        ],
        'collapseIcon' => [
            'type'=> 'string',
            'default'=> 'fas fa-angle-up',
        ],
        'extraButton' => [
            'type' => 'boolean',
            'default' => false,
        ],
        'extraButtonText' => [
            'type'=> 'string',
	        'default'=> 'Watch Video',
        ],
        'extraButtonLink' => [
            'type'=> 'object',
            'default'=> [
                'url' => '',
                'target' => '',
                'nofollow' => '',
            ],
        ],
        'ariaLabelEb' => [
            'type' => 'string',
            'default' => '',	
        ],
        'extraButtonIcon' => [
            'type'=> 'string',
            'default'=> 'fas fa-angle-right',
        ],
        'contentExpandDir' => [
            'type'=> 'string',
	        'default'=> 'above',
        ],
        'contentMaxHeight' => [
            'type' => 'object',
            'default' => [ 
                "md" => "100",
                "unit" => "px",
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-description{ height: {{contentMaxHeight}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'customOpacity' => [
            'type' => 'boolean',
            'default' => false,
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}:not(.fullview) .tpgb-unfold-description:after{ top: auto; }',
                ],
            ],
			'scopy' => true,
        ],
        'opacityHeight' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'customOpacity', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}}:not(.fullview) .tpgb-unfold-description:after{ min-height : {{opacityHeight}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'opacityColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                    (object) [
                    'selector' => '{{PLUS_WRAP}}:not(.fullview) .tpgb-unfold-description:after{ background: linear-gradient(rgba(255,255,255,0), {{opacityColor}} ); }',
                ],
            ],
			'scopy' => true,
        ],
        'transDuration' => [
            'type' => 'string',
            'default' => 200,
        ],
        'toggleAlignment' => [
            'type' => 'string',
            'default' => 'left',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'toggleAlignment', 'relation' => '!=', 'value' => 'right' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle{ justify-content: {{toggleAlignment}}; }',
                ],
                [
                    'condition' => [(object) ['key' => 'toggleAlignment', 'relation' => '==', 'value' => 'right' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle{ justify-content: flex-end; }',
                ],
				[
                    'condition' => [(object) ['key' => 'toggleAlignment', 'relation' => '==', 'value' => 'right' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle-link{  }',
                ],
            ],
			'scopy' => true,
        ],
        'titleMargin' => [
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
                    'condition' => [(object) ['key' => 'title', 'relation' => '!=', 'value' => '' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-title{ margin: {{titleMargin}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'titleAlign' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'title', 'relation' => '!=', 'value' => '' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-title{ text-align: {{titleAlign}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'titleTypo' => [
            'type'=> 'object',
            'default'=> (object) [
                'openTypography' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'title', 'relation' => '!=', 'value' => '' ]],
                    'selector' => '{{PLUS_WRAP}}.tp-expand.tpgb-unfold-wrapper .tpgb-unfold-title',
                ],
            ],
			'scopy' => true,
        ],
        'titleColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'title', 'relation' => '!=', 'value' => '' ]],
                    'selector' => '{{PLUS_WRAP}}.tp-expand.tpgb-unfold-wrapper .tpgb-unfold-title{ color: {{titleColor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'descMargin' => [
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-description,{{PLUS_WRAP}} .tpgb-unfold-description p{ margin: {{descMargin}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'descAlign' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-description,{{PLUS_WRAP}} .tpgb-unfold-description p{ text-align: {{descAlign}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'descTypo' => [
            'type'=> 'object',
            'default'=> (object) [
                'openTypography' => 0,
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-description,{{PLUS_WRAP}} .tpgb-unfold-description p',
                ],
            ],
			'scopy' => true,
        ],
        'descColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                    (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-description,{{PLUS_WRAP}} .tpgb-unfold-description p{ color: {{descColor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'toggleBtnMargin' => [
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle,{{PLUS_WRAP}}.fullview .tpgb-unfold-last-toggle .tpgb-unfold-toggle-link{ margin: {{toggleBtnMargin}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'toggleBtnPadding' => [
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle,{{PLUS_WRAP}}.fullview .tpgb-unfold-last-toggle .tpgb-unfold-toggle-link{ padding: {{toggleBtnPadding}}; }',
                ],
            ],
			'scopy' => true,
        ],
		'toggleWidth' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle{ width: {{toggleWidth}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'toggleBtnTypo' => [
            'type'=> 'object',
            'default'=> (object) [
                'openTypography' => 0,
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle',
                ],
            ],
			'scopy' => true,
        ],
        'toggleBtnColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                    (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle{ color: {{toggleBtnColor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'toggleBtnColorH' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                    (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle:hover{ color: {{toggleBtnColorH}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'toggleBtnBg' => [
            'type' => 'object',
            'default' => (object) [
                'openBg'=> 1,
                'bgType' => 'color',
                'videoSource' => 'local',
                'bgDefaultColor' => '#101011',
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle',
                ],
            ],
			'scopy' => true,
        ],
        'toggleBtnBgH' => [
            'type' => 'object',
            'default' => (object) [
                'openBg'=> 0,
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle:hover',
                ],
            ],
			'scopy' => true,
        ],
        'toggleBtnBorder' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 1,
                'type' => 'solid',
                'color' => '#101011',
                'width' => (object) [
                    'md' => (object) [
                        'top' => '1',
                        'left' => '1',
                        'bottom' => '1',
                        'right' => '1',
                    ],
                    "unit" => "px",
                ],
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle',
                ],
            ],
			'scopy' => true,
        ],
        'toggleBtnBorderH' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
                'type' => '',
                    'color' => '',
                'width' => (object) [
                    'md' => (object) [
                        'top' => '',
                        'left' => '',
                        'bottom' => '',
                        'right' => '',
                    ],
                ],
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle:hover',
                ],
            ],
			'scopy' => true,
        ],
        'toggleBtnBoxShadow' => [
            'type' => 'object',
            'default' => (object) [
                'openShadow' => 0,
                'inset' => 0,
                'horizontal' => 0,
                'vertical' => 4,
                'blur' => 8,
                'spread' => 0,
                'color' => "rgba(0,0,0,0.40)",
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle',
                ],
            ],
			'scopy' => true,
        ],
        'toggleBtnBoxShadowH' => [
            'type' => 'object',
            'default' => (object) [
                'openShadow' => 0,
                'inset' => 0,
                'horizontal' => 0,
                'vertical' => 4,
                'blur' => 8,
                'spread' => 0,
                'color' => "rgba(0,0,0,0.40)",
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle:hover',
                ],
            ],
			'scopy' => true,
        ],
        'toggleIconSize' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle .toggle-button-icon{ font-size: {{toggleIconSize}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'toggleIconOffsetAfter' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'iconPosition', 'relation' => '==', 'value' => 'after' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle .toggle-button-icon{ margin-left: {{toggleIconOffsetAfter}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'toggleIconOffsetBefore' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'iconPosition', 'relation' => '==', 'value' => 'before' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle .toggle-button-icon{ margin-right: {{toggleIconOffsetBefore}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'toggleIconColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                    (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle .toggle-button-icon{ color: {{toggleIconColor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'toggleIconColorH' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                    (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle .toggle-button-icon:hover{ color: {{toggleIconColorH}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'extraButtonMargin' => [
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
                    'condition' => [(object) ['key' => 'extraButton', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle-link{ margin: {{extraButtonMargin}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'extraBtnTypo' => [
            'type'=> 'object',
            'default'=> (object) [
                'openTypography' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'extraButton', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle-link',
                ],
            ],
			'scopy' => true,
        ],
        'extraBtnColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'extraButton', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-unfold-wrapper .tpgb-unfold-last-toggle .tpgb-unfold-toggle-link{ color: {{extraBtnColor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'extraBtnColorH' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'extraButton', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-unfold-wrapper .tpgb-unfold-last-toggle .tpgb-unfold-toggle-link:hover{ color: {{extraBtnColorH}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'extraBtnBg' => [
            'type' => 'object',
            'default' => (object) [
                'openBg'=> 1,
                'bgType' => 'color',
                'bgDefaultColor' => '#6f14f1',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'extraButton', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle-link',
                ],
            ],
			'scopy' => true,
        ],
        'extraBtnBgH' => [
            'type' => 'object',
            'default' => (object) [
                'openBg'=> 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'extraButton', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle-link:hover',
                ],
            ],
			'scopy' => true,
        ],
        'extraBtnBorder' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
                'type' => '',
                'color' => '',
                'width' => (object) [
                    'md' => (object) [
                        'top' => '',
                        'left' => '',
                        'bottom' => '',
                        'right' => '',
                    ],
                ],
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'extraButton', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle-link',
                ],
            ],
			'scopy' => true,
        ],
        'extraBtnBorderH' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
                'type' => '',
                    'color' => '',
                'width' => (object) [
                    'md' => (object) [
                        'top' => '',
                        'left' => '',
                        'bottom' => '',
                        'right' => '',
                    ],
                ],
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'extraButton', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle-link:hover',
                ],
            ],
			'scopy' => true,
        ],
        'extraBtnBoxShadow' => [
            'type' => 'object',
            'default' => (object) [
                'openShadow' => 0,
                'inset' => 0,
                'horizontal' => 0,
                'vertical' => 4,
                'blur' => 8,
                'spread' => 0,
                'color' => "rgba(0,0,0,0.40)",
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'extraButton', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle-link',
                ],
            ],
			'scopy' => true,
        ],
        'extraBtnBoxShadowH' => [
            'type' => 'object',
            'default' => (object) [
                'openShadow' => 0,
                'inset' => 0,
                'horizontal' => 0,
                'vertical' => 4,
                'blur' => 8,
                'spread' => 0,
                'color' => "rgba(0,0,0,0.40)",
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'extraButton', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle-link:hover',
                ],
            ],
			'scopy' => true,
        ],
        'extraToggleIconSize' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'extraButton', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle-link .extra-button-icon{ font-size: {{extraToggleIconSize}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'extraTIconOffsetAfter' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'iconPosition', 'relation' => '==', 'value' => 'after' ], ['key' => 'extraButton', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle-link .extra-button-icon{ margin-left: {{extraTIconOffsetAfter}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'extraTIconOffsetBefore' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'iconPosition', 'relation' => '==', 'value' => 'before'], ['key' => 'extraButton', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle-link .extra-button-icon{ margin-right: {{extraTIconOffsetBefore}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'extraTIconColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'extraButton', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-unfold-last-toggle .tpgb-unfold-toggle-link .extra-button-icon{ color: {{extraTIconColor}}; }',
                ],
            ],
			'scopy' => true,
        ],
    ];

    $attributesOptions = array_merge($attributesOptions	, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);

    register_block_type( 'tpgb/tp-expand', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_expand_callback'
    ));
}
add_action( 'init', 'tpgb_tp_expand_render' );