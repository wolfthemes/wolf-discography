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
use WolfDiscography\Core\HTMLRenderer;

defined( 'ABSPATH' ) || exit;

class Post {

	private $attribute_processor;
	private $query_builder;
	private $html_renderer;

	public string $cpt_slug = 'release';


	/**
	 * Constructor
	 */
	public function __construct() {
		$this->attribute_processor = new AttributeProcessor();
		$this->query_builder       = new QueryBuilder( $this->cpt_slug );
		$this->html_renderer       = new HTMLRenderer( $this->cpt_slug );
		add_action( 'wolf_discography_posts', array( $this, 'output_posts' ) );
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
		$json_params = $this->html_renderer->build_json_params( $atts );

		/* Get container attributes */
		$container_attrs = $this->html_renderer->build_container_attributes( $atts, $json_params );

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
}
