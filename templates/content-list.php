<?php
/**
 * Display the release inside the loop
 *
 * @author WolfThemes
 * @package Wolf_Discography/Templates
 * @version 1.5.1
 * @since 1.0.2
 */
?>
<li itemscope itemtype="http://schema.org/MusicAlbum" id="post-<?php the_ID(); ?>" <?php post_class( array( 'wolf-release', 'clearfix' ) ); ?>>
	<?php
		/**
		 * wolf_release_start_hook
		 */
		do_action( 'wolf_release_start' );
	?>
	<div class="entry-thumbnail release-thumbnail">
		<?php wd_release_thumbnail(); ?>
	</div><!-- .entry-thumbnail -->

	<div class="entry-content release-content">
		<h2 class="entry-title release-title">
			<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'wolf-discography' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h2>
		<div class="wolf-release-meta">
			<?php wd_release_meta(); ?>
		</div>
		<?php
			/**
			 * Content excerpt
			 */
			the_excerpt();
		?>
	</div><!-- .entry-content -->
	<div class="clear"></div>
</li><!-- article.wolf-release -->