<?php
/**
 * Block : TP Social Feed
 * @since 1.3.0.1
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_social_feed() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$plusButton_options = Tpgb_Blocks_Global_Options::load_plusButton_options();

	$uidId = uniqid();
	$uidId ='F'.substr($uidId,-4);
	$attributesOptions = [
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'feed_id' => [
            'type' => 'string',
            'default' => '',
        ],
		'layout' => [
			'type'=> 'string',
			'default'=> 'grid',
		],
		'style' => [
			'type'=> 'string',
			'default'=> 'style-1',
		],		

		'AllReapeter' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'selectFeed' => [
						'type' => 'string',
						'default' =>'Facebook',	
					],
					'FbTokenGen' => [
						'type' => 'string',
						'default' => 'manually',	
					],
					'SFFbAppId' => [
						'type' => 'string',
						'default' =>'',	
					],
					'SFFbAppSecretId' => [
						'type' => 'string',
						'default' =>'',	
					],
					'RAToken' => [
						'type' => 'string',
						'default' =>'',	
					],
					'ProfileType' => [
						'type' => 'string',
						'default' =>'post',	
					],
					'Pageid' => [
						'type' => 'string',
						'default' =>'',	
					],
					'content' => [
						'type' => 'string',
						'default' => '[]',
					],
					'fbAlbum' => [
						'type' => 'boolean',
						'default' => false,	
					],
					'AlbumMaxR' => [
						'type' => 'string',
						'default' => 8,	
					],

					'MaxR' => [
						'type' => 'string',
						'default' => 6,	
					],
				],
			],
			'default' => [ 
				['_key'=> $uidId, 'selectFeed' => 'Facebook', 'FbTokenGen' => 'manually', 'ProfileType' => 'post', 'MaxR' => 6 , 'content' => '[]'],
			],
		],

		'columns' => [
			'type' => 'object',
			'default' => [ 'md' => 4,'sm' => 4,'xs' => 6 ],
		],
		'columnSpace' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .post-loop-inner .grid-item{padding:{{columnSpace}};}',
				],
			],
			'scopy' => true,
		],

		'TotalPost' => [
			'type'=> 'string',
			'default'=> 1000,
		],
		'BackendOff' => [
			'type' => 'boolean',
			'default' => true,	
		],
		'DescripBTM' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'MediaFilter' => [
			'type' => 'string',
			'default' => 'default',	
		],
		'ShowTitle' => [
			'type' => 'boolean',
			'default' => false,
		],
		'ShowFeedId' => [
			'type' => 'boolean',
			'default' => false,
		],
		'FeedId' => [
			'type' => 'string',
			'default' => "",	
		],
		'showFooterIn' => [
			'type' => 'boolean',
			'default' => false,
		],

		'TimeFrq' => [
			'type' => 'string',
			'default' => '3600',
		],
		'TextLimit' => [
			'type' => 'boolean',
			'default' => true,
		],
		'TextType' => [
			'type' => 'string',
			'default' => 'char',	
		],
		'TextMore' => [
			'type' => 'string',
			'default' => 'Show More',	
		],
		'TextCount' => [
			'type' => 'string',
			'default' => 100,	
		],
		'TextDots' => [
			'type' => 'boolean',
			'default' => true,
		],
		'OnPopup' => [
			'type'=> 'string',
			'default'=> 'Donothing',
		],
		'CURLOPT_SSL_VERIFYPEER' => [
			'type' => 'boolean',
			'default' => true,	
		],
		
		'perf_manage' => [
			'type' => 'boolean',
			'default' => false,	
		],
		
		'CategoryWF' => [
			'type' => 'boolean',
			'default' => False,	
		],

		// load more
		'postLodop' => [
			'type' => 'string',
			'default' => 'none',
		],
		
		'FbMsgTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook .tpgb-sf-feed .tpgb-title',
				],
			],
			'scopy' => true,
		],
		'FbDesTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook .tpgb-sf-feed .tpgb-message',
				],
			],
			'scopy' => true,
		],
		'FbNameTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook .tpgb-sf-feed .tpgb-sf-username a',
				],
			],
			'scopy' => true,
		],
		'FbTimeTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook .tpgb-sf-feed .tpgb-sf-time a',
				],
			],
			'scopy' => true,
		],
		'fbIconSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .feed-Facebook .social-logo-fb {font-size:{{fbIconSize}};}',
				],
			],
			'scopy' => true,
		],
		'fbIconColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .feed-Facebook .social-logo-fb {color:{{fbIconColor}};}',
				],
			],
			'scopy' => true,
		],
		
		'FbNBgCr' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'FbNBcr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'FbNBRcr' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],	
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook .tpgb-sf-feed{border-radius:{{FbNBRcr}};}',
				],
			],
			'scopy' => true,
		],
		'FbNBs' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'FbHBgCr' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook:hover .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'FbHBcr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook:hover .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'FbHBRcr' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook:hover .tpgb-sf-feed{border-radius:{{FbHBRcr}};}',
				],
			],
			'scopy' => true,
		],
		'FbHBs' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook:hover .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],

		'FbPRs' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook .tpgb-sf-feed .tpgb-sf-logo{border-radius:{{FbPRs}};}',
				],
			],
			'scopy' => true,
		],

		'FbNNameC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook .tpgb-sf-feed .tpgb-sf-username a{color:{{FbNNameC}};}',
				],
			],
			'scopy' => true,
		],
		'FbNTimeC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook .tpgb-sf-feed .tpgb-sf-time a{color:{{FbNTimeC}};}',
				],
			],
			'scopy' => true,
		],
		'FbNIconCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook .tpgb-sf-feed .tpgb-sf-footer{color:{{FbNIconCr}};}',
				],
			],
			'scopy' => true,
		],
		'FbNTitleC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook .tpgb-sf-feed .tpgb-title{color:{{FbNTitleC}};}',
				],
			],
			'scopy' => true,
		],
		'FbNDesC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook .tpgb-sf-feed .tpgb-message{color:{{FbNDesC}};}',
				],
			],
			'scopy' => true,
		],
		'FbHNameC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook:hover .tpgb-sf-feed .tpgb-sf-username a{color:{{FbHNameC}};}',
				],
			],
			'scopy' => true,
		],
		'FbHTimeC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook:hover .tpgb-sf-feed .tpgb-sf-time a{color:{{FbHTimeC}};}',
				],
			],
			'scopy' => true,
		],
		'FbHIconCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook:hover .tpgb-sf-feed .tpgb-sf-footer{color:{{FbHIconCr}};}',
				],
			],
			'scopy' => true,
		],
		'FbHTitleC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook:hover .tpgb-sf-feed .tpgb-title{color:{{FbHTitleC}};}',
				],
			],
			'scopy' => true,
		],
		'FbHDesC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Facebook:hover .tpgb-sf-feed .tpgb-message{color:{{FbHDesC}};}',
				],
			],
			'scopy' => true,
		],
		
		'AllMsgTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed .tpgb-title',
				],
			],
			'scopy' => true,
		],
		'AllDesTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed .tpgb-message',
				],
			],
			'scopy' => true,
		],
		'AllNameTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed .tpgb-sf-username a',
				],
			],
			'scopy' => true,
		],
		'AllTimeTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed .tpgb-sf-time a',
				],
			],
			'scopy' => true,
		],
		'allIconSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .social-logo-fb, {{PLUS_WRAP}} .social-logo-ig, {{PLUS_WRAP}} .social-logo-vm, {{PLUS_WRAP}} .social-logo-yt, {{PLUS_WRAP}} .social-logo-tw{font-size:{{allIconSize}};}',
				],
			],
			'scopy' => true,
		],
		'allIconColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .social-logo-fb, {{PLUS_WRAP}} .social-logo-ig, {{PLUS_WRAP}} .social-logo-vm, {{PLUS_WRAP}} .social-logo-yt, {{PLUS_WRAP}} .social-logo-tw{color:{{allIconColor}};}',
				],
			],
			'scopy' => true,
		],
		
		'AllNBgCr' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'AllNBcr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'AllNBrs' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed{border-radius:{{AllNBrs}};}',
				],
			],
			'scopy' => true,
		],
		'AllNBs' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'AllHBgCr' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed:hover',
				],
			],
			'scopy' => true,
		],
		'AllHBcr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed:hover',
				],
			],
			'scopy' => true,
		],
		'AllHBrs' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed:hover{border-radius:{{AllHBrs}};}',
				],
			],
			'scopy' => true,
		],
		'AllHboxpadd' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed:hover{padding:{{AllHboxpadd}};}',
				],
			],
			'scopy' => true,
		],
		'AllHBs' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed:hover',
				],
			],
			'scopy' => true,
		],

		'AllPRs' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed img.tpgb-sf-logo{border-radius:{{AllPRs}};}',
				],
			],
			'scopy' => true,
		],
		'AllBoxSh' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed img.tpgb-sf-logo',
				],
			],
			'scopy' => true,
		],

		'AllNNameC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed .tpgb-sf-username a{color:{{AllNNameC}};}',
				],
			],
			'scopy' => true,
		],
		'AllNTimeC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed .tpgb-sf-time a{color:{{AllNTimeC}};}',
				],
			],
			'scopy' => true,
		],
		'AllNIconCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed .tpgb-sf-footer,{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed .tpgb-sf-footer a{color:{{AllNIconCr}};}',
				],
			],
			'scopy' => true,
		],
		'AllNTitleC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed .tpgb-title{color:{{AllNTitleC}};}',
				],
			],
			'scopy' => true,
		],
		'AllNDesC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed .tpgb-message{color:{{AllNDesC}};}',
				],
			],
			'scopy' => true,
		],
		'AllHsmC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .grid-item a.readbtn{color:{{AllHsmC}};}',
				],
			],
			'scopy' => true,
		],

		'AllNurlC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-feedurl{color:{{AllNurlC}};}',
				],
			],
			'scopy' => true,
		],
		'AllNMtC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-mantion{color:{{AllNMtC}};}',
				],
			],
			'scopy' => true,
		],
		'AllNHtC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-hashtag{color:{{AllNHtC}};}',
				],
			],
			'scopy' => true,
		],
		'AllHNameC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed:hover .tpgb-sf-username a{color:{{AllHNameC}};}',
				],
			],
			'scopy' => true,
		],
		'AllHTimeC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed:hover .tpgb-sf-time a{color:{{AllHTimeC}};}',
				],
			],
		],
		'AllHIconCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed:hover .tpgb-sf-footer,{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed:hover .tpgb-sf-footer a{color:{{AllHIconCr}};}',
				],
			],
			'scopy' => true,
		],
		'AllHurlC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-message:hover .tpgb-feedurl{color:{{AllHurlC}};}',
				],
			],
			'scopy' => true,
		],
		'AllHMtC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-message:hover .tpgb-mantion{color:{{AllHMtC}};}',
				],
			],
			'scopy' => true,
		],
		'AllHHtC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-message:hover .tpgb-hashtag{color:{{AllHHtC}};}',
				],
			],
			'scopy' => true,
		],

		'AllHTitleC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed:hover .tpgb-title{color:{{AllHTitleC}};}',
				],
			],
			'scopy' => true,
		],
		'AllHDesC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed:hover .tpgb-message{color:{{AllHDesC}};}',
				],
			],
			'scopy' => true,
		],
		'AllImg' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => "",
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed img.tpgb-post-thumb{padding:{{AllImg}};}',
				],
			],
			'scopy' => true,
		],
		'AllTitle' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed .tpgb-title{padding:{{AllTitle}};}',
				],
			],
			'scopy' => true,
		],
		'AllTitleBR' => [
			'type' => 'object',
			'default' => (object) ['openBorder' => 0],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed .tpgb-title',
				],
			],
			'scopy' => true,
		],
		'Alldescription' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed .tpgb-message{padding:{{Alldescription}};}',
				],
			],
			'scopy' => true,
		],
		'AllDesBR' => [
			'type' => 'object',
			'default' => (object) ['openBorder' => 0],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed .tpgb-message',
				],
			],
			'scopy' => true,
		],
		'AllProfile' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed .tpgb-sf-header{padding:{{AllProfile}};}',
				],
			],
			'scopy' => true,
		],
		'AllProfBR' => [
			'type' => 'object',
			'default' => (object) ['openBorder' => 0],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed .tpgb-sf-header',
				],
			],
			'scopy' => true,
		],
		'AllFooter' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed .tpgb-sf-footer{padding:{{AllFooter}};}',
				],
			],
			'scopy' => true,
		],
		'AllbtmBR' => [
			'type' => 'object',
			'default' => (object) ['openBorder' => 0],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed .tpgb-sf-footer',
				],
			],
			'scopy' => true,
		],
		'Allboxpadd' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-sf-feed{padding:{{Allboxpadd}};}',
				],
			],
			'scopy' => true,
		],
		
		'SmTxtTypo' => [
			'type'=> 'object',
			'default'=> (object) ['openTypography' => 0],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-message a.readbtn',
				],
			],
			'scopy' => true,
		],

		'SmTxtNCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-message a.readbtn{color:{{SmTxtNCr}};}',
				],
			],
			'scopy' => true,
		],
		'SlTxtNCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-message.show-less a.readbtn{color:{{SlTxtNCr}};}',
				],
			],
			'scopy' => true,
		],
		'DotTxtNCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-message .sf-dots{color:{{DotTxtNCr}};}',
				],
			],
			'scopy' => true,
		],
		'SmTxtHCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-message a.readbtn:hover{color:{{SmTxtHCr}};}',
				],
			],
			'scopy' => true,
		],
		'SlTxtHCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-message.show-less a.readbtn:hover{color:{{SlTxtHCr}};}',
				],
			],
			'scopy' => true,
		],
		'DotTxtHCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-message:hover .sf-dots{color:{{DotTxtHCr}};}',
				],
			],
			'scopy' => true,
		],
		
		'ScrollOn' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'ScrollHgt' => [
			'type' => 'string',
			'default' => '',
		],
		'ScrollBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'ScrollOn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-normal-scroll::-webkit-scrollbar',
				],
			],
			'scopy' => true,
		],
		'ScrollWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'ScrollOn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-normal-scroll::-webkit-scrollbar{width:{{ScrollWidth}};}',
				],
			],
			'scopy' => true,
		],
		'ThumbBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'ScrollOn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-normal-scroll::-webkit-scrollbar-thumb',
				],
			],
			'scopy' => true,
		],
		'ThumbBrs' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'ScrollOn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-normal-scroll::-webkit-scrollbar-thumb{border-radius:{{ThumbBrs}};}',
				],
			],
			'scopy' => true,
		],
		'ThumbBsw' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'ScrollOn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-normal-scroll::-webkit-scrollbar-thumb',
				],
			],
			'scopy' => true,
		],
		'TrackBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'ScrollOn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-normal-scroll::-webkit-scrollbar-track',
				],
			],
			'scopy' => true,
		],
		'TrackBRs' => [
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
					'condition' => [(object) ['key' => 'ScrollOn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-normal-scroll::-webkit-scrollbar-track{border-radius:{{TrackBRs}};}',
				],
			],
			'scopy' => true,
		],
		'TrackBsw' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'ScrollOn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .tpgb-normal-scroll::-webkit-scrollbar-track',
				],
			],
			'scopy' => true,
		],
			
		'FcySclOn' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'FcySclHgt' => [
			'type' => 'string',
			'default' => '',
		],
		'FcySclBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FcySclOn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fancy-scroll::-webkit-scrollbar',
				],
			],
			'scopy' => true,
		],
		'FcySclWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FcySclOn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fancy-scroll::-webkit-scrollbar{width:{{FcySclWidth}};}',
				],
			],
			'scopy' => true,
		],
		'FcyThumbBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FcySclOn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fancy-scroll::-webkit-scrollbar-thumb',
				],
			],
			'scopy' => true,
		],
		'FcyThumbBrs' => [
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
					'condition' => [(object) ['key' => 'FcySclOn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fancy-scroll::-webkit-scrollbar-thumb{border-radius:{{FcyThumbBrs}};}',
				],
			],
			'scopy' => true,
		],
		'FcyThumbBsw' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FcySclOn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fancy-scroll::-webkit-scrollbar-thumb',
				],
			],
			'scopy' => true,
		],
		'FcyTrackBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FcySclOn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fancy-scroll::-webkit-scrollbar-track',
				],
			],
			'scopy' => true,
		],
		'FcyTrackBRs' => [
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
					'condition' => [(object) ['key' => 'FcySclOn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fancy-scroll::-webkit-scrollbar-track{border-radius:{{FcyTrackBRs}};}',
				],
			],
			'scopy' => true,
		],
		'FcyTrackBsw' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FcySclOn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fancy-scroll::-webkit-scrollbar-track',
				],
			],
			'scopy' => true,
		],	

		'FancyStyle' => [
			'type' => 'string',
			'default' => 'default',	
		],
		'FancyOption' => [
			'type' => 'string',
			'default' => '[]',
		],
		'LoopFancy' => [
			'type' => 'boolean',
			'default' => true,
		],
		'infobar' => [
			'type' => 'boolean',
			'default' => true,
		],
		'ArrowsFancy' => [
			'type' => 'boolean',
			'default' => true,
		],
		'AnimationFancy' => [
			'type' => 'string',
			'default' => 'zoom',
		],
		'DurationFancy' => [
			'type' => 'string',
			'default' => 366,
		],
		'ClickContent' => [
			'type' => 'string',
			'default' => 'next',	
		],
		'Slideclick' => [
			'type' => 'string',
			'default' => 'close',	
		],
		'TransitionFancy' => [
			'type' => 'string',
			'default' => 'slide',
		],
		'TranDuration' => [
			'type' => 'string',
			'default' => 366,
		],
		'ThumbsOption' => [
			'type' => 'boolean',
			'default' => false,
		],
		'ThumbsBrCr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'ThumbsOption', 'relation' => '==', 'value' => true]],
					'selector' => '.fancybox-thumbs__list a.fancybox-thumbs-active:before,.fancybox-thumbs__list a:before',
				],
			],
			'scopy' => true,
		],
		'ThumbsBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'ThumbsOption', 'relation' => '==', 'value' => true]],
					'selector' => '.fancybox-thumbs .fancybox-thumbs__list',
				],
			],
			'scopy' => true,
		],
		
		'FancyBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '.fancybox-container .fancybox-bg',
				],
			],
			'scopy' => true,
		],
		'FancyInBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si',
				],
			],
			'scopy' => true,
		],
		'FancyInBgB' => [
			'type' => 'object',
			'default' => (object) ['openBorder' => 0],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si',
				],
			],
			'scopy' => true,
		],
		'FancyInBgBs' => [
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
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si{border-radius:{{FancyInBgBs}};}',
				],
			],
			'scopy' => true,
		],
		'FancyInBoxSw' => [
			'type' => 'object',
			'default' => (object) ['openShadow' => 0],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si',
				],
			],
			'scopy' => true,
		],
		
		'FancyName' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fcb-username a',
				],
			],
			'scopy' => true,
		],
		'FancyTime' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fcb-time a',
				],
			],
			'scopy' => true,
		],
		'FancyTitle' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fcb-title',
				],
			],
			'scopy' => true,
		],
		'FancyDes' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-message',
				],
			],
			'scopy' => true,
		],
		'FancyNameCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fcb-username a{color:{{FancyNameCr}};}',
				],
			],
			'scopy' => true,
		],
		'FancyTimeCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fcb-time a{color:{{FancyTimeCr}};}',
				],
			],
			'scopy' => true,
		],
		'FancytitleCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fcb-title{color:{{FancytitleCr}};}',
				],
			],
			'scopy' => true,
		],
		'FancyDesCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-message{color:{{FancyDesCr}};}',
				],
			],
			'scopy' => true,
		],
		'FancyiconCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-sf-footer a{color:{{FancyiconCr}};}',
				],
			],
			'scopy' => true,
		],
		'FancySICr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fcb-logo{color:{{FancySICr}};}',
				],
			],
			'scopy' => true,
		],
		'FancySIs' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fcb-logo{font-size:{{FancySIs}};}',
				],
			],
			'scopy' => true,
		],
		'FancyBtnCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fcb-footer .tpgb-btn-viewpost{background:{{FancyBtnCr}};}',
				],
			],
			'scopy' => true,
		],
		'FancyBtnTxtCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fcb-footer .tpgb-btn-viewpost a{color:{{FancyBtnTxtCr}};}',
				],
			],
			'scopy' => true,
		],
		'FancyBtnBr' => [
			'type' => 'object',
			'default' => (object) ['openBorder' => 0],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fcb-footer .tpgb-btn-viewpost',
				],
			],
			'scopy' => true,
		],
		'FancyBtnpadd' => [
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
					'condition' => [(object) ['key' => 'FancyStyle', 'relation' => '==', 'value' => ['style-1','style-2']]],
					'selector' => '{{PLUS_WRAP}}.fancybox-si .tpgb-fcb-footer .tpgb-btn-viewpost{padding:{{FancyBtnpadd}};}',
				],
			],
			'scopy' => true,
		],
	];
		
	$attributesOptions = array_merge($attributesOptions,$plusButton_options, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-social-feed', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_social_feed_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_social_feed' );

function tpgb_tp_social_feed_render_callback( $attributes, $content) {
	$SocialFeed = '';
	$block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$feed_id = (!empty($attributes['feed_id'])) ? $attributes['feed_id'] : uniqid("feed");
	$layout = (!empty($attributes['layout'])) ? $attributes['layout'] : 'grid';
	$style = (!empty($attributes['style'])) ? $attributes['style'] : 'style-1';
	$Rsocialfeed = (!empty($attributes['AllReapeter'])) ? $attributes['AllReapeter'] : [];
	$columns = (!empty($attributes['columns'])) ? $attributes['columns'] : 'tpgb-col-12';
	$RefreshTime = !empty($attributes['TimeFrq']) ? $attributes['TimeFrq'] : '3600';
	$TimeFrq = array( 'TimeFrq' => $attributes['TimeFrq'] );
	$TotalPost = (!empty($attributes['TotalPost'])) ? $attributes['TotalPost'] : 1000;
	
	$FeedId = (!empty($attributes['FeedId'])) ? preg_split("/\,/", $attributes['FeedId']) : [];
	$ShowTitle = !empty($attributes['ShowTitle']) ? $attributes['ShowTitle'] : false;
	$showFooterIn = (!empty($attributes['showFooterIn'])) ? true : false;
	
	$Postdisplay = (!empty($attributes['Postdisplay']) ? (int)$attributes['Postdisplay'] : 6);
	$postLodop = (!empty($attributes['postLodop']) ? $attributes['postLodop'] : '');
	$postview = (!empty($attributes['postview']) ? $attributes['postview'] : 1);
	$loadbtnText = (!empty($attributes['loadbtnText']) ? $attributes['loadbtnText'] : '');
	$loadingtxt = (!empty($attributes['loadingtxt']) ? $attributes['loadingtxt'] : '');
	$allposttext = (!empty($attributes['allposttext']) ? $attributes['allposttext'] : '');

	$txtLimt = (!empty($attributes['TextLimit']) ? $attributes['TextLimit'] : false );
	$TextCount = (!empty($attributes['TextCount']) ? $attributes['TextCount'] : 100 );
	$TextType = (!empty($attributes['TextType']) ? $attributes['TextType'] : 'char' );
	$TextMore = (!empty($attributes['TextMore']) ? $attributes['TextMore'] : 'Show More' );
	$TextDots = (!empty($attributes['TextDots']) ? '...' : '' );

	$FancyStyle = (!empty($attributes['FancyStyle']) ? $attributes['FancyStyle'] : 'default' );
	$DescripBTM = (!empty($attributes['DescripBTM']) ? $attributes['DescripBTM'] : false );
	$MediaFilter = (!empty($attributes['MediaFilter']) ? $attributes['MediaFilter'] : 'default' );
	
	$ShowFeedId = !empty($attributes['ShowFeedId']) ? $attributes['ShowFeedId'] : false;
	$PopupOption = !empty($attributes['OnPopup']) ? $attributes['OnPopup'] : 'Donothing';
	$Performance = !empty($attributes['perf_manage']) ? $attributes['perf_manage'] : false;

	$NormalScroll='';
	$ScrollOn = !empty($attributes['ScrollOn']) ? $attributes['ScrollOn'] : false;
	$FcyScrolllOn = !empty($attributes['FcySclOn']) ? $attributes['FcySclOn'] : false;
	$OffsetPost = !empty($FeedId) ? $Postdisplay - count($FeedId) : '';
	
	if( !empty($ScrollOn) || !empty($FcyScrolllOn) ){
		$ScrollData = array(
			'className'     => 'tpgb-normal-scroll',
			'ScrollOn'      => $ScrollOn,
			'Height'        => !empty($attributes['ScrollHgt']) ? (int)$attributes['ScrollHgt'] : 150,
			'TextLimit'     => $txtLimt,

			'Fancyclass'    => 'tpgb-fancy-scroll',
			'FancyScroll'   => $FcyScrolllOn,
			'FancyHeight'   => !empty($attributes['FcySclHgt']) ? (int)$attributes['FcySclHgt'] : 150
		);
		$NormalScroll = json_encode($ScrollData, true);
	}

	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$list_layout='';
	if( $layout=='grid' || $layout=='masonry' ){
		$list_layout = 'tpgb-isotope';
	}else{
		$list_layout = 'tpgb-isotope';
	}

	$desktop_class='';
	if( $columns ){
		$desktop_class .= 'tpgb-col-'.esc_attr($columns['xs']);
		$desktop_class .= ' tpgb-col-lg-'.esc_attr($columns['md']);
		$desktop_class .= ' tpgb-col-md-'.esc_attr($columns['sm']);
		$desktop_class .= ' tpgb-col-sm-'.esc_attr($columns['xs']);
	}
	
	$fancybox_settings = "";
	if($PopupOption=='OnFancyBox'){
		$fancybox_settings = tpgb_social_feed_fancybox($attributes);
		$fancybox_settings = json_encode($fancybox_settings);
	}
	

	$SocialFeed .= '<div id="'.esc_attr($block_id).'" class="tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' tpgb-social-feed tpgb-relative-block '.esc_attr($list_layout).'" data-style="'.esc_attr($style).'" data-layout="'.esc_attr($layout).'" data-id="'.esc_attr($block_id).'" data-fid="'.esc_attr($feed_id).'" data-fancy-option=\''.$fancybox_settings.'\' data-scroll-normal=\''.esc_attr($NormalScroll).'\' >';
		
		$FancyBoxJS = '';
		if($PopupOption == 'OnFancyBox'){
			$FancyBoxJS = 'data-fancybox="'.esc_attr($block_id).'"';
		}
		
		$FinalData = [];
		$Perfo_transient = get_transient("SF-Performance-$feed_id");
		
		if( ($Performance == false) || ($Performance == true && $Perfo_transient === false) ){
			$AllData = [];
			foreach ($Rsocialfeed as $index => $social) {
				$RFeed = (!empty($social['selectFeed'])) ? $social['selectFeed'] : 'Facebook';
				$social = array_merge($TimeFrq,$social);

				if($RFeed == 'Facebook'){
					$AllData[] = tpgb_FacebookFeed($social, $attributes);
				}
			}
			if(!empty($AllData)){
				foreach($AllData as $key => $val){
					foreach($val as $key => $vall){ 
						$FinalData[] =  $vall; 
					}
				}
			}
			$Feed_Index = array_column($FinalData, 'Feed_Index');
			array_multisort($Feed_Index, SORT_ASC, $FinalData);
			set_transient("SF-Performance-$feed_id", $FinalData, $RefreshTime);
		} else {
			$FinalData = get_transient("SF-Performance-$feed_id");
		}
		
		if(!empty($FinalData)){
			foreach ($FinalData as $index => $data) {
				$PostId = !empty($data['PostId']) ? $data['PostId'] : [];
				if(in_array($PostId, $FeedId)){
					unset($FinalData[$index]);
				}
			}

			if(!empty($FinalData)){
				$SocialFeed .= '<div class="post-loop-inner social-feed-'.esc_attr($style).'" >';
				foreach ($FinalData as $F_index => $AllVmData) {
					$uniqEach = uniqid();
					$PopupSylNum = "{$block_id}-${F_index}-{$uniqEach}";
					$RKey = (!empty($AllVmData['RKey'])) ? $AllVmData['RKey'] : '';
					$PostId = (!empty($AllVmData['PostId'])) ? $AllVmData['PostId'] : '';
					$UName = (!empty($AllVmData['UName'])) ? $AllVmData['UName'] : '';
					$selectFeed = (!empty($AllVmData['selectFeed'])) ? $AllVmData['selectFeed'] : '';
					$Massage = (!empty($AllVmData['Massage'])) ? $AllVmData['Massage'] : '';
					$Description = (!empty($AllVmData['Description'])) ? $AllVmData['Description'] : '';
					$Type = (!empty($AllVmData['Type'])) ? $AllVmData['Type'] : '';
					$PostLink = (!empty($AllVmData['PostLink'])) ? $AllVmData['PostLink'] : '';
					$CreatedTime = (!empty($AllVmData['CreatedTime'])) ? $AllVmData['CreatedTime'] : '';
					$PostImage = (!empty($AllVmData['PostImage'])) ? $AllVmData['PostImage'] : '';
					$UserName = (!empty($AllVmData['UserName'])) ? $AllVmData['UserName'] : '';
					$UserImage = (!empty($AllVmData['UserImage'])) ? $AllVmData['UserImage'] : '';
					$UserLink = (!empty($AllVmData['UserLink'])) ? $AllVmData['UserLink'] : '';
					$socialIcon = (!empty($AllVmData['socialIcon'])) ? $AllVmData['socialIcon'] : '';
					$ErrorClass = (!empty($AllVmData['ErrorClass'])) ? $AllVmData['ErrorClass'] : '';

					$EmbedURL = (!empty($AllVmData['Embed'])) ? $AllVmData['Embed'] : '';
					$EmbedType = (!empty($AllVmData['EmbedType'])) ? $AllVmData['EmbedType'] : '';
					
					if($selectFeed == 'Facebook'){
						$Fblikes = (!empty($AllVmData['FbLikes'])) ? $AllVmData['FbLikes'] : 0;
						$comment = (!empty($AllVmData['comment'])) ? $AllVmData['comment'] : 0;
						$share = (!empty($AllVmData['share'])) ? $AllVmData['share'] : 0;
						$likeImg = TPGB_ASSETS_URL.'assets/images/social-feed/like.png';
						$ReactionImg = TPGB_ASSETS_URL.'assets/images/social-feed/love.png';
						
						$FbAlbum = (!empty($AllVmData['FbAlbum'])) ? $AllVmData['FbAlbum'] : false;
						if(!empty($FbAlbum)){
							$FancyBoxJS = 'data-fancybox="album-Facebook'.esc_attr($F_index).'-'.esc_attr($block_id).'"';
						}
					}
					$ImageURL=$videoURL="";
					if($Type == 'video' || $Type == 'photo'){
						$sepPostId = explode("_",$PostId);
						$newPId = (!empty($sepPostId[1])) ? $sepPostId[1] : '';
						$fbPostRD = 'https://www.facebook.com/'.esc_attr($UName).'/posts/'.esc_attr($newPId);
						$videoURL = ($selectFeed == 'Facebook' && $PopupOption == 'GoWebsite') ? $fbPostRD : $PostLink;
						$ImageURL = $PostImage;
					}
					if(!empty($FbAlbum)){
						$PostLink = (!empty($PostLink[0]['link'])) ? $PostLink[0]['link'] : 0;
					}
					
					if( (!in_array($PostId,$FeedId) && $F_index < $TotalPost) && ( ($MediaFilter == 'default') || ($MediaFilter == 'ompost' && !empty($PostLink) && !empty($PostImage)) || ($MediaFilter == 'hmcontent' &&  empty($PostLink) && empty($PostImage) )) ){
						$SocialFeed .= '<div class="grid-item splide__slide '.esc_attr('feed-'.$selectFeed.' '.$desktop_class .' '.$RKey.' ').'" data-index="'.esc_attr($selectFeed).esc_attr($F_index).'" >';
							ob_start(); 
								include TPGB_INCLUDES_URL. "social-feed/social-feed-".sanitize_file_name($style).".php";
								$SocialFeed .= ob_get_contents();
							ob_end_clean();
						$SocialFeed .= '</div>';
					}

				}
				$SocialFeed .= '</div>';
			}else{
				$SocialFeed .= '<div class="error-handal">'.esc_html__('All Social Feed','tpgb').'</div>';
			}
		}else{
			$SocialFeed .= '<div class="error-handal">'.esc_html__('All Social Feed','tpgb').'</div>';
		}

	$SocialFeed .= '</div>';

    return $SocialFeed;
}

function tpgb_FacebookFeed($social,$attr){
	$BaseURL = 'https://graph.facebook.com/v11.0';
	$FbKey = (!empty($social['_key'])) ? $social['_key'] : '';
	$FbAcT = (!empty($social['RAToken'])) ? $social['RAToken'] : '';
	$FbPType = (!empty($social['ProfileType'])) ? $social['ProfileType'] : 'post';
	$FbPageid = (!empty($social['Pageid'])) ? $social['Pageid'] : '';
	$FbAlbum = (!empty($social['fbAlbum'])) ? $social['fbAlbum'] : false;
	$FbLimit = (!empty($social['MaxR'])) ? $social['MaxR'] : 6;
	$FbALimit = (!empty($social['AlbumMaxR'])) ? $social['AlbumMaxR'] : 6;	
	$Fbcontent = (!empty($social['content'])) ? $social['content'] : [];
	$FbTime = (!empty($social['TimeFrq'])) ? $social['TimeFrq'] : '3600';
	$FbselectFeed = !empty($social['selectFeed']) ? $social['selectFeed'] : '';
	$FbIcon = 'fab fa-facebook social-logo-fb';
	$SSL_VER = $attr['CURLOPT_SSL_VERIFYPEER'];
	$content = [];
	if(!empty($Fbcontent) && (is_array($Fbcontent) || is_object($Fbcontent)) ){
		foreach ($Fbcontent as $Data) {
			$Filter = (!empty($Data['value'])) ? $Data['value'] : 'photo';
			array_push($content,$Filter);
		}
	}else{
		array_push($content,'photo');
	}
	
	$url = '';
	$FbAllData = '';
	$FbArr = [];
	if(!empty($FbAcT) && $FbPType == 'post'){
		$url = "{$BaseURL}/me?fields=id,name,first_name,last_name,link,email,birthday,picture,posts.limit($FbLimit){type,message,story,caption,description,shares,picture,full_picture,source,created_time,reactions.summary(true),comments.summary(true).filter(toplevel)},albums.limit($FbLimit){id,type,link,picture,created_time,name,count,photos.limit($FbALimit){id,link,created_time,likes,images,name,comments.summary(true).filter(toplevel)}}&access_token={$FbAcT}";
	}else if(!empty($FbAcT) && !empty($FbPageid) && $FbPType == 'page'){
		$url = "{$BaseURL}/{$FbPageid}?fields=id,name,username,link,fan_count,new_like_count,phone,emails,about,birthday,category,picture,posts.limit($FbLimit){id,full_picture,created_time,message,attachments{media,media_type,title,url},picture,story,status_type,shares,reactions.summary(true),likes.summary(true),comments.summary(true).filter(toplevel)},albums.limit($FbLimit){id,type,link,picture,created_time,name,count,photos.limit($FbALimit){id,link,created_time,images,name}}&access_token={$FbAcT}";
	}
	
	if(!empty($url)){
		$GetFbRL = get_transient("Fb-Url-$FbKey");
		$GetFbTime = get_transient("Fb-Time-$FbKey");
		
		if( $GetFbRL != $url || $GetFbTime != $FbTime ){
			$FbAllData = tpgb_api_call($url,$SSL_VER);
				set_transient("Fb-Url-$FbKey", $url, $FbTime);
				set_transient("Data-Fb-$FbKey", $FbAllData, $FbTime);
				set_transient("Fb-Time-$FbKey", $FbTime, $FbTime);
		 }else{
		 	$FbAllData = get_transient("Data-Fb-$FbKey");
		 }
		
		$status = (!empty($FbAllData['HTTP_CODE']) ? $FbAllData['HTTP_CODE'] : '');
		if($status == 200){
			$FbPost = '';
			if(!empty($FbAlbum)){
				$FbPost = (!empty($FbAllData['albums']['data'])) ? $FbAllData['albums']['data'] : [];
			}else{
				$FbPost = (!empty($FbAllData['posts']['data'])) ? $FbAllData['posts']['data'] : [];
			}
			
			foreach ($FbPost as $index => $FbData){
				
				$link = (!empty($FbAllData['link']) ? $FbAllData['link'] : '');
				$name = (!empty($FbAllData['name']) ? $FbAllData['name'] : '');
				$u_name = (!empty($FbAllData['username']) ? $FbAllData['username'] : '');
				$id = (!empty($FbData['id']) ? $FbData['id'] : '');
				$type = (!empty($FbData['type']) ? $FbData['type'] : '');
				$FbMessage = (!empty($FbData['message']) ? $FbData['message'] : '');
				$FbPicture = $FbSource = (!empty($FbData['full_picture']) ? $FbData['full_picture'] : '');
				$Created_time = (!empty($FbData['created_time'])) ? tpgb_feed_Post_time($FbData['created_time']) : '';
				$FbReactions = (!empty($FbData['reactions']['summary']['total_count']) ? tpgb_number_short($FbData['reactions']['summary']['total_count']) : 0);
				$FbComments = (!empty($FbData['comments']['summary']['total_count']) ? tpgb_number_short($FbData['comments']['summary']['total_count']) : 0);
				$Fbshares = (!empty($FbData['shares']['count']) ? tpgb_number_short($FbData['shares']['count']) : '');
				
				

				if($type == "video"){
					$FbSource = (!empty($FbData['source']) ? $FbData['source'] : '');
				}
				$FbCaption = (!empty($FbData['caption']) ? $FbData['caption'] : '');
				$FbDescription = (!empty($FbData['description'])) ? $FbData['description'] : '';
				
				if($FbPType == 'page'){
					$type = (!empty($FbData['attachments']['data'][0]['media_type']) ? $FbData['attachments']['data'][0]['media_type'] : '');
					if($type == 'album'){
						$type = "photo";
					}
					if($type == 'video'){
						$FbSource = (!empty($FbData['attachments']['data'][0]['media']['source']) ? $FbData['attachments']['data'][0]['media']['source'] : '');
					}
				}
				

				if(!empty($FbAlbum)){
					$type = 'video'; 
					$link = (!empty($FbData['link']) ? $FbData['link'] : '');
					$FbMessage = (!empty($FbData['name']) ? $FbData['name'] : '');
					$Fbcount = (!empty($FbData['count']) ? $FbData['count'] : '');
					$FbPicture = (!empty($FbData['picture']['data']['url']) ? $FbData['picture']['data']['url'] : '');
					$FbSource = (!empty($FbData['photos']['data']) ? $FbData['photos']['data'] : []);
				}
				
				if( (in_array('photo',$content) && $type == 'photo') || (in_array('video',$content) && $type == 'video') || ( in_array('status',$content) && ($type == 'status' || $type == 'link')) ){	
					$FbArr[] = array(
						"Feed_Index"	=> $index,
						"PostId"		=> $id,
						"Massage" 		=> $FbCaption . $FbDescription,
						"Description"	=> $FbMessage,
						"Type" 			=> "video",
						"PostLink" 		=> $FbSource,
						"CreatedTime" 	=> $Created_time,
						"PostImage" 	=> $FbPicture,
						"UserName" 		=> $name,
						"UName"			=> $u_name,
						"UserImage" 	=> (!empty($FbAllData['picture']['data']['url']) ? $FbAllData['picture']['data']['url'] : ''),
						"UserLink" 		=> $link,
						"share" 		=> $Fbshares,
						"comment" 		=> $FbComments,
						"FbLikes" 		=> $FbReactions,
						"Embed" 		=> "Alb",
						"EmbedType"     => $type,
						"FbAlbum" 		=> $FbAlbum,
						"socialIcon" 	=> $FbIcon,
						"selectFeed"    => $FbselectFeed,
						"RKey" 			=> "tp-repeater-item-$FbKey",
					);
				}
			}		
		}else{
			$FbArr[] = tpgb_SF_Error_handler($FbAllData, $FbKey, $FbselectFeed, $FbIcon);
		}
	}else{
		$Msg = "";
		if(empty($FbAcT)){
			$Msg .= 'Empty Access Token </br>';
		}
		if($FbPType == 'page' && empty($FbPageid)){
			$Msg .= 'Empty Page ID';
		}
		$ErrorData['error']['message'] = $Msg;
		$FbArr[] = tpgb_SF_Error_handler($ErrorData, $FbKey, $FbselectFeed, $FbIcon);
	}
	
	return $FbArr;
}

function tpgb_api_call($API,$SSL){
	$CURLOPT_SSL_VERIFYPEER = $SSL;
	$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $API,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_SSL_VERIFYPEER => $CURLOPT_SSL_VERIFYPEER
			));
	$response = json_decode(curl_exec($curl),true);
	$statuscode = array("HTTP_CODE"=>curl_getinfo($curl, CURLINFO_HTTP_CODE));
	
	$Final=[];
	if(is_array($statuscode) && is_array($response)){
		$Final = array_merge($statuscode,$response);
	}
	curl_close($curl);
	return $Final;
}

function tpgb_feed_Post_time($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
 
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
 
    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
 
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function tpgb_number_short( $n, $precision = 1 ) {
    if ($n < 900) {
        $n_format = number_format($n, $precision);
        $suffix = '';
    } else if ($n < 900000) {
        $n_format = number_format($n / 1000, $precision);
        $suffix = 'K';
    } else if ($n < 900000000) {
        $n_format = number_format($n / 1000000, $precision);
        $suffix = 'M';
    } else if ($n < 900000000000) {
        $n_format = number_format($n / 1000000000, $precision);
        $suffix = 'B';
    } else {
        $n_format = number_format($n / 1000000000000, $precision);
        $suffix = 'T';
	}
	
    if ( $precision > 0 ) {
        $dotzero = '.' . str_repeat( '0', $precision );
        $n_format = str_replace( $dotzero, '', $n_format );
    }
    return $n_format . $suffix;
}

function tpgb_social_feed_fancybox($attr){
	$FancyData = (!empty($attr['FancyOption'])) ? json_decode($attr['FancyOption']) : [];

	$button = array();
	if (is_array($FancyData) || is_object($FancyData)) {
		foreach ($FancyData as $value) {
			$button[] = $value->value;
		}
	}

	$fancybox = array();
	$fancybox['loop'] = $attr['LoopFancy'];
	$fancybox['infobar'] = $attr['infobar'];
	$fancybox['arrows'] = $attr['ArrowsFancy'];
	$fancybox['animationEffect'] = $attr['AnimationFancy'];
	$fancybox['animationDuration'] = $attr['DurationFancy'];
	$fancybox['slideclick'] = $attr['Slideclick'];
	$fancybox['clickContent'] = $attr['ClickContent'];
	$fancybox['transitionEffect'] = $attr['TransitionFancy'];
	$fancybox['transitionDuration'] = $attr['TranDuration'];
	$fancybox['button'] = $button;

	return $fancybox;
}

function tpgb_SF_Error_handler($ErrorData, $Rkey='', $selectFeed='', $Icon=''){
	$Error = !empty($ErrorData['error']) ? $ErrorData['error'] : [];
	return array(
		"Feed_Index" 	=> 0,
		"ErrorClass"    => "error-class",
		"socialIcon" 	=> $Icon,
		"CreatedTime" 	=> "<b>{$selectFeed}</b>",
		"Description" 	=> !empty($Error['message']) ? $Error['message'] : 'Something Wrong',
		"UserName" 		=> !empty($Error['HTTP_CODE']) ? 'Error Code : '.$Error['HTTP_CODE'] : 400,
		"UserImage" 	=> TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
		"selectType"    => $selectFeed,
		"RKey" 			=> "tp-repeater-item-$Rkey",
	);
}