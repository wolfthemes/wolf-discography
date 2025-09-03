<?php
/**
 * Elementor functions
 *
 * @author WolfThemes
 * @category Core
 * @package WolfDiscography/Core
 * @version 1.5.1
 */

defined( 'ABSPATH' ) || exit;

function wd_register_widgets() {

	if ( ! WD()->is_wolf_theme() ) {

		$cpt = 'release';

		if ( post_type_exists( $cpt ) && is_file( WD()->plugin_url() . '/elementor/' . sanitize_title_with_dashes( $cpt ) . '-index.php' ) ) {
			require_once get_template_directory() . '/elementor/' . sanitize_title_with_dashes( $cpt ) . '-index.php';
		}
	}
}
add_action( 'elementor/widgets/widgets_registered', 'wd_register_widgets' );