<?php
/**
 * Hooks
 *
 * @package WolfDiscography
 * @subpackage Frontend
 * @since 2.0.0
 */

namespace WolfDiscography\Frontend;

defined( 'ABSPATH' ) || exit;

class Hooks {

	/**
	 * Constructor
	 */
	public function __construct() {

		/**
		 * Body class
		 *
		 * @see  wd_body_class()
		 */
		add_filter( 'body_class', 'wd_body_class' );

		/**
		 * WP Header
		 *
		 * @see  wd_generator_tag()
		 */
		add_action( 'get_the_generator_html', 'wd_generator_tag', 10, 2 );
		add_action( 'get_the_generator_xhtml', 'wd_generator_tag', 10, 2 );

		add_action( 'wolf_release_start', 'wd_release_microdata' );

		/**
		 * Content wrappers
		 *
		 * @see wolf_discography_output_content_wrapper()
		 * @see wolf_discography_output_content_wrapper_end()
		 */
		add_action( 'wolf_discography_before_main_content', 'wolf_discography_output_content_wrapper', 10 );
		add_action( 'wolf_discography_after_main_content', 'wolf_discography_output_content_wrapper_end', 10 );

		add_action( 'wolf_discography_single_content', 'wolf_discography_output_single_content', 10 );
	}
}
