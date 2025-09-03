<?php
/**
 * Post hooks functions
 *
 * A function that returns the release loop
 * @author WolfThemes
 * @category Core
 * @package WolfDiscography/Functions
 * @since 1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default post attributes
 */
function wd_get_default_post_atts() {

	return array(

		/* Core */
		'post_type'                              => 'release',
		'posts_per_page'                         => 100,
		'paged'                                  => null,

		'orderby'                                => '',
		'order'                                  => '',

		/* Common attributes */
		'grid_padding'                           => wolf_get_release_option( 'post_grid_padding', 'yes' ),
		'item_animation'                         => wolf_get_release_option( 'post_item_animation' ),
		'columns'                                => 3,
		'include_ids'                            => '',
		'exclude_ids'                            => '',
		'offset'                                 => 0,

		/**
		 * Item default overlay color filtered
		 *
		 * @since 1.0.0
		 */
		'overlay_color'                          => apply_filters( 'wd_default_item_overlay_color', 'black' ),

		/**
		 * Item default overlay custom color filtered
		 *
		 * @since 1.0.0
		 */
		'overlay_custom_color'                   => apply_filters( 'wd_default_item_overlay_custom_color', '' ),

		/**
		 * Item default overlay text color filtered
		 *
		 * @since 1.0.0
		 */
		'overlay_text_color'                     => apply_filters( 'wd_default_item_overlay_text_color', 'white' ),
		'overlay_text_custom_color'              => '',

		/**
		 * Item default overlay opacity filtered
		 *
		 * @since 1.0.0
		 */
		'overlay_opacity'                        => apply_filters( 'wd_default_item_overlay_opacity', 44 ),

		/**
		 * Item default text alignement
		 *
		 * @since 1.0.0
		 */
		'caption_text_alignment'                 => apply_filters( 'wd_default_caption_text_align', 'center' ),

		/**
		 * Item default text vertical alignement
		 *
		 * @since 1.0.0
		 */
		'caption_v_align'                        => apply_filters( 'wd_default_caption_v_align', 'middle' ),


		/* Release */
		'release_index'                          => false,
		'release_display'                        => wolf_get_release_option( 'display', 'grid' ),
		'release_metro_pattern'                  => 'pattern-1',
		'release_hover_effect'                   => apply_filters( 'default_hover_effect', 'default' ),
		'release_category_filter'                => wolf_get_release_option( 'category_filter', false ),
		'release_category_filter_text_alignment' => 'center',
		'release_module'                         => 'grid',
		'release_thumbnail_size'                 => 'square',
		'release_custom_thumbnail_size'          => '',
		'release_layout'                         => 'standard',
		'release_alternate_thumbnail_position'   => '',
		'release_add_buy_links'                  => false,
		'release_meta'                           => '',
		'band_include'                           => '',
		'band_exclude'                           => '',
		'label_include'                          => '',
		'label_exclude'                          => '',
		'genre_include'                          => '',
		'genre_exclude'                          => '',
		'release_category_link_id'               => '',
		'release_do_redirect_url'                => false,


		/* Additional styles */
		'context'                                => 'archive',
		'hide_class'                             => '',
		'inline_style'                           => '',
		'el_class'                               => '',
		'el_id'                                  => '',
		'css'                                    => '',
	);
}

/**
 * Output posts
 *
 * @param  [array] $atts an array of options to display different post types.
 * @return void
 */
function wd_output_posts( $atts ) {

	/* Retrieve all VC shortcode attributes and/or set default values */
	$atts = wp_parse_args(
		$atts,
		wd_get_default_post_atts()
	);

	/**
	 * Post module attributes filtered
	 *
	 * @since 1.0.0
	 */
	$atts = apply_filters( 'wd_post_module_atts', $atts );

	$clean_atts  = array_filter(
		$atts,
		function( $var ) {
			return ( $var );
		}
	); // clean empty atts for json params.
	$json_params = wp_json_encode( $clean_atts );

	extract( $atts ); // phpcs:ignore

	$post_type = 'release';

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
	$category_filter = ( isset( ${$post_type . '_category_filter'} ) ) ? wd_attr_bool( ${$post_type . '_category_filter'} ) : false;

	// if ( $pagination && 'none' !== $pagination && -1 !== $posts_per_page && 'post' !== $post_type ) {
	// $category_filter = false;
	// }

	$thumbnail_size        = ( isset( ${$post_type . '_thumbnail_size'} ) ) ? ${$post_type . '_thumbnail_size'} : 'standard';
	$custom_thumbnail_size = ( isset( ${$post_type . '_custom_thumbnail_size'} ) ) ? ${$post_type . '_custom_thumbnail_size'} : '';
	$custom_thumbnail_size = apply_filters( 'wd_post_module_custom_thumbnail_size', $custom_thumbnail_size, $display, $atts );

	$is_index = ( isset( ${$post_type . '_index'} ) ) ? wd_attr_bool( ${$post_type . '_index'} ) : false;

	$inline_style  = wd_sanitize_css_field( $inline_style ); // sanitize user CSS input.
	$inline_style .= wd_shortcode_custom_style( $css ); // add VC CSS from custom class.

	// Start writing container class.
	$class  = $el_class;
	$class .= " $hide_class clearfix items wvc-element wolf-core-element entry-grid-loading";
	$class .= ' ' . $post_type . '-items';
	$class .= ' ' . $post_type . 's';
	$class .= " caption-text-align-$caption_text_alignment";
	$class .= " caption-valign-$caption_v_align";
	$class .= " grid-padding-$grid_padding";

	$class .= " display-$display";
	$class .= " $post_type-display-$display";

	if ( $item_animation && 'none' !== $item_animation ) {
		$class .= ' has-entrance-animation';
		$class .= ' items-entrance-animation-' . $item_animation;
	}


	$class .= " module-$module";
	$class .= " $post_type-module-$module";

	$class .= " layout-$layout";
	$class .= " $post_type-layout-$layout";


	$is_list = in_array( $display, array( 'list', 'minimal-list', 'list_minimal', 'small-list', 'text-background' ), true );

	if ( $is_list ) {
		$class .= ' list';
	}

	// Disable pagination & filter for carousel module.
	if ( 'carousel' === $module ) {
		$pagination      = 'none';
		$category_filter = null;
	}

	if ( $category_filter ) {
		$class .= ' filtered-content';
	} else {
		$class .= ' no-filtered-content';
	}

	// Query args.
	if ( isset( $_GET['wpage'] ) && isset( $_GET['index'] ) && sanitize_key( $_GET['index'] ) === $id ) {
		$paged = absint( $_GET['wpage'] );
	}

	if ( ! $paged ) {
		/* Fixed in  4.8 ? */
		$page_var = ( is_front_page() ) ? 'page' : 'paged';
		$paged    = ( get_query_var( $page_var ) ) ? get_query_var( $page_var ) : 1;
	}

	if ( $offset ) {

		// offset is ignored if posts per page is -1, so we use the default ppp setting insead.
		if ( -1 === $posts_per_page ) {
			$posts_per_page = get_option( 'posts_per_page' );
		}

		$offset = $offset + ( ( $paged - 1 ) * $posts_per_page );
	}

	if ( $category_filter ) {
		$pagination = 'none';
	}


	// Set default args.
	$args = array(
		'post_type'           => $post_type,
		'post_status'         => array( 'publish' ), // published post only.
		'posts_per_page'      => $posts_per_page,
		'paged'               => $paged,
		'post__in'            => array(),
		'post__not_in'        => array(),
	);


	if ( $offset ) {
		$args['offset']              = $offset;
	}

	// Include.
	if ( $include_ids ) {
		$args['post__in'] = wd_clean_list( $include_ids );

		if ( ! $orderby ) {
			$args['orderby'] = 'post__in';
		}
	}

	// Exclude.
	$exclude_ids_array   = array();
	$exclude_ids_array[] = wd_get_the_id(); // exclude current post, obviously or the internet will explode.

	if ( $exclude_ids ) {
		$exclude_ids_array = array_merge( $exclude_ids_array, wd_clean_list( $exclude_ids ) );
	}

	$exclude_ids_array = array_unique( $exclude_ids_array );

	$args['post__not_in'] = $exclude_ids_array;

	// Include Band.
	if ( $band_include ) {
		$args['band'] = wd_clean_list( $band_include );
	}

	// Exclude Band.
	if ( $band_exclude ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'band',
				'terms'    => wd_clean_list( $band_exclude ),
				'field'    => 'slug',
				'operator' => 'NOT IN',
			),
		);
	}

	// Include Label.
	if ( $label_include ) {
		$args['label'] = wd_clean_list( $label_include );
	}

	// Exclude Label.
	if ( $label_exclude ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'label',
				'terms'    => wd_clean_list( $label_exclude ),
				'field'    => 'slug',
				'operator' => 'NOT IN',
			),
		);
	}

	// Include Label.
	if ( $genre_include ) {
		$args['genre'] = wd_clean_list( $genre_include );
	}

	// Exclude genre.
	if ( $genre_exclude ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'release_genre',
				'terms'    => wd_clean_list( $genre_exclude ),
				'field'    => 'slug',
				'operator' => 'NOT IN',
			),
		);
	}

	$args['meta_key'] = '_thumbnail_id'; // force post with thumbnail.

	if ( 'featured' === $release_meta ) {

		$args['meta_query'] = array(
			array(
				'key'     => '_post_release_meta',
				'value'   => 'featured',
				'compare' => '=',
			),
		);

	} elseif ( 'upcoming' === $release_meta ) {

		$args['meta_query'] = array(
			array(
				'key'     => '_post_release_meta',
				'value'   => 'upcoming',
				'compare' => '=',
			),
		);
	}

	$class .= " hover-effect-$release_hover_effect";
	$class .= " release-hover-effect-$release_hover_effect";

	// Custom Order.
	if ( $orderby ) {

		$args['orderby'] = $orderby;

	} elseif ( ! isset( $args['orderby'] ) && function_exists( 'initCPTO' ) ) { // post type order plugin.

		$cpto_options = get_option( 'cpto_options' );

		if ( empty( $cpto_options['autosort'] ) ) {
			$args['orderby'] = 'menu_order';
			$args['order']   = 'ASC';
		}
	}

	if ( $order ) {
		$args['order'] = $order;
	}

	// Get main WP query in a variable.
	if ( $is_index ) { // is index page.

		global $wp_query;
		$query = $wp_query;
		// $query->set( 'posts_per_page', $posts_per_page );

	}


	/* The query */
	$query = new WP_Query( apply_filters( 'wd_post_module_main_query_args', $args, $atts ) );


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


		$tag = ( $is_list ) ? 'ul' : 'div';


		// Container open tag.
		echo '<' . esc_attr( $tag ) . ' id="' . esc_attr( $id ) . '" data-post-type="' . esc_attr( $post_type ) . '" data-params="' . esc_js( $json_params ) . '" class="' . wd_sanitize_html_classes( $class ) . '"';

		if ( wd_esc_style_attr( $inline_style ) ) {
			echo ' style="' . wd_esc_style_attr( $inline_style ) . '" ';
		}

		echo ' data-scroll data-scroll-css-progress';

		echo apply_filters( 'wd_post_module_additional_params', '' );

		echo '>';
		echo "\n";

		$i = 0;

		if ( ( 0 !== absint( $posts_per_page ) % 2 ) && ( 1 !== absint( $paged ) ) ) {
			$i = 1;
		}

		while ( $query->have_posts() ) {

			$i++;

			$query->the_post();
			$post_id = get_the_ID();


			set_query_var( 'module_atts', $atts );

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

						'index'                     => $i,
						'post_id'                   => $post_id,

						'display'                   => $display,
						'layout'                    => $layout,

						'overlay_color'             => $overlay_color,
						'overlay_custom_color'      => $overlay_custom_color,
						'overlay_opacity'           => $overlay_opacity,
						'overlay_text_color'        => $overlay_text_color,
						'overlay_text_custom_color' => $overlay_text_custom_color,
						'thumbnail_size'            => $thumbnail_size,
						'custom_thumbnail_size'     => $custom_thumbnail_size,

						'release_alternate_thumbnail_position' => $release_alternate_thumbnail_position,
						'release_add_buy_links'     => $release_add_buy_links,
						'release_do_redirect_url'   => $release_do_redirect_url,
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

		echo '</' . esc_attr( $tag ) . '><!--.release-items-->';

		/**
		 * After post module hook
		 *
		 * @since 1.0.0
		 */
		do_action( 'wd_after_post_module', $atts );

	}

}
add_action( 'wolf_discography_posts', 'wd_output_posts' );