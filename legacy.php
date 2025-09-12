<?php
/**
 * Legacy Wolf_Discography Class (renamed but unchanged)
 * This ensures backward compatibility during migration
 */
if ( ! class_exists( 'Wolf_Discography_Legacy' ) ) {

	class Wolf_Discography_Legacy {

		/**
		 * @var string
		 */
		private $required_php_version = '7.4.0';

		/**
		 * @var string
		 */
		public $version = '1.6.0';

		/**
		 * @var Wolf_Discography_Legacy The single instance of the class
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
		 * @var string
		 */
		public $cpt_slug = 'release';

		/**
		 * Main Discography Instance
		 *
		 * Ensures only one instance of Discography is loaded or can be loaded.
		 *
		 * @static
		 * @return Wolf_Discography_Legacy - Main instance
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
			$this->init_hooks();

			do_action( 'wolf_discography_loaded' );
		}

		/**
		 * Display error notice if PHP version is too low
		 */
		public function warning_php_version() {
			?>
			<div class="notice notice-error">
				<p>
				<?php
				printf(
					esc_html__( '%1$s needs at least PHP %2$s installed on your server. You have version %3$s currently installed. Please contact your hosting service provider if you\'re not able to update PHP by yourself.', 'wolf-discography' ),
					'Discography',
					$this->required_php_version,
					phpversion()
				);
				?>
				</p>
			</div>
			<?php
		}

		/**
		 * Hook into actions and filters
		 */
		private function init_hooks() {
			add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
			add_action( 'init', array( $this, 'includes' ), 0 );
			add_action( 'init', array( $this, 'init' ), 0 );

			if ( ! $this->isWolfTheme() ) {
				require_once $this->plugin_path() . '/inc/module-params.php';
				add_action( 'init', array( $this, 'include_vc_modules' ) );
				add_action( 'elementor/widgets/widgets_registered', array( $this, 'init_elementor_widgets' ) );
			}

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
		public function flush_rewrite_rules() {
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
				'WD_DEV'         => false,
				'WD_DIR'         => $this->plugin_path(),
				'WD_URI'         => $this->plugin_url(),
				'WD_CSS'         => $this->plugin_url() . '/assets/css',
				'WD_JS'          => $this->plugin_url() . '/assets/js',
				'WD_SLUG'        => plugin_basename( __DIR__ ),
				'WD_PATH'        => plugin_basename( __FILE__ ),
				'WD_VERSION'     => $this->version,
				'WD_SUPPORT_URL' => $this->support_url,
				'WD_DOC_URI'     => 'https://docs.wolfthemes.com/documentation/plugins/' . plugin_basename( __DIR__ ),
				'WD_WOLF_DOMAIN' => 'wolfthemes.com',
			);

			foreach ( $constants as $name => $value ) {
				$this->define( $name, $value );
			}
		}

		/**
		 * Define constant if not already set
		 *
		 * @param  string      $name
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
		 *
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin':
					return is_admin();
				case 'ajax':
					return defined( 'DOING_AJAX' );
				case 'cron':
					return defined( 'DOING_CRON' );
				case 'frontend':
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
			include_once 'inc/wd-core-functions.php';

			if ( ! $this->isWolfTheme() ) {
				if ( defined( 'ELEMENTOR_VERSION' ) ) {
					include_once 'inc/wd-elementor-functions.php';
				}

				if ( defined( 'WPB_VC_VERSION' ) ) {
					include_once 'inc/wd-vc-functions.php';
				}
			}

			if ( $this->is_request( 'admin' ) ) {
				include_once 'inc/admin/class-wd-admin.php';
			}

			if ( $this->is_request( 'frontend' ) ) {
				include_once 'inc/frontend/wd-functions.php';
				include_once 'inc/frontend/wd-helpers.php';
				include_once 'inc/frontend/wd-image-functions.php';
				include_once 'inc/frontend/wd-template-hooks.php';
				include_once 'inc/frontend/wd-posts.php';
				include_once 'inc/frontend/class-wd-shortcode.php';
				include_once 'inc/frontend/class-wd-template-manager.php';
			}
		}

		// ... (rest of your methods remain exactly the same)

		public function init_elementor_widgets() {
			if ( post_type_exists( $this->cpt_slug ) ) {
				require_once $this->plugin_path() . '/elementor/' . sanitize_title_with_dashes( $this->cpt_slug ) . '-index.php';
			}
		}

		public function include_vc_modules() {
			if ( defined( 'WPB_VC_VERSION' ) && post_type_exists( $this->cpt_slug ) ) {
				require_once $this->plugin_path() . '/vc/' . sanitize_title_with_dashes( $this->cpt_slug ) . '-index.php';
			}
		}

		public function include_template_functions() {
			include_once 'inc/frontend/wd-template-functions.php';
		}

		public function register_widget() {
			include_once 'inc/widgets/class-wd-widget-discography.php';
			include_once 'inc/widgets/class-wd-widget-last-release.php';

			register_widget( 'WD_Widget_Discography' );
			register_widget( 'WD_Widget_Last_Release' );
		}

		public function init() {
			do_action( 'before_wolf_discography_init' );

			$this->load_plugin_textdomain();

			$this->template_url = apply_filters( 'wolf_discography_url', 'wolf-discography/' );

			if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
				// add_filter('template_include', array($this, 'template_loader'));
			}

			add_action( 'widgets_init', array( $this, 'register_widget' ) );

			$this->register_post_type();
			$this->register_taxonomy();
			$this->flush_rewrite_rules();

			do_action( 'wolf_discography_init' );
		}

		public function register_post_type() {
			include_once 'inc/wd-register-post-type.php';
		}

		public function register_taxonomy() {
			include_once 'inc/wd-register-taxonomy.php';
		}

		public function template_loader( $template ) {
			$find = array( 'wolf-discography.php' );
			$file = '';

			if ( is_single() && $this->cpt_slug == get_post_type() ) {
				$file   = 'single-' . $this->cpt_slug . '.php';
				$find[] = $file;
				$find[] = $this->template_url . $file;

			} elseif ( is_tax( 'band' ) || is_tax( 'label' ) || is_tax( 'release_genre' ) ) {
				$term = get_queried_object();

				$file   = 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = $this->template_url . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = $file;
				$find[] = $this->template_url . $file;

			} elseif ( is_post_type_archive( $this->cpt_slug ) ) {
				$file   = 'archive-' . $this->cpt_slug . '.php';
				$find[] = $file;
				$find[] = $this->template_url . $file;
			}

			if ( $file ) {
				$template = locate_template( $find );
				if ( ! $template ) {
					$template = $this->plugin_path() . '/templates/' . $file;
				}
			}

			return $template;
		}

		public function load_plugin_textdomain() {
			$domain = 'wolf-discography';
			$locale = apply_filters( 'wolf-discography', get_locale(), $domain );
			load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		public function template_path() {
			return apply_filters( 'wd_template_path', 'wolf-discography/' );
		}

		public function isWolfTheme() {
			$theme      = wp_get_theme();
			$author     = $theme->get( 'Author' );
			$author_uri = $theme->get( 'AuthorURI' );

			if (
				stripos( $author, 'wolf' ) !== false ||
				stripos( $author_uri, 'wolfthemes' ) !== false ||
				stripos( $author_uri, 'wpwolf' ) !== false
			) {
				return true;
			}

			return false;
		}

		public function plugin_update() {
			if ( ! class_exists( 'WP_GitHub_Updater' ) ) {
				include_once 'inc/admin/updater.php';
			}

			$repo = 'wolfthemes/wolf-discography';

			$config = array(
				'slug'               => plugin_basename( __FILE__ ),
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

			new WP_GitHub_Updater( $config );
		}
	}
}
