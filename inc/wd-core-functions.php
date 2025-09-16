<?php
/**
 * Discography core functions
 *
 * General core functions available on admin and frontend
 *
 * @author WolfThemes
 * @category Core
 * @package WolfDiscography/Core
 * @version 1.5.1
 */

use WolfDiscography\Frontend\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Add image sizes
 *
 * These size will be ued for galleries and sliders
 *
 * @since 1.2.6
 */
function wd_add_image_sizes() {

	// add discography image sizes
	add_image_size( 'CD', 400, 400, true );
	add_image_size( 'DVD', 400, 570, true );
}
add_action( 'init', 'wd_add_image_sizes' );

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
function wolf_discography_get_page_id() {

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
function wolf_discography_get_page_link() {

	$page_id = wolf_discography_get_page_id();

	if ( $page_id != -1 ) {
		return get_permalink( $page_id );
	}
}

/**
 * Get template part (for templates like the release-loop).
 *
 * @param mixed  $slug
 * @param string $name (default: '')
 */
function wolf_discography_get_template_part( $slug, $name = '' ) {
	$template = '';

	$wolf_discography = WD();

	// Look in yourtheme/slug-name.php and yourtheme/wolf_discography/slug-name.php
	if ( $name ) {
		$template = locate_template( array( "{$slug}-{$name}.php", "{$wolf_discography->template_url}{$slug}-{$name}.php" ) );
	}

	// Get default slug-name.php
	if ( ! $template && $name && file_exists( $wolf_discography->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
		$template = $wolf_discography->plugin_path() . "/templates/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/wolf_discography/slug.php
	if ( ! $template ) {
		$template = locate_template( array( "{$slug}.php", "{$wolf_discography->template_url}{$slug}.php" ) );
	}

	if ( $template ) {
		load_template( $template, false );
	}
}


/**
 * Get other templates (e.g. ticket attributes) passing attributes and including the file.
 *
 * @param mixed  $template_name
 * @param array  $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 */
function wolf_discography_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {

	if ( $args && is_array( $args ) ) {
		extract( $args );
	}

	$located = wolf_discography_locate_template( $template_name, $template_path, $default_path );

	do_action( 'wolf_discography_before_template_part', $template_name, $template_path, $located, $args );

	include $located;

	do_action( 'wolf_discography_after_template_part', $template_name, $template_path, $located, $args );
}


/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 * yourtheme/$template_path/$template_name
 * yourtheme/$template_name
 * $default_path/$template_name
 *
 * @param mixed  $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function wolf_discography_locate_template( $template_name, $template_path = '', $default_path = '' ) {

	if ( ! $template_path ) {
		$template_path = WD()->template_url;
	}
	if ( ! $default_path ) {
		$default_path = WD()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);

	// Get default template
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	// Return what we found
	return apply_filters( 'wolf_discography_locate_template', $template, $template_name, $template_path );
}

/**
 * Widget function
 *
 * Displays the show list in the widget
 *
 * @param int $count, string $url, bool $link
 * @return string
 */
function wolf_get_release_option( $value, $default = null ) {

	$wolf_releases_settings = get_option( 'wolf_release_settings' );

	if ( isset( $wolf_releases_settings[ $value ] ) && '' != $wolf_releases_settings[ $value ] ) {

		return $wolf_releases_settings[ $value ];

	} elseif ( $default ) {

		return $default;
	}
}

/**
 * Get post attributes
 *
 * @param int $post_id The post ID.
 * @return array $post_attrs
 */
function wd_get_post_attr( $post_id ) {

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
function wd_post_attr( $class = '', $post_id = null ) {

	$post_id = ( $post_id ) ? $post_id : get_the_ID();
	$attrs   = wd_get_post_attr( $post_id );
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

if ( ! function_exists( 'debug' ) ) {
	/**
	 *  Debug function for developpment
	 *  Display less infos than a var_dump
	 *
	 * @param string $var The variable to debug.
	 */
	function debug( $desc, $var = null ) { // phpcs:ignore
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