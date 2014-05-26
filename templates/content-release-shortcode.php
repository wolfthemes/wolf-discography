<?php
global $more;
$more = 0;

$post_id = get_the_ID();

/* Metaboxes and Taxonomy */
$release_title = get_post_meta( $post_id, '_wolf_release_title', true );
$release_itunes = get_post_meta( $post_id, '_wolf_release_itunes', true );
$release_amazon = get_post_meta( $post_id, '_wolf_release_amazon', true );
$release_buy = get_post_meta( $post_id, '_wolf_release_buy', true );
$release_free = get_post_meta( $post_id, '_wolf_release_free', true );
?>
<li id="post-<?php the_ID(); ?>" <?php post_class( array( 'wolf-release', 'clearfix' ) ); ?>>
	<span class="entry-thumbnail">
		<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'wolf' ), the_title_attribute( 'echo=0' ) ) ); ?>">
			<?php the_post_thumbnail( 'CD' ); ?>
		</a>
		<?php endif; ?>
		<h3 class="entry-title">
			<a class="entry-link" href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'wolf' ), the_title_attribute( 'echo=0' ) ) ); ?>">
				<?php the_title(); ?>
			</a>
		</h3>
		<span class="wolf-release-buttons">
			<?php if ( $release_itunes ) : ?>
			<span class="wolf-release-button">
				<a class="wolf-release-itunes" href="<?php echo $release_itunes; ?>">iTunes</a>
			</span>
			<?php endif; ?>
			<?php if ( $release_amazon ) : ?>
			<span class="wolf-release-button">
				<a class="wolf-release-amazon" href="<?php echo $release_amazon; ?>">Amazon</a>
			</span>
			<?php endif; ?>
			<?php if ( $release_buy ) : ?>
			<span class="wolf-release-button">
				<a class="wolf-release-buy" href="<?php echo $release_buy; ?>"><?php _e( 'Buy', 'wolf' ); ?></a>
			</span>
			<?php endif; ?>
			<?php if ( $release_free ) : ?>
			<span class="wolf-release-button">
				<a class="wolf-release-free" href="<?php echo $release_free; ?>"><?php _e( 'Free Download', 'wolf' ); ?></a>
			</span>
			<?php endif; ?>
		</span>
	</span>			
</li><!-- .wolf-release -->