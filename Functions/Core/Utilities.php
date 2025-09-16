<?php
/**
 * Utilities
 *
 * @package WolfDiscography
 * @subpackage Cores
 * @since 2.0.0
 */

namespace WolfDiscography\Core;

use WolfDiscography\Frontend\Helpers;

defined( 'ABSPATH' ) || exit;

class Utilities {

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
	 * Get post attributes
	 *
	 * @param int $post_id The post ID.
	 * @return array $post_attrs
	 */
	public static function get_post_attr( $post_id ) {

		$post_attrs = array();

		$post_attrs['id']           = 'post-' . $post_id;
		$post_attrs['class']        = Helpers::array_to_list( get_post_class(), ' ' );
		$post_attrs['data-post-id'] = $post_id;
		if ( 'release' === get_post_type() ) {
			$post_attrs['itemscope'] = '';
			$post_attrs['itemtype']  = 'https://schema.org/MusicAlbum';
		}

		if ( has_post_thumbnail( $post_id ) ) {

			$img_dominant_color = Helpers::get_image_dominant_color( get_post_thumbnail_id( $post_id ) );
			$img_color_tone     = Helpers::get_color_tone( $img_dominant_color, 180 );

			$post_attrs['data-thumbnail-color-tone'] = $img_color_tone;
		}

		/**
		 * Filters post tag attributes
		 *
		 * @since %NAME% 1.0.0
		 */
		return apply_filters( 'wd_post_attrs', $post_attrs, $post_id );
	}

	/**
	 * Output post attributes
	 *
	 * @param int $post_id The post ID.
	 */
	public static function post_attr( $class = '', $post_id = null ) {

		$post_id = ( $post_id ) ? $post_id : get_the_ID();
		$attrs   = self::get_post_attr( $post_id );
		$output  = '';

		$classes = array();

		if ( $class ) {
			if ( ! is_array( $class ) ) {
				$class = preg_split( '#\s+#', $class );
			}
				$classes = array_map( 'esc_attr', $class );
		} else {
			// Ensure that we always coerce class to being an array.
			$class = array();
		}

		foreach ( $attrs as $attr => $value ) {
			if ( $value ) {

				if ( array() !== $classes && 'class' === $attr ) {
					$classes = array_unique( $classes );

					foreach ( $classes as $class ) {
						$value .= ' ' . $class;
					}
				}

				$output .= esc_attr( $attr ) . '="' . esc_attr( $value ) . '" ';

			} else {
				$output .= esc_attr( $attr ) . ' ';
			}
		}

		// debug( $output );

		echo wp_kses_data( $output );
	}

	/**
	 *  Debug function for developpment
	 *  Display less infos than a var_dump
	 *
	 * @param string $var The variable to debug.
	 */
	public static function debug( $desc, $var = null ) { // phpcs:ignore
		if ( WP_DEBUG ) {

			echo '<br><pre style="border: 1px solid #ccc; padding:5px; width:98%">';
			if ( ! is_string( $desc ) ) {
				print_r( $desc );
			} else {
				echo $desc;
				echo ' ';
				if ( $var ) {
					print_r( $var ); // phpcs:ignore
				}
			}
			echo '</pre>';
		}
	}
}
