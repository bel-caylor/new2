<?php 
/**
 * The Plus Blocks Registered Lists
 *
 * @since   1.0.0
 * @package TPGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

Class Tpgb_Get_Blocks {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */	
	private static $instance = null;
	
	public $transient_blocks = [];
	public $post_type = [];
	public $post_id = [];
	
	public $templates_ids = [];

	public $preload_name = '';
	
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
	 */
	public function __construct( $post_type = '', $post_id = '' ) {
		if( !empty($post_id) ){
			$this->post_type = $post_type;
			$this->post_id = intval( $post_id );
			$this->tpgb_post_block_css( $post_type, $this->post_id );
		}
	}

	/**
	 * get ids for all wp template part
	 * @since 2.0.3
	 */
	public function get_fse_template_part( $block ) {
		if(!empty($block['attrs']) && !empty($block['attrs']['slug']) ){
			$slug = $block['attrs']['slug'];
			$templates_parts = get_block_templates( array( 'slugs__in' => $slug ), 'wp_template_part' );
			foreach ( $templates_parts as $templates_part ) {
				if ( $slug === $templates_part->slug ) {
					$temp_id = $templates_part->wp_id;
					return $temp_id;
				}
			}
		}
	}

	/**
	 * Get Reference ID
	 * @since 1.1.1
	 */
	public function block_reference_id( $res_blocks ) {
		$ref_id = array();
		if ( ! empty( $res_blocks ) ) {
			foreach ( $res_blocks as $key => $block ) {
				if ( $block['blockName'] == 'core/block' ) {
					if(isset($block['attrs']['ref'])){
						$ref_id[] = $block['attrs']['ref'];
					}
				}else if ( $block['blockName'] === 'core/template-part') {
					$temp_id = $this->get_fse_template_part( $block );
					if ( !empty($temp_id) ) {
						$ref_id[] = $temp_id;
					}
				}
				$this->render_block($block);
				if ( count( $block['innerBlocks'] ) > 0 ) {
					$ref_id = array_merge( $this->block_reference_id( $block['innerBlocks'] ), $ref_id );
				}
			}
		}
		return $ref_id;
	}
	
	/*
	 * Frontend Post Block Load Css
	 * @since 1.1.1
	 */
	public function tpgb_post_block_css( $post_type = '' , $post_id = 0 ){

		if ( $post_id != '' ) {
			$post_content = get_post( $post_id );
			if ( isset( $post_content->post_content ) ) {
				$content = $post_content->post_content;
				$parse_blocks = parse_blocks( $content );
				$res_id = $this->block_reference_id( $parse_blocks );
				if ( is_array( $res_id ) && ! empty( $res_id )) {
					$res_id = array_unique( $res_id );
					$this->templates_ids = $res_id;
				}
			}
			
			$this->transient_blocks = $this->get_blocks_elements( $this->transient_blocks );
			$this->preload_name = $post_id;
			// if no cache files, generate new
			if(class_exists('Tpgb_Library')){

				//current page/post load all templates one time load elements
				if( isset($this->transient_blocks) && !empty($this->transient_blocks) ){
					
					if(isset(tpgb_load_data()->post_assets_objects['elements'])){
						$elements = tpgb_load_data()->post_assets_objects['elements'];
					}else{
						$elements = [];
					}
					$different_elements = array_diff($this->transient_blocks, $elements);
					if($this->transient_blocks != $different_elements){
						$this->preload_name = get_queried_object_id().'-'.$post_id;
					}
					tpgb_load_data()->post_assets_objects['elements'] = array_unique(array_merge($elements, $this->transient_blocks ) );
					$this->transient_blocks = $different_elements;
				}

				if (get_option('tpgb_save_updated_at') == get_option('theplus-' . $post_type . '-' . $this->preload_name . '_updated_at')){
					return false;
				}
				//remove page/post generate files
				tpgb_library()->remove_files_unlink( $post_type, $this->preload_name, ['css'] , true );

				//regenerate files page/post
				if ( !tpgb_library()->check_cache_files( $post_type, $this->preload_name, 'css', true ) && tpgb_library()->get_caching_option() == false ) {
					tpgb_library()->plus_generate_scripts( $this->transient_blocks, 'theplus-preload-' . $post_type . '-' . $this->preload_name, ['css'], false );
				}
			}
		}

	}
	
	/*
	 * Enqueue Styles preloaded
	 * @since 2.0.0
	 */
	public function enqueue_scripts( $dependency = false) {
		$plus_version = get_post_meta( $this->post_id, '_block_css', true );
		if( empty($plus_version) ){
			$plus_version = time();
		}
		$localize = '';
		if(tpgb_library()->get_caching_option() == false){
			if ( tpgb_library()->check_css_js_cache_files( $this->post_type, $this->preload_name, 'css', true ) ) {
				$css_file = TPGB_ASSET_URL . '/theplus-preload-' . $this->post_type . '-' . $this->preload_name . '.min.css';
				$enqueue_name = 'tpgb-plus-'.$this->post_type . '-' . $this->preload_name;
				if( $dependency == false ){
					$enqueue_name = 'tpgb-plus-block-front-css';
					$dependency = ['plus-global'];
					wp_enqueue_style( 'tpgb-common',tpgb_library()->pathurl_security( TPGB_URL . "/assets/css/main/general/tpgb-common.css" ), $dependency, $plus_version );
				}else{
					$dependency = ['tpgb-plus-block-front-css'];
				}
				wp_enqueue_style( $enqueue_name,tpgb_library()->pathurl_security($css_file), $dependency, $plus_version );
			}
		}else if(tpgb_library()->get_caching_option() == 'separate'){
			$tpgb_path = TPGB_PATH . DIRECTORY_SEPARATOR;
			$tpgb_url = TPGB_URL;
			$separate_path = tpgb_library()->load_separate_file($this->transient_blocks);
			if(isset($separate_path['css']) && !empty($separate_path['css'])){
				foreach( $separate_path['css'] as $key => $path ){
					if(is_readable(tpgb_library()->secure_path_url($path))){
						$css_sep_url = str_replace( $tpgb_path, $tpgb_url, $path);
						if(defined('TPGBP_VERSION') && defined('TPGBP_URL')){
							$css_sep_url = str_replace( TPGBP_PATH . DIRECTORY_SEPARATOR, TPGBP_URL, $css_sep_url);
						}
						$css_file_key = basename($css_sep_url, ".css");
						$css_file_key = basename($css_file_key, ".min");
						$lastFolder = basename(dirname($css_sep_url));
						$enq_name = 'theplus-'.$css_file_key.'-'.$lastFolder;
						wp_enqueue_style( $enq_name , tpgb_library()->pathurl_security($css_sep_url), false,$plus_version);
					}
				}
			}
			if(isset($separate_path['js']) && !empty($separate_path['js'])){
				$iji = 0;
				foreach( $separate_path['js'] as $key => $path ){
					if(is_readable(tpgb_library()->secure_path_url($path))){
						$js_sep_url = str_replace( $tpgb_path, $tpgb_url, $path);
						if(defined('TPGBP_VERSION') && defined('TPGBP_URL')){
							$js_sep_url = str_replace( TPGBP_PATH . DIRECTORY_SEPARATOR, TPGBP_URL, $js_sep_url);
						}
						$js_file_key = basename($js_sep_url, ".js");
						$js_file_key = basename($js_file_key, ".min");
						if($iji === 0){
							$localize = 'theplus-'.$js_file_key;
						}
						wp_enqueue_script( 'theplus-'.$js_file_key, tpgb_library()->pathurl_security($js_sep_url), ['jquery'], $plus_version, true);
						$iji++;
					}
				}
			}
			return $localize;
		}
	}

	/**
	 * post content of a single block.
	 */
	public function render_block( $block ) {
		if ( $block['blockName'] ) {
			$options = (!empty($block['attrs'])) ? $block['attrs'] : '';
			$this->plus_blocks_options( $options, $block['blockName'] );
			$this->transient_blocks[] = $block['blockName'];
		}
	}
	
	/*
	 * Get Blocks Elements
	 * @since 2.0.0
	 */
	public function get_blocks_elements( $block_lists = []){
		$elements = [];
		if(!empty($block_lists)){
			$replace = array();
			$elements = array_map(function ($val) use ($replace) {
				return (array_key_exists($val, $replace) ? $replace[$val] : $val);
			}, $block_lists);
			$elements = array_intersect(array_keys(tpgb_registered_blocks()), $elements);
	
			$elements = array_unique($elements);
			sort($elements);
		}
		return $elements;
	}

	/*
	 * List of Blocks Condition Check
	 *
	 * @since 1.1.1
	 */
	public function plus_blocks_options($options='' , $blockname=''){
		
		if(!empty($options) && !empty($options['contentHoverEffect'])){
			$this->transient_blocks[] = 'content-hover-effect';
		}
		if(!empty($options) && !empty($options['globalAnim']) && ((!empty($options['globalAnim']['md']) && $options['globalAnim']['md']!='none') || (!empty($options['globalAnim']['sm']) && $options['globalAnim']['sm']!='none') || (!empty($options['globalAnim']['xs']) && $options['globalAnim']['xs']!='none'))){
			$this->transient_blocks[] = 'tpgb-animation';
			if(isset($options['globalAnim']['md']) && $options['globalAnim']['md']!='none'){
				$this->transient_blocks[] = 'tpgb-animation-'.$options['globalAnim']['md'];
			}
			if(isset($options['globalAnim']['sm']) && $options['globalAnim']['sm']!='none'){
				$this->transient_blocks[] = 'tpgb-animation-'.$options['globalAnim']['sm'];
			}
			if(isset($options['globalAnim']['xs']) && $options['globalAnim']['xs']!='none'){
				$this->transient_blocks[] = 'tpgb-animation-'.$options['globalAnim']['xs'];
			}
		}
		
		// Row Column Link Js
		if(($blockname=='tpgb/tp-row' || $blockname=='tpgb/tp-column' || $blockname=='tpgb/tp-container' || $blockname=='tpgb/tp-container-inner' ) && !empty($options) && !empty($options['wrapLink']) && $options['wrapLink'] == true ) {
            $this->transient_blocks[] = 'tpgb-row-column-link';
        }

		if(!empty($options) && !empty($options['layoutType']) && $options['layoutType']=='carousel'){	//infobox / flipbox
			$this->transient_blocks[] = 'carouselSlider';
		}
		if(!empty($options) && !empty($options['extBtnshow'])){
			$this->transient_blocks[] = 'tpgb-group-button';
		}
		if($blockname=='tpgb/tp-countdown') {
			$this->transient_blocks[] = 'countdown-style-1';
		}
		if($blockname=='tpgb/tp-flipbox' && !empty($options) && (!empty($options['backBtn']) || !empty($options['backCarouselBtn']))){
			$this->transient_blocks[] = 'tpgb-group-button';
		}
		/* Heading Title */
		if($blockname=='tpgb/tp-heading-title'){
			if(!empty($options) && !empty($options['style'])){
				if($options['style']=='style-8'){
					$this->transient_blocks[] = 'tpx-heading-title-style-3';
				}
				$this->transient_blocks[] = 'tpx-heading-title-'.$options['style'];
			}else{
				$this->transient_blocks[] = 'tpx-heading-title-style-1';
			}
		}
		//Post Listing
		if($blockname=='tpgb/tp-post-listing') {
			if(!empty($options) && !empty($options['postLodop']) && $options['postLodop'] == 'pagination' ){
				$this->transient_blocks[] = 'tpgb-pagination';
			}

			if(!empty($options) && !empty($options['style']) ){
				$this->transient_blocks[] = 'tpx-post-listing-'.$options['style'];
			}else{
					$this->transient_blocks[] = 'tpx-post-listing-style-1';
			}

		}
		
		//External-Form-Styler
		if($blockname=='tpgb/tp-external-form-styler' && !empty($options)) {
			if(!empty($options['formType'])){
				$this->transient_blocks[] = 'tpx-'.$options['formType'];
			}else{
				$this->transient_blocks[] = 'tpx-contact-form-7';
			}
		}
		//Social Feed
		if($blockname=='tpgb/tp-social-feed' && !empty($options)) {
			if(!empty($options['style']) && ($options['style']=='style-3' || $options['style']=='style-4')){
				$this->transient_blocks[] = 'tpx-social-feed-'.$options['style'];
			}
		}
		//social Review
		if($blockname=='tpgb/tp-social-reviews' && !empty($options)){
			if(!empty($options['RType'])){
				if(!empty($options['style'])){
					$this->transient_blocks[] = 'tpx-review-'.$options['style'];
				}else{
					$this->transient_blocks[] = 'tpx-review-style-1';
				}
			}else{
				if(!empty($options['style'])){
					$this->transient_blocks[] = 'tpx-review-'.$options['style'];
				}else{
					$this->transient_blocks[] = 'tpx-review-style-1';
				}
			}
		}
		//Social Icons
		if($blockname=='tpgb/tp-social-icons' && !empty($options)) {
			if(!empty($options['style'])){
				$this->transient_blocks[] = 'tpx-social-icons-'.$options['style'];
			}else{
				$this->transient_blocks[] = 'tpx-social-icons-style-1';
			}
		}
		//Progress Bar
		if($blockname=='tpgb/tp-progress-bar' && !empty($options)) {
			if(!empty($options['layoutType'])){
				$this->transient_blocks[] = 'tpx-'.$options['layoutType'];
			}else{
				$this->transient_blocks[] = 'tpx-progressbar';
			}
		}
		//Pricing List
		if($blockname=='tpgb/tp-pricing-list' && !empty($options)) {
			if(!empty($options['style'])){
				$this->transient_blocks[] = 'tpx-pricing-list-'.$options['style'];
			}else{
				$this->transient_blocks[] = 'tpx-pricing-list-style-1';
			}
			
			if(!empty($options['imgShape']) && $options['imgShape']=='custom'){
				$this->transient_blocks[] = 'tpx-pricing-list-masking';
			}
		}

		//Svg Icon Load 
		if( ($blockname=='tpgb/tp-flipbox' && !empty($options) && !empty($options['iconType']) && $options['iconType'] == 'svg') || ($blockname=='tpgb/tp-infobox' && !empty($options) && !empty($options['iconType']) && $options['iconType'] == 'svg') || ($blockname=='tpgb/tp-number-counter' && !empty($options) && !empty($options['iconType']) && $options['iconType'] == 'svg') || ($blockname=='tpgb/tp-pricing-table' && !empty($options) && !empty($options['iconType']) && $options['iconType'] == 'svg') ){
			$this->transient_blocks[] = 'tpgb-draw-svg';
		}
		
		// tpgb-fancy-box
		if($blockname=='tpgb/tp-post-image' && !empty($options) && !empty($options['fancyBox']) && $options['fancyBox'] == true ) {
			$this->transient_blocks[] = 'tpgb-fancy-box';
		}
		if($blockname=='tpgb/tp-creative-image' && !empty($options) ) {
			if(!empty($options['fancyBox']) && $options['fancyBox'] == true){
				$this->transient_blocks[] = 'tpgb-fancy-box';
			}
			if( !empty($options['showMaskImg']) ){
				$this->transient_blocks[] = 'tpx-tp-image-mask-img';
			}
		}
		/*Button*/
		if($blockname=='tpgb/tp-button' && !empty($options) && !empty($options['btnHvrCnt']) && !empty($options['selectHvrCnt']) ) {
			$this->transient_blocks[] = 'content-hover-effect';
		}
		/* Infobox */
		if($blockname=='tpgb/tp-infobox') {
			if(!empty($options) && !empty($options['styleType'])){
				$this->transient_blocks[] = 'tpx-infobox-'.$options['styleType'];
			}else{
				$this->transient_blocks[] = 'tpx-infobox-style-1';
			}
			
			if(!empty($options) && !empty($options['contenthoverEffect'])){
				$this->transient_blocks[] = 'content-hover-effect';
			}
		}

		/* Button */
		if($blockname=='tpgb/tp-button') {
			if(!empty($options) && !empty($options['styleType'])){
				$this->transient_blocks[] = 'tpx-button-'.$options['styleType'];
			}else{
				$this->transient_blocks[] = 'tpx-button-style-1';
			}
		}

		/* Breadcrumb */
		if($blockname=='tpgb/tp-breadcrumbs') {
			if(!empty($options) && !empty($options['style'])){
				$this->transient_blocks[] = 'tpx-breadcrumbs-'.$options['style'];
			}else{
				$this->transient_blocks[] = 'tpx-breadcrumbs-style-1';
			}
		}

		/* Number Counter */
		if($blockname=='tpgb/tp-number-counter') {
			if(!empty($options) && !empty($options['style'])){
				$this->transient_blocks[] = 'tpx-number-counter-s2';
			}
		}
		/*Code Highlighter*/
		if($blockname=='tpgb/tp-code-highlighter') {
			if(!empty($options) && !empty($options['themeType'])){
			   $this->transient_blocks[] = 'tpx-'.$options['themeType'];
			}else{
			   $this->transient_blocks[] = 'tpx-prism-default';
			}
		}
		/*Dark Mode*/
		if($blockname=='tpgb/tp-dark-mode') {
			if(!empty($options) && !empty($options['dmStyle'])){
			   $this->transient_blocks[] = 'tpx-dark-mode-'.$options['dmStyle'];
			}else{
			   $this->transient_blocks[] = 'tpx-dark-mode-style-1';
			}
		}
	   
	   	/* Testimonials */
	   	if($blockname=='tpgb/tp-testimonials'){
			if(!empty($options) && !empty($options['telayout']) && $options['telayout']!='carousel'){
				$this->transient_blocks[] = 'tpgb_grid_layout';
			}
			
			if( !empty($options) && !empty($options['style']) ){
				$this->transient_blocks[] = 'tpx-testimonials-'.$options['style'];
			}else{
				$this->transient_blocks[] = 'tpx-testimonials-style-1';
			}

			if( ( !empty($options) &&  !empty($options['telayout']) && $options['telayout'] == 'grid' ) || ( isset($options['caroByheight']) && !empty( $options['caroByheight'] ) && $options['caroByheight'] == 'height' ) ){
				$this->transient_blocks[] = 'tpx-testimonials-scroll';
			}
		}

		/* Blockquote */
		if($blockname=='tpgb/tp-blockquote') {
			if(!empty($options) && !empty($options['style']) && $options['style']=='style-2'){
				$this->transient_blocks[] = 'tpx-blockquote-style-2';
			}
		}

		/*Stylist List*/
		if($blockname=='tpgb/tp-stylist-list' && !empty($options)){
			if( !empty($options['hoverInverseEffect']) ){
				$this->transient_blocks[] = 'tpx-stylist-list-hover-inverse';
			}
		}
		
		/*tabs tours*/
		if($blockname=='tpgb/tp-tabs-tours' && !empty($options) && !empty($options['tabLayout']) && $options['tabLayout']=='vertical'){
			$this->transient_blocks[] = 'tpx-tabs-tours-vertical';
		}

		/*Post Meta info*/
		if($blockname=='tpgb/tp-post-meta'){
			if(!empty($options) && !empty($options['metaLayout']) && $options['metaLayout']=='layout-2'){
				$this->transient_blocks[] = 'tpx-tp-post-layout-2';
			}
			if(!isset($options['showCategory']) || (!empty($options) && $options['showCategory']==true)){
				$this->transient_blocks[] = 'tpx-tp-post-meta-category';
			}
		}
		
		/*Post author*/
		if($blockname=='tpgb/tp-post-author'){
			if(!empty($options) && !empty($options['authorStyle']) && $options['authorStyle']=='style-2'){
				$this->transient_blocks[] = 'tpx-tp-post-author-style-2';
			}
			if(!isset($options['ShowAvatar']) || (!empty($options) && $options['ShowAvatar']==true)){
				$this->transient_blocks[] = 'tpx-tp-post-author-avatar';
			}
			if(!isset($options['ShowSocial']) || (!empty($options) && $options['ShowSocial']==true)){
				$this->transient_blocks[] = 'tpx-tp-post-author-social';
			}
			if(!isset($options['ShowRole']) || (!empty($options) && $options['ShowRole']==true)){
				$this->transient_blocks[] = 'tpx-tp-post-author-role';
			}
		}
		
		if($blockname=='tpgb/tp-video' && !empty($options) && !empty($options['ContinueAnim'])){
			$this->transient_blocks[] = 'tpgb-plus-hover-effect';
		}
		if(has_filter('tpgb_has_blocks_condition')) {
			$this->transient_blocks = apply_filters('tpgb_has_blocks_condition', $this->transient_blocks, $options, $blockname );
		}

		return $this->transient_blocks;
	}

}

Tpgb_Get_Blocks::get_instance();

/**
 * Get post assets object.
 *
 * @since 2.0.0
 */
function tpgb_get_post_assets( $post_type, $post_id ) {

	if ( ! isset( tpgb_load_data()->post_assets_objects[ $post_id ] ) ) {
		$post_obj = new Tpgb_Get_Blocks( $post_type, $post_id );
		tpgb_load_data()->post_assets_objects[ $post_id ] = $post_obj;
	}

	return tpgb_load_data()->post_assets_objects[ $post_id ];
}
?>