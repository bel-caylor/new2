<?php
/**
 * Block : TP Social Feed
 * @since 1.3.0.1
 */
defined( 'ABSPATH' ) || exit;

function tpgbp_tp_social_feed() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$plusButton_options = Tpgb_Blocks_Global_Options::load_plusButton_options();
	$carousel_options = Tpgb_Blocks_Global_Options::carousel_options();
	$globalEqualHeightOptions = Tpgbp_Plus_Extras_Opt::load_plusEqualHeight_options();

	$sliderOpt = [
		'slideColumns' => [
			'type' => 'object',
			'default' => [ 'md' => '3','sm' => '3','xs' => '2' ],
		],
	];
	$carousel_options = array_merge($carousel_options,$sliderOpt);

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
					'InstagramType' => [
						'type' => 'string',
						'default' =>'Instagram_Basic',	
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

					'IGImgPic' => [
						'type' => 'object',
						'default' => [
							'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder-grid.jpg',
							'Id' => '',
						],
					],
					'IG_FeedTypeGp' => [
						'type' => 'string',
						'default' => 'IGUserdata',	
					],
					'IGUserName_GP' => [
						'type' => 'string',
						'default' => '',	
					],
					'IGHashtagName_GP' => [
						'type' => 'string',
						'default' => '',	
					],
					'IG_hashtagType' => [
						'type' => 'string',
						'default' => 'top_media',	
					],

					'TwApi' => [
						'type' => 'string',
						'default' =>'',	
					],
					'TwApiSecret' => [
						'type' => 'string',
						'default' =>'',	
					],
					'TwAccesT' => [
						'type' => 'string',
						'default' =>'',	
					],
					'TwAccesTS' => [
						'type' => 'string',
						'default' =>'',	
					],
					'TwfeedType' => [
						'type' => 'string',
						'default' =>'userfeed',	
					],
					'Twtimeline' => [
						'type' => 'string',
						'default' =>'Hometimline',	
					],
					'TwSearch' => [
						'type' => 'string',
						'default' =>'',	
					],
					'TwRtype' => [
						'type' => 'string',
						'default' =>'',	
					],
					'TwWOEID' => [
						'type' => 'string',
						'default' =>'',	
					],
					'TwcustId' => [
						'type' => 'string',
						'default' =>'',	
					],
					'TwUsername' => [
						'type' => 'string',
						'default' =>'',	
					],
					'Twlistsid' => [
						'type' => 'string',
						'default' =>'',	
					],
					'Twcollsid' => [
						'type' => 'string',
						'default' =>'',	
					],
					'TwRetweet' => [
						'type' => 'string',
						'default' =>'',	
					],
					'TwComRep' => [
						'type' => 'string',
						'default' =>'',	
					],
					
					'VimeoType' => [
						'type' => 'string',
						'default' => 'Vm_Channel',	
					],
					'VmUname' => [
						'type' => 'string',
						'default' =>'',	
					],
					'VmQsearch' => [
						'type' => 'string',
						'default' =>'',	
					],
					'VmChannel' => [
						'type' => 'string',
						'default' =>'',	
					],
					'VmGroup' => [
						'type' => 'string',
						'default' =>'',	
					],
					'VmAlbum' => [
						'type' => 'string',
						'default' =>'',	
					],
					'VmAlbumPass' => [
						'type' => 'string',
						'default' =>'',	
					],
					'VmCategories' => [
						'type' => 'string',
						'default' =>'',	
					],

					'RYtType' => [
						'type' => 'string',
						'default' =>'YT_Channel',	
					],
					'YtName' => [
						'type' => 'string',
						'default' =>'',	
					],
					'YTChannel' => [
						'type' => 'string',
						'default' =>'',	
					],
					'YTPlaylist' => [
						'type' => 'string',
						'default' =>'',	
					],
					'YTsearchQ' => [
						'type' => 'string',
						'default' =>'',	
					],
					'YTvOrder' => [
						'type' => 'string',
						'default' =>'date',	
					],
					'YTthumbnail' => [
						'type' => 'string',
						'default' =>'default',	
					],

					'MaxR' => [
						'type' => 'string',
						'default' => 6,	
					],
					'RCategory' => [
						'type' => 'string',
						'default' =>'',	
					],

				],
			],
			'default' => [ 
				['_key'=> $uidId, 'selectFeed' => 'Facebook', 'FbTokenGen' => 'manually', 'ProfileType' => 'post', 'InstagramType' => 'Instagram_Basic', 'IG_FeedTypeGp' => 'IGUserdata', 'MaxR' => 6 , 'RYtType' => 'YT_Channel', 'content' => '[]', 'TwfeedType' => 'userfeed', 'Twtimeline' => 'Hometimline', 'VimeoType'=>'Vm_Channel', 'YTthumbnail'=>'default' ],
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
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel']],
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
		'TextCat' => [
			'type'=> 'string',
			'default'=> 'All',
		],
		'CatFilterS' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'CatName' => [
			'type'=> 'string',
			'default'=> 'Filters',
		],
		'FilterHs' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'FilterAlig' => [
			'type' => 'string',
			'default' =>  [ 'md' => 'center', 'sm' =>  '', 'xs' =>  '' ],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-filter-data{text-align:{{FilterAlig}};}',
				],
			],
			'scopy' => true,
		],

		// load more
		'Postdisplay' => [
			'type' => 'string',
			'default' => '',
		],
		'postLodop' => [
			'type' => 'string',
			'default' => 'none',
		],
		'postview' => [
			'type'=> 'string',
			'default'=> '',
		],
		'loadbtnText' => [
			'type' => 'string',
			'default' => 'Load More',
		],
		'loadingtxt' => [
			'type' => 'string',
			'default' => 'Loading...',
		],
		'allposttext' => [
			'type' => 'string',
			'default' => 'All Done',
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

		'VmMsgTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo .tpgb-sf-feed .tpgb-title',
				],
			],
			'scopy' => true,
		],
		'VmDesTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo .tpgb-sf-feed .tpgb-message',
				],
			],
			'scopy' => true,
		],
		'VmNameTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo .tpgb-sf-feed .tpgb-sf-username a',
				],
			],
			'scopy' => true,
		],
		'VmTimeTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo .tpgb-sf-feed .tpgb-sf-time a',
				],
			],
			'scopy' => true,
		],
		'vmIconSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .feed-Vimeo .social-logo-vm {font-size:{{vmIconSize}};}',
				],
			],
			'scopy' => true,
		],
		'vmIconColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .feed-Vimeo .social-logo-vm {color:{{vmIconColor}};}',
				],
			],
			'scopy' => true,
		],

		'VmNBgCr' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'VmNBcr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'VmNBRs' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo .tpgb-sf-feed{border-radius:{{VmNBRs}};}',
				],
			],
			'scopy' => true,
		],
		'VmNBs' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'VmHBgCr' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo:hover .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'VmHBcr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo:hover .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'VmHBrs' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo:hover .tpgb-sf-feed{border-radius:{{VmHBrs}};}',
				],
			],
			'scopy' => true,
		],
		
		'VmHBs' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo:hover .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],

		'VmPRs' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo .tpgb-sf-feed .tpgb-sf-logo{border-radius:{{VmPRs}};}',
				],
			],
			'scopy' => true,
		],

		'VmNNameC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo .tpgb-sf-feed .tpgb-sf-username a{color:{{VmNNameC}};}',
				],
			],
			'scopy' => true,
		],
		'VmNTimeC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo .tpgb-sf-feed .tpgb-sf-time a{color:{{VmNTimeC}};}',
				],
			],
			'scopy' => true,
		],
		'VmNIconCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo .tpgb-sf-feed .tpgb-sf-footer{color:{{VmNIconCr}};}',
				],
			],
			'scopy' => true,
		],
		'VmNTitleC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo .tpgb-sf-feed .tpgb-title{color:{{VmNTitleC}};}',
				],
			],
			'scopy' => true,
		],
		'VmNDesC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo .tpgb-sf-feed .tpgb-message{color:{{VmNDesC}};}',
				],
			],
			'scopy' => true,
		],
		'VmHNameC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo:hover .tpgb-sf-feed .tpgb-sf-username a{color:{{VmHNameC}};}',
				],
			],
			'scopy' => true,
		],
		'VmHTimeC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo:hover .tpgb-sf-feed .tpgb-sf-time a{color:{{VmHTimeC}};}',
				],
			],
			'scopy' => true,
		],
		'VmHIconCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo:hover .tpgb-sf-feed .tpgb-sf-footer{color:{{VmHIconCr}};}',
				],
			],
			'scopy' => true,
		],
		'VmHTitleC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo:hover .tpgb-sf-feed .tpgb-title{color:{{VmHTitleC}};}',
				],
			],
			'scopy' => true,
		],
		'VmHDesC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Vimeo:hover .tpgb-sf-feed .tpgb-message{color:{{VmHDesC}};}',
				],
			],
			'scopy' => true,
		],

		'YtMsgTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube .tpgb-sf-feed .tpgb-title',
				],
			],
			'scopy' => true,
		],
		'YtDesTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube .tpgb-sf-feed .tpgb-message',
				],
			],
			'scopy' => true,
		],
		'YtNameTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube .tpgb-sf-feed .tpgb-sf-username a',
				],
			],
			'scopy' => true,
		],
		'YtTimeTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube .tpgb-sf-feed .tpgb-sf-time a',
				],
			],
			'scopy' => true,
		],
		'ytIconSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .feed-Youtube .social-logo-yt {font-size:{{ytIconSize}};}',
				],
			],
			'scopy' => true,
		],
		'ytIconColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .feed-Youtube .social-logo-yt {color:{{ytIconColor}};}',
				],
			],
			'scopy' => true,
		],

		'YtNBgCr' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'YtNBcr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'YtNBrs' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube .tpgb-sf-feed{border-radius:{{YtNBrs}};}',
				],
			],
			'scopy' => true,
		],
		'YtNBs' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'YtHBgCr' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube:hover .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'YtHBcr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube:hover .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'YtHBrs' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube:hover .tpgb-sf-feed{border-radius:{{YtHBrs}};}',
				],
			],
			'scopy' => true,
		],
		'YtHBs' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube:hover .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],

		'YtPRs' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube .tpgb-sf-feed .tpgb-sf-logo{border-radius:{{YtPRs}};}',
				],
			],
			'scopy' => true,
		],

		'YtNNameC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube .tpgb-sf-feed .tpgb-sf-username a{color:{{YtNNameC}};}',
				],
			],
			'scopy' => true,
		],
		'YtNTimeC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube .tpgb-sf-feed .tpgb-sf-time a{color:{{YtNTimeC}};}',
				],
			],
			'scopy' => true,
		],
		'YtNIconCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube .tpgb-sf-feed .tpgb-sf-footer{color:{{YtNIconCr}};}',
				],
			],
			'scopy' => true,
		],
		'YtNTitleC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube .tpgb-sf-feed .tpgb-title{color:{{YtNTitleC}};}',
				],
			],
			'scopy' => true,
		],
		'YtNDesC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube .tpgb-sf-feed .tpgb-message{color:{{YtNDesC}};}',
				],
			],
			'scopy' => true,
		],
		'YtHNameC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube:hover .tpgb-sf-feed .tpgb-sf-username a{color:{{YtHNameC}};}',
				],
			],
			'scopy' => true,
		],
		'YtHTimeC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube:hover .tpgb-sf-feed .tpgb-sf-time a{color:{{YtHTimeC}};}',
				],
			],
			'scopy' => true,
		],
		'YtHIconCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube:hover .tpgb-sf-feed .tpgb-sf-footer{color:{{YtHIconCr}};}',
				],
			],
			'scopy' => true,
		],
		'YtHTitleC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube:hover .tpgb-sf-feed .tpgb-title{color:{{YtHTitleC}};}',
				],
			],
			'scopy' => true,
		],
		'YtHDesC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Youtube:hover .tpgb-sf-feed .tpgb-message{color:{{YtHDesC}};}',
				],
			],
			'scopy' => true,
		],
		
		'TwMsgTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter .tpgb-sf-feed .tpgb-title',
				],
			],
			'scopy' => true,
		],
		'TwDesTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter .tpgb-sf-feed .tpgb-message',
				],
			],
			'scopy' => true,
		],
		'TwNameTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter .tpgb-sf-feed .tpgb-sf-username',
				],
			],
			'scopy' => true,
		],
		'TwTimeTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter .tpgb-sf-feed .tpgb-sf-time a',
				],
			],
			'scopy' => true,
		],
		'twIconSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .feed-Twitter .social-logo-tw {font-size:{{twIconSize}};}',
				],
			],
			'scopy' => true,
		],
		'twIconColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .feed-Twitter .social-logo-tw {color:{{twIconColor}};}',
				],
			],
			'scopy' => true,
		],
		
		'TwNBgCr' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'TwNBcr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'TwNBrs' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter .tpgb-sf-feed{border-radius:{{TwNBrs}};}',
				],
			],
			'scopy' => true,
		],
		'TwNBs' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'TwHBgCr' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter:hover .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'TwHBcr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter:hover .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'TwHBrs' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter:hover .tpgb-sf-feed{border-radius:{{TwHBrs}};}',
				],
			],
			'scopy' => true,
		],
		'TwHBs' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter:hover .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],

		'TwPRs' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter .tpgb-sf-feed .tpgb-sf-logo{border-radius:{{FbPRs}};}',
				],
			],
			'scopy' => true,
		],

		'TwNNameC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter .tpgb-sf-feed .tpgb-sf-username a{color:{{TwNNameC}};}',
				],
			],
			'scopy' => true,
		],
		'TwNTimeC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter .tpgb-sf-feed .tpgb-sf-time a{color:{{TwNTimeC}};}',
				],
			],
			'scopy' => true,
		],
		'TwNIconCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter .tpgb-sf-feed .tpgb-sf-footer{color:{{TwNIconCr}};}',
				],
			],
			'scopy' => true,
		],
		'TwNTitleC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter .tpgb-sf-feed .tpgb-title{color:{{TwNTitleC}};}',
				],
			],
			'scopy' => true,
		],
		'TwNDesC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter .tpgb-sf-feed .tpgb-message{color:{{TwNDesC}};}',
				],
			],
			'scopy' => true,
		],
		'TwHNameC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter:hover .tpgb-sf-feed .tpgb-sf-username a{color:{{TwHNameC}};}',
				],
			],
			'scopy' => true,
		],
		'TwHTimeC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter:hover .tpgb-sf-feed .tpgb-sf-time a{color:{{TwHTimeC}};}',
				],
			],
			'scopy' => true,
		],
		'TwHIconCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter:hover .tpgb-sf-feed .tpgb-sf-footer a{color:{{TwHIconCr}};}',
				],
			],
			'scopy' => true,
		],
		'TwHTitleC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter:hover .tpgb-sf-feed .tpgb-title{color:{{TwHTitleC}};}',
				],
			],
			'scopy' => true,
		],
		'TwHDesC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Twitter:hover .tpgb-sf-feed .tpgb-message{color:{{TwHDesC}};}',
				],
			],
			'scopy' => true,
		],
		
		'IgMsgTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram .tpgb-sf-feed .tpgb-title',
				],
			],
			'scopy' => true,
		],
		'IgDesTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram .tpgb-sf-feed .tpgb-message',
				],
			],
			'scopy' => true,
		],
		'IgNameTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram .tpgb-sf-feed .tpgb-sf-username a',
				],
			],
			'scopy' => true,
		],
		'IgTimeTp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram .tpgb-sf-feed .tpgb-sf-time a',
				],
			],
			'scopy' => true,
		],
		'igIconSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .feed-Instagram .social-logo-ig {font-size:{{igIconSize}};}',
				],
			],
			'scopy' => true,
		],
		'igIconColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .feed-Instagram .social-logo-ig {color:{{igIconColor}};}',
				],
			],
			'scopy' => true,
		],
		
		'IgNBgCr' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'IgNBcr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'IgNBrs' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram .tpgb-sf-feed{border-radius:{{IgNBrs}};}',
				],
			],
			'scopy' => true,
		],
		'IgNBs' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'IgHBgCr' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram:hover .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'IgHBcr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram:hover .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],
		'IgHBrs' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram:hover .tpgb-sf-feed{border-radius:{{IgHBrs}};}',
				],
			],
			'scopy' => true,
		],
		'IgHBs' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram:hover .tpgb-sf-feed',
				],
			],
			'scopy' => true,
		],

		'IgPRs' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram .tpgb-sf-feed .tpgb-sf-logo{border-radius:{{IgPRs}};}',
				],
			],
			'scopy' => true,
		],

		'IgNNameC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram .tpgb-sf-feed .tpgb-sf-username a{color:{{IgNNameC}};}',
				],
			],
			'scopy' => true,
		],
		'IgNTimeC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram .tpgb-sf-feed .tpgb-sf-time a{color:{{IgNTimeC}};}',
				],
			],
			'scopy' => true,
		],
		'IgNTitleC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram .tpgb-sf-feed .tpgb-title{color:{{IgNTitleC}};}',
				],
			],
			'scopy' => true,
		],
		'IgNDesC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram .tpgb-sf-feed .tpgb-message{color:{{IgNDesC}};}',
				],
			],
			'scopy' => true,
		],
		'IgHNameC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram .tpgb-sf-feed:hover .tpgb-sf-username a{color:{{IgHNameC}};}',
				],
			],
			'scopy' => true,
		],
		'IgHTimeC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram .tpgb-sf-feed:hover .tpgb-sf-time a{color:{{IgHTimeC}};}',
				],
			],
			'scopy' => true,
		],
		'IgHTitleC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram .tpgb-sf-feed:hover .tpgb-title{color:{{IgHTitleC}};}',
				],
			],
			'scopy' => true,
		],
		'IgHDesC' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4']] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-feed .feed-Instagram .tpgb-sf-feed:hover .tpgb-message{color:{{IgHDesC}};}',
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
		
		'FcatTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-categories .tpgb-filter-list a',
				],
			],
			'scopy' => true,
		],
		'InPadding' => [
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
					'condition' => [(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-1'],
									(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],	
					'selector' => '{{PLUS_WRAP}} .tpgb-categories.hover-style-1 .tpgb-filter-list a span:not(.tpgb-category-count){padding:{{InPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-2'],
									(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count),{{PLUS_WRAP}} .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)::before{padding:{{InPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-3'],
									(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],	
					'selector' => '{{PLUS_WRAP}} .tpgb-categories.hover-style-3 .tpgb-filter-list a{padding:{{InPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-4'],
									(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],	
					'selector' => '{{PLUS_WRAP}} .tpgb-categories.hover-style-4 .tpgb-filter-list a{padding:{{InPadding}};}',
				],
			],
			'scopy' => true,
		],
		'FCMargin' => [
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
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-categories .tpgb-filter-list{margin:{{FCMargin}};}',
				],
			],
			'scopy' => true,
		],
		'FCNcr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a.all span.tpgb-category-count{color:{{FCNcr}};}',
				],
			],
			'scopy' => true,
		],
		'FCHBcr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-1'],
									(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .hover-style-1 .tpgb-filter-list a.active::after,{{PLUS_WRAP}} .tpgb-category-filter .hover-style-1 .tpgb-filter-list a:hover::after{background:{{FCHBcr}};}',
				],
			],
			'scopy' => true,
		],
		'FCHcr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter:not(.hover-style-2) .tpgb-filter-list a:hover,
					{{PLUS_WRAP}} .tpgb-category-filter:not(.hover-style-2) .tpgb-filter-list a:focus,
					{{PLUS_WRAP}} .tpgb-category-filter:not(.hover-style-2) .tpgb-filter-list a.active,
					{{PLUS_WRAP}} .tpgb-category-filter .hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)::before{color:{{FCHcr}};}',
				],
			],
			'scopy' => true,
		],
		'FCBgHvrs' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true],
									(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => ['style-2','style-4']]],
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.tpgb-category-list:hover span:not(.tpgb-category-count):before,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.tpgb-category-list.active span:not(.tpgb-category-count):before',

				],
			],
			'scopy' => true,
		],
		'FCHvrBre' => [
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
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true],
									(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-2']],
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a:hover span:not(.tpgb-category-count):before,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.active span:not(.tpgb-category-count):before{border-radius:{{FCHvrBre}};}',
				],
			],
			'scopy' => true,
		],
		'FcBoxhversd'=> [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true],
									(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-2']],
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a:hover span:not(.tpgb-category-count):before,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.active span:not(.tpgb-category-count):before',
				],
			],
			'scopy' => true,
		],
		'FCBgHs' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-2'],
									(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)',
				],
				(object) [
					'condition' => [(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-4'],
									(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-4 .tpgb-filter-list a:after',
				],
			],
			'scopy' => true,
		],
		'FCBgRs' => [
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
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true],
									(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-2']],
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count){border-radius:{{FCBgRs}};}',
				],
			],
			'scopy' => true,
		],			
		'FcBoxhsd' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true],
									(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-2']],
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)',
				],
			],
			'scopy' => true,
		],
		'FCCategCcr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a.all.active span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a.active span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a:hover span.tpgb-category-count{color:{{FCCategCcr}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true],
									(object) ['key' => 'CatFilterS', 'relation' => '==', 'value' => 'style-1']],
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.all span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.active span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a:hover span.tpgb-category-count{color:{{FCCategCcr}};}',
				],
			],
			'scopy' => true,
		],				
		'FCBgTp' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a.all span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a.active span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a:hover span.tpgb-category-count',
				],
				(object) [
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true],
									(object) ['key' => 'CatFilterS', 'relation' => '==', 'value' => 'style-1']],
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.all span.tpgb-category-count, {{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.active span.tpgb-category-count, {{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a:hover span.tpgb-category-count',
				],
			],
			'scopy' => true,
		],
		'FcBCrHs' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true],
									(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-4']],
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-4 .tpgb-filter-list a:before{border-top-color:{{FcBCrHs}};}',
				],
			],
			'scopy' => true,
		],	
		'FCBoxSd' => [
			'type' => 'object',
			'default' =>  (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.all span.tpgb-category-count',
				],
			],
			'scopy' => true,
		],
			
		'btnTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-feed-load-more .feed-load-more',
				],
			],
			'scopy' => true,
		],
		'btncolor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-feed-load-more .feed-load-more{color : {{btncolor}}; }',
				],
			],
			'scopy' => true,
		],
		'btnBgtype' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-feed-load-more .feed-load-more',
				],
			],
			'scopy' => true,
		],
		'btnBorder' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,	
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-feed-load-more .feed-load-more',
				],
			],
			'scopy' => true,
		],
		'btnBradius' => [
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
					'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-feed-load-more .feed-load-more{border-radius : {{btnBradius}} }',
				],
			],
			'scopy' => true,
		],
		'btnhvrcolor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-feed-load-more .feed-load-more:hover{color : {{btnhvrcolor}}; }',
				],
			],
			'scopy' => true,
		],
		'btnHvrBgtype' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-feed-load-more .feed-load-more:hover',
				],
			],
			'scopy' => true,
		],
		'btnhvrBorder' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,	
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-feed-load-more .feed-load-more:hover',
				],
			],
			'scopy' => true,
		],
		'btnBradius' => [
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
					'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-feed-load-more .feed-load-more{border-radius : {{btnBradius}} }',
				],
			],
			'scopy' => true,
		],
		'btnhvrBradius' => [
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
					'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-feed-load-more .feed-load-more:hover{border-radius : {{btnhvrBradius}} }',
				],
			],
			'scopy' => true,
		],
		'allTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-feed-load-more .tpgb-feed-loaded',
				],
				(object) [
					'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'lazy_load' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-feed-lazy-load .tpgb-feed-loaded',
				],
			],
			'scopy' => true,
		],
		'allcolor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-feed-load-more .tpgb-feed-loaded{color : {{allcolor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'lazy_load' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-feed-lazy-load .tpgb-feed-loaded{color : {{allcolor}}; }',
				],
			],
			'scopy' => true,
		],
		'spinSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'lazy_load']],
					'selector' => '{{PLUS_WRAP}} .tpgb-feed-lazy-load .tpgb-spin-ring div{ width: {{spinSize}}px; height:{{spinSize}}px; }',
				],
			],
			'scopy' => true,
		],
		'spinBSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'lazy_load']],
					'selector' => '{{PLUS_WRAP}} .tpgb-feed-lazy-load .tpgb-spin-ring div{ border-width: {{spinBSize}}px; }',
				],
			],
			'scopy' => true,
		],
		'spinColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'lazy_load']],
					'selector' => '{{PLUS_WRAP}} .tpgb-feed-lazy-load .tpgb-spin-ring div{ border-color: {{spinColor}} transparent transparent transparent ; }',
				],
			],
			'scopy' => true,
		],
	];
		
	$attributesOptions = array_merge($attributesOptions,$carousel_options,$plusButton_options, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption, $globalEqualHeightOptions);
	
	register_block_type( 'tpgb/tp-social-feed', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgbp_tp_social_feed_render_callback'
    ) );
}
add_action( 'init', 'tpgbp_tp_social_feed' );

function tpgbp_tp_social_feed_render_callback( $attributes, $content) {
	$SocialFeed = '';
	$block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$feed_id = (!empty($attributes['feed_id'])) ? $attributes['feed_id'] : uniqid("feed");
	$layout = (!empty($attributes['layout'])) ? $attributes['layout'] : 'grid';
	$style = (!empty($attributes['style'])) ? $attributes['style'] : 'style-1';
	$Rsocialfeed = (!empty($attributes['AllReapeter'])) ? $attributes['AllReapeter'] : [];
	$columns = (!empty($attributes['columns'])) ? $attributes['columns'] : 'tpgb-col-12';
	$Rowclass = ($layout!='carousel') ? 'tpgb-row' : '';
	$RefreshTime = !empty($attributes['TimeFrq']) ? $attributes['TimeFrq'] : '3600';
	$TimeFrq = array( 'TimeFrq' => $attributes['TimeFrq'] );
	$TotalPost = (!empty($attributes['TotalPost'])) ? $attributes['TotalPost'] : 1000;

	$showDots = (!empty($attributes['showDots'])) ? $attributes['showDots'] : [ 'md' => false ];
	$slideHoverDots = (!empty($attributes['slideHoverDots'])) ? $attributes['slideHoverDots'] : false;
	$showArrows = (!empty($attributes['showArrows'])) ? $attributes['showArrows'] : [ 'md' => false ];
	$outerArrows = (!empty($attributes['outerArrows'])) ? $attributes['outerArrows'] : false;
	$slideHoverArrows = (!empty($attributes['slideHoverArrows'])) ? $attributes['slideHoverArrows'] : false;
	
	$FeedId = (!empty($attributes['FeedId'])) ? preg_split("/\,/", $attributes['FeedId']) : [];
	$ShowTitle = !empty($attributes['ShowTitle']) ? $attributes['ShowTitle'] : false;
	$showFooterIn = (!empty($attributes['showFooterIn'])) ? true : false;
	$CategoryWF = (!empty($attributes['CategoryWF'])) ? $attributes['CategoryWF'] : '';
	$Categoryclass = (!empty($CategoryWF) ? 'tpgb-filter' : '' );
	
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
	
	$arrowsStyle = (!empty($attributes['arrowsStyle'])) ? $attributes['arrowsStyle'] : 'style-1';
	$arrowsPosition = (!empty($attributes['arrowsPosition'])) ? $attributes['arrowsPosition'] : 'top-right';
	$dotsStyle = (!empty($attributes['dotsStyle'])) ? $attributes['dotsStyle'] : 'style-1';
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

	$equalHeightAtt = Tpgbp_Pro_Blocks_Helper::global_equal_height( $attributes );
	$equalHclass = '';
	if(!empty($equalHeightAtt)){
		$equalHclass = ' tpgb-equal-height';
	}

	$list_layout='';
	if( $layout=='grid' || $layout=='masonry' ){
		$list_layout = 'tpgb-isotope';
	}else if( $layout =='carousel' ){
		$list_layout = 'tpgb-carousel splide';	
	}else{
		$list_layout = 'tpgb-isotope';
	}

	$desktop_class='';
	if( $layout !='carousel' && $columns ){
		$desktop_class .= 'tpgb-col-'.esc_attr($columns['xs']);
		$desktop_class .= ' tpgb-col-lg-'.esc_attr($columns['md']);
		$desktop_class .= ' tpgb-col-md-'.esc_attr($columns['sm']);
		$desktop_class .= ' tpgb-col-sm-'.esc_attr($columns['xs']);
	}

	$Sliderclass = '';
	if($slideHoverDots==true && ($showDots['md'] || $showDots['sm'] || $showDots['xs']) ){
		$Sliderclass .= ' hover-slider-dots';
	}
	if($outerArrows==true && ($showArrows['md'] || $showArrows['sm'] || $showArrows['xs']) ){
		$Sliderclass .= ' outer-slider-arrow';
	}
	if($slideHoverArrows==true && ($showArrows['md'] || $showArrows['sm'] || $showArrows['xs']) ){
		$Sliderclass .= ' hover-slider-arrow';
	}
	if( $layout =='carousel' && ( isset($showDots['md']) && !empty($showDots['md']) ) || ( isset($showDots['sm']) && !empty($showDots['sm']) ) || ( isset($showDots['xs']) && !empty($showDots['xs']) ) ){
		$Sliderclass .=' dots-'.esc_attr($dotsStyle);
	}

	$carousel_settings = '';
	$arrowCss = '';
	if($layout=='carousel'){
		$carousel_settings = Tp_Blocks_Helper::carousel_settings( $attributes );
		$carousel_settings = 'data-splide=\''.json_encode($carousel_settings).'\'' ;

		//Show Arrow Media Css
		$arrowCss = Tp_Blocks_Helper::tpgb_carousel_arrow_css( $showArrows , $block_id );

	}
	
	$fancybox_settings = "";
	if($PopupOption=='OnFancyBox'){
		$fancybox_settings = tpgbp_social_feed_fancybox($attributes);
		$fancybox_settings = json_encode($fancybox_settings);
	}
	

	$SocialFeed .= '<div id="'.esc_attr($block_id).'" class="tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' tpgb-social-feed tpgb-relative-block '.esc_attr($list_layout).' '.esc_attr($Categoryclass).' '.esc_attr($Sliderclass).' '.esc_attr($equalHclass).'" data-style="'.esc_attr($style).'" data-layout="'.esc_attr($layout).'" data-id="'.esc_attr($block_id).'" data-fid="'.esc_attr($feed_id).'" data-fancy-option=\''.$fancybox_settings.'\' data-scroll-normal=\''.esc_attr($NormalScroll).'\' '.$carousel_settings.' '.$equalHeightAtt.'>';

		if( $layout == 'carousel' &&  ( ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) )){
			if(isset($showArrows) && !empty($showArrows)){
				$SocialFeed .= Tp_Blocks_Helper::tpgb_carousel_arrow($arrowsStyle,$arrowsPosition);
			}
		}
		
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
					$AllData[] = tpgbp_FacebookFeed($social, $attributes);
				}else if($RFeed == 'Twitter'){
					$AllData[] = tpgbp_TwetterFeed($social ,$attributes);
				}else if($RFeed == 'Instagram'){
					$AllData[] = tpgbp_InstagramFeed($social, $attributes);
				}else if($RFeed == 'Vimeo'){
					$AllData[] = tpgbp_VimeoFeed($social, $attributes);
				}else if($RFeed == 'Youtube'){
					$AllData[] = tpgbp_YouTubeFeed($social, $attributes);
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
			
			if(!empty($CategoryWF) && $layout != 'carousel'){
				$FilterTotal = '';
				if($postLodop == 'load_more' || $postLodop == 'lazy_load'){
					$FilterTotal = $Postdisplay;
				}else{
					$FilterTotal = count($FinalData);
				}
				$SocialFeed .= tpgbp_SF_CategoryFilter($FilterTotal, $FinalData, $attributes );
			}
			
			if($layout != 'carousel' && ($postLodop == 'load_more' || $postLodop == 'lazy_load')){
				$totalFeed = (count($FinalData));
				$trans_store = get_transient("SF-LoadMore-".$feed_id);
					
				if( $trans_store === false){
					set_transient("SF-LoadMore-".$feed_id, $FinalData , $RefreshTime);
				}else if(!empty($trans_store) && is_array($trans_store) && count($trans_store)!=$totalFeed){
					set_transient("SF-LoadMore-".$feed_id, $FinalData , $RefreshTime);
				}
				
				$FinalData = array_slice($FinalData, 0, $Postdisplay);
				
				$postattr = [
					'load_class' => esc_attr($block_id),
					'feed_id'		=> esc_attr($feed_id),
					'layout' => esc_attr($layout),
					'style' => esc_attr($style),
					'desktop_column' => esc_attr($attributes['columns']['md']),
					'tablet_column' => esc_attr($attributes['columns']['sm']),
					'mobile_column' => esc_attr($attributes['columns']['xs']),
					'postview' => esc_attr((int)$postview),
					'display' => esc_attr($Postdisplay),
					'TextLimit' => esc_attr($txtLimt),
					'TextCount' => esc_attr($TextCount),
					'TextType' => esc_attr($TextType),
					'TextMore' => esc_attr($TextMore),
					'TextDots' => esc_attr($TextDots),
					'loadingtxt' => esc_attr($loadingtxt),
					'allposttext' => esc_attr($allposttext),
					'totalFeed' => esc_attr($totalFeed),
					'FancyStyle' => esc_attr($FancyStyle),
					'DescripBTM' => esc_attr($DescripBTM),
					'MediaFilter' => esc_attr($MediaFilter),
					'TotalPost' => esc_attr($TotalPost),
					'categorytext' => esc_attr($CategoryWF),
					'PopupOption' => esc_attr($PopupOption),
					'FilterStyle' => esc_attr($attributes['CatFilterS']),
					'tpgb_nonce' => wp_create_nonce("theplus-addons-block"),
				];
				$data_loadkey = Tpgbp_Pro_Blocks_Helper::tpgb_simple_decrypt( json_encode($postattr), 'ey' );

			}

			if(!empty($FinalData)){
				$SocialFeed .= '<div class="'.esc_attr($Rowclass).' post-loop-inner '.($layout == 'carousel' ? ' splide__track ' : '').' social-feed-'.esc_attr($style).'" >';
				if($layout =='carousel'){
					$SocialFeed .= '<div class="splide__list">';
				}
				foreach ($FinalData as $F_index => $AllVmData) {
					$uniqEach = uniqid();
					$PopupSylNum = "{$block_id}-{$F_index}-{$uniqEach}";
					$RKey = (!empty($AllVmData['RKey'])) ? $AllVmData['RKey'] : '';
					$PostId = (!empty($AllVmData['PostId'])) ? $AllVmData['PostId'] : '';
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
					$categoryTxt = (!empty($AllVmData['FilterCategory'])) ? $AllVmData['FilterCategory'] : '';
					$ErrorClass = (!empty($AllVmData['ErrorClass'])) ? $AllVmData['ErrorClass'] : '';

					$EmbedURL = (!empty($AllVmData['Embed'])) ? $AllVmData['Embed'] : '';
					$EmbedType = (!empty($AllVmData['EmbedType'])) ? $AllVmData['EmbedType'] : '';
			
					$category_filter = $loop_category = '';
					if( !empty($CategoryWF) && !empty($categoryTxt)  && $layout !='carousel' ){
						$loop_category = explode(',', $categoryTxt);
						foreach( $loop_category as $category ) {
							$category = preg_replace('/[^A-Za-z0-9-]+/', '-', $category);
							$category_filter .=' '.esc_attr($category).' ';
						}
					}
					
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
					
					if($selectFeed == 'Twitter'){
						$TwRT = (!empty($AllVmData['TWRetweet'])) ? $AllVmData['TWRetweet'] : 0;
						$TWLike = (!empty($AllVmData['TWLike'])) ? $AllVmData['TWLike'] : 0;
						
						$TwReplyURL = (!empty($AllVmData['TwReplyURL'])) ? $AllVmData['TwReplyURL'] : '';
						$TwRetweetURL = (!empty($AllVmData['TwRetweetURL'])) ? $AllVmData['TwRetweetURL'] : '';
						$TwlikeURL = (!empty($AllVmData['TwlikeURL'])) ? $AllVmData['TwlikeURL'] : '';
						$TwtweetURL = (!empty($AllVmData['TwtweetURL'])) ? $AllVmData['TwtweetURL'] : '';
					}
					if($selectFeed == 'Vimeo'){
						$share = (!empty($AllVmData['share'])) ? $AllVmData['share'] : 0;
						$likes = (!empty($AllVmData['likes'])) ? $AllVmData['likes'] : 0;
						$comment = (!empty($AllVmData['comment'])) ? $AllVmData['comment'] : 0;
					}
					if($selectFeed == 'Youtube'){
						$view = (!empty($AllVmData['view'])) ? $AllVmData['view'] : 0;
						$likes = (!empty($AllVmData['likes'])) ? $AllVmData['likes'] : 0;
						$comment = (!empty($AllVmData['comment'])) ? $AllVmData['comment'] : 0;
						$Dislike = (!empty($AllVmData['Dislike'])) ? $AllVmData['Dislike'] : 0;
					}
					$ImageURL=$videoURL="";
					if( ($Type == 'video' || $Type == 'photo') && $selectFeed != 'Instagram' ){
						$videoURL = $PostLink;
						$ImageURL = $PostImage;
					}
					$IGGP_Icon='';
					if($selectFeed == 'Instagram'){
						$IGGP_Type = !empty($AllVmData['IG_Type']) ? $AllVmData['IG_Type'] : 'Instagram_Basic';
						if($IGGP_Type == 'Instagram_Graph'){
							$IGGP_Icon = !empty($AllVmData['IGGP_Icon']) ? $AllVmData['IGGP_Icon'] : '';
							$likes = !empty($AllVmData['likes']) ? $AllVmData['likes']: 0;
							$comment = !empty($AllVmData['comment']) ? $AllVmData['comment'] : 0;
							$videoURL = $PostLink;
							$PostLink = !empty($AllVmData['IGGP_PostLink']) ? $AllVmData['IGGP_PostLink'] : '';
							$ImageURL = $PostImage;

							$IGGP_CAROUSEL = !empty($AllVmData['IGGP_CAROUSEL']) ? $AllVmData['IGGP_CAROUSEL'] : '';
							if( $Type == "CAROUSEL_ALBUM" && $FancyStyle == 'default' ){
								$FancyBoxJS = 'data-fancybox="IGGP-CAROUSEL-'.esc_attr($F_index).'-'.esc_attr($block_id).'-'.esc_attr($uniqEach).'"';
							}else{
								$FancyBoxJS = 'data-fancybox="'.esc_attr($block_id).'"';
							}
						}else if($IGGP_Type == 'Instagram_Basic'){
							$videoURL = $PostLink;
							$ImageURL = $PostImage;
						}
					}
					
					if(!empty($FbAlbum)){
						$PostLink = (!empty($PostLink[0]['link'])) ? $PostLink[0]['link'] : 0;
					}
					
					if( (!in_array($PostId,$FeedId) && $F_index < $TotalPost) && ( ($MediaFilter == 'default') || ($MediaFilter == 'ompost' && !empty($PostLink) && !empty($PostImage)) || ($MediaFilter == 'hmcontent' &&  empty($PostLink) && empty($PostImage) )) ){
						$SocialFeed .= '<div class="grid-item splide__slide '.esc_attr('feed-'.$selectFeed.' '.$desktop_class .' '.$RKey.' '.$category_filter).'" data-index="'.esc_attr($selectFeed).esc_attr($F_index).'" >';
							ob_start();
								include TPGBP_INCLUDES_URL. "social-feed/social-feed-{$style}.php"; 
								$SocialFeed .= ob_get_contents();
							ob_end_clean();
						$SocialFeed .= '</div>';
					}

				}
				if($layout =='carousel'){
					$SocialFeed .= '</div>';
				}
				$SocialFeed .= '</div>';
			}else{
				$SocialFeed .= '<div class="error-handal">'.esc_html__('All Social Feed','tpgbp').'</div>';
			}
			if( !empty($totalFeed) && $totalFeed > $Postdisplay ){
				if($postLodop == 'load_more' && $layout != 'carousel'){
					$SocialFeed .= '<div class="tpgb-feed-load-more">';
						$SocialFeed .= '<a class="feed-load-more" aria-label="'.esc_attr($loadbtnText).'" data-loadingtxt="'.esc_attr($loadingtxt).'" data-layout="'.esc_attr($layout).'"  data-loadclass="'.esc_attr($block_id).'" data-totalfeed="'.esc_attr($totalFeed).'" data-display="'.esc_attr($Postdisplay).'" data-loadview="'.esc_attr($postview).'" data-loadattr= \'' . $data_loadkey . '\'>';
							$SocialFeed .= $loadbtnText;
						$SocialFeed .= '</a>';
					$SocialFeed .= '</div>';
				}else if($postLodop == 'lazy_load' && $layout!='carousel'){
					$SocialFeed .= '<div class="tpgb-feed-lazy-load">';
						$SocialFeed .= '<a class="feed-lazy-load" aria-label="'.esc_attr($loadingtxt).'" data-loadingtxt="'.esc_attr($loadingtxt).'" data-lazylayout="'.esc_attr($layout).'" data-lazyclass="'.esc_attr($block_id).'" data-totalfeed="'.esc_attr($totalFeed).'" data-display="'.esc_attr($Postdisplay).'" data-lazyview="'.esc_attr($postview).'" data-lazyattr= \'' . $data_loadkey . '\'>';
							$SocialFeed .= '<div class="tpgb-spin-ring"><div></div><div></div><div></div></div>';
						$SocialFeed .= '</a>';
					$SocialFeed .= '</div>';
				}
			}
		}else{
			$SocialFeed .= '<div class="error-handal">'.esc_html__('All Social Feed','tpgbp').'</div>';
		}

	$SocialFeed .= '</div>';

	if($layout=='carousel' && $arrowCss != ''){
		$SocialFeed .= $arrowCss;
	}

    return $SocialFeed;
}

function tpgbp_FacebookFeed($social,$attr){
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
	$FbCategory = !empty($social['RCategory']) ? $social['RCategory'] : '';
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
			$FbAllData = tpgbp_api_call($url,$SSL_VER);
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
				$id = (!empty($FbData['id']) ? $FbData['id'] : '');
				$type = (!empty($FbData['type']) ? $FbData['type'] : '');
				$FbMessage = (!empty($FbData['message']) ? $FbData['message'] : '');
				$FbPicture = $FbSource = (!empty($FbData['full_picture']) ? $FbData['full_picture'] : '');
				$Created_time = (!empty($FbData['created_time'])) ? tpgbp_feed_Post_time($FbData['created_time']) : '';
				$FbReactions = (!empty($FbData['reactions']['summary']['total_count']) ? tpgbp_number_short($FbData['reactions']['summary']['total_count']) : 0);
				$FbComments = (!empty($FbData['comments']['summary']['total_count']) ? tpgbp_number_short($FbData['comments']['summary']['total_count']) : 0);
				$Fbshares = (!empty($FbData['shares']['count']) ? tpgbp_number_short($FbData['shares']['count']) : '');
				
				

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
						"FilterCategory"=> $FbCategory,
						"RKey" 			=> "tp-repeater-item-$FbKey",
					);
				}
			}		
		}else{
			$FbArr[] = tpgbp_SF_Error_handler($FbAllData, $FbKey, $FbCategory, $FbselectFeed, $FbIcon);
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
		$FbArr[] = tpgbp_SF_Error_handler($ErrorData, $FbKey, $FbCategory, $FbselectFeed, $FbIcon);
	}
	
	return $FbArr;
}

function tpgbp_TwetterFeed($social,$attr){
	$BaseURL = "https://api.twitter.com/1.1";
	$TwKey = (!empty($social['_key'])) ? $social['_key'] : '';
	$TwApi = (!empty($social['TwApi'])) ? $social['TwApi'] : '';
	$TwApiSecret = (!empty($social['TwApiSecret'])) ? $social['TwApiSecret'] : '';
	$TwAccesT = (!empty($social['TwAccesT'])) ? $social['TwAccesT'] : '';
	$TwAccesTS = (!empty($social['TwAccesTS'])) ? $social['TwAccesTS'] : '';
	$twcount = (!empty($social['MaxR'])) ? $social['MaxR'] * 5 : 6 * 5;
	$TwTime = (!empty($social['TimeFrq'])) ? $social['TimeFrq'] : '3600';
	$MediaFilter = !empty($attr['MediaFilter']) ? $attr['MediaFilter'] : 'default';
	$RCategory = !empty($social['RCategory']) ? $social['RCategory'] : '';
	$selectFeed = !empty($social['selectFeed']) ? $social['selectFeed'] : '';
	$TwIcon = 'fab fa-twitter social-logo-tw';
	$SSL_VER = $attr['CURLOPT_SSL_VERIFYPEER'];
	
	$url = '';
	$getfield = '';
	$TwArr = [];
	$TwResponce = [];

	if( !empty($TwApi) && !empty($TwApiSecret) && !empty($TwAccesT) && !empty($TwAccesTS) ){
		$TwUsername = (!empty($social['TwUsername'])) ? $social['TwUsername'] : '';
		$TwType = (!empty($social['TwfeedType'])) ? $social['TwfeedType'] : '';
		$TwSearch = (!empty($social['TwSearch'])) ? $social['TwSearch'] : '';
		$TwDmedia = (!empty($social['TwDmedia'])) ? $social['TwDmedia'] : false;
		$TwComRep = (!empty($attr['TwComRep'])) ? false : true;
		$TwRetweet = (!empty($social['TwRetweet'])) ? $social['TwRetweet'] : false;

		require_once(TPGBP_INCLUDES_URL.'social-feed/TwitterAPIExchange.php');

		$settings = array(
			'consumer_key' => $TwApi,
			'consumer_secret' => $TwApiSecret,
			'oauth_access_token' => $TwAccesT,
			'oauth_access_token_secret' => $TwAccesTS
		);

		if( $TwType == 'wptimline' ){
			$Twtimeline = (!empty($social['Twtimeline'])) ? $social['Twtimeline'] : '';
			if( $Twtimeline == 'Hometimline' ){
				$url = "{$BaseURL}/statuses/home_timeline.json";
				$getfield = "?screen_name={$TwUsername}&count={$twcount}&exclude_replies={$TwComRep}&include_entities={$TwDmedia}&tweet_mode=extended";
			}else if( $Twtimeline == 'mentionstimeline' ){
				$url = "{$BaseURL}/statuses/mentions_timeline.json";
				$getfield = "?count={$twcount}&include_entities={$TwDmedia}&tweet_mode=extended";
			}
		}else if( $TwType == 'userfeed' ){
			$url = "{$BaseURL}/statuses/user_timeline.json";
			$getfield = "?screen_name={$TwUsername}&count={$twcount}&include_entities={$TwDmedia}&include_rts={$TwRetweet}&exclude_replies={$TwComRep}&tweet_mode=extended";
		}else if( $TwType == 'twsearch' ){
			$TwSearch = (!empty($social['TwSearch'])) ? $social['TwSearch'] : 'twitter';
			$TwRtype = (!empty($social['TwRtype'])) ? $social['TwRtype'] : 'recent';

			$url = "{$BaseURL}/search/tweets.json";
			$getfield = "?q={$TwSearch}&result_type={$TwRtype}&count={$twcount}&include_entities={$TwDmedia}&tweet_mode=extended";
		}else if( $TwType == 'userlist' ){
			$Twlistsid = (!empty($social['Twlistsid'])) ? $social['Twlistsid'] : '99921778';
			$url = "{$BaseURL}/lists/statuses.json";
			$getfield = "?list_id={$Twlistsid}&count={$twcount}&include_rts={$TwRetweet}&include_entities={$TwDmedia}&tweet_mode=extended";
		}else if( $TwType == 'twcollection' ){
			$Twcollsid = (!empty($social['Twcollsid'])) ? $social['Twcollsid'] : '539487832448843776';
			$url = "{$BaseURL}/collections/entries.json";
			$getfield = "?id=custom-{$Twcollsid}&count={$twcount}&tweet_mode=extended";
		}else if( $TwType == 'userlikes' ){
			$url = "{$BaseURL}/favorites/list.json";
			$getfield = "?screen_name={$TwUsername}&count={$twcount}&include_entities={$TwDmedia}&tweet_mode=extended";
		}else if( $TwType == 'twtrends' ){
			$TwWOEID = (!empty($social['TwWOEID'])) ? $social['TwWOEID'] : '23424848';
			$url = "{$BaseURL}/trends/place.json";
			$getfield = "?id={$TwWOEID}";
		}else if( $TwType == 'twRTMe' ){
			$url = "{$BaseURL}/statuses/retweets_of_me.json";
			$getfield = "?count={$twcount}&include_entities={$TwDmedia}&include_user_entities=true&tweet_mode=extended";
		}else if( $TwType == 'Twcustom' ){
			$TwcustId = (!empty($social['TwcustId'])) ? $social['TwcustId'] : '';
			$url = "{$BaseURL}/statuses/lookup.json";
			$getfield = "?id={$TwcustId}&include_entities={$TwDmedia}&tweet_mode=extended";
		}
		$GetTwBaseUrl = get_transient("Tw-BaseUrl-$TwKey");
		$GetTwURL = get_transient("Tw-Url-$TwKey");
		$GetTwTime = get_transient("Tw-Time-$TwKey");
		if( ($GetTwURL != $getfield) || ($GetTwBaseUrl != $url) || ($TwTime != $GetTwTime) ){
			
				$requestMethod = 'GET';		
				$twitter = new TwitterAPIExchange($settings);
				$TwResponse = $twitter->setGetfield($getfield)->buildOauth( $url, $requestMethod )->performRequest();
				$TwResponce = json_decode($TwResponse,true);

				set_transient("Tw-BaseUrl-$TwKey", $url, $TwTime);
				set_transient("Tw-Url-$TwKey", $getfield, $TwTime);
				set_transient("Tw-Time-$TwKey", $TwTime, $TwTime);
				set_transient("Data-tw-$TwKey", $TwResponce, $TwTime);
		}else{
			$TwResponce = get_transient("Data-tw-$TwKey");
		}
	}
	$Twcode='';
	if(!empty($TwResponce['errors'])){
		$Twcode = 400;
	}
	if(!empty($TwResponce && $TwType != 'twtrends' && $Twcode != 400 )){
		
		if( $TwType == 'twsearch' ){
			$TwResponce = (!empty($TwResponce['statuses'])) ? $TwResponce['statuses'] : [];
		}
		if( $TwType == 'twcollection' ){
			$TwColluser = (!empty($TwResponce['objects']['users'])) ? $TwResponce['objects']['users'] : [];
			$TwResponce = (!empty($TwResponce['objects']['tweets'])) ? $TwResponce['objects']['tweets'] : [];

			
		}
		$CountFiler = 0;
		foreach ($TwResponce as $index => $TwData) {
			if( $TwType == 'twcollection' ){
				$index = $CountFiler;
			}
			$twid = (!empty($TwData['id'])) ? $TwData['id'] : '';
			$retweet_count = (!empty($TwData['retweet_count'])) ? tpgbp_number_short($TwData['retweet_count']) : 0;
			$favorite_count = (!empty($TwData['favorite_count'])) ? tpgbp_number_short($TwData['favorite_count']) : 0;			

			$Full_Text = (!empty($TwData['full_text'])) ? $TwData['full_text'] : '';
			$TwUsername = (!empty($TwData['user']['name'])) ? $TwData['user']['name'] : '';
			$twname = (!empty($TwData['user']['screen_name'])) ? $TwData['user']['screen_name'] : '';
			$TwProfile = (!empty($TwData['user']['profile_image_url'])) ? $TwData['user']['profile_image_url'] : '';
			$Type='';
			if(!empty($TwData['extended_entities']['media'][0]['media_url']) && ((!empty($social['TwDmedia']) && $social['TwDmedia']=='yes') || (!empty($attr['layout']) && $attr['layout']=='carousel'))){
				$TwImg = !empty($TwData['extended_entities']['media'][0]['media_url']) ? $TwData['extended_entities']['media'][0]['media_url'] : '';
				$Twlink = !empty($TwData['extended_entities']['media'][0]['media_url']) ? $TwData['extended_entities']['media'][0]['media_url'] : '';
				$Type = !empty($TwData['extended_entities']['media'][0]['type']) ? $TwData['extended_entities']['media'][0]['type'] : '';
			}else if(!empty($TwData['entities']) && !empty($TwData['entities']['media'])){
				$TwImg = !empty($TwData['entities']['media'][0]['media_url']) ? $TwData['entities']['media'][0]['media_url'] : '';
				$Twlink = !empty($TwData['entities']['media'][0]['media_url']) ? $TwData['entities']['media'][0]['media_url'] : '';
				$Type = !empty($TwData['entities']['media'][0]['type']) ? $TwData['entities']['media'][0]['type'] : '';
			}
			
			if( $TwType == 'twcollection' ){
				$twCuser = (!empty($TwData['user'])) ? $TwData['user']['id'] : '';
				
				foreach ($TwColluser as $data) {
					$twUid = (!empty($data['id'])) ? $data['id'] : '';
					if( $twCuser == $twUid ) {
						$TwUsername = (!empty($data['name'])) ? $data['name'] : '';
						$Fbname = (!empty($data['screen_name'])) ? $data['screen_name'] : '';
						$TwProfile = (!empty($data['profile_image_url'])) ? $data['profile_image_url'] : '';
					}
				}
			}
			
			$TwFilter = !empty($social['MaxR']) ? $social['MaxR'] : 6; 
			
			if( ($MediaFilter == 'default' && $TwFilter > $index) || ($MediaFilter == 'ompost' && !empty($Twlink) && $CountFiler <= $TwFilter ) || ($MediaFilter == 'hmcontent' && empty($Twlink) && $CountFiler <= $TwFilter) ){
				
					$TwArr[] = array(
						"Feed_Index"	=> $index,
						"PostId"		=> $twid,
						"Description"	=> $Full_Text,
						"Type" 			=> $Type,
						"PostLink" 		=> !empty($Twlink) ? $Twlink : '',
						"CreatedTime" 	=> !empty($TwData['created_at']) ? tpgbp_feed_Post_time($TwData['created_at']) : '',
						"PostImage" 	=> !empty($TwImg) ? $TwImg : '',
						"UserName" 		=> $TwUsername,
						"UserImage" 	=> $TwProfile,
						"UserLink" 		=> "https://twitter.com/{$twname}",
						"TWRetweet"		=> $retweet_count,
						"TWLike"		=> $favorite_count,
						"TwReplyURL" 	=> "https://twitter.com/intent/tweet?in_reply_to={$twid}",
						"TwRetweetURL" 	=> "https://twitter.com/intent/retweet?tweet_id={$twid}",
						"TwlikeURL" 	=> "https://twitter.com/intent/like?tweet_id={$twid}",
						"TwtweetURL" 	=> "https://twitter.com/{$twname}/status/{$twid}",
						"socialIcon" 	=> $TwIcon,
						"selectFeed"    => $selectFeed,
						"FilterCategory"=> $RCategory,
						"RKey" 			=> "tp-repeater-item-$TwKey",
					);
				$CountFiler++;
			}

			
			
		}
	}else if(!empty($TwResponce && $TwType == 'twtrends' && $Twcode != 400 )){
		$TwResTrends = (!empty($TwResponce[0]['trends'])) ? $TwResponce[0]['trends'] : [];
		foreach ($TwResTrends as $index => $trends) {
			$TrendName = (!empty($trends['name'])) ? $trends['name'] : '';
			$TrendURL = (!empty($trends['url'])) ? $trends['url'] : '';
			
			$TwArr[] = array("Feed_Index" => $index,
								"UserName" => $TrendName,
								"UserLink"	=> $TrendURL,
								"socialIcon" 	=> 'fab fa-twitter social-logo-tw',
							);
		}
	}else{
		$Msg = "";
		if(empty($TwApi)){
			$Msg .= "Empty CONSUMER KEY </br>";
		}
		if(empty($TwApiSecret)){
			$Msg .= "Empty CONSUMER SECRET </br>";
		}
		if(empty($TwAccesT)){
			$Msg .= "Empty ACCESS TOKEN </br>";
		}
		if(empty($TwAccesTS)){
			$Msg .= "Empty ACCESS TOKEN SECRET </br>";
		}
		$Error = !empty($TwResponce['errors']) ? $TwResponce['errors'][0] : [];
		$ErrorData['error']['HTTP_CODE'] = !empty($Error['code']) ? $Error['code'] : 400;
		$ErrorData['error']['message'] = !empty($Error['message']) ? $Error['message'] : $Msg;

		$TwArr[] = tpgbp_SF_Error_handler($ErrorData, $TwKey, $RCategory, $selectFeed, $TwIcon);
	}

	return $TwArr;
}

function tpgbp_InstagramFeed($social, $attr){
	$IGKey = (!empty($social['_key'])) ? $social['_key'] : '';
	$IGAcT = (!empty($social['RAToken'])) ? $social['RAToken'] : '';
	$IGcount = (!empty($social['MaxR'])) ? $social['MaxR'] : 6;
	$Profile = (!empty($social['IGImgPic']) && !empty($social['IGImgPic']['url']) ) ? $social['IGImgPic']['url'] : '';
	$TimeFrq = (!empty($social['TimeFrq'])) ? $social['TimeFrq'] : '3600';
	$IGType = !empty($social['InstagramType']) ? $social['InstagramType'] : 'Instagram_Basic';
	$HashtagType = !empty($social['IG_hashtagType']) ? $social['IG_hashtagType'] : 'top_media';
	$RCategory = !empty($social['RCategory']) ? $social['RCategory'] : '';
	$selectFeed = !empty($social['selectFeed']) ? $social['selectFeed'] : '';
	$Default_Img = TPGB_ASSETS_URL.'assets/images/tpgb-placeholder-grid.jpg';
	
	$SSL_VER = $attr['CURLOPT_SSL_VERIFYPEER'];
	$IGIcon = 'fab fa-instagram social-logo-ig';
	$IGArr = [];
	if($IGType == "Instagram_Basic"){
		$IGAPI = "https://graph.instagram.com/me/?fields=account_type,id,media_count,username,media.limit($IGcount){id,caption,permalink,thumbnail_url,timestamp,username,media_type,media_url}&access_token={$IGAcT}";
		$GetURL = get_transient("IG-Url-$IGKey");
		$GetTime = get_transient("IG-Time-$IGKey");
		$GetProfile = get_transient("IG-Profile-$IGKey");
		
		$IGData = '';
		if( ($GetURL != $IGAPI) || ($GetProfile != $Profile) || ($GetTime != $TimeFrq ) ){
			$IGData = tpgbp_api_call($IGAPI,$SSL_VER);
			set_transient("IG-Url-$IGKey", $IGAPI, $TimeFrq);
			set_transient("Data-IG-$IGKey", $IGData, $TimeFrq);
			set_transient("IG-Profile-$IGKey", $Profile, $TimeFrq);
			set_transient("IG-Time-$IGKey", $TimeFrq, $TimeFrq);
		}else{
			$IGData = get_transient("Data-IG-$IGKey");
		}
		$IGStatus = (!empty($IGData['HTTP_CODE'])) ? $IGData['HTTP_CODE'] : 400;
		if( $IGStatus == 200 ){
			$posts = (!empty($IGData['media']) && !empty($IGData['media']['data']) ) ? $IGData['media']['data'] : [];
			foreach ($posts as $index => $IGPost) {
				$media_type = (!empty($IGPost['media_type'])) ? $IGPost['media_type'] : '';
				if($media_type == 'IMAGE'){
					$type = 'photo';
				}
				
				$PostImage='';
				if( !empty($IGPost['media_url']) && $IGPost['media_type'] == 'VIDEO' ) {	
					$PostImage = !empty($IGPost['thumbnail_url']) ? $IGPost['thumbnail_url'] : $Default_Img;
				}else if(!empty($IGPost['media_url'])){
					$PostImage = $IGPost['media_url'];
				}
				
				$IGArr[] = array(
					"Feed_Index"	=> $index,
					"PostId"		=> (!empty($IGPost['id'])) ? $IGPost['id'] : '',
					"Massage" 		=> '',
					"Description"	=> (!empty($IGPost['caption'])) ? $IGPost['caption'] : '',
					"Type" 			=> 'video',
					"PostLink" 		=> (!empty($IGPost['media_url'])) ? $IGPost['media_url'] : '',
					"CreatedTime" 	=> (!empty($IGPost['timestamp'])) ? tpgbp_feed_Post_time($IGPost['timestamp']) : '',
					"PostImage" 	=> $PostImage,
					"UserName" 		=> (!empty($IGData['username'])) ? $IGData['username'] : '',
					"UserImage" 	=> $Profile,
					"UserLink" 		=> (!empty($IGPost['permalink'])) ? $IGPost['permalink'] : '',
					"IG_Type"		=> $IGType,
					"socialIcon" 	=> $IGIcon,
					"selectFeed"    => $selectFeed,
					"FilterCategory"=> $RCategory,
					"RKey" 			=> "tp-repeater-item-$IGKey",
				);
				
			}
		}else{
			if(empty($IGAcT)){
				$IGData['error']['message'] = 'Enter Access Token';
			}
			$IGArr[] = tpgbp_SF_Error_handler($IGData, $IGKey, $RCategory, $selectFeed, $IGIcon);
		}
		
	}else if($IGType == "Instagram_Graph"){
		$BashURL = "https://graph.facebook.com/v11.0";
		$IGPageId = !empty($social['IGPageId']) ? $social['IGPageId'] : '';
		$IGFeedType = !empty($social['IG_FeedTypeGp']) ? $social['IG_FeedTypeGp'] : 'IGUserdata';
		$IGGPcount = ($IGcount > 49) ? $IGcount : $IGcount * 6;
		
		$UserID_API = "{$BashURL}/{$IGPageId}?fields=instagram_business_account{id,profile_picture_url,username,ig_id,media_count}&access_token={$IGAcT}";
		$GetURL = get_transient("IG-GP-Url-$IGKey");
		$GetTime = get_transient("IG-GP-Time-$IGKey");
		$UserID_Res = [];
		
		if( ($GetURL != $UserID_API) || ($GetTime != $TimeFrq) ){
			$UserID_Res = tpgbp_api_call($UserID_API,$SSL_VER);
			set_transient("IG-GP-Url-$IGKey", $UserID_API, $TimeFrq);
			set_transient("IG-GP-Time-$IGKey", $TimeFrq, $TimeFrq);
			set_transient("IG-GP-Data-$IGKey", $UserID_Res, $TimeFrq);
		}else{
			$UserID_Res = get_transient("IG-GP-Data-$IGKey");
		}
		$UserID_CODE = !empty($UserID_Res['HTTP_CODE']) ? $UserID_Res['HTTP_CODE'] : 400;
		
		if($UserID_CODE == 200){
			$GET_UserID = !empty($UserID_Res['instagram_business_account']) ? $UserID_Res['instagram_business_account']['id'] : '';
			$GET_UserName = !empty($UserID_Res['instagram_business_account']['username']) ? $UserID_Res['instagram_business_account']['username'] : '';
			$GET_Profile = !empty($UserID_Res['instagram_business_account']['profile_picture_url']) ? $UserID_Res['instagram_business_account']['profile_picture_url'] : $Default_Img;
			$IGGP_CountFiler = 0;
			
			if($IGFeedType == 'IGUserdata'){
				$IGUserName = !empty($social['IGUserName_GP']) ? $social['IGUserName_GP'] : $GET_UserName;
				$UserPost_API = "{$BashURL}/{$GET_UserID}?fields=business_discovery.username({$IGUserName}){username,profile_picture_url,followers_count,media_count,media.limit({$IGGPcount}){permalink,media_type,media_url,like_count,comments_count,timestamp,caption,id,media_product_type,children{media_url,permalink,media_type}}}&access_token={$IGAcT}";
				
				$UserPost_Databash = get_transient("IG-GP-UserFeed-Url-$IGKey");
				$UserPost_Res=[];
				if( $UserPost_Databash != $UserPost_API || $GetTime != $TimeFrq ){
					$UserPost_Res = tpgbp_api_call($UserPost_API,$SSL_VER);
					set_transient("IG-GP-UserFeed-Url-$IGKey", $UserPost_API, $TimeFrq);
					set_transient("IG-GP-UserFeed-Data-$IGKey", $UserPost_Res, $TimeFrq);
				}else{
					$UserPost_Res = get_transient("IG-GP-UserFeed-Data-$IGKey");
				}
				$UserPost_CODE = !empty($UserPost_Res['HTTP_CODE']) ? $UserPost_Res['HTTP_CODE'] : 400;
				
				if($UserPost_CODE == 200){
					$GET_Profile = !empty($UserPost_Res['business_discovery']['profile_picture_url']) ? $UserPost_Res['business_discovery']['profile_picture_url'] : $GET_Profile;
						$BD = !empty($UserPost_Res['business_discovery']['media']) ? $UserPost_Res['business_discovery']['media']['data'] : [];
						foreach ($BD as $index => $IGGA) {
							$Permalink = !empty($IGGA['permalink']) ? $IGGA['permalink'] : '';
							
							$PostImage='';
							if( !empty($IGGA['media_url']) && $IGGA['media_type'] == 'VIDEO' ) {	
								$PostImage = TPGB_URL.'aseets/images/tpgb-placeholder-grid.jpg';
							}else if(!empty($IGGA['media_url'])){
								$PostImage = $IGGA['media_url'];
							}

							$IGGP_Icon="";
							$Media_type = !empty($IGGA['media_type']) ? $IGGA['media_type'] : '';
							
							if($Media_type == 'IMAGE'){
							}else if($Media_type == 'VIDEO'){
								$IGGP_Icon = '<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="video" class="svg-inline--fa fa-video fa-w-18 IGGP_video" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M525.6 410.2L416 334.7V177.3l109.6-75.6c21.3-14.6 50.4.4 50.4 25.8v256.9c0 25.5-29.2 40.4-50.4 25.8z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M0 400.2V111.8A47.8 47.8 0 0 1 47.8 64h288.4a47.8 47.8 0 0 1 47.8 47.8v288.4a47.8 47.8 0 0 1-47.8 47.8H47.8A47.8 47.8 0 0 1 0 400.2z"></path></g></svg>';
							}else if( $Media_type == 'CAROUSEL_ALBUM' ){
								$IGGP_Icon = '<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="clone" class="svg-inline--fa fa-clone fa-w-16 IGGP_Multiple" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M48 512a48 48 0 0 1-48-48V176a48 48 0 0 1 48-48h48v208a80.09 80.09 0 0 0 80 80h208v48a48 48 0 0 1-48 48H48z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M512 48v288a48 48 0 0 1-48 48H176a48 48 0 0 1-48-48V48a48 48 0 0 1 48-48h288a48 48 0 0 1 48 48z"></path></g></svg>';
							}

							$CAROUSEL_ALBUM = !empty($IGGA['children']) ? $IGGA['children']['data'] : [];
							$IGGP_CAROUSEL_ALBUM=[];
							foreach ($CAROUSEL_ALBUM as $key => $IGGP){
								$IGGP_MediaType = !empty($IGGP['media_type']) ? $IGGP['media_type'] : 'IMAGE'; 
                                $IGGP_MediaURl = !empty($IGGP['media_url']) ? $IGGP['media_url'] : '';

								if($key == 0 && $IGGP_MediaType == 'VIDEO'){
									foreach ($CAROUSEL_ALBUM as $thumb_i => $IGGP_Thumb){
										$IGGP_ThumbImg = !empty($IGGP_Thumb['media_type']) ? $IGGP_Thumb['media_type'] : 'IMAGE'; 
										if($IGGP_ThumbImg == 'IMAGE'){
											$PostImage = !empty($IGGP_Thumb['media_url']) ? $IGGP_Thumb['media_url'] : '';
											break;
										}
									}
								}
								if($IGGP_MediaType == 'IMAGE' || $IGGP_MediaType == 'VIDEO'){
									$IGGP_CAROUSEL_ALBUM[] = array(
										"IGGPCAR_Index" => $index,
										"IGGPImg_Type" => $IGGP_MediaType,
										"IGGPURL_Media" => $IGGP_MediaURl,
									);
								}
							}

							if( $Media_type != 'VIDEO' && $IGGP_CountFiler < $IGcount ){
								$IGArr[] = array(
									"Feed_Index"	=> $index,
									"PostId"		=> !empty($IGGA['id']) ? $IGGA['id'] : '',
									"Massage" 		=> '',
									"Description"	=> !empty($IGGA['caption']) ? $IGGA['caption'] : '',
									"Type" 			=> $Media_type,
									"PostLink" 		=> !empty($IGGA['media_url']) ? $IGGA['media_url'] : '',
									"CreatedTime" 	=> !empty($IGGA['timestamp']) ? tpgbp_feed_Post_time($IGGA['timestamp']) : '',
									"PostImage" 	=> $PostImage,
									"UserName" 		=> $IGUserName,
									"UserImage" 	=> !empty($GET_Profile) ? $GET_Profile : $Default_Img,
									"UserLink" 		=> "https://www.instagram.com/{$IGUserName}",
									"comment" 		=> !empty($IGGA['comments_count']) ?  tpgbp_number_short($IGGA['comments_count']) : 0,
									"likes" 		=> !empty($IGGA['like_count']) ? tpgbp_number_short($IGGA['like_count']) : 0,
									"IGGP_PostLink" => $Permalink,
									"IG_Type"		=> $IGType,
									"IGGP_Icon"		=> $IGGP_Icon,
									"IGGP_CAROUSEL" => $IGGP_CAROUSEL_ALBUM,
									"socialIcon" 	=> $IGIcon,
									"selectFeed"    => $selectFeed,
									"FilterCategory"=> $RCategory,
									"RKey" 			=> "tp-repeater-item-$IGKey",
								);
								$IGGP_CountFiler++;
							}
						}
				}else{
					$IGArr[] = tpgbp_SF_Error_handler($UserPost_Res, $IGKey, $RCategory, $selectFeed, $IGIcon);
				}
			}else if($IGFeedType == "IGHashtag"){
				$HashtagName = !empty($social['IGHashtagName_GP']) ? $social['IGHashtagName_GP'] : 'words';

				$HashtagID_API = "{$BashURL}/ig_hashtag_search?user_id={$GET_UserID}&q={$HashtagName}&access_token={$IGAcT}";
				$Hashtag_Databash = get_transient("IG-GP-HashtagID-Url-$IGKey");
				$Hashtag_Res = [];
				if( $Hashtag_Databash != $HashtagID_API || $GetTime != $TimeFrq ){
					$Hashtag_Res = tpgbp_api_call($HashtagID_API,$SSL_VER);
					set_transient("IG-GP-HashtagID-Url-$IGKey", $HashtagID_API, $TimeFrq);
					set_transient("IG-GP-HashtagID-data-$IGKey", $Hashtag_Res, $TimeFrq);
				}else{
					$Hashtag_Res = get_transient("IG-GP-HashtagID-data-$IGKey");
				}

				$Hashtag_CODE = !empty($Hashtag_Res['HTTP_CODE']) ? $Hashtag_Res['HTTP_CODE'] : 400;
				if($Hashtag_CODE == 200){
					$Hashtag_GetID = !empty($Hashtag_Res['data'][0]['id']) ? $Hashtag_Res['data'][0]['id'] : '';

					$Hashtag_Data = "{$BashURL}/{$Hashtag_GetID}/{$HashtagType}?user_id={$GET_UserID}&fields=id,media_type,media_url,comments_count,like_count,caption,permalink,timestamp,children{media_url,permalink,media_type}&limit=50&access_token={$IGAcT}";
					$Hashtag_Data_Databash = get_transient("IG-GP-HashtagData-Url-$IGKey");
					$Hashtag_Data_Res = [];
					if( $Hashtag_Data_Databash != $Hashtag_Data || $GetTime != $TimeFrq ){
						$Hashtag_Data_Res = tpgbp_api_call($Hashtag_Data,$SSL_VER);
						set_transient("IG-GP-HashtagData-Url-$IGKey", $Hashtag_Data, $TimeFrq);
						set_transient("IG-GP-Hashtag-Data-$IGKey", $Hashtag_Data_Res, $TimeFrq);
					}else{
						$Hashtag_Data_Res = get_transient("IG-GP-Hashtag-Data-$IGKey");
					}

					$Hashtag_Data_CODE = !empty($Hashtag_Data_Res['HTTP_CODE']) ? $Hashtag_Data_Res['HTTP_CODE'] : 400;
					if($Hashtag_Data_CODE == 200){
						
						$HashtagData = !empty($Hashtag_Data_Res['data']) ? $Hashtag_Data_Res['data'] : [];
						foreach ($HashtagData as $index => $IGHash) {
							$media_url = !empty($IGHash['media_url']) ? $IGHash['media_url'] : '';
							$permalink = !empty($IGHash['permalink']) ? $IGHash['permalink'] : '';

							$IGGP_Icon=$PostImage="";
							$Media_type = !empty($IGHash['media_type']) ? $IGHash['media_type'] : '';
							if($Media_type == 'IMAGE'){
								$PostImage = $media_url;
							}else if($Media_type == 'VIDEO'){
								$IGGP_Icon = '<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="video" class="svg-inline--fa fa-video fa-w-18 IGGP_video" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M525.6 410.2L416 334.7V177.3l109.6-75.6c21.3-14.6 50.4.4 50.4 25.8v256.9c0 25.5-29.2 40.4-50.4 25.8z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M0 400.2V111.8A47.8 47.8 0 0 1 47.8 64h288.4a47.8 47.8 0 0 1 47.8 47.8v288.4a47.8 47.8 0 0 1-47.8 47.8H47.8A47.8 47.8 0 0 1 0 400.2z"></path></g></svg>';
								$PostImage = $media_url;
							}else if( $Media_type == 'CAROUSEL_ALBUM' ){
								$IGGP_Icon = '<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="clone" class="svg-inline--fa fa-clone fa-w-16 IGGP_Multiple" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M48 512a48 48 0 0 1-48-48V176a48 48 0 0 1 48-48h48v208a80.09 80.09 0 0 0 80 80h208v48a48 48 0 0 1-48 48H48z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M512 48v288a48 48 0 0 1-48 48H176a48 48 0 0 1-48-48V48a48 48 0 0 1 48-48h288a48 48 0 0 1 48 48z"></path></g></svg>';
								$PostImage = !empty($IGHash['children']['data'][0]['media_url']) ? $IGHash['children']['data'][0]['media_url'] : '';
							}

							$CAROUSEL_ALBUM = !empty($IGHash['children']) ? $IGHash['children']['data'] : [];
							$IGGP_CAROUSEL_ALBUM=[];
							
							foreach ($CAROUSEL_ALBUM as $key => $IGGP){
								$IGGP_MediaType = !empty($IGGP['media_type']) ? $IGGP['media_type'] : 'IMAGE'; 
								$IGGP_MediaURl = !empty($IGGP['media_url']) ? $IGGP['media_url'] : '';

								if($key == 0 && $IGGP_MediaType == 'VIDEO'){
									foreach ($CAROUSEL_ALBUM as $thumb_i => $IGGP_Thumb){
										$IGGP_ThumbImg = !empty($IGGP_Thumb['media_type']) ? $IGGP_Thumb['media_type'] : 'IMAGE'; 
										if($IGGP_ThumbImg == 'IMAGE'){
											$PostImage = !empty($IGGP_Thumb['media_url']) ? $IGGP_Thumb['media_url'] : '';
											break;
										}
									}
								}
								
								if($IGGP_MediaType == 'IMAGE' || $IGGP_MediaType == 'VIDEO'){
									$IGGP_CAROUSEL_ALBUM[] = array(
										"IGGPCAR_Index" => $index,
										"IGGPImg_Type" => $IGGP_MediaType,
										"IGGPURL_Media" => $IGGP_MediaURl,
									);
								}
							}

							if( $Media_type != 'VIDEO' && $IGGP_CountFiler < $IGcount ){
								$IGArr[] = array(
									"Feed_Index"	=> $index,
									"PostId"		=> !empty($IGHash['id']) ? $IGHash['id'] : '',
									"Massage" 		=> '',
									"Description"	=> !empty($IGHash['caption']) ? $IGHash['caption'] : '',
									"Type" 			=> $Media_type,
									"PostLink" 		=> $media_url,
									"PostImage" 	=> $PostImage,
									"CreatedTime" 	=> !empty($IGHash['timestamp']) ? tpgbp_feed_Post_time($IGHash['timestamp']) : '',
									"UserLink" 		=> $permalink,
									"comment" 		=> !empty($IGHash['comments_count']) ?  tpgbp_number_short($IGHash['comments_count']) : 0,
									"likes" 		=> !empty($IGHash['like_count']) ? tpgbp_number_short($IGHash['like_count']) : 0,
									"IG_Type"		=> $IGType,
									"IGGP_Icon"		=> $IGGP_Icon,
									"IGGP_CAROUSEL" => $IGGP_CAROUSEL_ALBUM,
									"IGGP_PostLink" => $permalink,
									"socialIcon" 	=> $IGIcon,
									"selectFeed"    => $selectFeed,
									"FilterCategory"=> $RCategory,
									"RKey" 			=> "tp-repeater-item-$IGKey",
								);
								$IGGP_CountFiler++;
							}
						}
					}else{
						$IGArr[] = tpgbp_SF_Error_handler($Hashtag_Data_Res, $IGKey, $RCategory, $selectFeed);
					}
				}else{
					$IGArr[] = tpgbp_SF_Error_handler($Hashtag_Res, $IGKey, $RCategory, $selectFeed, $IGIcon);
				}
			}else if($IGFeedType == "IGTag"){
				$Tag_API = "{$BashURL}/{$GET_UserID}/tags?fields=id,username,media_type,media_url,like_count,caption,timestamp,permalink,comments_count,media_product_type,children{media_url,permalink,media_type}&limit={$IGGPcount}&access_token={$IGAcT}";
				$Tag_Databash = get_transient("IG-GP-Tag-Url-$IGKey");
				$Tag_Res=[];
				if( $Tag_Databash != $Tag_API || $GetTime != $TimeFrq ){
					$Tag_Res = tpgbp_api_call($Tag_API,$SSL_VER);
					set_transient("IG-GP-Tag-Url-$IGKey", $Tag_API, $TimeFrq);
					set_transient("IG-GP-Tag-Data-$IGKey", $Tag_Res, $TimeFrq);
				}else{
					$Tag_Res = get_transient("IG-GP-Tag-Data-$IGKey");
				}

				$Tag_CODE = !empty($Tag_Res['HTTP_CODE']) ? $Tag_Res['HTTP_CODE'] : 400;
				$Tag_Data = !empty($Tag_Res['data']) ? $Tag_Res['data'] : [];
				if( $Tag_CODE == 200 && !empty($Tag_Data) ){
					foreach ($Tag_Data as $index => $Tag) {
						$CAROUSEL_ALBUM = !empty($Tag['children']) ? $Tag['children']['data'] : [];
						$Permalink = !empty($Tag['permalink']) ? $Tag['permalink'] : '';
						$Tag_Username = !empty($Tag['username']) ? $Tag['username'] : '';

						$IGGP_Icon="";
						$Media_type = !empty($Tag['media_type']) ? $Tag['media_type'] : '';
						if($Media_type == 'IMAGE'){
						}else if($Media_type == 'VIDEO'){
							$IGGP_Icon = '<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="video" class="svg-inline--fa fa-video fa-w-18 IGGP_video" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M525.6 410.2L416 334.7V177.3l109.6-75.6c21.3-14.6 50.4.4 50.4 25.8v256.9c0 25.5-29.2 40.4-50.4 25.8z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M0 400.2V111.8A47.8 47.8 0 0 1 47.8 64h288.4a47.8 47.8 0 0 1 47.8 47.8v288.4a47.8 47.8 0 0 1-47.8 47.8H47.8A47.8 47.8 0 0 1 0 400.2z"></path></g></svg>';
						}else if( $Media_type == 'CAROUSEL_ALBUM' ){
							$IGGP_Icon = '<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="clone" class="svg-inline--fa fa-clone fa-w-16 IGGP_Multiple" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M48 512a48 48 0 0 1-48-48V176a48 48 0 0 1 48-48h48v208a80.09 80.09 0 0 0 80 80h208v48a48 48 0 0 1-48 48H48z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M512 48v288a48 48 0 0 1-48 48H176a48 48 0 0 1-48-48V48a48 48 0 0 1 48-48h288a48 48 0 0 1 48 48z"></path></g></svg>';
						}

						$CAROUSEL_ALBUM = !empty($Tag['children']) ? $Tag['children']['data'] : [];
						$IGGP_CAROUSEL_ALBUM=[];
						foreach ($CAROUSEL_ALBUM as $key => $IGGP){
							$IGGP_MediaType = !empty($IGGP['media_type']) ? $IGGP['media_type'] : 'IMAGE'; 
							$IGGP_MediaURl = !empty($IGGP['media_url']) ? $IGGP['media_url'] : '';

							if($key == 0 && $IGGP_MediaType == 'VIDEO'){
								foreach ($CAROUSEL_ALBUM as $thumb_i => $IGGP_Thumb){
									$IGGP_ThumbImg = !empty($IGGP_Thumb['media_type']) ? $IGGP_Thumb['media_type'] : 'IMAGE'; 
									if($IGGP_ThumbImg == 'IMAGE'){
										$PostImage = !empty($IGGP_Thumb['media_url']) ? $IGGP_Thumb['media_url'] : $Default_Img;
										break;
									}
								}
							}
							if($IGGP_MediaType == 'IMAGE' || $IGGP_MediaType == 'VIDEO'){
								$IGGP_CAROUSEL_ALBUM[] = array(
									"IGGPCAR_Index" => $index,
									"IGGPImg_Type" => $IGGP_MediaType,
									"IGGPURL_Media" => $IGGP_MediaURl,
								);
							}
						}

						$Taggedby = 'Tagged by <a href="https://www.instagram.com/'.esc_attr($Tag_Username).'" class="tpgb-mantion" target="_blank" rel="noopener noreferrer" aria-label="'.esc_attr($Tag_Username).'"> @'.esc_attr($Tag_Username).'</a>';

						if( $Media_type != 'VIDEO' && $IGGP_CountFiler < $IGcount ) {
							$IGArr[] = array(
								"Feed_Index"	=> $index,
								"PostId"		=> !empty($Tag['id']) ? $Tag['id'] : '',
								"Massage" 		=> $Taggedby,
								"Description"	=> !empty($Tag['caption']) ? $Tag['caption'] : '',
								"Type" 			=> $Media_type,
								"PostLink" 		=> !empty($Tag['media_url']) ? $Tag['media_url'] : '',
								"CreatedTime" 	=> !empty($Tag['timestamp']) ? tpgbp_feed_Post_time($Tag['timestamp']) : '',
								"PostImage" 	=> !empty($Tag['media_url']) ? $Tag['media_url'] : '',
								"UserName" 		=> $GET_UserName,
								"UserImage" 	=> $GET_Profile,
								"UserLink" 		=> $Permalink,
								"comment" 		=> !empty($Tag['comments_count']) ?  tpgbp_number_short($Tag['comments_count']) : 0,
								"likes" 		=> !empty($Tag['like_count']) ? tpgbp_number_short($Tag['like_count']) : 0,
								"IG_Type"		=> $IGType,
								"IGGP_Icon"		=> $IGGP_Icon,
								"IGGP_CAROUSEL" => $IGGP_CAROUSEL_ALBUM,
								"IGGP_PostLink" => $Permalink,
								"socialIcon" 	=> $IGIcon,
								"selectFeed"    => $selectFeed,
								"FilterCategory"=> $RCategory,
								"RKey" 			=> "tp-repeater-item-$IGKey",
							);
							$IGGP_CountFiler++;
						}
					}
				}else{
					$IGArr[] = tpgbp_SF_Error_handler($Tag_Res, $IGKey, $RCategory, $selectFeed, $IGIcon);
				}
			}
		}else{
			$IGArr[] = tpgbp_SF_Error_handler($UserID_Res, $IGKey, $RCategory, $selectFeed, $IGIcon);
		}
	}
	return $IGArr;
}

function tpgbp_VimeoFeed($social,$attr){
	$BaseURL = 'https://api.vimeo.com';
	$VmKey = (!empty($social['_key'])) ? $social['_key'] : '';
	$VmAcT = (!empty($social['RAToken'])) ? $social['RAToken'] : '';
	$VmType = (!empty($social['VimeoType'])) ? $social['VimeoType'] : 'Vm_User';
	$VmUname = (!empty($social['VmUname'])) ? $social['VmUname'] : '';
	$VmQsearch = (!empty($social['VmQsearch'])) ? $social['VmQsearch'] : '';
	$VmChannel = (!empty($social['VmChannel'])) ? $social['VmChannel'] : '';
	$VmGroup = (!empty($social['VmGroup'])) ? $social['VmGroup'] : '';
	$VmCategories = (!empty($social['VmCategories'])) ? str_replace(' ','', $social['VmCategories']) : '';
	$VmAlbum = (!empty($social['VmAlbum'])) ? $social['VmAlbum'] : '';
	$VmMax = (!empty($social['MaxR'])) ? $social['MaxR'] : 6;
	$VmTime = (!empty($social['TimeFrq'])) ? $social['TimeFrq'] : '3600';
	$SSL_VER = $attr['CURLOPT_SSL_VERIFYPEER'];
	$VmSelectFeed = !empty($social['selectFeed']) ? $social['selectFeed'] : '';
	$VmRCategory = !empty($social['RCategory']) ? $social['RCategory'] : '';
	$VmIcon = 'fab fa-vimeo-v social-logo-vm';
	
	$URL='';$Vimeo='';	

	if($VmType == "Vm_User"){
		$URL = "{$BaseURL}/users/{$VmUname}/videos?access_token={$VmAcT}&per_page={$VmMax}&page=1";
	}else if($VmType == "Vm_search"){
		$URL = "{$BaseURL}/videos?access_token={$VmAcT}&query={$VmQsearch}&per_page={$VmMax}&page=1";
	}else if($VmType == "Vm_liked"){
		$URL = "{$BaseURL}/users/{$VmUname}/likes?access_token={$VmAcT}&per_page={$VmMax}&page=1";
	}else if($VmType == "Vm_Channel"){
		$URL = "{$BaseURL}/channels/{$VmChannel}/videos?access_token={$VmAcT}&per_page={$VmMax}&page=1";
	}else if($VmType == "Vm_Group"){
		$URL = "{$BaseURL}/groups/{$VmGroup}/videos?access_token={$VmAcT}&per_page={$VmMax}&page=1";
	}else if($VmType == "Vm_Album"){
		$VmAPass = (!empty($social['VmAlbumPass'])) ? "&password=".$social['VmAlbumPass'] : '';
		$URL = "{$BaseURL}/users/{$VmUname}/albums/{$VmAlbum}/videos?access_token={$VmAcT}&per_page={$VmMax}&page=1$VmAPass";
	}else if($VmType == "Vm_categories"){
		$URL = "{$BaseURL}/categories/{$VmCategories}/videos?access_token={$VmAcT}&per_page={$VmMax}&page=1";
	}

	$GetVmURL = get_transient("Vm-Url-$VmKey");
	$GetVmTime = get_transient("Vm-Time-$VmKey");
	if( ($GetVmURL != $URL) || ($GetVmTime != $VmTime) ){
		$Vimeo = tpgbp_api_call($URL,$SSL_VER);
			set_transient("Vm-Url-$VmKey", $URL, $VmTime);
			set_transient("Vm-Time-$VmKey", $VmTime, $VmTime);
			set_transient("Data-Vm-$VmKey", $Vimeo, $VmTime);
	}else{
		$Vimeo = get_transient("Data-Vm-$VmKey");
	}

	$VmArr = [];
	$HTTP_CODE = (!empty($Vimeo['HTTP_CODE'])) ? $Vimeo['HTTP_CODE'] : '';
	if($HTTP_CODE == 200){
		$VmData = (!empty($Vimeo['data'])) ? $Vimeo['data'] : [];
		foreach ($VmData as $index => $Vmsocial) {
			$VmUrl = (!empty($Vmsocial['uri'])) ?  str_replace('videos', 'video', $Vmsocial['uri'])  : '';
			$VmImg = (!empty($Vmsocial['pictures'])) ? $Vmsocial['pictures']["sizes"] : [];
			$VmThumb = [];
			foreach ($VmImg as $VmValue) { 
				$VmThumb[] = $VmValue["link"];
			}
			$VmImage = end($VmThumb);

			$VmProfile = (!empty($Vmsocial["user"])) ? $Vmsocial["user"]["pictures"]["sizes"] : [];
			$VmPThumb = [];
			foreach ($VmProfile as $Vmlink) { 
				$VmPThumb[] = $Vmlink["link"]; 
			}
			$VmProfileLink = end($VmPThumb);

			$VmArr[] = array(
				"Feed_Index"	=> $index,
				"PostId"		=> (!empty($Vmsocial['resource_key'])) ? $Vmsocial['resource_key'] : '',
				"Massage" 		=> (!empty($Vmsocial['name'])) ? $Vmsocial['name'] : '',
				"Description"	=> (!empty($Vmsocial['description'])) ? $Vmsocial['description'] : '',
				"Type" 			=> (!empty($Vmsocial['type'])) ? $Vmsocial['type'] : '',
				"PostLink" 		=> (!empty($Vmsocial['link'])) ? $Vmsocial['link'] : '',
				"CreatedTime" 	=> (!empty($Vmsocial['created_time'])) ? tpgbp_feed_Post_time($Vmsocial['created_time']) : '',
				"PostImage" 	=> (!empty($VmImage)) ? $VmImage : '',
				"UserName" 		=> (!empty($Vmsocial["user"]["name"])) ? $Vmsocial["user"]["name"] : '',
				"UserImage" 	=> (!empty($VmProfileLink)) ? $VmProfileLink : '',
				"UserLink" 		=> (!empty($Vmsocial["user"]["link"])) ? $Vmsocial["user"]["link"] : '',
				"share" 		=> (!empty($Vmsocial["user"]["metadata"])) ? tpgbp_number_short($Vmsocial["user"]["metadata"]["connections"]["shared"]["total"]) : 0,
				"likes" 		=> (!empty($Vmsocial['metadata'])) ? tpgbp_number_short($Vmsocial["metadata"]["connections"]["likes"]["total"]) : 0,
				"comment" 		=> (!empty($Vmsocial['metadata'])) ? tpgbp_number_short($Vmsocial["metadata"]["connections"]["comments"]["total"]) : 0,
				"Embed" 		=> "https://player.vimeo.com{$VmUrl}",
				"EmbedType"     => (!empty($Vmsocial['type'])) ? $Vmsocial['type'] : '',
				"socialIcon" 	=> $VmIcon,
				"selectFeed"    => $VmSelectFeed,
				"FilterCategory"=> $VmRCategory,
				"RKey" 			=> "tp-repeater-item-$VmKey",
			);
		}
	}else{
		$Error = !empty($Vimeo['error']) ? $Vimeo['error'] : '';
		$ErrorData['error']['message'] = !empty($Vimeo['error']) && !empty($Vimeo['developer_message']) ? '<b>'.$Vimeo['error'].'</b></br>'.$Vimeo['developer_message'] : '';
		$ErrorData['error']['HTTP_CODE'] = !empty($Vimeo['HTTP_CODE']) ? $Vimeo['HTTP_CODE'] : 400;

		$VmArr[] = tpgbp_SF_Error_handler($ErrorData, $VmKey, $VmRCategory, $VmSelectFeed, $VmIcon);
	}

	return $VmArr;
}

function tpgbp_YouTubeFeed($social,$attr){
	$BaseURL = 'https://www.googleapis.com/youtube/v3';
	$YtKey = (!empty($social['_key'])) ? $social['_key'] : '';
	$YtAcT = (!empty($social['RAToken'])) ? $social['RAToken'] : '';
	$YtType = (!empty($social['RYtType'])) ? $social['RYtType'] : 'YT_Channel';
	$YtName = (!empty($social['YtName'])) ? $social['YtName'] : '';
	$YtOrder = (!empty($social['YTvOrder'])) ? $social['YTvOrder'] : 'date';
	$YTthumbnail = !empty($social['YTthumbnail']) ? $social['YTthumbnail'] : 'medium';
	$YtMax = (!empty($social['MaxR'])) ? $social['MaxR'] : 6;
	$YtTime = (!empty($social['TimeFrq'])) ? $social['TimeFrq'] : '3600';
	$YtCategory = !empty($social['RCategory']) ? $social['RCategory'] : '';
	$YtselectFeed = !empty($social['selectFeed']) ? $social['selectFeed'] : '';
	$YtIcon = 'fab fa-youtube social-logo-yt';
	$SSL_VER = $attr['CURLOPT_SSL_VERIFYPEER'];
	
	$URL = '';
	$UserLink = '';
	$YTData = [];
	$YtArr = [];

	if($YtType == 'YT_Userfeed'){
		$YTUserAPI = "{$BaseURL}/channels?part=snippet&forUsername={$YtName}&key={$YtAcT}";
		$GetYtuser = get_transient("Yt-user-$YtKey");
		$GetYtUserTime = get_transient("Yt-user-Time-$YtKey");
		if( ($GetYtuser != $YTUserAPI) || ($GetYtUserTime != $YtTime) ){
			$YtUNdata = tpgbp_api_call($YTUserAPI,$SSL_VER);
				set_transient("Data-Yt-user-$YtKey", $YtUNdata, $YtTime);
				set_transient("Yt-user-$YtKey", $YTUserAPI, $YtTime);
				set_transient("Yt-user-Time-$YtKey", $YtTime, $YtTime);
		}else{
			$YtUNdata = get_transient("Data-Yt-user-$YtKey");
		}

		$YTStatus = (!empty($YtUNdata['HTTP_CODE'])) ? $YtUNdata['HTTP_CODE'] : '';
		if($YTStatus == 200){
			$YTUserID = (!empty($YtUNdata['items'][0]['id'])) ? $YtUNdata['items'][0]['id'] : '';

			$YtPic = '';
			$YtPicPath = !empty($YtUNdata['items'][0]['snippet']['thumbnails']) ? $YtUNdata['items'][0]['snippet']['thumbnails'] : '';
			
			if(!empty($YtPicPath)){
				if(!empty($YtPicPath['default']['url'])){ $YtPic = $YtPicPath['default']['url']; }
				if(!empty($YtPicPath['medium']['url'])){ $YtPic = $YtPicPath['medium']['url']; }
				if(!empty($YtPicPath['high']['url'])){ $YtPic = $YtPicPath['high']['url']; }
			}
			$UserLink = array( 'UserLink'=> "https://www.youtube.com/user/{$YtName}", 'YTprofile'=> $YtPic );
			$URL = "{$BaseURL}/search?part=snippet&type=video&order={$YtOrder}&maxResults={$YtMax}&channelId={$YTUserID}&key={$YtAcT}";
		}
	}else if($YtType == 'YT_Channel'){
		$YtChannel = (!empty($social['YTChannel'])) ? $social['YTChannel'] : '';
		$UserLink = array('UserLink'=> "https://www.youtube.com/channel/{$YtChannel}");
		$URL = "{$BaseURL}/search?part=snippet&type=video&order={$YtOrder}&maxResults={$YtMax}&channelId={$YtChannel}&key={$YtAcT}";
	}else if($YtType == 'YT_Playlist'){
		$YtPlaylist = (!empty($social['YTPlaylist'])) ? $social['YTPlaylist'] : '';
		$UserLink = array('UserLink'=> "https://www.youtube.com/playlist?list={$YtPlaylist}");
		$URL = "{$BaseURL}/playlistItems?part=snippet&playlistId={$YtPlaylist}&maxResults={$YtMax}&key={$YtAcT}";
	}else if($YtType == 'YT_Search'){
		$Ytsearch = (!empty($social['YTsearchQ'])) ? $social['YTsearchQ'] : '';
		$UserLink = array('UserLink'=> "https://www.youtube.com/channel/");
		$URL = "{$BaseURL}/search?part=id,snippet&q={$Ytsearch}&type=video&maxResults={$YtMax}&key={$YtAcT}";
	}

	$GetYtURL = get_transient("Yt-Url-$YtKey");
	$GetYtTime = get_transient("Yt-Time-$YtKey");
	if( ($GetYtURL != $URL) || ($GetYtTime != $YtTime) ){
		$YTPData = tpgbp_api_call($URL,$SSL_VER);
		$YTData = array_merge($UserLink, $YTPData);
			set_transient("Yt-Url-$YtKey", $URL, $YtTime);
			set_transient("Yt-Time-$YtKey", $YtTime, $YtTime);
			set_transient("Data-Yt-$YtKey", $YTData, $YtTime);
	}else{
		$Yt_S_Data = get_transient("Data-Yt-$YtKey");
		if(!empty($Yt_S_Data)){
			$YTData = array_merge($UserLink, $Yt_S_Data);
		}
	}
	
	$HTTP_CODE = (!empty($YTData['HTTP_CODE'])) ? $YTData['HTTP_CODE'] : '';
	if($HTTP_CODE == 200){
		$UserLink = (!empty($YTData['UserLink'])) ? $YTData['UserLink'] : '';
		$YtProfile = (!empty($YTData['YTprofile'])) ? $YTData['YTprofile'] : '';
		
		$Ytpost = (!empty($YTData['items'])) ? $YTData['items'] : [];
		
		foreach ($Ytpost as $index => $YtSearch) {
			$snippet = (!empty($YtSearch['snippet'])) ? $YtSearch['snippet'] : '';
			$VideoId = (!empty($YtSearch['id']['videoId'])) ? $YtSearch['id']['videoId'] : '';
			
			$thumbnails = '';
			if($YTthumbnail == 'default' && !empty($snippet['thumbnails']['default']['url']) ){
				$thumbnails = $snippet['thumbnails']['default']['url'];
			}else if($YTthumbnail == 'medium' && !empty($snippet['thumbnails']['medium']['url']) ){
				$thumbnails = $snippet['thumbnails']['medium']['url'];
			}else if($YTthumbnail == 'high' && !empty($snippet['thumbnails']['high']['url']) ){
				$thumbnails = $snippet['thumbnails']['high']['url'];
			}else if($YTthumbnail == 'standard' && !empty($snippet['thumbnails']['standard']['url']) ){
				$thumbnails = $snippet['thumbnails']['standard']['url'];
			}else if($YTthumbnail == 'maxres' && !empty($snippet['thumbnails']['maxres']['url']) ){
				$thumbnails = $snippet['thumbnails']['maxres']['url'];
			}

			if($YtType == 'YT_Userfeed' || $YtType == 'YT_Channel' || $YtType == 'YT_Search'){
				$YtVideoUrl = "https://www.youtube.com/watch?v={$VideoId}";
			}else if($YtType == 'YT_Playlist'){
				$V_ID = $VideoId = (!empty($snippet['resourceId']['videoId'])) ? $snippet['resourceId']['videoId'] : '';
				$P_ID = (!empty($snippet['playlistId'])) ? $snippet['playlistId'] : '';
				$YtVideoUrl = "https://www.youtube.com/watch?v={$V_ID}&list={$P_ID}";
			}
			if($YtType == 'YT_Playlist' || $YtType == 'YT_Search' || $YtType == 'YT_Channel'){
				$channelId = (!empty($snippet['channelId'])) ? $snippet['channelId'] : '';

				$YTsPic = "{$BaseURL}/channels?part=snippet&id={$channelId}&key={$YtAcT}";			
				if( (get_transient("Yt-C-Url-$YtKey") != $YTsPic) || (get_transient("Yt-c-Time-$YtKey") != $YtTime) ){
					$YTRPic = tpgbp_api_call($YTsPic,$SSL_VER);
						set_transient("Yt-C-Url-$YtKey", $YTsPic, $YtTime);
						set_transient("Yt-c-Time-$YtKey", $YtTime, $YtTime);
						set_transient("Data-c-Yt-$YtKey", $YTRPic, $YtTime);
				}else{
					$YTRPic = get_transient("Data-c-Yt-$YtKey");	
				}
				
				$YtSstatus = (!empty($YTRPic['HTTP_CODE'])) ? $YTRPic['HTTP_CODE'] : '';
				if($YtSstatus == 200){
					$YtProfile = (($YTRPic['items'][0]['snippet']['thumbnails']['high']['url']) ? $YTRPic['items'][0]['snippet']['thumbnails']['high']['url'] : '');
				}
			}
			
			$GetComment = "{$BaseURL}/videos?part=statistics&id={$VideoId}&maxResults={$YtMax}&key={$YtAcT}";
			$YtCommentAll = tpgbp_api_call($GetComment,$SSL_VER);
			$HTTP_CODE_C = (!empty($YtCommentAll['HTTP_CODE'])) ? $YtCommentAll['HTTP_CODE'] : '';
			if($HTTP_CODE_C == 200){
				$statistics = (!empty($YtCommentAll['items'][0]['statistics']) ? $YtCommentAll['items'][0]['statistics'] : '');
				$YtCMTstatus = (!empty($YtCommentAll['HTTP_CODE'])) ? $YtCommentAll['HTTP_CODE'] : '';
				if($YtCMTstatus == 200 && !empty($statistics)){
					$view = (!empty($statistics) && !empty($statistics['viewCount'])) ? $statistics['viewCount'] : 0;
					$like = (!empty($statistics) && !empty($statistics['likeCount'])) ? $statistics['likeCount'] : 0;
					$Dislike = (!empty($statistics) && !empty($statistics['dislikeCount'])) ? $statistics['dislikeCount'] : 0;
					$comment = (!empty($statistics) && !empty($statistics['commentCount']) ) ? $statistics['commentCount'] : 0;
				}
			}
			
			$YtArr[] = array(
				"Feed_Index"	=> $index,
				"PostId"		=> $VideoId,
				"Massage" 		=> (!empty($snippet['title'])) ? $snippet['title'] : '',
				"Description"	=> (!empty($snippet['description'])) ? $snippet['description'] : '',
				"Type" 			=> 'video',
				"PostLink" 		=> (!empty($YtVideoUrl) ? $YtVideoUrl : ''),
				"CreatedTime" 	=> (!empty($snippet['publishedAt'])) ? tpgbp_feed_Post_time($snippet['publishedAt']) : '',
				"PostImage" 	=> (!empty($thumbnails)) ? $thumbnails : '',
				"UserName" 		=> (!empty($snippet['channelTitle'])) ? $snippet['channelTitle'] : '',
				"UserImage" 	=> (!empty($YtProfile)) ? $YtProfile : '',
				"UserLink" 		=> (!empty($UserLink)) ? $UserLink : '',
				"view" 			=> (isset($view)) ? tpgbp_number_short($view) : 0,
				"likes" 		=> (isset($like)) ? tpgbp_number_short($like) : 0,
				"comment" 		=> (isset($comment)) ? tpgbp_number_short($comment) : 0,
				"Dislike" 		=> (isset($Dislike)) ? tpgbp_number_short($Dislike) : 0,
				"Embed" 		=> "https://www.youtube.com/embed/{$VideoId}",
				"EmbedType"     => 'video',
				"socialIcon" 	=> 'fab fa-youtube social-logo-yt',
				"selectFeed"    => (!empty($social['selectFeed'])) ? $social['selectFeed'] : '',
				"FilterCategory"=> (!empty($social['RCategory'])) ? $social['RCategory'] : '',
				"RKey" 			=> "tp-repeater-item-$YtKey",
			);
		}
	}else{
		$Error = !empty($YTData['error']) ? $YTData['error'] : [];
		$ErrorData['error']['message'] = !empty($Error['message']) ? $Error['message'] : '';
		$ErrorData['error']['HTTP_CODE'] = !empty($Error['HTTP_CODE']) ? $Error['HTTP_CODE'] : 400;
		$YtArr[] = tpgbp_SF_Error_handler($ErrorData, $YtKey, $YtCategory, $YtselectFeed, $YtIcon);
	}
	
	return $YtArr;
}

function tpgbp_api_call($API,$SSL){
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
				CURLOPT_SSL_VERIFYPEER => $CURLOPT_SSL_VERIFYPEER,
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

function tpgbp_feed_Post_time($datetime, $full = false) {
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

function tpgbp_number_short( $n, $precision = 1 ) {
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

function tpgbp_social_feed_fancybox($attr){
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

function tpgbp_SF_CategoryFilter($count, $allreview, $arr){
	$category_filter = '';
	$TeamMemberR = (!empty($arr['AllReapeter'])) ? $arr['AllReapeter'] : [];  // repeater name
	
	$CategoryWF = !empty($arr['CategoryWF']) ? $arr['CategoryWF'] : false;	
	
	if(!empty($CategoryWF)){
		$filter_style = !empty($arr['CatFilterS']) ? $arr['CatFilterS'] : 'style-1';	
		$filter_hover_style = !empty($arr['FilterHs']) ? $arr['FilterHs'] : 'style-1';
		$all_filter_category = (!empty($arr["TextCat"])) ? $arr["TextCat"] : esc_html__('All','tpgbp');
		$loop_category = [];
		foreach ( $TeamMemberR as $TMFilter ) {
			$TMCategory = !empty($TMFilter['RCategory']) ? $TMFilter['RCategory'] : '';  // repeater category name
			if(!empty($TMCategory)){
				$loop_category[] = explode(',', $TMCategory);
			}
		}
		$loop_category = tpgbp_SF_Split_Array_Category($loop_category);
		$count_category = array_count_values($loop_category);
		
		$all_category=$category_post_count='';
		if($filter_style=='style-1'){
			$all_category='<span class="tpgb-category-count">'.esc_html($count).'</span>';
		}
		if($filter_style=='style-2' || $filter_style=='style-3'){
			$category_post_count='<span class="tpgb-category-count">'.esc_html($count).'</span>';
		}
		$category_filter .='<div class="tpgb-category-filter">';
			$category_filter .='<div class="tpgb-filter-data '.esc_attr($filter_style).'">';
			
			if($filter_style=='style-4'){
				$category_filter .= '<span class="tpgb-filters-link">'.esc_html__('Filters','tpgbp').'<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 64 64" style="enable-background:new 0 0 64 64;" xml:space="preserve"><g><line x1="0" y1="32" x2="63" y2="32"></line></g><polyline points="50.7,44.6 63.3,32 50.7,19.4 "></polyline><circle cx="32" cy="32" r="31"></circle></svg></span>';
			}
			
				$category_filter .='<div class="tpgb-categories '.esc_attr($filter_style).' hover-'.esc_attr($filter_hover_style).'">';
					$category_filter .= '<div class="tpgb-filter-list"><a href="#" class="tpgb-category-list active all" data-filter="*" aria-label="'.esc_attr($all_filter_category).'">'.$category_post_count.'<span data-hover="'.esc_attr($all_filter_category).'">'.esc_html($all_filter_category).'</span>'.$all_category.'</a></div>';

					foreach ( $loop_category as $i => $key ) {
						$slug = tpgbp_SF_Media_createSlug($key) ;		
						$category_post_count = '';
						if($filter_style == 'style-2' || $filter_style == 'style-3'){
							$CategoryCount=0;
							foreach ($allreview as $index => $value) {
								$CategoryName = !empty($value['FilterCategory']) ? $value['FilterCategory'] : '';
								$nCatName = explode(',', $CategoryName);
								if(in_array($key, $nCatName) && $index < $count){
									$CategoryCount++;
								}
							}
							$category_post_count = '<span class="tpgb-category-count">'.esc_html($CategoryCount).'</span>';
						}

						$category_filter .= '<div class="tpgb-filter-list">';
							$category_filter .= '<a href="#" class="tpgb-category-list"  data-filter=".'.esc_attr($slug).'" aria-label="'.esc_attr($key).'">';
								$category_filter .= $category_post_count;
								$category_filter .= '<span data-hover="'.esc_attr($key).'">';
									$category_filter .= esc_html($key);
								$category_filter .= '</span>';
							$category_filter .= '</a>';
						$category_filter .= '</div>';
					}
				$category_filter .= '</div>';
			$category_filter .= '</div>';
		$category_filter .= '</div>';
	}
	return $category_filter;

}

function tpgbp_SF_Split_Array_Category($array){
	if (!is_array($array)) { 
	  return FALSE; 
	} 
	$result = array(); 
	foreach ($array as $key => $value) { 
	  if (is_array($value)) { 
		$result = array_merge($result, tpgbp_SF_Split_Array_Category($value)); 
	  } 
	  else { 
		$result[$key] = $value; 
	  }
	}
	
	return $result; 
}

function tpgbp_SF_Media_createSlug($str, $delimiter = '-'){
	$slug = preg_replace('/[^A-Za-z0-9-]+/', $delimiter, $str);
	return $slug;
}

function tpgbp_SF_Error_handler($ErrorData, $Rkey='', $RCategory='', $selectFeed='', $Icon=''){
	$Error = !empty($ErrorData['error']) ? $ErrorData['error'] : [];
	return array(
		"Feed_Index" 	=> 0,
		"ErrorClass"    => "error-class",
		"socialIcon" 	=> $Icon,
		"CreatedTime" 	=> "<b>{$selectFeed}</b>",
		"Description" 	=> !empty($Error['message']) ? $Error['message'] : 'Somthing Wrong',
		"UserName" 		=> !empty($Error['HTTP_CODE']) ? 'Error Code : '.$Error['HTTP_CODE'] : 400,
		"UserImage" 	=> TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
		"selectType"    => $selectFeed,
		"FilterCategory"=> $RCategory,
		"RKey" 			=> "tp-repeater-item-$Rkey",
	);
}