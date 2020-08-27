<?php
/**
 * Discography Options.
 *
 * @class WD_Options
 * @author WolfThemes
 * @category Admin
 * @package WolfDiscography/Admin
 * @version 1.5.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * WD_Options class.
 */
class WD_Options {
	/**
	 * Constructor
	 */
	public function __construct() {

		// default options
		add_action( 'admin_init', array( $this, 'default_options' ) );
		
		// register settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// add option sub-menu
		add_action( 'admin_menu', array( $this, 'add_settings_menu' ) );
	}

	/**
	 * Add options menu
	 */
	public function add_settings_menu() {

		add_submenu_page( 'edit.php?post_type=release', esc_html__( 'Settings', 'wolf-discography' ), esc_html__( 'Settings', 'wolf-discography' ), 'edit_plugins', 'wolf-discography-settings', array( $this, 'options_form' ) );
		add_submenu_page( 'edit.php?post_type=release', esc_html__( 'Shortcode', 'wolf-discography' ), esc_html__( 'Shortcode', 'wolf-discography' ), 'edit_plugins', 'wolf-discography-shortcode', array( $this, 'help' ) );
	}

	/**
	 * Set default options
	 */
	public function default_options() {

		global $options;

		if ( false ===  get_option( 'wolf_release_settings' )  ) {

			$default = array(

				'use_band_tax' => 1,
				'use_label_tax' => 1,
				'display_format' => 1

			);

			add_option( 'wolf_release_settings', $default );
		}
	}

	/**
	 * Register options
	 */
	public function register_settings() {

		register_setting( 'wolf-release-settings', 'wolf_release_settings', array( $this, 'settings_validate' ) );
		add_settings_section( 'wolf-release-settings', '', array( $this, 'section_intro' ), 'wolf-release-settings' );
		add_settings_field( 'page_id', esc_html__( 'Discography Page', 'wolf-discography' ), array( $this, 'setting_page_id' ), 'wolf-release-settings', 'wolf-release-settings' );
		add_settings_field( 'use_band_tax', esc_html__( 'Link Artist Name', 'wolf-discography' ), array( $this, 'setting_use_band_tax' ), 'wolf-release-settings', 'wolf-release-settings' );
		add_settings_field( 'use_label_tax', esc_html__( 'Link Label Name', 'wolf-discography' ), array( $this, 'setting_use_label_tax' ), 'wolf-release-settings', 'wolf-release-settings', array( 'class' => 'wolf-discography-settings-link-label' ) );
		add_settings_field( 'use_genre_tax', esc_html__( 'Link Genre', 'wolf-discography' ), array( $this, 'setting_use_genre_tax' ), 'wolf-release-settings', 'wolf-release-settings', array( 'class' => 'wolf-discography-settings-link-genre' ) );
		add_settings_field( 'display_format', esc_html__( 'Display format (like CD, digital download etc...)', 'wolf-discography' ), array( $this, 'setting_display_format' ), 'wolf-release-settings', 'wolf-release-settings', array( 'class' => 'wolf-discography-settings-display-format' ) );
	}

	/**
	 * Validate options
	 *
	 * @param array $input
	 * @return array $input
	 */
	public function settings_validate( $input ) {

		if ( isset( $input['page_id'] ) ) {
			update_option( '_wolf_discography_page_id', intval( $input['page_id'] ) );
			unset( $input['page_id'] );
		}

		$input['use_band_tax'] = intval( $input['use_band_tax'] );
		$input['use_label_tax'] = intval( $input['use_label_tax'] );
		$input['display_format'] = intval( $input['display_format'] );
		return $input;
	}

	/**
	 * Debug section
	 *
	 * @return string
	 */
	public function section_intro() {
		// debug
		// global $options;
		//var_dump(get_option('_wolf_discography_page_id'));
	}

	/**
	 * Page settings
	 *
	 * @access public
	 * @return string
	 */
	public function setting_page_id() {
		$page_option = array( '' => esc_html__( '- Disabled -', 'wolf-discography' ) );
		$pages = get_pages();

		foreach ( $pages as $page ) {

			if ( get_post_field( 'post_parent', $page->ID ) ) {
				$page_option[ absint( $page->ID ) ] = '&nbsp;&nbsp;&nbsp; ' . sanitize_text_field( $page->post_title );
			} else {
				$page_option[ absint( $page->ID ) ] = sanitize_text_field( $page->post_title );
			}
		}
		?>
		<select name="wolf_release_settings[page_id]">
			<option value="-1"><?php esc_html_e( 'Select a page...', 'wolf-discography' ); ?></option>
			<?php foreach ( $page_option as $k => $v ) : ?>
				<option value="<?php echo absint( $k ); ?>" <?php selected( absint( $k ), get_option( '_wolf_discography_page_id' ) ); ?>><?php echo sanitize_text_field( $v ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Use Band Taxonomy option
	 *
	 * @return string
	 */
	public function setting_use_band_tax() {
		?>
		<input type="hidden" name="wolf_release_settings[use_band_tax]" value="0">
		<label><input type="checkbox" name="wolf_release_settings[use_band_tax]" value="1" <?php echo ( ( wolf_get_release_option( 'use_band_tax' ) == 1) ? ' checked="checked"' : '' ); ?>>
		</label>
		<?php
	}

	/**
	 * Use Label Taxonomy option
	 *
	 * @return string
	 */
	public function setting_use_label_tax() {
		?>
		<input type="hidden" name="wolf_release_settings[use_label_tax]" value="0">
		<label><input type="checkbox" name="wolf_release_settings[use_label_tax]" value="1" <?php echo ( ( wolf_get_release_option( 'use_label_tax' ) == 1) ? ' checked="checked"' : '' ); ?>>
		</label>
		<?php
	}

	/**
	 * Use Genre Taxonomy option
	 *
	 * @return string
	 */
	public function setting_use_genre_tax() {
		?>
		<input type="hidden" name="wolf_release_settings[use_genre_tax]" value="0">
		<label><input type="checkbox" name="wolf_release_settings[use_genre_tax]" value="1" <?php echo ( ( wolf_get_release_option( 'use_genre_tax' ) == 1) ? ' checked="checked"' : '' ); ?>>
		</label>
		<?php
	}

	/**
	 * Display release format option
	 *
	 * @return string
	 */
	public function setting_display_format() {
		?>
		<input type="hidden" name="wolf_release_settings[display_format]" value="0">
		<label><input type="checkbox" name="wolf_release_settings[display_format]" value="1" <?php echo ( ( wolf_get_release_option( 'display_format' ) == 1) ? ' checked="checked"' : '' ); ?>>
		</label>
		<?php
	}

	/**
	 * Displays Shortcode help
	 */
	public function help() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Discography Shortcode', 'wolf-discography' ) ?></h2>
			<p><?php esc_html_e( 'To display your last releases in your post or page you can use the following shortcode.', 'wolf-discography' ); ?></p>
			<p><code>[wolf_last_releases]</code></p>
			<p><?php esc_html_e( 'Additionally, you can add a count, column, and categories attributes.', 'wolf-discography' ); ?></p>
			<p><code>[wolf_last_releases count="6" col="2|3|4" label="my-label" band="this-band"]</code></p>
		</div>
		<?php
	}

	/**
	 * Options form
	 *
	 * @return string
	 */
	public function options_form() {
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2><?php esc_html_e( 'Discography Options', 'wolf-discography' ); ?></h2>
			<?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) { ?>
			<div id="setting-error-settings_updated" class="updated settings-error">
				<p><strong><?php esc_html_e( 'Settings saved.', 'wolf-discography' ); ?></strong></p>
			</div>
			<?php } ?>
			<form action="options.php" method="post">
				<?php settings_fields( 'wolf-release-settings' ); ?>
				<?php do_settings_sections( 'wolf-release-settings' ); ?>
				<p class="submit"><input name="save" type="submit" class="button-primary" value="<?php esc_html_e( 'Save Changes', 'wolf-discography' ); ?>" /></p>
			</form>
		</div>
		<?php
	}
}

return new WD_Options();