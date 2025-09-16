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

defined( 'ABSPATH' ) || exit;

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
	return \WolfDiscography\Core\Core::get_page_id();
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
	return \WolfDiscography\Core\Core::get_page_link();
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
	return \WolfDiscography\Core\Core::get_release_option( $value, $default );
}

/**
 * Get template part (for templates like the release-loop).
 *
 * @param mixed  $slug
 * @param string $name (default: '')
 */
function wolf_discography_get_template_part( $slug, $name = '' ) {
	return \WolfDiscography\Frontend\TemplateLoader::discography_get_template_part( $slug, $name );
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
	return \WolfDiscography\Frontend\TemplateLoader::discography_get_template( $template_name, $args, $template_path, $default_path );
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
	return \WolfDiscography\Frontend\TemplateLoader::discography_locate_template( $template_name, $template_path, $default_path );
}
