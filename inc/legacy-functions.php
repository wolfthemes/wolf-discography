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

// Backwards compatibility wrappers

function wolf_discography_get_page_id() {
	return \Wolf_Discography\Core\Core::get_discography_page_id();
}

function wolf_discography_get_page_link() {
	return \Wolf_Discography\Core\Core::get_page_link();
}

function wolf_get_release_option( $value, $default = null ) {
	return \Wolf_Discography\Core\Options::get_option( $value, $default );
}

function wolf_discography_output_content_wrapper() {
	return \Wolf_Discography\Frontend\Template_Loader::output_content_wrapper();
}

function wolf_discography_output_content_wrapper_end() {
	return \Wolf_Discography\Frontend\Template_Loader::output_content_wrapper_end();
}

function wolf_discography_loop_start( $echo = true ) {
	return \Wolf_Discography\Frontend\Template_Loader::loop_start( $echo );
}

function wolf_discography_loop_end( $echo = true ) {
	return \Wolf_Discography\Frontend\Template_Loader::loop_end( $echo );
}

function wolf_discography_output_single_content( $echo = true ) {
	return \Wolf_Discography\Frontend\Template_Loader::output_single_content( $echo );
}

function wolf_discography_get_template_part( $slug, $name = '' ) {
	return \Wolf_Discography\Frontend\Template_Loader::get_template_part( $slug, $name );
}

function wolf_discography_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	return \Wolf_Discography\Frontend\Template_Loader::get_template( $template_name, $args, $template_path, $default_path );
}

function wolf_discography_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	return \Wolf_Discography\Frontend\Template_Loader::locate_template( $template_name, $template_path, $default_path );
}

function wolf_release_nav() {
	return \Wolf_Discography\Frontend\Template_Helper::release_nav();
}

function wolf_release_page_nav( $loop = null ) {
	return \WolfDiscography\Frontend\Template_Loader::release_page_nav( $loop );
}
