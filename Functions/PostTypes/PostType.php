<?php
/**
 * Discography Post Type
 *
 * Handles registration of the discography post type
 *
 * @package WolfDiscography
 * @subpackage PostTypes
 * @since 2.0.0
 */

namespace WolfDiscography\PostTypes;

defined( 'ABSPATH' ) || exit;

/**
 * Discography Post Type Class
 *
 * Manages the registration and configuration of the release post type
 */
class PostType {

	/**
	 * Post type slug
	 *
	 * @var string
	 */
	private string $post_type = 'release';

	/**
	 * Constructor
	 */
	public function __construct() {
		// Constructor is kept light - actual registration happens in register()
	}

	/**
	 * Register the post type
	 */
	public function register(): void {
		add_action( 'init', array( $this, 'registerPostType' ) );
	}

	/**
	 * Register the discography post type
	 */
	public function registerPostType(): void {

		$admin_skin = get_user_option( 'admin_color' );

		if ( $admin_skin == 'light' ) {

			$icon_url = WD_URI . '/assets/img/admin/vynil-dark.png';
		} else {

			$icon_url = WD_URI . '/assets/img/admin/vynil.png';
		}

		$labels = array(
			'name'               => esc_html__( 'Releases', 'wolf-discography' ),
			'singular_name'      => esc_html__( 'Release', 'wolf-discography' ),
			'add_new'            => esc_html__( 'Add New', 'wolf-discography' ),
			'add_new_item'       => esc_html__( 'Add New Release', 'wolf-discography' ),
			'all_items'          => esc_html__( 'All Releases', 'wolf-discography' ),
			'edit_item'          => esc_html__( 'Edit Release', 'wolf-discography' ),
			'new_item'           => esc_html__( 'New Release', 'wolf-discography' ),
			'view_item'          => esc_html__( 'View Release', 'wolf-discography' ),
			'search_items'       => esc_html__( 'Search Releases', 'wolf-discography' ),
			'not_found'          => esc_html__( 'No releases found', 'wolf-discography' ),
			'not_found_in_trash' => esc_html__( 'No releases found in Trash', 'wolf-discography' ),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html__( 'Releases', 'wolf-discography' ),
		);

		$args = array(

			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => false,
			'rewrite'             => array( 'slug' => 'release' ),
			'capability_type'     => 'post',
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => 5,
			'taxonomies'          => array(),
			'supports'            => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'comments' ),
			'exclude_from_search' => false,
			'menu_icon'           => $icon_url,
		);

		register_post_type( $this->post_type, $args );
	}

	/**
	 * Get the post type slug
	 *
	 * @return string
	 */
	public function getPostTypeSlug(): string {
		return apply_filters( 'wolf_discography_post_type_slug', $this->post_type );
	}

	/**
	 * Get post type labels
	 *
	 * @return array
	 */
	public function getLabels(): array {
		return apply_filters(
			'wolf_discography_post_type_labels',
			array(
				'name'          => _x( 'Releases', 'Post type general name', 'wolf-discography' ),
				'singular_name' => _x( 'Release', 'Post type singular name', 'wolf-discography' ),
				'menu_name'     => _x( 'Discography', 'Admin Menu text', 'wolf-discography' ),
			// Add more labels as needed
			)
		);
	}

	/**
	 * Get post type arguments
	 *
	 * @return array
	 */
	public function getArgs(): array {
		return apply_filters(
			'wolf_discography_post_type_args',
			array(
				'labels'             => $this->getLabels(),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => $this->getPostTypeSlug() ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => 20,
				'menu_icon'          => 'dashicons-album',
				'show_in_rest'       => true,
				'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
			)
		);
	}
}
