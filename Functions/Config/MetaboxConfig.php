<?php
/**
 * Metabox Configuration
 *
 * Defines all metabox configurations for the plugin
 *
 * @package WolfDiscography
 * @subpackage Config
 * @since 2.0.0
 */

namespace WolfDiscography\Config;

defined( 'ABSPATH' ) || exit;

/**
 * Metabox Configuration Class
 */
class MetaboxConfig {

	/**
	 * Get all metabox configurations
	 *
	 * @return array
	 */
	public static function getConfig(): array {
		return array(
			'release_details' => array(
				'title'    => esc_html__( 'Release Details', 'wolf-discography' ),
				'screen'   => 'release',
				'context'  => 'normal',
				'priority' => 'high',
				'fields'   => self::get_release_detail_fields(),
			),
		);
	}

	/**
	 * Get release detail fields configuration
	 *
	 * @return array
	 */
	private static function get_release_detail_fields(): array {
		return array(
			array(
				'label' => esc_html__( 'Title', 'wolf-discography' ),
				'id'    => '_wolf_release_title',
				'type'  => 'text',
			),
			array(
				'label' => esc_html__( 'Release date', 'wolf-discography' ),
				'id'    => '_wolf_release_date',
				'type'  => 'datepicker',
			),
			array(
				'label' => esc_html__( 'Catalog Number', 'wolf-discography' ),
				'id'    => '_wolf_release_catalog_number',
				'type'  => 'text',
			),
			array(
				'label'   => esc_html__( 'Type', 'wolf-discography' ),
				'id'      => '_wolf_release_type',
				'desc'    => esc_html__( 'You can choose to not display the format in the plugin setting.', 'wolf-discography' ),
				'type'    => 'select',
				'choices' => array(
					'cd'      => esc_html__( 'CD', 'wolf-discography' ),
					'digital' => esc_html__( 'Digital Download', 'wolf-discography' ),
					'dvd'     => esc_html__( 'DVD', 'wolf-discography' ),
					'vinyl'   => esc_html__( 'Vinyl', 'wolf-discography' ),
					'tape'    => esc_html__( 'Tape', 'wolf-discography' ),
				),
			),
			array(
				'label' => esc_html__( 'Amazon', 'wolf-discography' ),
				'id'    => '_wolf_release_amazon',
				'type'  => 'url',
			),
			array(
				'label' => esc_html__( 'Apple', 'wolf-discography' ),
				'id'    => '_wolf_release_apple',
				'type'  => 'url',
			),
			array(
				'label' => esc_html__( 'Bandcamp', 'wolf-discography' ),
				'id'    => '_wolf_release_bandcamp',
				'type'  => 'url',
			),
			array(
				'label' => esc_html__( 'Deezer', 'wolf-discography' ),
				'id'    => '_wolf_release_deezer',
				'type'  => 'url',
			),
			array(
				'label' => esc_html__( 'iTunes', 'wolf-discography' ),
				'id'    => '_wolf_release_itunes',
				'type'  => 'url',
			),
			array(
				'label' => esc_html__( 'Spotify', 'wolf-discography' ),
				'id'    => '_wolf_release_spotify',
				'type'  => 'url',
			),
			array(
				'label' => esc_html__( 'Tidal', 'wolf-discography' ),
				'id'    => '_wolf_release_tidal',
				'type'  => 'url',
			),
			array(
				'label' => esc_html__( 'YouTube Music', 'wolf-discography' ),
				'id'    => '_wolf_release_google_play',
				'type'  => 'url',
			),
			array(
				'label' => esc_html__( 'Buy (any link where the release can be purchased)', 'wolf-discography' ),
				'id'    => '_wolf_release_buy',
				'type'  => 'url',
			),
			array(
				'label' => esc_html__( 'Free Download link', 'wolf-discography' ),
				'id'    => '_wolf_release_free',
				'type'  => 'url',
			),
			array(
				'label' => esc_html__( 'Tracklist', 'wolf-discography' ),
				'id'    => '_wolf_release_tracklist',
				'type'  => 'repeatable',
			),
		);
	}

	/**
	 * Get field types that require URL validation
	 *
	 * @return array
	 */
	public static function getUrlFields(): array {
		return array(
			'_wolf_release_amazon',
			'_wolf_release_apple',
			'_wolf_release_bandcamp',
			'_wolf_release_deezer',
			'_wolf_release_itunes',
			'_wolf_release_spotify',
			'_wolf_release_tidal',
			'_wolf_release_google_play',
			'_wolf_release_buy',
			'_wolf_release_free',
		);
	}

	/**
	 * Get field types that are repeatable
	 *
	 * @return array
	 */
	public static function getRepeatableFields(): array {
		return array(
			'_wolf_release_tracklist',
		);
	}
}
