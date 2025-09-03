<?php
/**
 * The discography template file.
 *
 * @author WolfThemes
 * @category Core
 * @package WolfDiscography/Templates
 * @version 1.5.1
 */
get_header();
?>
	<div id="primary" class="content-area">
		<main id="content" class="clearfix">
			<?php
				/**
				 * Output post loop through hook so we can do the magic however we want
				 */
				do_action(
					'wolf_discography_posts',
					array(
						'el_id'             => 'discography-index',
						'post_type'         => 'release',
						'pagination'        => wolf_get_release_option( 'release_pagination', '' ),
						'releases_per_page' => wolf_get_release_option( 'releases_per_page', '' ),
						'grid_padding'      => wolf_get_release_option( 'release_grid_padding', 'yes' ),
						'item_animation'    => wolf_get_release_option( 'release_item_animation' ),
					)
				);
				?>
		</main><!-- #content -->
	</div><!-- #primary -->
<?php
get_sidebar( 'discography' );
get_footer();