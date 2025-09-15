<?php
/**
 * Release Index Elementor Widget
 *
 * @package WolfDiscography
 * @subpackage Elementor
 * @since 2.0.0
 */

use WolfDiscography\Config\ModuleParams;
use WolfDiscography\PageBuilders\ElementorHelper;

defined( 'ABSPATH' ) || exit;
defined( 'ELEMENTOR_VERSION' ) || exit;

class Wolf_Discography_Elementor_Release_Index_Widget extends \Elementor\Widget_Base {

	/**
	 * Element parameters
	 *
	 * @var array
	 */
	public $params = array();

	/**
	 * Element scripts
	 *
	 * @var array
	 */
	public $scripts = array();

	/**
	 * Constructor
	 *
	 * @param array $data
	 * @param mixed $args
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		// Load parameters using namespaced config
		$this->params = ModuleParams::get_release_index_params();
	}

	/**
	 * Get widget name
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->params['properties']['el_base'] ?? 'release-index';
	}

	/**
	 * Get widget title
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->params['properties']['name'] ?? __( 'Releases', 'wolf-discography' );
	}

	/**
	 * Get widget icon
	 *
	 * @return string
	 */
	public function get_icon() {
		return $this->params['properties']['icon'] ?? 'wolf-release-elementor-icon';
	}

	/**
	 * Get widget categories
	 *
	 * @return array
	 */
	public function get_categories() {
		return $this->params['properties']['el_categories'] ?? array( 'post-modules' );
	}

	/**
	 * Get widget keywords
	 *
	 * @return array
	 */
	public function get_keywords() {
		return $this->params['properties']['keywords'] ?? array( 'release', 'discography', 'music' );
	}

	/**
	 * Get script dependencies
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return $this->scripts;
	}

	/**
	 * Register widget controls
	 */
	protected function register_controls() {
		// Use the namespaced helper class instead of global function
		ElementorHelper::register_elementor_controls( $this );
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$atts              = $this->get_settings_for_display();
		$atts['post_type'] = 'release';
		$atts['context']   = 'elementor';

		// Uses the main post hook to display the releases
		do_action( 'wolf_discography_posts', $atts );
	}
}

// Register the widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type(
	new \Wolf_Discography_Elementor_Release_Index_Widget()
);
