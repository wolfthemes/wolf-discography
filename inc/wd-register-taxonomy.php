<?php
/**
 * %NAME% register taxonomy
 *
 * @author %AUTHOR%
 * @category Core
 * @package %PACKAGENAME%/Admin
 * @version %VERSION%
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$labels = array(
	'name' => esc_html__( 'Artists', '%TEXTDOMAIN%' ),
	'singular_name' => esc_html__( 'Artist', '%TEXTDOMAIN%' ),
	'search_items' => esc_html__( 'Search Artists', '%TEXTDOMAIN%' ),
	'popular_items' => esc_html__( 'Popular Artists', '%TEXTDOMAIN%' ),
	'all_items' => esc_html__( 'All Artists', '%TEXTDOMAIN%' ),
	'parent_item' => esc_html__( 'Parent Artist', '%TEXTDOMAIN%' ),
	'parent_item_colon' => esc_html__( 'Parent Artist:', '%TEXTDOMAIN%' ),
	'edit_item' => esc_html__( 'Edit Artist', '%TEXTDOMAIN%' ),
	'update_item' => esc_html__( 'Update Artist', '%TEXTDOMAIN%' ),
	'add_new_item' => esc_html__( 'Add New Artist', '%TEXTDOMAIN%' ),
	'new_item_name' => esc_html__( 'New Artist', '%TEXTDOMAIN%' ),
	'separate_items_with_commas' => esc_html__( 'Separate artists with commas', '%TEXTDOMAIN%' ),
	'add_or_remove_items' => esc_html__( 'Add or remove artists', '%TEXTDOMAIN%' ),
	'choose_from_most_used' => esc_html__( 'Choose from the most used artists', '%TEXTDOMAIN%' ),
	'not_found' => esc_html__( 'No artists found', '%TEXTDOMAIN%' ),
	'menu_name' => esc_html__( 'Artists', '%TEXTDOMAIN%' ),
);

$args = array(

	'labels' => $labels,
	'hierarchical' => false,
	'public' => true,
	'show_ui' => true,
	'query_var' => true,
	'update_count_callback' => '_update_post_term_count',
	'rewrite' => array( 'slug' => 'band', 'with_front' => false),
);

register_taxonomy( 'band', array( 'release' ), $args );

$labels = array(
	'name' => esc_html__( 'Labels', '%TEXTDOMAIN%' ),
	'singular_name' => esc_html__( 'Label', '%TEXTDOMAIN%' ),
	'search_items' => esc_html__( 'Search Labels', '%TEXTDOMAIN%' ),
	'popular_items' => esc_html__( 'Popular Labels', '%TEXTDOMAIN%' ),
	'all_items' => esc_html__( 'All Labels', '%TEXTDOMAIN%' ),
	'parent_item' => esc_html__( 'Parent Label', '%TEXTDOMAIN%' ),
	'parent_item_colon' => esc_html__( 'Parent Label:', '%TEXTDOMAIN%' ),
	'edit_item' => esc_html__( 'Edit Label', '%TEXTDOMAIN%' ),
	'update_item' => esc_html__( 'Update Label', '%TEXTDOMAIN%' ),
	'add_new_item' => esc_html__( 'Add New Label', '%TEXTDOMAIN%' ),
	'new_item_name' => esc_html__( 'New Label', '%TEXTDOMAIN%' ),
	'separate_items_with_commas' => esc_html__( 'Separate labels with commas', '%TEXTDOMAIN%' ),
	'add_or_remove_items' => esc_html__( 'Add or remove labels', '%TEXTDOMAIN%' ),
	'choose_from_most_used' => esc_html__( 'Choose from the most used labels', '%TEXTDOMAIN%' ),
	'menu_name' => esc_html__( 'Labels', '%TEXTDOMAIN%' ),
);

$args = array(

	'labels' => $labels,
	'hierarchical' => false,
	'public' => true,
	'show_ui' => true,
	'query_var' => true,
	'update_count_callback' => '_update_post_term_count',
	'rewrite' => array( 'slug' => 'label', 'with_front' => false),
);

register_taxonomy( 'label', array( 'release' ), $args );

$labels = array(
	'name' => esc_html__( 'Genres', '%TEXTDOMAIN%' ),
	'singular_name' => esc_html__( 'Genre', '%TEXTDOMAIN%' ),
	'search_items' => esc_html__( 'Search Genres', '%TEXTDOMAIN%' ),
	'popular_items' => esc_html__( 'Popular Genres', '%TEXTDOMAIN%' ),
	'all_items' => esc_html__( 'All Genres', '%TEXTDOMAIN%' ),
	'parent_item' => esc_html__( 'Parent Genre', '%TEXTDOMAIN%' ),
	'parent_item_colon' => esc_html__( 'Parent Genre:', '%TEXTDOMAIN%' ),
	'edit_item' => esc_html__( 'Edit Genre', '%TEXTDOMAIN%' ),
	'update_item' => esc_html__( 'Update Genre', '%TEXTDOMAIN%' ),
	'add_new_item' => esc_html__( 'Add New Genre', '%TEXTDOMAIN%' ),
	'new_item_name' => esc_html__( 'New Genre', '%TEXTDOMAIN%' ),
	'separate_items_with_commas' => esc_html__( 'Separate genres with commas', '%TEXTDOMAIN%' ),
	'add_or_remove_items' => esc_html__( 'Add or remove genres', '%TEXTDOMAIN%' ),
	'choose_from_most_used' => esc_html__( 'Choose from the most used genres', '%TEXTDOMAIN%' ),
	'menu_name' => esc_html__( 'Genres', '%TEXTDOMAIN%' ),
);

$args = array(

	'labels' => $labels,
	'hierarchical' => false,
	'public' => true,
	'show_ui' => true,
	'query_var' => true,
	'update_count_callback' => '_update_post_term_count',
	'rewrite' => array( 'slug' => 'genre', 'with_front' => false),
);

register_taxonomy( 'release_genre', array( 'release' ), $args );