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
use WolfDiscography\Taxonomies\TaxonomyConfig;

defined( 'ABSPATH' ) || exit;

/**
 * Discography Taxonomies Class
 *
 * Manages the registration of band, label, and genre taxonomies
 */
class Taxonomies {

	/**
	 * Taxonomies configuration
	 *
	 * @var array
	 */
	private array $taxonomies = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		// Constructor is kept light - actual registration happens in register()
		$this->load_config();

	}

	/**
	 * Load metabox configuration from Config class
	 */
	private function load_config(): void {
		$this->taxonomies = TaxonomyConfig::get_config();
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
		foreach ($this->taxonomies as $taxonomy_slug => $config) {
			$this->registerTaxonomy($taxonomy_slug, $config);
		}
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