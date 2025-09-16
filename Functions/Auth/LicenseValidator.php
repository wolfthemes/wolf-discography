<?php
/**
 * License Validator
 *
 * @package WolfDiscography
 * @subpackage Admin
 * @since 2.0.0
 */

namespace WolfDiscography\Auth;

class LicenseValidator {

    private const TRIAL_PERIOD = 10 * DAY_IN_SECONDS;
    private const API_ENDPOINT = 'https://api.wolfthemes.cloud/envato/';

    private $option_prefix = 'wd_';

    public function __construct() {
        add_action( 'admin_notices', array( $this, 'show_activation_notice' ) );
    }

    public function is_activated(): bool {
        if ( $this->validated_wolf_theme() ) {
            return true;
        }

        // new.
        if ( ! get_transient( $this->option_prefix . 'activation_notice' ) && ! get_option( $this->option_prefix . 'activation_notice_set' ) ) {
            set_transient( $this->option_prefix . 'activation_notice', true, self::TRIAL_PERIOD );
            update_option( $this->option_prefix . 'activation_notice_set', true );
        }
        return true;
    }

    public function validate_remote_license(): bool {
        // TODO: Implement remote validation
        return false;
    }

    public function validated_wolf_theme(): bool {
        // activated.
        if ( get_transient( 'wolf_core_activated' ) ) {
            return (bool) get_option( 'wolf_core_key' );
        }

        return false;
    }

    /**
     * Show 10 days activation notice
     */
    public function show_activation_notice() {
        if ( $this->is_activated() ) {
            return;
        }

        global $pagenow;

        $theme_slug = apply_filters( 'wolftheme_theme_slug', esc_attr( sanitize_title_with_dashes( get_template() ) ) );

        if ( isset( $_GET['page'] ) && $_GET['page'] === $theme_slug . '-about' ) {
            return;
        }

        if ( 'index.php' !== $pagenow ) {
            return;
        }

        if ( get_option( $this->option_prefix . 'activated' ) ) {
            return;
        }

        $plugin_name = 'Wolf Discography';
        $timeout = $this->get_transient_timeout( $this->option_prefix . 'activation_notice' );

        echo '<div class="notice notice-info">
            <p>' . sprintf(
            wp_kses_post( __( 'Hey there, thanks a lot for using our awesome <strong>%1$s</strong> plugin! To ensure that it will work for verified customers only, you just need to enter your <a href="%2$s" target="_blank" title="Find your purchase code">plugin purchase code</a> within the next <strong>%3$d days</strong>. You won\'t have to activate anything else after that.', 'wolf-discography' ) ),
            $plugin_name,
            'https://help.market.envato.com/hc/en-us/articles/202822600-Where-Can-I-Find-my-Purchase-Code-',
            $timeout
        ) . '</p>
                <p>
                <a class="button button-primary" href="' . esc_url( admin_url( 'themes.php?page=' . $theme_slug . '-about#license' ) ) . '">' . esc_html( 'Activate', 'wolf-discography' ) . '</a>

                <a class="button button-secondary" target="_blank" href="https://wolfthemes.ticksy.com/article/13268/">' . esc_html( 'More infos', 'wolf-discography' ) . '</a>
            </p>
        </div>';
    }

    /**
     * Get transient timeout
     *
     * @param string $transient The transient name.
     * @return int|false Days remaining or false if not found
     */
    private function get_transient_timeout( $transient ) {
        global $wpdb;
        $transient_timeout = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT option_value FROM {$wpdb->options} WHERE option_name LIKE %s",
                '%_transient_timeout_' . $transient . '%'
            )
        );
        return ( isset( $transient_timeout[0] ) ) ? absint( ( $transient_timeout[0] - time() ) / DAY_IN_SECONDS ) : false;
    }
}