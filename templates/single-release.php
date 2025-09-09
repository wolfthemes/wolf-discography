<?php
/**
 * The Template for displaying all single releases.
 *
 * @author WolfThemes
 * @package WolfDiscography/Templates
 * @version 1.5.1
 * @since 1.2.6
 */
 get_header();
	/**
	 * wolf_discography_before_main_content hook
	 *
	 * @hooked wolf_discography_output_content_wrapper - 10 (outputs opening divs for the content)
	 */
	do_action( 'wolf_discography_before_main_content' );

	while ( have_posts() ) : the_post();

		wolf_discography_get_template_part( 'content', 'single' );
		wolf_release_nav();

	endwhile;

	/**
	 * wolf_discography_after_main_content hook
	 *
	 * @hooked wolf_discography_output_content_wrapper_end - 10 (outputs closing divs for the content)
	 */
	do_action( 'wolf_discography_after_main_content' );
//get_sidebar();
get_footer();
?>