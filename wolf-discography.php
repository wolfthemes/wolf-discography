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
if ( class_exists( 'WolfDiscography\Core\Plugin' ) ) {
	// New namespaced approach
	try {
		\WolfDiscography\Core\Plugin::getInstance();
	} catch ( Exception $e ) {
		// Fallback to legacy if something goes wrong
		error_log( 'Wolf Discography Namespace Error: ' . $e->getMessage() );
	}
}

/**
 * Backward compatibility function
 * This ensures existing code that calls WD() still works
 *
 * @return Wolf_Discography_Legacy|WolfDiscography\Core\Plugin
 */
function WD() {
	if ( class_exists( 'WolfDiscography\Core\Plugin' ) ) {
		return \WolfDiscography\Core\Plugin::getInstance();
	}
}
