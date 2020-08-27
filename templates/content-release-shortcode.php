<?php
/**
 * Display the release inside the shortcode loop
 *
 * @author WolfThemes
 * @package WolfDiscography/Templates
 * @version 1.5.1
 * @since 1.0.2
 */
?>
<article itemscope itemtype="http://schema.org/MusicAlbum" id="post-<?php the_ID(); ?>" <?php post_class( array( 'wolf-release', 'clearfix' ) ); ?>>
	<?php 
		/**
		 * wolf_release_start_hook
		 */
		do_action( 'wolf_release_start' );
	?>
	<div class="entry-thumbnail release-thumbnail">
		<?php wd_release_thumbnail(); ?>
		<h2 class="entry-title release-title" itemprop="name">
			<a itemprop="url" href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'wolf-discography' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h2>
		<?php wd_release_buttons(); ?>
	</div><!-- .entry-thumbnail -->
</article><!-- article.wolf-release -->