<?php
/**
 * Options Configuration
 *
 * Defines all options configurations for the plugin
 *
 * @package WolfDiscography
 * @subpackage Config
 * @since 2.0.0
 */

namespace Wolf_Discography\Config;

use Wolf_Discography\Admin\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Metabox Configuration Class
 */
class Options_Params {

	/**
	 * Get panel settings configuration
	 *
	 * @return array Settings configuration
	 */
	public static function get_config( $options_instance = null ) {
		$settings = array(
			// General Tab
			'discography_page' => array(
				'label'       => esc_html__( 'Discography Page', 'wolf-discography' ),
				'type'        => 'page_select',
				'tab'         => 'general',
				'description' => esc_html__( 'Select the page that will display your discography.', 'wolf-discography' ),
				/* Force to display legacy option for better user experience */
				'default'     => Options::get_option( 'discography_page' ),
			),
			'posts_per_page'     => array(
				'label'       => esc_html__( 'Posts per Page', 'wolf-discography' ),
				'type'        => 'number',
				'tab'         => 'general',
				'default'     => 12,
			),
			'pagination'      => array(
				'label'       => esc_html__( 'Pagination Type', 'wolf-discography' ),
				'type'        => 'select',
				'tab'         => 'general',
				'choices'     => array(
					'none' => esc_html__( 'None', 'wolf-discography' ),
					'numbers' => esc_html__( 'Numbered', 'wolf-discography' ),
				),
				'default'     => 'list',
			),
			'use_band_tax'     => array(
				'label'       => esc_html__( 'Link Artist Names', 'wolf-discography' ),
				'type'        => 'checkbox',
				'tab'         => 'general',
				'description' => esc_html__( 'Make artist names clickable links to show all releases by that artist', 'wolf-discography' ),
				'default'     => 1,
			),
			'use_label_tax'    => array(
				'label'       => esc_html__( 'Link Label Names', 'wolf-discography' ),
				'type'        => 'checkbox',
				'tab'         => 'general',
				'description' => esc_html__( 'Make label names clickable links to show all releases by that label', 'wolf-discography' ),
				'default'     => 1,
			),
			'use_genre_tax'    => array(
				'label'       => esc_html__( 'Link Genre Names', 'wolf-discography' ),
				'type'        => 'checkbox',
				'tab'         => 'general',
				'description' => esc_html__( 'Make genre names clickable links to show all releases in that genre', 'wolf-discography' ),
				'default'     => 1,
			),
			'display_format'   => array(
				'label'       => esc_html__( 'Display Release Format', 'wolf-discography' ),
				'type'        => 'checkbox',
				'tab'         => 'general',
				'description' => esc_html__( 'Display the release format (CD, Digital Download, Vinyl, etc.)', 'wolf-discography' ),
				'default'     => 1,
			),
		);

		// Add display options only for non-Wolf themes
		// if ( WD()->theme_supports_v2() ) {
			$settings['display_style'] = array(
				'label'       => esc_html__( 'Display Style', 'wolf-discography' ),
				'type'        => 'select',
				'tab'         => 'display',
				'description' => esc_html__( 'Choose how to display your releases.', 'wolf-discography' ),
				'choices'     => array(
					'list' => esc_html__( 'List View', 'wolf-discography' ),
					'grid' => esc_html__( 'Grid View', 'wolf-discography' ),
				),
				'default'     => 'list',
			);

			$settings['grid_columns'] = array(
				'label'       => esc_html__( 'Grid Columns', 'wolf-discography' ),
				'type'        => 'select',
				'tab'         => 'display',
				'description' => esc_html__( 'Number of columns when using Grid view.', 'wolf-discography' ),
				'choices'     => array(
					'2' => esc_html__( '2 Columns', 'wolf-discography' ),
					'3' => esc_html__( '3 Columns', 'wolf-discography' ),
					'4' => esc_html__( '4 Columns', 'wolf-discography' ),
				),
				'default'     => '3',
				'depends_on'  => array(
					'field' => 'display_style',
					'value' => 'grid',
				),
			);
			$settings['layout']       = array(
				'label'      => esc_html__( 'Layout', 'wolf-discography' ),
				'type'       => 'select',
				'tab'        => 'display',
				'choices'    => array(
					'standard' => esc_html__( 'Standard', 'wolf-discography' ),
					'overlay'  => esc_html__( 'Overlay', 'wolf-discography' ),
				),
				'default'    => '',
				'depends_on' => array(
					'field' => 'display_style',
					'value' => 'grid',
				),
			);

			// }

			// License Tab
			$settings['license_key'] = array(
				'label'             => esc_html__( 'License Key', 'wolf-discography' ),
				'type'              => 'license',
				'tab'               => 'license',
				'description'       => sprintf(
					esc_html__( 'Enter your Envato purchase code. %1$sFind your purchase code%2$s', 'wolf-discography' ),
					'<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Can-I-Find-my-Purchase-Code-" target="_blank">',
					'</a>'
				),
				'placeholder'       => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx',
				'sanitize_callback' => $options_instance ? array( $options_instance, 'sanitize_license_field' ) : 'sanitize_text_field',

				'default'           => '',
			);

			return $settings;
	}
}