<?php
/**
 * Core class
 *
 * @package WolfDiscography
 * @subpackage Core
 * @since 2.0.0
 */

namespace WolfDiscography\Core;

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

		$page_id = -1;

		if ( -1 != get_option( '_wolf_discography_page_id' ) && get_option( '_wolf_discography_page_id' ) ) {

			$page_id = get_option( '_wolf_discography_page_id' );

		}

		if ( -1 != $page_id ) {
			$page_id = apply_filters( 'wpml_object_id', absint( $page_id ), 'page', true ); // filter for WPML
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
	 * Widget function
	 *
	 * Displays the show list in the widget
	 *
	 * @param int $count, string $url, bool $link
	 * @return string
	 */
	public static function get_release_option( $value, $default = null ) {

		$wolf_releases_settings = get_option( 'wolf_release_settings' );

		if ( isset( $wolf_releases_settings[ $value ] ) && '' != $wolf_releases_settings[ $value ] ) {

			return $wolf_releases_settings[ $value ];

		} elseif ( $default ) {

			return $default;
		}
	}
}
