<?php
/**
 * The discography template file.
 *
 * @author WolfThemes
 * @category Core
 * @package WolfDiscography/Templates
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;
get_header( 'discography' );
	/**
	 * wolf_discography_before_main_content hook
	 *
	 * @hooked wolf_discography_output_content_wrapper - 10 (outputs opening divs for the content)
	 */
	do_action( 'wolf_discography_before_main_content' );

		/**
		 * Output post loop through hook so we can do the magic however we want
		 */
		do_action(
			'wolf_discography_posts',
			array(
				'el_id' => 'discography-index',
			)
		);

		get_sidebar( 'discography' );

		/**
		 * wolf_discography_after_main_content hook
		 *
		 * @hooked wolf_discography_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'wolf_discography_after_main_content' );

		get_footer( 'discography' );
