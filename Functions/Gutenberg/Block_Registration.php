<?php
/**
 * Gutenberg Block Registration
 *
 * @package WolfDiscography
 * @subpackage Gutenberg
 * @since 2.0.0
 */

namespace Wolf_Discography\Gutenberg;

use Wolf_Discography\Config\Module_Attributes;
use Wolf_Discography\Core\Attribute_Processor;
use Wolf_Discography\Core\Query_Builder;
use Wolf_Discography\Core\HTML_Renderer;
use Wolf_Discography\Core\Utilities;

defined( 'ABSPATH' ) || exit;

class Block_Registration {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_assets' ) );
	}

	/**
	 * Register Gutenberg blocks
	 */
	public function register_blocks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// Register releases block
		register_block_type( 'wolf-discography/releases', array(
			'attributes'      => $this->get_block_attributes(),
			'render_callback' => array( $this, 'render_releases_block' ),
			'editor_script'   => 'wolf-discography-blocks',
			'editor_style'    => 'wolf-discography-blocks-editor',
			'style'          => 'wolf-discography-blocks-frontend',
		) );

		// Register single release info block
		register_block_type( 'wolf-discography/release-info', array(
			'attributes'      => $this->get_release_info_attributes(),
			'render_callback' => array( $this, 'render_release_info_block' ),
			'editor_script'   => 'wolf-discography-blocks',
		) );

		// Register buy links block
		register_block_type( 'wolf-discography/buy-links', array(
			'attributes'      => array(
				'postId' => array(
					'type'    => 'number',
					'default' => 0,
				),
			),
			'render_callback' => array( $this, 'render_buy_links_block' ),
			'editor_script'   => 'wolf-discography-blocks',
		) );
	}

	/**
	 * Get block attributes from ModuleAttributes registry
	 */
	private function get_block_attributes() {
		if ( ! class_exists( 'Wolf_Discography\Config\Module_Attributes' ) ) {
			return array();
		}

		$registry = Module_Attributes::get_attribute_registry();
		$block_attributes = array();

		// Convert internal attributes to Gutenberg block attributes
		foreach ( $registry as $category => $attributes ) {
			foreach ( $attributes as $attr_name => $config ) {
				$block_attributes[ $this->camelCase( $attr_name ) ] = $this->convert_to_block_attribute( $config );
			}
		}

		// Add block-specific attributes
		$block_attributes['align'] = array(
			'type' => 'string',
		);

		$block_attributes['className'] = array(
			'type' => 'string',
		);

		// Utilities::debug( $block_attributes );

		return $block_attributes;
	}

	/**
	 * Convert internal attribute config to Gutenberg block attribute
	 */
	private function convert_to_block_attribute( $config ) {
		$block_attr = array(
			'type'    => $this->map_type_to_gutenberg( $config['type'] ),
			'default' => $config['default'],
		);

		// Add enum values if available
		if ( ! empty( $config['enum'] ) ) {
			$block_attr['enum'] = $config['enum'];
		}

		// Add validation for numbers
		if ( 'number' === $block_attr['type'] ) {
			if ( isset( $config['min'] ) ) {
				$block_attr['minimum'] = $config['min'];
			}
			if ( isset( $config['max'] ) ) {
				$block_attr['maximum'] = $config['max'];
			}
		}

		return $block_attr;
	}

	/**
	 * Map internal types to Gutenberg types
	 */
	private function map_type_to_gutenberg( $type ) {
		$type_mapping = array(
			'int'    => 'number',
			'bool'   => 'boolean',
			'string' => 'string',
			'csv'    => 'string', // CSV will be handled as comma-separated string
		);

		return $type_mapping[ $type ] ?? 'string';
	}

	/**
	 * Convert snake_case to camelCase for Gutenberg
	 */
	private function camelCase( $string ) {
		return lcfirst( str_replace( '_', '', ucwords( $string, '_' ) ) );
	}

	/**
	 * Convert camelCase back to snake_case for internal processing
	 */
	private function snake_case( $string ) {
		return strtolower( preg_replace( '/(?<!^)[A-Z]/', '_$0', $string ) );
	}

	/**
	 * Render releases block
	 */
	public function render_releases_block( $attributes, $content ) {
		// Convert camelCase attributes back to snake_case

		ob_start();

		do_action( 'wolf_discography_posts', $attributes, 'block' );

		return ob_get_clean();
	}

	/**
	 * Get release info block attributes
	 */
	private function get_release_info_attributes() {
		return array(
			'postId' => array(
				'type'    => 'number',
				'default' => 0,
			),
			'showBands' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'showLabels' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'showGenres' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'showReleaseDate' => array(
				'type'    => 'boolean',
				'default' => true,
			),
		);
	}

	/**
	 * Render release info block
	 */
	public function render_release_info_block( $attributes, $content ) {
		$post_id = $attributes['postId'] ?: get_the_ID();

		if ( ! $post_id || get_post_type( $post_id ) !== 'release' ) {
			return '';
		}

		$output = '<div class="wp-block-wolf-discography-release-info">';

		if ( $attributes['showReleaseDate'] ) {
			$release_date = get_post_meta( $post_id, '_release_date', true );
			if ( $release_date ) {
				$output .= '<p class="release-date"><strong>' .
				          esc_html__( 'Release Date:', 'wolf-discography' ) . '</strong> ' .
				          esc_html( date( 'F j, Y', strtotime( $release_date ) ) ) . '</p>';
			}
		}

		if ( $attributes['showBands'] ) {
			$bands = get_the_terms( $post_id, 'band' );
			if ( $bands && ! is_wp_error( $bands ) ) {
				$band_names = array_map( function( $band ) { return $band->name; }, $bands );
				$output .= '<p class="release-bands"><strong>' .
				          esc_html__( 'Band:', 'wolf-discography' ) . '</strong> ' .
				          esc_html( implode( ', ', $band_names ) ) . '</p>';
			}
		}

		if ( $attributes['showLabels'] ) {
			$labels = get_the_terms( $post_id, 'label' );
			if ( $labels && ! is_wp_error( $labels ) ) {
				$label_names = array_map( function( $label ) { return $label->name; }, $labels );
				$output .= '<p class="release-labels"><strong>' .
				          esc_html__( 'Label:', 'wolf-discography' ) . '</strong> ' .
				          esc_html( implode( ', ', $label_names ) ) . '</p>';
			}
		}

		if ( $attributes['showGenres'] ) {
			$genres = get_the_terms( $post_id, 'release_genre' );
			if ( $genres && ! is_wp_error( $genres ) ) {
				$genre_names = array_map( function( $genre ) { return $genre->name; }, $genres );
				$output .= '<p class="release-genres"><strong>' .
				          esc_html__( 'Genre:', 'wolf-discography' ) . '</strong> ' .
				          esc_html( implode( ', ', $genre_names ) ) . '</p>';
			}
		}

		$output .= '</div>';

		return $output;
	}

	/**
	 * Render buy links block
	 */
	public function render_buy_links_block( $attributes, $content ) {
		$post_id = $attributes['postId'] ?: get_the_ID();

		if ( ! $post_id || get_post_type( $post_id ) !== 'release' ) {
			return '';
		}

		$buy_links = get_post_meta( $post_id, '_release_buy_links', true );

		if ( empty( $buy_links ) || ! is_array( $buy_links ) ) {
			return '';
		}

		$output = '<div class="wp-block-wolf-discography-buy-links">';
		$output .= '<h3>' . esc_html__( 'Available On:', 'wolf-discography' ) . '</h3>';
		$output .= '<ul class="buy-links-list">';

		foreach ( $buy_links as $link ) {
			if ( ! empty( $link['url'] ) && ! empty( $link['label'] ) ) {
				$output .= '<li>';
				$output .= '<a href="' . esc_url( $link['url'] ) . '" target="_blank" rel="noopener noreferrer">';
				$output .= esc_html( $link['label'] );
				$output .= '</a>';
				$output .= '</li>';
			}
		}

		$output .= '</ul>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Enqueue block assets
	 */
	public function enqueue_block_assets() {
		wp_enqueue_script(
			'wolf-discography-blocks',
			WD_JS . '/blocks.js',
			array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components' ),
			WD_VERSION,
			true
		);

		wp_enqueue_style(
			'wolf-discography-blocks-editor',
			WD_CSS . '/blocks-editor.css',
			array( 'wp-edit-blocks' ),
			WD_VERSION
		);

		wp_enqueue_style(
			'wolf-discography-blocks-frontend',
			WD_CSS . '/blocks-frontend.css',
			array(),
			WD_VERSION
		);

		// Localize script with data
		wp_localize_script( 'wolf-discography-blocks', 'wolfDiscographyBlocks', array(
			'apiUrl' => rest_url( 'wolf-discography/v1/' ),
			'nonce'  => wp_create_nonce( 'wp_rest' ),
		) );
	}
}