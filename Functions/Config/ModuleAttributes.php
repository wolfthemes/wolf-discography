<?php
/**
 * Module Attributes Configuration
 *
 * @package WolfDiscography
 * @subpackage PageBuilders\Config
 * @since 2.0.0
 */

namespace WolfDiscography\Config;

defined( 'ABSPATH' ) || exit;

/**
 * Module Parameters Class
 */
class ModuleAttributes {
	/**
	 * Get release index parameters
	 *
	 * @return array
	 */
	public static function get_attribute_registry(): array {

		return array(
			// Core WordPress query attributes
			'query'    => array(
				'post_type'                => array(
					'default'     => 'release',
					'type'        => 'string',
					'description' => 'Post type to query',
				),
				'posts_per_page'           => array(
					'default'     => 100,
					'type'        => 'int',
					'aliases'     => array( 'postsPerPage', 'per_page', 'count' ),
					'min'         => -1,
					'max'         => 500,
					'description' => 'Number of posts to retrieve (-1 for all)',
				),
				'paged'                    => array(
					'default'     => null,
					'type'        => 'int',
					'aliases'     => array( 'page', 'currentPage' ),
					'min'         => 1,
					'description' => 'Page number for pagination',
				),
				'orderby'                  => array(
					'default'     => '',
					'type'        => 'string',
					'enum'        => array( '', 'date', 'title', 'menu_order', 'rand', 'post__in', 'release_date' ),
					'description' => 'Field to order posts by',
				),
				'order'                    => array(
					'default'     => '',
					'type'        => 'string',
					'enum'        => array( '', 'ASC', 'DESC' ),
					'description' => 'Sort order direction',
				),
				'include_ids'              => array(
					'default'     => '',
					'type'        => 'csv',
					'aliases'     => array( 'includeIds', 'post__in', 'ids' ),
					'description' => 'Comma-separated list of post IDs to include',
				),
				'exclude_ids'              => array(
					'default'     => '',
					'type'        => 'csv',
					'aliases'     => array( 'excludeIds', 'post__not_in' ),
					'description' => 'Comma-separated list of post IDs to exclude',
				),
				'offset'                   => array(
					'default'     => 0,
					'type'        => 'int',
					'min'         => 0,
					'description' => 'Number of posts to skip',
				),
				'band_include'             => array(
					'default'     => '',
					'type'        => 'csv',
					'aliases'     => array( 'bandInclude', 'bands', 'band' ),
					'description' => 'Include specific bands (slugs or names)',
				),
				'band_exclude'             => array(
					'default'     => '',
					'type'        => 'csv',
					'aliases'     => array( 'bandExclude' ),
					'description' => 'Exclude specific bands',
				),
				'label_include'            => array(
					'default'     => '',
					'type'        => 'csv',
					'aliases'     => array( 'labelInclude', 'labels', 'label' ),
					'description' => 'Include specific labels',
				),
				'label_exclude'            => array(
					'default'     => '',
					'type'        => 'csv',
					'aliases'     => array( 'labelExclude' ),
					'description' => 'Exclude specific labels',
				),
				'genre_include'            => array(
					'default'     => '',
					'type'        => 'csv',
					'aliases'     => array( 'genreInclude', 'genres', 'genre' ),
					'description' => 'Include specific genres',
				),
				'genre_exclude'            => array(
					'default'     => '',
					'type'        => 'csv',
					'aliases'     => array( 'genreExclude' ),
					'description' => 'Exclude specific genres',
				),
				'release_meta'             => array(
					'default'     => '',
					'type'        => 'string',
					'aliases'     => array( 'releaseMeta', 'meta' ),
					'enum'        => array( '', 'featured', 'upcoming' ),
					'description' => 'Filter by release meta status',
				),
				'release_category_link_id' => array(
					'default'     => '',
					'type'        => 'string',
					'description' => 'Release category link ID',
				),
				'release_do_redirect_url'  => array(
					'default'     => false,
					'type'        => 'bool',
					'description' => 'Enable redirect to release URL',
				),
			),

			// Display attributes (affect HTML structure and layout)
			'display'  => array(
				'columns'                              => array(
					'default'     => 3,
					'type'        => 'int',
					'min'         => 1,
					'max'         => 6,
					'description' => 'Number of columns for grid display',
				),
				'grid_padding'                         => array(
					'default'     => wolf_get_release_option( 'post_grid_padding', 'yes' ),
					'type'        => 'bool',
					'aliases'     => array( 'gridPadding', 'padding' ),
					'description' => 'Add padding between grid items',
				),
				'item_animation'                       => array(
					'default'     => wolf_get_release_option( 'post_item_animation' ),
					'type'        => 'string',
					'aliases'     => array( 'itemAnimation', 'animation' ),
					'description' => 'Animation for items',
				),
				'release_index'                        => array(
					'default'     => false,
					'type'        => 'bool',
					'description' => 'Is this an index page',
				),
				'release_display'                      => array(
					'default'     => wolf_get_release_option( 'display', 'grid' ),
					'type'        => 'string',
					'aliases'     => array( 'display', 'layout_type', 'view' ),
					'enum'        => array( 'grid', 'list' ),
					'description' => 'Display layout type',
				),
				'release_metro_pattern'                => array(
					'default'     => 'pattern-1',
					'type'        => 'string',
					'description' => 'Metro layout pattern',
				),
				'release_module'                       => array(
					'default'     => 'grid',
					'type'        => 'string',
					'aliases'     => array( 'module' ),
					'enum'        => array( 'grid', 'carousel' ),
					'description' => 'Display module type',
				),
				'release_thumbnail_size'               => array(
					'default'     => 'square',
					'type'        => 'string',
					'aliases'     => array( 'thumbnailSize', 'thumbnail_size', 'image_size' ),
					'description' => 'Featured image size',
				),
				'release_custom_thumbnail_size'        => array(
					'default'     => '',
					'type'        => 'string',
					'aliases'     => array( 'customThumbnailSize', 'custom_image_size' ),
					'description' => 'Custom image size (WxH)',
				),
				'release_layout'                       => array(
					'default'     => 'standard',
					'type'        => 'string',
					'aliases'     => array( 'layout' ),
					'enum'        => array( 'standard', 'overlay' ),
					'description' => 'Layout variation within display type',
				),
				'release_alternate_thumbnail_position' => array(
					'default'     => '',
					'type'        => 'string',
					'description' => 'Alternate thumbnail position for list layouts',
				),
				'release_add_buy_links'                => array(
					'default'     => false,
					'type'        => 'bool',
					'description' => 'Add buy links to releases',
				),
			),
			// Behavior attributes (affect JavaScript/interactions)
			'behavior' => array(
				'release_category_filter'                => array(
					'default'     => wolf_get_release_option( 'category_filter', false ),
					'type'        => 'bool',
					'aliases'     => array( 'categoryFilter', 'filter', 'filtering' ),
					'description' => 'Enable category filtering',
				),
				'release_category_filter_text_alignment' => array(
					'default'     => 'center',
					'type'        => 'string',
					'enum'        => array( 'left', 'center', 'right' ),
					'description' => 'Category filter text alignment',
				),
			),

			// Style attributes (affect CSS) - using your existing filters
			'style'    => array(
				'overlay_color'             => array(
					'default'     => apply_filters( 'wd_default_item_overlay_color', 'black' ),
					'type'        => 'string',
					'aliases'     => array( 'overlayColor' ),
					'description' => 'Overlay background color',
				),
				'overlay_custom_color'      => array(
					'default'     => apply_filters( 'wd_default_item_overlay_custom_color', '' ),
					'type'        => 'string',
					'description' => 'Custom overlay color',
				),
				'overlay_text_color'        => array(
					'default'     => apply_filters( 'wd_default_item_overlay_text_color', 'white' ),
					'type'        => 'string',
					'description' => 'Overlay text color',
				),
				'overlay_text_custom_color' => array(
					'default'     => '',
					'type'        => 'string',
					'description' => 'Custom overlay text color',
				),
				'overlay_opacity'           => array(
					'default'     => apply_filters( 'wd_default_item_overlay_opacity', 44 ),
					'type'        => 'int',
					'aliases'     => array( 'overlayOpacity' ),
					'min'         => 0,
					'max'         => 100,
					'description' => 'Overlay opacity percentage',
				),
				'caption_text_alignment'    => array(
					'default'     => apply_filters( 'wd_default_caption_text_align', 'center' ),
					'type'        => 'string',
					'aliases'     => array( 'captionTextAlignment', 'textAlign' ),
					'enum'        => array( 'left', 'center', 'right' ),
					'description' => 'Text alignment for captions',
				),
				'caption_v_align'           => array(
					'default'     => apply_filters( 'wd_default_caption_v_align', 'middle' ),
					'type'        => 'string',
					'enum'        => array( 'top', 'middle', 'bottom' ),
					'description' => 'Vertical alignment for captions',
				),
				'release_hover_effect'      => array(
					'default'     => apply_filters( 'release_default_hover_effect', 'default' ),
					'type'        => 'string',
					'aliases'     => array( 'hoverEffect', 'hover_effect' ),
					'description' => 'Hover effect for items',
				),
			),

			// Output attributes (affect rendering method)
			'output'   => array(
				'context'         => array(
					'default'     => 'archive',
					'type'        => 'string',
					'description' => 'Display context',
				),
				'hide_class'      => array(
					'default'     => '',
					'type'        => 'string',
					'description' => 'CSS classes to hide elements',
				),
				'inline_style'    => array(
					'default'     => '',
					'type'        => 'string',
					'description' => 'Inline CSS styles',
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
				'css'             => array(
					'default'     => '',
					'type'        => 'string',
					'description' => 'Custom CSS from page builder',
				),
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
			),
		);
	}
}
