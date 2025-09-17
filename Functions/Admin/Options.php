<?php
/**
 * Wolf Discography Options
 *
 * Plugin-specific options implementation using the generic OptionsPanel class
 *
 * @package WolfDiscography
 * @subpackage Admin
 * @since 2.0.0
 */

namespace WolfDiscography\Admin;

use WolfDiscography\Core\Core;
use WolfDiscography\Core\Utilities;
use WolfDiscography\Auth\LicenseValidator;

defined( 'ABSPATH' ) || exit;

/**
 * Options class for Wolf Discography
 */
class Options {

	/**
	 * Options panel instance
	 */
	private $options_panel;

	/**
	 * License validator instance
	 */
	private $license_validator;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->license_validator = new LicenseValidator();

		add_action( 'init', array( $this, 'init_options_panel' ) );
		add_action( 'wolf_options_panel_before_form_wolf-discography-settings', array( $this, 'render_license_status' ) );
		add_action( 'wolf_options_panel_render_license_field', array( $this, 'render_custom_license_field' ), 10, 2 );

		// Add shortcode help page
		add_action( 'admin_menu', array( $this, 'add_shortcode_help_menu' ) );
	}

	/**
	 * Initialize the options panel
	 */
	public function init_options_panel() {

		$panel_args = array(
			'title'           => esc_html__( 'Settings', 'wolf-discography' ),
			'option_name'     => 'wolf_discography_options',
			'slug'            => 'wolf-discography-settings',
			'user_capability' => 'manage_options',
			'parent_slug'     => 'edit.php?post_type=release',
			'tabs'            => array(
				'general' => esc_html__( 'General', 'wolf-discography' ),
				'display' => esc_html__( 'Display', 'wolf-discography' ),
				'license' => esc_html__( 'License', 'wolf-discography' ),
			),
		);

		$panel_settings = $this->get_panel_settings();

		$this->options_panel = new OptionsPanel( $panel_args, $panel_settings );
	}

	/**
	 * Add shortcode help menu
	 */
	public function add_shortcode_help_menu() {
		add_submenu_page(
			'edit.php?post_type=release',
			esc_html__( 'Shortcode Help', 'wolf-discography' ),
			esc_html__( 'Shortcode Help', 'wolf-discography' ),
			'edit_plugins',
			'wolf-discography-shortcode-help',
			array( $this, 'render_shortcode_help' )
		);
	}

	/**
	 * Get panel settings configuration
	 *
	 * @return array Settings configuration
	 */
	protected function get_panel_settings() {
		$settings = array(
			// General Tab
			'discography_page' => array(
				'label'       => esc_html__( 'Discography Page', 'wolf-discography' ),
				'type'        => 'page_select',
				'tab'         => 'general',
				'description' => esc_html__( 'Select the page that will display your discography.', 'wolf-discography' ),
				/* Force to display legacy option for better user experience */
				'default'     => self::get_option( 'discography_page' ),
			),
			'use_band_tax'     => array(
				'label'       => esc_html__( 'Link Artist Names', 'wolf-discography' ),
				'type'        => 'checkbox',
				'tab'         => 'general',
				'description' => esc_html__( 'Make artist names clickable links to show all releases by that artist', 'wolf-discography' ),
				'default'     => 1,
			),
			'use_label_tax'    => array(
				'label'       => esc_html__( 'Link Label Names', 'wolf-discography' ),
				'type'        => 'checkbox',
				'tab'         => 'general',
				'description' => esc_html__( 'Make label names clickable links to show all releases by that label', 'wolf-discography' ),
				'default'     => 1,
			),
			'use_genre_tax'    => array(
				'label'       => esc_html__( 'Link Genre Names', 'wolf-discography' ),
				'type'        => 'checkbox',
				'tab'         => 'general',
				'description' => esc_html__( 'Make genre names clickable links to show all releases in that genre', 'wolf-discography' ),
				'default'     => 1,
			),
			'display_format'   => array(
				'label'       => esc_html__( 'Display Release Format', 'wolf-discography' ),
				'type'        => 'checkbox',
				'tab'         => 'general',
				'description' => esc_html__( 'Display the release format (CD, Digital Download, Vinyl, etc.)', 'wolf-discography' ),
				'default'     => 1,
			),
		);

		// Add display options only for non-Wolf themes
		if ( WD()->theme_supports_v2() ) {
			$settings['display_style'] = array(
				'label'       => esc_html__( 'Display Style', 'wolf-discography' ),
				'type'        => 'select',
				'tab'         => 'display',
				'description' => esc_html__( 'Choose how to display your releases.', 'wolf-discography' ),
				'choices'     => array(
					'list' => esc_html__( 'List View', 'wolf-discography' ),
					'grid' => esc_html__( 'Grid View', 'wolf-discography' ),
				),
				'default'     => 'list',
			);

			$settings['grid_columns'] = array(
				'label'       => esc_html__( 'Grid Columns', 'wolf-discography' ),
				'type'        => 'select',
				'tab'         => 'display',
				'description' => esc_html__( 'Number of columns when using Grid view.', 'wolf-discography' ),
				'choices'     => array(
					'2' => esc_html__( '2 Columns', 'wolf-discography' ),
					'3' => esc_html__( '3 Columns', 'wolf-discography' ),
					'4' => esc_html__( '4 Columns', 'wolf-discography' ),
				),
				'default'     => '3',
				'depends_on'  => [
                    'field' => 'display_style',
                    'value' => 'grid',
                ],
			);
		}

		// License Tab
		$settings['license_key'] = array(
			'label'             => esc_html__( 'License Key', 'wolf-discography' ),
			'type'              => 'license',
			'tab'               => 'license',
			'description'       => sprintf(
				esc_html__( 'Enter your Envato purchase code. %1$sFind your purchase code%2$s', 'wolf-discography' ),
				'<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Can-I-Find-my-Purchase-Code-" target="_blank">',
				'</a>'
			),
			'placeholder'       => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx',
			'sanitize_callback' => array( $this, 'sanitize_license_field' ),
			'default'           => '',
		);

		return $settings;
	}

	/**
	 * Custom license field sanitization
	 *
	 * @param string $value License key value
	 * @param array  $field_args Field arguments
	 * @return string Sanitized license key
	 */
	public function sanitize_license_field( $value, $field_args = array() ) {
		$value = sanitize_text_field( $value );

		// If license key changed, clear activation status
		$current_license = self::get_option( 'license_key' );
		if ( $current_license !== $value ) {
			// Clear activation status when license changes
			delete_option( 'wd_activated' );
			delete_transient( 'wd_activated' );
		}

		return $value;
	}

	/**
	 * Custom license field renderer
	 *
	 * @param array        $args Field arguments
	 * @param OptionsPanel $panel Panel instance
	 */
	public function render_custom_license_field( $args, $panel ) {
		$option_name = $args['label_for'];
		$value       = $panel->get_option_value( $option_name );
		$description = $panel->settings[ $option_name ]['description'] ?? '';
		$placeholder = $panel->settings[ $option_name ]['placeholder'] ?? '';

		// Check license status
		$is_active = $this->license_validator->is_activated();
		?>
		<input
			type="text"
			id="<?php echo esc_attr( $option_name ); ?>"
			name="<?php echo esc_attr( $panel->option_name ); ?>[<?php echo esc_attr( $option_name ); ?>]"
			value="<?php echo esc_attr( $value ); ?>"
			placeholder="<?php echo esc_attr( $placeholder ); ?>"
			class="regular-text"
			style="margin-right: 10px;">

		<?php if ( $is_active ) { ?>
			<span class="dashicons dashicons-yes-alt" style="color: #46b450; font-size: 20px; vertical-align: middle;"></span>
			<span style="color: #46b450; font-weight: 600;"><?php esc_html_e( 'Active', 'wolf-discography' ); ?></span>
		<?php } else { ?>
			<span class="dashicons dashicons-warning" style="color: #ffb900; font-size: 20px; vertical-align: middle;"></span>
			<span style="color: #ffb900; font-weight: 600;"><?php esc_html_e( 'Not Activated', 'wolf-discography' ); ?></span>
		<?php } ?>

		<?php if ( $description ) { ?>
			<p class="description"><?php echo wp_kses_post( $description ); ?></p>
		<?php } ?>

		<?php if ( ! empty( $value ) && ! $is_active ) { ?>
			<p class="description" style="color: #d63638;">
				<?php esc_html_e( 'License key entered but not validated. Please check your purchase code.', 'wolf-discography' ); ?>
			</p>
		<?php } ?>
		<?php
	}

	/**
	 * Render license status notice
	 */
	public function render_license_status() {
		if ( $this->license_validator->is_activated() ) {
			echo '<div class="notice notice-success inline"><p>';
			echo '<span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span> ';
			echo '<strong>' . esc_html__( 'License Active', 'wolf-discography' ) . '</strong> - ';
			echo esc_html__( 'All features are unlocked and working properly.', 'wolf-discography' );
			echo '</p></div>';
		} else {
			$timeout = $this->license_validator->get_trial_days_remaining();
			if ( $timeout > 0 ) {
				echo '<div class="notice notice-info inline"><p>';
				echo '<span class="dashicons dashicons-info" style="color: #0073aa;"></span> ';
				echo '<strong>' . esc_html__( 'Trial Mode', 'wolf-discography' ) . '</strong> - ';
				printf(
					esc_html__( 'You have %d days remaining in your trial period.', 'wolf-discography' ),
					$timeout
				);
				echo '</p></div>';
			} else {
				echo '<div class="notice notice-warning inline"><p>';
				echo '<span class="dashicons dashicons-warning" style="color: #ffb900;"></span> ';
				echo '<strong>' . esc_html__( 'Trial Expired', 'wolf-discography' ) . '</strong> - ';
				echo esc_html__( 'Please enter a valid license key to continue using all features.', 'wolf-discography' );
				echo '</p></div>';
			}
		}
	}

	/**
	 * Render shortcode help page
	 */
	public function render_shortcode_help() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Shortcodes', 'wolf-discography' ); ?></h1>

			<div class="card">
				<h2><?php esc_html_e( 'Basic Usage', 'wolf-discography' ); ?></h2>
				<p><?php esc_html_e( 'To display your releases in posts or pages, use the following shortcode:', 'wolf-discography' ); ?></p>
				<p><code>[wolf_last_releases]</code></p>
			</div>

			<div class="card">
				<h2><?php esc_html_e( 'Shortcode Attributes', 'wolf-discography' ); ?></h2>
				<p><?php esc_html_e( 'You can customize the output using these attributes:', 'wolf-discography' ); ?></p>

				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Attribute', 'wolf-discography' ); ?></th>
							<th><?php esc_html_e( 'Description', 'wolf-discography' ); ?></th>
							<th><?php esc_html_e( 'Default', 'wolf-discography' ); ?></th>
							<th><?php esc_html_e( 'Example', 'wolf-discography' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><code>count</code></td>
							<td><?php esc_html_e( 'Number of releases to display', 'wolf-discography' ); ?></td>
							<td>-1 (all)</td>
							<td><code>count="6"</code></td>
						</tr>
						<tr>
							<td><code>col</code></td>
							<td><?php esc_html_e( 'Number of columns (grid view)', 'wolf-discography' ); ?></td>
							<td>3</td>
							<td><code>col="4"</code></td>
						</tr>
						<tr>
							<td><code>display</code></td>
							<td><?php esc_html_e( 'Layout style (list or grid)', 'wolf-discography' ); ?></td>
							<td>list</td>
							<td><code>display="grid"</code></td>
						</tr>
						<tr>
							<td><code>band</code></td>
							<td><?php esc_html_e( 'Filter by artist/band slug', 'wolf-discography' ); ?></td>
							<td>-</td>
							<td><code>band="my-band"</code></td>
						</tr>
						<tr>
							<td><code>label</code></td>
							<td><?php esc_html_e( 'Filter by label slug', 'wolf-discography' ); ?></td>
							<td>-</td>
							<td><code>label="my-label"</code></td>
						</tr>
						<tr>
							<td><code>genre</code></td>
							<td><?php esc_html_e( 'Filter by genre slug', 'wolf-discography' ); ?></td>
							<td>-</td>
							<td><code>genre="rock"</code></td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="card">
				<h2><?php esc_html_e( 'Examples', 'wolf-discography' ); ?></h2>

				<h3><?php esc_html_e( 'Display 6 Latest Releases in Grid', 'wolf-discography' ); ?></h3>
				<p><code>[wolf_last_releases count="6" display="grid" col="3"]</code></p>

				<h3><?php esc_html_e( 'Show All Releases by Specific Artist', 'wolf-discography' ); ?></h3>
				<p><code>[wolf_last_releases band="artist-slug" display="list"]</code></p>

				<h3><?php esc_html_e( 'Display Label Releases in 4-Column Grid', 'wolf-discography' ); ?></h3>
				<p><code>[wolf_last_releases label="label-slug" display="grid" col="4"]</code></p>

				<h3><?php esc_html_e( 'Rock Genre Releases', 'wolf-discography' ); ?></h3>
				<p><code>[wolf_last_releases genre="rock" count="8"]</code></p>

				<h3><?php esc_html_e( 'Combined Filters', 'wolf-discography' ); ?></h3>
				<p><code>[wolf_last_releases band="my-band" genre="rock" count="4" display="grid" col="2"]</code></p>
			</div>

			<?php if ( WD()->theme_supports_v2() ) : ?>
			<div class="card">
				<h2><?php esc_html_e( 'Layout Override', 'wolf-discography' ); ?></h2>
				<p><?php esc_html_e( 'Since you\'re not using a Wolf Theme, you can override the default layout settings:', 'wolf-discography' ); ?></p>
				<ul>
					<li><code>display="list"</code> - <?php esc_html_e( 'Force list layout regardless of settings', 'wolf-discography' ); ?></li>
					<li><code>display="grid"</code> - <?php esc_html_e( 'Force grid layout regardless of settings', 'wolf-discography' ); ?></li>
					<li><code>col="2|3|4"</code> - <?php esc_html_e( 'Override column settings for grid view', 'wolf-discography' ); ?></li>
				</ul>
			</div>
			<?php endif; ?>

			<div class="card">
				<h2><?php esc_html_e( 'Pro Tips', 'wolf-discography' ); ?></h2>
				<ul>
					<li><?php esc_html_e( 'Use the artist, label, and genre slugs (not display names) in shortcode attributes.', 'wolf-discography' ); ?></li>
					<li><?php esc_html_e( 'You can find slugs in the respective taxonomy pages in your admin area.', 'wolf-discography' ); ?></li>
					<li><?php esc_html_e( 'Grid view works best with even numbers of releases.', 'wolf-discography' ); ?></li>
					<li><?php esc_html_e( 'Use count="-1" to display all releases (be careful with large discographies).', 'wolf-discography' ); ?></li>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Get an option value
	 *
	 * @param string $key Option key
	 * @param mixed  $default Default value
	 * @return mixed Option value
	 */
	public static function get_option( $key, $default = '' ) {
		$options = get_option( 'wolf_discography_options', array() );

		// Handle backward compatibility for specific keys
		if ( 'discography_page' === $key && ! isset( $options[ $key ] ) ) {
			// Check old option format
			$legacy_page_id = get_option( '_wolf_discography_page_id' );
			if ( $legacy_page_id && $legacy_page_id != -1 ) {
				return $legacy_page_id; // This will populate the field
			}
		}

		return $options[ $key ] ?? $default;
	}
}