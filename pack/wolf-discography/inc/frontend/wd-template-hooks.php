<?php
/**
 * WolfDiscography Hooks
 *
 * Action/filter hooks used for WolfDiscography functions/templates
 *
 * @author WolfThemes
 * @category Core
 * @package WolfDiscography/Templates
 * @since 1.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed direct
}

/**
 * Body class
 *
 * @see  wd_body_class()
 */
add_filter( 'body_class', 'wd_body_class' );

/**
 * WP Header
 *
 * @see  wd_generator_tag()
 */
add_action( 'get_the_generator_html', 'wd_generator_tag', 10, 2 );
add_action( 'get_the_generator_xhtml', 'wd_generator_tag', 10, 2 );

/**
 * Content wrappers
 *
 * @see wolf_discography_output_content_wrapper()
 * @see wolf_discography_output_content_wrapper_end()
 */
add_action( 'wolf_discography_before_main_content', 'wolf_discography_output_content_wrapper', 10 );
add_action( 'wolf_discography_after_main_content', 'wolf_discography_output_content_wrapper_end', 10 );

add_action( 'template_redirect', 'wolf_discography_template_redirect', 40 );