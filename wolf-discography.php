<?php
/**
 * Plugin Name: Discography
 * Plugin URI: https://wlfthm.es/wolf-discography
 * Description: A discography plugin to display your releases.
 * Version: 1.5.1
 * Author: WolfThemes
 * Author URI: https://wolfthemes.com
 * Requires at least: 5.0
 * Tested up to: 5.5
 *
 * Text Domain: wolf-discography
 * Domain Path: /languages/
 *
 * @package WolfDiscography
 * @category Core
 * @author WolfThemes
 *
 * Verified customers who have purchased a premium theme at https://wlfthm.es/tf/
 * will have access to support for this plugin in the forums
 * https://wlfthm.es/help/
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Wolf_Discography' ) ) {

	/**
	 * Main Wolf_Discography Class
	 *
	 * Contains the main functions for Wolf_Discography
	 *
	 * @class Wolf_Discography
	 * @version 1.5.1
	 * @since 1.0.0
	 * @package WolfDiscography
	 * @author WolfThemes
	 */
	class Wolf_Discography {

		/**
		 * @var string
		 */
		private $required_php_version = '5.4.0';

		/**
		 * @var string
		 */
		public $version = '1.5.1';

		/**
		 * @var Discography The single instance of the class
		 */
		protected static $_instance = null;



		/**
		 * @var the support forum URL
		 */
		private $support_url = 'https://wlfthm.es/help';

		/**
		 * @var string
		 */
		public $template_url;

		/**
		 * Main Discography Instance
		 *
		 * Ensures only one instance of Discography is loaded or can be loaded.
		 *
		 * @static
		 * @see WD()
		 * @return Discography - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Discography Constructor.
		 */
		public function __construct() {

			if ( phpversion() < $this->required_php_version ) {
				add_action( 'admin_notices', array( $this, 'warning_php_version' ) );
				return;
			}

			$this->define_constants();
			$this->includes();
			$this->init_hooks();

			do_action( 'wolf_discography_loaded' );
		}

		/**
		 * Display error notice if PHP version is too low
		 */
		public function warning_php_version() {
			?>
			<div class="notice notice-error">
				<p><?php

				printf(
					esc_html__( '%1$s needs at least PHP %2$s installed on your server. You have version %3$s currently installed. Please contact your hosting service provider if you\'re not able to update PHP by yourself.', 'wolf-discography' ),
					'Discography',
					$this->required_php_version,
					phpversion()
				);
				?></p>
			</div>
			<?php
		}

		/**
		 * Hook into actions and filters
		 */
		private function init_hooks() {
			add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
			add_action( 'init', array( $this, 'init' ), 0 );
			register_activation_hook( __FILE__, array( $this, 'activate' ) );
		}

		/**
		 * Activation function
		 */
		public function activate() {

			add_option( '_wolf_discography_needs_page', true );

			if ( ! get_option( '_wolf_discography_flush_rewrite_rules_flag' ) ) {
				add_option( '_wolf_discography_flush_rewrite_rules_flag', true );
			}
		}

		/**
		 * Flush rewrite rules on plugin activation to avoid 404 error
		 */
		public function flush_rewrite_rules(){
			if ( get_option( '_wolf_discography_flush_rewrite_rules_flag' ) ) {
				flush_rewrite_rules();
				delete_option( '_wolf_discography_flush_rewrite_rules_flag' );
			}
		}

		/**
		 * Define WD Constants
		 */
		private function define_constants() {

			$constants = array(
				'WD_DEV' => false,
				'WD_DIR' => $this->plugin_path(),
				'WD_URI' => $this->plugin_url(),
				'WD_CSS' => $this->plugin_url() . '/assets/css',
				'WD_JS' => $this->plugin_url() . '/assets/js',
				'WD_SLUG' => plugin_basename( dirname( __FILE__ ) ),
				'WD_PATH' => plugin_basename( __FILE__ ),
				'WD_VERSION' => $this->version,
				'WD_SUPPORT_URL' => $this->support_url,
				'WD_DOC_URI' => 'https://docs.wolfthemes.com/documentation/plugins/' . plugin_basename( dirname( __FILE__ ) ),
				'WD_WOLF_DOMAIN' => 'wolfthemes.com',
			);

			foreach ( $constants as $name => $value ) {
				$this->define( $name, $value );
			}
		}

		/**
		 * Define constant if not already set
		 * @param  string $name
		 * @param  string|bool $value
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * What type of request is this?
		 * string $type ajax, frontend or admin
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin' :
					return is_admin();
				case 'ajax' :
					return defined( 'DOING_AJAX' );
				case 'cron' :
					return defined( 'DOING_CRON' );
				case 'frontend' :
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {

			/**
			 * Functions used in frontend and admin
			 */
			include_once( 'inc/wd-core-functions.php' );

			if ( $this->is_request( 'admin' ) ) {
				include_once( 'inc/admin/class-wd-admin.php' );
			}

			if ( $this->is_request( 'frontend' ) ) {
				include_once( 'inc/frontend/wd-functions.php' );
				include_once( 'inc/frontend/wd-template-hooks.php' );
				include_once( 'inc/frontend/class-wd-shortcode.php' );
			}
		}

		/**
		 * Function used to Init Discography Template Functions - This makes them pluggable by plugins and themes.
		 */
		public function include_template_functions() {
			include_once( 'inc/frontend/wd-template-functions.php' );
		}

		/**
		 * register_widget function.
		 *
		 * @access public
		 * @return void
		 */
		public function register_widget() {

			// Include
			include_once( 'inc/widgets/class-wd-widget-discography.php' );
			include_once( 'inc/widgets/class-wd-widget-last-release.php' );

			// Register widgets
			register_widget( 'WD_Widget_Discography' );
			register_widget( 'WD_Widget_Last_Release' );
		}

		/**
		 * Init Discography when WordPress Initialises.
		 */
		public function init() {
			// Before init action
			do_action( 'before_wolf_discography_init' );

			// Set up localisation
			$this->load_plugin_textdomain();

			// Variables
			$this->template_url = apply_filters( 'wolf_discography_url', 'wolf-discography/' );

			// Classes/actions loaded for the frontend and for ajax requests
			if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {

				// Hooks
				add_filter( 'template_include', array( $this, 'template_loader' ) );

			}

			// Hooks
			add_action( 'widgets_init', array( $this, 'register_widget' ) );

			$this->register_post_type();
			$this->register_taxonomy();
			$this->flush_rewrite_rules();

			// Init action
			do_action( 'wolf_discography_init' );
		}

		/**
		 * Register post type
		 */
		public function register_post_type() {
			include_once( 'inc/wd-register-post-type.php' );
		}

		/**
		 * Register taxonomy
		 */
		public function register_taxonomy() {
			include_once( 'inc/wd-register-taxonomy.php' );
		}

		/**
		 * Load a template.
		 *
		 * Handles template usage so that we can use our own templates instead of the themes.
		 *
		 * @param mixed $template
		 * @return string
		 */
		public function template_loader( $template ) {

			$find = array( 'wolf-discography.php' ); // nope! not used
			$file = '';

			if ( is_single() && 'release' == get_post_type() ) {

				$file    = 'single-release.php';
				$find[] = $file;
				$find[] = $this->template_url . $file;

			} elseif ( is_tax( 'band' ) || is_tax( 'label' ) || is_tax( 'release_genre' ) ) {

				$term = get_queried_object();

				$file 	= 'taxonomy-' . $term->taxonomy . '.php';
				$find[] 	= 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] 	= $this->template_url . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] 	= $file;
				$find[] 	= $this->template_url . $file;

			} elseif ( is_post_type_archive( 'release' ) ) {

				$file = 'archive-release.php';
				$find[] = $file;
				$find[] = $this->template_url . $file;

			}

			if ( $file ) {
				$template = locate_template( $find );
				if ( ! $template ) $template = $this->plugin_path() . '/templates/' . $file;
			}

			return $template;
		}

		/**
		 * Loads the plugin text domain for translation
		 */
		public function load_plugin_textdomain() {

			$domain = 'wolf-discography';
			$locale = apply_filters( 'wolf-discography', get_locale(), $domain );
			load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Get the plugin url.
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Get the template path.
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'wd_template_path', 'wolf-discography/' );
		}

			/**
		 * Plugin update
		 */
		public function plugin_update() {

			if ( ! class_exists( 'WP_GitHub_Updater' ) ) {
				include_once 'inc/admin/updater.php';
			}

			$repo = 'wolfthemes/wolf-discography';

			$config = array(
				'slug' => plugin_basename( __FILE__ ),
				'proper_folder_name' => 'wolf-discography',
				'api_url' => 'https://api.github.com/repos/' . $repo . '',
				'raw_url' => 'https://raw.github.com/' . $repo . '/master/',
				'github_url' => 'https://github.com/' . $repo . '',
				'zip_url' => 'https://github.com/' . $repo . '/archive/master.zip',
				'sslverify' => true,
				'requires' => '5.0',
				'tested' => '5.5',
				'readme' => 'README.md',
				'access_token' => '',
			);

			new WP_GitHub_Updater( $config );
		}
	} // end class

} // end class exists check

/**
 * Returns the main instance of WD to prevent the need to use globals.
 *
 * @return Wolf_Discography
 */
function WD() {
	return Wolf_Discography::instance();
}

WD(); // Go
