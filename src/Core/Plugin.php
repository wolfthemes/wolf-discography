<?php
/**
 * Main Plugin Class
 *
 * @package WolfDiscography
 * @subpackage Core
 * @since 2.0.0
 */

namespace WolfDiscography\Core;

use WolfDiscography\Admin\AdminHandler;
use WolfDiscography\Admin\AdminNotices;
use WolfDiscography\Frontend\FrontendHandler;
use WolfDiscography\PostTypes\PostType;
use WolfDiscography\Taxonomies\Taxonomies;

defined( 'ABSPATH' ) || exit;

class Plugin {

	private static ?Plugin $instance = null;

	public string $template_url = '';
	public string $cpt_slug     = 'release';

	private ?AdminHandler $admin_handler       = null;
	private ?FrontendHandler $frontend_handler = null;
	private ?PostType $post_type_manager       = null;
	private ?Taxonomies $taxonomy_manager      = null;
	private ?AdminNotices $admin_notices       = null;

	public static function getInstance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->checkPhpVersion();
		Constants::define( $this->getPluginPath(), $this->getPluginUrl() );
		$this->initHooks();

		do_action( 'wolf_discography_loaded' );
	}

	private function __clone() {}
	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize singleton' );
	}

	private function checkPhpVersion(): void {
		if ( version_compare( PHP_VERSION, Constants::REQUIRED_PHP_VERSION, '<' ) ) {
			$this->admin_notices = new AdminNotices();
		}
	}

	private function initHooks(): void {
		add_action( 'after_setup_theme', array( $this, 'includeTemplateFunctions' ), 11 );
		add_action( 'init', array( $this, 'loadCoreFunctions' ), 0 );
		add_action( 'init', array( $this, 'init' ), 0 );

		register_activation_hook( $this->getPluginPath() . '/wolf-discography.php', array( $this, 'activate' ) );

		if ( ! $this->isWolfTheme() ) {
			add_action( 'init', array( $this, 'loadPageBuilderIntegrations' ) );
		}
	}

	public function loadCoreFunctions(): void {
		$core_file = $this->getPluginPath() . '/inc/wd-core-functions.php';
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

		$this->loadPluginTextdomain();
		$this->template_url = apply_filters( 'wolf_discography_url', 'wolf-discography/' );

		$this->initializeComponents();

		$this->flushRewriteRules();
		add_action( 'widgets_init', array( $this, 'registerWidgets' ) );

		do_action( 'wolf_discography_init' );
	}

	private function initializeComponents(): void {
		$this->post_type_manager = new PostType();
		$this->taxonomy_manager  = new Taxonomies();

		$this->post_type_manager->register();
		$this->taxonomy_manager->register();

		if ( $this->isRequest( 'admin' ) ) {
			$this->admin_handler = new AdminHandler();
		}

		if ( $this->isRequest( 'frontend' ) ) {
			$this->frontend_handler = new FrontendHandler();
		}
	}

	private function isRequest( string $type ): bool {
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

	// Rest of methods stay the same...
	public function loadPageBuilderIntegrations(): void {
		/* $module_params_file = $this->getPluginPath() . '/inc/module-params.php'; */
		/* if ( file_exists( $module_params_file ) ) { */
		/* 	require_once $module_params_file; */
		/* } */

		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			include_once $this->getPluginPath() . '/inc/wd-elementor-functions.php';
			add_action( 'elementor/widgets/widgets_registered', array( $this, 'initElementorWidgets' ) );
		}

		if ( defined( 'WPB_VC_VERSION' ) ) {
			add_action( 'init', array( $this, 'includeVcModules' ) );
		}
	}

	public function includeTemplateFunctions(): void {
		$template_file = $this->getPluginPath() . '/inc/frontend/wd-template-functions.php';
		if ( file_exists( $template_file ) ) {
			include_once $template_file;
		}
	}

	public function initElementorWidgets(): void {
		if ( post_type_exists( $this->cpt_slug ) ) {
			$elementor_file = $this->getPluginPath() . '/elementor/' . sanitize_title_with_dashes( $this->cpt_slug ) . '-index.php';
			if ( file_exists( $elementor_file ) ) {
				require_once $elementor_file;
			}
		}
	}

	public function includeVcModules(): void {
		if ( post_type_exists( $this->cpt_slug ) ) {
			$vc_file = $this->getPluginPath() . '/vc/' . sanitize_title_with_dashes( $this->cpt_slug ) . '-index.php';
			if ( file_exists( $vc_file ) ) {
				require_once $vc_file;
			}
		}
	}

	public function registerWidgets(): void {
		$widget_files = array(
			'class-wd-widget-discography.php',
			'class-wd-widget-last-release.php',
		);

		foreach ( $widget_files as $widget_file ) {
			$file_path = $this->getPluginPath() . '/inc/widgets/' . $widget_file;
			if ( file_exists( $file_path ) ) {
				include_once $file_path;
			}
		}

		if ( class_exists( 'WD_Widget_Discography' ) ) {
			register_widget( 'WD_Widget_Discography' );
		}
		if ( class_exists( 'WD_Widget_Last_Release' ) ) {
			register_widget( 'WD_Widget_Last_Release' );
		}
	}

	public function flushRewriteRules(): void {
		if ( get_option( '_wolf_discography_flush_rewrite_rules_flag' ) ) {
			flush_rewrite_rules();
			delete_option( '_wolf_discography_flush_rewrite_rules_flag' );
		}
	}

	public function loadPluginTextdomain(): void {
		$domain = Constants::TEXT_DOMAIN;
		$locale = apply_filters( 'wolf-discography', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->getPluginPath() . '/wolf-discography.php' ) ) . '/languages/' );
	}

	public function isWolfTheme(): bool {
		$theme      = wp_get_theme();
		$author     = $theme->get( 'Author' );
		$author_uri = $theme->get( 'AuthorURI' );

		return (
			stripos( $author, 'wolf' ) !== false ||
			stripos( $author_uri, 'wolfthemes' ) !== false ||
			stripos( $author_uri, 'wpwolf' ) !== false
		);
	}

	public function getPluginUrl(): string {
		return untrailingslashit( plugins_url( '/', dirname( __DIR__, 2 ) . '/wolf-discography.php' ) );
	}

	public function getPluginPath(): string {
		return untrailingslashit( plugin_dir_path( dirname( __DIR__, 2 ) . '/wolf-discography.php' ) );
	}

	public function getTemplatePath(): string {
		return apply_filters( 'wd_template_path', 'wolf-discography/' );
	}

	public function getVersion(): string {
		return Constants::VERSION;
	}

	public function getCptSlug(): string {
		return $this->cpt_slug;
	}

	// Legacy compatibility
	public function is_wolf_theme(): bool {
		return $this->isWolfTheme();
	}

	public function plugin_path(): string {
		return $this->getPluginPath();
	}
}