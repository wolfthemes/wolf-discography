<?php
/**
 * Page Setup Notices
 *
 * Manages all options functionality
 *
 * @package WolfDiscography
 * @subpackage Admin
 * @since 2.0.0
 */

namespace Wolf_Discography\Admin;

use Wolf_Discography\Core\Core;

defined( 'ABSPATH' ) || exit;

class Page_Setup_Notices {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'admin_notices', array( $this, 'check_page' ) );
		add_action( 'admin_notices', array( $this, 'create_page' ) );
	}

	/**
	 * Check discography page
	 *
	 * Display a notification if we can't get the discography page id
	 */
	public function check_page() {

		$output    = '';
		$theme_dir = get_template_directory();

		// update_option( '_wolf_discography_needs_page', true );
		// delete_option( '_wolf_discography_no_needs_page', true );
		// delete_option( '_wolf_discography_page_id' );

		if ( get_option( '_wolf_discography_no_needs_page' ) ) {
			return;
		}

		if ( ! get_option( '_wolf_discography_needs_page' ) ) {
			return;
		}

		if ( -1 == Core::get_discography_page_id() && ! isset( $_GET['wolf_discography_create_page'] ) ) {

			if ( isset( $_GET['skip_wolf_discography_setup'] ) ) {
				delete_option( '_wolf_discography_needs_page' );
				return;
			}

			update_option( '_wolf_discography_needs_page', true );

			$message = '<strong>Wolf Discography</strong> ' . sprintf(
				wp_kses(
					__( 'says : <em>Almost done! you need to <a href="%1$s">create a page</a> for your releases or <a href="%2$s">select an existing page</a> in the plugin settings</em>.', 'wolf-discography' ),
					array(
						'a'      => array(
							'href'  => array(),
							'class' => array(),
							'title' => array(),
						),
						'br'     => array(),
						'em'     => array(),
						'strong' => array(),
					)
				),
				esc_url( admin_url( '?wolf_discography_create_page=true' ) ),
				esc_url( admin_url( 'edit.php?post_type=release&page=wolf-discography-settings' ) )
			);

			$message .= sprintf(
				wp_kses(
					__(
						'<br><br>
					<a href="%1$s" class="button button-primary">Create a page</a>
					&nbsp;
					<a href="%2$s" class="button button-primary">Select an existing page</a>
					&nbsp;
					<a href="%3$s" class="button">Skip setup</a>',
						'wolf-discography'
					),
					array(
						'a'      => array(
							'href'  => array(),
							'class' => array(),
							'title' => array(),
						),
						'br'     => array(),
						'em'     => array(),
						'strong' => array(),
					)
				),
				esc_url( admin_url( '?wolf_discography_create_page=true' ) ),
				esc_url( admin_url( 'edit.php?post_type=release&page=wolf-discography-settings' ) ),
				esc_url( admin_url( '?skip_wolf_discography_setup=true' ) )
			);

			$output = '<div class="updated wolf-admin-notice wolf-plugin-admin-notice"><p>';

				$output .= $message;

			$output .= '</p></div>';

			echo $output;
		} else {

			delete_option( '_wolf_discography_need_page' );
		}

		return false;
	}

	/**
	 * Create discography page
	 */
	public function create_page() {

		if ( isset( $_GET['wolf_discography_create_page'] ) && $_GET['wolf_discography_create_page'] == 'true' ) {

			$output = '';

			// Create post object
			$post = array(
				'post_title'  => esc_html__( 'Discography', 'wolf-discography' ),
				'post_type'   => 'page',
				'post_status' => 'publish',
			);

			// Insert the post into the database
			$post_id = wp_insert_post( $post );

			if ( $post_id ) {

				update_option( '_wolf_discography_page_id', $post_id );
				update_post_meta( $post_id, '_wpb_status', 'off' ); // disable page builder mode for this page

				$message = esc_html__( 'Your discography page has been created succesfully', 'wolf-discography' );

				$output = '<div class="updated"><p>';

				$output .= $message;

				$output .= '</p></div>';

				echo $output;
			}
		}

		return false;
	}
}
