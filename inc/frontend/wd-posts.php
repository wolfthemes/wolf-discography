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

		/* Common attributes */
		'grid_padding'                           => wolf_get_release_option( 'post_grid_padding', 'yes' ),
		'item_animation'                         => wolf_get_release_option( 'post_item_animation' ),
		'columns'                                => 3,
		'include_ids'                            => '',
		'exclude_ids'                            => '',
		'offset'                                 => 0,

		/* Release */
		'release_index'                          => false,
		'release_display'                        => wolf_get_release_option( 'release_display', 'grid' ),
		'release_metro_pattern'                  => 'pattern-1',
		'release_hover_effect'                   => apply_filters( 'release_default_hover_effect', 'default' ),
		'release_category_filter'                => wolf_get_release_option( 'release_category_filter', false ),
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
	);
}