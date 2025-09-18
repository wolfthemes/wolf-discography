<?php
/**
 * Constants Manager
 *
 * @package WolfDiscography
 * @subpackage Core
 * @since 2.0.0
 */

namespace Wolf_Discography\Core;

defined( 'ABSPATH' ) || exit;

class Constants {

	public const VERSION              = '2.0.0';
	public const REQUIRED_PHP_VERSION = '7.4.0';
	public const TEXT_DOMAIN          = 'wolf-discography';
	public const CPT_SLUG             = 'release';

	public static function define( string $plugin_path, string $plugin_url ): void {
		$constants = array(
			'WD_DEV'         => false,
			'WD_DIR'         => $plugin_path,
			'WD_URI'         => $plugin_url,
			'WD_CSS'         => $plugin_url . '/assets/css',
			'WD_JS'          => $plugin_url . '/assets/js',
			'WD_SLUG'        => plugin_basename( $plugin_path ),
			'WD_PATH'        => plugin_basename( $plugin_path . '/wolf-discography.php' ),
			'WD_VERSION'     => self::VERSION,
			'WD_SUPPORT_URL' => 'https://wlfthm.es/help',
			'WD_DOC_URI'     => 'https://docs.wolfthemes.com/documentation/plugins/' . plugin_basename( $plugin_path ),
			'WD_WOLF_DOMAIN' => 'wolfthemes.com',
		);

		foreach ( $constants as $name => $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}
	}
}
