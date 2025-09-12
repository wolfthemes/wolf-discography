<?php
/**
 * Discography Taxonomies
 *
 * Handles registration of discography-related taxonomies
 *
 * @package WolfDiscography
 * @subpackage Taxonomies
 * @since 2.0.0
 */

namespace WolfDiscography\Taxonomies;

defined( 'ABSPATH' ) || exit;

/**
 * Discography Taxonomies Class
 *
 * Manages the registration of band, label, and genre taxonomies
 */
class DiscographyTaxonomies {

	/**
	 * Taxonomies configuration
	 *
	 * @var array
	 */
	private array $taxonomies = array(
		'band'          => array(
			'labels'       => array(
				'name'              => 'Bands',
				'singular_name'     => 'Band',
				'search_items'      => 'Search Bands',
				'all_items'         => 'All Bands',
				'parent_item'       => 'Parent Band',
				'parent_item_colon' => 'Parent Band:',
				'edit_item'         => 'Edit Band',
				'update_item'       => 'Update Band',
				'add_new_item'      => 'Add New Band',
				'new_item_name'     => 'New Band Name',
				'menu_name'         => 'Bands',
			),
			'hierarchical' => true,
			'show_ui'      => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'band' ),
		),
		'label'         => array(
			'labels'       => array(
				'name'              => 'Labels',
				'singular_name'     => 'Label',
				'search_items'      => 'Search Labels',
				'all_items'         => 'All Labels',
				'parent_item'       => 'Parent Label',
				'parent_item_colon' => 'Parent Label:',
				'edit_item'         => 'Edit Label',
				'update_item'       => 'Update Label',
				'add_new_item'      => 'Add New Label',
				'new_item_name'     => 'New Label Name',
				'menu_name'         => 'Labels',
			),
			'hierarchical' => true,
			'show_ui'      => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'label' ),
		),
		'release_genre' => array(
			'labels'       => array(
				'name'              => 'Genres',
				'singular_name'     => 'Genre',
				'search_items'      => 'Search Genres',
				'all_items'         => 'All Genres',
				'parent_item'       => 'Parent Genre',
				'parent_item_colon' => 'Parent Genre:',
				'edit_item'         => 'Edit Genre',
				'update_item'       => 'Update Genre',
				'add_new_item'      => 'Add New Genre',
				'new_item_name'     => 'New Genre Name',
				'menu_name'         => 'Genres',
			),
			'hierarchical' => true,
			'show_ui'      => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'genre' ),
		),
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		// Constructor is kept light - actual registration happens in register()
	}

	/**
	 * Register all taxonomies
	 */
	public function register(): void {
		add_action( 'init', array( $this, 'registerTaxonomies' ) );
	}

	/**
	 * Register the discography taxonomies
	 */
	public function registerTaxonomies(): void {
		// During migration, we can either:
		// 1. Call the legacy file (safe approach)
		// 2. Implement the registration here (new approach)

		// Option 1: Legacy approach (safe during migration)
		$legacy_file = WD_DIR . '/inc/wd-register-taxonomy.php';
		if ( file_exists( $legacy_file ) ) {
			include_once $legacy_file;
			return;
		}

		// Option 2: New implementation (uncomment when ready to migrate)
		/*
		foreach ($this->taxonomies as $taxonomy_slug => $config) {
			$this->registerTaxonomy($taxonomy_slug, $config);
		}
		*/
	}

	/**
	 * Register a single taxonomy
	 *
	 * @param string $taxonomy_slug Taxonomy slug
	 * @param array  $config        Taxonomy configuration
	 */
	private function registerTaxonomy( string $taxonomy_slug, array $config ): void {
		// Translate labels
		$labels = array();
		foreach ( $config['labels'] as $key => $label ) {
			$labels[ $key ] = _x( $label, 'Taxonomy label', 'wolf-discography' );
		}

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => $config['hierarchical'] ?? false,
			'public'            => true,
			'show_ui'           => $config['show_ui'] ?? true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'show_in_rest'      => $config['show_in_rest'] ?? true,
			'rewrite'           => $config['rewrite'] ?? array( 'slug' => $taxonomy_slug ),
		);

		// Allow filtering of taxonomy arguments
		$args = apply_filters( "wolf_discography_{$taxonomy_slug}_taxonomy_args", $args );

		register_taxonomy( $taxonomy_slug, array( 'release' ), $args );
	}

	/**
	 * Get all registered taxonomies
	 *
	 * @return array
	 */
	public function getTaxonomies(): array {
		return apply_filters( 'wolf_discography_taxonomies', $this->taxonomies );
	}

	/**
	 * Get taxonomy configuration
	 *
	 * @param string $taxonomy_slug Taxonomy slug
	 * @return array|null
	 */
	public function getTaxonomyConfig( string $taxonomy_slug ): ?array {
		return $this->taxonomies[ $taxonomy_slug ] ?? null;
	}

	/**
	 * Check if taxonomy exists in our configuration
	 *
	 * @param string $taxonomy_slug Taxonomy slug
	 * @return bool
	 */
	public function hasTaxonomy( string $taxonomy_slug ): bool {
		return isset( $this->taxonomies[ $taxonomy_slug ] );
	}
}
