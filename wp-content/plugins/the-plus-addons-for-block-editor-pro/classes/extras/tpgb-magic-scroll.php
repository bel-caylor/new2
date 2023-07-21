<?php
/**
 * TPGB Pro Magic Scroll.
 *
 * @package TPGBP
 * @since 1.4.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;
class Tpgbp_Magic_Scroll_Extra {
	
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
		add_filter('tpgb_globalWrapAttr', [ $this, 'global_attr_magic_scroll'], 10, 2 );
		add_filter( 'tpgb_display_option', [ $this, 'tpgb_magic_scroll_option'], 10 );
	}

	/*
	 * Magic Scroll Options
	 * @since 1.4.0
	 */
	public static function tpgb_magic_scroll_option($option =[]){
		$msOption = [
			'PlusMagicScroll' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'MSView' => [
				'type' => 'array',
				'default' => [ 
					(object)[ "value" => "md","label" => "Desktop" ],
					(object)[ "value"=> "sm","label" => "Tablet"],
					(object)[ "value"=> "xs","label" => "Mobile"]
				],
				'scopy' => true,
			],
			'MSType' => [
				'type' => 'string',
				'default' => 'normal',
				'scopy' => true,
			],
			'MSScrollOpt' => [
				'type' => 'object',
				'default' => [
					'trigger' => (object)[ 'md' => 0.5, ],
					'duration' => (object)[ 'md' => 300, "unit" => 'px', ],
					'offset' => (object)[ 'md' => '0', "unit" => 'px', ],
					'tpgbReset' => 0
				],
			],
			'MSVertical' => [
				'type' => 'object',
				'default' => [
					'speed' => (object)[ 'md' => [0,5] ],
					'reverse' => false,
					'tpgbReset' => 0
				],
				'scopy' => true,
			],
			'MSHorizontal' => [
				'type' => 'object',
				'default' => [
					'speed' => (object)[ 'md' => [0,5] ],
					'reverse' => false,
					'tpgbReset' => 0
				],
				'scopy' => true,
			],
			'MSOpacity' => [
				'type' => 'object',
				'default' => [
					'speed' => (object)[ 'md' => [0,10] ],
					'reverse' => false,
					'tpgbReset' => 0
				],
				'scopy' => true,
			],
			'MSRotate' => [
				'type' => 'object',
				'default' => [
					'position' => 'center center',
					'rotateX' => (object)[ 'md' => [0, 4] ],
					'rotateY' => (object)[ 'md' => [0, 0] ],
					'rotateZ' => (object)[ 'md' => [0, 0] ],
					'reverse' => false,
					'tpgbReset' => 0
				],
			],
			'MSScale' => [
				'type' => 'object',
				'default' => [
					'scaleX' => (object)[ 'md' => [1, 1.5] ],
					'scaleY' => (object)[ 'md' => [1, 1] ],
					'scaleZ' => (object)[ 'md' => [1, 1] ],
					'reverse' => false,
					'tpgbReset' => 0
				],
			],
			'MSSticky' => [
				'type' => 'boolean',
				'default' => false
			],
			'MSDevelop' => [
				'type' => 'boolean',
				'default' => false
			],
			'MSdevName' => [
				'type' => 'string',
				'default' => ''
			],
			'MSFrame' => [
				'type'=> 'array',
				'repeaterField' => [
					(object) [
						'scrollOpt' => [
							'type' => 'array',
							'default' => [ 
								'trigger' => (object)[ 'md' => 0.5 ],
								'duration' => (object)[ 'md' => 300, "unit" => 'px', ],
								'offset' => (object)[ 'md' => '0', "unit" => 'px', ],
								'tpgbReset' => 0
							],
						],
						'vertical' => [
							'type' => '',
							'default' => [
								'speed' => (object)[ 'md' => [0,5] ],
								'reverse' => false,
								'tpgbReset' => 0
							],
						],
						'horizontal' => [
							'type' => 'array',
							'default' => [
								'speed' => (object)[ 'md' => [0,5] ],
								'reverse' => false,
								'tpgbReset' => 0
							],
						],
						'opacity' => [
							'type' => 'array',
							'default' => [
								'speed' => (object)[ 'md' => [0,10] ],
								'reverse' => false,
								'tpgbReset' => 0
							],
						],
						'rotate' => [
							'type' => 'array',
							'default' => [
								'position' => 'center center',
								'rotateX' => (object)[ 'md' => [0, 4] ],
								'rotateY' => (object)[ 'md' => [0, 0] ],
								'rotateZ' => (object)[ 'md' => [0, 0] ],
								'reverse' => false,
								'tpgbReset' => 0
							],
						],
						'scale' => [
							'type' => 'array',
							'default' => [
								'scaleX' => (object)[ 'md' => [1, 1.5] ],
								'scaleY' => (object)[ 'md' => [1, 1] ],
								'scaleZ' => (object)[ 'md' => [1, 1] ],
								'reverse' => false,
								'tpgbReset' => 0
							],
						],
						'skew' => [
							'type' => 'array',
							'default' => [
								'skewX' => (object)[ 'md' => [0, 1] ],
								'skewY' => (object)[ 'md' => [0, 0] ],
								'reverse' => false,
								'tpgbReset' => 0
							],
						],
						'borderR' => [
							'type' => 'array',
							'default' => [
								'fromBR' => (object)[ 'md' => [ "top" => '', "bottom" => '', "left" => '', "right" => '' ], "unit" => 'px',	],
								'toBR' => (object)[ 'md' => [ "top" => '', "bottom" => '', "left" => '', "right" => '' ], "unit" => 'px', ],
								'tpgbReset' => 0
							],
						],
						'bgColor' => [
							'type' => 'array',
							'default' => [
								'fromColor' => '',
								'toColor' => '',
								'tpgbReset' => 0
							],
						],
						'advOption' => [
							'type' => 'array',
							'default' => [ 
								'repeat' => (object)[ 'md' => '0', ],
								'easing' => '',
								'delay' => (object)[ 'md' => '0', ],
								'timing' => (object)[ 'md' => '1', ],
								'reverse' => true,
								'selector' => '',
								'tpgbReset' => 0
							],
						],
						'sticky' => [
							'type' => 'boolean',
							'default' => false
						],
						'develop' => [
							'type' => 'boolean',
							'default' => false
						],
						'devName' => [
							'type' => 'string',
							'default' => '',
						],
					],
				],
				'default' => [
					[ 
						"_key" => '0',
						'scrollOpt' => [
							'trigger' => (object)[ 'md' => 0.5 ],
							'duration' => (object)[ 'md' => 300, "unit" => 'px', ],
							'offset' => (object)[ 'md' => '0', "unit" => 'px', ],
							'tpgbReset' => 0
						],
						'vertical' => [
							'speed' => (object)[ 'md' => [0,5] ],
							'reverse' => false,
							'tpgbReset' => 0
						],
						'horizontal' => [
							'speed' => (object)[ 'md' => [0,5] ],
							'reverse' => false,
							'tpgbReset' => 0
						],
						'opacity' => [
							'speed' => (object)[ 'md' => [0,10] ],
							'reverse' => false,
							'tpgbReset' => 0
						],
						'rotate' => [
							'position' => 'center center',
							'rotateX' => (object)[ 'md' => [0, 4] ],
							'rotateY' => (object)[ 'md' => [0, 0] ],
							'rotateZ' => (object)[ 'md' => [0, 0] ],
							'reverse' => false,
							'tpgbReset' => 0
						],
						'scale' => [
							'scaleX' => (object)[ 'md' => [1, 1.5] ],
							'scaleY' => (object)[ 'md' => [1, 1] ],
							'scaleZ' => (object)[ 'md' => [1, 1] ],
							'reverse' => false,
							'tpgbReset' => 0
						],
						'skew' => [
							'skewX' => (object)[ 'md' => [0, 1] ],
							'skewY' => (object)[ 'md' => [0, 0] ],
							'reverse' => false,
							'tpgbReset' => 0
						],
						'borderR' => [
							'fromBR' => (object)[ 'md' => [ "top" => '', "bottom" => '', "left" => '', "right" => '' ], "unit" => 'px',	],
							'toBR' => (object)[ 'md' => [ "top" => '', "bottom" => '', "left" => '', "right" => '' ], "unit" => 'px', ],
							'tpgbReset' => 0
						],
						'bgColor' => [
							'fromColor' => '',
							'toColor' => '',
							'tpgbReset' => 0
						],
						'advOption' => [
							'repeat' => (object)[ 'md' => '0', ],
							'easing' => '',
							'delay' => (object)[ 'md' => '0', ],
							'timing' => (object)[ 'md' => '1', ],
							'reverse' => true,
							'selector' => '',
							'tpgbReset' => 0
						],
						'sticky' => false,
						'develop' => false,
						'devName' => '',
					],
				],
				'scopy' => true,
			],
		];
		return array_merge( $option, $msOption );
	}

	/*
	 * Add Global Attribute Magic Scroll
	 * @since 1.4.0
	 */
	public static function global_attr_magic_scroll($wrapAttr = '', $attr = []){
		if(!empty($attr['PlusMagicScroll']) && !empty($attr['MSFrame']) ){
			$scrollAttr = [];
			if(!empty( $attr['MSType'] ) && $attr['MSType'] == 'normal'){
				$effect = [ 'vertical' => 'MSVertical', 'horizontal' => 'MSHorizontal', 'opacity' => 'MSOpacity', 'rotate' => 'MSRotate', 'scale' => 'MSScale' ];
				foreach( $effect as $key => $val){
					if( isset($attr[$val]) && !empty($attr[$val]) && isset($attr[$val]['tpgbReset']) && !empty($attr[$val]['tpgbReset']) ){
						unset($attr[$val]['tpgbReset']);
						$scene_loop[$key] = $attr[$val];
					}
				}
				if(isset($attr['MSScrollOpt']) && !empty($attr['MSScrollOpt']) && isset($attr['MSScrollOpt']['tpgbReset']) && !empty($attr['MSScrollOpt']['tpgbReset'])){
					$extraOpt = $attr['MSScrollOpt'];
					if(isset($extraOpt['trigger']) && !empty($extraOpt['trigger'])){
						$scene_loop['trigger'] = $extraOpt['trigger'];
					}
					if(isset($extraOpt['duration']) && !empty($extraOpt['duration'])){
						$scene_loop['duration'] = $extraOpt['duration'];
					}
					if(isset($extraOpt['offset']) && !empty($extraOpt['offset'])){
						$scene_loop['offset'] = $extraOpt['offset'];
					}
				}else{
					$scene_loop['trigger'] = (object)[ 'md' => 0.5 ];
					$scene_loop['duration'] = (object)[ 'md' => 300, "unit" => 'px', ];
					$scene_loop['offset'] = (object)[ 'md' => '0', "unit" => 'px', ];
				}
				if(isset($attr['MSSticky'])){
					$scene_loop['sticky'] = $attr['MSSticky'];
				}
				if(isset($attr['MSDevelop'])){
					$scene_loop['develop'] = $attr['MSDevelop'];
					if(isset($attr['MSdevName']) && !empty($attr['MSdevName'])){
						$scene_loop['develop_name'] = $attr['MSdevName'];
					}
				}
				$scene_loop['repeat'] = (object)[ 'md' => 0, ];
				$scene_loop['delay'] = (object)[ 'md' => 0, ];
				$scene_loop['timing'] = (object)[ 'md' => 1, ];
				$scene_loop['reverse'] = true;
				if(!empty($scene_loop)){
					$scrollAttr[] = $scene_loop;
				}
			}else if(!empty( $attr['MSType'] ) && $attr['MSType'] == 'advanced'){
				foreach($attr['MSFrame'] as $key => $val ){
					$scene_loop = [];
					//vertical
					if( isset($val['vertical']) && !empty($val['vertical']) && isset($val['vertical']['tpgbReset']) && !empty($val['vertical']['tpgbReset']) ){
						unset($val['vertical']['tpgbReset']);
						$scene_loop['vertical'] = $val['vertical'];
					}
					//Horizontal
					if( isset($val['horizontal']) && !empty($val['horizontal']) && isset($val['horizontal']['tpgbReset']) && !empty($val['horizontal']['tpgbReset']) ){
						unset($val['horizontal']['tpgbReset']);
						$scene_loop['horizontal'] = $val['horizontal'];
					}
					//Opacity
					if( isset($val['opacity']) && !empty($val['opacity']) && isset($val['opacity']['tpgbReset']) && !empty($val['opacity']['tpgbReset']) ){
						unset($val['opacity']['tpgbReset']);
						$scene_loop['opacity'] = $val['opacity'];
					}
					//Rotate
					if( isset($val['rotate']) && !empty($val['rotate']) && isset($val['rotate']['tpgbReset']) && !empty($val['rotate']['tpgbReset']) ){
						unset($val['rotate']['tpgbReset']);
						$scene_loop['rotate'] = $val['rotate'];
					}
					//Scale
					if( isset($val['scale']) && !empty($val['scale']) && isset($val['scale']['tpgbReset']) && !empty($val['scale']['tpgbReset']) ){
						unset($val['scale']['tpgbReset']);
						$scene_loop['scale'] = $val['scale'];
					}
					//Skew
					if( isset($val['skew']) && !empty($val['skew']) && isset($val['skew']['tpgbReset']) && !empty($val['skew']['tpgbReset']) ){
						unset($val['skew']['tpgbReset']);
						$scene_loop['skew'] = $val['skew'];
					}
					//BRadius
					if( isset($val['borderR']) && !empty($val['borderR']) && isset($val['borderR']['tpgbReset']) && !empty($val['borderR']['tpgbReset']) ){
						unset($val['borderR']['tpgbReset']);
						$scene_loop['borderR'] = $val['borderR'];
					}
					//bgColor
					if( isset($val['bgColor']) && !empty($val['bgColor']) && isset($val['bgColor']['tpgbReset']) && !empty($val['bgColor']['tpgbReset']) ){
						unset($val['bgColor']['tpgbReset']);
						$scene_loop['bgColor'] = $val['bgColor'];
					}
					if(isset($val['scrollOpt']) && !empty($val['scrollOpt']) && isset($val['scrollOpt']['tpgbReset']) && !empty($val['scrollOpt']['tpgbReset'])){

						$scrollOpt = $val['scrollOpt'];
						if(isset($scrollOpt['trigger']) && !empty($scrollOpt['trigger'])){
							$scene_loop['trigger'] = $scrollOpt['trigger'];
						}
						if(isset($scrollOpt['duration']) && !empty($scrollOpt['duration'])){
							$scene_loop['duration'] = $scrollOpt['duration'];
						}
						if(isset($scrollOpt['offset']) && !empty($scrollOpt['offset'])){
							$scene_loop['offset'] = $scrollOpt['offset'];
						}
					}else{
						$scene_loop['trigger'] = (object)[ 'md' => 0.5 ];
						$scene_loop['duration'] = (object)[ 'md' => 300, "unit" => 'px', ];
						$scene_loop['offset'] = (object)[ 'md' => '0', "unit" => 'px', ];
					}
					if(isset($val['selector']) && !empty($val['selector'])){
						$scene_loop['selector'] = $val['selector'];
					}
					if(isset($val['advOption']) && !empty($val['advOption']) && isset($val['advOption']['tpgbReset']) && !empty($val['advOption']['tpgbReset'])){
						$advOpt = $val['advOption'];
						if(isset($advOpt['repeat']) && !empty($advOpt['repeat'])){
							$scene_loop['repeat'] = $advOpt['repeat'];
						}
						if(isset($advOpt['delay']) && !empty($advOpt['delay'])){
							$scene_loop['delay'] = $advOpt['delay'];
						}
						if(isset($advOpt['timing']) && !empty($advOpt['timing'])){
							$scene_loop['timing'] = $advOpt['timing'];
						}
						if(isset($advOpt['easing']) && !empty($advOpt['easing'])){
							$scene_loop['easing'] = $advOpt['easing'];
						}
						if(isset($advOpt['reverse'])){
							$scene_loop['reverse'] = $advOpt['reverse'];
						}
					}else{
						$scene_loop['repeat'] = (object)[ 'md' => 0, ];
						$scene_loop['delay'] = (object)[ 'md' => 0, ];
						$scene_loop['timing'] = (object)[ 'md' => 1, ];
						$scene_loop['reverse'] = true;
					}
					
					if(isset($val['sticky'])){
						$scene_loop['sticky'] = $val['sticky'];
					}
					if(isset($val['develop'])){
						$scene_loop['develop'] = $val['develop'];
						if(isset($val['devName']) && !empty($val['devName'])){
							$scene_loop['develop_name'] = $val['devName'];
						}
					}
					if(!empty($scene_loop)){
						$scrollAttr[] = $scene_loop;
					}
				}
			}
			
			if(isset($attr['MSView']) && !empty($attr['MSView']) ){
				$devices = [];
				foreach($attr['MSView'] as $key => $val){
					$devices[] = $val['value'];
				}
				
				$wrapAttr .= ' data-tpgb-msview="'.htmlspecialchars(json_encode($devices), ENT_QUOTES, 'UTF-8').'"';
			}
			if(!empty($scrollAttr)){
				$wrapAttr .= ' data-tpgb-ms="'.htmlspecialchars(json_encode($scrollAttr), ENT_QUOTES, 'UTF-8').'"';
			}
		}
		return $wrapAttr;
	}
}
Tpgbp_Magic_Scroll_Extra::get_instance();