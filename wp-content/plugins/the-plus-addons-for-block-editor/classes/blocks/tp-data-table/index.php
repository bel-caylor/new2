<?php
/* Block : Data Table
 * @since : 1.1.3
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_datatable_callback( $attributes, $content) {
	$DataTable = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
    $ContentTable = (!empty($attributes['ContentTable'])) ? $attributes['ContentTable'] : '';
    $TableHeader = (!empty($attributes['TableHeader'])) ? $attributes['TableHeader'] : [];
    $Tablebody = (!empty($attributes['Tablebody'])) ? $attributes['Tablebody'] : [];
	$TbSort = (!empty($attributes['TbSort'])) ? $attributes['TbSort'] : false;
    $IconPosition = (!empty($attributes['IconPosition'])) ? $attributes['IconPosition'] : 'left';
    $ImgPosition = (!empty($attributes['ImgPosition'])) ? $attributes['ImgPosition'] : 'left';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
    $sorting = (!empty($TbSort)) ? 'yes' : 'no';
    $Search = 'no';
    $Filter = 'no';

    $DTHeader = '';
    $DTBody = '';

    $DataTable .= '<div class="tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'">';
        $DataTable .= '<div class="tpgb-table-wrapper">';
            $DataTable .= '<table class="tpgb-table" id="tpgb-table-id-'.esc_attr($block_id).'" data-id="'.esc_attr($block_id).'" data-sort-table="'.esc_attr($sorting).'" data-show-entry="'.esc_attr($Filter).'" data-searchable="'.esc_attr($Search).'" role="grid">';

                if( $ContentTable =='custom' ){
                    $row_count_tb = count( $TableHeader );
                    $headerArray = array();
                    $headerArrayicon = array();
                    $headerArrayimage = array();
                    if ( $row_count_tb > 1 ) {
                        $counter_row = 1;
                        $inline_count = 0;
                        $cell_col_count = 0;
                        $first_row_th = true;
                        $Mob_thc = 0;

                        foreach ( $TableHeader as $index => $item ) {
                            $ThIcon= '';
                            $ThImg = '';
                            $thColumnSpan = (!empty($item['thColumnSpan'])) ? $item['thColumnSpan'] : 1;
                            $thRowSpan = (!empty($item['thRowSpan'])) ? $item['thRowSpan'] : 1;
							$checkText = (!empty($item['thtext']) ? '' : ' less-icon-space');
                            if( (!empty($item['thDRicon'])) && $item['thDRicon'] == 'icon' && !empty($item['thicon']) ){
                                $ThIcon = '<span class="tpgb-align-icon--'.esc_attr($IconPosition).esc_attr($checkText).'"><i class="'.esc_attr($item['thicon']).' tableicon"></i></span>';
                            }else if(!empty($item['thDRicon']) && $item['thDRicon'] == 'image' && !empty($item['thDRimage'])) {
                                $Thimagesize = (!empty($item['thimagesize'])) ? $item['thimagesize'] : 'thumbnail';
                                $ThImgID = $item['thDRimage']['id'];
                                $ThImgurl = wp_get_attachment_image( $ThImgID,$Thimagesize, false, ['class' => 'tpgb-col-img--'.esc_attr($IconPosition).esc_attr($checkText) ] );
                                $ThImg = $ThImgurl;
                            }

                            if( $item['thAction'] === 'cell' ){
                                $DTHeader .= '<th class="tpgb-table-col tp-repeater-item-'.esc_attr($item['_key']).'" colspan="'.esc_attr($thColumnSpan).'" rowspan="'.esc_attr($thRowSpan).'" data-sort="'.esc_attr($cell_col_count).'" scope="col">';
                                        $DTHeader .= '<span class="tpgb-table__text">';
                                            $DTHeader .= ( $IconPosition == 'left' ) ? $ThIcon : '';
                                            $DTHeader .= ( $ImgPosition == 'left') ? $ThImg : '';
												$DTHeader .= (!empty($item['thtext']) ? '<span class="tpgb-table__text-inner">'.wp_kses_post($item['thtext']).'</span>' : '');
                                            $DTHeader .= ( $IconPosition == 'right' ) ? $ThIcon : '';
                                            $DTHeader .= ( $ImgPosition == 'right') ? $ThImg : '';
                                        $DTHeader .= '</span>';
                                        $DTHeader .= '<span class="tpgb-sort-icon">';
                                            if(!empty($TbSort)){
                                                $DTHeader .= '<i class="up-icon fas fa-sort-up"></i>';
                                                $DTHeader .= '<i class="down-icon fas fa-sort-down"></i>';
                                            }
                                        $DTHeader .= '</span>';
                                $DTHeader .= '</th>';
                                
                                    $headerArray[$Mob_thc] = wp_kses_post($item['thtext']);
                                    $headerArrayicon[$Mob_thc] = $ThIcon;
                                    $headerArrayimage[$Mob_thc] = $ThImg;

                                $Mob_thc++;
                                $cell_col_count++;
                            }else {
                                if ( $counter_row > 1 && $counter_row < $row_count_tb ) {
                                    $DTHeader .= '</tr><tr class="tpgb-table-row" role="row">';                                    
                                    $first_row_th = false;
                                } elseif ( 1 === $counter_row && "row" === $attributes['TableHeader'][0]['thAction'] ) {                                    
                                    $DTHeader .= '<tr class="tpgb-table-row" role="row">';
                                }
                                $Mob_thc = 0;
                            }   
                            $counter_row++;
                            $inline_count++;
                        }  
                    }          
                    
                    $row_count = count( $Tablebody );
                    if ( $row_count > 1 ) {
                        $counter = 1;	
                        $cell_inline_count = 0;
                        $data_entry_col = 0;
                        $Mob_trc = 0;
                    
                        foreach ( $Tablebody as $index => $item ) {
                            if( $item['trAction'] == 'cell' ){
                                $TrColumnSpan = (!empty($item['TrColumnSpan'])) ? $item['TrColumnSpan'] : 1;
                                $TrRowSpan = (!empty($item['TrRowSpan'])) ? $item['TrRowSpan'] : 1;
                                $Tag = (!empty($item['TrHeading']) && $item['TrHeading'] == 'th') ? $item['TrHeading'] : 'td';
                                $Btntx = (!empty($item['Trbtntext']) ? $item['Trbtntext'] : __('Click Here','tpgb') );
                                $Btnlink = (!empty($item['TrbtnLink']) && !empty($item['TrbtnLink']['url'])) ? 'href="'.esc_url($item['TrbtnLink']['url']).'"' : '';
                                $target = ( !empty($item['TrbtnLink']['target'])) ? 'target="_blank"' : '';
				                $nofollow = (!empty($item['TrbtnLink']['nofollow'])) ? 'rel="nofollow"' : '';
                                
								$TRIcon = '';
                                $TRImg = '';
                                
								$checkText = (!empty($item['trtext']) ? '' : ' less-icon-space');

                                if( !empty($item['trDricon']) && $item['trDricon'] == 'icon' && !empty($item['TrfaIcon']) ){
                                    $TRIcon = '<span class="tpgb-align-icon--'.esc_attr($IconPosition).$checkText.'"><i class="'.esc_attr($item['TrfaIcon']).' tableicon"></i></span>';
                                }else if(!empty($item['trDricon']) && $item['trDricon'] == 'image' && !empty($item['trDrimage'])){
                                    $TRimagesize = (!empty($item['trimagesize'])) ? $item['trimagesize'] : 'thumbnail';
                                    $TRDrimgid = (!empty($item['trDrimage']['id'])) ? $item['trDrimage']['id'] : '';
                                    $TRImgurl = wp_get_attachment_image( $TRDrimgid,$TRimagesize, false, ['class' => 'tpgb-col-img--'.esc_attr($IconPosition).$checkText ] );
                                    $TRImg = $TRImgurl;
                                }
                                $DTBody .= '<'.Tp_Blocks_Helper::validate_html_tag($Tag).' class="tpgb-table-col tp-repeater-item-'.esc_attr($item['_key']).'"  colspan="'.esc_attr($TrColumnSpan).'" rowspan="'.esc_attr($TrRowSpan).'">';
                                    
                                    if( !empty($item['TrLink']) && !empty($item['TrLink']['url']) ){
										$target1 = ( !empty ($item['TrLink']['target'])) ? 'target="_blank"' : '';
                                        $nofollow1= ( !empty($item['TrLink']['nofollow']) ) ? 'rel="nofollow"' : '';
										$link_attr = Tp_Blocks_Helper::add_link_attributes($item['TrLink']);
                                        $DTBody.='<a href="'.esc_url($item['TrLink']['url']).'" class="tb-col-link tpgb-relative-block " '.$target1.'  '.$nofollow1.' '.$link_attr.'>';
                                    }
                                    if((isset($item['trtext']) && $item['trtext'] != '') || $TRIcon != '' || $TRImg != '' ){
                                        $DTBody .= '<span class="tpgb-table__text">';
                                            $DTBody .= ( $IconPosition == 'left') ? $TRIcon : '';
                                            $DTBody .= ( $ImgPosition == 'left') ? $TRImg : '';
                                                $DTBody .= (!empty($item['trtext']) ? '<span class="tpgb-table__text-inner">'.wp_kses_post($item['trtext']).'</span>' : '');
                                            $DTBody .= ( $IconPosition == 'right') ? $TRIcon : '';
                                            $DTBody .= ( $ImgPosition == 'right') ? $TRImg : '';
                                        $DTBody .= '</span>';   
                                    }
									
									if( !empty($item['TrLink']) && !empty($item['TrLink']['url']) ){
										$DTBody .= '</a>';
									}

                                    if( (!empty($item['Trbtn'])) && $item['Trbtn'] == TRUE ){
										$btn_attr = Tp_Blocks_Helper::add_link_attributes($item['TrbtnLink']);
                                        $DTBody .='<div class="pt_tpgb_button tp-repeater-item-'.esc_attr($item['_key']).' button-style-8">';
                                            $DTBody .='<a '.(!empty($Btnlink) ? $Btnlink : 'href="#"').'  '.$target.' '.$nofollow.' class="button-link-wrap" '.$btn_attr.'>'.wp_kses_post($Btntx).'</a>';
                                        $DTBody .='</div>';
                                    }
                                 
                                $DTBody .= '</'.Tp_Blocks_Helper::validate_html_tag($Tag).'>';
                                
                                $Mob_trc++;
                            }else{
                                if ( $counter > 1 && $counter < $row_count ) {
                                    $data_entry_col++;
                                    $DTBody .= '</tr><tr data-entry="'.esc_attr($data_entry_col).'" class="tpgb-table-row odd" role="row">';
                                } elseif ( 1 === $counter && "row" === $attributes['Tablebody'][0]['trAction'] ) {
                                    $data_entry_col = 1;
                                    $DTBody .= '<tr data-entry="'.esc_attr($data_entry_col).'" class="tpgb-table-row odd" role="row">';
                                }
                                $Mob_trc = 0;
                            }
                            $counter++;
                            $cell_inline_count++;
                        }                        
                    }

                        $DataTable .= '<thead>';
                            $DataTable .= $DTHeader;
                        $DataTable .= '</thead>';

                        $DataTable .= '<tbody>';
                            $DataTable .= $DTBody;
                        $DataTable .= '</tbody>';
                }

                $DataTable .= '</table>';
        $DataTable .= '</div>';
    $DataTable .= '</div>';
	
	$DataTable = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $DataTable);
	
    return $DataTable;
}

function tpgb_tp_datatable_render() {
    $globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
    $globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
    $globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
    $attributesOptions = [
        'block_id' => [
            'type' => 'string',
            'default' => '',
        ],
        'ContentTable' => [
            'type' => 'string',
            'default' => 'custom',	
        ],
        'TableHeader' => [
            'type'=> 'array',
            'repeaterField' => [
                (object) [
                    'thAction' => [
                        'type' => 'string',
                        'default' =>'cell',	
                    ],
                    'thtext' => [
                        'type'=> 'string',
                        'default'=> 'New Heading',
                    ],
                    'thDRicon' => [
                        'type' => 'string',
                        'default' => 'none',	
                    ],
                    'thicon' => [
                        'type'=> 'string',
                        'default'=> '',
                        'style' => [
                            (object) [
                                'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom'],
                                                (object) ['key' => 'thDRicon', 'relation' => '==', 'value' => 'icon']],
                                'selector' => '{{PLUS_WRAP}} tbody .tpgb-table-row{ background-color: {{TBbgCR}}; }',
                            ],
                        ],
                    ],
                    'thDRimage' => [
                        'type' => 'object',
                        'default' => [
                            'url' => '',
                            'Id' => '',
                        ],
                    ],
                    'thimagesize' => [
                        'type' =>'string',
                        'default' =>'thumbnail',	
                    ],
                    'thColumnSpan' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'thRowSpan' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'resColWidth' => [
                        'type' => 'boolean',
                        'default' => false,	
                    ],
                    'thColumnWidth' => [
                        'type' => 'object',
                        'default' => '',
                        'style' => [
                            (object) [
                                'condition' => [(object) ['key' => 'resColWidth', 'relation' => '==', 'value' => false]],
                                'selector' => '{{PLUS_WRAP}} th{{TP_REPEAT_ID}}{width:{{thColumnWidth}}px;}',
                            ],
                        ],
                    ],
                    'thResColumnWidth' => [
                        'type' => 'object',
                        'default' => [ 
                            'md' => '',
                            "unit" => 'px',
                        ],
                        'style' => [
                            (object) [
                                'condition' => [(object) ['key' => 'resColWidth', 'relation' => '==', 'value' => true]],
                                'selector' => '{{PLUS_WRAP}} th{{TP_REPEAT_ID}}{width:{{thResColumnWidth}};}',
                            ],
                        ],
                        'scopy' => true,
                    ],
                    'ThTextAlignment' => [
                        'type' => 'string',
                        'default' => 'center',
                        'style' => [
                            (object) [
                                'selector' => '{{PLUS_WRAP}} thead th.tpgb-table-col{{TP_REPEAT_ID}}, {{PLUS_WRAP}} thead tr th{{TP_REPEAT_ID}} {text-align:{{ThTextAlignment}};}',
                                            
                            ],
                        ],
                    ],
                    'thColor' => [
                        'type' => 'string',
                        'default' => '',
                        'style' => [
                            (object) [
                                'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-table-row,
                                            {{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-table__text{ color: {{thColor}}; }',
                            ],
                        ],
                    ],
                    'thBGColor' => [
                        'type' => 'string',
                        'default' => '',
                        'style' => [
                            (object) [
                                'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.tpgb-table-col{ background-color: {{thBGColor}}; }',
                            ],
                        ],
                    ],
                ],
            ],
            'default' => [ 
                ['_key'=> 'r1','thAction'=>'row'],
                ['_key'=> 'r2','thAction'=>'cell','thtext'=>'ID', 'resColWidth'=> false],
                ['_key'=> 'r3','thAction'=>'cell','thtext'=>'Title 1', 'resColWidth'=> false],
                ['_key'=> 'r4','thAction'=>'cell','thtext'=>'Title 2', 'resColWidth'=> false],
            ],
        ],
        'Tablebody' => [
            'type'=> 'array',
            'repeaterField' => [
                (object) [
                    'trAction' => [
                        'type' => 'string',
                        'default' =>'cell',	
                    ],
                    'trtext' => [
                        'type'=> 'string',
                        'default'=> 'New cell',
                    ],
                    'TrLink' => [
                        'type'=> 'object',
                        'default'=> [
                            'url' => '',	    
                            'target' => '',	   
                            'nofollow' => ''	
                        ],
                    ],
                    'Trbtn' => [
                        'type' => 'boolean',
                        'default' => false,	
                    ],
                    'TrbtnStyle' => [
                        'type' => 'string',
                        'default' => 'Style-8',	
                    ],
                    'Trbtntext' => [
                        'type'=> 'string',
                        'default'=> 'Click Here',
                    ],
                    'TrbtnLink' => [
                        'type'=> 'object',
                        'default'=> [
                            'url' => '',	    
                            'target' => '',	    
                            'nofollow' => ''	
                        ],
                    ],
                    'ShowTitle' => [
                        'type' => 'boolean',
                        'default' => false,	
                    ],
                    'CustomAttributes' => [
                        'type'=> 'string',
                        'default'=> '',
                    ],
                    'trDricon' => [
                        'type' => 'string',
                        'default' => 'none',	
                    ],
                    'TrfaIcon' => [
                        'type'=> 'string',
                        'default'=> '',
                    ],
                    'TrIconcolor' => [
                        'type' => 'string',
                        'default' => '',
                        'style' => [
                            (object) [
                                'condition' => [(object) ['key' => 'trDricon', 'relation' => '==', 'value' => 'icon']],
                                'selector' => '{{PLUS_WRAP}} .tpgb-table-row td{{TP_REPEAT_ID}} .tpgb-table__text .tableicon{color:{{TrIconcolor}};}',
                            ], 
                        ],
                    ],
                    'trDrimage' => [
                        'type' => 'object',
                        'default' => [
                            'url' => '',
                            'Id' => '',
                        ],
                    ],
                    'trimagesize' => [
                        'type' => 'string',
                        'default' => 'thumbnail',	
                    ],
                    'TrTextAlignment' => [
                        'type' => 'string',
                        'default' => 'center',
                        'style' => [
                            (object) [
                                'selector' => '{{PLUS_WRAP}} tbody td.tpgb-table-col{{TP_REPEAT_ID}}, {{PLUS_WRAP}} tbody tr th{{TP_REPEAT_ID}} {text-align:{{TrTextAlignment}};}',
                                            
                            ],
                        ],
                    ],
                    'TrColumnSpan' => [
                        'type' => 'object',
                        'default' =>'',
                    ],
                    'TrRowSpan' => [
                        'type' => 'string',
                        'default' => '',
                    ],
                    'TrHeading' => [
                        'type' => 'string',
                        'default' => 'td',	
                    ],
                ],
            ],
            'default' => [ 
                ['_key'=> '0','trAction'=>'row'],
                ['_key'=> '1','trAction'=>'cell','trtext'=>'Sample #1'],
                ['_key'=> '2','trAction'=>'cell','trtext'=>'Row 1, Content 1'],
                ['_key'=> '3','trAction'=>'cell','trtext'=>'Row 1, Content 2'],
                ['_key'=> '4','trAction'=>'row'],
                ['_key'=> '5','trAction'=>'cell','trtext'=>'Sample #2'],
                ['_key'=> '6','trAction'=>'cell','trtext'=>'Row 2, Content 1'],
                ['_key'=> '7','trAction'=>'cell','trtext'=>'Row 2, Content 2'],
                ['_key'=> '8','trAction'=>'row'],
                ['_key'=> '9','trAction'=>'cell','trtext'=>'Sample #3'],
                ['_key'=> '10','trAction'=>'cell','trtext'=>'Row 3, Content 1'],
                ['_key'=> '11','trAction'=>'cell','trtext'=>'Row 3, Content 2'],
            ],
        ], 

        'TbSearch' => [
            'type' => 'boolean',
            'default' => false,	
        ],
        'TbSort' => [
            'type' => 'boolean',
            'default' => false,	
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-sort-icon{ display : block }',
                ],
            ],
            'scopy' => true,
        ],
        'TbFilter' => [
            'type' => 'boolean',
            'default' => false,	
        ],
        'MResponsive' => [
            'type' => 'string',
            'default' => 'swipe',	
        ],  

        'ThAlignment' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} thead th.tpgb-table-col,{{PLUS_WRAP}} tbody tr th{text-align:{{ThAlignment}};}',
                ],
            ],
			'scopy' => true,
        ],
        'ThTypo' => [
            'type'=> 'object',
            'default'=> (object) [
                'openTypography' => 0,
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} th.tpgb-table-col,{{PLUS_WRAP}} thead tr th',
                ],
            ],
			'scopy' => true,
        ],
        'ThPadding' => [
            'type' => 'object',
            'default' => (object) [ 
                'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} thead tr.tpgb-table-row th.tpgb-table-col,{{PLUS_WRAP}} tbody tr th{padding:{{ThPadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'ThRTxCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} thead .tpgb-table-row th .tpgb-table__text,{{PLUS_WRAP}} tbody tr th{color:{{ThRTxCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'ThRBgCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} thead .tpgb-table-row th,{{PLUS_WRAP}} table tbody>tr:nth-child(odd)>th,{{PLUS_WRAP}} tbody tr:nth-child(even)>th{background-color:{{ThRBgCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'ThABorder' => [
            'type' => 'boolean',
            'default' => false,	
			'scopy' => true,
        ],
        'ThBorderType' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,	
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ThABorder', 'relation' => '==', 'value' => true]],
                    'selector' => '{{PLUS_WRAP}} thead th.tpgb-table-col,{{PLUS_WRAP}} tbody tr th.tpgb-table-col,{{PLUS_WRAP}} thead tr th',
                ],
            ],
			'scopy' => true,
        ],
        'ThHTxCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} thead .tpgb-table-row:hover .tpgb-table__text,{{PLUS_WRAP}} tbody .tpgb-table-row:hover th .tpgb-table__text,{{PLUS_WRAP}} .csv-html-table tr:hover th{color:{{ThHTxCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'ThHBgCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} thead .tpgb-table-row:hover > th,{{PLUS_WRAP}} .tpgb-table tbody .tpgb-table-row:hover > th,{{PLUS_WRAP}} .thead tr:hover > th{background-color:{{ThHBgCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'ThHCellCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} thead th.tpgb-table-col:hover .tpgb-table__text,{{PLUS_WRAP}} tbody .tpgb-table-row th.tpgb-table-col:hover .tpgb-table__text,{{PLUS_WRAP}} .csv-html-table tr th:hover{color:{{ThHCellCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'ThHCellBGCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} thead .tpgb-table-row th.tpgb-table-col:hover,{{PLUS_WRAP}} .tpgb-table tbody .tpgb-table-row:hover >  th.tpgb-table-col:hover,{{PLUS_WRAP}} .csv-html-table tr th:hover{ background-color: {{ThHCellBGCr}}; }',
                ],
            ],
			'scopy' => true,
        ],

        'TBAlignment' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} tbody td.tpgb-table-col{text-align:{{TBAlignment}};}',
                ],
            ],
			'scopy' => true,
        ],
        'TBvAlignment' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} tbody .tpgb-table-col{vertical-align:{{TBvAlignment}};}',
                ],
            ],
			'scopy' => true,
        ],
        'TBTypo' => [
            'type'=> 'object',
            'default'=> (object) [
                'openTypography' => 0,
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} td .tpgb-table__text-inner,{{PLUS_WRAP}} td .tpgb-align-icon--left,{{PLUS_WRAP}} td .tpgb-align-icon--right,{{PLUS_WRAP}} td',
                ],
            ],
			'scopy' => true,
        ],
        'TBPadding' => [
            'type' => 'object',
            'default' => (object) [ 
                'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} tbody td.tpgb-table-col,{{PLUS_WRAP}} tbody span.tpgb-table__text-inner{padding:{{TBPadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'TBrTxCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} tbody td.tpgb-table-col .tpgb-table__text,{{PLUS_WRAP}} tbody td{color:{{TBrTxCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'TBStripEff' => [
            'type' => 'boolean',
            'default' => false,	
			'scopy' => true,
        ],
        'TBbgCR' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'TBStripEff', 'relation' => '==', 'value' => false]],
                    'selector' => '{{PLUS_WRAP}} tbody .tpgb-table-row,{{PLUS_WRAP}} table tbody>tr:nth-child(odd)>td,{{PLUS_WRAP}} tbody tr:nth-child(even){background-color:{{TBbgCR}};}',
                ],
            ],
			'scopy' => true,
        ],
        
        'TBABorder' => [
            'type' => 'boolean',
            'default' => false,	
			'scopy' => true,
        ],
        'TBborder' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,	
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'TBABorder', 'relation' => '==', 'value' => true]],
                    'selector' => '{{PLUS_WRAP}} tbody .tpgb-table-col',
                ],
            ],
			'scopy' => true,
        ],
        'TBhRTxCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} tbody .tpgb-table-row:hover td.tpgb-table-col .tpgb-table__text,{{PLUS_WRAP}} tbody .tpgb-table-row:hover td.tpgb-table-col{color:{{TBhRTxCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'TBhRBGCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} tbody .tpgb-table-row:hover{background-color:{{TBhRBGCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'TBHcellCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-table tbody td.tpgb-table-col:hover .tpgb-table__text,{{PLUS_WRAP}} .tpgb-table tbody td.tpgb-table-col:hover{color:{{TBHcellCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'TBHcellBGCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-table tbody .tpgb-table-row:hover > td.tpgb-table-col:hover{ background-color: {{TBHcellBGCr}}; }',
                ],
            ],
			'scopy' => true,
        ],
        
        'BtnTypo' => [
            'type'=> 'object',
            'default'=> (object) [
                'openTypography' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-table-col .pt_tpgb_button .button-link-wrap',
                ],
            ],
			'scopy' => true,
        ],
        'BtnPadding' => [
            'type' => 'object',
            'default' => (object) [
                'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom']],
                    'selector' => '{{PLUS_WRAP}} .pt_tpgb_button .button-link-wrap{padding:{{BtnPadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'Btnwidth' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom']],
                    'selector' =>'{{PLUS_WRAP}} .button-style-8 .button-link-wrap{min-width:{{Btnwidth}};display:inline-block;text-align:center;}',
                ],
            ],
			'scopy' => true,
        ],

        'BtnNtxcr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom']],
                    'selector' => '{{PLUS_WRAP}} .pt_tpgb_button .button-link-wrap{color:{{BtnNtxcr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BtnNcr' => [
            'type' => 'object',
            'default' => (object) [
                'openBg'=> 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom']],
                    'selector' => '{{PLUS_WRAP}} .pt_tpgb_button.button-style-8 .button-link-wrap',
                ],
            ],
			'scopy' => true,
        ],
        'BtnNBorder' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom']],
                    'selector' => '{{PLUS_WRAP}} .pt_tpgb_button.button-style-8 .button-link-wrap',
                ],
            ],
			'scopy' => true,
        ],
        'BtnNBR' => [
            'type' => 'object',
            'default' => (object) [ 
                'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom']],
                    'selector' => '{{PLUS_WRAP}} .pt_tpgb_button.button-style-8 .button-link-wrap{border-radius:{{BtnNBR}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BtnNBs' => [
            'type' => 'object',
            'default' => (object) [
                'openShadow' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom']],
                    'selector' => '{{PLUS_WRAP}} .pt_tpgb_button.button-style-8 .button-link-wrap',
                ],
            ],
			'scopy' => true,
        ],
        'BtnHtxcr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom']],
                    'selector' => '{{PLUS_WRAP}} .pt_tpgb_button .button-link-wrap:hover{color:{{BtnHtxcr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BtnHcr' => [
            'type' => 'object',
            'default' => (object) [
                'openBg'=> 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom']],
                    'selector' => '{{PLUS_WRAP}} .pt_tpgb_button.button-style-8 .button-link-wrap:hover',
                ],
            ],
			'scopy' => true,
        ],
        'BtnHBcr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom']],
                    'selector' => '{{PLUS_WRAP}} .pt_tpgb_button.button-style-8 .button-link-wrap:hover{border-color:{{BtnHBcr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BtnHBRs' => [
            'type' => 'object',
            'default' => (object) [ 
                'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
                "unit" => 'px'
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom']],
                    'selector' => '{{PLUS_WRAP}} .pt_tpgb_button.button-style-8 .button-link-wrap:hover{border-radius:{{BtnHBRs}};}',
                ],
            ],
			'scopy' => true,
        ],

        'IconColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-align-icon--left .tableicon,{{PLUS_WRAP}} .tpgb-align-icon--right .tableicon{color:{{IconColor}};}',
                ],
            ],
			'scopy' => true,
        ],
        'IconSize' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],        
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-align-icon--left .tableicon,{{PLUS_WRAP}} .tpgb-align-icon--right .tableicon{font-size:{{IconSize}};}',
                ],
            ],
			'scopy' => true,
        ],
        'IconPosition' => [
            'type' => 'string',
            'default' => 'left',
			'scopy' => true,
        ],       
        'IconSpacing' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],  
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-align-icon--left{margin-right:{{IconSpacing}};}{{PLUS_WRAP}} .tpgb-align-icon--right{margin-left:{{IconSpacing}};}',
                ],
            ],
			'scopy' => true,
        ],
        'ImgSize' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],  
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom']],
                    'selector' =>'{{PLUS_WRAP}} .tpgb-col-img--left,{{PLUS_WRAP}} .tpgb-col-img--right{width:{{ImgSize}};}',
                ],
            ],
			'scopy' => true,
        ],
        'ImgPosition' => [
            'type' => 'string',
            'default' => 'left',	
			'scopy' => true,
        ],
        'ImgSpacing' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],  
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-col-img--left{margin-right:{{ImgSpacing}};}{{PLUS_WRAP}} .tpgb-col-img--right{margin-left:{{ImgSpacing}};}',
                ],
            ],
			'scopy' => true,
        ],
        'ImgBRs' => [
            'type' => 'object',
            'default' => (object) [ 
                'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
                "unit" => 'px',
            ],        
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-col-img--left,{{PLUS_WRAP}} .tpgb-col-img--right{border-radius:{{ImgBRs}}}',
                ],
            ],
			'scopy' => true,
        ],


        'ToMargin' => [
            'type' => 'object',
            'default' => (object) [
                'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
                "unit" => 'px',
            ],  
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-table-wrapper{margin:{{ToMargin}};}',
                ],
            ],
			'scopy' => true,
        ],
        'ToPadding' => [
            'type' => 'object',
            'default' => (object) [
                'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
                "unit" => 'px',
            ],        
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-table-wrapper{padding:{{ToPadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'Tobg' => [
            'type' => 'object',
            'default' => (object) [
                'openBg'=> 0,
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} table tbody>tr:nth-child(odd)>td,{{PLUS_WRAP}} table tbody>tr:nth-child(even)>td',
                ],
            ],
			'scopy' => true,
        ],
        'Toborder' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 1,
                'type' => 'solid',
                    'color' => '#000',
                'width' => (object) [
                    'md' => (object)[
                        'top' => 1,
                        'left' => 1,
                        'bottom' => 1,
                        'right' => 1,
                    ],
                    'sm' => (object)[ ],
                    'xs' => (object)[ ],
                    "unit" => "px",
                ],
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Toshowtitle', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-table-wrapper',
                ],
            ],
			'scopy' => true,
        ],
        'ToBrs' => [
            'type' => 'object',
            'default' => (object) [ 
                'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Toshowtitle', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-table-wrapper,{{PLUS_WRAP}} table{border-radius:{{ToBrs}};}',
                ],
            ],
			'scopy' => true,
        ],
        'ToBoxS' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-table-wrapper .tpgb-table',
                ],
            ],
			'scopy' => true,
        ],

    ];  

    $attributesOptions = array_merge($attributesOptions	, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);

    register_block_type( 'tpgb/tp-data-table', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_datatable_callback'
    ));
}
add_action( 'init', 'tpgb_tp_datatable_render' );