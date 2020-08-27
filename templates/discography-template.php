<?php
/**
 * The Template for displaying the main releases page
 *
 * Override this template by copying it to yourtheme/wolf-discography/discography-template.php
 *
 * @author WolfThemes
 * @package WolfDiscography/Templates
 * @version 1.5.1
 * @since 1.0.3
 */

if ( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

get_header( 'discography' );

	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$posts_per_page = apply_filters( 'wd_posts_per_page', -1 );
	
	$args = array(
		'post_type' => 'release',
		'posts_per_page' => $posts_per_page,
	);

	if ( -1 < $posts_per_page ) {
		$args['paged'] = $paged;
	}

	$loop = new WP_Query( $args );

	/**
	 * wolf_discography_before_main_content hook
	 *
	 * @hooked wolf_discography_output_content_wrapper - 10 (outputs opening divs for the content)
	 */
	do_action( 'wolf_discography_before_main_content' );

	if ( $loop->have_posts() ) : ?>

		<?php wolf_discography_loop_start(); ?>

			<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>

				<?php wolf_discography_get_template_part( 'content', 'release' ); ?>

			<?php endwhile; ?>

		<?php wolf_discography_loop_end(); ?>


	<?php wolf_release_page_nav( $loop ); ?>

	<?php else : ?>

		<?php wolf_discography_get_template( 'loop/no-releases-found.php' ); ?>

	<?php endif; ?>

<?php
	/**
	 * wolf_discography_after_main_content hook
	 *
	 * @hooked wolf_discography_output_content_wrapper_end - 10 (outputs closing divs for the content)
	 */
	do_action('wolf_discography_after_main_content');

get_sidebar( 'discography' );
get_footer( 'discography' );
?>