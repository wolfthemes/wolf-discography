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

use WolfDiscography\Config\ModuleAttributes;

defined( 'ABSPATH' ) || exit;

class AttributeProcessor {

	/**
	 * Attribute registry - single source of truth
	 */
	private function retrieve_attribute_registry() {
		return ModuleAttributes::get_attribute_registry();
	}

	/**
	 * Process attributes from any source into categorized arrays
	 *
	 * @param array  $raw_atts Raw attributes from any source
	 * @param string $source Source identifier for debugging
	 * @return array Processed and categorized attributes
	 */
	public function process_attributes( $raw_atts = array(), $source = 'unknown' ) {
		$registry = $this->retrieve_attribute_registry();

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
		$registry = $this->retrieve_attribute_registry();

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
		$registry = $this->retrieve_attribute_registry();
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
