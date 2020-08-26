<?php
/**
 * The Template for displaying release archives
 *
 * Override this template by copying it to yourtheme/wolf-discography/archive-release.php
 *
 * @author WolfThemes
 * @package WolfDiscography/Templates
 * @version %VERSION%
 * @since 1.0.2
 */

defined( 'ABSPATH' ) || exit;

get_header( 'discography' ); ?>

	<?php
		/**
		 * wolf_discography_before_main_content hook
		 *
		 * @hooked wolf_discography_output_content_wrapper - 10 (outputs opening divs for the content)
		 */
		do_action( 'wolf_discography_before_main_content' );
	?>

	<?php
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1; ?>
		
		<?php if ( have_posts() ) : ?>
			
			<?php wolf_discography_loop_start(); ?>
			
				<?php while ( have_posts() ) : the_post(); ?>
				
					<?php wolf_discography_get_template_part( 'content', 'release' ); ?>
				
				<?php endwhile; ?>
			
			<?php wolf_discography_loop_end(); ?>
		
			<?php wolf_release_page_nav(); ?>
		
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
	?>

<?php
get_sidebar( 'discography' );
get_footer( 'discography' ); 
?>