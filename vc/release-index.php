<?php
/**
 * Release Index
 *
 * @author WolfThemes
 * @category Core
 * @package WolfDiscography/WPBakery
 * @version 1.5.1
 * @since 1.6.0
 */

use Wolf_Discography\Config\Module_Params;
use Wolf_Discography\Page_Builders\WPBakery_Helper;

defined( 'ABSPATH' ) || exit;
defined( 'WPB_VC_VERSION' ) || exit;

vc_map( WPBakery_Helper::convert_params_to_vc( Module_Params::get_release_index_params() ) );
