<?php
/**
 * WPBakery Page Builder functions
 *
 * @author WolfThemes
 * @category Core
 * @package WolfDiscography/Core
 * @version 1.5.1
 * @since 1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Covert raw params to VC format params
 *
 * @param array $params The parameters array to convert.
 * @return array
 */
function wd_convert_params_to_vc( $params ) {

	$vc_params  = array();
	$properties = $params['properties'];
	$params     = $params['params'];

	/* Properties */
	$vc_params['name']        = $properties['name'];
	$vc_params['description'] = $properties['description'];
	$vc_params['base']        = $properties['vc_base'];
	$vc_params['category']    = $properties['vc_category'];
	$vc_params['icon']        = $properties['icon'];

	if ( isset( $properties['vc_as_parent'] ) ) {
		$vc_params['as_parent'] = $properties['vc_as_parent'];
	}

	if ( isset( $properties['vc_as_child'] ) ) {
		$vc_params['as_child'] = $properties['vc_as_child'];
	}

	$vc_params['params'] = array();

	/* Other */
	$vc_params['js_view'] = ( isset( $params['js_view'] ) ) ? $params['js_view'] : '';

	$i = 0;

	foreach ( $params as $p ) {

		if ( isset( $p['page_builder'] ) && 'elementor' === $p['page_builder'] ) {
			continue;
		}

		if ( ! isset( $p['type'] ) ) {
			continue;
		}

		$type = $p['type'];

		$vc_params['params'][ $i ]['type'] = $type;

		$vc_params['params'][ $i ]['param_name'] = ( isset( $p['param_name'] ) ) ? $p['param_name'] : '';

		if ( isset( $p['label'] ) ) {
			$vc_params['params'][ $i ]['heading'] = $p['label'];
		}

		if ( isset( $p['default'] ) ) {
			$vc_params['params'][ $i ]['value'] = $p['default'];
		}

		if ( 'text' === $type ) {

			$vc_params['params'][ $i ]['type'] = 'textfield';

		} elseif ( 'textarea' === $type ) {

			$vc_params['params'][ $i ]['type'] = 'textarea';

		} elseif ( 'select' === $type ) {

			$vc_params['params'][ $i ]['type']  = 'dropdown';
			$vc_params['params'][ $i ]['value'] = array_flip( $p['options'] );

			if ( isset( $p['default'] ) ) {
				$vc_params['params'][ $i ]['std'] = $p['default'];
			}
		} elseif ( 'checkbox' === $type ) {

			$label_on     = ( isset( $p['label_on'] ) ) ? $p['label_on'] : esc_html__( 'Yes', 'wolf-core' );
			$return_value = ( isset( $p['return_value'] ) ) ? $p['return_value'] : 'yes';

			$vc_params['params'][ $i ]['value'] = array(
				$label_on => $return_value,
			);

		} elseif ( 'font_family' === $type ) {

			$vc_params['params'][ $i ]['type']  = 'dropdown';
			$vc_params['params'][ $i ]['value'] = array_flip( wd_get_google_fonts_options() );

		} elseif ( 'link' === $type ) {

			$vc_params['params'][ $i ]['type'] = 'vc_link';

		} elseif ( 'icon' === $type ) {

			$library_options = array();
			$all_libraries   = array_merge( wd_get_icon_libraires(), wd_get_vc_default_icon_libraries() );
			// $all_libraries   = wd_get_icon_libraires();

			foreach ( $all_libraries as $library ) {
				$library_options[ $library['properties']['label'] ] = $library['properties']['name'];
			}

			$vc_params['params'][ $i ]['type']        = 'dropdown';
			$vc_params['params'][ $i ]['heading']     = esc_html__( 'Icon library', 'wolf-core' );
			$vc_params['params'][ $i ]['param_name']  = 'icon_type';
			$vc_params['params'][ $i ]['admin_label'] = true;
			$vc_params['params'][ $i ]['description'] = esc_html__( 'Select icon library.', 'wolf-core' );
			$vc_params['params'][ $i ]['std']         = apply_filters( 'wd_default_icon_font', 'dripicons' );
			$vc_params['params'][ $i ]['value']       = $library_options;
			$vc_params['params'][ $i ]['dependency']  = array(
				'element' => 'add_icon',
				'value'   => 'yes',
			);

			foreach ( $all_libraries as $library ) {
				$i++;

				$vc_params['params'][ $i ] = array(
					'type'        => 'iconpicker',
					'heading'     => $library['properties']['label'],
					'param_name'  => 'icon_' . $library['properties']['name'],
					'value'       => $library['properties']['labelIcon'],
					'settings'    => array(
						'type'         => $library['properties']['name'],
						'emptyIcon'    => false,
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => $library['properties']['name'],
					),
					'description' => esc_html__( 'Select icon from library.', 'wolf-core' ),
				);

				// debug( $vc_params['params'][ $i ] );
			}
		} elseif ( 'colorpicker' === $type ) {

			$vc_params['params'][ $i ]['type'] = 'colorpicker';

		} elseif ( 'slider' === $type ) {

			$vc_params['params'][ $i ]['type'] = 'wd_numeric_slider';
			$vc_params['params'][ $i ]['min']  = ( isset( $p['min'] ) ) ? $p['min'] : 0;
			$vc_params['params'][ $i ]['max']  = ( isset( $p['max'] ) ) ? $p['max'] : 100;
			$vc_params['params'][ $i ]['step'] = ( isset( $p['step'] ) ) ? $p['step'] : 1;

		} elseif ( 'video' === $type ) {

			$vc_params['params'][ $i ]['type'] = 'wd_video_url';

		} elseif ( 'image' === $type ) {

			$vc_params['params'][ $i ]['type'] = 'attach_image';

		} elseif ( 'images' === $type ) {

			$vc_params['params'][ $i ]['type'] = 'attach_images';

		} elseif ( 'hover_animation' === $type ) {
			$vc_params['params'][ $i ]['type'] = 'dropdown';
			$vc_params['params'][ $i ]['value'] = array_flip( wd_get_hover_animations() );
		}

		if ( isset( $p['vc_dependency'] ) ) {
			foreach ( $p['vc_dependency'] as $k => $v ) {
				$vc_params['params'][ $i ]['dependency']['element'] = $k;
				$vc_params['params'][ $i ]['dependency']['value']   = $v;
			}
		} elseif ( isset( $p['condition'] ) ) {
			foreach ( $p['condition'] as $k => $v ) {
				$vc_params['params'][ $i ]['dependency']['element'] = $k;
				$vc_params['params'][ $i ]['dependency']['value']   = $v;
			}
		}

		if ( isset( $p['group'] ) ) {
			$vc_params['params'][ $i ]['group'] = $p['group'];
		}

		if ( isset( $p['weight'] ) ) {
			$vc_params['params'][ $i ]['weight'] = $p['weight'];
		}

		if ( isset( $p['save_always'] ) ) {
			$vc_params['params'][ $i ]['save_always'] = $p['save_always'];
		}

		if ( isset( $p['param_holder_class'] ) ) {
			$vc_params['params'][ $i ]['param_holder_class'] = $p['param_holder_class'];
		}

		if ( isset( $p['admin_label'] ) ) {
			$vc_params['params'][ $i ]['admin_label'] = $p['admin_label'];
		}

		$i++;
	}

	// die( debug( $vc_params ) );

	return $vc_params;
}

/**
 * Filtering template path for each shortcode
 *
 * Using vc_set_shortcodes_templates_dir will prevent the theme from having a VC template directory.
 * We filter the template path for each shortcode so we can have our shortcode templates in the plugin AND in the theme
 */
function wd_vc_hook_template_dir() {

	$template_dir   = WD()->plugin_path() . '/vc_templates';

	$slug = 'release-index';

	$vc_filename = wd_locate_shortcode_template( 'vc_' . sanitize_title_with_dashes( $slug ) . '.php' );


	if ( is_file( $vc_filename ) ) {

		vc_map_update(
			'vc_' . $slug,
			array(
				'html_template' => $vc_filename,
			)
		);

	}
}
add_action( 'vc_after_init', 'wd_vc_hook_template_dir' );

/**
 * Locate a file and return the path for inclusion.
 *
 * Used to check if the file is in a theme folder of from the original plugin directory
 *
 * @param string $filename The file to include.
 * @return string
 */
function wd_locate_shortcode_template( $filename ) {

	$file = WD()->plugin_path() . '/' . WD()->vc_shortcode_template_path() . '/' . untrailingslashit( $filename );

	// Return what we found.
	return apply_filters( 'wd_locate_shortcode_template', $file );
}