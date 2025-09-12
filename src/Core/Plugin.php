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
use WolfDiscography\Frontend\FrontendHandler;
use WolfDiscography\PostTypes\DiscographyPostType;
use WolfDiscography\Taxonomies\DiscographyTaxonomies;

defined( 'ABSPATH' ) || exit;

/**
 * Main Plugin Class
 *
 * Handles plugin initialization and coordination between components
 */
class Plugin {

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public const VERSION = '2.0.0';

	/**
	 * Required PHP version
	 *
	 * @var string
	 */
	private const REQUIRED_PHP_VERSION = '7.4.0';

	/**
	 * Single instance of the class
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * Support forum URL
	 *
	 * @var string
	 */
	private string $support_url = 'https://wlfthm.es/help';

	/**
	 * Template URL path
	 *
	 * @var string
	 */
	public string $template_url = '';

	/**
	 * Custom post type slug
	 *
	 * @var string
	 */
	public string $cpt_slug = 'release';

	/**
	 * Admin handler instance
	 *
	 * @var AdminHandler|null
	 */
	private ?AdminHandler $admin_handler = null;

	/**
	 * Frontend handler instance
	 *
	 * @var FrontendHandler|null
	 */
	private ?FrontendHandler $frontend_handler = null;

	/**
	 * Post type manager instance
	 *
	 * @var DiscographyPostType|null
	 */
	private ?DiscographyPostType $post_type_manager = null;

	/**
	 * Taxonomy manager instance
	 *
	 * @var DiscographyTaxonomies|null
	 */
	private ?DiscographyTaxonomies $taxonomy_manager = null;

	/**
	 * Get main Plugin instance
	 *
	 * Ensures only one instance of Plugin is loaded or can be loaded.
	 *
	 * @return Plugin Main instance
	 */
	public static function getInstance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Plugin Constructor
	 *
	 * @throws \Exception If PHP version requirement is not met
	 */
	private function __construct() {
		$this->checkPhpVersion();
		$this->defineConstants();
		$this->initHooks();
		// $this->loadDependencies();

		do_action( 'wolf_discography_loaded' );
	}

	/**
	 * Prevent cloning
	 */
	private function __clone() {}

	/**
	 * Prevent unserialization
	 */
	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize singleton' );
	}

	/**
	 * Check PHP version requirement
	 *
	 * @throws \Exception If PHP version is insufficient
	 */
	private function checkPhpVersion(): void {
		if ( version_compare( PHP_VERSION, self::REQUIRED_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'displayPhpVersionWarning' ) );
			throw new \Exception(
				sprintf(
					'Wolf Discography requires PHP %s or higher. Current version: %s',
					self::REQUIRED_PHP_VERSION,
					PHP_VERSION
				)
			);
		}
	}

	/**
	 * Display PHP version warning
	 */
	public function displayPhpVersionWarning(): void {
		?>
		<div class="notice notice-error">
			<p>
			<?php
			printf(
				esc_html__( '%1$s needs at least PHP %2$s installed on your server. You have version %3$s currently installed. Please contact your hosting service provider if you\'re not able to update PHP by yourself.', 'wolf-discography' ),
				'Discography',
				self::REQUIRED_PHP_VERSION,
				PHP_VERSION
			);
			?>
			</p>
		</div>
		<?php
	}

	/**
	 * Define plugin constants
	 */
	private function defineConstants(): void {
		$constants = array(
			'WD_DEV'         => false,
			'WD_DIR'         => $this->getPluginPath(),
			'WD_URI'         => $this->getPluginUrl(),
			'WD_CSS'         => $this->getPluginUrl() . '/assets/css',
			'WD_JS'          => $this->getPluginUrl() . '/assets/js',
			'WD_SLUG'        => plugin_basename( dirname( __DIR__, 2 ) ),
			'WD_PATH'        => plugin_basename( __FILE__ ),
			'WD_VERSION'     => self::VERSION,
			'WD_SUPPORT_URL' => $this->support_url,
			'WD_DOC_URI'     => 'https://docs.wolfthemes.com/documentation/plugins/' . plugin_basename( dirname( __DIR__, 2 ) ),
			'WD_WOLF_DOMAIN' => 'wolfthemes.com',
		);

		foreach ( $constants as $name => $value ) {
			$this->defineConstant( $name, $value );
		}
	}

	/**
	 * Define constant if not already set
	 *
	 * @param string $name  Constant name
	 * @param mixed  $value Constant value
	 */
	private function defineConstant( string $name, $value ): void {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Initialize WordPress hooks
	 */
	private function initHooks(): void {
		add_action( 'after_setup_theme', array( $this, 'includeTemplateFunctions' ), 11 );
		add_action( 'init', array( $this, 'loadDependencies' ), 0 );
		add_action( 'init', array( $this, 'init' ), 0 );

		// Handle activation
		register_activation_hook( dirname( __DIR__, 2 ) . '/wolf-discography.php', array( $this, 'activate' ) );

		// Load page builders integration if not using Wolf theme
		if ( ! $this->isWolfTheme() ) {
			add_action( 'init', array( $this, 'loadPageBuilderIntegrations' ) );
		}
	}

	/**
	 * Load plugin dependencies
	 */
	public function loadDependencies(): void {
		// Core functionality is always loaded
		$this->loadCoreFunctions();

		// Load context-specific handlers
		if ( $this->isRequest( 'admin' ) ) {
			$this->admin_handler = new AdminHandler();
		}

		if ( $this->isRequest( 'frontend' ) ) {
			$this->frontend_handler = new FrontendHandler();
		}
	}

	/**
	 * Load core functions
	 */
	private function loadCoreFunctions(): void {
		// Load legacy functions for compatibility during migration
		if ( file_exists( $this->getPluginPath() . '/inc/wd-core-functions.php' ) ) {
			include_once $this->getPluginPath() . '/inc/wd-core-functions.php';
		}
	}

	/**
	 * Plugin activation handler
	 */
	public function activate(): void {
		add_option( '_wolf_discography_needs_page', true );

		if ( ! get_option( '_wolf_discography_flush_rewrite_rules_flag' ) ) {
			add_option( '_wolf_discography_flush_rewrite_rules_flag', true );
		}
	}

	/**
	 * Initialize plugin
	 */
	public function init(): void {
		do_action( 'before_wolf_discography_init' );

		// Set up localization - moved here to fix translation loading timing
		$this->loadPluginTextdomain();

		// Set template URL
		$this->template_url = apply_filters( 'wolf_discography_url', 'wolf-discography/' );

		// Initialize post types and taxonomies
		$this->post_type_manager = new DiscographyPostType();
		$this->taxonomy_manager  = new DiscographyTaxonomies();

		// Register post types and taxonomies
		$this->post_type_manager->register();
		$this->taxonomy_manager->register();

		// Flush rewrite rules if needed
		$this->flushRewriteRules();

		// Initialize widgets
		add_action( 'widgets_init', array( $this, 'registerWidgets' ) );

		do_action( 'wolf_discography_init' );
	}

	/**
	 * Load page builder integrations
	 */
	public function loadPageBuilderIntegrations(): void {
		// Load module parameters
		$module_params_file = $this->getPluginPath() . '/inc/module-params.php';
		if ( file_exists( $module_params_file ) ) {
			require_once $module_params_file;
		}

		// Elementor integration
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			add_action( 'elementor/widgets/widgets_registered', array( $this, 'initElementorWidgets' ) );
		}

		// WPBakery integration
		if ( defined( 'WPB_VC_VERSION' ) ) {
			add_action( 'init', array( $this, 'includeVcModules' ) );
		}
	}

	/**
	 * Include template functions
	 */
	public function includeTemplateFunctions(): void {
		$template_functions_file = $this->getPluginPath() . '/inc/frontend/wd-template-functions.php';
		if ( file_exists( $template_functions_file ) ) {
			include_once $template_functions_file;
		}
	}

	/**
	 * Initialize Elementor widgets
	 */
	public function initElementorWidgets(): void {
		if ( post_type_exists( $this->cpt_slug ) ) {
			$elementor_file = $this->getPluginPath() . '/elementor/' . sanitize_title_with_dashes( $this->cpt_slug ) . '-index.php';
			if ( file_exists( $elementor_file ) ) {
				require_once $elementor_file;
			}
		}
	}

	/**
	 * Include WPBakery modules
	 */
	public function includeVcModules(): void {
		if ( post_type_exists( $this->cpt_slug ) ) {
			$vc_file = $this->getPluginPath() . '/vc/' . sanitize_title_with_dashes( $this->cpt_slug ) . '-index.php';
			if ( file_exists( $vc_file ) ) {
				require_once $vc_file;
			}
		}
	}

	/**
	 * Register widgets
	 */
	public function registerWidgets(): void {
		// Legacy widget files - keep during migration
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

		// Register legacy widgets
		if ( class_exists( 'WD_Widget_Discography' ) ) {
			register_widget( 'WD_Widget_Discography' );
		}
		if ( class_exists( 'WD_Widget_Last_Release' ) ) {
			register_widget( 'WD_Widget_Last_Release' );
		}
	}

	/**
	 * Flush rewrite rules if needed
	 */
	public function flushRewriteRules(): void {
		if ( get_option( '_wolf_discography_flush_rewrite_rules_flag' ) ) {
			flush_rewrite_rules();
			delete_option( '_wolf_discography_flush_rewrite_rules_flag' );
		}
	}

	/**
	 * Load plugin text domain
	 */
	public function loadPluginTextdomain(): void {
		$domain = 'wolf-discography';
		$locale = apply_filters( 'wolf-discography', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ), 2 ) . '/languages/' );
	}

	/**
	 * Check what type of request this is
	 *
	 * @param string $type Request type (admin, ajax, cron, frontend)
	 * @return bool
	 */
	public function isRequest( string $type ): bool {
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

	/**
	 * Check if current installation uses WolfThemes
	 *
	 * @return bool
	 */
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

	/**
	 * Get the plugin URL
	 *
	 * @return string
	 */
	public function getPluginUrl(): string {
		return untrailingslashit( plugins_url( '/', dirname( __DIR__, 2 ) . '/wolf-discography.php' ) );
	}

	/**
	 * Get the plugin path
	 *
	 * @return string
	 */
	public function getPluginPath(): string {
		return untrailingslashit( plugin_dir_path( dirname( __DIR__, 2 ) . '/wolf-discography.php' ) );
	}



	/**
	 * Get the template path
	 *
	 * @return string
	 */
	public function getTemplatePath(): string {
		return apply_filters( 'wd_template_path', 'wolf-discography/' );
	}

	/**
	 * Get plugin version
	 *
	 * @return string
	 */
	public function getVersion(): string {
		return self::VERSION;
	}

	/**
	 * Get CPT slug
	 *
	 * @return string
	 */
	public function getCptSlug(): string {
		return $this->cpt_slug;
	}

	/**
	 * Legacy functions
	 *
	 * @return string
	 */
	public function is_wolf_theme(): string {
		return $this->isWolfTheme();
	}

	public function plugin_path(): string {
		return $this->getPluginPath();
	}
}