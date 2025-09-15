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
use WolfDiscography\API\RestAPI;

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

		$this->init_rest_api();
	}

	/**
	 * Initialize REST API
	 */
	private function init_rest_api() {
		// Only initialize REST API when actually needed
		if ( ! $this->should_load_rest_api() ) {
			return;
		}

		if ( class_exists( 'WolfDiscography\API\RestAPI' ) ) {
			new \WolfDiscography\API\RestAPI();
		}
	}

	/**
	 * Check if REST API should be loaded
	 */
	private function should_load_rest_api() {
		// Don't load in admin unless it's an AJAX request
		if ( is_admin() && ! wp_doing_ajax() ) {
			return false;
		}

		// Don't load during cron jobs
		if ( wp_doing_cron() ) {
			return false;
		}

		// Only load if REST API is available
		if ( ! function_exists( 'rest_get_url_prefix' ) ) {
			return false;
		}

		// Load on REST requests or frontend
		return true;
	}

	/**
	 * Output posts
	 *
	 * @param  [array] $atts an array of options to display different post types.
	 * @return void
	 */
	public function output_posts( $atts ) {

		// 1. Process attributes
		$atts = wp_parse_args( $atts, $this->attribute_processor->get_all_defaults() );
		$atts = apply_filters( 'wd_post_module_atts', $atts );

		debug( 'Processed Attributes', $atts );

		// 2. Build components
		$json_params     = $this->html_renderer->build_json_params( $atts );
		$container_attrs = $this->html_renderer->build_container_attributes( $atts, $json_params );
		$query           = $this->query_builder->build_query( $atts );

		// 3. Check if category filter should be shown
		$show_category_filter = $this->should_show_category_filter( $atts );

		// 4. Hooks and rendering
		do_action( 'wd_before_post_module', $atts, $query );

		if ( $query->have_posts() ) {
			$this->render_posts_output( $query, $atts, $container_attrs, $show_category_filter );
		}

		do_action( 'wd_after_post_module', $atts );
	}

	/**
	 * Render the complete posts output
	 *
	 * @param \WP_Query $query WordPress query object
	 * @param array     $atts Processed attributes
	 * @param array     $container_attrs Container attributes
	 * @param bool      $show_category_filter Whether to show category filter
	 * @return void
	 */
	private function render_posts_output( $query, $atts, $container_attrs, $show_category_filter ) {

		// Category filter setup
		if ( $show_category_filter ) {
			set_query_var( 'filter_args', array() );
			// Category filter template part can be added here
		}

		// Container opening
		$this->html_renderer->render_container_opening( $container_attrs );

		// Posts loop
		$this->render_posts_loop( $query, $atts );

		// Container closing
		$this->html_renderer->render_container_closing( $container_attrs['tag'] );
	}

	/**
	 * Render the posts loop
	 *
	 * @param \WP_Query $query WordPress query object
	 * @param array     $atts Processed attributes
	 * @return void
	 */
	private function render_posts_loop( $query, $atts ) {
		$i              = 0;
		$posts_per_page = $atts['posts_per_page'] ?? 100;
		$paged          = $atts['paged'] ?? 1;

		// Handle odd posts per page on non-first pages
		if ( ( 0 !== absint( $posts_per_page ) % 2 ) && ( 1 !== absint( $paged ) ) ) {
			$i = 1;
		}

		while ( $query->have_posts() ) {
			++$i;
			$query->the_post();
			$post_id = get_the_ID();

			// Set query vars for template compatibility
			set_query_var( 'wd_module_atts', $atts );
			set_query_var( 'template_args', $this->build_template_args( $atts, $i, $post_id ) );

			// Render individual post template
			$this->render_single_post_template( $atts );
		}

		do_action( 'wd_post_module_end', $atts );
	}

	/**
	 * Render single post template
	 *
	 * @param array $atts Processed attributes
	 * @return void
	 */
	private function render_single_post_template( $atts ) {
		$post_type = $this->cpt_slug;
		$display   = $atts[ $post_type . '_display' ] ?? 'grid';

		$template_name = apply_filters( 'wd_post_template_part_name', $display, $atts );
		wolf_discography_get_template_part( 'content', $template_name );
	}

	/**
	 * Build template arguments for rendering
	 *
	 * @param array $atts Processed attributes
	 * @param int   $index Current item index
	 * @param int   $post_id Current post ID
	 * @return array Template arguments
	 */
	private function build_template_args( $atts, $index, $post_id ) {
		$post_type = $this->cpt_slug;

		// Get display values with filters applied
		$layout = apply_filters(
			'wd_post_module_layout',
			$atts[ $post_type . '_layout' ] ?? 'standard',
			$atts
		);

		$display = apply_filters(
			'wd_post_module_display',
			$atts[ $post_type . '_display' ] ?? 'grid',
			$atts
		);

		$module = apply_filters(
			'wd_post_module_module',
			$atts[ $post_type . '_module' ] ?? 'grid',
			$atts
		);

		// Handle thumbnail size
		$thumbnail_size        = $atts[ $post_type . '_thumbnail_size' ] ?? 'standard';
		$custom_thumbnail_size = apply_filters(
			'wd_post_module_custom_thumbnail_size',
			$atts[ $post_type . '_custom_thumbnail_size' ] ?? '',
			$display,
			$atts
		);

		// Build template args
		$template_args = array(
			'index'                                => $index,
			'post_id'                              => $post_id,
			'display'                              => $display,
			'layout'                               => $layout,
			'module'                               => $module,

			// Style attributes
			'overlay_color'                        => $atts['overlay_color'] ?? '',
			'overlay_custom_color'                 => $atts['overlay_custom_color'] ?? '',
			'overlay_opacity'                      => $atts['overlay_opacity'] ?? '',
			'overlay_text_color'                   => $atts['overlay_text_color'] ?? '',
			'overlay_text_custom_color'            => $atts['overlay_text_custom_color'] ?? '',

			// Image attributes
			'thumbnail_size'                       => $thumbnail_size,
			'custom_thumbnail_size'                => $custom_thumbnail_size,

			// Release specific attributes
			'release_alternate_thumbnail_position' => $atts['release_alternate_thumbnail_position'] ?? '',
			'release_add_buy_links'                => $atts['release_add_buy_links'] ?? false,
			'release_do_redirect_url'              => $atts['release_do_redirect_url'] ?? false,
		);

		return apply_filters( 'post_template_args', $template_args, $atts );
	}

	/**
	 * Check if category filter should be enabled
	 *
	 * @param array $atts Processed attributes
	 * @return bool True if category filter is enabled
	 */
	private function should_show_category_filter( $atts ) {
		$post_type       = $this->cpt_slug;
		$module          = $atts[ $post_type . '_module' ] ?? 'grid';
		$category_filter = $atts[ $post_type . '_category_filter' ] ?? false;

		// Disable filter for carousel module
		if ( 'carousel' === $module ) {
			return false;
		}

		return Helpers::attr_bool( $category_filter );
	}
}