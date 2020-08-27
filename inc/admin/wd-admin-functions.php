<?php
/**
 * Discography admin functions
 *
 * Functions available on admin
 *
 * @author WolfThemes
 * @category Core
 * @package WolfDiscography/Core
 * @version 1.5.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display archive page state
 *
 * @param array $states
 * @param object $post
 * @return array $states
 */
function wd_custom_post_states( $states, $post ) { 

	if ( 'page' == get_post_type( $post->ID ) && absint( $post->ID ) === wolf_discography_get_page_id() ) {

		$states[] = esc_html__( 'Discography Page' );
	} 

	return $states;
}
add_filter( 'display_post_states', 'wd_custom_post_states', 10, 2 );