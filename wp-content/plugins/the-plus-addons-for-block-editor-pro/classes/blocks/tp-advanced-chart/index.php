<?php
/* Block : Advanced Chart
 * @since : 1.2.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_advanced_chart_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$chartType = (!empty($attributes['chartType'])) ? $attributes['chartType'] : 'line';
	$barType = (!empty($attributes['barType'])) ? $attributes['barType'] : 'horizontal';
	$pieType = (!empty($attributes['pieType'])) ? $attributes['pieType'] : 'pie';
	
	$labelValue = (!empty($attributes['labelValue'])) ? $attributes['labelValue'] : 'Jan | Feb | Mar';
	
	$aloneData = (!empty($attributes['aloneData'])) ? $attributes['aloneData'] : '10 | 15 | 20';
	$aloneBG = (!empty($attributes['aloneBG'])) ? $attributes['aloneBG'] : '#f7d78299 | #6fc78499 | #8072fc99';
	$aloneBdr = (!empty($attributes['aloneBdr'])) ? $attributes['aloneBdr'] : '#f7d78299 | #6fc78499 | #8072fc99';
	$dataBox = (!empty($attributes['dataBox'])) ? $attributes['dataBox'] : [];
	$dntDataBox = (!empty($attributes['dntDataBox'])) ? $attributes['dntDataBox'] : [];
	$bblDataBox = (!empty($attributes['bblDataBox'])) ? $attributes['bblDataBox'] : [];
	
	$cPointStyle = (!empty($attributes['cPointStyle'])) ? $attributes['cPointStyle'] : false;
	$pointStyle = (!empty($attributes['pointStyle'])) ? $attributes['pointStyle'] : 'circle';
	$pointBG = (!empty($attributes['pointBG'])) ? $attributes['pointBG'] : '#ff5a6e99';
	$pointNmlSize = (!empty($attributes['pointNmlSize'])) ? $attributes['pointNmlSize'] : '12';
	$pointHvrSize = (!empty($attributes['pointHvrSize'])) ? $attributes['pointHvrSize'] : '14';
	$pointBColor = (!empty($attributes['pointBColor'])) ? $attributes['pointBColor'] : '#ff5a6e99';
	$pointBWidth = (!empty($attributes['pointBWidth'])) ? $attributes['pointBWidth'] : '1';
	
	$smooth = (!empty($attributes['smooth'])) ? $attributes['smooth'] : false;
	$tensionS = (!empty($attributes['tensionS'])) ? $attributes['tensionS'] : 0;
	
	$barSize = (!empty($attributes['barSize'])) ? $attributes['barSize'] : '30';
	$barSpace = (!empty($attributes['barSpace'])) ? $attributes['barSpace'] : '40';
	
	$gridLine = (!empty($attributes['gridLine'])) ? $attributes['gridLine'] : false;
	$gridXColor = (!empty($attributes['gridXColor'])) ? $attributes['gridXColor'] : 'rgba(0,0,0,0.5)';
	$gridYColor = (!empty($attributes['gridYColor'])) ? $attributes['gridYColor'] : 'rgba(0,0,0,0.5)';
	$zeroXLineColor = (!empty($attributes['zeroXLineColor'])) ? $attributes['zeroXLineColor'] : 'rgba(0,0,0,0.25)';
	$zeroYLineColor = (!empty($attributes['zeroYLineColor'])) ? $attributes['zeroYLineColor'] : 'rgba(0,0,0,0.25)';
	$drawBdrX = (!empty($attributes['drawBdrX'])) ? $attributes['drawBdrX'] : false;
	$drawBdrXChart = (!empty($attributes['drawBdrXChart'])) ? $attributes['drawBdrXChart'] : false;
	$xPrePostfix = (!empty($attributes['xPrePostfix'])) ? $attributes['xPrePostfix'] : false;
	$xPreFixText = (!empty($attributes['xPreFixText'])) ? $attributes['xPreFixText'] : '';
	$xPostFixText = (!empty($attributes['xPostFixText'])) ? $attributes['xPostFixText'] : '';
	$drawBdrY = (!empty($attributes['drawBdrY'])) ? $attributes['drawBdrY'] : false;
	$drawBdrYChart = (!empty($attributes['drawBdrYChart'])) ? $attributes['drawBdrYChart'] : false;
	$yPrePostfix = (!empty($attributes['yPrePostfix'])) ? $attributes['yPrePostfix'] : false;
	$yPreFixText = (!empty($attributes['yPreFixText'])) ? $attributes['yPreFixText'] : '';
	$yPostFixText = (!empty($attributes['yPostFixText'])) ? $attributes['yPostFixText'] : '';
	
	$labels = (!empty($attributes['labels'])) ? $attributes['labels'] : false;
	$labelColor = (!empty($attributes['labelColor'])) ? $attributes['labelColor'] : 'rgba(0,0,0,0.25)';
	$labelSize = (!empty($attributes['labelSize'])) ? $attributes['labelSize'] : '12';
	
	$legends = (!empty($attributes['legends'])) ? $attributes['legends'] : false;
	$legendColor = (!empty($attributes['legendColor'])) ? $attributes['legendColor'] : 'rgba(0,0,0,0.25)';
	$legendSize = (!empty($attributes['legendSize'])) ? $attributes['legendSize'] : '12';
	$legendPos = (!empty($attributes['legendPos'])) ? $attributes['legendPos'] : 'top';
	$legendAlign = (!empty($attributes['legendAlign'])) ? $attributes['legendAlign'] : 'center';
	
	$animation = (!empty($attributes['animation'])) ? $attributes['animation'] : 'easeOutQuart';
	$anDuration = (!empty($attributes['anDuration'])) ? $attributes['anDuration'] : '1000';
	
	$tooltip = (!empty($attributes['tooltip'])) ? $attributes['tooltip'] : false;
	$aspctRatio = (!empty($attributes['aspctRatio'])) ? $attributes['aspctRatio'] : false;
	$mAspctRatio = (!empty($attributes['mAspctRatio'])) ? $attributes['mAspctRatio'] : false;
	$tipEvent = (!empty($attributes['tipEvent'])) ? $attributes['tipEvent'] : 'hover';
	$tipFontSize = (!empty($attributes['tipFontSize'])) ? $attributes['tipFontSize'] : '12';
	$tipTitleColor = (!empty($attributes['tipTitleColor'])) ? $attributes['tipTitleColor'] : '#fff';
	$bodyFontColor = (!empty($attributes['bodyFontColor'])) ? $attributes['bodyFontColor'] : '#fff';
	$tooltipBG = (!empty($attributes['tooltipBG'])) ? $attributes['tooltipBG'] : '#ff5a6e99';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$dotBColor=$dotBG='';

	
		$output = $label_data = $get_data = $chart_type = '';	
	
		if($chartType=='bar' && $barType=='horizontal'){
			$chart_type ='horizontalBar';
		}else if($chartType=='pie' && $pieType=='doughnut'){
			$chart_type ='doughnut';
		}else{
			$chart_type = $chartType;
		} 
		
		$options=$datasets=$datasets1=$chart_datasets=[];

		if($chartType=='pie' || $chartType=='polarArea'){
			if($pieType!='doughnut' || $chartType=='polarArea'){
				$alone_data = array_map('floatval', explode('|', $aloneData));
				if(!empty($alone_data)){
					$datasets[] = [ 
						'data' => $alone_data,
						'backgroundColor' => explode('|', $aloneBG),
						'borderColor' => explode('|', $aloneBdr)
					];
				}
			}else{
				foreach($dntDataBox as $index => $item1){
					
					$datasets2['data']  =  array_map('floatval', explode('|', $item1['dntData']));
					
					$datasets2['backgroundColor'] = ($item1['dntBG']) ? explode('|', $item1['dntBG']) : [];
					
					$datasets2['borderColor'] = ($item1['dntBdr']) ? explode('|', $item1['dntBdr']) : [];
					
					$datasets[] = $datasets2;
				}
			}			
			
		}else{
			$chart_datasets = ($chartType=='bubble') ?  $bblDataBox : $dataBox;			
			foreach($chart_datasets as $index => $item){
				if($chartType=='bubble'){
					$datasets1['label'] = $item['bblLabel'];
				} else {
					$datasets1['label'] = $item['label'];
				}
				if ($chartType=='bubble') {
					$datasets1['data'] = tpgb_bubble_array($item['bblData']);
				} else {
					$datasets1['data']  =  array_map('floatval', explode('|', $item['data']));				
				}
				if($chartType=='bubble'){
					if((!empty($item['bblMultiBG'])) && !empty($item['bblMultiDotBG'])) {
						$datasets1['backgroundColor'] = explode('|', $item['bblMultiDotBG']);
					} else {
						$datasets1['backgroundColor'] = !empty($item['bblDotBG']) ? $item['bblDotBG'] : '';
					}
				} else {
					$datasets1['backgroundColor'] = !empty($item['fillBG']) ? $item['fillBG'] : '';
					if($chartType=='bar') {
						if(!empty($item['multiDot']) && !empty($item['multiDotBG'])) {
							$datasets1['backgroundColor'] = explode('|', $item['multiDotBG']);
						} 
					}
					
					$dotBG = '';
					if((!empty($item['multiDot'])) && !empty($item['multiDotBG'])) {
						$dotBG = explode('|', $item['multiDotBG']);
					} else {
						$dotBG = !empty($item['singleDot']) ? $item['singleDot'] : '';
					}
				}
				
				if($chartType=='bubble'){
					if((!empty($item['bblMultiBdr'])) && !empty($item['bblMultiBColor'])){
						$datasets1['borderColor'] = explode('|', $item['bblMultiBColor']);
					} else {
						$datasets1['borderColor'] = !empty($item['bblBColor']) ? $item['bblBColor'] : '';
					}
				} else {
					$datasets1['borderColor'] = !empty($item['bdrColor']) ? $item['bdrColor'] : '';
					if($chartType=='bar'){
						if((!empty($item['multiBdr'])) && !empty($item['dotMultiBColor'])){
							$datasets1['borderColor'] = explode('|', $item['dotMultiBColor']);
						}
					}
					
					$dotBColor = '';
					if((!empty($item['multiBdr'])) && !empty($item['dotMultiBColor'])){
						$dotBColor = explode('|', $item['dotMultiBColor']);
					} else {
						$dotBColor = !empty($item['dotBColor']) ? $item['dotBColor'] : '';
					}
				}
				
				$datasets1['borderDash']=[];
				if(($chartType=='line' || $chartType=='radar') && !empty($item['bDashTgl'])){
					$datasets1['borderDash'] = [5, 5];
				}
				
				if(!empty($item['fillTgl'])){
					 $datasets1['fill'] =true;
				}else{
					 $datasets1['fill'] =false;
				}
				
				if ($chartType=='line' || $chartType=='radar' || $chartType=='bubble'){	
					if($chartType=='line' && !empty($smooth) && !empty($tensionS)){
						 $datasets1['tension']= $tensionS;
					}else{
						 $datasets1['tension']= 0;
					}
					
					if(!empty($cPointStyle)){
						if(!empty($pointStyle)){
							$datasets1['pointStyle'] =$pointStyle;
						}
						if(!empty($pointBG) && $chartType!='bubble'){
							$datasets1['pointBackgroundColor'] = (!empty($dotBG) ? $dotBG : $pointBG);
						}
						if(!empty($pointNmlSize) && $chartType!='bubble'){
							$datasets1['pointRadius'] =(int)$pointNmlSize;
						}
						if(!empty($pointHvrSize) && $chartType!='bubble'){
							$datasets1['pointHoverRadius'] =(int)$pointHvrSize;
						}
						if(!empty($pointBWidth) && $chartType!='bubble'){
							$datasets1['borderWidth'] = (int)$pointBWidth;
						} 
						if(!empty($pointBColor) && $chartType!='bubble'){
							$datasets1['pointBorderColor'] = (!empty($dotBColor) ? $dotBColor : $pointBColor);	
						}
						
					} else {
						if($chartType!='bubble') {
							$datasets1['borderWidth'] = 2;
							$datasets1['pointBackgroundColor'] =$dotBG;
							$datasets1['pointBorderColor'] = $dotBColor;
						}
					}
				}
									
				if ($chartType=='bar'){
					$datasets1['borderWidth'] = 2;
					if(!empty($barSpace)){
						$datasets1['barThickness']= (int)$barSpace; 
					}
					if(!empty($barSize)){
						$datasets1['maxBarThickness']= (int)$barSize; 
					}	
					
			    }	

				$datasets[] = $datasets1;
			} 
		}

		if($chartType=='pie' && $pieType=='pie'){
			$options['cutoutPercentage'] = 0;
		}else if($chartType=='pie' && $pieType=='doughnut'){
			$options['cutoutPercentage'] = 50;
		}else{
			if(!empty($gridLine)){
				$options['scales'] = [
					'yAxes' => [[
						'ticks' => [
							'display' => (!empty($labels) ) ? true : false,
							'fontColor' => $labelColor,
							'fontSize' => (int)$labelSize,
						],
						'gridLines' => [							
							'color'      => $gridYColor,
							'zeroLineColor' => $zeroYLineColor,
							'drawBorder' => ( !empty($drawBdrY)) ? true : false,
							'drawOnChartArea' => (!empty( $drawBdrYChart )) ? true : false,
						]
					]],
					'xAxes' => [[
						'ticks' => [
							'display' => ( !empty($labels)) ? true : false,
							'fontColor' => $labelColor,
							'fontSize' => (int)$labelSize,
						],
						'gridLines' => [							
							'color'      => $gridXColor,
							'zeroLineColor' => $zeroXLineColor,
							'drawBorder' => ( !empty($drawBdrX )) ? true : false,
							'drawOnChartArea' => (!empty( $drawBdrXChart )) ? true : false,							
						]
					]]
				];
			}else{
				$options['scales'] = [
					'yAxes' => [[
						'ticks' => [
							'display' => ( $labels ) ? true : false,
							'fontColor' => $labelColor,
							'fontSize' => (int)$labelSize,
						],
						'gridLines' => [
							'display'    => false,
						]
					]],
					'xAxes' => [[
						'ticks' => [
							'display' => ( $labels ) ? true : false,
							'fontColor' => $labelColor,
							'fontSize' => (int)$labelSize,
						],
						'gridLines' => [
							'display'    => false,
						]
					]]
				];
			}
		}
		
		if (!empty($legends)) {
			if (!empty($legendPos)){
				$options['legend'] = [ 
					'position' => $legendPos,
					'align' => $legendAlign,
					'labels' => [
						'fontColor' => $legendColor,
						'fontSize' => (int)$legendSize,
					],
				];
			}
		}else{
			$options['legend'] = [ 'display' => false ];
		}
		
		if(!empty($animation) && !empty($anDuration)){
			$options['animation'] = [ 'duration' => (int)$anDuration , 'easing' => $animation];
		}
		
		if(!empty($tooltip)) {
			if (!empty($tooltipBG) || !empty($tipTitleColor) || !empty($bodyFontColor)){
				$options['tooltips'] = [ 
					'backgroundColor' => $tooltipBG,
					'titleFontColor' => $tipTitleColor,
					'bodyFontColor' => $bodyFontColor,
					'titleFontSize' => (int)$tipFontSize,
					'bodyFontSize' => (int)$tipFontSize,
				];
			}
			if(!empty($tipEvent) && $tipEvent=='click'){
				$options['events'] = ['click'];
			}
			
		}else{
			$options['tooltips'] = [ 'enabled' => false ];
		}
		
		if (!empty($aspctRatio)) {
			$options['aspectRatio'] = 1;
		}

		if (!empty($mAspctRatio)) {
			$options['maintainAspectRatio'] = false;
		}
		$options['responsive'] = true;
		$dataSetting =[];
		$dataSetting['type'] = $chart_type;
		$dataSetting['data']['labels'] = explode("|", $labelValue);
		$dataSetting['data']['datasets'] = $datasets;
		$dataSetting['options'] = $options;
		$dataSetting = htmlspecialchars(json_encode($dataSetting), ENT_QUOTES, 'UTF-8');


		$dataPrePost = [];
		if(!empty($xPrePostfix)){
			$dataPrePost['xPrePost'] = esc_attr($xPrePostfix);
			$dataPrePost['xPreText'] = esc_attr($xPreFixText);
			$dataPrePost['xPostText'] = esc_attr($xPostFixText);
		}else{
			$dataPrePost['xPrePost'] = esc_attr($xPrePostfix);
		}
		if(!empty($yPrePostfix)){
			$dataPrePost['yPrePost'] = esc_attr($yPrePostfix);
			$dataPrePost['yPreText'] = esc_attr($yPreFixText);
			$dataPrePost['yPostText'] = esc_attr($yPostFixText);
		}else{
			$dataPrePost['yPrePost'] = esc_attr($yPrePostfix);
		}
		$dataPrePost = htmlspecialchars(json_encode($dataPrePost), ENT_QUOTES, 'UTF-8');
	$output = '';
	$output .= '<div class="tpgb-advanced-chart tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'" data-id="tpgb-block-'.esc_attr($block_id).'" data-settings= \'' .$dataSetting. '\' data-prepost= \'' .$dataPrePost. '\'>';
		$output .= '<canvas id="tpgb-block-'.esc_attr($block_id).'"></canvas>';
	$output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
    return $output;
}
function tpgb_bubble_array( $bblData ) {
	$bblData = trim( $bblData );		
	$split_value = preg_match_all( '#\[([^\]]+)\]#U', $bblData, $fetch_and_match );		
	if ( !$split_value ) {
		return [];
	}else {
		$data_value = $fetch_and_match[1];
		$bubble = [];
		foreach ( $data_value as $item_value ) {
			$item_value = trim( $item_value, '][ ' );
			$item_value = explode( '|', $item_value );
			
			if (count($item_value) != 3){
				continue;
			}					
			
			$x_y_r = new \stdClass();
			$x_y_r->x = floatval( trim( $item_value[0] ) );
			$x_y_r->y = floatval( trim( $item_value[1] ) );
			$x_y_r->r = floatval( trim( $item_value[2] ) );
			$bubble[] = $x_y_r;
		}
		return $bubble;
	}
}
/**
 * Render for the server-side
 */
function tpgb_advanced_chart() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
  
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'chartType' => [
			'type' => 'string',
			'default' => 'line',	
		],
		'barType' => [
			'type' => 'string',
			'default' => 'horizontal',	
		],
		'pieType' => [
			'type' => 'string',
			'default' => 'doughnut',	
		],
		'labelValue' => [
			'type' => 'string',
			'default' => 'Jan | Feb | Mar',	
		],
		'dataBox' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'label' => [
						'type' => 'string',
						'default' => 'Label'
					],
					'data' => [
						'type' => 'string',
						'default' => '0|25|42',	
					],
					'multiDot' => [
						'type' => 'boolean',
						'default' => false,	
					],
					'fillBG' => [
						'type' => 'string',
						'default' => '#ffff99',
					],
					'bdrColor' => [
						'type' => 'string',
						'default' => '#ffff00',
					],
					'singleDot' => [
						'type' => 'string',
						'default' => '#ffff00',
					],
					'multiDotBG' => [
						'type' => 'string',
						'default' => '#f7d78299 | #6fc78499 | #8072fc99',	
					],
					'multiBdr' => [
						'type' => 'boolean',
						'default' => false,	
					],
					'dotBColor' => [
						'type' => 'string',
						'default' => '#ffff99',
					],
					'dotMultiBColor' => [
						'type' => 'string',
						'default' => '#f7d782 | #6fc784 | #8072fc',	
					],
					'fillTgl' => [
						'type' => 'boolean',
						'default' => false,	
					],
					'bDashTgl' => [
						'type' => 'boolean',
						'default' => false,	
					],
				],
			],
			'default' => [
				[
					'_key' => '0',
					'label' => 'Jan',
					'data' => '25|15|30',
					'fillBG' =>'#ff9999',
					'bdrColor' => '#ff0000',
					'multiDot' => false, 
					'singleDot' => '#ff9999',
					'multiDotBG' => '#f7d78299 | #6fc78499 | #8072fc99',
					'multiBdr' => false,
					'dotBColor' => '#ff0000',
					'dotMultiBColor' => '#f7d782 | #6fc784 | #8072fc'
				],
				[
					'_key' => '1',
					'label' => 'Feb',
					'data' => '12|18|28',
					'fillBG' =>'#99ff99',
					'bdrColor' => '#00ff00',
					'multiDot' => false,
					'singleDot' => '#99ff99',
					'multiDotBG' => '#f7d78299 | #6fc78499 | #8072fc99',
					'multiBdr' => false,
					'dotBColor' => '#00ff00',
					'dotMultiBColor' => '#f7d782 | #6fc784 | #8072fc'
				],
				[
					'_key' => '2',
					'label' => 'Mar',
					'data' => '11|20|40',
					'fillBG' =>'#9999ff',
					'bdrColor' => '#0000ff',
					'multiDot' => false,
					'singleDot' => '#9999ff',
					'multiDotBG' => '#f7d78299 | #6fc78499 | #8072fc99',
					'multiBdr' => false,
					'dotBColor' => '#0000ff',
					'dotMultiBColor' => '#f7d782 | #6fc784 | #8072fc'
				],
			],
		],
		'aloneData' => [
			'type' => 'string',
			'default' => '10 | 15 | 20',	
		],
		'aloneBG' => [
			'type' => 'string',
			'default' => '#f7d782 | #6fc784 | #8072fc',	
		],
		'aloneBdr' => [
			'type' => 'string',
			'default' => '#f7d78299 | #6fc78499 | #8072fc99',	
		],
		'dntDataBox' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'dntLabel' => [
						'type' => 'string',
						'default' => 'Label'
					],
					'dntData' => [
						'type' => 'string',
						'default' => '0 | 25 | 42',	
					],
					'dntBG' => [
						'type' => 'string',
						'default' => '#ff5a6e99 | #8072fc99 | #6fc78499',	
					],
					'dntBdr' => [
						'type' => 'string',
						'default' => '#000000 | #000000 | #000000',	
					],
				],
			],
			'default' => [
				[
					'_key' => '0',
					'dntLabel' => 'Jan',
					'dntData' => '25 | 15 | 30',
					'dntBG' => '#ff5a6e99 | #8072fc99 | #6fc78499',
					'dntBdr' => '#000000 | #000000 | #000000',
				],
				[
					'_key' => '1',
					'dntLabel' => 'Feb',
					'dntData' => '12 | 18 | 28',
					'dntBG' => '#f7d78299 | #6fc78499 | #8072fc99',
					'dntBdr' => '#40e0d0 | #40e0d0 | #40e0d0',
				],
				[
					'_key' => '2',
					'dntLabel' => 'Mar',
					'dntData' => '11 | 20 | 40',
					'dntBG' => '#71d1dc99 | #8072fc99 | #ff5a6e99',
					'dntBdr' => '#000000 | #000000 | #000000',
				],
			],
		],
		
		'bblDataBox' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'bblLabel' => [
						'type' => 'string',
						'default' => 'Label'
					],
					'bblData' => [
						'type' => 'string',
						'default' => '[23|34|28][8|16|24][4|12|18]',	
					],
					'bblMultiBG' => [
						'type' => 'boolean',
						'default' => false,	
					],
					'bblDotBG' => [
						'type' => 'string',
						'default' => '#ff4f0f99',
					],
					'bblMultiDotBG' => [
						'type' => 'string',
						'default' => '#ff4f0f99 | #6fc78499 | #8072fc99',	
					],
					'bblMultiBdr' => [
						'type' => 'boolean',
						'default' => false,	
					],
					'bblBColor' => [
						'type' => 'string',
						'default' => '#ff4f0f',
					],
					'bblMultiBColor' => [
						'type' => 'string',
						'default' => '#f7d78299 | #6fc78499 | #8072fc99',	
					],
				],
			],
			'default' => [
				[
					'_key' => '0',
					'bblLabel' => 'Jan',
					'bblData' => '[5 | 15 | 15] [10 | 18 | 12] [7 | 14 | 14]',
					'bblDotBG' => '#f7d78299',
					'bblBColor' => '#f7d782',
					'bblMultiBG' => false,
					'bblMultiDotBG' => '#f7d78299 | #6fc78499 | #8072fc99',
					'bblMultiBdr' => false,
					'bblMultiBColor' => '#f7d782 | #6fc784 | #8072fc',
				],
				[
					'_key' => '1',
					'bblLabel' => 'Feb',
					'bblData' => '[7 | 10 | 16] [15 | 14 | 18] [15 | 17 | 12]',
					'bblDotBG' => '#6fc78499',
					'bblBColor' => '#6fc784',
					'bblMultiBG' => false,
					'bblMultiDotBG' => '#f7d78299 | #6fc78499 | #8072fc99',
					'bblMultiBdr' => false,
					'bblMultiBColor' => '#f7d782 | #6fc784 | #8072fc',
				],
				[
					'_key' => '2',
					'bblLabel' => 'Mar',
					'bblData' => '[9 | 20 | 12] [8 | 16 | 16] [14 | 24 | 20]',
					'bblDotBG' => '#8072fc99',
					'bblBColor' => '#8072fc',
					'bblMultiBG' => false,
					'bblMultiDotBG' => '#f7d78299 | #6fc78499 | #8072fc99',
					'bblMultiBdr' => false,
					'bblMultiBColor' => '#f7d782 | #6fc784 | #8072fc',
				],
			],
		],
		
		'barSize' => [
			'type' => 'string',
			'default' => '30',
			'scopy' => true,
		],
		'barSpace' => [
			'type' => 'string',
			'default' => '40',
			'scopy' => true,
		],
		
		'gridLine' => [
			'type' => 'boolean',
			'default' => true,	
			'scopy' => true,
		],
		'gridXColor' => [
			'type' => 'string',
			'default' => 'rgba(0,0,0,0.5)',
			'scopy' => true,
		],
		'zeroXLineColor' => [
			'type' => 'string',
			'default' => 'rgba(0,0,0,0.25)',
			'scopy' => true,
		],
		'gridYColor' => [
			'type' => 'string',
			'default' => 'rgba(0,0,0,0.5)',
			'scopy' => true,
		],
		'zeroYLineColor' => [
			'type' => 'string',
			'default' => 'rgba(0,0,0,0.25)',
			'scopy' => true,
		],
		'drawBdrX' => [
			'type' => 'boolean',
			'default' => true,
			'scopy' => true,
		],
		'drawBdrXChart' => [
			'type' => 'boolean',
			'default' => true,	
			'scopy' => true,
		],
		'xPrePostfix' => [
			'type' => 'boolean',
			'default' => false,	
			'scopy' => true,
		],
		'xPreFixText' => [
			'type' => 'string',
			'default' => '',	
			'scopy' => true,
		],
		'xPostFixText' => [
			'type' => 'string',
			'default' => '',	
			'scopy' => true,
		],
		'drawBdrY' => [
			'type' => 'boolean',
			'default' => true,	
			'scopy' => true,
		],
		'drawBdrYChart' => [
			'type' => 'boolean',
			'default' => true,	
			'scopy' => true,
		],
		'yPrePostfix' => [
			'type' => 'boolean',
			'default' => false,	
			'scopy' => true,
		],
		'yPreFixText' => [
			'type' => 'string',
			'default' => '',	
			'scopy' => true,
		],
		'yPostFixText' => [
			'type' => 'string',
			'default' => '',	
			'scopy' => true,
		],
		'labels' => [
			'type' => 'boolean',
			'default' => true,	
			'scopy' => true,
		],
		'labelColor' => [
			'type' => 'string',
			'default' => '#000000',
			'scopy' => true,
		],
		'labelSize' => [
			'type' => 'string',
			'default' => '12',
			'scopy' => true,
		],
		'legends' => [
			'type' => 'boolean',
			'default' => true,	
			'scopy' => true,
		],
		'legendColor' => [
			'type' => 'string',
			'default' => '#000000',
			'scopy' => true,
		],
		'legendSize' => [
			'type' => 'string',
			'default' => '12',
			'scopy' => true,
		],
		'legendPos' => [
			'type' => 'string',
			'default' => 'top',
			'scopy' => true,
		],
		'legendAlign' => [
			'type' => 'string',
			'default' => 'center',
			'scopy' => true,
		],
		'smooth' => [
			'type' => 'boolean',
			'default' => true,	
			'scopy' => true,
		],
		'tensionS' => [
			'type' => 'string',
			'default' => '0.4',
			'scopy' => true,
		],
		'cPointStyle' => [
			'type' => 'boolean',
			'default' => false,	
			'scopy' => true,
		],
		'pointStyle' => [
			'type' => 'string',
			'default' => 'circle',
			'scopy' => true,
		],
		'pointBG' => [
			'type' => 'string',
			'default' => '#ff5a6e99',
			'scopy' => true,
		],
		'pointNmlSize' => [
			'type' => 'string',
			'default' => '12',
			'scopy' => true,
		],
		'pointHvrSize' => [
			'type' => 'string',
			'default' => '14',
			'scopy' => true,
		],
		'pointBColor' => [
			'type' => 'string',
			'default' => '#ff5a6e99',
			'scopy' => true,
		],
		'pointBWidth' => [
			'type' => 'string',
			'default' => '1',
			'scopy' => true,
		],
		'tooltip' => [
			'type' => 'boolean',
			'default' => true,	
			'scopy' => true,
		],
		'tipEvent' => [
			'type' => 'string',
			'default' => 'hover',
			'scopy' => true,
		],
		'tipFontSize' => [
			'type' => 'string',
			'default' => '12',
			'scopy' => true,
		],
		'tipTitleColor' => [
			'type' => 'string',
			'default' => '#fff',
			'scopy' => true,
		],
		'bodyFontColor' => [
			'type' => 'string',
			'default' => '#fff',
			'scopy' => true,
		],
		'tooltipBG' => [
			'type' => 'string',
			'default' => '#ff5a6e99',
			'scopy' => true,
		],
		'aspctRatio' => [
			'type' => 'boolean',
			'default' => false,	
			'scopy' => true,
		],
		'mAspctRatio' => [
			'type' => 'boolean',
			'default' => false,	
			'scopy' => true,
		],
		'chartHeight' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mAspctRatio', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} { height: {{chartHeight}}; }',
				],
			],
			'scopy' => true,
		],
		'animation' => [
			'type' => 'string',
			'default' => 'easeOutQuart',	
			'scopy' => true,
		],
		'anDuration' => [
			'type' => 'string',
			'default' => '1000',
			'scopy' => true,
		],
	);
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-advanced-chart', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_advanced_chart_render_callback'
    ) );
}
add_action( 'init', 'tpgb_advanced_chart' );