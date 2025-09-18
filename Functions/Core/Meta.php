<?php
/**
 * Meta
 *
 * @package WolfDiscography
 * @subpackage Cores
 * @since 2.0.0
 */

namespace Wolf_Discography\Core;

defined( 'ABSPATH' ) || exit;

class Meta {

	/**
	 * Get any thumbnail URL
	 *
	 * @param string $format
	 * @param int    $post_id
	 * @return string
	 */
	public static function get_post_thumbnail_url( $format = 'medium', $post_id = null ) {
		global $post;

		if ( is_object( $post ) && isset( $post->ID ) && null == $post_id ) {

			$ID = $post->ID;
		} else {
			$ID = $post_id;
		}

		if ( $ID && has_post_thumbnail( $ID ) ) {

			$attachment_id = get_post_thumbnail_id( $ID );
			if ( $attachment_id ) {
				$img_src = wp_get_attachment_image_src( $attachment_id, $format );

				if ( $img_src && isset( $img_src[0] ) ) {
					return esc_url( $img_src[0] );
				}
			}
		}
	}


	/**
	 * Get release tracklist as array
	 */
	public static function release_get_tracklist() {

		$post_id   = get_the_ID();
		$tracklist = ( is_array( get_post_meta( $post_id, '_wolf_release_tracklist', true ) ) ) ? get_post_meta( $post_id, '_wolf_release_tracklist', true ) : array();

		if ( isset( $tracklist[0] ) && '' != $tracklist[0] ) {
			return $tracklist;
		}
	}


	/**
	 * Get tracklist count
	 */
	public static function release_get_tracklist_count() {

		$post_id   = get_the_ID();
		$tracklist = self::release_get_tracklist();

		if ( $tracklist ) {
			return absint( count( $tracklist ) );
		}
	}

	/**
	 * Get release meta
	 *
	 * @since 1.2.6
	 * @return array
	 */
	public static function get_meta() {

		$meta = self::get_default_meta(); // get empty object

		$post_id     = get_the_ID();
		$title       = get_post_meta( $post_id, '_wolf_release_title', true );
		$date        = get_post_meta( $post_id, '_wolf_release_date', true );
		$catalog     = get_post_meta( $post_id, '_wolf_release_catalog_number', true );
		$format      = get_post_meta( $post_id, '_wolf_release_type', true );
		$itunes      = get_post_meta( $post_id, '_wolf_release_itunes', true );
		$google_play = get_post_meta( $post_id, '_wolf_release_google_play', true );
		$amazon      = get_post_meta( $post_id, '_wolf_release_amazon', true );
		$bandcamp    = get_post_meta( $post_id, '_wolf_release_bandcamp', true );
		$deezer      = get_post_meta( $post_id, '_wolf_release_deezer', true );
		$apple       = get_post_meta( $post_id, '_wolf_release_apple', true );
		$tidal       = get_post_meta( $post_id, '_wolf_release_tidal', true );
		$qobuz       = get_post_meta( $post_id, '_wolf_release_qobuz', true );
		$spotify     = get_post_meta( $post_id, '_wolf_release_spotify', true );
		$buy         = get_post_meta( $post_id, '_wolf_release_buy', true );
		$free        = get_post_meta( $post_id, '_wolf_release_free', true );
		$tracklist   = self::release_get_tracklist();

		$display_date = '';
		if ( $date ) {
			list( $month, $day, $year ) = explode( '-', $date );
			$sql_date                   = $year . '-' . $month . '-' . $day . ' 00:00:00';
			$display_date               = mysql2date( get_option( 'date_format' ), $sql_date );
		}

		if ( $title ) {
			$meta['title'] = $title;
		}

		if ( $display_date ) {
			$meta['date'] = $display_date;
		}

		if ( $catalog ) {
			$meta['catalog'] = $catalog;
		}

		if ( $format ) {
			$meta['format'] = $format;
		}

		if ( $itunes ) {
			$meta['itunes'] = $itunes;
		}

		if ( $google_play ) {
			$meta['google_play'] = $google_play;
		}

		if ( $amazon ) {
			$meta['amazon'] = $amazon;
		}

		if ( $bandcamp ) {
			$meta['bandcamp'] = $bandcamp;
		}

		if ( $deezer ) {
			$meta['deezer'] = $deezer;
		}

		if ( $apple ) {
			$meta['apple'] = $apple;
		}

		if ( $tidal ) {
			$meta['tidal'] = $tidal;
		}

		if ( $qobuz ) {
			$meta['qobuz'] = $qobuz;
		}

		if ( $spotify ) {
			$meta['spotify'] = $spotify;
		}

		if ( $buy ) {
			$meta['buy'] = $buy;
		}

		if ( $free ) {
			$meta['free'] = $free;
		}

		if ( $tracklist ) {
			$meta['tracklist'] = $tracklist;
		}

		return apply_filters( 'wd_meta', $meta );
	}

	/**
	 * Get default release meta
	 *
	 * @since 1.2.6
	 * @return object
	 */
	public static function get_default_meta() {

		$meta = array(
			'title'       => '',
			'date'        => '',
			'catalog'     => '',
			'format'      => '',
			'itunes'      => '',
			'google_play' => '',
			'amazon'      => '',
			'bandcamp'    => '',
			'apple'       => '',
			'deezer'      => '',
			'tidal'       => '',
			'qobuz'       => '',
			'spotify'     => '',
			'buy'         => '',
			'free'        => '',
			'tracklist'   => array(),
		);

		return $meta;
	}

	/**
	 * Get release artist
	 *
	 * @since 1.2.6
	 * @return string
	 */
	public static function get_artist() {

		$post_id = get_the_ID();
		$band    = '';

		if ( strip_tags( get_the_term_list( $post_id, 'band', '', ', ', '' ) ) != '' ) {

			$band = '<strong>' . apply_filters( 'wolf_discography_band_string', esc_html( 'Band', 'wolf-discography' ) ) . ' </strong> : ' . strip_tags( get_the_term_list( $post_id, 'band', '', ', ', '' ) ) . '<br>';

		}

		if ( Options::get_option( 'use_band_tax' ) ) {
			$band = get_the_term_list( $post_id, 'band', '<strong>' . apply_filters( 'wolf_discography_band_string', esc_html( 'Band', 'wolf-discography' ) ) . ' </strong> : ', ', ', '<br>' );
		}

		return $band;
	}

	/**
	 * Get release label
	 *
	 * @since 1.2.6
	 * @return string
	 */
	public static function get_label() {

		$post_id = get_the_ID();
		$label   = '';

		if ( ! taxonomy_exists( 'label' ) ) {
			return;
		}

		if ( wp_strip_all_tags( get_the_term_list( $post_id, 'label', '', ', ', '' ) ) != '' ) {

			$label = '<strong>' . esc_html( 'Label', 'wolf-discography' ) . ' </strong> : ' . wp_strip_all_tags( get_the_term_list( $post_id, 'label', '', ', ', '' ) ) . '<br>';
		}

		if ( Options::get_option( 'use_label_tax' ) ) {
			$label = get_the_term_list( $post_id, 'label', '<strong>' . esc_html( 'Label', 'wolf-discography' ) . ' </strong> : ', ', ', '<br>' );
		}

		return $label;
	}
}
