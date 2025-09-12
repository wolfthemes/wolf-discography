<?php
/**
 * Taxonomy Configuration
 *
 * Defines all taxonomy configurations for the plugin
 *
 * @package WolfDiscography
 * @subpackage Taxonomies\Config
 * @since 2.0.0
 */

namespace WolfDiscography\Taxonomies;

defined('ABSPATH') || exit;

/**
 * Taxonomy Configuration Class
 */
class TaxonomyConfig {

    /**
     * Get all taxonomy configurations
     *
     * @return array
     */
    public static function get_config(): array {
        return [
            'band' => self::get_band_config(),
            'label' => self::get_label_config(),
            'release_genre' => self::get_genre_config(),
        ];
    }

    /**
     * Get band taxonomy configuration
     *
     * @return array
     */
    private static function get_band_config(): array {
        return [
            'labels' => [
                'name'                       => __('Artists', 'wolf-discography'),
                'singular_name'              => __('Artist', 'wolf-discography'),
                'search_items'               => __('Search Artists', 'wolf-discography'),
                'popular_items'              => __('Popular Artists', 'wolf-discography'),
                'all_items'                  => __('All Artists', 'wolf-discography'),
                'parent_item'                => __('Parent Artist', 'wolf-discography'),
                'parent_item_colon'          => __('Parent Artist:', 'wolf-discography'),
                'edit_item'                  => __('Edit Artist', 'wolf-discography'),
                'update_item'                => __('Update Artist', 'wolf-discography'),
                'add_new_item'               => __('Add New Artist', 'wolf-discography'),
                'new_item_name'              => __('New Artist', 'wolf-discography'),
                'separate_items_with_commas' => __('Separate artists with commas', 'wolf-discography'),
                'add_or_remove_items'        => __('Add or remove artists', 'wolf-discography'),
                'choose_from_most_used'      => __('Choose from the most used artists', 'wolf-discography'),
                'not_found'                  => __('No artists found', 'wolf-discography'),
                'menu_name'                  => __('Artists', 'wolf-discography'),
            ],
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'show_in_nav_menus'     => true,
            'show_tagcloud'         => true,
            'show_in_rest'          => true,
            'query_var'             => true,
            'update_count_callback' => '_update_post_term_count',
            'rewrite'               => [
                'slug'       => 'band',
                'with_front' => false,
            ],
        ];
    }

    /**
     * Get label taxonomy configuration
     *
     * @return array
     */
    private static function get_label_config(): array {
        return [
            'labels' => [
                'name'                       => __('Labels', 'wolf-discography'),
                'singular_name'              => __('Label', 'wolf-discography'),
                'search_items'               => __('Search Labels', 'wolf-discography'),
                'popular_items'              => __('Popular Labels', 'wolf-discography'),
                'all_items'                  => __('All Labels', 'wolf-discography'),
                'parent_item'                => __('Parent Label', 'wolf-discography'),
                'parent_item_colon'          => __('Parent Label:', 'wolf-discography'),
                'edit_item'                  => __('Edit Label', 'wolf-discography'),
                'update_item'                => __('Update Label', 'wolf-discography'),
                'add_new_item'               => __('Add New Label', 'wolf-discography'),
                'new_item_name'              => __('New Label', 'wolf-discography'),
                'separate_items_with_commas' => __('Separate labels with commas', 'wolf-discography'),
                'add_or_remove_items'        => __('Add or remove labels', 'wolf-discography'),
                'choose_from_most_used'      => __('Choose from the most used labels', 'wolf-discography'),
                'not_found'                  => __('No labels found', 'wolf-discography'),
                'menu_name'                  => __('Labels', 'wolf-discography'),
            ],
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'show_in_nav_menus'     => true,
            'show_tagcloud'         => true,
            'show_in_rest'          => true,
            'query_var'             => true,
            'update_count_callback' => '_update_post_term_count',
            'rewrite'               => [
                'slug'       => 'label',
                'with_front' => false,
            ],
        ];
    }

    /**
     * Get genre taxonomy configuration
     *
     * @return array
     */
    private static function get_genre_config(): array {
        return [
            'labels' => [
                'name'                       => __('Genres', 'wolf-discography'),
                'singular_name'              => __('Genre', 'wolf-discography'),
                'search_items'               => __('Search Genres', 'wolf-discography'),
                'popular_items'              => __('Popular Genres', 'wolf-discography'),
                'all_items'                  => __('All Genres', 'wolf-discography'),
                'parent_item'                => __('Parent Genre', 'wolf-discography'),
                'parent_item_colon'          => __('Parent Genre:', 'wolf-discography'),
                'edit_item'                  => __('Edit Genre', 'wolf-discography'),
                'update_item'                => __('Update Genre', 'wolf-discography'),
                'add_new_item'               => __('Add New Genre', 'wolf-discography'),
                'new_item_name'              => __('New Genre', 'wolf-discography'),
                'separate_items_with_commas' => __('Separate genres with commas', 'wolf-discography'),
                'add_or_remove_items'        => __('Add or remove genres', 'wolf-discography'),
                'choose_from_most_used'      => __('Choose from the most used genres', 'wolf-discography'),
                'not_found'                  => __('No genres found', 'wolf-discography'),
                'menu_name'                  => __('Genres', 'wolf-discography'),
            ],
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'show_in_nav_menus'     => true,
            'show_tagcloud'         => true,
            'show_in_rest'          => true,
            'query_var'             => true,
            'update_count_callback' => '_update_post_term_count',
            'rewrite'               => [
                'slug'       => 'genre',
                'with_front' => false,
            ],
        ];
    }

    /**
     * Get all taxonomy slugs
     *
     * @return array
     */
    public static function get_taxonomy_slugs(): array {
        return array_keys(self::get_config());
    }

    /**
     * Get taxonomy configuration by slug
     *
     * @param string $taxonomy_slug
     * @return array|null
     */
    public static function get_taxonomy_config(string $taxonomy_slug): ?array {
        $config = self::get_config();
        return $config[$taxonomy_slug] ?? null;
    }
}