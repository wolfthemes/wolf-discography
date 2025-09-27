<?php
/**
 * Plugin Name: Discography
 * Plugin URI: https://wlfthm.es/wolf-discography
 * Description: A Professional Music Release Manager.
 * Version: 2.0.0
 * Author: WolfThemes
 * Author URI: https://wolfthemes.com
 * Requires at least: 6.0
 * Tested up to: 6.8
 *
 * Text Domain: wolf-discography
 * Domain Path: /languages/
 *
 * @package WolfDiscography
 * @category Core
 * @author WolfThemes
 */

defined( 'ABSPATH' ) || exit;

// Load Composer autoloader if available
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

// Initialize plugin - only once
if ( class_exists( 'Wolf_Discography\Core\Plugin' ) ) {
	// New namespaced approach
	try {
		\Wolf_Discography\Core\Plugin::get_instance();
	} catch ( Exception $e ) {
		error_log( 'Wolf Discography Namespace Error: ' . $e->getMessage() );
	}
}

/**
 * Main function to get legacy wrapper instance
 *
 * @return Wolf_Discography|null
 */
function WD() {
	if ( class_exists( 'Wolf_Discography' ) ) {
		return Wolf_Discography::instance();
	}
	return null;
}

/**
 * Wolf_Discography singleton wrapper class
 * Contains the actual plugin instance
 */
if ( ! class_exists( 'Wolf_Discography' ) ) {
	class Wolf_Discography {

		/**
		 * @var Wolf_Discography The single instance of the class
		 */
		protected static $_instance = null;

		/**
		 * @var WolfDiscography\Core\Plugin The actual plugin instance
		 */
		private $plugin_instance;

		/**
		 * Constructor - private to prevent direct instantiation
		 */
		private function __construct() {
			if ( class_exists( 'Wolf_Discography\Core\Plugin' ) ) {
				$this->plugin_instance = \Wolf_Discography\Core\Plugin::get_instance();
			}
		}

		/**
		 * Prevent cloning of the instance
		 */
		private function __clone() {
			// Empty - cloning is forbidden
		}

		/**
		 * Prevent unserialization of the instance
		 */
		public function __wakeup() {
			throw new Exception( 'Cannot unserialize singleton' );
		}

		/**
		 * Get Wolf_Discography wrapper instance
		 *
		 * @return Wolf_Discography
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Get the actual plugin instance
		 *
		 * @return WolfDiscography\Core\Plugin|null
		 */
		public function get_instance() {
			return $this->plugin_instance;
		}

		/**
		 * Magic method to forward all calls to the actual plugin instance
		 */
		public function __call( $method, $args ) {
			if ( $this->plugin_instance && method_exists( $this->plugin_instance, $method ) ) {
				return call_user_func_array( array( $this->plugin_instance, $method ), $args );
			}
			return null;
		}

		/**
		 * Magic method to forward property access to the actual plugin instance
		 */
		public function __get( $property ) {
			if ( $this->plugin_instance && property_exists( $this->plugin_instance, $property ) ) {
				return $this->plugin_instance->$property;
			}
			return null;
		}
	}
}
