<?php
/**
 * Release index WPBakery Page Builder Template
 *
 * The arguments are passed to the wd_posts hook so we can do whatever we want with it
 *
 * @author WolfThemes
 * @category Core
 * @package WolfDiscography/WPBakery
 * @version 1.5.1
 * @since 1.6.0
 */

defined( 'ABSPATH' ) || exit;

/* retrieve shortcode attributes */
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );

/* hook passing VC arguments */
do_action( 'wolf_discography_posts', $atts );