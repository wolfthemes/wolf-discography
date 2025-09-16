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

use WolfDiscography\Admin;
use WolfDiscography\Core\Core;

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

		add_filter( 'display_post_states', array( $this, 'custom_post_state' ), 10, 2 );
		add_filter( 'plugin_action_links_' . plugin_basename( WD()->get_plugin_path() . '/wolf-discography.php' ), array( $this, 'settings_action_links' ) );
	}

	/**
	 * Load admin-related classes and files
	 */
	private function load_admin_classes(): void {

		// Load specialized admin classes
		new PageSetupNotices();  // Handles page creation notices
		new MetaboxManager();    // Handles all metaboxes
		new AdminColumns();      // Handles admin list columns
		new Options();           // Handles settings page
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
	/**
	 * Display archive page state
	 *
	 * @param array  $states
	 * @param object $post
	 * @return array $states
	 */
	public function custom_post_state( $states, $post ) {

		if ( 'page' == get_post_type( $post->ID ) && absint( $post->ID ) === Core::get_discography_page_id() ) {

			$states[] = esc_html__( 'Discography Page', 'wolf-discography' );
		}

		return $states;
	}

	/**
	 * add settings link in plugin page
	 */
	public function settings_action_links( $links ) {
		$setting_link = array(
			'<a href="' . admin_url( 'edit.php?post_type=release&page=wolf-discography-settings' ) . '">' . esc_html__( 'settings', 'wolf-discography' ) . '</a>',
		);
		return array_merge( $links, $setting_link );
	}

	public function plugin_update() {

		$repo = 'wolfthemes/wolf-discography';

		$config = array(
			'slug'               => plugin_basename( WD()->get_plugin_path() . '/wolf-discography.php' ),
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
