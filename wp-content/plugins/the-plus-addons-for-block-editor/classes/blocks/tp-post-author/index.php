<?php
/* Tp Block : Post Author
 * @since	: 2.0.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_post_author_render_callback( $attr, $content) {
	$output = '';
	
    $post = get_queried_object();
    $block_id = (!empty($attr['block_id'])) ? $attr['block_id'] : uniqid("title");
	$Align = (!empty($attr['Align'])) ? $attr['Align'] : '';
	$authorStyle = (!empty($attr['authorStyle'])) ? $attr['authorStyle'] : 'style-1';
    $ShowName = (!empty($attr['ShowName'])) ? $attr['ShowName'] : false;
    $ShowBio = (!empty($attr['ShowBio'])) ? $attr['ShowBio'] : false;
    $ShowAvatar = (!empty($attr['ShowAvatar'])) ? $attr['ShowAvatar'] : false;
    $ShowSocial = (!empty($attr['ShowSocial'])) ? $attr['ShowSocial'] : false;
    $ShowRole = (!empty($attr['ShowRole'])) ? $attr['ShowRole'] : false;
	$roleLabel = (!empty($attr['roleLabel'])) ? $attr['roleLabel'] : 'Role : ';
    $titleLabel = (!empty($attr['titleLabel'])) ? $attr['titleLabel'] : 'Author : ';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attr );
	
	$outputavatar=$outputname=$outputbio=$outputrole=$authorsocial='';
	if(!empty($post)){
		$author_page_url = get_author_posts_url($post->post_author);
		$author_bio =  get_the_author_meta('user_description',$post->post_author);
		if( !empty( $ShowName ) ){
			$author_name = get_the_author_meta('display_name', $post->post_author);
			$outputname .='<a href="'.esc_url($author_page_url).'" class="author-name tpgb-trans-linear" rel="'.esc_attr__('author','tpgb').'" >'.wp_kses_post($titleLabel).esc_html($author_name).'</a>';
		}
		if(!empty($ShowAvatar)){
			$author_name = get_the_author_meta('display_name', $post->post_author);
			$outputavatar .= '<a href="'.esc_url($author_page_url).'" rel="'.esc_attr__('author','tpgb').'" aria-label="'.esc_attr($author_name).'" class="author-avatar tpgb-trans-linear">'.get_avatar( get_the_author_meta('email',$post->post_author), 130 ).'</a>';
		}
		if(!empty($ShowBio)){
			$outputbio .= '<div class="author-bio tpgb-trans-linear" >'.esc_html($author_bio).'</div>';
		}

		$user_meta=get_the_author_meta('roles',$post->post_author);
		if(!empty($ShowRole) && !empty($user_meta)){
			$author_role = $user_meta[0];
			$outputrole .= '<span class="author-role">'.wp_kses_post( $roleLabel ).esc_html($author_role).'</span>';
		}

		if(!empty($ShowSocial)){
			$author_website =  get_the_author_meta('user_url',$post->post_author);
			$author_facebook = get_the_author_meta('author_facebook', $post->post_author);
			$author_email =  get_the_author_meta('email',$post->post_author);
			$author_twitter = get_the_author_meta('author_twitter', $post->post_author);
			$author_instagram = get_the_author_meta('author_instagram', $post->post_author);
			$authorsocial .= '<div class="author-social">';
				if(!empty($author_website)){
					$authorsocial .= '<div class="tpgb-author-social-list" ><a href="'.esc_url($author_website).'" aria-label="'.esc_attr__("website","tpgb").'" target="_blank"><i class="fas fa-globe-asia"></i></a></div>';
				}
				if(!empty($author_email)){
					$authorsocial .= '<div class="tpgb-author-social-list" ><a href="'.esc_url($author_email).'" aria-label="'.esc_attr__("Email","tpgb").'" target="_blank"><i class="fas fa-envelope"></i></a></div>';
				}
				if(!empty($author_facebook)){
					$authorsocial .= '<div class="tpgb-author-social-list" ><a href="'.esc_url($author_facebook).'" aria-label="'.esc_attr__("facebook","tpgb").'" target="_blank"><i class="fab fa-facebook-f"></i></a></div>';
				}
				if(!empty($author_twitter)){
					$authorsocial .= '<div class="tpgb-author-social-list" ><a href="'.esc_url($author_twitter).'" aria-label="'.esc_attr__("twitter","tpgb").'" target="_blank"><i class="fab fa-twitter" ></i></a></div>';
				}
				if(!empty($author_instagram)){
					$authorsocial .= '<div class="tpgb-author-social-list" ><a href="'.esc_url($author_instagram).'" aria-label="'.esc_attr__("instagram","tpgb").'" target="_blank"><i class="fab fa-instagram"></i></a></div>';
				}
			$authorsocial .='</div>';
		}
	}

    $output .= '<div class="tpgb-post-author tpgb-block-'.esc_attr($block_id ).' '.esc_attr($blockClass).'" >';
		$output .= '<div class="tpgb-post-inner  author-'.esc_attr($authorStyle).' '.($authorStyle == 'style-2' ? ' text-'.esc_attr($Align) : '' ).' ">';
			if($ShowAvatar){
				$output .=$outputavatar;
			}
			$output .='<div class="author-info">';
				if(!empty($ShowName)){
					$output .=$outputname;
				}
				if(!empty($ShowRole)){
					$output .= $outputrole;
				}
				if(!empty($ShowBio)){
					$output .=$outputbio;
				}
				if(!empty($ShowSocial)){
					$output .=$authorsocial;
				}
			$output .= '</div>';
		$output .= '</div>';
    $output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attr, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_post_author_content() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
    $globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
    $globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'authorStyle' => [
				'type' => 'string',
				'default' => 'style-1',
			],
			'Align' => [
				'type' => 'string',
				'default' => 'left',
			],
			'maxWidth' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'authorStyle', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner{ max-width: {{maxWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'ShowName' => array(
                'type' => 'boolean',
				'default' => true,
			),
			'titleLabel' => [
				'type' => 'string',
				'default' => 'Author : ',
			],
			'nameTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowName', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner .author-name',
					],
				],
				'scopy' => true,
			],
			
			'nameNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowName', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner .author-name{color: {{nameNormalColor}};}',
					],
				],
				'scopy' => true,
            ],
			'nameHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowName', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner:hover .author-name{color: {{nameHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			'ShowRole' => [
				'type' => 'boolean',
				'default' => true,
			],
			'roleLabel' => [
				'type' => 'string',
				'default' => 'Role : ',
			],
			'roleTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowRole', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner .author-role',
					],
				],
				'scopy' => true,
			],
			'roleColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowRole', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner .author-role{color: {{roleColor}};}',
					],
				],
				'scopy' => true,
            ],
			'roleHvrColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowRole', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner:hover .author-role{color: {{roleHvrColor}};}',
					],
				],
				'scopy' => true,
            ],
			'ShowBio' => array(
                'type' => 'boolean',
				'default' => true,
			),
			'bioMargin' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner .author-bio {margin: {{bioMargin}};}',
					],
				],
				'scopy' => true,
			],
			'bioTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowBio', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner .author-bio',
					],
				],
				'scopy' => true,
			],
			
			'bioNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowBio', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner .author-bio{color: {{bioNormalColor}};}',
					],
				],
				'scopy' => true,
            ],
			'bioHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowBio', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner:hover .author-bio{color: {{bioHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			'ShowAvatar' => array(
                'type' => 'boolean',
				'default' => true,
			),
			'avatarWidth' => [
				'type' => 'object',
				'default' => ['md' => '', 'unit' => 'px'],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowAvatar', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner .author-avatar{max-width: {{avatarWidth}};}',
					],
				],
				'scopy' => true,
			],
			
			'avatarBorderRadius' => [
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
						'condition' => [(object) ['key' => 'ShowAvatar', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner .author-avatar,{{PLUS_WRAP}} .tpgb-post-inner .author-avatar img{border-radius: {{avatarBorderRadius}};}',
					],
				],
				'scopy' => true,
			],
			'avatarBoxShadow' => [
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
						'condition' => [(object) ['key' => 'ShowAvatar', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner .author-avatar',
					],
				],
				'scopy' => true,
			],
			
			'ShowSocial' => array(
                'type' => 'boolean',
				'default' => true,
			),
			'socialSize' => [
				'type' => 'object',
				'default' => ['md' => '', 'unit' => 'px'],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowSocial', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner .author-social .tpgb-author-social-list a{font-size: {{socialSize}};}',
					],
				],
				'scopy' => true,
			],
			'socialNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowSocial', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner .author-social .tpgb-author-social-list a{color: {{socialNormalColor}};}',
					],
				],
				'scopy' => true,
            ],
			'socialHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowSocial', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner .author-social .tpgb-author-social-list a:hover{color: {{socialHoverColor}};}',
					],
				],
				'scopy' => true,
            ],

			'padding' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner {padding: {{padding}};}',
					],
				],
				'scopy' => true,
			],
			'boxBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 1,
					'type' => 'solid',
					'color' => '#f4f4f4',
					'width' => (object) [
						'md' => (object)[
							'top' => '2',
							'left' => '2',
							'bottom' => '2',
							'right' => '2',
						],
						'unit' => 'px',
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner ',
					],
				],
				'scopy' => true,
			],
			'boxBorderHover' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
					'width' => (object) [
						'md' => (object)[
							'top' => '',
							'left' => '',
							'bottom' => '',
							'right' => '',
						],
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner:hover',
					],
				],
				'scopy' => true,
			],
			
			'boxBRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner {border-radius: {{boxBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'boxBRadiusHover' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner:hover{border-radius: {{boxBRadiusHover}};}',
					],
				],
				'scopy' => true,
			],
			'boxBg' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [
						"direction" => 90,
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner ',
					],
				],
				'scopy' => true,
			],
			'boxBgHover' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [
						"direction" => 90,
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner:hover',
					],
				],
				'scopy' => true,
			],
			'boxBoxShadow' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner ',
					],
				],
				'scopy' => true,
			],
			'boxBoxShadowHover' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-post-inner:hover',
					],
				],
				'scopy' => true,
			],
		);
	
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-post-author', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_post_author_render_callback'
    ) );
}
add_action( 'init', 'tpgb_post_author_content' );