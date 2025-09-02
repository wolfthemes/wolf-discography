<?php
/**
 * Discography Options.
 *
 * @class WD_Options
 * @author WolfThemes
 * @category Admin
 * @package WolfDiscography/Admin
 * @version 1.6.0
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
				'display_format' => 1,
				'layout' => 'list',
				'columns' => '3'
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

		// Layout options only for non-Wolf themes
		if ( ! WD()->is_wolf_theme() ) {
			add_settings_field( 'layout', esc_html__( 'Layout Style', 'wolf-discography' ), array( $this, 'setting_layout' ), 'wolf-release-settings', 'wolf-release-settings' );
			add_settings_field( 'columns', esc_html__( 'Grid Columns', 'wolf-discography' ), array( $this, 'setting_columns' ), 'wolf-release-settings', 'wolf-release-settings' );
		}

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

		// Validate layout and columns only if they exist (non-Wolf themes)
		if ( isset( $input['layout'] ) ) {
			$input['layout'] = in_array( $input['layout'], array( 'list', 'grid' ) ) ? $input['layout'] : 'list';
		}

		if ( isset( $input['columns'] ) ) {
			$input['columns'] = in_array( $input['columns'], array( '2', '3', '4' ) ) ? $input['columns'] : '3';
		}

		$input['use_band_tax'] = isset( $input['use_band_tax'] ) ? intval( $input['use_band_tax'] ) : 0;
		$input['use_label_tax'] = isset( $input['use_label_tax'] ) ? intval( $input['use_label_tax'] ) : 0;
		$input['use_genre_tax'] = isset( $input['use_genre_tax'] ) ? intval( $input['use_genre_tax'] ) : 0;
		$input['display_format'] = isset( $input['display_format'] ) ? intval( $input['display_format'] ) : 0;

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
	 * Layout settings
	 *
	 * @access public
	 * @return string
	 */
	public function setting_layout() {
		$current_layout = wolf_get_release_option( 'layout', 'list' );
		?>
		<select name="wolf_release_settings[layout]" id="wolf_discography_layout">
			<option value="list" <?php selected( $current_layout, 'list' ); ?>><?php esc_html_e( 'List View', 'wolf-discography' ); ?></option>
			<option value="grid" <?php selected( $current_layout, 'grid' ); ?>><?php esc_html_e( 'Grid View', 'wolf-discography' ); ?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Choose how to display your releases. List view shows releases horizontally with details, Grid view shows them as cards in columns.', 'wolf-discography' ); ?></p>

		<script type="text/javascript">
		jQuery(document).ready(function($) {
			function toggleColumnsField() {
				var layout = $('#wolf_discography_layout').val();
				var columnsRow = $('#wolf_discography_columns').closest('tr');
				if (layout === 'grid') {
					columnsRow.show();
				} else {
					columnsRow.hide();
				}
			}

			$('#wolf_discography_layout').on('change', toggleColumnsField);
			toggleColumnsField(); // Run on page load
		});
		</script>
		<?php
	}

	/**
	 * Columns settings
	 *
	 * @access public
	 * @return string
	 */
	public function setting_columns() {
		$current_columns = wolf_get_release_option( 'columns', '3' );
		?>
		<select name="wolf_release_settings[columns]" id="wolf_discography_columns">
			<option value="2" <?php selected( $current_columns, '2' ); ?>><?php esc_html_e( '2 Columns', 'wolf-discography' ); ?></option>
			<option value="3" <?php selected( $current_columns, '3' ); ?>><?php esc_html_e( '3 Columns', 'wolf-discography' ); ?></option>
			<option value="4" <?php selected( $current_columns, '4' ); ?>><?php esc_html_e( '4 Columns', 'wolf-discography' ); ?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Number of columns to display when using Grid view. Only applies to Grid layout.', 'wolf-discography' ); ?></p>
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
		<?php esc_html_e( 'Make artist names clickable links to show all releases by that artist', 'wolf-discography' ); ?>
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
		<?php esc_html_e( 'Make label names clickable links to show all releases by that label', 'wolf-discography' ); ?>
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
		<?php esc_html_e( 'Make genre names clickable links to show all releases in that genre', 'wolf-discography' ); ?>
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
		<?php esc_html_e( 'Display the release format (CD, Digital Download, Vinyl, etc.)', 'wolf-discography' ); ?>
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
			<p><code>[wolf_last_releases count="6" col="3" label="my-label" band="this-band"]</code></p>

			<?php if ( ! WD()->is_wolf_theme() ) : ?>
			<h3><?php esc_html_e( 'Layout Options', 'wolf-discography' ); ?></h3>
			<p><?php esc_html_e( 'You can override the default layout settings using shortcode parameters:', 'wolf-discography' ); ?></p>
			<p><code>[wolf_last_releases layout="list"]</code> - <?php esc_html_e( 'Force list layout', 'wolf-discography' ); ?></p>
			<p><code>[wolf_last_releases layout="grid" col="4"]</code> - <?php esc_html_e( 'Force grid layout with 4 columns', 'wolf-discography' ); ?></p>
			<?php endif; ?>
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

			<?php if ( WD()->is_wolf_theme() ) : ?>
				<div class="notice notice-info">
					<p><strong><?php esc_html_e( 'Wolf Theme Detected', 'wolf-discography' ); ?></strong></p>
					<p><?php esc_html_e( 'You are using a Wolf Theme. Layout options are managed by your theme and not shown here.', 'wolf-discography' ); ?></p>
				</div>
			<?php endif; ?>

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