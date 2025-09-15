<?php
/**
 * Enqueues styles and scripts
 *
 * @package WolfDiscography
 * @subpackage Frontend
 * @since 2.0.0
 */

namespace WolfDiscography\Frontend;

defined( 'ABSPATH' ) || exit;

class Enqueues {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Enqeue default style
	 *
	 * @since 1.2.6
	 */
	public function enqueue_styles() {

		if ( ! WD()->is_wolf_theme() ) {
			wp_enqueue_style( 'wolf-discography', WD_URI . '/build/styles.css', array(), WD_VERSION, 'all' );
		}
	}
}
