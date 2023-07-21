<?php
namespace MMP\Widget;

use MMP\Maps_Marker_Pro as MMP;

class Shortcode extends \WP_Widget {
	/**
	 * Sets up the class
	 *
	 * @since 4.0
	 */
	public function __construct() {
		parent::__construct(
			'mmp_shortcode',
			'Maps Marker Pro - ' . esc_html__('Shortcode'),
			array(
				'description' => esc_html__('Adds a map shortcode.', 'mmp')
			)
		);
	}

	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		add_action('widgets_init', function() {
			register_widget('MMP\Widget\Shortcode');
		});
	}

	/**
	 * Displays the widget on the frontend
	 *
	 * @since 4.0
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments
	 * @param array $instance Saved widget values
	 */
	public function widget($args, $instance) {
		$map_id = (isset($instance['map'])) ? absint($instance['map']) : 0;

		if (!$map_id) {
			return;
		}

		echo do_shortcode('[' . MMP::$settings['shortcode'] . ' map="' . $map_id . '"]');
	}

	/**
	 * Displays the widget form on the backend
	 *
	 * @since 4.0
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Saved widget values
	 */
	public function form($instance) {
		$db = MMP::get_instance('MMP\DB');

		$map_id = (isset($instance['map'])) ? absint($instance['map']) : 0;

		$maps = $db->get_all_maps();

		?>
		<p>
			<select class="widefat" id="<?= $this->get_field_id('map') ?>" name="<?= $this->get_field_name('map') ?>">
				<option value="0" <?php selected($map_id, '0') ?>><?= esc_html__('Please select the map you want to display', 'mmp') ?></option>
				<?php foreach ($maps as $map): ?>
					<option value="<?= $map->id ?>" <?php selected($map_id, $map->id) ?>>
						[<?= $map->id ?>] <?= ($map->name) ? esc_html($map->name) : esc_html__('(no name)', 'mmp') ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	/**
	 * Proccesses the saving of the widget values
	 *
	 * @since 4.0
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Current widget values
	 * @param array $old_instance Saved widget values
	 */
	public function update($new_instance, $old_instance) {
		$instance['map'] = (isset($new_instance['map'])) ? absint($new_instance['map']) : 0;

		return $instance;
	}

}
