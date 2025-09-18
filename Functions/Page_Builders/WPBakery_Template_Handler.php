<?php
/**
 * WPBakery Template Handler
 *
 * @package WolfDiscography
 * @subpackage PageBuilders
 * @since 2.0.0
 */

namespace Wolf_Discography\Page_Builders;

use Wolf_Discography\Core\Constants;

defined( 'ABSPATH' ) || exit;
defined( 'WPB_VC_VERSION' ) || exit;

/**
 * Elementor Helper Class
 */
class WPBakery_Template_Handler {


	public function __construct() {
		add_action( 'vc_after_init', array( $this, 'hook_template_dir' ) );
	}

	/**
	 * Filtering template path for each shortcode
	 *
	 * Using vc_set_shortcodes_templates_dir will prevent the theme from having a VC template directory.
	 * We filter the template path for each shortcode so we can have our shortcode templates in the plugin AND in the theme
	 */
	public function hook_template_dir() {

		$vc_template_dir = WD_DIR . '/vc_templates';

		$slug = 'release-index';

		// Important: we use the wvc prefix for compatilbiilty with WolfThemes themes
		$slug = 'wvc_' . str_replace( '-', '_', basename( $slug ) );

		$vc_filename = $vc_template_dir . '/' . $slug . '.php';

		if ( is_file( $vc_filename ) ) {

			vc_map_update(
				$slug,
				array(
					'html_template' => $vc_filename,
				)
			);

		}
	}
}