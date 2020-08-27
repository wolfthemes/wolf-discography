<?php
/**
 * Discography register post type
 *
 * @author WolfThemes
 * @category Core
 * @package WolfDiscography/Admin
 * @version 1.5.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$admin_skin = get_user_option('admin_color');

if ( $admin_skin == 'light' ) {

	$icon_url = WD_URI . '/assets/img/admin/vynil-dark.png';
} else {

	$icon_url = WD_URI . '/assets/img/admin/vynil.png';
}

$labels = array(
	'name' => esc_html__( 'Releases', 'wolf-discography' ),
	'singular_name' => esc_html__( 'Release', 'wolf-discography' ),
	'add_new' => esc_html__( 'Add New', 'wolf-discography' ),
	'add_new_item' => esc_html__( 'Add New Release', 'wolf-discography' ),
	'all_items'  =>  esc_html__( 'All Releases', 'wolf-discography' ),
	'edit_item' => esc_html__( 'Edit Release', 'wolf-discography' ),
	'new_item' => esc_html__( 'New Release', 'wolf-discography' ),
	'view_item' => esc_html__( 'View Release', 'wolf-discography' ),
	'search_items' => esc_html__( 'Search Releases', 'wolf-discography' ),
	'not_found' => esc_html__( 'No releases found', 'wolf-discography' ),
	'not_found_in_trash' => esc_html__( 'No releases found in Trash', 'wolf-discography' ),
	'parent_item_colon' => '',
	'menu_name' => esc_html__( 'Releases', 'wolf-discography' ),
);

$args = array(

	'labels' => $labels,
	'public' => true,
	'publicly_queryable' => true,
	'show_ui' => true,
	'show_in_menu' => true,
	'query_var' => false,
	'rewrite' => array( 'slug' => 'release' ),
	'capability_type' => 'post',
	'has_archive' => false,
	'hierarchical' => false,
	'menu_position' => 5,
	'taxonomies' => array(),
	'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'comments' ),
	'exclude_from_search' => false,
	'menu_icon' => $icon_url
);

register_post_type( 'release', $args );