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

use WolfDiscography\Admin\Updater;

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
		$this->init_hooks();
		$this->load_admin_classes();
	}

	/**
	 * Initialize admin hooks
	 */
	private function init_hooks(): void {
		// Admin-specific hooks can go here
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Load admin-related classes and files
	 */
	private function load_admin_classes(): void {
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
	public function admin_init(): void {
		// Admin initialization logic
		$this->plugin_update();
	}

	/**
	 * Enqueue admin assets
	 *
	 * @param string $hook Current admin page hook
	 */
	public function enqueue_admin_assets( string $hook ): void {
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

	public function plugin_update() {
		if ( ! class_exists( 'WP_GitHub_Updater' ) ) {
			include_once WD()->getPluginPath() . '/inc/admin/updater.php';
		}

		$repo = 'wolfthemes/wolf-discography';

		$config = array(
			'slug'               => plugin_basename( WD()->getPluginPath() . '/wolf-discography.php' ),
			'proper_folder_name' => 'wolf-discography',
			'api_url'            => 'https://api.github.com/repos/' . $repo . '',
			'raw_url'            => 'https://raw.github.com/' . $repo . '/master/',
			'github_url'         => 'https://github.com/' . $repo . '',
			'zip_url'            => 'https://github.com/' . $repo . '/archive/master.zip',
			'sslverify'          => true,
			'requires'           => '5.0',
			'tested'             => '5.5',
			'readme'             => 'README.md',
			'access_token'       => '',
		);

		new Updater( $config );
	}
}
