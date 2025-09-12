<?php
/**
 * Frontend Handler
 *
 * Manages all frontend-related functionality
 *
 * @package WolfDiscography
 * @subpackage Frontend
 * @since 2.0.0
 */

namespace WolfDiscography\Frontend;

defined( 'ABSPATH' ) || exit;

/**
 * Frontend Handler Class
 *
 * Coordinates frontend functionality including templates, shortcodes, assets, etc.
 */
class FrontendHandler {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->initHooks();
		$this->loadFrontendClasses();
	}

	/**
	 * Initialize frontend hooks
	 */
	private function initHooks(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueueFrontendAssets' ) );
		add_filter( 'template_include', array( $this, 'templateLoader' ) );
	}

	/**
	 * Load frontend-related classes and files
	 */
	private function loadFrontendClasses(): void {
		// Load legacy frontend files during migration
		$frontend_files = array(
			'wd-functions.php',
			'wd-helpers.php',
			'wd-image-functions.php',
			'wd-template-hooks.php',
			'wd-posts.php',
			'class-wd-shortcode.php',
			'class-wd-template-manager.php',
		);

		foreach ( $frontend_files as $file ) {
			$file_path = WD_DIR . '/inc/frontend/' . $file;
			if ( file_exists( $file_path ) ) {
				include_once $file_path;
			}
		}

		// TODO: Progressively migrate frontend functionality to new classes
		// Examples:
		// new TemplateManager();
		// new ShortcodeManager();
		// new AssetManager();
	}

	/**
	 * Enqueue frontend assets
	 */
	public function enqueueFrontendAssets(): void {
		// Only load on discography-related pages
		if ( $this->shouldLoadAssets() ) {
			wp_enqueue_style(
				'wolf-discography-frontend',
				WD_CSS . '/wolf-discography.css',
				array(),
				WD_VERSION
			);

			wp_enqueue_script(
				'wolf-discography-frontend',
				WD_JS . '/wolf-discography.js',
				array( 'jquery' ),
				WD_VERSION,
				true
			);

			// Localize script with data
			wp_localize_script(
				'wolf-discography-frontend',
				'WolfDiscographyData',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'wolf_discography_nonce' ),
				)
			);
		}
	}

	/**
	 * Check if we should load assets on current page
	 *
	 * @return bool
	 */
	private function shouldLoadAssets(): bool {
		global $post;

		// Load on discography post type pages
		if ( is_singular( 'release' ) || is_post_type_archive( 'release' ) ) {
			return true;
		}

		// Load on discography taxonomy pages
		if ( is_tax( array( 'band', 'label', 'release_genre' ) ) ) {
			return true;
		}

		// Load if post content contains discography shortcodes
		if ( is_object( $post ) && has_shortcode( $post->post_content, 'wolf_discography' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Template loader
	 *
	 * Handles template loading for discography pages
	 *
	 * @param string $template Current template
	 * @return string Modified template path
	 */
	public function templateLoader( string $template ): string {
		$find = array( 'wolf-discography.php' );
		$file = '';

		if ( is_single() && 'release' === get_post_type() ) {
			$file   = 'single-release.php';
			$find[] = $file;
			$find[] = 'wolf-discography/' . $file;

		} elseif ( is_tax( 'band' ) || is_tax( 'label' ) || is_tax( 'release_genre' ) ) {
			$term = get_queried_object();

			if ( $term ) {
				$file   = 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = 'wolf-discography/taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = $file;
				$find[] = 'wolf-discography/' . $file;
			}
		} elseif ( is_post_type_archive( 'release' ) ) {
			$file   = 'archive-release.php';
			$find[] = $file;
			$find[] = 'wolf-discography/' . $file;
		}

		if ( $file ) {
			$template = locate_template( $find );
			if ( ! $template ) {
				$plugin_template = WD_DIR . '/templates/' . $file;
				if ( file_exists( $plugin_template ) ) {
					$template = $plugin_template;
				}
			}
		}

		return $template;
	}
}
