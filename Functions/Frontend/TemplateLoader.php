<?php
/**
 * Template Loader
 *
 * @package WolfDiscography/Frontend
 * @since 2.0.0
 */

namespace WolfDiscography\Frontend;

defined( 'ABSPATH' ) || exit;

class TemplateLoader {

	/**
	 * Output the start of the page wrapper.
	 *
	 * @since 1.0.2
	 */
	public static function output_content_wrapper() {
		self::get_template( 'global/wrapper-start.php' );
	}

	/**
	 * Output the end of the page wrapper.
	 *
	 * @since 1.0.2
	 */
	public static function output_content_wrapper_end() {
		self::get_template( 'global/wrapper-end.php' );
	}

	/**
	 * Output the start of a release loop. By default this is a UL
	 *
	 * @since 1.0.2
	 * @param bool $echo Whether to echo or return the output
	 * @return string|void
	 */
	public static function loop_start( $echo = true ) {
		ob_start();
		self::get_template( 'loop/loop-start.php' );

		if ( $echo ) {
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}

	/**
	 * Output the end of a release loop. By default this is a UL
	 *
	 * @since 1.0.2
	 * @param bool $echo Whether to echo or return the output
	 * @return string|void
	 */
	public static function loop_end( $echo = true ) {
		ob_start();
		self::get_template( 'loop/loop-end.php' );

		if ( $echo ) {
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}

	/**
	 * Output single release content
	 *
	 * @since 1.6.0
	 * @param bool $echo Whether to echo or return the output
	 * @return string|void
	 */
	public static function output_single_content( $echo = true ) {
		ob_start();

		// For block themes, ensure we have post data
		if ( wp_is_block_theme() && is_singular( 'release' ) ) {
			global $post;
			if ( $post ) {
				setup_postdata( $post );
				self::get_template_part( 'content', 'single' );
				Template_Helper::release_nav();
				wp_reset_postdata();
			}
		} else {
			// Classic theme with proper query loop
			while ( have_posts() ) :
				the_post();
				self::get_template_part( 'content', 'single' );
				Template_Helper::release_nav();
			endwhile;
		}

		if ( $echo ) {
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}

	/**
	 * Get template part (for templates like the release-loop).
	 *
	 * @param mixed  $slug
	 * @param string $name (default: '')
	 */
	public static function get_template_part( $slug, $name = '' ) {
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
	public static function get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {

		if ( $args && is_array( $args ) ) {
			extract( $args );
		}

		$located = self::discography_locate_template( $template_name, $template_path, $default_path );

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
	public static function discography_locate_template( $template_name, $template_path = '', $default_path = '' ) {

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
}
