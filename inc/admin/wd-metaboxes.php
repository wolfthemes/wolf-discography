<?php
/**
 * Discography register metaboxes
 *
 * @author WolfThemes
 * @category Core
 * @package WolfDiscography/Admin
 * @version 1.5.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$metabox = array(
	'Release Details' => array(
		'title' => esc_html__( 'Release Details', 'wolf-discography' ),
		'page' => array( 'release' ),
		'metafields' => array(


			array(
				'label'	=> esc_html__( 'Title', 'wolf-discography' ),
				'id'	=> '_wolf_release_title',
				'type'	=> 'text',
			),

			array(
				'label'	=> esc_html__( 'Release date', 'wolf-discography' ),
				'id'	=> '_wolf_release_date',
				'type'	=> 'datepicker',
			),

			array(
				'label'	=> esc_html__( 'Catalog Number', 'wolf-discography' ),
				'id'	=> '_wolf_release_catalog_number',
				'type'	=> 'text',
			),

			array(
				'label'	=> esc_html__( 'Type', 'wolf-discography' ),
				'id'	=> '_wolf_release_type',
				'desc'   =>esc_html__( 'You can choose to not display the format in the plugin setting.' ),
				'type'	=> 'select',
				'choices' => array(
					esc_html__( 'CD', 'wolf-discography' ),
					esc_html__( 'Digital Download', 'wolf-discography' ),
					esc_html__( 'DVD', 'wolf-discography' ),
					esc_html__( 'Vinyl', 'wolf-discography' ),
					esc_html__( 'Tape', 'wolf-discography' ),
				),
			),

			array(
				'label'	=> esc_html__( 'iTunes', 'wolf-discography' ),
				'id'	=> '_wolf_release_itunes',
				'type'	=> 'url',
			),

			array(
				'label'	=> esc_html__( 'YouTube Music', 'wolf-discography' ),
				'id'	=> '_wolf_release_google_play',
				'type'	=> 'url',
			),

			array(
				'label'	=> esc_html__( 'Amazon', 'wolf-discography' ),
				'id'	=> '_wolf_release_amazon',
				'type'	=> 'url',
			),

			array(
				'label'	=> esc_html__( 'Spotify', 'wolf-discography' ),
				'id'	=> '_wolf_release_spotify',
				'type'	=> 'url',
			),

			array(
				'label'	=> esc_html__( 'Buy (any link where the release can be purchased)', 'wolf-discography' ),
				'id'	=> '_wolf_release_buy',
				'type'	=> 'url',
			),

			array(
				'label'	=> esc_html__( 'Free Download link', 'wolf-discography' ),
				'id'	=> '_wolf_release_free',
				'type'	=> 'url',
			),

			'tracklist' => array(
				'label'	=> esc_html__( 'Tracklist', 'wolf-discography' ),
				'id'	=> '_wolf_release_tracklist',
				'type'	=> 'repeatable',
			),
		)
	),
);

new WD_Admin_Metabox( apply_filters( 'wolf_discography_metaboxes', $metabox ) );