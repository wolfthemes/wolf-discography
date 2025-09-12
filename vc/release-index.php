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

use WolfDiscography\Config\ModuleParams;

defined( 'ABSPATH' ) || exit;
defined( 'WPB_VC_VERSION' ) || exit;

vc_map( wd_convert_params_to_vc( ModuleParams::getReleaseIndexParams() ) );