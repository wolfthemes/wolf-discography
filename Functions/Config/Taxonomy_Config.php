<?php
/**
 * Taxonomy Configuration
 *
 * Defines all taxonomy configurations for the plugin
 *
 * @package WolfDiscography
 * @subpackage Config
 * @since 2.0.0
 */

namespace WolfDiscography\Config;

defined( 'ABSPATH' ) || exit;

/**
 * Taxonomy Configuration Class
 */
class Taxonomy_Config {

	/**
	 * Get all taxonomy configurations
	 *
	 * @return array
	 */
	public static function get_config(): array {
		return array(
			'band'          => self::get_band_config(),
			'label'         => self::get_label_config(),
			'release_genre' => self::get_genre_config(),
		);
	}

	/**
	 * Get band taxonomy configuration
	 *
	 * @return array
	 */
	private static function get_band_config(): array {
		return array(
			'labels'                => array(
				'name'                       => esc_html__( 'Artists', 'wolf-discography' ),
				'singular_name'              => esc_html__( 'Artist', 'wolf-discography' ),
				'search_items'               => esc_html__( 'Search Artists', 'wolf-discography' ),
				'popular_items'              => esc_html__( 'Popular Artists', 'wolf-discography' ),
				'all_items'                  => esc_html__( 'All Artists', 'wolf-discography' ),
				'parent_item'                => esc_html__( 'Parent Artist', 'wolf-discography' ),
				'parent_item_colon'          => esc_html__( 'Parent Artist:', 'wolf-discography' ),
				'edit_item'                  => esc_html__( 'Edit Artist', 'wolf-discography' ),
				'update_item'                => esc_html__( 'Update Artist', 'wolf-discography' ),
				'add_new_item'               => esc_html__( 'Add New Artist', 'wolf-discography' ),
				'new_item_name'              => esc_html__( 'New Artist', 'wolf-discography' ),
				'separate_items_with_commas' => esc_html__( 'Separate artists with commas', 'wolf-discography' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove artists', 'wolf-discography' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used artists', 'wolf-discography' ),
				'not_found'                  => esc_html__( 'No artists found', 'wolf-discography' ),
				'menu_name'                  => esc_html__( 'Artists', 'wolf-discography' ),
			),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'query_var'             => true,
			'update_count_callback' => '_update_post_term_count',
			'rewrite'               => array(
				'slug'       => 'band',
				'with_front' => false,
			),
		);
	}

	/**
	 * Get label taxonomy configuration
	 *
	 * @return array
	 */
	private static function get_label_config(): array {
		return array(
			'labels'                => array(
				'name'                       => esc_html__( 'Labels', 'wolf-discography' ),
				'singular_name'              => esc_html__( 'Label', 'wolf-discography' ),
				'search_items'               => esc_html__( 'Search Labels', 'wolf-discography' ),
				'popular_items'              => esc_html__( 'Popular Labels', 'wolf-discography' ),
				'all_items'                  => esc_html__( 'All Labels', 'wolf-discography' ),
				'parent_item'                => esc_html__( 'Parent Label', 'wolf-discography' ),
				'parent_item_colon'          => esc_html__( 'Parent Label:', 'wolf-discography' ),
				'edit_item'                  => esc_html__( 'Edit Label', 'wolf-discography' ),
				'update_item'                => esc_html__( 'Update Label', 'wolf-discography' ),
				'add_new_item'               => esc_html__( 'Add New Label', 'wolf-discography' ),
				'new_item_name'              => esc_html__( 'New Label', 'wolf-discography' ),
				'separate_items_with_commas' => esc_html__( 'Separate labels with commas', 'wolf-discography' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove labels', 'wolf-discography' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used labels', 'wolf-discography' ),
				'menu_name'                  => esc_html__( 'Labels', 'wolf-discography' ),
			),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'query_var'             => true,
			'update_count_callback' => '_update_post_term_count',
			'rewrite'               => array(
				'slug'       => 'label',
				'with_front' => false,
			),

		);
	}

	/**
	 * Get genre taxonomy configuration
	 *
	 * @return array
	 */
	private static function get_genre_config(): array {
		return array(
			'labels'                => array(
				'name'                       => esc_html__( 'Genres', 'wolf-discography' ),
				'singular_name'              => esc_html__( 'Genre', 'wolf-discography' ),
				'search_items'               => esc_html__( 'Search Genres', 'wolf-discography' ),
				'popular_items'              => esc_html__( 'Popular Genres', 'wolf-discography' ),
				'all_items'                  => esc_html__( 'All Genres', 'wolf-discography' ),
				'parent_item'                => esc_html__( 'Parent Genre', 'wolf-discography' ),
				'parent_item_colon'          => esc_html__( 'Parent Genre:', 'wolf-discography' ),
				'edit_item'                  => esc_html__( 'Edit Genre', 'wolf-discography' ),
				'update_item'                => esc_html__( 'Update Genre', 'wolf-discography' ),
				'add_new_item'               => esc_html__( 'Add New Genre', 'wolf-discography' ),
				'new_item_name'              => esc_html__( 'New Genre', 'wolf-discography' ),
				'separate_items_with_commas' => esc_html__( 'Separate genres with commas', 'wolf-discography' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove genres', 'wolf-discography' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used genres', 'wolf-discography' ),
				'menu_name'                  => esc_html__( 'Genres', 'wolf-discography' ),
			),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'query_var'             => true,
			'update_count_callback' => '_update_post_term_count',
			'rewrite'               => array(
				'slug'       => 'genre',
				'with_front' => false,
			),
		);
	}

	/**
	 * Get all taxonomy slugs
	 *
	 * @return array
	 */
	public static function get_taxonomy_slugs(): array {
		return array_keys( self::get_config() );
	}

	/**
	 * Get taxonomy configuration by slug
	 *
	 * @param string $taxonomy_slug
	 * @return array|null
	 */
	public static function get_taxonomy_config( string $taxonomy_slug ): ?array {
		$config = self::get_config();
		return $config[ $taxonomy_slug ] ?? null;
	}
}
