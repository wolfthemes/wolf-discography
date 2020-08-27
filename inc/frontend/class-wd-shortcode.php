<?php
/**
 * Discography Shortcode.
 *
 * @class WD_Shortcode
 * @author WolfThemes
 * @category Core
 * @package WolfDiscography/Shortcode
 * @version 1.5.1
 * @since 1.2.6
 */

defined( 'ABSPATH' ) || exit;

/**
 * WD_Shortcode class.
 */
class WD_Shortcode {
	/**
	 * Constructor
	 */
	public function __construct() {

		add_shortcode( 'wolf_last_releases', array( $this, 'shortcode' ) );
		add_shortcode( 'wolf_last_release', array( $this, 'shortcode_single' ) );
	}

	/**
	 * Add filter to exlude password protected posts
	 *
	 * Create a new filtering function that will add our where clause to the query
	 */
	public function filter_where( $where = '' ) {
		$where .= " AND post_password = ''";
		return $where;
	}

	/**
	 * Shortcode
	 *
	 * @param array $atts
	 * @return string
	 */
	public function shortcode( $atts ) {
		extract(
			shortcode_atts(
				array(
					'count' => 4,
					'band' => null,
					'label' => null,
					'col' => wolf_get_release_option( 'col', 4 ),
					'padding' => 'yes',
					'display' => '', // for custom appareance in theme
					'animation' => '',
					'animation_delay' => '',
				), $atts
			)
		);

		ob_start();

		$args = array(
			'post_type' => array( 'release' ),
			'posts_per_page' => absint( $count ),
		);

		if ( $band ) {
			$args['band'] = $band;
		}

		if ( $label ) {
			$args['label'] = $label;
		}

		$class = 'shortcode-release-grid';

		if ( $display ) {
			$class .= ' discography-display-' . esc_attr( $display );
		}

		$class .= ' release-grid-col-' . absint( $col );
		$class .= ' shortcode-release-padding-' . esc_attr( $padding );

		add_filter( 'posts_where', array( $this, 'filter_where' ) );
		$loop = new WP_Query( $args );
		remove_filter( 'posts_where', array( $this, 'filter_where' ) );
		
		if ( $loop->have_posts() ) : ?>
			<div class="<?php echo apply_filters( 'wolf_discography_last_releases_shortcode_class', $class ); ?>" data-animation-parent="<?php echo esc_attr( $animation ); ?>">
				<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>

					<?php wolf_discography_get_template_part( 'content', 'release-shortcode' ); ?>

				<?php endwhile; ?>
			</div><!-- .shortcode-release-grid -->
			<div class="clear"></div>
		<?php else : // no release ?>
			<?php wolf_discography_get_template( 'loop/no-releases-found.php' ); ?>
		<?php endif;
		wp_reset_postdata();

		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	/**
	 * Shortcode
	 *
	 * @param array $atts
	 * @return string
	 */
	public function shortcode_single( $atts ) {
		extract(
			shortcode_atts(
				array(
					'count' => 4,
					'band' => null,
					'label' => null,
					'display_title' => true,
					'display_buttons' => true,
					'animation' => '',
					'animation_delay' => '',
				), $atts
			)
		);

		ob_start();

		$args = array(
			'post_type' => array( 'release' ),
			'posts_per_page' => 1,
		);

		if ( $band ) {
			$args['band'] = $band;
		}

		if ( $label ) {
			$args['label'] = $label;
		}

		$class = 'shortcode-release-single';

		if ( $animation ) {
			$class .= " wow $animation";
		}

		$style = '';
		if ( $animation_delay && $animation ) {
			$style = 'animation-delay:' . absint( $animation_delay ) / 1000 . 's;-webkit-animation-delay:' . absint( $animation_delay ) / 1000 . 's;';
		}

		$loop = new WP_Query( $args );
		if ( $loop->have_posts() ) : ?>
			<div class="<?php echo $class; ?>" style="<?php echo esc_attr( $style ); ?>" data-animation="<?php echo esc_attr( $animation ); ?>">
				<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>

					<?php wolf_discography_get_template_part( 'content', 'release-shortcode' ); ?>

				<?php endwhile; ?>
			</div><!-- .shortcode-single-release -->
			<div class="clear"></div>
		<?php else : // no release ?>
			<?php wolf_discography_get_template( 'loop/no-releases-found.php' ); ?>
		<?php endif;
		wp_reset_postdata();

		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	/**
	 * Helper method to determine if a shortcode attribute is true or false.
	 *
	 * @since 1.0.2
	 *
	 * @param string|int|bool $var Attribute value.
	 * @return bool
	 */
	protected function shortcode_bool( $var ) {
		$falsey = array( 'false', '0', 'no', 'n' );
		return ( ! $var || in_array( strtolower( $var ), $falsey, true ) ) ? false : true;
	}

} // end class

return new WD_Shortcode();