<?php
/**
 * Attribute Processor for Wolf Discography
 *
 * Handles attribute processing from multiple sources:
 * - Shortcodes
 * - Elementor widgets
 * - Gutenberg blocks
 * - REST API
 * - Admin defaults
 *
 * @package WolfDiscography
 * @subpackage Core
 * @since 2.0.0
 */

namespace WolfDiscography\Core;

defined( 'ABSPATH' ) || exit;

class AttributeProcessor {

	/**
	 * Attribute registry - single source of truth
	 */
	private function get_attribute_registry() {
		return array(
			// Query attributes (affect database queries)
			'query'    => array(
				'post_type'      => array(
					'default'     => 'release',
					'type'        => 'string',
					'description' => 'Post type to query',
				),
				'posts_per_page' => array(
					'default'     => 100,
					'type'        => 'int',
					'aliases'     => array( 'postsPerPage', 'per_page', 'count' ),
					'min'         => -1,
					'max'         => 500,
					'description' => 'Number of posts to retrieve (-1 for all)',
				),
				'paged'          => array(
					'default'     => null,
					'type'        => 'int',
					'aliases'     => array( 'page', 'currentPage' ),
					'min'         => 1,
					'description' => 'Page number for pagination',
				),
				'orderby'        => array(
					'default'     => '',
					'type'        => 'string',
					'enum'        => array( '', 'date', 'title', 'menu_order', 'rand', 'post__in', 'release_date' ),
					'description' => 'Field to order posts by',
				),
				'order'          => array(
					'default'     => 'DESC',
					'type'        => 'string',
					'enum'        => array( 'ASC', 'DESC' ),
					'description' => 'Sort order direction',
				),
				'include_ids'    => array(
					'default'     => '',
					'type'        => 'csv',
					'aliases'     => array( 'includeIds', 'post__in', 'ids' ),
					'description' => 'Comma-separated list of post IDs to include',
				),
				'exclude_ids'    => array(
					'default'     => '',
					'type'        => 'csv',
					'aliases'     => array( 'excludeIds', 'post__not_in' ),
					'description' => 'Comma-separated list of post IDs to exclude',
				),
				'band_include'   => array(
					'default'     => '',
					'type'        => 'csv',
					'aliases'     => array( 'bandInclude', 'bands', 'band' ),
					'description' => 'Include specific bands (slugs or names)',
				),
				'band_exclude'   => array(
					'default'     => '',
					'type'        => 'csv',
					'aliases'     => array( 'bandExclude' ),
					'description' => 'Exclude specific bands',
				),
				'genre_include'  => array(
					'default'     => '',
					'type'        => 'csv',
					'aliases'     => array( 'genreInclude', 'genres', 'genre' ),
					'description' => 'Include specific genres',
				),
				'genre_exclude'  => array(
					'default'     => '',
					'type'        => 'csv',
					'aliases'     => array( 'genreExclude' ),
					'description' => 'Exclude specific genres',
				),
				'label_include'  => array(
					'default'     => '',
					'type'        => 'csv',
					'aliases'     => array( 'labelInclude', 'labels', 'label' ),
					'description' => 'Include specific labels',
				),
				'label_exclude'  => array(
					'default'     => '',
					'type'        => 'csv',
					'aliases'     => array( 'labelExclude' ),
					'description' => 'Exclude specific labels',
				),
				'release_meta'   => array(
					'default'     => '',
					'type'        => 'string',
					'aliases'     => array( 'releaseMeta', 'meta' ),
					'enum'        => array( '', 'featured', 'upcoming' ),
					'description' => 'Filter by release meta status',
				),
				'offset'         => array(
					'default'     => 0,
					'type'        => 'int',
					'min'         => 0,
					'description' => 'Number of posts to skip',
				),
			),

			// Display attributes (affect HTML structure)
			'display'  => array(
				'columns'               => array(
					'default'     => 3,
					'type'        => 'int',
					'min'         => 1,
					'max'         => 6,
					'description' => 'Number of columns for grid display',
				),
				'display'               => array(
					'default'     => 'grid',
					'type'        => 'string',
					'aliases'     => array( 'layout_type', 'view' ),
					'enum'        => array( 'grid', 'list', 'carousel', 'masonry' ),
					'description' => 'Display layout type',
				),
				'layout'                => array(
					'default'     => 'standard',
					'type'        => 'string',
					'enum'        => array( 'standard', 'minimal', 'detailed' ),
					'description' => 'Layout variation within display type',
				),
				'thumbnail_size'        => array(
					'default'     => 'square',
					'type'        => 'string',
					'aliases'     => array( 'thumbnailSize', 'image_size', 'imageSize' ),
					'description' => 'Featured image size',
				),
				'custom_thumbnail_size' => array(
					'default'     => '',
					'type'        => 'string',
					'aliases'     => array( 'customThumbnailSize', 'custom_image_size' ),
					'description' => 'Custom image size (WxH)',
				),
				'grid_padding'          => array(
					'default'     => 'yes',
					'type'        => 'bool',
					'aliases'     => array( 'gridPadding', 'padding' ),
					'description' => 'Add padding between grid items',
				),
			),

			// Behavior attributes (affect JavaScript/interactions)
			'behavior' => array(
				'category_filter' => array(
					'default'     => false,
					'type'        => 'bool',
					'aliases'     => array( 'categoryFilter', 'filter', 'filtering' ),
					'description' => 'Enable category filtering',
				),
				'load_more'       => array(
					'default'     => false,
					'type'        => 'bool',
					'aliases'     => array( 'loadMore', 'pagination' ),
					'description' => 'Enable load more button',
				),
				'ajax_loading'    => array(
					'default'     => false,
					'type'        => 'bool',
					'aliases'     => array( 'ajaxLoading', 'ajax' ),
					'description' => 'Enable AJAX content loading',
				),
				'item_animation'  => array(
					'default'     => '',
					'type'        => 'string',
					'aliases'     => array( 'itemAnimation', 'animation' ),
					'enum'        => array( '', 'fade', 'slide', 'zoom' ),
					'description' => 'Animation for items',
				),
			),

			// Style attributes (affect CSS)
			'style'    => array(
				'overlay_color'          => array(
					'default'     => 'black',
					'type'        => 'string',
					'aliases'     => array( 'overlayColor' ),
					'description' => 'Overlay background color',
				),
				'overlay_opacity'        => array(
					'default'     => 44,
					'type'        => 'int',
					'aliases'     => array( 'overlayOpacity' ),
					'min'         => 0,
					'max'         => 100,
					'description' => 'Overlay opacity percentage',
				),
				'caption_text_alignment' => array(
					'default'     => 'center',
					'type'        => 'string',
					'aliases'     => array( 'captionTextAlignment', 'textAlign' ),
					'enum'        => array( 'left', 'center', 'right' ),
					'description' => 'Text alignment for captions',
				),
				'hover_effect'           => array(
					'default'     => 'default',
					'type'        => 'string',
					'aliases'     => array( 'hoverEffect' ),
					'enum'        => array( 'default', 'zoom', 'fade', 'slide' ),
					'description' => 'Hover effect for items',
				),
			),

			// Output attributes (affect rendering method)
			'output'   => array(
				'format'          => array(
					'default'     => 'html',
					'type'        => 'string',
					'enum'        => array( 'html', 'json', 'data' ),
					'description' => 'Output format',
				),
				'css_mode'        => array(
					'default'     => 'auto',
					'type'        => 'string',
					'aliases'     => array( 'cssMode' ),
					'enum'        => array( 'auto', 'legacy', 'modern', 'minimal' ),
					'description' => 'CSS class generation mode',
				),
				'template_source' => array(
					'default'     => 'auto',
					'type'        => 'string',
					'aliases'     => array( 'templateSource' ),
					'enum'        => array( 'auto', 'theme', 'builtin' ),
					'description' => 'Template source preference',
				),
				'el_class'        => array(
					'default'     => '',
					'type'        => 'string',
					'aliases'     => array( 'elClass', 'className', 'css_class', 'class' ),
					'description' => 'Additional CSS classes',
				),
				'el_id'           => array(
					'default'     => '',
					'type'        => 'string',
					'aliases'     => array( 'elId', 'id' ),
					'description' => 'Element ID',
				),
			),
		);
	}

	/**
	 * Process attributes from any source into categorized arrays
	 *
	 * @param array  $raw_atts Raw attributes from any source
	 * @param string $source Source identifier for debugging
	 * @return array Processed and categorized attributes
	 */
	public function process_attributes( $raw_atts = array(), $source = 'unknown' ) {
		$registry = $this->get_attribute_registry();

		// Initialize processed structure
		$processed = array(
			'query'    => array(),
			'display'  => array(),
			'behavior' => array(),
			'style'    => array(),
			'output'   => array(),
			'_meta'    => array(
				'source'       => $source,
				'raw_input'    => $raw_atts,
				'processed_at' => current_time( 'timestamp' ),
			),
		);

		// Normalize attribute names (handle aliases)
		$normalized_atts = $this->normalize_attribute_names( $raw_atts, $registry );

		// Process each category
		foreach ( $registry as $category => $attributes ) {
			foreach ( $attributes as $attr_name => $config ) {
				$raw_value                            = $normalized_atts[ $attr_name ] ?? $config['default'];
				$processed[ $category ][ $attr_name ] = $this->validate_and_cast( $raw_value, $config, $attr_name );
			}
		}

		// Apply contextual defaults
		$processed = $this->apply_contextual_defaults( $processed, $source );

		return $processed;
	}

	/**
	 * Normalize attribute names using aliases
	 */
	private function normalize_attribute_names( $raw_atts, $registry ) {
		$normalized = array();

		// Build alias map
		$alias_map = array();
		foreach ( $registry as $category => $attributes ) {
			foreach ( $attributes as $canonical_name => $config ) {
				// Map canonical name to itself
				$alias_map[ $canonical_name ] = $canonical_name;

				// Map aliases to canonical name
				if ( ! empty( $config['aliases'] ) ) {
					foreach ( $config['aliases'] as $alias ) {
						$alias_map[ $alias ] = $canonical_name;
					}
				}
			}
		}

		// Normalize input using alias map
		foreach ( $raw_atts as $key => $value ) {
			$canonical_name = $alias_map[ $key ] ?? null;
			if ( $canonical_name ) {
				$normalized[ $canonical_name ] = $value;
			}
		}

		return $normalized;
	}

	/**
	 * Validate and cast value according to type configuration
	 */
	private function validate_and_cast( $value, $config, $attr_name ) {
		$type = $config['type'] ?? 'string';

		// Handle empty values
		if ( empty( $value ) && $value !== 0 && $value !== '0' ) {
			return $config['default'];
		}

		switch ( $type ) {
			case 'int':
				$value = intval( $value );

				// Apply min/max constraints
				if ( isset( $config['min'] ) && $value < $config['min'] ) {
					$value = $config['min'];
				}
				if ( isset( $config['max'] ) && $value > $config['max'] ) {
					$value = $config['max'];
				}

				return $value;

			case 'bool':
				return $this->cast_to_bool( $value );

			case 'string':
				$value = sanitize_text_field( $value );

				// Validate against enum if provided
				if ( ! empty( $config['enum'] ) && ! in_array( $value, $config['enum'], true ) ) {
					return $config['default'];
				}

				return $value;

			case 'csv':
				return $this->process_csv_value( $value );

			default:
				return sanitize_text_field( $value );
		}
	}

	/**
	 * Cast value to boolean (handles various formats)
	 */
	private function cast_to_bool( $value ) {
		if ( is_bool( $value ) ) {
			return $value;
		}

		if ( is_string( $value ) ) {
			$value = strtolower( trim( $value ) );
			return in_array( $value, array( 'true', '1', 'yes', 'on' ), true );
		}

		return (bool) $value;
	}

	/**
	 * Process comma-separated values
	 */
	private function process_csv_value( $value ) {
		if ( is_array( $value ) ) {
			return $value;
		}

		if ( empty( $value ) ) {
			return array();
		}

		// Split by comma and clean up
		$items = explode( ',', $value );
		$items = array_map( 'trim', $items );
		$items = array_filter( $items ); // Remove empty values

		return array_values( $items ); // Re-index array
	}

	/**
	 * Apply contextual defaults based on source
	 */
	private function apply_contextual_defaults( $processed, $source ) {
		switch ( $source ) {
			case 'rest_api':
				// REST API might prefer JSON output by default
				if ( $processed['output']['format'] === 'html' ) {
					$processed['output']['format'] = 'json';
				}
				break;

			case 'gutenberg':
				// Gutenberg might prefer modern CSS
				if ( $processed['output']['css_mode'] === 'auto' ) {
					$processed['output']['css_mode'] = 'modern';
				}
				break;

			case 'legacy':
			case 'shortcode':
				// Legacy sources should use legacy CSS by default
				if ( $processed['output']['css_mode'] === 'auto' ) {
					$processed['output']['css_mode'] = 'legacy';
				}
				break;
		}

		return $processed;
	}

	/**
	 * Get default attributes for a specific category
	 */
	public function get_category_defaults( $category ) {
		$registry = $this->get_attribute_registry();

		if ( ! isset( $registry[ $category ] ) ) {
			return array();
		}

		$defaults = array();
		foreach ( $registry[ $category ] as $attr_name => $config ) {
			$defaults[ $attr_name ] = $config['default'];
		}

		return $defaults;
	}

	/**
	 * Get all default attributes (flattened)
	 */
	public function get_all_defaults() {
		$registry = $this->get_attribute_registry();
		$defaults = array();

		foreach ( $registry as $category => $attributes ) {
			foreach ( $attributes as $attr_name => $config ) {
				$defaults[ $attr_name ] = $config['default'];
			}
		}

		return $defaults;
	}

	/**
	 * Validate processed attributes (for debugging)
	 */
	public function validate_processed_attributes( $processed ) {
		$errors = array();

		// Check structure
		$required_categories = array( 'query', 'display', 'behavior', 'style', 'output', '_meta' );
		foreach ( $required_categories as $category ) {
			if ( ! isset( $processed[ $category ] ) ) {
				$errors[] = "Missing category: {$category}";
			}
		}

		return $errors;
	}
}