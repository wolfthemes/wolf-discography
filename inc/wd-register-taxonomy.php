<?php
/**
 * Discography register taxonomy
 *
 * @author WolfThemes
 * @category Core
 * @package WolfDiscography/Admin
 * @version 1.5.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$labels = array(
	'name' => esc_html__( 'Artists', 'wolf-discography' ),
	'singular_name' => esc_html__( 'Artist', 'wolf-discography' ),
	'search_items' => esc_html__( 'Search Artists', 'wolf-discography' ),
	'popular_items' => esc_html__( 'Popular Artists', 'wolf-discography' ),
	'all_items' => esc_html__( 'All Artists', 'wolf-discography' ),
	'parent_item' => esc_html__( 'Parent Artist', 'wolf-discography' ),
	'parent_item_colon' => esc_html__( 'Parent Artist:', 'wolf-discography' ),
	'edit_item' => esc_html__( 'Edit Artist', 'wolf-discography' ),
	'update_item' => esc_html__( 'Update Artist', 'wolf-discography' ),
	'add_new_item' => esc_html__( 'Add New Artist', 'wolf-discography' ),
	'new_item_name' => esc_html__( 'New Artist', 'wolf-discography' ),
	'separate_items_with_commas' => esc_html__( 'Separate artists with commas', 'wolf-discography' ),
	'add_or_remove_items' => esc_html__( 'Add or remove artists', 'wolf-discography' ),
	'choose_from_most_used' => esc_html__( 'Choose from the most used artists', 'wolf-discography' ),
	'not_found' => esc_html__( 'No artists found', 'wolf-discography' ),
	'menu_name' => esc_html__( 'Artists', 'wolf-discography' ),
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
	'name' => esc_html__( 'Labels', 'wolf-discography' ),
	'singular_name' => esc_html__( 'Label', 'wolf-discography' ),
	'search_items' => esc_html__( 'Search Labels', 'wolf-discography' ),
	'popular_items' => esc_html__( 'Popular Labels', 'wolf-discography' ),
	'all_items' => esc_html__( 'All Labels', 'wolf-discography' ),
	'parent_item' => esc_html__( 'Parent Label', 'wolf-discography' ),
	'parent_item_colon' => esc_html__( 'Parent Label:', 'wolf-discography' ),
	'edit_item' => esc_html__( 'Edit Label', 'wolf-discography' ),
	'update_item' => esc_html__( 'Update Label', 'wolf-discography' ),
	'add_new_item' => esc_html__( 'Add New Label', 'wolf-discography' ),
	'new_item_name' => esc_html__( 'New Label', 'wolf-discography' ),
	'separate_items_with_commas' => esc_html__( 'Separate labels with commas', 'wolf-discography' ),
	'add_or_remove_items' => esc_html__( 'Add or remove labels', 'wolf-discography' ),
	'choose_from_most_used' => esc_html__( 'Choose from the most used labels', 'wolf-discography' ),
	'menu_name' => esc_html__( 'Labels', 'wolf-discography' ),
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
	'name' => esc_html__( 'Genres', 'wolf-discography' ),
	'singular_name' => esc_html__( 'Genre', 'wolf-discography' ),
	'search_items' => esc_html__( 'Search Genres', 'wolf-discography' ),
	'popular_items' => esc_html__( 'Popular Genres', 'wolf-discography' ),
	'all_items' => esc_html__( 'All Genres', 'wolf-discography' ),
	'parent_item' => esc_html__( 'Parent Genre', 'wolf-discography' ),
	'parent_item_colon' => esc_html__( 'Parent Genre:', 'wolf-discography' ),
	'edit_item' => esc_html__( 'Edit Genre', 'wolf-discography' ),
	'update_item' => esc_html__( 'Update Genre', 'wolf-discography' ),
	'add_new_item' => esc_html__( 'Add New Genre', 'wolf-discography' ),
	'new_item_name' => esc_html__( 'New Genre', 'wolf-discography' ),
	'separate_items_with_commas' => esc_html__( 'Separate genres with commas', 'wolf-discography' ),
	'add_or_remove_items' => esc_html__( 'Add or remove genres', 'wolf-discography' ),
	'choose_from_most_used' => esc_html__( 'Choose from the most used genres', 'wolf-discography' ),
	'menu_name' => esc_html__( 'Genres', 'wolf-discography' ),
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