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
?>
	<?php
		/**
		 * wolf_discography_before_main_content hook
		 *
		 * @hooked wolf_discography_output_content_wrapper - 10 (outputs opening divs for the content)
		 */
		do_action( 'wolf_discography_before_main_content' );
	?>

	<?php while ( have_posts() ) : the_post(); ?>
	<article itemscope itemtype="http://schema.org/MusicAlbum" data-post-id="<?php the_ID(); ?>" id="post-<?php the_ID(); ?>" <?php post_class( array( 'wolf-release' ) ); ?>>
		<?php
			/**
			 * wolf_release_start_hook
			 */
			do_action( 'wolf_release_start' );
		?>
		<div class="entry-thumbnail">
			<?php
				/**
				 * Cover
				 */
				wd_release_thumbnail();

				/**
				 * Buy Buttons
				 */
				wd_release_buttons();
			?>
		</div>

		<div class="entry-content">
			<h2 class="entry-title">
				<?php the_title(); ?>
			</h2>
			<div class="wolf-release-meta">
				<?php
					/**
					 * Meta
					 */
					wd_release_meta();

					/**
					 * Tracklists
					 */
					wd_release_tracklist();
				?>
			</div>

			<?php the_content(); ?>
		</div><!-- .entry-content -->

		<div class="clear"></div>

		<?php // comments_template(); ?>

	</article><!-- .wolf-release -->
	<?php wolf_release_nav(); ?>
	<?php endwhile; ?>
	<?php
		/**
		 * wolf_discography_after_main_content hook
		 *
		 * @hooked wolf_discography_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'wolf_discography_after_main_content' );
	?>

<?php
get_sidebar();
get_footer();
?>