<?php
/**
 * Admin Columns
 *
 * Manages all options functionality
 *
 * @package WolfDiscography
 * @subpackage Admin
 * @since 2.0.0
 */

namespace WolfDiscography\Admin;

use WolfDiscography\Core\Core;

defined( 'ABSPATH' ) || exit;

class Admin_Columns {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Hide editors from index page
		add_action( 'edit_form_after_title', array( $this, 'is_index_page' ) );
		add_action( 'admin_init', array( $this, 'hide_editor' ) );
		add_action( 'admin_head', array( $this, 'hide_wpb_editor' ) );

		// Add columns to post list
		add_filter( 'manage_release_posts_columns', array( $this, 'admin_columns_head_release_thumb' ), 10 );
		add_action( 'manage_release_posts_custom_column', array( $this, 'admin_columns_content_release_thumb' ), 10, 2 );
	}

	/**
	 * Display notice on album index page
	 */
	public function is_index_page() {

		if ( isset( $_GET['post'] ) && absint( $_GET['post'] ) == Core::get_discography_page_id() ) {
			$message = esc_html__( 'You are currently editing the page that shows the discography.', 'wolf-discography' );

			$output = '<div class="notice notice-warning inline"><p>';

			$output .= $message;

			$output .= '</p></div>';

			echo $output;
		}
	}

	/**
	 * Hide the editor if we're on the admin discography page
	 */
	public function hide_editor() {
		if ( isset( $_GET['post'] ) && absint( $_GET['post'] ) == Core::get_discography_page_id() ) {
			remove_post_type_support( 'page', 'editor' );
		}
	}

	/**
	 * Hide the editor if we're on the admin discography page
	 */
	public function hide_wpb_editor() {
		if ( isset( $_GET['post'] ) && absint( $_GET['post'] ) == Core::get_discography_page_id() ) {
			?>
			<style type="text/css">
			.wpb-toggle-editor,
			#wpb-import-export-buttons,
			#wpb_content{
				display:none!important;
			}
			</style>
			<?php
		}
	}

	/**
	 * Add thumbnail column head in admin posts list
	 *
	 * @param array $columns
	 * @return array $columns
	 */
	public function admin_columns_head_release_thumb( $columns ) {

		$columns['release_thumbnail'] = esc_html__( 'Thumbnail', 'wolf-discography' );
		return $columns;
	}

	/**
	 * Add thumbnail column in admin posts list
	 *
	 * @param string $column_name
	 * @param int    $post_id
	 */
	public function admin_columns_content_release_thumb( $column_name, $post_id ) {

		$thumbnail = get_the_post_thumbnail();

		if ( 'release_thumbnail' == $column_name ) {

			if ( $thumbnail ) {
				echo '<a href="' . get_edit_post_link() . '" title="' . esc_attr( sprintf( esc_html__( 'Edit "%s"', 'wolf-discography' ), get_the_title() ) ) . '">' . get_the_post_thumbnail( '', array( 60, 60 ), array( 'style' => 'max-width:60px;height:auto;' ) ) . '</a>';
			}
		}
	}
}