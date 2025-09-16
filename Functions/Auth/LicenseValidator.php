<?php
namespace WolfDiscography\Auth;

class LicenseValidator {
    private const TRIAL_PERIOD = 10 * DAY_IN_SECONDS;
    private const API_ENDPOINT = 'https://api.wolfthemes.cloud/envato/';

    private $option_prefix = 'wd_';

    public function is_activated(): bool {
        // Your existing logic here
		return true;
    }

    public function validate_remote_license(): bool {
        // Your remote validation logic
    }

	public function check_if_validated_wolf_theme() {

	if ( ! get_transient( 'wolf_core_activation_notice' ) && ! get_option( 'wolf_core_activation_notice_set' ) ) {
			set_transient( 'wolf_core_activation_notice', true, 31 * DAY_IN_SECONDS );
			update_option( 'wolf_core_activation_notice_set', true );
		}

		// activated.
		if ( ( get_option( 'wolf_core_activated' ) || get_transient( 'wolf_core_activated' ) ) && get_option( 'wolf_core_key' ) && get_option( 'wolf_core_code' ) ) {
			// die( 'is fully activated' );
			return get_option( 'wolf_core_key' );
		}

		// Trial expired.
		if ( ( ! get_option( 'wolf_core_activated' ) || ! get_transient( 'wolf_core_activated' ) ) && ! get_option( 'wolf_core_key' ) && ! get_option( 'wolf_core_code' ) && ! get_transient( 'wolf_core_activation_notice' ) && get_option( 'wolf_core_activation_notice_set' ) ) {
			// die( 'period expired' );
			return false;
		}

		// Trial running.
		if ( get_transient( 'wolf_core_activation_notice' ) && get_option( 'wolf_core_activation_notice_set' ) ) {
			// die( 'period current' );
			return true;
		}
	}

	/**
	 * Show 10 days activation notice
	 */
	function show_activation_notice() {

		global $pagenow;

		$theme_slug = apply_filters( 'wolftheme_theme_slug', esc_attr( sanitize_title_with_dashes( get_template() ) ) );

		if ( isset( $_GET['page'] ) && $_GET['page'] === $theme_slug . '-about' ) {
			return;
		}

		if ( 'index.php' !== $pagenow ) {
			return;
		}

		if ( get_option( $this->option_prefix . '_activated' ) ) {
			return;
		}

		$wp_theme   = wp_get_theme( get_template() );
		$theme_name = $wp_theme->Name;
		$timeout    = $this->get_transient_timeout( $this->option_prefix . '_activation_notice' );

		echo '<div class="notice notice-info">
			<p>' . sprintf(
			wp_kses_post( __( 'Hey there, thanks a lot for using our awesome <strong>%1$s</strong> plugin! To ensure that it will work for verified customers only, you just need to enter your <a href="%2$s" target="_blank" title="Find your purchase code">plugin purchase code</a> within the next <strong>%3$d days</strong>. You won\'t have to activate anything else after that.', 'wolf-core' ) ),
			$theme_name,
			'https://help.market.envato.com/hc/en-us/articles/202822600-Where-Can-I-Find-my-Purchase-Code-',
			$timeout
		) . '</p>
				<p>
				<a class="button button-primary" href="' . esc_url( admin_url( 'themes.php?page=' . $theme_slug . '-about#license' ) ) . '">' . esc_html( 'Activate', 'wolf-core' ) . '</a>

				<a class="button button-secondary" target="_blank" href="https://wolfthemes.ticksy.com/article/13268/">' . esc_html( 'More infos', 'wolf-core' ) . '</a>
			</p>
		</div>';
	}

	/**
	 * Get transient timeout
	 *
	 * @param string $transient The transient name.
	 * @return void
	 */
	private function get_transient_timeout( $transient ) {
		global $wpdb;
			$transient_timeout = $wpdb->get_col(
				"
			SELECT option_value
			FROM $wpdb->options
			WHERE option_name
			LIKE '%_transient_timeout_$transient%'
			"
			);
		return ( isset( $transient_timeout[0] ) ) ? absint( ( $transient_timeout[0] - time() ) / DAY_IN_SECONDS ) : false;
	}
}