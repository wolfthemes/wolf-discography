<?php
/**
 * Query Builder for Wolf Discography
 *
 * Handles all query building logic separate from template rendering
 *
 * @package WolfDiscography
 * @subpackage Core
 * @since 2.0.0
 */

namespace WolfDiscography\Core;

use WolfDiscography\Frontend\Helpers;

defined( 'ABSPATH' ) || exit;

class HtmlRenderer {

	/**
	 * Post type slug
	 */
	private string $post_type;

	/**
	 * Constructor
	 *
	 * @param string $post_type Post type slug
	 */
	public function __construct( $post_type = 'release' ) {
		$this->post_type = $post_type;
	}

	public function build_json_params( $atts ) {
		$clean_atts = array_filter(
			$atts,
			function ( $var ) {
				return ( $var );
			}
		); // clean empty atts for json params.
		return wp_json_encode( $clean_atts );
	}

	/**
	 * Build CSS classes for the release container
	 *
	 * @param array $atts Processed attributes array
	 * @return string Complete CSS class string
	 */
	public function build_css_classes( $atts ) {

		// Extract commonly used variables
		$post_type              = $this->post_type;
		$display                = $atts[ $post_type . '_display' ] ?? 'grid';
		$layout                 = $atts[ $post_type . '_layout' ] ?? 'standard';
		$module                 = $atts[ $post_type . '_module' ] ?? 'grid';
		$columns                = $atts['columns'] ?? 3;
		$grid_padding           = $atts['grid_padding'] ?? 'yes';
		$item_animation         = $atts['item_animation'] ?? '';
		$caption_text_alignment = $atts['caption_text_alignment'] ?? 'center';
		$caption_v_align        = $atts['caption_v_align'] ?? 'middle';
		$release_hover_effect   = $atts[ $post_type . '_hover_effect' ] ?? 'default';
		$el_class               = $atts['el_class'] ?? '';
		$hide_class             = $atts['hide_class'] ?? '';

		// Initialize class array
		$classes = array();

		// Base classes
		$classes[] = $el_class;
		$classes[] = $hide_class;
		$classes[] = 'clearfix';
		$classes[] = 'items';
		$classes[] = 'wvc-element'; // backcompat with WVC
		$classes[] = 'wolf-core-element'; // backcompat with Wolf Core
		$classes[] = 'entry-grid-loading';

		// Post type classes
		$classes[] = $post_type . '-items';
		$classes[] = $post_type . 's';

		// Caption alignment classes
		$classes[] = 'caption-text-align-' . $caption_text_alignment;
		$classes[] = 'caption-valign-' . $caption_v_align;

		// Grid padding class
		$grid_padding_value = $this->normalize_bool_value( $grid_padding );
		$classes[]          = 'grid-padding-' . $grid_padding_value;

		// Display classes
		$classes[] = 'display-' . $display;
		$classes[] = $post_type . '-display-' . $display;

		// Animation classes
		if ( $item_animation && 'none' !== $item_animation ) {
			$classes[] = 'has-entrance-animation';
			$classes[] = 'items-entrance-animation-' . $item_animation;
		}

		// Module classes
		$classes[] = 'module-' . $module;
		$classes[] = $post_type . '-module-' . $module;

		// Grid column classes
		if ( 'grid' === $display ) {
			$classes[] = 'post-grid-columns';
			$classes[] = 'post-grid-columns-' . $columns;
			$classes[] = $post_type . '-grid-columns';
			$classes[] = $post_type . '-grid-columns-' . $columns;
		}

		// Layout classes
		$classes[] = 'layout-' . $layout;
		$classes[] = $post_type . '-layout-' . $layout;

		// List detection and class
		if ( $this->is_list( $atts ) ) {
			$classes[] = 'list';
		}

		// Category filter classes
		$category_filter = $this->normalize_bool_value( $atts['release_category_filter'] ?? false );
		if ( $category_filter ) {
			$classes[] = 'filtered-content';
		} else {
			$classes[] = 'no-filtered-content';
		}

		// Hover effect classes
		$classes[] = 'hover-effect-' . $release_hover_effect;
		$classes[] = 'release-hover-effect-' . $release_hover_effect;

		// Clean and sanitize classes
		$classes = array_filter( $classes ); // Remove empty values
		$classes = array_map( 'trim', $classes ); // Trim whitespace
		$classes = array_unique( $classes ); // Remove duplicates

		return implode( ' ', $classes );
	}


	/**
	 * Normalize boolean values to yes/no strings for CSS classes
	 *
	 * @param mixed $value Value to normalize
	 * @return string 'yes' or 'no'
	 */
	private function normalize_bool_value( $value ) {
		if ( is_bool( $value ) ) {
			return $value ? 'yes' : 'no';
		}

		if ( is_string( $value ) ) {
			$value = strtolower( trim( $value ) );
			if ( in_array( $value, array( 'true', '1', 'yes', 'on' ), true ) ) {
				return 'yes';
			}
		}

		return $value ? 'yes' : 'no';
	}

	/**
	 * Build inline styles for the release container
	 *
	 * @param array $atts Processed attributes array
	 * @return string Sanitized inline style string
	 */
	public function build_inline_styles( $atts ) {
		$inline_style = $atts['inline_style'] ?? '';
		$css          = $atts['css'] ?? '';

		// Sanitize user CSS input
		$inline_style = Helpers::sanitize_css_field( $inline_style );

		// Add VC CSS from custom class
		$inline_style .= Helpers::shortcode_custom_style( $css );

		return $inline_style;
	}

	/**
	 * Determine the HTML tag for the container
	 *
	 * @param array $atts Processed attributes array
	 * @return string HTML tag name
	 */
	public function get_container_tag( $atts ) {

		return $this->is_list( $atts ) ? 'ul' : 'div';
	}

	/**
	 * Build complete container attributes
	 *
	 * @param array  $atts Processed attributes array
	 * @param string $json_params JSON encoded parameters
	 * @return array Container attributes
	 */
	public function build_container_attributes( $atts, $json_params ) {

		$post_type = $this->post_type;

		// Generate unique ID
		$unique_id = uniqid( 'items-' . esc_attr( $post_type ) . '-' );
		$id        = ! empty( $atts['el_id'] ) ? $atts['el_id'] : $unique_id;

		// Build all the parts
		$tag           = $this->get_container_tag( $atts );
		$css_classes   = $this->build_css_classes( $atts );
		$inline_styles = $this->build_inline_styles( $atts );

		$attributes = array(
			'tag'                      => $tag,
			'id'                       => esc_attr( $id ),
			'class'                    => Helpers::sanitize_html_classes( $css_classes ),
			'data-post-type'           => esc_attr( $post_type ),
			'data-params'              => esc_js( $json_params ),
			'data-scroll'              => '',
			'data-scroll-css-progress' => '',
		);

		// Add style attribute if we have inline styles
		if ( Helpers::esc_style_attr( $inline_styles ) ) {
			$attributes['style'] = Helpers::esc_style_attr( $inline_styles );
		}

		return $attributes;
	}

	public function is_list( $atts ) {

		$display = $atts[ $this->post_type . '_display' ] ?? 'grid';

		return in_array( $display, array( 'list', 'minimal-list', 'list_minimal', 'small-list', 'text-background' ), true );
	}
}
