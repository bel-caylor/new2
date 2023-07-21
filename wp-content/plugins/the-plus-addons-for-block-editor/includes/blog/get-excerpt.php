<?php 
defined( 'ABSPATH' ) || exit;

$excerpt = get_the_excerpt();
global $post;

	if( $excerptByLimit === 'words' ){
	
		$limit_words = explode(' ', get_the_excerpt(), $excerptLimit);
		if (count($limit_words)>=$excerptLimit) {
			array_pop($limit_words);
			$excerpt = implode(" ",$limit_words).'...';
		} else {
			$excerpt = implode(" ",$limit_words);
		}
		
	}else if( $excerptByLimit === 'letters' ){
	
		$limit_words = substr(get_the_excerpt(),0,$excerptLimit);
		if(strlen($excerpt)>$excerptLimit){
			$excerpt = $limit_words.'...';
		}else{
			$excerpt = $limit_words;
		}
		
	}
?>
<?php if(!empty($excerpt)){ ?>
	<div class="tpgb-post-excerpt tpgb-d-block tpgb-dynamic-tran"><p><?php echo $excerpt; ?></p></div>
<?php } ?>