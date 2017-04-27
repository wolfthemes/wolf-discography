<?php
/**
 * %NAME% register metaboxes
 *
 * @author %AUTHOR%
 * @category Core
 * @package %PACKAGENAME%/Admin
 * @version %VERSION%
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$metabox = array(
	'Release Details' => array(
		'title' => esc_html__( 'Release Details', '%TEXTDOMAIN%' ),
		'page' => array( 'release' ),
		'metafields' => array(


			array(
				'label'	=> esc_html__( 'Title', '%TEXTDOMAIN%' ),
				'id'	=> '_wolf_release_title',
				'type'	=> 'text',
			),

			array(
				'label'	=> esc_html__( 'Release date', '%TEXTDOMAIN%' ),
				'id'	=> '_wolf_release_date',
				'type'	=> 'datepicker',
			),

			array(
				'label'	=> esc_html__( 'Catalog Number', '%TEXTDOMAIN%' ),
				'id'	=> '_wolf_release_catalog_number',
				'type'	=> 'text',
			),

			array(
				'label'	=> esc_html__( 'Type', '%TEXTDOMAIN%' ),
				'id'	=> '_wolf_release_type',
				'desc'   =>esc_html__( 'You can choose to not display the format in the plugin setting.' ),
				'type'	=> 'select',
				'choices' => array(
					esc_html__( 'CD', '%TEXTDOMAIN%' ),
					esc_html__( 'Digital Download', '%TEXTDOMAIN%' ),
					esc_html__( 'DVD', '%TEXTDOMAIN%' ),
					esc_html__( 'Vinyl', '%TEXTDOMAIN%' ),
					esc_html__( 'Tape', '%TEXTDOMAIN%' ),
				),
			),

			array(
				'label'	=> esc_html__( 'iTunes', '%TEXTDOMAIN%' ),
				'id'	=> '_wolf_release_itunes',
				'type'	=> 'url',
			),

			array(
				'label'	=> esc_html__( 'Amazon', '%TEXTDOMAIN%' ),
				'id'	=> '_wolf_release_amazon',
				'type'	=> 'url',
			),

			array(
				'label'	=> esc_html__( 'Buy (any link where the release can be purchased)', '%TEXTDOMAIN%' ),
				'id'	=> '_wolf_release_buy',
				'type'	=> 'url',
			),

			array(
				'label'	=> esc_html__( 'Free Download link', '%TEXTDOMAIN%' ),
				'id'	=> '_wolf_release_free',
				'type'	=> 'url',
			),

			array(
				'label'	=> esc_html__( 'Tracklist', '%TEXTDOMAIN%' ),
				'id'	=> '_wolf_release_tracklist',
				'type'	=> 'repeatable',
			),
		)
	),
);

new WD_Admin_Metabox( $metabox );