<?php
/**
 * TPGB Pro Global Plus Extras
 *
 * @package TPGBP
 * @since 1.4.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;
class Tpgbp_Plus_Extras_Opt {
	
	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;
	
	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	/**
	 * Constructor
	 * @since 1.4.0
	 */
	public function __construct() {
		add_filter( 'tpgb_display_option', [ $this, 'tpgb_plus_extras_opt'], 10 );
	}

	/*
	 * Plus Extras Options
	 * @since 1.4.0
	 */
	public static function tpgb_plus_extras_opt($option =[]){
		$msOption = [
			'Plus3DTilt' => [
				'type' => 'object',
				'default' => [],
				'scopy' => true,
			],
			'PlusMouseParallax' => [
				'type' => 'object',
				'default' => [],	
				'scopy' => true,
			],
			'continueAnimation' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'continueAniStyle' => [
				'type' => 'string',
				'default' => 'pulse',	
				'scopy' => true,
			],
			'continueHoverAnimation' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'continueAniDuration' => [
				'type' => 'string',
				'default' => '2',	
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'continueAniStyle', 'relation' => '==', 'value' => 'pulse' ]],
						'selector' => '{{PLUS_BLOCK}}.tpgb-normal-pulse, {{PLUS_BLOCK}}.tpgb-hover-pulse:hover { animation-duration: {{continueAniDuration}}s; -webkit-animation-duration: {{continueAniDuration}}s; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'continueAniStyle', 'relation' => '==', 'value' => 'floating' ]],
						'selector' => '{{PLUS_BLOCK}}.tpgb-normal-floating, {{PLUS_BLOCK}}.tpgb-hover-floating:hover { animation-duration: {{continueAniDuration}}s; -webkit-animation-duration: {{continueAniDuration}}s; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'continueAniStyle', 'relation' => '==', 'value' => 'tossing' ]],
						'selector' => '{{PLUS_BLOCK}}.tpgb-normal-tossing, {{PLUS_BLOCK}}.tpgb-hover-tossing:hover{ animation-duration: {{continueAniDuration}}s; -webkit-animation-duration: {{continueAniDuration}}s; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'continueAniStyle', 'relation' => '==', 'value' => 'rotating' ]],
						'selector' => '{{PLUS_BLOCK}}.tpgb-normal-rotating, {{PLUS_BLOCK}}.tpgb-hover-rotating:hover{ animation-duration: {{continueAniDuration}}s; -webkit-animation-duration: {{continueAniDuration}}s; }',
					],
				],
				'scopy' => true,
			],
			'globalTooltip' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'gblTooltipType' => [
				'type' => 'string',
				'default' => 'content'
			],
			'gblblockTemp' => [
				'type' => 'string',
				'default' => 'none'
			],
			'gblbackTempVis' => [
				'type' => 'boolean',
				'default' => false,
			],
			'gblTooltipText' => [
				'type' => 'string',
				'default' => 'Your Tooltip Content',	
				'scopy' => true,
			],

			'gbltipInteractive' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'gbltipPlacement' => [
				'type' => 'string',
				'default' => 'top',
				'scopy' => true,
			],
			'gbltipFlCursor' => [
				'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'gbltipMaxWidth' => [
				'type' => 'string',
				'default' => '100',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'globalTooltip', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_BLOCK}} .tippy-box{width : {{gbltipMaxWidth}}px; max-width : {{gbltipMaxWidth}}px !important; }  ',
					],
				],
				'scopy' => true,
			],
			'gbltipOffset' => [
				'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'gbltipDistance' => [
				'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'gbltipArrow' => [
				'type' => 'boolean',
				'default' => true,
				'scopy' => true,
			],
			'gbltipTriggers' => [
				'type' => 'string',
				'default' => 'mouseenter',
				'scopy' => true,
			],
			'gbltipAnimation' => [
				'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'gbltipDurationIn' => [
				'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'gbltipDurationOut' => [
				'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'gbltextVisHide' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => false,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'globalTooltip', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_BLOCK}} .tippy-box{display: none;}',
					],
				],
				'scopy' => true,
			],
			
			'gbltooltipTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'globalTooltip', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_BLOCK}} .tippy-box .tippy-content',
					],
				],
				'scopy' => true,
			],
			'gbltooltipColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'globalTooltip', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_BLOCK}} .tippy-box .tippy-content{ color: {{gbltooltipColor}}; }',
					],
				],
				'scopy' => true,
			],
			'gbltipArrowColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'globalTooltip', 'relation' => '==', 'value' => true ], ['key' => 'gbltipArrow', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_BLOCK}} .tippy-arrow{color: {{gbltipArrowColor}};}',
					],
				],
				'scopy' => true,
			],
			'gbltipPadding' => [
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
						'condition' => [(object) ['key' => 'globalTooltip', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_BLOCK}} .tippy-box{padding: {{gbltipPadding}};}',
					],
				],
				'scopy' => true,
			],
			'gbltipBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'globalTooltip', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_BLOCK}} .tippy-box',
					],
				],
				'scopy' => true,
			],
			'gbltipBorderRadius' => [
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
						'condition' => [(object) ['key' => 'globalTooltip', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_BLOCK}} .tippy-box{border-radius: {{gbltipBorderRadius}};}',
					],
				],
				'scopy' => true,
			],
			'gbltipBg' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgGradient' => (object) [],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'globalTooltip', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_BLOCK}} .tippy-box',
					],
				],
				'scopy' => true,
			],
			'gbltipBoxShadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'blur' => 8,
					'color' => "rgba(0,0,0,0.40)",
					'horizontal' => 0,
					'inset' => 0,
					'spread' => 0,
					'vertical' => 4
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'globalTooltip', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_BLOCK}} .tippy-box',
					],
				],
				'scopy' => true,
			],

			'advBorderRadius' => [
				'type' => 'object',
				'default' => [
					'selBdrArea' => 'background',
					'advBdrUniqueClass' => '',
					'abNlayout' => 'layout-1',
					'advBdrNcustom' => '',
					'abHlayout' => 'layout-1',
					'advBdrHcustom' => '',
				],
				'scopy' => true,
			],
			'globalPosition' => [
				'type' => 'object',
				'default' => [ 'md' => '','sm' => '','xs' => '' ],	
			],
			'gloabhorizoOri' => [
				'type' => 'object',
				'default' => [ 'md' => 'left', 'sm' =>  'left', 'xs' =>  'left' ]
			],
			'glohoriOffset' => [
				'type' => 'object',
				'default' =>[ 
					'md' => '0',
					"unit" => 'px',
				],
			],
			'gloabverticalOri' => [
				'type' => 'object',
				'default' => [ 'md' => 'top', 'sm' =>  'top', 'xs' =>  'top' ]
			],
			'gloverticalOffset' => [
				'type' => 'object',
				'default' => [ 
					'md' => '0',
					"unit" => 'px',
				],
			],
			'globalOverflow' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_BLOCK}} { overflow: {{globalOverflow}}; }',
					],
				],
			],
		];

		return array_merge( $option, $msOption );
	}

	/**
	 * Extra Options : Equal Height
	 * @since 1.4.0
	 */
	public static function load_plusEqualHeight_options() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		$options = [
			'tpgbEqualHeight' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'equalUnqClass' => [
				'type' => 'string',
				'default' => '',	
				'scopy' => true,
			],
		];

		return $options;
	}
}
Tpgbp_Plus_Extras_Opt::get_instance();