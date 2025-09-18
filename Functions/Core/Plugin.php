<?php
/**
 * Main Plugin Class
 *
 * @package WolfDiscography
 * @subpackage Core
 * @since 2.0.0
 */

namespace Wolf_Discography\Core;

use Wolf_Discography\Admin\Admin_Handler;
use Wolf_Discography\Admin\Admin_Notices;
use Wolf_Discography\Frontend\Frontend_Handler;
use Wolf_Discography\Post_Types\Post_Type;
use Wolf_Discography\Taxonomies\Taxonomies;
use Wolf_Discography\Block_Theme\Template_Support;
use Wolf_Discography\Gutenberg\Block_Registration;
use Wolf_Discography\Page_Builders\WPBakery_Template_Handler;
use Wolf_Discography\Widgets\Discography_Widget;
use Wolf_Discography\Widgets\Last_Release_Widget;

defined( 'ABSPATH' ) || exit;

class Plugin {

	private static ?Plugin $instance = null;

	public string $template_url = '';
	public string $cpt_slug     = 'release';

	private ?Admin_Handler $admin_handler       = null;
	private ?Frontend_Handler $frontend_handler = null;
	private ?Post_Type $post_type_manager       = null;
	private ?Taxonomies $taxonomy_manager       = null;
	private ?Admin_Notices $admin_notices       = null;

	public static function get_instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {

		$this->check_php_version();
		Constants::define( $this->get_plugin_path(), $this->getPluginUrl() );
		$this->init_hooks();

		do_action( 'wolf_discography_loaded' );
	}

	private function __clone() {}
	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize singleton' );
	}

	private function check_php_version(): void {
		if ( version_compare( PHP_VERSION, Constants::REQUIRED_PHP_VERSION, '<' ) ) {
			$this->admin_notices = new Admin_Notices();
		}
	}

	private function init_hooks(): void {

		add_action( 'after_setup_theme', array( $this, 'init_block_theme_support' ) );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'init', array( $this, 'load_legacy_functions' ), 0 );
		add_action( 'init', array( $this, 'load_pagebuilder_integrations' ) );

		register_activation_hook( $this->get_plugin_path() . '/wolf-discography.php', array( $this, 'activate' ) );
	}

	public function load_legacy_functions(): void {
		$core_file = $this->get_plugin_path() . '/inc/legacy-functions.php';
		if ( file_exists( $core_file ) ) {
			include_once $core_file;
		}
	}

	public function activate(): void {
		add_option( '_wolf_discography_needs_page', true );
		if ( ! get_option( '_wolf_discography_flush_rewrite_rules_flag' ) ) {
			add_option( '_wolf_discography_flush_rewrite_rules_flag', true );
		}
	}

	public function init(): void {
		do_action( 'before_wolf_discography_init' );

		$this->load_plugin_textdomain();
		$this->template_url = apply_filters( 'wolf_discography_url', 'wolf-discography/' );

		$this->initialize_components();

		$this->flush_rewrite_rules();
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		do_action( 'wolf_discography_init' );
	}

	private function initialize_components(): void {
		$this->post_type_manager = new Post_Type();
		$this->taxonomy_manager  = new Taxonomies();

		$this->post_type_manager->register();
		$this->taxonomy_manager->register();

		if ( $this->is_request( 'admin' ) ) {
			$this->admin_handler = new Admin_Handler();
		}

		if ( $this->is_request( 'frontend' ) ) {
			$this->frontend_handler = new Frontend_Handler();
		}
	}

	private function is_request( string $type ): bool {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			default:
				return false;
		}
	}

	public function init_block_theme_support() {
		if ( wp_is_block_theme() || class_exists( 'WP_Block_Editor_Context' ) ) {
			new Template_Support();
			new Block_Registration();
		}
	}

	public function load_pagebuilder_integrations(): void {

		if ( ! $this->theme_supports_v2() ) {
			return;
		}

		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			add_action( 'elementor/widgets/widgets_registered', array( $this, 'init_elementor_widgets' ) );
		}

		if ( defined( 'WPB_VC_VERSION' ) ) {
			$this->include_vc_modules();
		}
	}

	public function init_elementor_widgets(): void {
		if ( post_type_exists( $this->cpt_slug ) ) {
			$elementor_file = $this->get_plugin_path() . '/elementor/' . sanitize_title_with_dashes( $this->cpt_slug ) . '-index.php';
			if ( file_exists( $elementor_file ) ) {
				require_once $elementor_file;
			}
		}
	}

	public function include_vc_modules(): void {

		new WPBakery_Template_Handler();

		$vc_file = $this->get_plugin_path() . '/vc/' . sanitize_title_with_dashes( $this->cpt_slug ) . '-index.php';
		if ( file_exists( $vc_file ) ) {
			require_once $vc_file;
		}
	}

	public function register_widgets(): void {
		register_widget( Discography_Widget::class );
		register_widget( Last_Release_Widget::class );
	}

	public function flush_rewrite_rules(): void {
		if ( get_option( '_wolf_discography_flush_rewrite_rules_flag' ) ) {
			flush_rewrite_rules();
			delete_option( '_wolf_discography_flush_rewrite_rules_flag' );
		}
	}

	public function load_plugin_textdomain(): void {
		$domain = Constants::TEXT_DOMAIN;
		$locale = apply_filters( 'wolf-discography', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->get_plugin_path() . '/wolf-discography.php' ) ) . '/languages/' );
	}

	public function is_wolf_theme(): bool {
		$theme      = wp_get_theme();
		$author     = $theme->get( 'Author' );
		$author_uri = $theme->get( 'AuthorURI' );

		return (
			stripos( $author, 'wolf' ) !== false ||
			stripos( $author_uri, 'wolfthemes' ) !== false ||
			stripos( $author_uri, 'wpwolf' ) !== false
		);
	}

	public function theme_supports_v2() {
		return current_theme_supports( 'wolf-discography-v2' ) || ! $this->is_wolf_theme();
	}

	public function getPluginUrl(): string {
		return untrailingslashit( plugins_url( '/', dirname( __DIR__, 2 ) . '/wolf-discography.php' ) );
	}

	public function get_plugin_path(): string {
		return untrailingslashit( plugin_dir_path( dirname( __DIR__, 2 ) . '/wolf-discography.php' ) );
	}

	public function get_template_path(): string {
		return apply_filters( 'wd_template_path', 'wolf-discography/' );
	}

	public function get_version(): string {
		return Constants::VERSION;
	}

	public function get_cpt_slug(): string {
		return $this->cpt_slug;
	}

	public function plugin_path(): string {
		return $this->get_plugin_path();
	}
}