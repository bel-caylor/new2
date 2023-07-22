<?php
/* Block : External Form Styler
 * @since : 1.1.3
 */
defined( 'ABSPATH' ) || exit;

function tpgb_external_form_styler_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
    $contactForm = (!empty($attributes['contactForm'])) ? $attributes['contactForm'] : '';
    $formType = (!empty($attributes['formType'])) ? $attributes['formType'] : 'contact-form-7';
    $titleShow = (!empty($attributes['titleShow'])) ? $attributes['titleShow'] : false;
    $outerSecStyle = (!empty($attributes['outerSecStyle'])) ? $attributes['outerSecStyle'] : 'tpgb-cf7-label';

	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	$titleShowLine = '';
	if($formType=='gravity-form' && !empty($titleShow)){
		$titleShowLine .= 'title=true description=true';
	} else if($formType=='gravity-form' && empty($titleShow)){
		$titleShowLine .= 'title=false description=false';
	}
	$cf7class = '';
	if($formType=='contact-form-7'){
		$cf7class = $outerSecStyle;
	}
	$output = '';
	$output .= '<div class="tpgb-external-form-styler tpgb-relative-block tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'">';
		if($contactForm==''){
			$output .= '<div class="tpgb-select-form-alert">'.esc_html__('Please select Form','tpgb').'</div>';
		} else {
			$sc = 'id="'.$contactForm.'"';
			$shortcode   = [];
			if($formType=='contact-form-7'){
				$shortcode[] = sprintf( '[contact-form-7 %s]', $sc );
			} else if($formType=='everest-form'){
				$shortcode[] = sprintf( '[everest_form %s]', $sc );
			} else if($formType=='gravity-form'){
				$shortcode[] = sprintf( '[gravityform %s '.$titleShowLine.']', $sc );
			} else if($formType=='ninja-form'){
				$shortcode[] = sprintf( '[ninja_form %s]', $sc );
			} else if($formType=='wp-form'){
				$shortcode[] = sprintf( '[wpforms %s]', $sc );
			}

			$shortcode_str = implode("", $shortcode);
			
			$output .='<div class="tpgb-'.esc_attr($formType).' '.esc_attr($cf7class).'">';
				$output .= do_shortcode( $shortcode_str );				
			$output .= '</div>';
		}
	$output .= '</div>';
  
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}
function tpgb_get_form_rendered(){
    $form_id = isset($_POST['form_id']) ? wp_unslash($_POST['form_id']) : '';
    $form_type = isset($_POST['form_type']) ? sanitize_text_field(wp_unslash($_POST['form_type'])) : '';
	
	if (!empty($form_id) && $form_type=='contact-form-7'){
		echo do_shortcode ( "[contact-form-7 id=".esc_attr($form_id)."]" );
	} else if(!empty($form_id) && $form_type=='everest-form'){
		echo do_shortcode ( "[everest_form id=".esc_attr($form_id)."]" );
	} else if(!empty($form_id) && $form_type=='gravity-form'){
		echo do_shortcode ( "[gravityform id=".esc_attr($form_id)." title=false description=false]" );
	} else if(!empty($form_id) && $form_type=='ninja-form'){
		echo do_shortcode ( "[ninja_form id=".esc_attr($form_id)."]" );
	} else if(!empty($form_id) && $form_type=='wp-form'){
		echo do_shortcode ( "[wpforms id=".esc_attr($form_id)."]" );
	}
    exit();
}
add_action('wp_ajax_tpgb_external_form_ajax', 'tpgb_get_form_rendered');
/**
 * Render for the server-side
 */
function tpgb_external_form_styler() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'formType' => [
			'type' => 'string',
			'default' => 'contact-form-7',	
		],
		'contactForm' => [
			'type' => 'string',
			'default' => ''
		],
		'titleShow' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'Alignment' => [
			'type' => 'object',
			'default' => 'center',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-external-form-styler{ text-align: {{Alignment}}; }',
				],
			],
			'scopy' => true,
		],
		'outerSecStyle' => [
			'type' => 'string',
			'default' => 'tpgb-cf7-label',
		],
		
		/* Label Field start*/
		'labelTypo' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-label form.wpcf7-form  label,{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-custom form.wpcf7-form .tpgb-cf7-outer',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .evf-field-label .evf-label',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield_label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_full label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_left label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_right label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .address_city label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .address_zip label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .address_country label',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-form-layout .nf-field-label label',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container label.wpforms-field-label',
				],
			],
			'scopy' => true,
		],
		'subLabelTypo' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .name_prefix label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .name_first label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .name_middle label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .name_last label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .name_suffix label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container.ginput_container_email label',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field-sublabel',
				],
			],
			'scopy' => true,
		],
		'labelNColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-label form.wpcf7-form  label,{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-custom form.wpcf7-form .tpgb-cf7-outer{color: {{labelNColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .evf-field-label .evf-label{color: {{labelNColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield_label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_full label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_left label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_right label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .address_city label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .address_zip label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .address_country label{color: {{labelNColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-label label{color: {{labelNColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container label.wpforms-field-label{color: {{labelNColor}};}',
				],
			],
			'scopy' => true,
		],
		'subLabelNColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .name_prefix label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .name_first label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .name_middle label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .name_last label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .name_suffix label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container.ginput_container_email label{color: {{subLabelNColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field-label-inline,{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field-sublabel{color: {{subLabelNColor}};}',
				],
			],
			'scopy' => true,
		],
		'maxCharColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .charleft.ginput_counter{color: {{maxCharColor}};}',
				],
			],
			'scopy' => true,
		],
		'labelDescColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .form-row .everest-forms-field-label-inline,{{PLUS_WRAP}} .tpgb-everest-form .form-row .evf-field-description{color: {{labelDescColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield_description, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper span.gf_step_number, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gsection_description, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper span.ginput_product_price_label, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper span.ginput_quantity_label{color: {{labelDescColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-description,{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-description p{color: {{labelDescColor}};}',
				],
			],
			'scopy' => true,
		],
		'reqSymColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row label .required{color: {{reqSymColor}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield_required{color: {{reqSymColor}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .ninja-forms-req-symbol{color: {{reqSymColor}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-required-label{color: {{reqSymColor}} !important;}',
				],
			],
			'scopy' => true,
		],
		'progressBarTSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper h3.gf_progressbar_title{ font-size: {{progressBarTSize}}; }',
				],
			],
			'scopy' => true,
		],
		'progressBarTColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper h3.gf_progressbar_title{color: {{progressBarTColor}};}',
				],
			],
			'scopy' => true,
		],
		'progressBarBdrSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gf_progressbar{ padding: {{progressBarBdrSize}}; }',
				],
			],
			'scopy' => true,
		],
		'progressBarBdrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gf_progressba{background-color: {{progressBarBdrColor}};}',
				],
			],
			'scopy' => true,
		],
		'priceColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_product_price,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_shipping_price,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper span.ginput_total{color: {{priceColor}};}',
				],
			],
			'scopy' => true,
		],
		'consentGrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield_consent_label{color: {{consentGrColor}};}',
				],
			],
			'scopy' => true,
		],
		'labelHColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-label form.wpcf7-form  label:hover,{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-custom form.wpcf7-form .tpgb-cf7-outer:hover{color: {{labelHColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .evf-field-label .evf-label:hover{color: {{labelHColor}};}',
				],
			],
			'scopy' => true,
		],
		/* Label Field end*/
		
		/* Description Field(wp-form) start*/
		'wpDescTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field-description',
				],
			],
			'scopy' => true,
		],
		'wpDescPadding' => [
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
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field-description{padding: {{wpDescPadding}};}',
				],
			],
			'scopy' => true,
		],
		'wpDescMargin' => [
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
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field-description{margin: {{wpDescMargin}};}',
				],
			],
			'scopy' => true,
		],
		'wpDescColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field-description{ color: {{wpDescColor}}; }',
				],
			],
			'scopy' => true,
		],
		'wpDescBG' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field-description{ background: {{wpDescBG}}; }',
				],
			],
			'scopy' => true,
		],
		'wpDescBdr' => [
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
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "px",
				],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field-description',
				],
			],
			'scopy' => true,
		],
		'wpDescBRadius' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field-description{border-radius: {{wpDescBRadius}};}',
				],
			],
			'scopy' => true,
		],
		/* Description Field(wp-form) end*/
		
		/* Form Heading Field start*/
		'formHeadTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-form-title h3',
				],
			],
			'scopy' => true,
		],
		'formHeadColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-form-title h3{ color: {{formHeadColor}}; }',
				],
			],
			'scopy' => true,
		],
		/* Form Heading Field end*/
		
		/* Hint Field start*/
		'hintIconColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form span.fa.fa-info-circle.nf-help:before{ color: {{hintIconColor}}; }',
				],
			],
			'scopy' => true,
		],
		'hintDescColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-description{ color: {{hintDescColor}}; }',
				],
			],
			'scopy' => true,
		],
		/* Hint Field end*/
		
		/* Input Field start*/
		'inputTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file)',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form input[type="text"], {{PLUS_WRAP}} .tpgb-everest-form input[type="email"], {{PLUS_WRAP}} .tpgb-everest-form input[type="number"], {{PLUS_WRAP}} .tpgb-everest-form input[type="url"], {{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row select',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="text"], {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper select,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="email"], {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="tel"], {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="url"]',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="text"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="email"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="number"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element select',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container input[type="text"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container input[type="email"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container input[type="number"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container select',
				],
			],
			'scopy' => true,
		],
		'inputPHcolor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file)::placeholder{ color: {{inputPHcolor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form input::-webkit-input-placeholder, {{PLUS_WRAP}} .tpgb-everest-form  email::-webkit-input-placeholder, {{PLUS_WRAP}} .tpgb-everest-form  number::-webkit-input-placeholder, {{PLUS_WRAP}} .tpgb-everest-form  select::-webkit-input-placeholder, {{PLUS_WRAP}} .tpgb-everest-form  url::-webkit-input-placeholder{ color: {{inputPHcolor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input::placeholder, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper  select::placeholder{ color: {{inputPHcolor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-form-content input::placeholder, {{PLUS_WRAP}} .tpgb-ninja-form .nf-form-content  email::placeholder, {{PLUS_WRAP}} .tpgb-ninja-form .nf-form-content  number::placeholder, {{PLUS_WRAP}} .tpgb-ninja-form .nf-form-content  select::placeholder{ color: {{inputPHcolor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container input::placeholder, {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container  email::placeholder, {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container  number::placeholder, {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container  select::placeholder{ color: {{inputPHcolor}}; }',
				],
			],
			'scopy' => true,
		],
		'inputPadding' => [
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
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file){padding: {{inputPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form input[type="text"], {{PLUS_WRAP}} .tpgb-everest-form input[type="email"], {{PLUS_WRAP}} .tpgb-everest-form input[type="number"], {{PLUS_WRAP}} .tpgb-everest-form input[type="url"], {{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row select{padding: {{inputPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container input[type="text"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container select,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="email"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="tel"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="url"]{padding: {{inputPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .textbox-wrap:not(.submit-wrap) .nf-field-element .ninja-forms-field, {{PLUS_WRAP}} .tpgb-ninja-form .firstname-wrap .nf-field-element .ninja-forms-field, {{PLUS_WRAP}} .tpgb-ninja-form .lastname-wrap .nf-field-element .ninja-forms-field, {{PLUS_WRAP}} .tpgb-ninja-form .email-wrap .nf-field-element .ninja-forms-field, {{PLUS_WRAP}} .tpgb-ninja-form .number-wrap .nf-field-element .ninja-forms-field, {{PLUS_WRAP}} .tpgb-ninja-form .date-wrap .nf-field-element .ninja-forms-field, {{PLUS_WRAP}} .tpgb-ninja-form .city-wrap .nf-field-element .ninja-forms-field, {{PLUS_WRAP}} .tpgb-ninja-form .address-wrap .nf-field-element .ninja-forms-field, {{PLUS_WRAP}} .tpgb-ninja-form .list-select-wrap .nf-field-element .ninja-forms-field{padding: {{inputPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="text"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="email"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="number"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field select{padding: {{inputPadding}};}',
				],
			],
			'scopy' => true,
		],
		'inputMargin' => [
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
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file){margin: {{inputMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form input[type="text"], {{PLUS_WRAP}} .tpgb-everest-form input[type="email"], {{PLUS_WRAP}} .tpgb-everest-form input[type="number"], {{PLUS_WRAP}} .tpgb-everest-form input[type="url"], {{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row select{margin: {{inputMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container input[type="text"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container select,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="email"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="tel"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="url"]{margin: {{inputMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .textbox-wrap:not(.submit-wrap) .nf-field-element .ninja-forms-field, {{PLUS_WRAP}} .tpgb-ninja-form .firstname-wrap .nf-field-element .ninja-forms-field, {{PLUS_WRAP}} .tpgb-ninja-form .lastname-wrap .nf-field-element .ninja-forms-field, {{PLUS_WRAP}} .tpgb-ninja-form .email-wrap .nf-field-element .ninja-forms-field, {{PLUS_WRAP}} .tpgb-ninja-form .number-wrap .nf-field-element .ninja-forms-field, {{PLUS_WRAP}} .tpgb-ninja-form .date-wrap .nf-field-element .ninja-forms-field, {{PLUS_WRAP}} .tpgb-ninja-form .city-wrap .nf-field-element .ninja-forms-field, {{PLUS_WRAP}} .tpgb-ninja-form .address-wrap .nf-field-element .ninja-forms-field, {{PLUS_WRAP}} .tpgb-ninja-form .list-select-wrap .nf-field-element .ninja-forms-field{margin: {{inputMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="text"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="email"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="number"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field select{margin: {{inputMargin}};}',
				],
			],
			'scopy' => true,
		],
		'inputFNColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file){color: {{inputFNColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form input[type="text"], {{PLUS_WRAP}} .tpgb-everest-form input[type="email"], {{PLUS_WRAP}} .tpgb-everest-form input[type="number"], {{PLUS_WRAP}} .tpgb-everest-form input[type="url"], {{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row select{color: {{inputFNColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container input[type="text"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container select,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="email"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="tel"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="url"]{color: {{inputFNColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="text"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="email"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="number"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="tel"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element select{color: {{inputFNColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="text"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="email"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="number"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field select{color: {{inputFNColor}};}',
				],
			],
			'scopy' => true,
		],
		'inputFNBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file)'
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form input[type="text"], {{PLUS_WRAP}} .tpgb-everest-form input[type="email"], {{PLUS_WRAP}} .tpgb-everest-form input[type="number"], {{PLUS_WRAP}} .tpgb-everest-form input[type="url"], {{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row select',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container input[type="text"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container select,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="email"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="tel"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="url"]',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="text"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="email"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="number"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="tel"], {{PLUS_WRAP}} .tpgb-ninja-form .list-select-wrap .nf-field-element select',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="text"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="email"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="number"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field select',
				],
			],
			'scopy' => true,
		],
		'inputFFColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file):focus{color: {{inputFFColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form input[type="text"]:focus, {{PLUS_WRAP}} .tpgb-everest-form input[type="email"]:focus, {{PLUS_WRAP}} .tpgb-everest-form input[type="number"]:focus, {{PLUS_WRAP}} .tpgb-everest-form input[type="url"]:focus, {{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row select:focus{color: {{inputFFColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container input[type="text"]:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container select:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="email"]:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="tel"]:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="url"]:focus{color: {{inputFFColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="text"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="email"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="number"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="tel"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .list-select-wrap:focus .nf-field-element select{color: {{inputFFColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="text"]:focus, {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="email"]:focus, {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="number"]:focus, {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field select:focus{color: {{inputFFColor}};}',
				],
			],
			'scopy' => true,
		],
		'inputFFBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file):focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form input[type="text"]:focus, {{PLUS_WRAP}} .tpgb-everest-form input[type="email"]:focus, {{PLUS_WRAP}} .tpgb-everest-form input[type="number"]:focus, {{PLUS_WRAP}} .tpgb-everest-form input[type="url"]:focus, {{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row select:focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container input[type="text"]:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container select:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="email"]:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="tel"]:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="url"]:focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="text"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="email"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="number"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="tel"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .list-select-wrap:focus .nf-field-element select',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="text"]:focus, {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="email"]:focus, {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="number"]:focus, {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field select:focus',
				],
			],
			'scopy' => true,
		],
		'inputNBdr' => [
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
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "px",
				],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file)',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form input[type="text"], {{PLUS_WRAP}} .tpgb-everest-form input[type="email"], {{PLUS_WRAP}} .tpgb-everest-form input[type="number"], {{PLUS_WRAP}} .tpgb-everest-form input[type="url"], {{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row select',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container input[type="text"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container select,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="email"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="tel"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="url"]',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="text"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="email"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="number"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="tel"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element select',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="text"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="email"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="number"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field select',
				],
			],
			'scopy' => true,
		],
		'inputFBdr' => [
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
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "px",
				],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file):focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form input[type="text"]:focus, {{PLUS_WRAP}} .tpgb-everest-form input[type="email"]:focus, {{PLUS_WRAP}} .tpgb-everest-form input[type="number"]:focus, {{PLUS_WRAP}} .tpgb-everest-form input[type="url"]:focus, {{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row select:focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container input[type="text"]:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container select:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="email"]:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="tel"]:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="url"]:focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="text"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="email"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="number"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="tel"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .list-select-wrap:focus .nf-field-element select',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="text"]:focus, {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="email"]:focus, {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="number"]:focus, {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field select:focus',
				],
			],
			'scopy' => true,
		],
		'inputNBRadius' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file){border-radius: {{inputNBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form input[type="text"], {{PLUS_WRAP}} .tpgb-everest-form input[type="email"], {{PLUS_WRAP}} .tpgb-everest-form input[type="number"], {{PLUS_WRAP}} .tpgb-everest-form input[type="url"], {{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row select{border-radius: {{inputNBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container input[type="text"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container select,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="email"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="tel"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="url"]{border-radius: {{inputNBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="text"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="email"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="number"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="tel"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element select{border-radius: {{inputNBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="text"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="email"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="number"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field select{border-radius: {{inputNBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'inputFBRadius' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file):focus{border-radius: {{inputFBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form input[type="text"]:focus, {{PLUS_WRAP}} .tpgb-everest-form input[type="email"]:focus, {{PLUS_WRAP}} .tpgb-everest-form input[type="number"]:focus, {{PLUS_WRAP}} .tpgb-everest-form input[type="url"]:focus, {{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row select:focus{border-radius: {{inputFBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container input[type="text"]:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container select:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="email"]:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="tel"]:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="url"]:focus{border-radius: {{inputFBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="text"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="email"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="number"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="tel"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .list-select-wrap:focus .nf-field-element select{border-radius: {{inputFBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="text"]:focus, {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="email"]:focus, {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="number"]:focus, {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field select:focus{border-radius: {{inputFBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'inputNBShadow' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file)',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form input[type="text"], {{PLUS_WRAP}} .tpgb-everest-form input[type="email"], {{PLUS_WRAP}} .tpgb-everest-form input[type="number"], {{PLUS_WRAP}} .tpgb-everest-form input[type="url"], {{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row select',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container input[type="text"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container select,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="email"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="tel"],{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="url"]',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="text"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="email"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="number"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="tel"], {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element select',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="text"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="email"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="number"], {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field select',
				],
			],
			'scopy' => true,
		],
		'inputFBShadow' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file):focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form input[type="text"]:focus, {{PLUS_WRAP}} .tpgb-everest-form input[type="email"]:focus, {{PLUS_WRAP}} .tpgb-everest-form input[type="number"]:focus, {{PLUS_WRAP}} .tpgb-everest-form input[type="url"]:focus, {{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row select:focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container input[type="text"]:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container select:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="email"]:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="tel"]:focus,{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="url"]:focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="text"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="email"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="number"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element input[type="tel"]:focus, {{PLUS_WRAP}} .tpgb-ninja-form .list-select-wrap:focus .nf-field-element select',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="text"]:focus, {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="email"]:focus, {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field input[type="number"]:focus, {{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-field select:focus',
				],
			],
			'scopy' => true,
		],
		/* Input Field end*/
		
		/* TextArea Field start*/
		'textATypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 textarea.wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file)',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row textarea',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container textarea',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element textarea',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea',
				],
			],
			'scopy' => true,
		],
		'textAPHcolor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 textarea.wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file)::placeholder{ color: {{textAPHcolor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form textarea::placeholder{ color: {{textAPHcolor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container textarea::placeholder{ color: {{textAPHcolor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-form-content  textarea::placeholder{ color: {{textAPHcolor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea::placeholder{ color: {{textAPHcolor}}; }',
				],
			],
			'scopy' => true,
		],
		'textAPadding' => [
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
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 textarea.wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file){padding: {{textAPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row textarea{padding: {{textAPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container textarea{padding: {{textAPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea{padding: {{textAPadding}};}',
				],
			],
			'scopy' => true,
		],
		'textAMargin' => [
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
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 textarea.wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file){margin: {{textAMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row textarea{margin: {{textAMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container textarea{margin: {{textAMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element textarea{margin: {{textAMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea{margin: {{textAMargin}};}',
				],
			],
			'scopy' => true,
		],
		'textANColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 textarea.wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file){color: {{textANColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row textarea{color: {{textANColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container textarea{color: {{textANColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element textarea{color: {{textANColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea{color: {{textANColor}};}',
				],
			],
			'scopy' => true,
		],
		'textANBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 textarea.wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file)'
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row textarea',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container textarea',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element textarea',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea',
				],
			],
			'scopy' => true,
		],
		'textAFColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 textarea.wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file):focus{color: {{textAFColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row textarea:focus{color: {{textAFColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container textarea:focus{color: {{textAFColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element textarea:focus{color: {{textAFColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea:focus{color: {{textAFColor}};}',
				],
			],
			'scopy' => true,
		],
		'textAFBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 textarea.wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file):focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row textarea:focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container textarea:focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element textarea:focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea:focus',
				],
			],
			'scopy' => true,
		],
		'textANBdr' => [
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
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "px",
				],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 textarea.wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file)',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row textarea',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container textarea',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element textarea',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea',
				],
			],
			'scopy' => true,
		],
		'textAFBdr' => [
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
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "px",
				],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 textarea.wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file):focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row textarea:focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container textarea:focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element textarea:focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea:focus',
				],
			],
			'scopy' => true,
		],
		'textANBRadius' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 textarea.wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file){border-radius: {{textANBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row textarea{border-radius: {{textANBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container textarea{border-radius: {{textANBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element textarea{border-radius: {{textANBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea{border-radius: {{textANBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'textAFBRadius' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} tpgb-contact-form-7 textarea.wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file):focus{border-radius: {{textAFBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row textarea:focus{border-radius: {{textAFBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container textarea:focus{border-radius: {{textAFBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element textareaa:focus{border-radius: {{textAFBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea:focus{border-radius: {{textAFBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'textANBShadow' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 textarea.wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file)',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row textarea',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container textarea',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element textarea',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea',
				],
			],
			'scopy' => true,
		],
		'textAFBShadow' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 textarea.wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-file):focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field-container .evf-frontend-row textarea:focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container textarea:focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-element textarea:focus',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea:focus',
				],
			],
			'scopy' => true,
		],
		/* TextArea Field end*/
		
		/*Select Field(gravity-form) start*/
		'heightAuto' => [
			'type' => 'boolean',
			'default' => false,	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ], ['key' => 'heightAuto', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container select{height: auto}',
				],
			],
			'scopy' => true,
		],
		'selectPadding' => [
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
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_container select{padding: {{selectPadding}};}',
				],
			],
			'scopy' => true,
		],
		/*Select Field(gravity-form) end*/
		
		/* CheckBox Field start*/
		'checkBTypo' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap .input__checkbox_btn',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .evf-field-checkbox label.everest-forms-field-label-inline',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield_checkbox li label',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap.listcheckbox-wrap .nf-field-element label',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-field.wpforms-field-checkbox li label,{{PLUS_WRAP}} .wpforms-field.wpforms-field-checkbox li.wpforms-image-choices-item .wpforms-image-choices-label',
				],
			],
			'scopy' => true,
		],
		'checkIconSize' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .evf-field-checkbox .everest-forms-field-label-inline .tpgb-everest-check{font-size: {{checkIconSize}}px;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_checkbox .tpgb-gravity-check{font-size: {{checkIconSize}}px;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap.listcheckbox-wrap .nf-field-element label:before,{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap.checkbox-wrap .nf-field-label label:before{font-size: {{checkIconSize}}px;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-field.wpforms-field-checkbox li label .tpgb-wp-check{font-size: {{checkIconSize}}px;}',
				],
			],
			'scopy' => true,
		],
		'checkBTextColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .evf-field-checkbox label.everest-forms-field-label-inline{color: {{checkBTextColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield_checkbox li label{color: {{checkBTextColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap.listcheckbox-wrap .nf-field-element label{color: {{checkBTextColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-field.wpforms-field-checkbox li label,{{PLUS_WRAP}} .wpforms-field.wpforms-field-checkbox li.wpforms-image-choices-item .wpforms-image-choices-label{color: {{checkBTextColor}};}',
				],
			],
			'scopy' => true,
		],
		'checkBUnCheckedColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .evf-field-checkbox .everest-forms-field-label-inline .tpgb-everest-check{color: {{checkBUnCheckedColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_checkbox .tpgb-gravity-check{color: {{checkBUnCheckedColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .checkbox-wrap .nf-field-element li label:before,
					{{PLUS_WRAP}} .tpgb-ninja-form .listcheckbox-wrap .nf-field-element li label:before{color: {{checkBUnCheckedColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-field.wpforms-field-checkbox li:not(.wpforms-selected) label .tpgb-wp-check{color: {{checkBUnCheckedColor}};}',
				],
			],
			'scopy' => true,
		],
		'checkBCheckedColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .input__checkbox_btn .toggle-button__icon .tpgb-checkcf7-icon{color: {{checkBCheckedColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-form .evf-field-checkbox input[type=checkbox]:checked + .everest-forms-field-label-inline .tpgb-everest-check{color: {{checkBCheckedColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_checkbox input[type=checkbox]:checked + label .tpgb-gravity-check{color: {{checkBCheckedColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .checkbox-wrap .nf-field-element label.nf-checked-label:before, {{PLUS_WRAP}} .tpgb-ninja-form .checkbox-wrap .nf-field-label label.nf-checked-label:before, {{PLUS_WRAP}} .tpgb-ninja-form .listcheckbox-wrap .nf-field-element label.nf-checked-label:before, {{PLUS_WRAP}} .tpgb-ninja-form .listcheckbox-wrap .nf-field-label label.nf-checked-label:before{color: {{checkBCheckedColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-field.wpforms-field-checkbox li.wpforms-selected label .tpgb-wp-check{color: {{checkBCheckedColor}};}',
				],
			],
			'scopy' => true,
		],
		'checkBUnCheckedBG' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .input__checkbox_btn .toggle-button__icon{background: {{checkBUnCheckedBG}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .evf-field-checkbox .everest-forms-field-label-inline .tpgb-everest-check {background: {{checkBUnCheckedBG}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_checkbox .tpgb-gravity-check {background: {{checkBUnCheckedBG}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .checkbox-wrap .nf-field-element li label:before,
					{{PLUS_WRAP}} .tpgb-ninja-form .listcheckbox-wrap .nf-field-element li label:before {background: {{checkBUnCheckedBG}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-field.wpforms-field-checkbox li:not(.wpforms-selected) label .tpgb-wp-check {background: {{checkBUnCheckedBG}};}',
				],
			],
			'scopy' => true,
		],
		'checkBCheckedBG' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .input__checkbox_btn .toggle-button__icon .tpgb-checkcf7-icon{background: {{checkBCheckedBG}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-form .evf-field-checkbox input[type=checkbox]:checked + .everest-forms-field-label-inline .tpgb-everest-check{background: {{checkBCheckedBG}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_checkbox input[type=checkbox]:checked + label .tpgb-gravity-check{background: {{checkBCheckedBG}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .checkbox-wrap .nf-field-element label.nf-checked-label:before, {{PLUS_WRAP}} .tpgb-ninja-form .checkbox-wrap .nf-field-label label.nf-checked-label:before, {{PLUS_WRAP}} .tpgb-ninja-form .listcheckbox-wrap .nf-field-element label.nf-checked-label:before, {{PLUS_WRAP}} .tpgb-ninja-form .listcheckbox-wrap .nf-field-label label.nf-checked-label:before{background: {{checkBCheckedBG}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-field.wpforms-field-checkbox li.wpforms-selected label .tpgb-wp-check{background: {{checkBCheckedBG}};}',
				],
			],
			'scopy' => true,
		],             
		'checkBBdr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,	// 0 Or 1
				'type' => 'solid',	// 'solid' OR 'dotted' OR 'dashed' OR 'double'
				'color' => '',	// "#000"
				'width' => (object) [
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "",
				],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .input__checkbox_btn .toggle-button__icon',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .evf-field-checkbox .everest-forms-field-label-inline .tpgb-everest-check',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_checkbox .tpgb-gravity-check',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .checkbox-wrap .nf-field-element li label:before,
					{{PLUS_WRAP}} .tpgb-ninja-form .listcheckbox-wrap .nf-field-element li label:before',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-field.wpforms-field-checkbox li label .tpgb-wp-check,
					{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern li label, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic li label',
				],
			],
			'scopy' => true,
		],
		'checkBBRadius' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .input__checkbox_btn .toggle-button__icon{border-radius: {{checkBBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .evf-field-checkbox .everest-forms-field-label-inline .tpgb-everest-check{border-radius: {{checkBBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_checkbox .tpgb-gravity-check{border-radius: {{checkBBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .checkbox-wrap .nf-field-element li label:before,
					{{PLUS_WRAP}} .tpgb-ninja-form .listcheckbox-wrap .nf-field-element li label:before{border-radius: {{checkBBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-field.wpforms-field-checkbox li label .tpgb-wp-check,
					{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern li label, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic li label{border-radius: {{checkBBRadius}};}',
				],
			],
			'scopy' => true,
		],
		//Img Choice Style
		'wpImgChoiceStyle' => [
			'type' => 'boolean',
			'default' => false,	
			'scopy' => true,
		],
		'wpImgCPadding' => [
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
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ],['key' => 'wpImgChoiceStyle', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern label, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic label, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-none label{padding: {{wpImgCPadding}};}',
				],
			],
			'scopy' => true,
		],
		'imgCBNormal' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ],['key' => 'wpImgChoiceStyle', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern label{background: {{imgCBNormal}};} {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic label{border: solid {{imgCBNormal}};}',
				],
			],
			'scopy' => true,
		],
		'imgCBSelected' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ],['key' => 'wpImgChoiceStyle', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern li.wpforms-selected label{background: {{imgCBSelected}};} {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic li.wpforms-selected label{border: solid {{imgCBSelected}};}',
				],
			],
			'scopy' => true,
		],
		'imgCheckedColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ],['key' => 'wpImgChoiceStyle', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern .wpforms-image-choices-image:after, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic .wpforms-image-choices-image:after{color: {{imgCheckedColor}};}',
				],
			],
			'scopy' => true,
		],
		'imgCheckedBG' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ],['key' => 'wpImgChoiceStyle', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern .wpforms-image-choices-image:after, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic .wpforms-image-choices-image:after{background: {{imgCheckedBG}};}',
				],
			],
			'scopy' => true,
		],
		'imgIconSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern .wpforms-image-choices-image:after, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic .wpforms-image-choices-image:after{ font-size: {{imgIconSize}}; }',
				],
			],
			'scopy' => true,
		],
		'imgIconBGSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern .wpforms-image-choices-image:after, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic .wpforms-image-choices-image:after{ width: {{imgIconBGSize}}; height: {{imgIconBGSize}}; line-height: {{imgIconBGSize}}; }',
				],
			],
			'scopy' => true,
		],
		/* CheckBox Field end*/
		
		/* Radio Field start*/
		'radioTypo' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap .input__radio_btn',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .evf-field-radio label.everest-forms-field-label-inline',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield_radio li label',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap.listradio-wrap .nf-field-element label',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-field.wpforms-field-radio li label,{{PLUS_WRAP}} .wpforms-field.wpforms-field-radio li.wpforms-image-choices-item .wpforms-image-choices-label',
				],
			],
			'scopy' => true,
		],
		'radioIconSize' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .evf-field-radio .everest-forms-field-label-inline .tpgb-everest-radio{font-size: {{radioIconSize}}px;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_radio .tpgb-gravity-radio{font-size: {{radioIconSize}}px;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-field.wpforms-field-radio li label .tpgb-wp-radio{font-size: {{radioIconSize}}px;}',
				],
			],
			'scopy' => true,
		],
		'radioBTextColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .evf-field-radio label.everest-forms-field-label-inline{color: {{radioBTextColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield_radio li label{color: {{radioBTextColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap.listradio-wrap .nf-field-element label{color: {{radioBTextColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-field.wpforms-field-radio li label, {{PLUS_WRAP}} .wpforms-field.wpforms-field-radio li.wpforms-image-choices-item .wpforms-image-choices-label{color: {{radioBTextColor}};}',
				],
			],
			'scopy' => true,
		],
		'radioBUnCheckedColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .evf-field-radio .everest-forms-field-label-inline .tpgb-everest-radio{color: {{radioBUnCheckedColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_radio .tpgb-gravity-radio{color: {{radioBUnCheckedColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .listradio-wrap .nf-field-element label:after{color: {{radioBUnCheckedColor}};} {{PLUS_WRAP}} .tpgb-ninja-form .listradio-wrap .nf-field-element label:after{border: 2px solid {{radioBUnCheckedColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-field.wpforms-field-radio li:not(.wpforms-selected) label .tpgb-wp-radio{color: {{radioBUnCheckedColor}};}',
				],
			],
			'scopy' => true,
		],
		'radioCheckColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .input__radio_btn .toggle-button__icon .tpgb-radiocf7-icon{color: {{radioCheckColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .everest-form .evf-field-radio input[type=radio]:checked + .everest-forms-field-label-inline .tpgb-everest-radio{color: {{radioCheckColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .gform_wrapper .ginput_container_radio input[type=radio]:checked + label .tpgb-gravity-radio{color: {{radioCheckColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .listradio-wrap .nf-field-element label.nf-checked-label:before{background: {{radioCheckColor}};} {{PLUS_WRAP}} .listradio-wrap .nf-field-element label.nf-checked-label:after{border-color: {{radioCheckColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-field.wpforms-field-radio li.wpforms-selected label .tpgb-wp-radio{color: {{radioCheckColor}};}',
				],
			],
			'scopy' => true,
		],
		'radioUncheckBG' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 label.input__radio_btn .toggle-button__icon{background: {{radioUncheckBG}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .evf-field-radio .everest-forms-field-label-inline .tpgb-everest-radio{background: {{radioUncheckBG}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_radio .tpgb-gravity-radio{background: {{radioUncheckBG}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-field.wpforms-field-radio li:not(.wpforms-selected) label .tpgb-wp-radio{background: {{radioUncheckBG}};}',
				],
			],
			'scopy' => true,
		],
		'radioCheckBG' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .input__radio_btn .toggle-button__icon .tpgb-radiocf7-icon{background: {{radioCheckBG}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .everest-form .evf-field-radio input[type=radio]:checked + .everest-forms-field-label-inline .tpgb-everest-radio{background: {{radioCheckBG}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .gform_wrapper .ginput_container_radio input[type=radio]:checked + label .tpgb-gravity-radio{background: {{radioCheckBG}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-field.wpforms-field-radio li.wpforms-selected label .tpgb-wp-radio{background: {{radioCheckBG}};}',
				],
			],
			'scopy' => true,
		],         
		'radioBdr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => 'solid',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "px",
				],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .input__radio_btn .toggle-button__icon',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .evf-field-radio .everest-forms-field-label-inline .tpgb-everest-radio',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_radio .tpgb-gravity-radio',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-field.wpforms-field-radio li label .tpgb-wp-radio, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern li label, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic li label',
				],
			],
			'scopy' => true,
		],         
		'radioBRadius' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .input__radio_btn .toggle-button__icon{border-radius: {{radioBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .evf-field-radio .everest-forms-field-label-inline .tpgb-everest-radio{border-radius: {{radioBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_radio .tpgb-gravity-radio{border-radius: {{radioBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-field.wpforms-field-radio li label .tpgb-wp-radio, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern li label, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic li label{border-radius: {{radioBRadius}};}',
				],
			],
			'scopy' => true,
		],
		//Img Choice Style
		'wpImgChoiceRadioStyle' => [
			'type' => 'boolean',
			'default' => false,	
			'scopy' => true,
		],
		'wpImgRPadding' => [
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
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ],['key' => 'wpImgChoiceRadioStyle', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern label, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-classic label, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-none label{padding: {{wpImgRPadding}};}',
				],
			],
			'scopy' => true,
		],
		'imgRNormal' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ],['key' => 'wpImgChoiceRadioStyle', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern label{background: {{imgRNormal}};} {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-classic label{border: solid {{imgRNormal}};}',
				],
			],
			'scopy' => true,
		],
		'imgRSelected' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ],['key' => 'wpImgChoiceRadioStyle', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern li.wpforms-selected label{background: {{imgRSelected}};} {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-classic li.wpforms-selected label{border: solid {{imgRSelected}};}',
				],
			],
			'scopy' => true,
		],
		'imgRadioColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ],['key' => 'wpImgChoiceRadioStyle', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern .wpforms-image-choices-image:after, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-classic .wpforms-image-choices-image:after{color: {{imgRadioColor}};}',
				],
			],
			'scopy' => true,
		],
		'imgRadioBG' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ],['key' => 'wpImgChoiceRadioStyle', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern .wpforms-image-choices-image:after, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-classic .wpforms-image-choices-image:after{background: {{imgRadioBG}};}',
				],
			],
			'scopy' => true,
		],
		'imgRadioIconSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ],['key' => 'wpImgChoiceRadioStyle', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern .wpforms-image-choices-image:after, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-classic .wpforms-image-choices-image:after{ font-size: {{imgRadioIconSize}}; }',
				],
			],
			'scopy' => true,
		],
		'imgRadioIconBGSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ],['key' => 'wpImgChoiceRadioStyle', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern .wpforms-image-choices-image:after, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-classic .wpforms-image-choices-image:after{ width: {{imgRadioIconBGSize}}; height: {{imgRadioIconBGSize}}; line-height: {{imgRadioIconBGSize}}; }',
				],
			],
			'scopy' => true,
		],
		/* Radio Field end*/
		
		/* File Field start*/
		'fileTypo' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-file + .input__file_btn',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_fileupload input[type="file"]',
				],
			],
			'scopy' => true,
		],
		'filePadding' => [
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
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_fileupload{padding: {{filePadding}};}',
				],
			],
			'scopy' => true,
		],
		'fileMargin' => [
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
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_fileupload{margin: {{fileMargin}};}',
				],
			],
			'scopy' => true,
		],
		'fileMinHeight' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap.your-file.cf7-style-file{min-height: {{fileMinHeight}}px;}',
				],
			],
			'scopy' => true,
		],
		'fileAlign' => [
			'type' => 'string',
			'default' => 'center',
			'style' => [
				(object) [  
					'condition' => [ (object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ],['key' => 'fileAlign', 'relation' => '==', 'value' => 'left'],],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap.cf7-style-file {-webkit-justify-content: flex-start; -ms-flex-pack: flex-start; justify-content: flex-start;} {{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap.cf7-style-file span{text-align: {{fileAlign}};}',
				],
				(object) [  
					'condition' => [ (object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ],['key' => 'fileAlign', 'relation' => '==', 'value' => 'center'],],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap.cf7-style-file {-webkit-justify-content: flex-start; -ms-flex-pack: flex-start; justify-content: flex-start;} {{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap.cf7-style-file span{text-align: {{fileAlign}};}',
				],
				(object) [  
					'condition' => [ (object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ],['key' => 'fileAlign', 'relation' => '==', 'value' => 'right'],],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap.cf7-style-file {-webkit-justify-content: flex-end; -ms-flex-pack: flex-end; justify-content: flex-end;} {{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap.cf7-style-file span{text-align: {{fileAlign}};}',
				],
			],
			'scopy' => true,
		],
		'fileStyle' => [
			'type' => 'boolean',
			'default' => false,
			'style' => [
				(object) [ 
					'condition' => [ (object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ],['key' => 'fileStyle', 'relation' => '==', 'value' => true],], 
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap.cf7-style-file .input__file_btn svg,{{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap.cf7-style-file span{display:block;margin: 0 auto;text-align:center;}',
				],
			],
			'scopy' => true,
		],
		'fileTextColor' => [
			'type' => 'string',
			'default' => '#212121',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap.cf7-style-file .input__file_btn span{color: {{fileTextColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_fileupload input[type="file"]{color: {{fileTextColor}};}',
				],
			],
			'scopy' => true,
		],
		'fileTextHColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap.cf7-style-file .input__file_btn:hover span{color: {{fileTextHColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_fileupload input[type="file"]:hover{color: {{fileTextHColor}};}',
				],
			],
			'scopy' => true,
		],
		'fileIconColor' => [
			'type' => 'string',
			'default' => '#212121',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap.cf7-style-file .input__file_btn svg *{fill: {{fileIconColor}};stroke:none;}',
				],
			],
			'scopy' => true,
		],
		'fileIconHColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap.cf7-style-file .input__file_btn:hover svg *{fill: {{fileIconHColor}};stroke:none;}',
				],
			],
			'scopy' => true,
		],
		'fileBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap.cf7-style-file .wpcf7-file + .input__file_btn',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_fileupload input[type="file"]',
				],
			],
			'scopy' => true,
		],
		'fileHBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap.cf7-style-file .wpcf7-file + .input__file_btn:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_fileupload input[type="file"]:hover',
				],
			],
			'scopy' => true,
		],
		'fileBdr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => 'solid',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "px",
				],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .cf7-style-file .wpcf7-file + .input__file_btn',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_fileupload input[type="file"]',
				],
			],
			'scopy' => true,
		], 
		'fileBdrHColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap.cf7-style-file .input__file_btn:hover{border-color: {{fileBdrHColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_fileupload input[type="file"]:hover{border-color: {{fileBdrHColor}};}',
				],
			],
			'scopy' => true,
		],
		'fileBRadius' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .cf7-style-file .wpcf7-file + .input__file_btn{border-radius: {{fileBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_fileupload input[type="file"]{border-radius: {{fileBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'fileNBshadow' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .cf7-style-file .wpcf7-file + .input__file_btn',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_fileupload input[type="file"]',
				],
			],
			'scopy' => true,
		],
		'fileHBshadow' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .cf7-style-file .wpcf7-file + .input__file_btn:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .ginput_container_fileupload input[type="file"]:hover',
				],
			],
			'scopy' => true,
		],
		'multipleFileUpld' => [
			'type' => 'boolean',
			'default' => false,
			'scopy' => true,
		],
		'mFileTypo' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ], ['key' => 'multipleFileUpld', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input.button.gform_button_select_files',
				],
			],
			'scopy' => true,
		],
		'mFileTextNColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ], ['key' => 'multipleFileUpld', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input.button.gform_button_select_files{color: {{mFileTextNColor}};}',
				],
			],
			'scopy' => true,
		],
		'mFileTextHColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ], ['key' => 'multipleFileUpld', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input.button.gform_button_select_files:hover{color: {{mFileTextHColor}};}',
				],
			],
			'scopy' => true,
		],
		'mFileNBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ], ['key' => 'multipleFileUpld', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input.button.gform_button_select_files',
				],
			],
			'scopy' => true,
		],
		'mFileHBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ], ['key' => 'multipleFileUpld', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input.button.gform_button_select_files:hover',
				],
			],
			'scopy' => true,
		],
		'mFileBdr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => 'solid',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "px",
				],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ], ['key' => 'multipleFileUpld', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input.button.gform_button_select_files',
				],
			],
			'scopy' => true,
		], 
		'mFileBdrHColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ], ['key' => 'multipleFileUpld', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input.button.gform_button_select_files:hover{border-color: {{mFileBdrHColor}};}',
				],
			],
			'scopy' => true,
		],
		'mFileBRadius' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ], ['key' => 'multipleFileUpld', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input.button.gform_button_select_files{border-radius: {{mFileBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'mFileNBshadow' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ], ['key' => 'multipleFileUpld', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input.button.gform_button_select_files',
				],
			],
			'scopy' => true,
		],
		'mFileHBshadow' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ], ['key' => 'multipleFileUpld', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input.button.gform_button_select_files:hover',
				],
			],
			'scopy' => true,
		],
		/* File Field end*/
		
		/* Outer Field start*/
		'outerPadding' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-label form.wpcf7-form  label,{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-custom form.wpcf7-form .tpgb-cf7-outer{padding: {{outerPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field{padding: {{outerPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper ul li.gfield{padding: {{outerPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-container:not(.submit-container){padding: {{outerPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-wp-form div.wpforms-container .wpforms-field,{{PLUS_WRAP}} .tpgb-wp-form .wpforms-container .wpforms-submit-container{padding: {{outerPadding}};}',
				],
			],
			'scopy' => true,
		],
		'outerMargin' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-label form.wpcf7-form  label,{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-custom form.wpcf7-form .tpgb-cf7-outer{margin: {{outerMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field{margin: {{outerMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper ul li.gfield{margin: {{outerMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-container:not(.submit-container){margin: {{outerMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-field,{{PLUS_WRAP}} .wpforms-container .wpforms-submit-container{margin: {{outerMargin}};}',
				],
			],
			'scopy' => true,
		],
		'outerNBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-label form.wpcf7-form  label,{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-custom form.wpcf7-form .tpgb-cf7-outer',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper ul li.gfield',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-container:not(.submit-container)',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field',
				],
			],
			'scopy' => true,
		],
		'outerHBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form.tpgb-cf7-label form.wpcf7-form  label:hover,{{PLUS_WRAP}} .tpgb-contact-form.tpgb-cf7-custom form.wpcf7-form .tpgb-cf7-outer:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper ul li.gfield:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-container:not(.submit-container):hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field:hover',
				],
			],
			'scopy' => true,
		],          
		'outerNBdr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => 'solid',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "px",
				],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-label form.wpcf7-form  label,{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-custom form.wpcf7-form .tpgb-cf7-outer',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper ul li.gfield',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-container:not(.submit-container)',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field',
				],
			],
			'scopy' => true,
		],
		'outerHBdr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => 'solid',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "px",
				],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-label form.wpcf7-form  label,{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-custom form.wpcf7-form .tpgb-cf7-outer:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper ul li.gfield:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-container:not(.submit-container):hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field:hover',
				],
			],
			'scopy' => true,
		],           
		'outerNBRadius' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form.tpgb-cf7-label form.wpcf7-form  label,{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-custom form.wpcf7-form .tpgb-cf7-outer{border-radius: {{outerNBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field{border-radius: {{outerNBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper ul li.gfield{border-radius: {{outerNBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-container:not(.submit-container){border-radius: {{outerNBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field{border-radius: {{outerNBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'outerHBRadius' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-label form.wpcf7-form label:hover,{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-custom form.wpcf7-form .tpgb-cf7-outer:hover{border-radius: {{outerHBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field:hover{border-radius: {{outerHBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper ul li.gfield:hover{border-radius: {{outerHBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-container:not(.submit-container):hover {border-radius: {{outerHBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field:hover {border-radius: {{outerHBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'outerNBshadow' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-label form.wpcf7-form label,{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-custom form.wpcf7-form .tpgb-cf7-outer',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper ul li.gfield',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-container:not(.submit-container)',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field',
				],
			],
			'scopy' => true,
		],
		'outerHBshadow' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-label form.wpcf7-form label:hover,{{PLUS_WRAP}} .tpgb-contact-form-7.tpgb-cf7-custom form.wpcf7-form .tpgb-cf7-outer:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .evf-field:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper ul li.gfield:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-field-container:not(.submit-container):hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container .wpforms-field:hover',
				],
			],
			'scopy' => true,
		],
		/* Outer Field end*/
		
		/* Button Field start*/
		'btnMWidth' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 input.wpcf7-form-control.wpcf7-submit{max-width: {{btnMWidth}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-part-button, {{PLUS_WRAP}} .tpgb-everest-form .everest-forms button[type=submit], {{PLUS_WRAP}} .tpgb-everest-form .everest-forms input[type=submit]{width: {{btnMWidth}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="button"], {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="submit"]{width: {{btnMWidth}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap input[type=button]{max-width: {{btnMWidth}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form button[type=submit], {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-page-button{width: {{btnMWidth}};}',
				],
			],
			'scopy' => true,
		],
		'gBtnAlign' => [
			'type' => 'object',
			'default' => 'left',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gform_footer{ text-align: {{gBtnAlign}};}',
				],
			],
			'scopy' => true,
		],
		'btnTypo' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 input.wpcf7-form-control.wpcf7-submit',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-part-button,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms button[type=submit],{{PLUS_WRAP}} .tpgb-everest-form .everest-forms input[type=submit]',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="button"], {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="submit"]',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap input[type=button]',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form button[type=submit], {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-page-button',
				],
			],
			'scopy' => true,
		],
		'btnPadding' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 input.wpcf7-form-control.wpcf7-submit{padding: {{btnPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-part-button,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms button[type=submit],{{PLUS_WRAP}} .tpgb-everest-form .everest-forms input[type=submit]{padding: {{btnPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="button"], {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="submit"]{padding: {{btnPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap input[type=button]{padding: {{btnPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form button[type=submit],{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-page-button{padding: {{btnPadding}};}',
				],
			],
			'scopy' => true,
		],
		'btnMargin' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 input.wpcf7-form-control.wpcf7-submit{margin: {{btnMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-part-button,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms button[type=submit],{{PLUS_WRAP}} .tpgb-everest-form .everest-forms input[type=submit]{margin: {{btnMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap input[type=button]{margin: {{btnMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form button[type=submit],
					{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-page-button{margin: {{btnMargin}};}',
				],
			],
			'scopy' => true,
		],
		'btnNColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 input.wpcf7-form-control.wpcf7-submit{color: {{btnNColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-part-button,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms button[type=submit],{{PLUS_WRAP}} .tpgb-everest-form .everest-forms input[type=submit]{color: {{btnNColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gform_button.button{color: {{btnNColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap input[type=button]{color: {{btnNColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form button[type=submit],
					{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-page-button{color: {{btnNColor}};}',
				],
			],
			'scopy' => true,
		],
		'nextBtnNColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gform_next_button{color: {{nextBtnNColor}};}',
				],
			],
			'scopy' => true,
		],
		'prevBtnNColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gform_previous_button{color: {{prevBtnNColor}};}',
				],
			],
			'scopy' => true,
		],
		'btnNBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 input.wpcf7-form-control.wpcf7-submit',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-part-button,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms button[type=submit],{{PLUS_WRAP}} .tpgb-everest-form .everest-forms input[type=submit]',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gform_button.button',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap input[type=button]',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form button[type=submit], {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-page-button',
				],
			],
			'scopy' => true,
		],
		'nextBtnNBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gform_next_button',
				],
			],
			'scopy' => true,
		],
		'prevBtnNBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gform_previous_button',
				],
			],
			'scopy' => true,
		],
		'btnHColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 input.wpcf7-form-control.wpcf7-submit:hover{color: {{btnHColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-part-button:hover,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms button[type=submit]:hover,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms input[type=submit]:hover{color: {{btnHColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gform_button.button:hover{color: {{btnHColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap:hover input[type=button]{color: {{btnHColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form button[type=submit]:hover,{{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-page-button:hover{color: {{btnHColor}};}',
				],
			],
			'scopy' => true,
		],
		'nextBtnHColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_next_button:hover{color: {{nextBtnHColor}};}',
				],
			],
			'scopy' => true,
		],
		'prevBtnHColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_previous_button:hover{color: {{prevBtnHColor}};}',
				],
			],
			'scopy' => true,
		],
		'btnHBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 input.wpcf7-form-control.wpcf7-submit:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-part-button:hover,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms button[type=submit]:hover,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms input[type=submit]:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gform_button.button:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap:hover input[type=button]',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form button[type=submit]:hover, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-page-button:hover',
				],
			],
			'scopy' => true,
		],
		'nextBtnHBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gform_next_button:hover',
				],
			],
			'scopy' => true,
		],
		'prevBtnHBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gform_previous_button:hover',
				],
			],
			'scopy' => true,
		],
		'btnNBdr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => 'solid',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "px",
				],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 input.wpcf7-form-control.wpcf7-submit',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-part-button,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms button[type=submit],{{PLUS_WRAP}} .tpgb-everest-form .everest-forms input[type=submit]',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="button"], {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="submit"]',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap input[type=button]',
				],
			],
			'scopy' => true,
		],
		'btnHBdr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => 'solid',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "",
				],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 input.wpcf7-form-control.wpcf7-submit:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-part-button:hover,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms button[type=submit]:hover,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms input[type=submit]:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="button"]:hover, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="submit"]:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap:hover input[type=button]',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form button[type=submit]:hover, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-page-button:hover',
				],
			],
			'scopy' => true,
		],
		'btnNBRadius' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 input.wpcf7-form-control.wpcf7-submit{border-radius: {{btnNBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-part-button,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms button[type=submit],{{PLUS_WRAP}} .tpgb-everest-form .everest-forms input[type=submit]{border-radius: {{btnNBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="button"], {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="submit"]{border-radius: {{btnNBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap input[type=button]{border-radius: {{btnNBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form button[type=submit], {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-page-button{border-radius: {{btnNBRadius}} !important;}',
				],
			],
			'scopy' => true,
		],            
		'btnHBRadius' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 input.wpcf7-form-control.wpcf7-submit:hover{border-radius: {{btnHBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-part-button:hover,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms button[type=submit]:hover,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms input[type=submit]:hover{border-radius: {{btnHBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="button"]:hover, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="submit"]:hover{border-radius: {{btnHBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap:hover input[type=button]{border-radius: {{btnHBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form button[type=submit]:hover, {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-page-button:hover{border-radius: {{btnHBRadius}} !important;}',
				],
			],
			'scopy' => true,
		],
		'btnNBshadow' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 input.wpcf7-form-control.wpcf7-submit',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-part-button,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms button[type=submit],{{PLUS_WRAP}} .tpgb-everest-form .everest-forms input[type=submit]',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="button"], {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="submit"]',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap input[type=button]',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form button[type=submit], {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-page-button',
				],
			],
			'scopy' => true,
		],
		'btnHBshadow' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 input.wpcf7-form-control.wpcf7-submit:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-part-button:hover,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms button[type=submit]:hover,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms input[type=submit]:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="button"]:hover, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper input[type="submit"]:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .field-wrap:hover input[type=button]',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form button[type=submit], {{PLUS_WRAP}} div.wpforms-container .wpforms-form .wpforms-page-button',
				],
			],
			'scopy' => true,
		],
		/* Button Field end*/
		
		/* Form Container start*/
		'formPadding' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms{padding: {{formPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper{padding: {{formPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form {padding: {{formPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container {padding: {{formPadding}};}',
				],
			],
			'scopy' => true,
		],
		'formMargin' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms{margin: {{formMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper{margin: {{formMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form {margin: {{formMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container {margin: {{formMargin}};}',
				],
			],
			'scopy' => true,
		],
		'formNBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container',
				],
			],
			'scopy' => true,
		],
		'formHBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container:hover',
				],
			],
			'scopy' => true,
		],
		'formNBdr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => 'solid',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "px",
				],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container',
				],
			],
			'scopy' => true,
		],
		'formHBdr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => 'solid',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "",
				],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container:hover',
				],
			],
			'scopy' => true,
		],
		'formNBRadius' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms{border-radius: {{formNBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper{border-radius: {{formNBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form{border-radius: {{formNBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container{border-radius: {{formNBRadius}} !important;}',
				],
			],
			'scopy' => true,
		],            
		'formHBRadius' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms:hover{border-radius: {{formHBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper:hover{border-radius: {{formHBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form:hover{border-radius: {{formHBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container:hover{border-radius: {{formHBRadius}} !important;}',
				],
			],
			'scopy' => true,
		],
		'formNBshadow' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container',
				],
			],
			'scopy' => true,
		],
		'formHBshadow' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-container:hover',
				],
			],
			'scopy' => true,
		],
		/* Form Container end*/
		
		/* Response Message Field start*/
		'responseMsgTypo' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .wpcf7-response-output',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-notice--success, {{PLUS_WRAP}} .tpgb-everest-form .everest-forms label.evf-error',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_confirmation_wrapper, {{PLUS_WRAP}} .tpgb-gravity-form .gfield_description.validation_message',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-form-wrap .nf-response-msg',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-confirmation-container-full,{{PLUS_WRAP}} .wpforms-confirmation-container-full p, {{PLUS_WRAP}} div.wpforms-container .wpforms-form label.wpforms-error',
				],
			],
			'scopy' => true,
		],
		'responseMsgPadding' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-response-output{padding: {{responseMsgPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-notice--success,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-notice::before,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms label.evf-error{padding: {{responseMsgPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_confirmation_wrapper, {{PLUS_WRAP}} .tpgb-gravity-form .gfield_description.validation_message{padding: {{responseMsgPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-form-wrap .nf-response-msg{padding: {{responseMsgPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-confirmation-container-full{padding: {{responseMsgPadding}};}',
				],
			],
			'scopy' => true,
		],
		'responseMsgMargin' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-response-output{margin: {{responseMsgMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-notice--success,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-notice::before,{{PLUS_WRAP}} .tpgb-everest-form .everest-forms label.evf-error{margin: {{responseMsgMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_confirmation_wrapper, {{PLUS_WRAP}} .tpgb-gravity-form .gfield_description.validation_message{margin: {{responseMsgMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-form-wrap .nf-response-msg{margin: {{responseMsgMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-confirmation-container-full{margin: {{responseMsgMargin}};}',
				],
			],
			'scopy' => true,
		],
		'responseSuccessColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-response-output.wpcf7-mail-sent-ok{color: {{responseSuccessColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-notice--success{color: {{responseSuccessColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_confirmation_wrapper{color: {{responseSuccessColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-form-wrap .nf-response-msg p{color: {{responseSuccessColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-confirmation-container-full,{{PLUS_WRAP}} .wpforms-confirmation-container-full p{color: {{responseSuccessColor}};}',
				],
			],
			'scopy' => true,
		],
		'responseSuccessBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-response-output.wpcf7-mail-sent-ok',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-notice--success',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_confirmation_wrapper',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-form-wrap .nf-response-msg',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-confirmation-container-full',
				],
			],
			'scopy' => true,
		],
		'responseValidateColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-response-output.wpcf7-validation-errors, {{PLUS_WRAP}} .tpgb-contact-form-7  .wpcf7-response-output.wpcf7-acceptance-missing{color: {{responseValidateColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms label.evf-error{color: {{responseValidateColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .validation_message, {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper div.validation_error{color: {{responseValidateColor}};} {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper li.gfield_error input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper li.gfield_error textarea{border-color: {{responseValidateColor}};} {{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper div.validation_error{border-top-color: {{responseValidateColor}}; border-bottom-color: {{responseValidateColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-form-wrap .nf-form-errors,{{PLUS_WRAP}} .tpgb-ninja-form .nf-form-wrap .nf-error-msg.nf-error-field-errors{color: {{responseValidateColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} div.wpforms-container .wpforms-form label.wpforms-error{color: {{responseValidateColor}};}',
				],
			],
			'scopy' => true,
		],
		'responseValidateBG' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-response-output.wpcf7-validation-errors, {{PLUS_WRAP}} .tpgb-contact-form-7  .wpcf7-response-output.wpcf7-acceptance-missing',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms label.evf-error',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .gform_wrapper li.gfield.gfield_error,{{PLUS_WRAP}} .gform_wrapper li.gfield.gfield_error.gfield_contains_required.gfield_creditcard_warning',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-form-wrap .nf-form-errors',
				],
			],
			'scopy' => true,
		],
		'responseSuccessBdr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => 'solid',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "px",
				],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-response-output',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-notice--success',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_confirmation_wrapper',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-form-wrap .nf-response-msg',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-confirmation-container-full',
				],
			],
			'scopy' => true,
		],
		'responseValidateBdr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => 'solid',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "px",
				],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-response-output.wpcf7-validation-errors, {{PLUS_WRAP}} .tpgb-contact-form-7  .wpcf7-response-output.wpcf7-acceptance-missing',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms label.evf-error',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .gform_wrapper li.gfield.gfield_error,{{PLUS_WRAP}} .gform_wrapper li.gfield.gfield_error.gfield_contains_required.gfield_creditcard_warning',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-form-wrap .nf-form-errors',
				],
			],
			'scopy' => true,
		],          
		'responseSuccessBRadius' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-response-output.wpcf7-mail-sent-ok {border-radius: {{responseSuccessBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms .everest-forms-notice--success {border-radius: {{responseSuccessBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_confirmation_wrapper {border-radius: {{responseSuccessBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-form-wrap .nf-response-msg {border-radius: {{responseSuccessBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'wp-form' ]],
					'selector' => '{{PLUS_WRAP}} .wpforms-confirmation-container-full {border-radius: {{responseSuccessBRadius}} !important;}',
				],
			],
			'scopy' => true,
		],
		'responseValidateBRadius' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-response-output.wpcf7-validation-errors, {{PLUS_WRAP}} .tpgb-contact-form-7 .wpcf7-response-output.wpcf7-acceptance-missing{border-radius: {{responseValidateBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms label.evf-error{border-radius: {{responseValidateBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .gform_wrapper li.gfield.gfield_error,{{PLUS_WRAP}} .gform_wrapper li.gfield.gfield_error.gfield_contains_required.gfield_creditcard_warning{border-radius: {{responseValidateBRadius}} !important;}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-form-wrap .nf-form-errors{border-radius: {{responseValidateBRadius}} !important;}',
				],
			],
			'scopy' => true,
		],
		'cntntMWidth' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7{max-width: {{cntntMWidth}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'everest-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-everest-form .everest-forms{max-width: {{cntntMWidth}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper{max-width: {{cntntMWidth}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form{max-width: {{cntntMWidth}};}',
				],
			],
			'scopy' => true,
		],
		'ninjaReqFPadding' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-error-msg.nf-error-required-error,{{PLUS_WRAP}} .tpgb-ninja-form .nf-error-msg.nf-error-field-errors{padding: {{ninjaReqFPadding}};}',
				],
			],
			'scopy' => true,
		],
		'reqTextColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap .wpcf7-not-valid-tip{color: {{reqTextColor}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-error-msg.nf-error-required-error,{{PLUS_WRAP}} .tpgb-ninja-form .nf-error-msg.nf-error-field-errors{color: {{reqTextColor}};} {{PLUS_WRAP}} .tpgb-ninja-form .nf-error-msg.nf-error-required-error,{{PLUS_WRAP}} .tpgb-ninja-form .nf-error-msg.nf-error-field-errors{border: 1px solid {{reqTextColor}};}',
				],
			],
			'scopy' => true,
		],
		'reqTextBG' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'contact-form-7' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-form-control-wrap .wpcf7-not-valid-tip{background: {{reqTextBG}};} {{PLUS_WRAP}} .tpgb-contact-form-7 span.wpcf7-not-valid-tip:before{border-bottom-color: {{reqTextBG}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-error-msg.nf-error-required-error{background: {{reqTextBG}};}',
				],
			],
			'scopy' => true,
		],
		'reqBdrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'ninja-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ninja-form .nf-error-msg.nf-error-required-error{border: 1px solid {{reqBdrColor}};}',
				],
			],
			'scopy' => true,
		],
		'captchaMargin' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'gravity-form' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-gravity-form .gform_wrapper .gfield .ginput_recaptcha{margin: {{captchaMargin}};}',
				],
			],
			'scopy' => true,
		],
		/* Response Message Field end*/
	);
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-external-form-styler', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_external_form_styler_render_callback'
    ) );
}
add_action( 'init', 'tpgb_external_form_styler');