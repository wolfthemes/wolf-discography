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

// Toggle between legacy and namespaced approach
define( 'WD_USE_LEGACY', false );

// Load Composer autoloader if available
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

// Initialize plugin - only once
if ( class_exists( 'WolfDiscography\Core\Plugin' ) && ! WD_USE_LEGACY ) {
	// New namespaced approach
	try {
		\WolfDiscography\Core\Plugin::getInstance();
	} catch ( Exception $e ) {
		// Fallback to legacy if something goes wrong
		error_log( 'Wolf Discography Namespace Error: ' . $e->getMessage() );
		require_once __DIR__ . '/legacy.php';
		Wolf_Discography_Legacy::instance();
	}
} else {
	// Legacy approach
	require_once __DIR__ . '/legacy.php';
	Wolf_Discography_Legacy::instance();
}

/**
 * Backward compatibility function
 * This ensures existing code that calls WD() still works
 *
 * @return Wolf_Discography_Legacy|WolfDiscography\Core\Plugin
 */
function WD() {
	if ( class_exists( 'WolfDiscography\Core\Plugin' ) && ! WD_USE_LEGACY ) {
		return \WolfDiscography\Core\Plugin::getInstance();
	}
	return Wolf_Discography_Legacy::instance();
}