<?php
/**
 * Discography helpers
 *
 * @author WolfThemes
 * @category Core
 * @package WolfDiscography/Functions
 * @version 1.0.2
 * @since 1.6.0
 */


defined( 'ABSPATH' ) || exit;

/**
 * Gets the ID of the post, even if it's not inside the loop.
 *
 * @uses WP_Query
 * @uses get_queried_object()
 * @extends get_the_ID()
 * @see get_the_ID()
 *
 * @return int
 */
function wd_get_the_id() {

	global $wp_query;

	$post_id = null;

	// Get post ID outside the loop.
	if ( is_object( $wp_query ) && isset( $wp_query->queried_object ) && isset( $wp_query->queried_object->ID ) ) {
		$post_id = $wp_query->queried_object->ID;
	} else {
		$post_id = get_the_ID();
	}

	return $post_id;
}

/**
 * Sanitize CSS from user intpu
 *
 * @param string $style
 * @return string
 */
function wd_sanitize_css_field( $style ) {

	if ( '' === $style ) {
		return;
	}

	// remove double semicolon.
	$style = str_replace( array( ';;', '; ;' ), '', $style );

	if ( ';' !== substr( $style, -1 ) ) {
		$style = $style . ';'; // add end semicolon if missing.
	}

	return esc_attr( trim( wd_core_clean_spaces( $style ) ) );
}

/**
 * Remove all double spaces and line breaks
 *
 * This function is mainly used to clean up inline CSS
 *
 * @param string $css
 * @return string
 */
function wd_core_clean_spaces( $string, $hard = false ) {

	if ( $hard ) {
		return str_replace( ' ', '', $string );
	} else {
		return preg_replace( '/\s+/', ' ', $string );
	}
}

/**
 * Get inline CSS from VC custom CSS class
 *
 * We use it to add inline style to row
 *
 * @param $param_value
 * @param string $prefix
 * @return string
 */
function wd_shortcode_custom_style( $param_value ) {

	if ( preg_match( "/\s*\.([^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/", $param_value, $match ) ) {
		if ( isset( $match[2] ) ) {
			return wd_core_clean_spaces( str_replace( '!important', '', $match[2] ), true ); // remove !important to allow CSS overwriting
		}
	}
}

/**
 * Create a formatted sample of any text
 *
 * Remove HTML and shortcode, sanitize and shorten a string
 *
 * @param string $text
 * @param int $num_words
 * @param string $more
 * @return string
 */
function wd_sample( $text = null, $num_words = 55, $more = '...' ) {
	$text = ( $text ) ? $text : get_the_excerpt();
	return wp_trim_words( strip_shortcodes( $text ), $num_words, $more );
}

/**
 * Convert list to array
 *
 * @param string $list
 * @return array
 */
function wd_core_list_to_array( $list, $separator = ',' ) {
	return ( $list ) ? explode( ',', trim( wd_core_clean_spaces( wd_core_clean_list( $list ) ) ) ) : array();
}

/**
 * Helper method to determine if an attribute is true or false.
 *
 * @param string|int|bool $var Attribute value.
 * @return bool
 */
function wd_attr_bool( $var ) {
	$falsey = array( 'false', '0', 'no', 'n', '', ' ' );
	return ( ! $var || in_array( strtolower( $var ), $falsey, true ) ) ? false : true;
}