<?php
/**
 * Enhanced Template System for Wolf Discography
 * Add this to your main plugin file or create a new inc/class-wd-templates.php
 *
 * @author WolfThemes
 * @category Core
 * @package WolfDiscography/Templates
 * @version 1.6.0
 * @since 1.6.0
 */

defined( 'ABSPATH' ) || exit;

class WD_Template_Manager {

    public function __construct() {
        add_action( 'init', array( $this, 'handle_templates' ) );

	}

	public function handle_templates() {
		//add_action( 'template_redirect', array( $this, 'discography_template_redirect' ), 40 );

		add_filter( 'template_include', array( $this, 'template_loader' ) );
	}

    /**
     * Check if we're on a discography-related page
     */
    private function is_discography_page() {
        return (
            is_singular( 'release' ) ||
            is_post_type_archive( 'release' ) ||
            is_tax( array( 'band', 'label', 'release_genre' ) ) ||
            ( function_exists( 'wolf_discography_get_page_id' ) && is_page( wolf_discography_get_page_id() ) )
        );
    }

	public function discography_template_redirect() {
		if ( is_page( wolf_discography_get_page_id() ) && ! post_password_required() ) {
			wolf_discography_get_template( 'discography-template.php' );
			exit();
		}
	}

	public function template_loader( $template ) {

		$find = array( 'wolf-discography.php' ); // nope! not used
		$file = 'archive-release.php';

		if ( is_single() && WD()->cpt_slug == get_post_type() ) {

			$file    = 'single-' . WD()->cpt_slug . '.php';
			$find[] = $file;
			$find[] = WD()->template_url . $file;

		} elseif ( is_tax( 'band' ) || is_tax( 'label' ) || is_tax( 'release_genre' ) ) {

			$term = get_queried_object();

			$file 	= 'taxonomy-' . $term->taxonomy . '.php';
			$find[] 	= 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] 	= WD()->template_url . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] 	= $file;
			$find[] 	= WD()->template_url . $file;

		} elseif ( is_post_type_archive( WD()->cpt_slug ) ) {

			$file = 'archive-' . WD()->cpt_slug . '.php';
			$find[] = $file;
			$find[] = WD()->template_url . $file;

		}

		if ( $file ) {
			$template = locate_template( $find );
			if ( ! $template ) $template = WD()->plugin_path() . '/templates/' . $file;
		}

		return $template;
	}

}

// Initialize the template manager
new WD_Template_Manager();