<?php
/**
 * Hooks
 *
 * @package WolfDiscography
 * @subpackage Frontend
 * @since 2.0.0
 */

namespace Wolf_Discography\Frontend;

defined( 'ABSPATH' ) || exit;

class Helpers {
	/**
	 * Gets the ID of the post, even if it's not inside the loop.
	 *
	 * @uses WP_Query
	 * @uses get_queried_object()
	 * @extends get_the_ID()
	 * @see get_the_ID()
	 *
	 * @return int
	 */
	public static function get_the_id() {

		global $wp_query;

		$post_id = null;

		// Get post ID outside the loop.
		if ( is_object( $wp_query ) && isset( $wp_query->queried_object ) && isset( $wp_query->queried_object->ID ) ) {
			$post_id = $wp_query->queried_object->ID;
		} else {
			$post_id = get_the_ID();
		}

		return $post_id;
	}


	/**
	 * Sanitize CSS from user intpu
	 *
	 * @param string $style
	 * @return string
	 */
	public static function sanitize_css_field( $style ) {

		if ( '' === $style ) {
			return;
		}

		// remove double semicolon.
		$style = str_replace( array( ';;', '; ;' ), '', $style );

		if ( ';' !== substr( $style, -1 ) ) {
			$style = $style . ';'; // add end semicolon if missing.
		}

		return esc_attr( trim( self::clean_spaces( $style ) ) );
	}

	/**
	 * Remove all double spaces and line breaks
	 *
	 * This function is mainly used to clean up inline CSS
	 *
	 * @param string $css
	 * @return string
	 */
	public static function clean_spaces( $string, $hard = false ) {

		if ( $hard ) {
			return str_replace( ' ', '', $string );
		} else {
			return preg_replace( '/\s+/', ' ', $string );
		}
	}

	/**
	 * Get inline CSS from VC custom CSS class
	 *
	 * We use it to add inline style to row
	 *
	 * @param $param_value
	 * @param string $prefix
	 * @return string
	 */
	public static function shortcode_custom_style( $param_value ) {

		if ( preg_match( '/\s*\.([^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/', $param_value, $match ) ) {
			if ( isset( $match[2] ) ) {
				return self::clean_spaces( str_replace( '!important', '', $match[2] ), true ); // remove !important to allow CSS overwriting
			}
		}
	}

	/**
	 * Create a formatted sample of any text
	 *
	 * Remove HTML and shortcode, sanitize and shorten a string
	 *
	 * @param string $text
	 * @param int    $num_words
	 * @param string $more
	 * @return string
	 */
	public static function sample( $text = null, $num_words = 55, $more = '...' ) {
		$text = ( $text ) ? $text : get_the_excerpt();
		return wp_trim_words( strip_shortcodes( $text ), $num_words, $more );
	}

	/**
	 * Helper method to determine if an attribute is true or false.
	 *
	 * @param string|int|bool $var Attribute value.
	 * @return bool
	 */
	public static function attr_bool( $var ) {
		$falsey = array( 'false', '0', 'no', 'n', '', ' ' );
		return ( ! $var || in_array( strtolower( $var ), $falsey, true ) ) ? false : true;
	}

	/**
	 * sanitize_html_class works just fine for a single class
	 * Some times le wild <span class="blue hedgehog"> appears, which is when you need this function,
	 * to validate both blue and hedgehog,
	 * Because sanitize_html_class doesn't allow spaces.
	 *
	 * @uses sanitize_html_class
	 * @param (mixed: string/array) $class   "blue hedgehog goes shopping" or array("blue", "hedgehog", "goes", "shopping")
	 * @param (mixed)               $fallback Anything you want returned in case of a failure
	 * @return (mixed: string / $fallback )
	 */
	public static function sanitize_html_classes( $class, $fallback = null ) {

		// Explode it, if it's a string
		if ( is_string( $class ) ) {
			$class = explode( ' ', $class );
		}

		if ( is_array( $class ) && count( $class ) > 0 ) {
			$class = array_unique( array_map( 'sanitize_html_class', $class ) );
			return trim( implode( ' ', $class ) );
		} else {
			return trim( sanitize_html_class( $class, $fallback ) );
		}
	}

	/**
	 * Escape html style attribute
	 *
	 * @param string $style The style attribute to clean
	 * @return string
	 */
	public static function esc_style_attr( $style ) {

		if ( '' === $style || empty( $style ) ) {
			return;
		}

		if ( ';' !== substr( $style, -1 ) ) {
			$style = $style . ';'; // add end semicolon if missing.
		}

		// remove double semicolon.
		$style = str_replace( array( ';;', '; ;' ), '', $style );

		$style = ( ';' !== $style ) ? $style : '';

		return esc_attr( trim( self::clean_spaces( $style ) ) );
	}

	/**
	 * Convert predefined WBPakery color to hex
	 *
	 * @param string $color The color to convert using the extension colors preset array.
	 * @return void
	 */
	public static function convert_color_class_to_hex_value( $color, $custom_color ) {

		$hex_color = '';

		$colors = array(
			'blue'        => '#5472d2',
			'turquoise'   => '#00c1cf',
			'pink'        => '#fe6c61',
			'violet'      => '#8d6dc4',
			'peacoc'      => '#4cadc9',
			'chino'       => '#cec2ab',
			'mulled-wine' => '#50485b',
			'vista-blue'  => '#75d69c',
			'orange'      => '#f7be68',
			'sky'         => '#5aa1e3',
			'green'       => '#6dab3c',
			'juicy-pink'  => '#f4524d',
			'sandy-brown' => '#f79468',
			'purple'      => '#b97ebb',
			'black'       => '#2a2a2a',
			'grey'        => '#ebebeb',
			'white'       => '#ffffff',
		);

		$colors = self::get_shared_colors_hex();

		if ( 'custom' === $color ) {
			$hex_color = $custom_color;
		} else {
			$hex_color = isset( $colors[ $color ] ) ? $colors[ $color ] : '';
		}

		return $hex_color;
	}

	/**
	 * Get shared color hex value
	 */
	public static function get_shared_colors_hex() {

		$wd_shared_colors_hex = array(

			'black'       => '#000000',
			'lightergrey' => '#f7f7f7',
			'darkgrey'    => '#444444',
			'white'       => '#ffffff',
			'orange'      => '#F7BE68',
			'green'       => '#6DAB3C',
			'turquoise'   => '#49afcd',
			'violet'      => '#8D6DC4',
			'pink'        => '#FE6C61',
			'greyblue'    => '#49535a',
			'red'         => '#da4f49',
			'yellow'      => '#e6ae48',
			'blue'        => '#75D69C',
			'peacoc'      => '#4CADC9',
			'chino'       => '#CEC2AB',
			'mulled-wine' => '#50485B',
			'vista-blue'  => '#75D69C',
			'grey'        => '#EBEBEB',
			'sky'         => '#5AA1E3',
			'juicy-pink'  => '#F4524D',
			'sandy-brown' => '#F79468',
			'purple'      => '#B97EBB',
		);

		$wd_shared_colors_hex = apply_filters( 'wd_shared_colors_hex', $wd_shared_colors_hex );

		return $wd_shared_colors_hex;
	}

	/**
	 * Get dominant color from image
	 *
	 * @param int $attachment_id The attachment id.
	 */
	public static function get_image_dominant_color( $attachment_id ) {

		if ( ! $attachment_id || ! extension_loaded( 'gd' ) ) {
			return;
		}

		$metadata = wp_get_attachment_metadata( $attachment_id );

		if ( ! isset( $metadata['file'] ) ) {
			return 'transparent';
		}

		$upload_dir = wp_upload_dir();
		$filename   = $upload_dir['basedir'] . '/' . $metadata['file'];
		$ext        = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

		if ( 'jpg' === $ext || 'jpeg' === $ext ) {

			$image = imagecreatefromjpeg( $filename );

		} elseif ( 'png' === $ext ) {

			$image = imagecreatefrompng( $filename );

		} elseif ( 'gif' === $ext ) {

			$image = imagecreatefromgif( $filename );

		} else {
			return 'transparent';
		}

		$thumb = imagecreatetruecolor( 1, 1 );
		imagecopyresampled( $thumb, $image, 0, 0, 0, 0, 1, 1, imagesx( $image ), imagesy( $image ) );
		$main_color = dechex( imagecolorat( $thumb, 0, 0 ) );

		$main_color = ( 6 === strlen( $main_color ) ) ? '#' . $main_color : 'transparent';

		return $main_color;
	}

	/**
	 * Get color brightness to adjust font color
	 *
	 * Used to determine if a background is light enough to use a dark font
	 *
	 * @param string $hex
	 * @return string light|dark
	 */
	public static function get_color_tone( $hex, $index = 215 ) {

		// Sanitize the color
		$hex = sanitize_hex_color( $hex );

		// If sanitize_hex_color returns null, default to a fallback value (e.g., '#000000').
		if ( is_null( $hex ) ) {
			$hex = '#ffffff';
		}

		// Remove #
		$hex = str_replace( '#', '', $hex );

		// Convert hex to RGB
		$c_r = hexdec( substr( $hex, 0, 2 ) );
		$c_g = hexdec( substr( $hex, 2, 2 ) );
		$c_b = hexdec( substr( $hex, 4, 2 ) );

		// Calculate brightness
		$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

		// Return light or dark
		return ( $index < $brightness ) ? 'light' : 'dark';
	}

	/**
	 * Convert list to array
	 *
	 * @param string $list
	 * @return array
	 */
	public static function list_to_array( $list, $separator = ',' ) {
		return ( $list ) ? explode( ',', trim( self::clean_spaces( self::clean_list( $list ) ) ) ) : array();
	}

	/**
	 * Convert array of ids to list
	 *
	 * @param string $list
	 * @return array
	 */
	public static function array_to_list( $array, $separator = ',' ) {
		$list = '';

		if ( is_array( $array ) ) {
			$list = rtrim( implode( $separator, array_unique( $array ) ), $separator );
		}

		return self::clean_list( $list );
	}

	/**
	 * Clean a list
	 *
	 * Remove first and last comma of a list and remove spaces before and after separator
	 *
	 * @param string $list
	 * @return string $list
	 */
	public static function clean_list( $list, $separator = ',' ) {

		if ( ! empty( $list ) ) {
			$list = str_replace( array( $separator . ' ', ' ' . $separator ), $separator, $list );
			$list = ltrim( $list, $separator );
			$list = rtrim( $list, $separator );
		}

		return $list;
	}

	/**
	 * Sanitize color input
	 *
	 * @link https://github.com/redelivre/wp-divi/blob/master/includes/functions/sanitization.php
	 *
	 * @param string $color
	 * @return string $color
	 */
	public static function sanitize_color( $color ) {

		// Trim unneeded whitespace
		$color = str_replace( ' ', '', $color );
		// If this is hex color, validate and return it
		if ( 1 === preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
			return $color;
		}
		// If this is rgb, validate and return it
		elseif ( 'rgb(' === substr( $color, 0, 4 ) ) {
			sscanf( $color, 'rgb(%d,%d,%d)', $red, $green, $blue );
			if ( ( $red >= 0 && $red <= 255 ) &&
				( $green >= 0 && $green <= 255 ) &&
				( $blue >= 0 && $blue <= 255 )
				) {
				return "rgb({$red},{$green},{$blue})";
			}
		}
		// If this is rgba, validate and return it
		elseif ( 'rgba(' === substr( $color, 0, 5 ) ) {
			sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );
			if ( ( $red >= 0 && $red <= 255 ) &&
				( $green >= 0 && $green <= 255 ) &&
				( $blue >= 0 && $blue <= 255 ) &&
					$alpha >= 0 && $alpha <= 1
				) {
				return "rgba({$red},{$green},{$blue},{$alpha})";
			}
		} elseif ( 'transparent' === $color ) {
			return 'transparent';
		}
	}


	/**
	 * Sanitize string with wp_kses
	 *
	 * @param string $output The string to sanitize.
	 * @return sring $output
	 */
	public static function kses( $output ) {

		return wp_kses(
			$output,
			array(
				'div'        => array(
					'class'     => array(),
					'id'        => array(),
					'itemscope' => array(),
					'itemtype'  => array(),
				),
				'p'          => array(
					'class' => array(),
					'id'    => array(),
				),
				'ul'         => array(
					'class' => array(),
					'id'    => array(),
					'style' => array(),
				),
				'ol'         => array(
					'class' => array(),
					'id'    => array(),
					'style' => array(),
				),
				'li'         => array(
					'class' => array(),
					'id'    => array(),
				),
				'span'       => array(
					'class'        => array(),
					'id'           => array(),
					'data-post-id' => array(),
					'itemprop'     => array(),

				),
				'i'          => array(
					'class'       => array(),
					'id'          => array(),
					'aria-hidden' => array(),
				),
				'time'       => array(
					'class'    => array(),
					'datetime' => array(),
					'itemprop' => array(),
				),
				'blockquote' => array(
					'class' => array(),
					'id'    => array(),
				),
				'hr'         => array(
					'class' => array(),
					'id'    => array(),
				),
				'strong'     => array(
					'class' => array(),
					'id'    => array(),
				),
				'em'         => array(
					'class' => array(),
					'id'    => array(),
				),
				'br'         => array(),
				'img'        => array(
					'src'      => array(),
					'srcset'   => array(),
					'class'    => array(),
					'id'       => array(),
					'width'    => array(),
					'height'   => array(),
					'sizes'    => array(),
					'alt'      => array(),
					'title'    => array(),
					'data-src' => array(),
				),
				'a'          => array(
					'class'                  => array(),
					'id'                     => array(),
					'href'                   => array(),
					'data-fancybox'          => array(),
					'rel'                    => array(),
					'title'                  => array(),
					'target'                 => array(),
					'data-mega-menu-tagline' => array(),
					'itemprop'               => array(),
				),
				'h1'         => array(
					'class' => array(),
					'id'    => array(),
				),
				'h2'         => array(
					'class' => array(),
					'id'    => array(),
				),
				'h3'         => array(
					'class' => array(),
					'id'    => array(),
				),
				'h4'         => array(
					'class' => array(),
					'id'    => array(),
				),
				'h5'         => array(
					'class' => array(),
					'id'    => array(),
				),
				'h6'         => array(
					'class' => array(),
					'id'    => array(),
				),
				'ins'        => array(
					'class' => array(),
					'id'    => array(),
				),
				'del'        => array(
					'class' => array(),
					'id'    => array(),
				),
				'svg'        => array(
					'class' => array(),
					'id'    => array(),
				),
				'iframe'     => array(
					'class'           => array(),
					'id'              => array(),
					'src'             => array(),
					'width'           => array(),
					'height'          => array(),
					'style'           => array(),
					'allowfullscreen' => array(),
					'loading'         => array(),
					'referrerpolicy'  => array(),
				),
			)
		);
	}

	/**
	 * Check if elementor editor
	 */
	public static function is_elementor_editor() {

		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			return \Elementor\Plugin::$instance->preview->is_preview_mode();
		}
	}

	/**
	 * Check if we are on the WPB VC Frontend Editor
	 *
	 * @return bool
	 */
	public static function is_wpb_vc_frontend() {
		return function_exists( 'vc_is_inline' ) && vc_is_inline() ? true : false;
	}

	/**
	 * Get current page URL
	 */
	public static function get_current_url() {
		global $wp;
		return esc_url( home_url( add_query_arg( array(), $wp->request ) ) );
	}

	/**
	 * Check if we're on a discography page
	 *
	 * @return bool
	 */
	public static function is_discography_archives() {
		return is_page( Core::get_discography_page_id() ) ||
				is_post_type_archive( 'release' ) ||
				is_tax( array( 'band', 'label', 'release_genre' ) );
	}

	/**
	 * Check if we're on a discography page
	 *
	 * @return bool
	 */
	public static function is_discography() {
		return self::is_discography_archives() || is_singular( 'release' );
	}

	/**
	 * Check if elementor
	 */
	public static function is_elementor_page( $post_id = null ) {

		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			global $post;

			$post_id = ( $post_id ) ? $post_id : null;

			if ( ! $post_id && is_object( $post ) ) {
				$post_id = $post->ID;
			}

			if ( $post_id ) {
				return \Elementor\Plugin::$instance->documents->get( $post_id )->is_built_with_elementor();
			}
		}
	}

	/**
	 * Output resized post thumbnail
	 *
	 * @param string $size The wanted image size.
	 * @param string $class Optional image class.
	 * @return void
	 */
	public static function resized_thumbnail( $size = '150x150', $class = '', $image_id = null, $echo = true ) {

		$image_id = ( $image_id ) ? $image_id : get_post_thumbnail_id();

		$thumbnail = self::get_img_by_size(
			array(
				'attach_id'  => $image_id,
				'thumb_size' => $size,
				'class'      => $class . ' resized-thumbnail',
			)
		);

		if ( $echo && $thumbnail ) {
			echo self::kses( $thumbnail['thumbnail'] );
		}

		if ( $thumbnail ) {
			return self::kses( $thumbnail['thumbnail'] );
		}
	}

	/**
	 * Output resized post thumbnail
	 *
	 * @param string $size The wanted image size.
	 * @param string $class Optional image class.
	 * @return void
	 */
	public static function resized_thumbnail_url( $size = '150x150', $image_id = null, $echo = true ) {

		$image_id = ( $image_id ) ? $image_id : get_post_thumbnail_id();

		$thumbnail = self::get_img_by_size(
			array(
				'attach_id'  => $image_id,
				'thumb_size' => $size,
			)
		);

		if ( preg_match( '(src=["\'](.*?)["\'])', $thumbnail['thumbnail'], $match ) ) {
			if ( isset( $match[1] ) ) {
				if ( $echo ) {
					echo esc_url( $match[1] );
				}

				return esc_url( $match[1] );
			}
		}
	}


	/**
	 * From WPBPB
	 *
	 * @param  array $params the arrray for parameters.
	 * @return array|bool
	 * @since 4.2
	 * vc_filter: vc_wpb_getimagesize - to override output of this function
	 */
	public static function get_img_by_size( $params = array() ) {
		$params = array_merge(
			array(
				'post_id'    => null,
				'attach_id'  => null,
				'thumb_size' => 'thumbnail',
				'class'      => '',
			),
			$params
		);

		if ( ! $params['thumb_size'] ) {
			$params['thumb_size'] = 'thumbnail';
		}

		if ( ! $params['attach_id'] && ! $params['post_id'] ) {
			return false;
		}

		$post_id = $params['post_id'];

		$attach_id = $post_id ? get_post_thumbnail_id( $post_id ) : $params['attach_id'];
		/**
		 * Filters attach_id object
		 *
		 * @since 1.0.0
		 */
		$attach_id   = apply_filters( 'wd_object_id', $attach_id );
		$thumb_size  = $params['thumb_size'];
		$thumb_class = ( isset( $params['class'] ) && '' !== $params['class'] ) ? $params['class'] . ' ' : '';

		global $_wp_additional_image_sizes;
		$thumbnail = '';

		$sizes = array(
			'thumbnail',
			'thumb',
			'medium',
			'large',
			'full',
		);

		if ( is_string( $thumb_size ) && ( ( ! empty( $_wp_additional_image_sizes[ $thumb_size ] ) && is_array( $_wp_additional_image_sizes[ $thumb_size ] ) ) || in_array( $thumb_size, $sizes, true ) ) ) {
			$attributes = array( 'class' => $thumb_class . 'attachment-' . $thumb_size );
			$thumbnail  = wp_get_attachment_image( $attach_id, $thumb_size, false, $attributes );
		} elseif ( $attach_id ) {
			if ( is_string( $thumb_size ) ) {
				preg_match_all( '/\d+/', $thumb_size, $thumb_matches );
				if ( isset( $thumb_matches[0] ) ) {
					$thumb_size = array();
					$count      = count( $thumb_matches[0] );
					if ( $count > 1 ) {
						$thumb_size[] = $thumb_matches[0][0]; // width.
						$thumb_size[] = $thumb_matches[0][1]; // height.
					} elseif ( 1 === $count ) {
						$thumb_size[] = $thumb_matches[0][0]; // width.
						$thumb_size[] = $thumb_matches[0][0]; // height.
					} else {
						$thumb_size = false;
					}
				}
			}
			if ( is_array( $thumb_size ) ) {
				// Resize image to custom size.
				$p_img      = self::resize_image( $attach_id, null, $thumb_size[0], $thumb_size[1], true );
				$alt        = trim( wp_strip_all_tags( get_post_meta( $attach_id, '_wp_attachment_image_alt', true ) ) );
				$attachment = get_post( $attach_id );
				if ( ! empty( $attachment ) ) {
					$title = trim( wp_strip_all_tags( $attachment->post_title ) );

					if ( empty( $alt ) ) {
						$alt = trim( wp_strip_all_tags( $attachment->post_excerpt ) ); // If not, use the caption.
					}
					if ( empty( $alt ) ) {
						$alt = $title;
					}
					if ( $p_img ) {

						$attributes = self::stringify_attributes(
							array(
								'class'  => $thumb_class,
								'src'    => $p_img['url'],
								'width'  => $p_img['width'],
								'height' => $p_img['height'],
								'alt'    => $alt,
								'title'  => $title,
							)
						);

						$thumbnail = '<img ' . $attributes . ' />';
					}
				}
			}
		}

		$p_img_large = wp_get_attachment_image_src( $attach_id, 'large' );

		/**
		 * WPBakery filter
		 *
		 * @since 1.0.0
		 */
		return apply_filters(
			'wd_get_img_by_size', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
			array(
				'thumbnail'   => $thumbnail,
				'p_img_large' => $p_img_large,
			),
			$attach_id,
			$params
		);
	}

	/**
	 * Resize images dynamically using wp built in functions
	 * Victor Teixeira
	 *
	 * @param int    $attach_id attachment ID.
	 * @param string $img_url image URL.
	 * @param int    $width width.
	 * @param int    $height height.
	 * @param bool   $crop crop or not.
	 *
	 * @return array
	 */
	public static function resize_image( $attach_id, $img_url, $width, $height, $crop = false ) {
		// This is an attachment, so we have the ID.
		$image_src = array();
		if ( $attach_id ) {
			$image_src        = wp_get_attachment_image_src( $attach_id, 'full' );
			$actual_file_path = get_attached_file( $attach_id );
			// This is not an attachment, let's use the image url.
		} elseif ( $img_url ) {
			$file_path        = wp_parse_url( $img_url );
			$actual_file_path = rtrim( ABSPATH, '/' ) . $file_path['path'];
			$orig_size        = getimagesize( $actual_file_path );
			$image_src[0]     = $img_url;
			$image_src[1]     = $orig_size[0];
			$image_src[2]     = $orig_size[1];
		}
		if ( ! empty( $actual_file_path ) ) {
			$file_info = pathinfo( $actual_file_path );
			$extension = '.' . $file_info['extension'];

			// the image path without the extension.
			$no_ext_path = $file_info['dirname'] . '/' . $file_info['filename'];

			$cropped_img_path = $no_ext_path . '-' . $width . 'x' . $height . $extension;

			// checking if the file size is larger than the target size.
			// if it is smaller or the same size, stop right here and return.
			if ( $image_src[1] > $width || $image_src[2] > $height ) {

				// the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match).
				if ( file_exists( $cropped_img_path ) ) {
					$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
					$vt_image        = array(
						'url'    => $cropped_img_url,
						'width'  => $width,
						'height' => $height,
					);

					return $vt_image;
				}

				if ( ! $crop ) {
					// calculate the size proportionaly.
					$proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
					$resized_img_path  = $no_ext_path . '-' . $proportional_size[0] . 'x' . $proportional_size[1] . $extension;

					// checking if the file already exists.
					if ( file_exists( $resized_img_path ) ) {
						$resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );

						$vt_image = array(
							'url'    => $resized_img_url,
							'width'  => $proportional_size[0],
							'height' => $proportional_size[1],
						);

						return $vt_image;
					}
				}

				// no cache files - let's finally resize it.
				$img_editor = wp_get_image_editor( $actual_file_path );

				if ( is_wp_error( $img_editor ) || is_wp_error( $img_editor->resize( $width, $height, $crop ) ) ) {
					return array(
						'url'    => '',
						'width'  => '',
						'height' => '',
					);
				}

				$new_img_path = $img_editor->generate_filename();

				if ( is_wp_error( $img_editor->save( $new_img_path ) ) ) {
					return array(
						'url'    => '',
						'width'  => '',
						'height' => '',
					);
				}
				if ( ! is_string( $new_img_path ) ) {
					return array(
						'url'    => '',
						'width'  => '',
						'height' => '',
					);
				}

				$new_img_size = getimagesize( $new_img_path );
				$new_img      = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

				// resized output.
				$vt_image = array(
					'url'    => $new_img,
					'width'  => $new_img_size[0],
					'height' => $new_img_size[1],
				);

				return $vt_image;
			}

			// default output - without resizing.
			$vt_image = array(
				'url'    => $image_src[0],
				'width'  => $image_src[1],
				'height' => $image_src[2],
			);

			return $vt_image;
		}

		return false;
	}

	/**
	 * Convert array of named params to string version
	 * All values will be escaped
	 *
	 * E.g. f(array('name' => 'foo', 'id' => 'bar')) -> 'name="foo" id="bar"'
	 *
	 * @param  array $attributes the attributes array.
	 * @return string
	 */
	public static function stringify_attributes( $attributes ) {
		$atts = array();
		foreach ( $attributes as $name => $value ) {
			$atts[] = $name . '="' . esc_attr( $value ) . '"';
		}

		return implode( ' ', $atts );
	}
}
