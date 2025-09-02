<?php
/**
 * Content wrappers
 *
 * @author WolfThemes
 * @package WolfDiscography/Templates
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

// Check if theme supports block themes (WordPress 5.9+)
if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
	// Modern block themes (Twenty Twenty-Four, Twenty Twenty-Three, etc.)
	echo '<div class="wp-site-blocks">';
	echo '<div class="wp-block-group alignfull" style="min-height: 50vh;">';
	echo '<div class="wp-block-group__inner-container">';
	echo '<main class="wp-block-group alignwide">';
} else {
	// Classic themes and older block themes
	echo '<div id="primary" class="content-area">';
	echo '<main id="main" class="site-main" role="main">';
}