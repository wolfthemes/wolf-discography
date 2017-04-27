<?php
/**
 * %NAME% register post type
 *
 * @author %AUTHOR%
 * @category Core
 * @package %PACKAGENAME%/Admin
 * @version %VERSION%
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
	'name' => esc_html__( 'Releases', '%TEXTDOMAIN%' ),
	'singular_name' => esc_html__( 'Release', '%TEXTDOMAIN%' ),
	'add_new' => esc_html__( 'Add New', '%TEXTDOMAIN%' ),
	'add_new_item' => esc_html__( 'Add New Release', '%TEXTDOMAIN%' ),
	'all_items'  =>  esc_html__( 'All Releases', '%TEXTDOMAIN%' ),
	'edit_item' => esc_html__( 'Edit Release', '%TEXTDOMAIN%' ),
	'new_item' => esc_html__( 'New Release', '%TEXTDOMAIN%' ),
	'view_item' => esc_html__( 'View Release', '%TEXTDOMAIN%' ),
	'search_items' => esc_html__( 'Search Releases', '%TEXTDOMAIN%' ),
	'not_found' => esc_html__( 'No releases found', '%TEXTDOMAIN%' ),
	'not_found_in_trash' => esc_html__( 'No releases found in Trash', '%TEXTDOMAIN%' ),
	'parent_item_colon' => '',
	'menu_name' => esc_html__( 'Releases', '%TEXTDOMAIN%' ),
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