<<?php echo Tp_Blocks_Helper::validate_html_tag($titleTag); ?> class="tpgb-post-title tpgb-dynamic-tran">
	<?php 
		$title = get_the_title();
		if( $titleByLimit === 'words' ){
	
			$title_words = explode(' ', get_the_title(), $titleLimit);
			if (count($title_words)>=$titleLimit) {
				array_pop($title_words);
				$title = implode(" ",$title_words).'...';
			} else {
				$title = implode(" ",$title_words);
			}
			
		}else if( $titleByLimit === 'letters' ){
		
			$title_words = substr(get_the_title(),0,$titleLimit);
			if(strlen($title)>$titleLimit){
				$title = $title_words.'...';
			}else{
				$title = $title_words;
			}
			
		}
	?>
	<a href="<?php echo esc_url(get_the_permalink()); ?>"><?php echo wp_kses_post($title); ?></a>
</<?php echo Tp_Blocks_Helper::validate_html_tag($titleTag); ?>>