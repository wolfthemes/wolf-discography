<?php
/**
 * REST API Handler for Wolf Discography
 *
 * @package WolfDiscography
 * @subpackage API
 * @since 2.0.0
 */

namespace WolfDiscography\API;

use WolfDiscography\Core\AttributeProcessor;
use WolfDiscography\Core\QueryBuilder;

defined( 'ABSPATH' ) || exit;

class RestAPI {

	/**
	 * Constructor - register REST routes
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register REST API routes
	 */
	public function register_routes() {

		register_rest_route(
			'wolf-discography/v1',
			'/releases',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_releases' ),
				'permission_callback' => '__return_true',
				'args'                => $this->get_endpoint_args(),
			)
		);

		register_rest_route(
			'wolf-discography/v1',
			'/releases/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_single_release' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'id' => array(
						'description' => 'Unique identifier for the release',
						'type'        => 'integer',
					),
				),
			)
		);
	}

	/**
	 * Get endpoint arguments based on AttributeProcessor
	 */
	private function get_endpoint_args() {
		return array(
			'posts_per_page' => array(
				'default'     => 10,
				'type'        => 'integer',
				'minimum'     => 1,
				'maximum'     => 100,
				'description' => 'Number of releases to retrieve',
			),
			'page'           => array(
				'default'     => 1,
				'type'        => 'integer',
				'minimum'     => 1,
				'description' => 'Page number for pagination',
			),
			'orderby'        => array(
				'default'     => 'date',
				'type'        => 'string',
				'enum'        => array( 'date', 'title', 'menu_order', 'rand' ),
				'description' => 'Field to order releases by',
			),
			'order'          => array(
				'default'     => 'desc',
				'type'        => 'string',
				'enum'        => array( 'asc', 'desc' ),
				'description' => 'Order direction',
			),
			'band'           => array(
				'default'     => '',
				'type'        => 'string',
				'description' => 'Filter by band slug',
			),
			'genre'          => array(
				'default'     => '',
				'type'        => 'string',
				'description' => 'Filter by genre slug',
			),
			'featured'       => array(
				'default'     => false,
				'type'        => 'boolean',
				'description' => 'Show only featured releases',
			),
			'format'         => array(
				'default'     => 'standard',
				'type'        => 'string',
				'enum'        => array( 'standard', 'minimal', 'detailed' ),
				'description' => 'Response format detail level',
			),
		);
	}

	/**
	 * Get releases endpoint
	 *
	 * @param \WP_REST_Request $request Request object
	 * @return \WP_REST_Response|WP_Error Response object
	 */
	public function get_releases( $request ) {
		try {
			// Convert REST params to internal attributes format
			$atts = $this->convert_rest_params_to_atts( $request->get_params() );

			// Use your existing components
			$processor     = new AttributeProcessor();
			$query_builder = new QueryBuilder( 'release' );

			// Process attributes
			$processed_atts = $processor->process_attributes( $atts, 'rest_api' );

			// Build query
			$query = $query_builder->build_query( $processed_atts );

			// Get format preference
			$format = $request->get_param( 'format' ) ?? 'standard';

			// Extract data from query
			$posts_data = $this->extract_posts_data( $query, $format );

			// Build response
			$response_data = array(
				'releases'   => $posts_data['posts'],
				'pagination' => array(
					'total'        => $query->found_posts,
					'total_pages'  => $query->max_num_pages,
					'current_page' => max( 1, $processed_atts['query']['paged'] ?? 1 ),
					'per_page'     => $processed_atts['query']['posts_per_page'] ?? 10,
				),
				'query_info' => array(
					'execution_time' => $posts_data['execution_time'],
					'cache_hit'      => false, // TODO: implement caching
				),
			);

			return new \WP_REST_Response( $response_data, 200 );

		} catch ( Exception $e ) {
			return new \WP_Error( 'rest_releases_error', $e->getMessage(), array( 'status' => 500 ) );
		}
	}

	/**
	 * Get single release endpoint
	 *
	 * @param \WP_REST_Request $request Request object
	 * @return \WP_REST_Response|WP_Error Response object
	 */
	public function get_single_release( $request ) {
		$id   = $request->get_param( 'id' );
		$post = get_post( $id );

		if ( ! $post || $post->post_type !== 'release' ) {
			return new \WP_Error( 'rest_release_invalid_id', 'Invalid release ID', array( 'status' => 404 ) );
		}

		$release_data = $this->format_single_release( $post );

		return new \WP_REST_Response(
			array(
				'release' => $release_data,
			),
			200
		);
	}

	/**
	 * Convert REST parameters to internal attributes format
	 *
	 * @param array $params REST request parameters
	 * @return array Internal attributes format
	 */
	private function convert_rest_params_to_atts( $params ) {
		$atts = array();

		// Map REST params to internal attribute names
		$param_mapping = array(
			'posts_per_page' => 'posts_per_page',
			'page'           => 'paged',
			'orderby'        => 'orderby',
			'order'          => 'order',
			'band'           => 'band_include',
			'genre'          => 'genre_include',
		);

		foreach ( $param_mapping as $rest_param => $internal_attr ) {
			if ( isset( $params[ $rest_param ] ) ) {
				$atts[ $internal_attr ] = $params[ $rest_param ];
			}
		}

		// Handle featured parameter
		if ( ! empty( $params['featured'] ) ) {
			$atts['release_meta'] = 'featured';
		}

		return $atts;
	}

	/**
	 * Extract posts data from query
	 *
	 * @param \WP_Query $query WordPress query object
	 * @param string    $format Response format
	 * @return array Posts data
	 */
	private function extract_posts_data( $query, $format ) {
		$start_time = microtime( true );
		$posts      = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id = get_the_ID();

				$posts[] = $this->format_release_data( $post_id, $format );
			}
		}

		wp_reset_postdata();

		return array(
			'posts'          => $posts,
			'execution_time' => round( ( microtime( true ) - $start_time ), 4 ) . 's',
		);
	}

	/**
	 * Format release data for API response
	 *
	 * @param int    $post_id Post ID
	 * @param string $format Response format
	 * @return array Formatted release data
	 */
	private function format_release_data( $post_id, $format ) {
		$post = get_post( $post_id );

		$base_data = array(
			'id'        => $post_id,
			'title'     => get_the_title( $post_id ),
			'slug'      => $post->post_name,
			'permalink' => get_permalink( $post_id ),
		);

		switch ( $format ) {
			case 'minimal':
				return $base_data;

			case 'detailed':
				return array_merge(
					$base_data,
					array(
						'content'        => get_the_content( null, false, $post_id ),
						'excerpt'        => get_the_excerpt( $post_id ),
						'featured_image' => $this->get_featured_image_data( $post_id ),
						'release_date'   => get_post_meta( $post_id, '_release_date', true ),
						'bands'          => $this->get_post_terms( $post_id, 'band' ),
						'genres'         => $this->get_post_terms( $post_id, 'release_genre' ),
						'labels'         => $this->get_post_terms( $post_id, 'label' ),
						'buy_links'      => get_post_meta( $post_id, '_release_buy_links', true ),
						'meta'           => get_post_meta( $post_id, '_post_release_meta', true ),
						'custom_fields'  => $this->get_custom_fields( $post_id ),
					)
				);

			default: // standard
				return array_merge(
					$base_data,
					array(
						'excerpt'        => get_the_excerpt( $post_id ),
						'featured_image' => $this->get_featured_image_data( $post_id ),
						'release_date'   => get_post_meta( $post_id, '_release_date', true ),
						'bands'          => $this->get_post_terms( $post_id, 'band' ),
						'genres'         => $this->get_post_terms( $post_id, 'release_genre' ),
						'labels'         => $this->get_post_terms( $post_id, 'label' ),
						'meta'           => get_post_meta( $post_id, '_post_release_meta', true ),
					)
				);
		}
	}

	/**
	 * Format single release for detailed endpoint
	 */
	private function format_single_release( $post ) {
		return $this->format_release_data( $post->ID, 'detailed' );
	}

	/**
	 * Get featured image data
	 */
	private function get_featured_image_data( $post_id ) {
		$thumbnail_id = get_post_thumbnail_id( $post_id );

		if ( ! $thumbnail_id ) {
			return null;
		}

		return array(
			'id'    => $thumbnail_id,
			'url'   => get_the_post_thumbnail_url( $post_id, 'full' ),
			'sizes' => array(
				'thumbnail' => get_the_post_thumbnail_url( $post_id, 'thumbnail' ),
				'medium'    => get_the_post_thumbnail_url( $post_id, 'medium' ),
				'large'     => get_the_post_thumbnail_url( $post_id, 'large' ),
			),
			'alt'   => get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ),
		);
	}

	/**
	 * Get post terms for a taxonomy
	 */
	private function get_post_terms( $post_id, $taxonomy ) {
		$terms = get_the_terms( $post_id, $taxonomy );

		if ( ! $terms || is_wp_error( $terms ) ) {
			return array();
		}

		return array_map(
			function ( $term ) {
				return array(
					'id'   => $term->term_id,
					'name' => $term->name,
					'slug' => $term->slug,
				);
			},
			$terms
		);
	}

	/**
	 * Get custom fields (excluding private meta)
	 */
	private function get_custom_fields( $post_id ) {
		$all_meta      = get_post_meta( $post_id );
		$custom_fields = array();

		foreach ( $all_meta as $key => $value ) {
			// Skip private meta fields (starting with _)
			if ( strpos( $key, '_' ) !== 0 ) {
				$custom_fields[ $key ] = $value[0] ?? $value;
			}
		}

		return $custom_fields;
	}
}