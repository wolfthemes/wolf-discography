<?php
/**
 * Release module parameters
 *
 * @package WordPress
 * @subpackage %NAME%
 * @version %VERSION%
 * @since 1.6.0
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
				'vc_base'       => 'wvc_release_index',
				'el_base'       => 'release-index',
				'vc_category'   => esc_html__( 'Content', 'wolf-discography' ),
				'el_categories' => array( 'post-modules' ),
				'icon'          => 'wolf-release-elementor-icon',
				'weight'        => 999,
			),

			'params'     => array(

				'release_display' => array(
					'param_name'  => 'release_display',
					'label'       => esc_html__( 'Release Display', 'wolf-discography' ),
					'type'        => 'select',

					/**
					 * Filters the release post display option
					 *
					 * @since 1.0.0
					 */
					'options'     => apply_filters(
						'wd_release_display_options',
						array(
							'grid' => esc_html__( 'Grid', 'wolf-discography' ),
							'list' => esc_html__( 'List', 'wolf-discography' ),
						)
					),
					'default'     => 'list',
					'admin_label' => true,
				),

				'columns' => array(
					'param_name'  => 'columns',
					'label'       => esc_html__( 'Columns', 'wolf-discography' ),
					'type'        => 'select',
					'options'     => array(
						3         => esc_html__( 'Three', 'wolf-discography' ),
						2         => esc_html__( 'Two', 'wolf-discography' ),
						4         => esc_html__( 'Four', 'wolf-discography' ),
						5         => esc_html__( 'Five', 'wolf-discography' ),
						6         => esc_html__( 'Six', 'wolf-discography' ),
						1         => esc_html__( 'One', 'wolf-discography' ),
					),
					'default'     => 3,
					'admin_label' => true,
					'condition'   => array(
						'release_display' => array( 'grid', 'animated_cover' ),
					),
					// 'group' => esc_html__( 'Extra', 'wolf-discography' ),
				),

				'release_layout' => array(
					'param_name'  => 'release_layout',
					'label'       => esc_html__( 'Layout', 'wolf-discography' ),
					'type'        => 'select',

					/**
					 * Filters the release post layout option
					 *
					 * @since 1.0.0
					 */
					'options'     => apply_filters(
						'wolftheme_release_layout_options',
						array(
							'standard' => esc_html__( 'Classic', 'wolf-discography' ),
							'overlay'  => esc_html__( 'Overlay', 'wolf-discography' ),
						)
					),
					'default'     => 'standard',
					'admin_label' => true,
					'condition'   => array(
						'release_display' => array( 'grid', 'metro', 'masonry' ),
					),
				),

				'release_module' =>array(
					'param_name'  => 'release_module',
					'label'       => esc_html__( 'Module', 'wolf-discography' ),
					'type'        => 'select',
					'options'     => array(
						'grid'     => esc_html__( 'Grid', 'wolf-discography' ),
						'carousel' => esc_html__( 'Carousel', 'wolf-discography' ),
					),
					'description' => esc_html__( 'The carousel is not visible in preview mode yet.', 'wolf-discography' ),
					'default'     => 'grid',
					'admin_label' => true,
					'condition'   => array(
						'release_display' => array( 'grid', 'animated_cover' ),
					),
				),

				'release_custom_thumbnail_size' => array(
					'param_name'  => 'release_custom_thumbnail_size',
					'label'       => esc_html__( 'Custom Thumbnail Size', 'wolf-discography' ),
					'type'        => 'text',
					'admin_label' => true,
					'placeholder' => '450x450',
					'ai' => array(
						'active' => false,
					),
				),

				'grid_padding' => array(
					'param_name'  => 'grid_padding',
					'label'       => esc_html__( 'Padding', 'wolf-discography' ),
					'type'        => 'select',
					'options'     => array(
						'yes' => esc_html__( 'Yes', 'wolf-discography' ),
						'no'  => esc_html__( 'No', 'wolf-discography' ),
					),
					'default'     => 'yes',
					'admin_label' => true,
					'condition'   => array(
						'release_layout' => array( 'standard', 'overlay', 'label' ),
						'release_display' => array( 'grid', 'metro', ),
					),
				),
			),
		),
	);
}