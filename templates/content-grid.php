<?php
/**
 * Display the release inside the loop
 *
 * @author WolfThemes
 * @package WolfDiscography/Templates
 * @version 1.5.1
 * @since 1.0.2
 */

use WolfDiscography\Frontend\Helpers;

defined( 'ABSPATH' ) || exit;

extract(
	wp_parse_args(
		$template_args,
		array(
			'overlay_color'             => 'auto',
			'overlay_custom_color'      => '',
			'overlay_opacity'           => 88,
			'overlay_text_color'        => '',
			'overlay_text_custom_color' => '',
			'release_add_buy_links'     => false,
			'release_do_redirect_url'   => false,
			'thumbnail_size'            => '400x400', // Default thumb size
			'custom_thumbnail_size'     => '',
		)
	)
);

if ( $custom_thumbnail_size ) {
	$thumbnail_size = $custom_thumbnail_size;
}

$text_style         = '';
$overlay_text_color = apply_filters( 'wd_release_overlay_text_color', $overlay_text_color );

if ( $overlay_text_color && 'overlay' === $layout ) {
	$text_color = Helpers::convert_color_class_to_hex_value( $overlay_text_color, $overlay_text_custom_color );
	if ( $text_color ) {
		$text_style .= 'color:' . Helpers::sanitize_color( $text_color ) . '!important;';
	}
}

$dominant_color       = Helpers::get_image_dominant_color( get_post_thumbnail_id() );
$actual_overlay_color = '';

if ( 'auto' === $overlay_color ) {

	$actual_overlay_color = $dominant_color;

} else {
	$actual_overlay_color = Helpers::convert_color_class_to_hex_value( $overlay_color, $overlay_custom_color );
}

$overlay_tone_class   = 'overlay-tone-' . Helpers::get_color_tone( $actual_overlay_color );
$custom_redirect_link = get_post_meta( get_the_ID(), '_release_redirect_url', true );
$permalink            = ( $custom_redirect_link ) ? $custom_redirect_link : get_the_permalink();
$target               = ( $release_do_redirect_url && $custom_redirect_link ) ? '_blank' : '';
?>
<article <?php wd_post_attr( array( $overlay_tone_class ) ); ?>>
	<?php
		/**
		 * wolf_release_start_hook
		 */
		do_action( 'wolf_release_start' );
	?>
	<div class="entry-box">
		<div class="entry-container">
			<a class="entry-link-mask" target="<?php echo esc_attr( $target ); ?>" href="<?php echo esc_url( $permalink ); ?>"></a>
			<?php
				$style              = '';
				$img_dominant_color = Helpers::get_image_dominant_color( get_post_thumbnail_id() );

			if ( $img_dominant_color ) {
				$img_dominant_color = Helpers::sanitize_color( $img_dominant_color );
				$style              = "background-color:$img_dominant_color;";
			}
			?>
				<div class="entry-image" style="<?php echo Helpers::esc_style_attr( $style ); ?>">
				<?php

					Helpers::resized_thumbnail( $thumbnail_size ); 				?>
			</div>
			<div class="entry-inner">
				<?php
					$dominant_color = Helpers::get_image_dominant_color( get_post_thumbnail_id() );

				if ( $dominant_color && 'auto' === $overlay_color ) {
					$overlay_custom_color = $dominant_color;
				}

					echo wd_background_overlay(
						array(
							'overlay_color'        => $overlay_color,
							'overlay_custom_color' => $overlay_custom_color,
							'overlay_opacity'      => $overlay_opacity,
						)
					);

					if ( Helpers::is_elementor_editor() ) {
						$text_style = '';
					}
					?>
				<div style="<?php echo Helpers::esc_style_attr( $text_style ); ?>" class="entry-summary">
					<h3 class="entry-title"><a target="<?php echo esc_attr( $target ); ?>"  href="<?php echo esc_url( $permalink ); ?>" style="<?php echo Helpers::esc_style_attr( $text_style ); ?>"><?php the_title(); ?></a></h3>
					<div style="<?php echo Helpers::esc_style_attr( $text_style ); ?>" class="entry-taxonomy">
						<?php echo get_the_term_list( get_the_ID(), 'band', apply_filters( 'wd_release_tax_before', '' ), ' / ', '' ); ?>
					</div><!-- .entry-taxonomy -->
					<?php do_action( 'wd_loop_release_caption_end' ); ?>
				</div><!--  .entry-summary  -->
			</div><!--  .entry-summary-container  -->
		</div>
	</div><!-- .entry-box -->

	<?php do_action( 'wd_loop_release_end', $release_add_buy_links ); ?>
</article><!-- #post-## -->