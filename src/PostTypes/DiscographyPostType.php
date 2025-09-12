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
class DiscographyPostType {

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
		// During migration, we can either:
		// 1. Call the legacy file (safe approach)
		// 2. Implement the registration here (new approach)

		// Option 1: Legacy approach (safe during migration)
		$legacy_file = WD_DIR . '/inc/wd-register-post-type.php';
		if ( file_exists( $legacy_file ) ) {
			include_once $legacy_file;
			return;
		}

		// Option 2: New implementation (uncomment when ready to migrate)
		/*
		$labels = [
			'name'                  => _x('Releases', 'Post type general name', 'wolf-discography'),
			'singular_name'         => _x('Release', 'Post type singular name', 'wolf-discography'),
			'menu_name'            => _x('Discography', 'Admin Menu text', 'wolf-discography'),
			'name_admin_bar'       => _x('Release', 'Add New on Toolbar', 'wolf-discography'),
			'add_new'              => __('Add New', 'wolf-discography'),
			'add_new_item'         => __('Add New Release', 'wolf-discography'),
			'new_item'             => __('New Release', 'wolf-discography'),
			'edit_item'            => __('Edit Release', 'wolf-discography'),
			'view_item'            => __('View Release', 'wolf-discography'),
			'all_items'            => __('All Releases', 'wolf-discography'),
			'search_items'         => __('Search Releases', 'wolf-discography'),
			'parent_item_colon'    => __('Parent Releases:', 'wolf-discography'),
			'not_found'            => __('No releases found.', 'wolf-discography'),
			'not_found_in_trash'   => __('No releases found in Trash.', 'wolf-discography'),
			'featured_image'       => _x('Release Cover Image', 'Overrides the "Featured Image" phrase', 'wolf-discography'),
			'set_featured_image'   => _x('Set cover image', 'Overrides the "Set featured image" phrase', 'wolf-discography'),
			'remove_featured_image' => _x('Remove cover image', 'Overrides the "Remove featured image" phrase', 'wolf-discography'),
			'use_featured_image'   => _x('Use as cover image', 'Overrides the "Use as featured image" phrase', 'wolf-discography'),
			'archives'             => _x('Release archives', 'The post type archive label', 'wolf-discography'),
			'insert_into_item'     => _x('Insert into release', 'Overrides the "Insert into post" phrase', 'wolf-discography'),
			'uploaded_to_this_item' => _x('Uploaded to this release', 'Overrides the "Uploaded to this post" phrase', 'wolf-discography'),
			'filter_items_list'    => _x('Filter releases list', 'Screen reader text for the filter links', 'wolf-discography'),
			'items_list_navigation' => _x('Releases list navigation', 'Screen reader text for the pagination', 'wolf-discography'),
			'items_list'           => _x('Releases list', 'Screen reader text for the items list', 'wolf-discography'),
		];

		$args = [
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => ['slug' => $this->getPostTypeSlug()],
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 20,
			'menu_icon'          => 'dashicons-album',
			'show_in_rest'       => true, // Enable Gutenberg editor
			'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
		];

		register_post_type($this->post_type, $args);
		*/
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
