<?php
/**
 * Metabox Manager
 *
 * Handles all metabox functionality for discography posts
 *
 * @package WolfDiscography
 * @subpackage Admin
 * @since 2.0.0
 */

namespace WolfDiscography\Admin;

use WolfDiscography\Config\MetaboxConfig;

defined( 'ABSPATH' ) || exit;

/**
 * Metabox Manager Class
 *
 * Manages registration, rendering, and saving of metaboxes
 */
class MetaboxManager {

	/**
	 * Metabox configurations
	 *
	 * @var array
	 */
	private array $metaboxes = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->load_configuration();
		$this->init_hooks();
	}

	/**
	 * Load metabox configuration from Config class
	 */
	private function load_configuration(): void {
		$this->metaboxes = MetaboxConfig::get_config();

		// Allow filtering of metaboxes
		$this->metaboxes = apply_filters( 'wolf_discography_metaboxes_config', $this->metaboxes );
	}

	/**
	 * Initialize WordPress hooks
	 */
	private function init_hooks(): void {
		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );
		add_action( 'save_post', array( $this, 'save_enqueue_as' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Add metaboxes to WordPress
	 */
	public function add_metaboxes(): void {
		foreach ( $this->metaboxes as $id => $metabox ) {
			add_meta_box(
				'wolf_' . $id,
				$metabox['title'],
				array( $this, 'renderMetabox' ),
				$metabox['screen'],
				$metabox['context'],
				$metabox['priority'],
				array( 'metabox_id' => $id ) // Pass metabox ID as argument
			);
		}
	}

	/**
	 * Generic metabox renderer
	 */
	public function renderMetabox( \WP_Post $post, array $args ): void {
		$metabox_id = $args['args']['metabox_id'];
		$fields     = $this->metaboxes[ $metabox_id ]['fields'];

		// Add nonce for security
		wp_nonce_field( 'wolf_discography_metabox', 'wolf_discography_metabox_nonce' );

		echo '<table class="form-table wolf-discography-metabox-table">';

		foreach ( $fields as $field ) {
			$this->renderField( $post, $field );
		}

		echo '</table>';
	}

	/**
	 * Render individual field
	 */
	private function renderField( \WP_Post $post, array $field ): void {
		$field_id = $field['id'];
		$value    = get_post_meta( $post->ID, $field_id, true );

		if ( ! $value && isset( $field['default'] ) ) {
			$value = $field['default'];
		}

		echo '<tr>';
		echo '<th style="width:15%"><label for="' . esc_attr( $field_id ) . '">' . esc_html( $field['label'] ) . '</label></th>';
		echo '<td>';

		switch ( $field['type'] ) {
			case 'text':
				$this->renderTextField( $field_id, $value );
				break;

			case 'url':
				$this->renderUrlField( $field_id, $value );
				break;

			case 'datepicker':
				$this->renderDatepickerField( $field_id, $value );
				break;

			case 'select':
				$this->renderSelectField( $field_id, $value, $field['choices'] );
				break;

			case 'repeatable':
				$this->renderRepeatableField( $field_id, $value );
				break;

			default:
				$this->renderTextField( $field_id, $value );
		}

		if ( ! empty( $field['desc'] ) ) {
			echo '<br><span class="description">' . esc_html( $field['desc'] ) . '</span>';
		}

		echo '</td></tr>';
	}

	/**
	 * Render text field
	 */
	private function renderTextField( string $field_id, $value ): void {
		echo '<input type="text" name="' . esc_attr( $field_id ) . '" id="' . esc_attr( $field_id ) . '" value="' . esc_attr( $value ) . '" class="regular-text" />';
	}

	/**
	 * Render URL field
	 */
	private function renderUrlField( string $field_id, $value ): void {
		echo '<input type="url" name="' . esc_attr( $field_id ) . '" id="' . esc_attr( $field_id ) . '" value="' . esc_url( $value ) . '" class="regular-text" />';
	}

	/**
	 * Render datepicker field
	 */
	private function renderDatepickerField( string $field_id, $value ): void {
		echo '<input type="text" class="wd-metabox-datepicker" name="' . esc_attr( $field_id ) . '" id="' . esc_attr( $field_id ) . '" value="' . esc_attr( $value ) . '" />';
	}

	/**
	 * Render select field
	 */
	private function renderSelectField( string $field_id, $value, array $choices ): void {
		echo '<select name="' . esc_attr( $field_id ) . '" id="' . esc_attr( $field_id ) . '">';

		foreach ( $choices as $key => $choice ) {
			// Handle both associative and indexed arrays
			$option_value = is_string( $key ) ? $key : $choice;
			$option_label = $choice;

			echo '<option value="' . esc_attr( $option_value ) . '"' . selected( $value, $option_value, false ) . '>' . esc_html( $option_label ) . '</option>';
		}

		echo '</select>';
	}

	/**
	 * Render repeatable field
	 */
	private function renderRepeatableField( string $field_id, $value ): void {
		$values = is_array( $value ) ? $value : array();

		echo '<div class="wd-repeatable-wrapper">';
		echo '<a class="wd-repeatable-add button" href="#" data-field="' . esc_attr( $field_id ) . '">+</a>';
		echo '<ul id="' . esc_attr( $field_id ) . '-repeatable" class="wd-custom-repeatable">';

		if ( ! empty( $values ) ) {
			foreach ( $values as $i => $item_value ) {
				echo '<li>';
				echo '<span class="sort hndle">|||</span>';
				echo '<input type="text" name="' . esc_attr( $field_id ) . '[' . $i . ']" value="' . esc_attr( $item_value ) . '" class="regular-text" />';
				echo '<a class="wd-repeatable-remove button" href="#">-</a>';
				echo '</li>';
			}
		} else {
			// Empty field for new entries
			echo '<li>';
			echo '<span class="sort hndle">|||</span>';
			echo '<input type="text" name="' . esc_attr( $field_id ) . '[0]" value="" class="regular-text" />';
			echo '<a class="wd-repeatable-remove button" href="#">-</a>';
			echo '</li>';
		}

		echo '</ul>';
		echo '</div>';
	}

	/**
	 * Save metabox data
	 */
	public function save_enqueue_as( int $post_id, \WP_Post $post ): void {
		// Verify nonce
		if ( ! isset( $_POST['wolf_discography_metabox_nonce'] ) ||
			! wp_verify_nonce( $_POST['wolf_discography_metabox_nonce'], 'wolf_discography_metabox' ) ) {
			return;
		}

		// Check permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Skip autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Only process our post type
		if ( $post->post_type !== 'release' ) {
			return;
		}

		// Save all fields
		foreach ( $this->metaboxes as $metabox ) {
			foreach ( $metabox['fields'] as $field ) {
				$this->saveField( $post_id, $field );
			}
		}
	}

	/**
	 * Save individual field
	 */
	private function saveField( int $post_id, array $field ): void {
		$field_id = $field['id'];

		if ( ! isset( $_POST[ $field_id ] ) ) {
			return;
		}

		$value = $_POST[ $field_id ];

		// Use MetaboxConfig to determine field type handling
		if ( in_array( $field_id, MetaboxConfig::getUrlFields() ) ) {
			$value = esc_url_raw( $value );
		} elseif ( in_array( $field_id, MetaboxConfig::getRepeatableFields() ) ) {
			$value = is_array( $value ) ? array_filter( $value ) : array();
		} else {
			$value = sanitize_text_field( $value );
		}

		if ( ! empty( $value ) ) {
			update_post_meta( $post_id, $field_id, $value );
		} else {
			delete_post_meta( $post_id, $field_id );
		}
	}

	/**
	 * Enqueue metabox assets
	 */
	public function enqueue_assets( string $hook ): void {
		global $post;

		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ) ) ||
			! $post ||
			$post->post_type !== 'release' ) {
			return;
		}

		// Enqueue datepicker
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-ui-custom', WD_CSS . '/admin/jquery-ui-custom.min.css', array(), WD_VERSION );

		// Enqueue repeatable fields script
		wp_enqueue_script(
			'wolf-discography-metabox',
			WD_JS . '/admin/metabox.js',
			array( 'jquery', 'jquery-ui-sortable' ),
			WD_VERSION,
			true
		);

		wp_enqueue_style(
			'wolf-discography-metabox',
			WD_CSS . '/admin/metabox.css',
			array(),
			WD_VERSION
		);
	}
}
