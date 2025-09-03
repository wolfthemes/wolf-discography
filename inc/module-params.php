<?php
/**
 * Release module parameters
 *
 * @package WordPress
 * @subpackage %NAME%
 * @version %VERSION%
 */

defined( 'ABSPATH' ) || exit;

/**
 * Release Index
 */
function wd_release_index_params() {

	/**
	 * Filters the release post module parameters
	 *
	 * @since 1.0.0
	 */
	return apply_filters(
		'wd_release_index_params',
		array(
			'properties' => array(
				'name'          => esc_html__( 'Releases', 'wolf-discography' ),
				'description'   => esc_html__( 'Display your releases using the theme layouts', 'wolf-discography' ),
				'vc_base'       => 'wd_release_index',
				'el_base'       => 'release-index',
				'vc_category'   => esc_html__( 'Content', 'wolf-discography' ),
				'el_categories' => array( 'post-modules' ),
				'icon'          => 'linea-arrows linea-arrows-squares',
				'weight'        => 999,
			),

			'params'     => array(

			),
		),
	);
}