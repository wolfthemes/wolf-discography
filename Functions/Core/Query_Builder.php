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

class Query_Builder {

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

	/**
	 * Build WP_Query from processed attributes
	 *
	 * @param array $atts Processed attributes array
	 * @return \WP_Query WordPress query object
	 */
	public function build_query( $atts ) {

		// Handle pagination
		$paged = $this->get_current_page( $atts );

		// Handle offset calculations
		$offset = $this->calculate_offset( $atts, $paged );

		// Build base query args
		$args = $this->get_base_query_args( $atts, $paged, $offset );

		// Add post inclusion/exclusion
		$args = $this->apply_post_filters( $args, $atts );

		// Add taxonomy queries
		$args = $this->apply_taxonomy_filters( $args, $atts );

		// Add meta queries
		$args = $this->apply_meta_filters( $args, $atts );

		// Apply custom ordering
		$args = $this->apply_ordering( $args, $atts );

		// Handle special cases
		if ( $this->is_index_page( $atts ) ) {
			global $wp_query;
			return $wp_query;
		}

		// Create and return query
		return new \WP_Query( apply_filters( 'wd_post_module_main_query_args', $args, $atts ) );
	}

	/**
	 * Get base query arguments
	 *
	 * @param array $atts Processed attributes
	 * @param int   $paged Current page
	 * @param int   $offset Calculated offset
	 * @return array Base query arguments
	 */
	private function get_base_query_args( $atts, $paged, $offset ) {
		$args = array(
			'post_type'      => $this->post_type,
			'post_status'    => array( 'publish' ),
			'posts_per_page' => $atts['posts_per_page'] ?? 100,
			'paged'          => $paged,
			'post__in'       => array(),
			'post__not_in'   => array(),
			'meta_key'       => '_thumbnail_id', // force post with thumbnail
		);

		if ( $offset > 0 ) {
			$args['offset'] = $offset;
		}

		return $args;
	}

	/**
	 * Apply post inclusion and exclusion filters
	 *
	 * @param array $args Query arguments
	 * @param array $atts Processed attributes
	 * @return array Modified query arguments
	 */
	private function apply_post_filters( $args, $atts ) {
		// Handle post inclusion
		$include_ids = $atts['include_ids'] ?? '';
		if ( $include_ids ) {
			$args['post__in'] = Helpers::clean_list( $include_ids );

			// If including specific posts, order by inclusion order
			if ( empty( $atts['orderby'] ) ) {
				$args['orderby'] = 'post__in';
			}
		}

		// Handle post exclusion
		$args['post__not_in'] = $this->build_exclude_list( $atts );

		return $args;
	}

	/**
	 * Apply taxonomy filters (bands, labels, genres)
	 *
	 * @param array $args Query arguments
	 * @param array $atts Processed attributes
	 * @return array Modified query arguments
	 */
	private function apply_taxonomy_filters( $args, $atts ) {
		$tax_query = array();

		// Band filters
		$tax_query = $this->add_taxonomy_filter( $tax_query, 'band', $atts['band_include'] ?? '', $atts['band_exclude'] ?? '' );

		// Label filters
		$tax_query = $this->add_taxonomy_filter( $tax_query, 'label', $atts['label_include'] ?? '', $atts['label_exclude'] ?? '' );

		// Genre filters
		$tax_query = $this->add_taxonomy_filter( $tax_query, 'release_genre', $atts['genre_include'] ?? '', $atts['genre_exclude'] ?? '' );

		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query;
		}

		return $args;
	}

	/**
	 * Add taxonomy filter to tax_query array
	 *
	 * @param array  $tax_query Existing tax query
	 * @param string $taxonomy Taxonomy name
	 * @param string $include Terms to include
	 * @param string $exclude Terms to exclude
	 * @return array Modified tax query
	 */
	private function add_taxonomy_filter( $tax_query, $taxonomy, $include, $exclude ) {
		// Include terms
		if ( $include ) {
			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => Helpers::clean_list( $include ),
			);
		}

		// Exclude terms
		if ( $exclude ) {
			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => Helpers::clean_list( $exclude ),
				'operator' => 'NOT IN',
			);
		}

		return $tax_query;
	}

	/**
	 * Apply meta filters (featured, upcoming, etc.)
	 *
	 * @param array $args Query arguments
	 * @param array $atts Processed attributes
	 * @return array Modified query arguments
	 */
	private function apply_meta_filters( $args, $atts ) {
		$release_meta = $atts['release_meta'] ?? '';

		if ( in_array( $release_meta, array( 'featured', 'upcoming' ), true ) ) {
			$args['meta_query'] = array(
				array(
					'key'     => '_post_release_meta',
					'value'   => $release_meta,
					'compare' => '=',
				),
			);
		}

		return $args;
	}

	/**
	 * Apply custom ordering
	 *
	 * @param array $args Query arguments
	 * @param array $atts Processed attributes
	 * @return array Modified query arguments
	 */
	private function apply_ordering( $args, $atts ) {
		$orderby = $atts['orderby'] ?? '';
		$order   = $atts['order'] ?? '';

		// Custom orderby
		if ( $orderby ) {
			$args['orderby'] = $orderby;
		} elseif ( ! isset( $args['orderby'] ) && function_exists( 'initCPTO' ) ) {
			// Post type order plugin support
			$cpto_options = get_option( 'cpto_options' );

			if ( empty( $cpto_options['autosort'] ) ) {
				$args['orderby'] = 'menu_order';
				$args['order']   = 'ASC';
			}
		}

		// Custom order
		if ( $order ) {
			$args['order'] = $order;
		}

		return $args;
	}

	/**
	 * Get current page number
	 *
	 * @param array $atts Processed attributes
	 * @return int Current page number
	 */
	private function get_current_page( $atts ) {
		$paged = $atts['paged'] ?? null;
		$el_id = $atts['el_id'] ?? '';

		// Check for custom pagination
		if ( isset( $_GET['wpage'] ) && isset( $_GET['index'] ) && sanitize_key( $_GET['index'] ) === $el_id ) {
			return absint( $_GET['wpage'] );
		}

		if ( ! $paged ) {
			$page_var = ( is_front_page() ) ? 'page' : 'paged';
			return ( get_query_var( $page_var ) ) ? get_query_var( $page_var ) : 1;
		}

		return (int) $paged;
	}

	/**
	 * Calculate offset for pagination
	 *
	 * @param array $atts Processed attributes
	 * @param int   $paged Current page
	 * @return int Calculated offset
	 */
	private function calculate_offset( $atts, $paged ) {
		$offset         = $atts['offset'] ?? 0;
		$posts_per_page = $atts['posts_per_page'] ?? 100;

		if ( $offset > 0 ) {
			// Offset is ignored if posts per page is -1, so we use the default ppp setting instead
			if ( -1 === $posts_per_page ) {
				$posts_per_page = get_option( 'posts_per_page' );
			}

			return $offset + ( ( $paged - 1 ) * $posts_per_page );
		}

		return $offset;
	}

	/**
	 * Build list of posts to exclude
	 *
	 * @param array $atts Processed attributes
	 * @return array Array of post IDs to exclude
	 */
	private function build_exclude_list( $atts ) {
		$exclude_ids       = $atts['exclude_ids'] ?? '';
		$exclude_ids_array = array();

		// Always exclude current post
		$exclude_ids_array[] = Helpers::get_the_id();

		if ( $exclude_ids ) {
			$exclude_ids_array = array_merge( $exclude_ids_array, Helpers::clean_list( $exclude_ids ) );
		}

		return array_unique( $exclude_ids_array );
	}

	/**
	 * Check if this is an index page
	 *
	 * @param array $atts Processed attributes
	 * @return bool True if index page
	 */
	private function is_index_page( $atts ) {
		return ! empty( $atts['release_index'] );
	}

	/**
	 * Get query arguments without executing query (for debugging/inspection)
	 *
	 * @param array $atts Processed attributes
	 * @return array Query arguments array
	 */
	public function get_query_args( $atts ) {
		$paged  = $this->get_current_page( $atts );
		$offset = $this->calculate_offset( $atts, $paged );

		$args = $this->get_base_query_args( $atts, $paged, $offset );
		$args = $this->apply_post_filters( $args, $atts );
		$args = $this->apply_taxonomy_filters( $args, $atts );
		$args = $this->apply_meta_filters( $args, $atts );
		$args = $this->apply_ordering( $args, $atts );

		return apply_filters( 'wd_post_module_main_query_args', $args, $atts );
	}
}
