<?php
/**
 * The discography template file.
 *
 * @author WolfThemes
 * @category Core
 * @package WolfDiscography/Templates
 * @version 1.5.1
 */


defined( 'ABSPATH' ) || exit;

get_header();

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
				'el_id'             => 'discography-index',
				'post_type'         => 'release',
				'pagination'        => wolf_get_release_option( 'release_pagination', '' ),
				'releases_per_page' => wolf_get_release_option( 'releases_per_page', '' ),
				'grid_padding'      => wolf_get_release_option( 'release_grid_padding', 'yes' ),
				'item_animation'    => wolf_get_release_option( 'release_item_animation' ),
			)
		);

	get_sidebar( 'discography' );

	/**
	 * wolf_discography_after_main_content hook
	 *
	 * @hooked wolf_discography_output_content_wrapper_end - 10 (outputs closing divs for the content)
	 */
	do_action('wolf_discography_after_main_content');

get_footer();