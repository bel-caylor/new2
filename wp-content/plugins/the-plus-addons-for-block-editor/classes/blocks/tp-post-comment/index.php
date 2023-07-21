<?php
/* Tp Block : Post Comment
 * @since	: 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_post_comment_render_callback( $attr, $content) {
	$output = '';
	$post_id = get_queried_object_id();
    $post = get_queried_object();
    $block_id = (!empty($attr['block_id'])) ? $attr['block_id'] : uniqid("title");
    $commentTitle = (!empty($attr['commentTitle'])) ? $attr['commentTitle'] : 'Comment';
	$comment_args = tpgb_comment_args($attr);
    $comment = get_comments($post);
    $list_args = array('style' => 'ul', 'short_ping' => true, 'avatar_size' => 100, 'page' => $post_id );
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attr );
	
	ob_start();
    echo '<div class="tpgb-post-comment tpgb-trans-linear tpgb-block-'.esc_attr($block_id ).' '.esc_attr($blockClass).'" >';
		echo '<div id="comments" class="comments-area">';
			if(get_comments_number($post_id) > 0) {
				echo '<ul class="comment-list">';
					echo '<li>';
						echo '<div class="comment-section-title">'.wp_kses_post($commentTitle).' ('. esc_html(get_comments_number($post_id)) . ')</div>';
					echo '<li>'; 
					wp_list_comments($list_args, $comment);
				echo "</ul>";
			}
			comment_form($comment_args, $post_id);
		echo "</div>";
	echo '</div>';

	$output .= ob_get_clean();
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attr, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_post_comment_content() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
    $globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
    $globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'commentTitle' => [
                'type' => 'string',
				'default' => 'Comment',
			],
			'commentFormTitle' => [
                'type' => 'string',
				'default' => 'Leave Your Comment',
			],
			'loggedInAsText' => [
                'type' => 'string',
				'default' => 'Logged in as',
			],
			'logOutText' => [
                'type' => 'string',
				'default' => 'Log out?',
			],
			'cancelReplyText' => [
                'type' => 'string',
				'default' => 'Cancel Reply',
			],
			'commentField' => [
                'type' => 'string',
				'default' => 'Comment',
			],
			'submitBtnText' => [
                'type' => 'string',
				'default' => 'Submit Now',
			],
			'commTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-list .comment-section-title,.tpgb-post-comment #respond.comment-respond h3#reply-title',
					],
				],
				'scopy' => true,
			],
			'commColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-list .comment-section-title,.tpgb-post-comment #respond.comment-respond h3#reply-title{color: {{commColor}};}',
					],
				],
				'scopy' => true,
            ],
			'profilePadding' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-list li.comment>.comment-body img.avatar, {{PLUS_WRAP}}.tpgb-post-comment .comment-list li.pingback>.comment-body img.avatar{ padding: {{profilePadding}}; }',
					],
				],
				'scopy' => true,
			],
			'profileBorderRadius' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-list li.comment>.comment-body img.avatar, {{PLUS_WRAP}}.tpgb-post-comment .comment-list li.pingback>.comment-body img.avatar{border-radius: {{profileBorderRadius}};}',
					],
				],
				'scopy' => true,
			],
			'profileBoxShadow' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-list li.comment>.comment-body img.avatar, {{PLUS_WRAP}}.tpgb-post-comment .comment-list li.pingback>.comment-body img.avatar',
					],
				],
				'scopy' => true,
			],
			'userTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-author.vcard cite.fn .url',
					],
				],
				'scopy' => true,
			],
			
			'userColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-author.vcard cite.fn .url{color: {{userColor}};}',
					],
				],
				'scopy' => true,
            ],
			'userHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-author.vcard cite.fn .url:hover{color: {{userHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			
			'metaTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-meta.commentmetadata a',
					],
				],
				'scopy' => true,
			],
			'metaColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-meta.commentmetadata a{color: {{metaColor}};}',
					],
				],
				'scopy' => true,
            ],
			'metaHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-meta.commentmetadata a:hover{color: {{metaHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			
			'replypadding' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-list .reply a{padding: {{replypadding}};}',
					],
				],
				'scopy' => true,
			],
			'replyTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-list .reply a',
					],
				],
				'scopy' => true,
			],
			'replyColor' => [
				'type' => 'string',
				'default' => '#f18248',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-list .reply a{color: {{replyColor}};}',
					],
				],
				'scopy' => true,
            ],
			'replyHoverColor' => [
				'type' => 'string',
				'default' => '#f18248',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-list .reply a:hover{border-color: {{replyHoverColor}};color: {{replyHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			'replyBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 1,
					'type' => 'solid',
					'color' => 'rgba(0,0,0,0)',
					'width' => (object) [
						'md' => (object)[
							'top' => '1',
							'left' => '1',
							'bottom' => '1',
							'right' => '1',
						],
						'unit' => 'px'
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-list .reply a',
					],
				],
				'scopy' => true,
			],
			'replyBorderHover' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 1,
					'type' => 'solid',
					'color' => '#f18248',
					'width' => (object) [
						'md' => (object)[
							'top' => '1',
							'left' => '1',
							'bottom' => '1',
							'right' => '1',
						],
						'unit' => 'px'
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-list .reply a:hover',
					],
				],
				'scopy' => true,
			],
			
			'replyBorderRadius' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-list .reply a{border-radius: {{replyBorderRadius}};}',
					],
				],
				'scopy' => true,
			],
			'replyBorderRadiusHover' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-list .reply a:hover{border-radius: {{replyBorderRadiusHover}};}',
					],
				],
				'scopy' => true,
			],
			'replyBg' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-list .reply a',
					],
				],
				'scopy' => true,
			],
			'replyBgHover' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-list .reply a:hover',
					],
				],
				'scopy' => true,
			],
			'replyBoxShadow' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-list .reply a',
					],
				],
				'scopy' => true,
			],
			'replyBoxShadowHover' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment .comment-list .reply a:hover',
					],
				],
				'scopy' => true,
			],
			
			'fieldTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #author, {{PLUS_WRAP}}.tpgb-post-comment #commentform #email, {{PLUS_WRAP}}.tpgb-post-comment #commentform #url, {{PLUS_WRAP}}.tpgb-post-comment form.comment-form textarea#comment',
					],
				],
				'scopy' => true,
			],
			'fieldColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #author, {{PLUS_WRAP}}.tpgb-post-comment #commentform #email, {{PLUS_WRAP}}.tpgb-post-comment #commentform #url, {{PLUS_WRAP}}.tpgb-post-comment form.comment-form textarea#comment{color: {{fieldColor}};}',
					],
				],
				'scopy' => true,
            ],
			'fieldHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #author:focus, {{PLUS_WRAP}}.tpgb-post-comment #commentform #email:focus, {{PLUS_WRAP}}.tpgb-post-comment #commentform #url:focus, {{PLUS_WRAP}}.tpgb-post-comment form.comment-form textarea#comment:focus{color: {{fieldHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			
			'fieldpadding' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #author, {{PLUS_WRAP}}.tpgb-post-comment #commentform #email, {{PLUS_WRAP}}.tpgb-post-comment #commentform #url, {{PLUS_WRAP}}.tpgb-post-comment form.comment-form textarea#comment{padding: {{fieldpadding}};}',
					],
				],
				'scopy' => true,
			],
			'fieldBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #author, {{PLUS_WRAP}}.tpgb-post-comment #commentform #email, {{PLUS_WRAP}}.tpgb-post-comment #commentform #url, {{PLUS_WRAP}}.tpgb-post-comment form.comment-form textarea#comment',
					],
				],
				'scopy' => true,
			],
			'fieldBorderHover' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #author:focus, {{PLUS_WRAP}}.tpgb-post-comment #commentform #email:focus, {{PLUS_WRAP}}.tpgb-post-comment #commentform #url:focus, {{PLUS_WRAP}}.tpgb-post-comment form.comment-form textarea#comment:focus',
					],
				],
				'scopy' => true,
			],
			
			'fieldBorderRadius' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #author, {{PLUS_WRAP}}.tpgb-post-comment #commentform #email, {{PLUS_WRAP}}.tpgb-post-comment #commentform #url, {{PLUS_WRAP}}.tpgb-post-comment form.comment-form textarea#comment{border-radius: {{fieldBorderRadius}};}',
					],
				],
				'scopy' => true,
			],
			'fieldBorderRadiusHover' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #author:focus, {{PLUS_WRAP}}.tpgb-post-comment #commentform #email:focus, {{PLUS_WRAP}}.tpgb-post-comment #commentform #url:focus, {{PLUS_WRAP}}.tpgb-post-comment form.comment-form textarea#comment:focus{border-radius: {{fieldBorderRadiusHover}};}',
					],
				],
				'scopy' => true,
			],
			'fieldBg' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #author, {{PLUS_WRAP}}.tpgb-post-comment #commentform #email, {{PLUS_WRAP}}.tpgb-post-comment #commentform #url, {{PLUS_WRAP}}.tpgb-post-comment form.comment-form textarea#comment',
					],
				],
				'scopy' => true,
			],
			'fieldBgHover' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #author:focus, {{PLUS_WRAP}}.tpgb-post-comment #commentform #email:focus, {{PLUS_WRAP}}.tpgb-post-comment #commentform #url:focus, {{PLUS_WRAP}}.tpgb-post-comment form.comment-form textarea#comment:focus',
					],
				],
				'scopy' => true,
			],
			'fieldBoxShadow' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #author, {{PLUS_WRAP}}.tpgb-post-comment #commentform #email, {{PLUS_WRAP}}.tpgb-post-comment #commentform #url, {{PLUS_WRAP}}.tpgb-post-comment form.comment-form textarea#comment',
					],
				],
				'scopy' => true,
			],
			'fieldBoxShadowHover' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #author:focus, {{PLUS_WRAP}}.tpgb-post-comment #commentform #email:focus, {{PLUS_WRAP}}.tpgb-post-comment #commentform #url:focus, {{PLUS_WRAP}}.tpgb-post-comment form.comment-form textarea#comment:focus',
					],
				],
				'scopy' => true,
			],
			
			'btnTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #submit',
					],
				],
				'scopy' => true,
			],
			'btnColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #submit{color: {{btnColor}};}',
					],
				],
				'scopy' => true,
            ],
			'btnHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #submit:hover{color: {{btnHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			
			'btnpadding' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #submit{padding: {{btnpadding}};}',
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #submit',
					],
				],
				'scopy' => true,
			],
			'btnBorderHover' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #submit:hover',
					],
				],
				'scopy' => true,
			],
			
			'btnBorderRadius' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #submit{border-radius: {{btnBorderRadius}};}',
					],
				],
				'scopy' => true,
			],
			'btnBorderRadiusHover' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #submit:hover{border-radius: {{btnBorderRadiusHover}};}',
					],
				],
				'scopy' => true,
			],
			'btnBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg' => 1,
					'bgType' => 'color',
					'bgDefaultColor' => '#6f14f1',
					'bgGradient' => (object) [
						"direction" => 90,
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #submit',
					],
				],
				'scopy' => true,
			],
			'btnBgHover' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #submit:hover',
					],
				],
				'scopy' => true,
			],
			'btnBoxShadow' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #submit',
					],
				],
				'scopy' => true,
			],
			'btnBoxShadowHover' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-comment #commentform #submit:hover',
					],
				],
				'scopy' => true,
			],
		);
	
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-post-comment', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_post_comment_render_callback'
    ) );
}
add_action( 'init', 'tpgb_post_comment_content' );

function tpgb_comment_args( $attr = []){
	$commentFormTitle = (!empty($attr) && !empty($attr['commentFormTitle'])) ? $attr['commentFormTitle'] : '';
	$loggedInAsText = (!empty($attr) && !empty($attr['loggedInAsText'])) ? $attr['loggedInAsText'] : '';
	$logOutText = (!empty($attr) && !empty($attr['logOutText'])) ? $attr['logOutText'] : '';
	$cancelReplyText = (!empty($attr) && !empty($attr['cancelReplyText'])) ? $attr['cancelReplyText'] : '';
	$submitBtnText = (!empty($attr) && !empty($attr['submitBtnText'])) ? $attr['submitBtnText'] : '';
	$commentField = (!empty($attr) && !empty($attr['commentField'])) ? $attr['commentField'] : '';
	$user          = wp_get_current_user();
	$user_identity = $user->exists() ? $user->display_name : '';
	$args = array(
	  'id_form'           => 'commentform',
	  'class_form' => 'comment-form',
	  'id_submit'         => 'submit',
	  'title_reply'       => wp_kses_post($commentFormTitle),
	  'title_reply_to'    => wp_kses_post($commentFormTitle) . esc_html__( ' %s', 'tpgb' ),
	  'cancel_reply_link' => wp_kses_post($cancelReplyText),
	  'label_submit'      => wp_kses_post($submitBtnText),

	  'comment_field' =>  '<div class="tpgb-row"><div class="tpgb-col-md-12 tpgb-col"><label><textarea id="comment" name="comment" cols="45" rows="6" placeholder="'.wp_kses_post($commentField).'" aria-required="true"></textarea></label></div></div>',

	  'must_log_in' => '<p class="must-log-in">' .
		sprintf(
		  esc_html__( 'You must be %1$slogged in%2$s to post a comment.', 'tpgb' ),
		  '<a href="'.wp_login_url( apply_filters( "the_permalink", get_permalink() ) ).'">',
		  '</a>'
		) . '</p>',

	  'logged_in_as' => '<p class="logged-in-as">' .
		sprintf(
			wp_kses_post($loggedInAsText).esc_html__( ' %1$s%2$s. %3$s%4$s%5$s', 'tpgb' ),
		  '<a href="'.admin_url( "profile.php" ).'">'.$user_identity,
		  '</a>',
		  '<a href="'.wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ).'" title="'.wp_kses_post($logOutText).'">',
		  wp_kses_post($logOutText),
		  '</a>'
		) . '</p>',

	  'comment_notes_before' => '',

	  'comment_notes_after' => '',

	);
	return $args;
}

function tpgb_move_comment_field_to_bottom( $fields ) {
	$comment_field = $fields['comment'];
	unset( $fields['comment'] );
	$fields['comment'] = $comment_field;
	return $fields;
} 
add_filter( 'comment_form_fields', 'tpgb_move_comment_field_to_bottom' );

function tpgb_comment_form_field( $fields ){

	$commenter = wp_get_current_commenter();
	$fields['author'] ='<div class="tpgb-col"><label>' .
		  '<input id="author" name="author" type="text" placeholder="'.esc_attr__('Name','tpgb').'" value="' . esc_attr( $commenter['comment_author'] ) .
		  '" size="30" /></label></div>';
	
	$fields['email'] ='<div class="tpgb-md-pl15 tpgb-col"><label>' .
		  '<input id="email" name="email" type="text" placeholder="'.esc_attr__('Email Address *','tpgb').'" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" /></label></div>';
	
	$fields['url'] ='<div class="tpgb-md-pl15 tpgb-col"><label>' .
		  '<input id="url" name="url" type="text" placeholder="'.esc_attr__('Website','tpgb').'" value="' . esc_attr( $commenter['comment_author_url'] ) .
		  '" size="30" /></label></div>';
	return $fields;
}
add_filter( 'comment_form_default_fields', 'tpgb_comment_form_field',11 );

function tpgb_comment_before_fields(){
	echo '<div class="tpgb-row">';
}
add_action( 'comment_form_before_fields', 'tpgb_comment_before_fields' );
	
function tpgb_comment_after_fields(){
	echo '</div>';
}
add_action( 'comment_form_after_fields', 'tpgb_comment_after_fields' );

//remove comment cookies field form
remove_action( 'set_comment_cookies', 'wp_set_comment_cookies' );