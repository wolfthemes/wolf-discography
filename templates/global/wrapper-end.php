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
	// Close modern block theme wrappers
	echo '</main>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
} else {
	// Close classic theme wrappers
	echo '</main>';
	echo '</div>';
}
