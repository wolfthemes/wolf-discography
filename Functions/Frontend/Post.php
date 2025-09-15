<?php
/**
 * Post Output
 *
 * @package WolfDiscography
 * @subpackage Frontend
 * @since 2.0.0
 */

namespace WolfDiscography\Frontend;

use WolfDiscography\Core\AttributeProcessor;
use WolfDiscography\Core\QueryBuilder;

defined( 'ABSPATH' ) || exit;

class Post {

	private $attribute_processor;
	private $query_builder;

	public string $cpt_slug = 'release';


	/**
	 * Constructor
	 */
	public function __construct() {
		$this->attribute_processor = new AttributeProcessor();
		$this->query_builder       = new QueryBuilder( $this->cpt_slug );
		add_action( 'wolf_discography_posts', array( $this, 'output_posts' ) );
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
	 * Output posts
	 *
	 * @param  [array] $atts an array of options to display different post types.
	 * @return void
	 */
	public function output_posts( $atts ) {

		/* Retrieve all VC shortcode attributes and/or set default values */
		$atts = wp_parse_args(
			$atts,
			$this->attribute_processor->get_all_defaults()
		);

		/**
		 * Post module attributes filtered
		 *
		 * @since 1.0.0
		 */
		$atts = apply_filters( 'wd_post_module_atts', $atts );

		// debug( $atts );

		/* Build JSON params array for data attribute */
		$json_params = $this->build_json_params( $atts );

		/* Get container attributes */
		$container_attrs = $this->build_container_attributes( $atts, $json_params );

		/* Extract all attributes as var (mayb be optmized as well!) */
		extract( $atts ); // phpcs:ignore

		$post_type = $this->cpt_slug;

		// Main ID.
		$unique_id = uniqid( 'items-' . esc_attr( $post_type ) . '-' );
		$id        = ( $el_id ) ? $el_id : $unique_id;

		// Layout.
		$layout = ( isset( ${$post_type . '_layout'} ) ) ? ${$post_type . '_layout'} : 'standard';

		/**
		 * Post module layout filtered
		 *
		 * @since 1.0.0
		 */
		$layout = apply_filters( 'wd_post_module_layout', $layout, $atts );

		// Display.
		$display = ( isset( ${$post_type . '_display'} ) ) ? ${$post_type . '_display'} : 'standard';
		$display = apply_filters( 'wd_post_module_display', $display, $atts );

		// Module.
		$module = ( isset( ${$post_type . '_module'} ) ) ? ${$post_type . '_module'} : 'grid';

		/**
		 * Post module module filtered
		 *
		 * @since 1.6.0
		 */
		$module = apply_filters( 'wd_post_module_module', $module, $atts );

		// Filter.
		$category_filter = ( isset( ${$post_type . '_category_filter'} ) ) ? Helpers::attr_bool( ${$post_type . '_category_filter'} ) : false;

		// if ( $pagination && 'none' !== $pagination && -1 !== $posts_per_page && 'post' !== $post_type ) {
		// $category_filter = false;
		// }

		$thumbnail_size        = ( isset( ${$post_type . '_thumbnail_size'} ) ) ? ${$post_type . '_thumbnail_size'} : 'standard';
		$custom_thumbnail_size = ( isset( ${$post_type . '_custom_thumbnail_size'} ) ) ? ${$post_type . '_custom_thumbnail_size'} : '';
		$custom_thumbnail_size = apply_filters( 'wd_post_module_custom_thumbnail_size', $custom_thumbnail_size, $display, $atts );

		$is_index = ( isset( ${$post_type . '_index'} ) ) ? Helpers::attr_bool( ${$post_type . '_index'} ) : false;

		// Disable pagination & filter for carousel module.
		if ( 'carousel' === $module ) {
			$pagination      = 'none';
			$category_filter = null;
		}

		/* Main Query */
		$query = $this->query_builder->build_query( $atts );

		/**
		 * Add action before the output
		 *
		 * @since 1.0.0
		 */
		do_action( 'wd_before_post_module', $atts, $query );

		// Start returning content if we have results.
		if ( $query->have_posts() ) {

			if ( $category_filter ) {
				/*
				 * Pass args to filter template. Cool stuff.
				 */
				set_query_var(
					'filter_args',
					array()
				);

				// Category filter template part
			}

			echo '<' . esc_attr( $container_attrs['tag'] ) . ' ';
			echo 'id="' . esc_attr( $container_attrs['id'] ) . '" ';
			echo 'class="' . Helpers::sanitize_html_classes( $container_attrs['class'] ) . '" ';
			echo 'data-post-type="' . esc_attr( $container_attrs['data-post-type'] ) . '" ';
			echo 'data-params="' . esc_js( $container_attrs['data-params'] ) . '" ';

			if ( isset( $container_attrs['style'] ) ) {
				echo 'style="' . Helpers::esc_style_attr( $container_attrs['style'] ) . '" ';
			}

			echo 'data-scroll data-scroll-css-progress';
			echo apply_filters( 'wd_post_module_additional_params', '' );
			echo '>';
			echo "\n";

			$i = 0;

			if ( ( 0 !== absint( $posts_per_page ) % 2 ) && ( 1 !== absint( $paged ) ) ) {
				$i = 1;
			}

			while ( $query->have_posts() ) {

				++$i;

				$query->the_post();
				$post_id = get_the_ID();

				set_query_var( 'wd_module_atts', $atts );

				/**
				 * Pass args to template
				 */
				set_query_var(
					'template_args',
					/**
					 * Filters post template args to pass
					 *
					 * @since 1.0.0
					 */
					apply_filters(
						'post_template_args',
						array(

							'index'                                => $i,
							'post_id'                              => $post_id,

							'display'                              => $display,
							'layout'                               => $layout,

							'overlay_color'                        => $overlay_color,
							'overlay_custom_color'                 => $overlay_custom_color,
							'overlay_opacity'                      => $overlay_opacity,
							'overlay_text_color'                   => $overlay_text_color,
							'overlay_text_custom_color'            => $overlay_text_custom_color,
							'thumbnail_size'                       => $thumbnail_size,
							'custom_thumbnail_size'                => $custom_thumbnail_size,

							'release_alternate_thumbnail_position' => $release_alternate_thumbnail_position,
							'release_add_buy_links'                => $release_add_buy_links,
							'release_do_redirect_url'              => $release_do_redirect_url,
						),
						$atts
					)
				);

				/*
				 * Include the template part for the content.
				 */
				wolf_discography_get_template_part( 'content', apply_filters( 'wd_post_template_part_name', $display, $atts ) );
			}

			/**
			 * Add action at the end
			 *
			 * @since 1.0.0
			 */
			do_action( 'wd_post_module_end', $atts );

			echo '</' . esc_attr( $container_attrs['tag'] ) . '><!--.' . $this->cpt_slug . '-items-->';

			/**
			 * After post module hook
			 *
			 * @since 1.0.0
			 */
			do_action( 'wd_after_post_module', $atts );

		}
	}

	/**
	 * Build CSS classes for the release container
	 *
	 * @param array $atts Processed attributes array
	 * @return string Complete CSS class string
	 */
	public function build_css_classes( $atts ) {

		// Extract commonly used variables
		$post_type              = $this->cpt_slug;
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

		$post_type = $this->cpt_slug;

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

		$display = $atts[ $this->cpt_slug . '_display' ] ?? 'grid';

		return in_array( $display, array( 'list', 'minimal-list', 'list_minimal', 'small-list', 'text-background' ), true );
	}
}