<?php
/**
 * Core class
 *
 * @package WolfDiscography
 * @subpackage Core
 * @since 2.0.0
 */

namespace Wolf_Discography\Core;

use Wolf_Discography\Admin\Options;

defined( 'ABSPATH' ) || exit;

class Core {
	/**
	 * wolf_discography page IDs
	 *
	 * retrieve page ids - used for the main discography page
	 *
	 * returns -1 if no page is found
	 *
	 * @param string $page
	 * @return int
	 */
	public static function get_discography_page_id() {

		// Try new option format first
		$page_id = Options::get_option( 'discography_page' );

		// Fall back to legacy option if new one doesn't exist
		if ( ! $page_id ) {
			$page_id = get_option( '_wolf_discography_page_id', -1 );
		}

		// Convert empty values to -1 for consistency
		if ( ! $page_id || '' === $page_id ) {
			$page_id = -1;
		}

		// Apply WPML filter if page exists
		if ( -1 != $page_id ) {
			$page_id = apply_filters( 'wpml_object_id', absint( $page_id ), 'page', true );
		}

		return $page_id;
	}

	/**
	 * wolf_discography page link
	 *
	 * retrieve discography page permalink
	 *
	 * @param string $page
	 * @return string
	 */
	public static function get_page_link() {

		$page_id = self::get_discography_page_id();

		if ( $page_id != -1 ) {
			return get_permalink( $page_id );
		}
	}

	/**
	 * Get release option
	 *
	 * @param string $value Option key
	 * @param mixed  $default Default value
	 * @return mixed
	 */
	public static function get_release_option( $value, $default = null ) {

		// Map old option names to new ones
		$option_map = array(
			'use_band_tax'   => 'use_band_tax',
			'use_label_tax'  => 'use_label_tax',
			'use_genre_tax'  => 'use_genre_tax',
			'display_format' => 'display_format',
			'display'        => 'display_style',
			'columns'        => 'grid_columns',
		);

		// Try new options first
		if ( isset( $option_map[ $value ] ) ) {
			$new_value = Options::get_option( $option_map[ $value ] );
			if ( '' !== $new_value && null !== $new_value ) {
				return $new_value;
			}
		}

		// Fall back to legacy options
		$legacy_settings = get_option( 'wolf_release_settings' );

		if ( isset( $legacy_settings[ $value ] ) && '' != $legacy_settings[ $value ] ) {
			return $legacy_settings[ $value ];
		} elseif ( $default ) {
			return $default;
		}

		return null;
	}
}
