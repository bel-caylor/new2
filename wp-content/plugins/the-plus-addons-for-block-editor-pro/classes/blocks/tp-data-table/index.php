<?php
/* Block : Data Table
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_datatable_callback( $attributes, $content) {
	$DataTable = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
    $ContentCSV = (!empty($attributes['CsvURL']['url'])) ? $attributes['CsvURL']['url'] : TPGB_ASSETS_URL.'assets/images/table.csv';
    $ContentTable = (!empty($attributes['ContentTable'])) ? $attributes['ContentTable'] : '';
    $TableHeader = (!empty($attributes['TableHeader'])) ? $attributes['TableHeader'] : [];
    $Tablebody = (!empty($attributes['Tablebody'])) ? $attributes['Tablebody'] : [];
    $TbSearch = (!empty($attributes['TbSearch'])) ? $attributes['TbSearch'] : false;
    $Searchfield = (!empty($attributes['SearchLabel'])) ? $attributes['SearchLabel'] : 'Search';
    $TbSort = (!empty($attributes['TbSort'])) ? $attributes['TbSort'] : false;
    $TbFilter = (!empty($attributes['TbFilter'])) ? $attributes['TbFilter'] : false;
    $IconPosition = (!empty($attributes['IconPosition'])) ? $attributes['IconPosition'] : 'left';
    $ImgPosition = (!empty($attributes['ImgPosition'])) ? $attributes['ImgPosition'] : 'left';
    $MResponsive = (!empty($attributes['MResponsive'])) ? $attributes['MResponsive'] : '';
    $GsheetURL = (!empty($attributes['GsheetURL'])) ? $attributes['GsheetURL'] : '';
    $GAKey = (!empty($attributes['GAKey'])) ? $attributes['GAKey'] : '';
    $GSID = (!empty($attributes['GSID'])) ? $attributes['GSID'] : '';
    $Gtr = (!empty($attributes['Gtr'])) ? $attributes['Gtr'] : '';
    $classone = ($MResponsive == 'one-by-one') ? 'tpgb-table-mob-res' : '';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

    $sorting = ($TbSort == true) ? 'yes' : 'no';
    $Search = ($TbSearch == true) ? 'yes' : 'no';
    $Filter = ($TbFilter == true) ? 'yes' : 'no';

    $DTHeader = '';
    $DTBody = '';

    $DataTable .= '<div class="tpgb-block-'.esc_attr($block_id).' '.esc_attr( $blockClass ).'">';
        $DataTable .= '<div class="tpgb-table-wrapper">';
            $DataTable .= '<table class="tpgb-table '.esc_attr($classone).'" id="tpgb-table-id-'.esc_attr($block_id).'" data-id="'.esc_attr($block_id).'" data-sort-table="'.esc_attr($sorting).'" data-show-entry="'.esc_attr($Filter).'" data-searchable="'.esc_attr($Search).'" data-searchable-label="'.$Searchfield.'" role="grid">';

                if( $ContentTable == 'csv_file' ){
                    if(!empty($ContentCSV)){
						$ContentCSV = (isset($attributes['CsvURL']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($attributes['CsvURL']) : (!empty($attributes['CsvURL']['url']) ? $attributes['CsvURL']['url'] : '');
						$ext = pathinfo( $ContentCSV, PATHINFO_EXTENSION);
						if(!empty($ContentCSV) && !empty($ext)){
							$ext = pathinfo( $ContentCSV, PATHINFO_EXTENSION);
							$DataTable .= tpgb_tabletocsv($ContentCSV,$sorting);
						}else{
							$DataTable .= '<h3 class="theplus-posts-not-found">'.esc_html__("Opps!! You didn\'t enter any table data or CSV file",'tpgbp').'</h3>';
						}
                    }else{
                        $DataTable .= '<h3 class="theplus-posts-not-found">'.esc_html__("Opps!! You didn\'t enter any table data or CSV file",'tpgbp').'</h3>';
                    }
                }else if( $ContentTable =='custom' ){
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
                                $ThImgurl = wp_get_attachment_image($ThImgID,$Thimagesize,false, ['class' => 'tpgb-col-img--'.esc_attr($IconPosition).esc_attr($checkText) ]);
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
                                
                                    $headerArray[$Mob_thc] = isset($item['thtext']) ? wp_kses_post($item['thtext']) : '';
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
                                $Btntx = (!empty($item['Trbtntext']) ? $item['Trbtntext'] : 'Click Here' );
                                $Btnlink = (!empty($item['TrbtnLink']) && !empty($item['TrbtnLink']['url'])) ? 'href="'.esc_url($item['TrbtnLink']['url']).'"' : '';
								$target = ( !empty($item['TrbtnLink']['target'])) ? 'target="_blank"' : '';
				                $nofollow = (!empty($item['TrbtnLink']['nofollow'])) ? 'rel="nofollow"' : '';
								
                                $TRIcon = '';
                                $TRImg = '';
                                $CA = '';
								
								$checkText = (!empty($item['trtext']) ? '' : ' less-icon-space');

                                if((!empty($item['ShowTitle'])) && $item['ShowTitle'] == True && !empty($item['CustomAttributes']) ){
                                    $CA = $item['CustomAttributes'];
                                }
                                if( !empty($item['trDricon']) && $item['trDricon'] == 'icon' && !empty($item['TrfaIcon']) ){
                                    $TRIcon = '<span class="tpgb-align-icon--'.esc_attr($IconPosition).esc_attr($checkText).'"><i class="'.esc_attr($item['TrfaIcon']).' tableicon"></i></span>';
                                }else if(!empty($item['trDricon']) && $item['trDricon'] == 'image' && !empty($item['trDrimage'])){
                                    $TRimagesize = (!empty($item['trimagesize'])) ? $item['trimagesize'] : 'thumbnail';
                                    $TRDrimgid = (!empty($item['trDrimage']['id'])) ? $item['trDrimage']['id'] : '';
                                    $TRImgurl = wp_get_attachment_image($TRDrimgid,$TRimagesize, false, ['class' => 'tpgb-col-img--'.esc_attr($IconPosition).esc_attr($checkText) ]);
                                    $TRImg = $TRImgurl;
                                }
                                $DTBody .= '<'.Tpgbp_Pro_Blocks_Helper::validate_html_tag($Tag).' class="tpgb-table-col tp-repeater-item-'.esc_attr($item['_key']).'"  colspan="'.esc_attr($TrColumnSpan).'" rowspan="'.esc_attr($TrRowSpan).'">';
                                    if($MResponsive == 'one-by-one'){
                                        $DTBody .= '<div class="tpgb-table-mob-wrap">';
                                            $DTBody .= '<span class="tpgb-table-mob-row">';
                                            $DTBody .= ( $IconPosition == 'left' ) ? $headerArrayicon[$Mob_trc] :'';
                                            $DTBody .= ( $ImgPosition == 'left' ) ? $headerArrayimage[$Mob_trc] :'';
                                                $DTBody .= '<span class="mob-heading-text">'.$headerArray[$Mob_trc].'</span>';
                                            $DTBody .= ( $IconPosition == 'right' ) ? $headerArrayicon[$Mob_trc] :'';
                                            $DTBody .= ( $ImgPosition == 'right' ) ? $headerArrayimage[$Mob_trc] :'';
                                            $DTBody .= '</span>';                                        
                                        $DTBody .= '</span>';
                                    }

                                    if( !empty($item['TrLink']) && !empty($item['TrLink']['url']) ){
										$target1 = ( !empty ($item['TrLink']['target'])) ? 'target="_blank"' : '';
                                        $nofollow1= ( !empty($item['TrLink']['nofollow']) ) ? 'rel="nofollow"' : '';
										$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($item['TrLink']);
										
                                        $DTBody.='<a href="'.esc_url($item['TrLink']['url']).'" class="tb-col-link" '.$target1.' '.$nofollow1.' '.$link_attr.'>';
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
										$btn_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($item['TrbtnLink']);
                                        $DTBody .='<div class="pt_tpgb_button tp-repeater-item-'.esc_attr($item['_key']).' button-style-8">';
                                            $DTBody .='<a '.(!empty($Btnlink) ? $Btnlink : 'href="#"').' class="button-link-wrap" '.$CA.' '.$target.' '.$nofollow.' '.$btn_attr.'>'.wp_kses_post($Btntx).'</a>';
                                        $DTBody .='</div>';
                                    }
                                    $DTBody .= ($MResponsive == 'one-by-one') ? '</div>' : ''; 
                                $DTBody .= '</'.Tpgbp_Pro_Blocks_Helper::validate_html_tag($Tag).'>';
                                
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
                }else if( $ContentTable =='gSheetUrl' ){
                    if($GAKey == '' || $GSID == '' || $Gtr == ''){
                        return '<h3 class="error-handal">'.esc_html__("Google Sheet Not Found",'tpgbp').'</h3>';
                    }
					if(class_exists('Tpgbp_Pro_Blocks_Helper')){
						if(!empty($GAKey)){
							$GAKey = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($GAKey);
						}
						
						if(!empty($GSID)){
							$GSID = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($GSID);
						}
						if(!empty($Gtr)){
							$Gtr = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($Gtr);
						}
					}
                    $GURL = wp_remote_get( "https://sheets.googleapis.com/v4/spreadsheets/$GSID/values/$Gtr?key=$GAKey" );

                    if ( !is_wp_error( $GURL ) ) {
                        $GURL = json_decode( wp_remote_retrieve_body( $GURL ), true );
                        if ( isset( $GURL['values'] ) ) {
                            $results = $GURL['values'];
                        }
                        $Gsheet = '';
                        if ( !empty( $results ) ) {
                                $Gsheet .= '<thead><tr class="tpgb-table-row">';
                                    foreach ( $results[0] as $key => $th ) {
                                        $Gsheet .= '<th class="tpgb-table-col">';
                                            $Gsheet .='<span class="tpgb-table__text">';
                                                $Gsheet .= '<span class="tpgb-table__text-inner">'. $th . '</span>';
                                            $Gsheet .= '</span>';                                            
                                            $Gsheet .= '<span class="tpgb-sort-icon">';
                                                if(!empty($TbSort)){
                                                    $Gsheet .= '<i class="up-icon fas fa-sort-up"></i>';
                                                    $Gsheet .= '<i class="down-icon fas fa-sort-down"></i>';
                                                }
                                            $Gsheet .= '</span>';
                                        $Gsheet .= '</th>';
                                    }
                                $Gsheet .= '</tr></thead>';
                                array_shift( $results );
                                $Gsheet .= '<tbody>';
                                    foreach ( $results as $tr ) {
                                        if(!sizeof($tr) == 0){
                                        $Gsheet .= '<tr class="tpgb-table-row" >';
                                            foreach ( $tr as $td ) {
                                                $Gsheet .= '<td class="tpgb-table-col">';
                                                $Gsheet .= '<span class="tpgb-table__text"><span class="tpgb-table__text-inner">'. $td .'</span></span>';
                                                $Gsheet .= '</td>';
                                            }
                                        $Gsheet .= '</tr>';
                                        }
                                    }
                                $Gsheet .= '</tbody>';
                        }
                        $DataTable .= $Gsheet;
                    }
                }

                $DataTable .= '</table>';
        $DataTable .= '</div>';
    $DataTable .= '</div>';
	
	$DataTable = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $DataTable);
	
    return $DataTable;
}   

function tpgb_tabletocsv( $file, $sorting ) {
	
   $column = $char_skip = '';
   $csv_rows = file( $file );
   if ( is_array( $csv_rows ) ) {
       $count = count( $csv_rows );
       for ( $i = 0; $i < $count; $i++ ) {
           $rows = $csv_rows[$i];
           $rows = trim( $rows );
           $first_character = true;
           $number_column = 0;
           $length = strlen( $rows );
           for ( $j = 0; $j < $length; $j++ ) {
               if ( $char_skip != true ) {
                   $display = true;
                   if ( $first_character == true ) {
                       if ( $rows[$j] == '"' ) {
                           $combine_char = '";';
                           $display = false;
                       }
                       else
                           $combine_char = ';';
                       $first_character = false;
                   }
                   if ( $rows[$j] == '"' ) {
                       $next_char = $rows[$j + 1];
                       if ( $next_char == '"' ) $char_skip = true;
                       elseif ( $next_char == ';' ) {
                           if ( $combine_char == '";' ) {
                               $first_character = true;
                               $display = false;
                               $char_skip = true;
                           }
                       }
                   }
                   if ( $display == true ) {
                       if ( $rows[$j] == ';' ) {
                           if ( $combine_char == ';' ) {
                               $first_character = true;
                               $display = false;
                           }
                       }
                   }
                   if ( $display == true ){ $column .= $rows[$j]; }
                   if ( $j == ( $length - 1 ) ){ $first_character = true; }
                   if ( $first_character == true ) {
                       $values[$i][$number_column] = $column;
                       $column = '';
                       $number_column++;
                   }
               }
               else
                   $char_skip = false;
           }
       }
   }
   
   $return = '<thead><tr class="tpgb-table-row">';
   
   foreach ( $values[0] as $value ){
       
        $return .= '<th class="sort-this tpgb-table-col">';
        $return .= '<span class="tpgb-table__text">';
            $return .= '<span class="tpgb-table__text-inner">';
                $return .= $value;
            $return .= '</span>';
        $return .= '</span>';
       if ( $sorting === 'yes') {
            $return .= '<span class="tpgb-sort-icon">';
                $return .= '<i class="up-icon fas fa-sort-up"></i>';
                $return .= '<i class="down-icon fas fa-sort-down"></i>';
            $return .= '</span>';
       }
       $return .= '</th>';
   }
   $return .= '</tr></thead><tbody>';
   array_shift( $values );
   foreach ( $values as $rows ) {
       $return .= '<tr class="tpgb-table-row">';
       foreach ( $rows as $col ) {

          $return .= '<td class="tpgb-table-col">' . htmlentities($col). '</td>';
       }
       $return .= '</tr>';
   }
   $return .= '</tbody>';
   
    return $return;
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
        'CsvURL' => [
            'type'=> 'object',
            'default'=> [
                'url' => TPGB_ASSETS_URL.'assets/images/table.csv',
                'target' => '',
                'nofollow' => ''
            ],
        ],

        'GAKey' => [
            'type'=> 'string',
            'default'=> '',
        ],
        'GSID' => [
            'type'=> 'string',
            'default'=> '',
        ],
        'Gtr' => [
            'type'=> 'string',
            'default'=> '',
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
        'SearchLabel' => [
            'type'=> 'string',
            'default'=> 'Search',
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
            'default' => '#000',
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

        'MobHALig' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'MResponsive', 'relation' => '==', 'value' => 'one-by-one']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-table.tpgb-table-mob-res .tpgb-table-mob-wrap span.tpgb-table-mob-row{text-align:{{MobHALig}};width: 100%;}',
                ],
            ],
			'scopy' => true,
        ],
        'MobTypo' => [
            'type'=> 'object',
            'default'=> (object) [
                'openTypography' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'MResponsive', 'relation' => '==', 'value' => 'one-by-one']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-table-mob-res span.tpgb-table-mob-row',
                ],
            ],
			'scopy' => true,
        ],
        'MobPadding' => [
            'type' => 'object',
            'default' => (object) [ 
                'md' => '',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'MResponsive', 'relation' => '==', 'value' => 'one-by-one']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-table.tpgb-table-mob-res .tpgb-table-mob-wrap span.tpgb-table-mob-row{padding:{{MobPadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'MobCellWid' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'MResponsive', 'relation' => '==', 'value' => 'one-by-one']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-table-mob-res .tpgb-table-mob-wrap span.tpgb-table-mob-row{-webkit-flex-basis:{{MobCellWid}};-ms-flex-preferred-size:{{MobCellWid}};flex-basis:{{MobCellWid}};}',
                ],
            ],
			'scopy' => true,
        ],
        'MobNCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
               (object) [
                    'condition' => [(object) ['key' => 'MResponsive', 'relation' => '==', 'value' => 'one-by-one']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-table-mob-res span.tpgb-table-mob-row{color:{{MobNCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'MobNBgcr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'MResponsive', 'relation' => '==', 'value' => 'one-by-one']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-table-mob-res span.tpgb-table-mob-row{background-color:{{MobNBgcr}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'MobHCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
               (object) [
                    'condition' => [(object) ['key' => 'MResponsive', 'relation' => '==', 'value' => 'one-by-one']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-table.tpgb-table-mob-res .tpgb-table-mob-wrap span.tpgb-table-mob-row:hover{color:{{MobHCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'MobHBgcr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'MResponsive', 'relation' => '==', 'value' => 'one-by-one']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-table.tpgb-table-mob-res .tpgb-table-mob-wrap span.tpgb-table-mob-row:hover{background-color:{{MobHBgcr}}; }',
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
            'default' => '#000',
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
        'TBrCRone' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'TBStripEff', 'relation' => '==', 'value' => true]],
                    'selector' => '{{PLUS_WRAP}} table tbody>tr:nth-child(odd)>td{ background-color: {{TBrCRone}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'TBrCRtwo' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'TBStripEff', 'relation' => '==', 'value' => true]],
                    'selector' => '{{PLUS_WRAP}} tbody tr:nth-child(even){background-color:{{TBrCRtwo}};}',
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
        'BtndaSpace' => [
            'type' => 'object',
            'default' => [ 
                'md' => '15',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'ContentTable', 'relation' => '==', 'value' => 'custom']],
                    'selector' =>'{{PLUS_WRAP}} .button-style-8 { margin-top : {{BtndaSpace}} }',
                ],
            ],
			'scopy' => true,
        ],

        'BtnNtxcr' => [
            'type' => 'string',
            'default' => '#313131',
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

        'SliconCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'TbSort', 'relation' => '==', 'value' => true]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-table-row .tpgb-sort-icon{color:{{SliconCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'Slcr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-advance-heading label{color:{{Slcr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'SivCR' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-advance-heading select,{{PLUS_WRAP}} .tpgb-advance-heading input{ color: {{SivCR}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'SiBGcr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-advance-heading select,{{PLUS_WRAP}} .tpgb-advance-heading input{background-color:{{SiBGcr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'STypography' => [
            'type' => 'string',
            'default' =>  (object) [
                'openTypography' => 0,
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-advance-heading label,{{PLUS_WRAP}} .tpgb-advance-heading select,{{PLUS_WRAP}} .tpgb-advance-heading input',
                ],
            ],
			'scopy' => true,
        ],
        'SIpadding' => [
            'type' => 'object',
            'default' => (object) [ 
                'md' => '',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-advance-heading .tpgb-tbl-search-wrapper input,{{PLUS_WRAP}} .tpgb-advance-heading .tpgb-tbl-entry-wrapper select {padding:{{SIpadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'SBorder' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,	
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-advance-heading select,{{PLUS_WRAP}} .tpgb-advance-heading input',
                ],
            ],
			'scopy' => true,
        ],
        'SiBrs' => [
            'type' => 'object',
            'default' => (object) [ 
                'md' => '',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-advance-heading select,{{PLUS_WRAP}} .tpgb-advance-heading input{border-radius:{{SiBrs}};}',
                ],
            ],
			'scopy' => true,
        ],
        'SBwidth' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],        
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-advance-heading .tpgb-tbl-search-wrapper input{ width: {{SBwidth}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'SEwidth' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],  
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-advance-heading .tpgb-tbl-entry-wrapper select{ width: {{SEwidth}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'SBspace' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],  
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-advance-heading{ margin-bottom: {{SBspace}}; }',
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
		'Toshowtitle' => [
            'type' => 'boolean',
            'default' => false,
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