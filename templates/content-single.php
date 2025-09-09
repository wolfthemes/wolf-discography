<?php
/**
 * Display the single release content
 *
 * @author WolfThemes
 * @package WolfDiscography/Templates
 * @version 1.5.1
 * @since 1.0.2
 */
?>
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