<?php
/**
 * Admin Handler
 *
 * Manages all admin-related functionality
 *
 * @package WolfDiscography
 * @subpackage Admin
 * @since 2.0.0
 */

namespace WolfDiscography\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin Handler Class
 *
 * Coordinates admin functionality including metaboxes, admin pages, etc.
 */
class AdminHandler {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->initHooks();
		$this->loadAdminClasses();
	}

	/**
	 * Initialize admin hooks
	 */
	private function initHooks(): void {
		// Admin-specific hooks can go here
		add_action( 'admin_init', array( $this, 'adminInit' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAdminAssets' ) );
	}

	/**
	 * Load admin-related classes and files
	 */
	private function loadAdminClasses(): void {
		// Load legacy admin class during migration
		$legacy_admin_file = WD_DIR . '/inc/admin/class-wd-admin.php';
		if ( file_exists( $legacy_admin_file ) ) {
			include_once $legacy_admin_file;
		}

		// TODO: Progressively migrate admin functionality to new classes
		// Examples:
		// new MetaboxManager();
		// new AdminPages();
		// new AdminAjax();
	}

	/**
	 * Admin initialization
	 */
	public function adminInit(): void {
		// Admin initialization logic
	}

	/**
	 * Enqueue admin assets
	 *
	 * @param string $hook Current admin page hook
	 */
	public function enqueueAdminAssets( string $hook ): void {
		// Enqueue admin-specific CSS and JS
		global $post;

		if ( empty( $post ) ) {
			return;
		}

		// Only load on discography-related pages
		if ( is_object( $post ) && $post->post_type === 'release' ) {
			wp_enqueue_script(
				'wolf-discography-admin',
				WD_JS . '/admin.js',
				array( 'jquery' ),
				WD_VERSION,
				true
			);

			wp_enqueue_style(
				'wolf-discography-admin',
				WD_CSS . '/admin.css',
				array(),
				WD_VERSION
			);
		}
	}
}
