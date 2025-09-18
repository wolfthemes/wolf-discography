<?php
/**
 * Admin Notices
 *
 * @package WolfDiscography
 * @subpackage Core
 * @since 2.0.0
 */

namespace WolfDiscography\Admin;

use WolfDiscography\Core\Constants;

defined( 'ABSPATH' ) || exit;

class Admin_Notices {

	public function __construct() {
		add_action( 'admin_notices', array( $this, 'display_notices' ) );
	}

	public function display_notices(): void {
		$this->display_php_version_warning();
	}

	public function display_php_version_warning(): void {
		if ( version_compare( PHP_VERSION, Constants::REQUIRED_PHP_VERSION, '>=' ) ) {
			return;
		}

		?>
		<div class="notice notice-error">
			<p>
			<?php
			printf(
				esc_html__( '%1$s needs at least PHP %2$s installed. You have %3$s.', 'wolf-discography' ),
				'Discography',
				Constants::REQUIRED_PHP_VERSION,
				PHP_VERSION
			);
			?>
			</p>
		</div>
		<?php
	}
}