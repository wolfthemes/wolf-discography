<?php
/**
 * Template Helper
 *
 * @package WolfDiscography/Frontend
 * @since 2.0.0
 */

namespace WolfDiscography\Frontend;

use WolfDiscography\Core\Utilities;

defined( 'ABSPATH' ) || exit;

class TemplateHelper {

	/**
	 * Output release thumbnail
	 *
	 * @since 1.2.6
	 */
	public static function release_thumbnail( $thumbnail_size = '' ) {

		$post_id        = get_the_ID();
		$thumbnail_size = get_post_meta( $post_id, '_wolf_release_type', true ) == 'DVD' || get_post_meta( $post_id, '_wolf_release_type', true ) == 'K7' ? 'DVD' : 'CD';
		$thumbnail_size = apply_filters( 'wd_thumbnail_size', $thumbnail_size );
		if ( has_post_thumbnail() ) :
			?>
			<?php if ( ! is_single() ) : ?>
				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to %s', 'wolf-discography' ), the_title_attribute( 'echo=0' ) ) ); ?>">
			<?php endif ?>
				<?php
					/**
					 * wd_before_thumbnail_hook
					 */
					do_action( 'wd_before_thumbnail' );

					/**
					 * Release thumbnail
					 */
					the_post_thumbnail( $thumbnail_size );

					/**
					 * wd_before_thumbnail_hook
					 */
					do_action( 'wd_after_thumbnail' );
				?>
			<?php if ( ! is_single() ) : ?>
				</a>
			<?php endif ?>
			<?php
		endif;
	}

	/**
	 * Output release meta
	 *
	 * @since 1.4.2
	 */
	public static function release_buttons() {

		$meta = Meta::get_meta();

		$release_itunes   = $meta['itunes'];
		$release_amazon   = $meta['amazon'];
		$release_bandcamp = $meta['bandcamp'];
		$release_spotify  = $meta['spotify'];
		$release_buy      = $meta['buy'];
		$release_free     = $meta['free'];
		$release_apple    = $meta['apple'];
		$release_deezer   = $meta['deezer'];
		$release_tidal    = $meta['tidal'];
		$release_yt       = $meta['google_play'];
		$release_qobuz    = $meta['qobuz'];
		$product_id       = absint( get_post_meta( get_the_ID(), '_post_wc_product_id', true ) );

		ob_start();
		?>
		<span class="wolf-release-buttons">


			<?php if ( $release_free ) : ?>
			<span class="wolf-release-button">
				<a class="wolf-release-free <?php echo apply_filters( 'wd_release_button_class', 'button' ); ?>" title="<?php esc_html_e( 'Download Now', '%TEXTDOMAIN%' ); ?>" href="<?php echo esc_url( $release_free ); ?>"><?php esc_html_e( 'Free Download', '%TEXTDOMAIN%' ); ?></a>
			</span>
			<?php endif; ?>
			<?php if ( $release_spotify ) : ?>
			<span class="wolf-release-button">
				<a target="_blank" title="<?php printf( esc_html__( 'Stream on %s', '%TEXTDOMAIN%' ), 'Spotify' ); ?>" class="wolf-release-spotify <?php echo apply_filters( 'wd_release_button_class', 'button' ); ?>" href="<?php echo esc_url( $release_spotify ); ?>"><?php esc_html_e( 'Spotify', '%TEXTDOMAIN%' ); ?></a>
			</span>
			<?php endif; ?>
			<?php if ( $release_tidal ) : ?>
			<span class="wolf-release-button">
				<a target="_blank" title="<?php printf( esc_html__( 'Stream on %s', '%TEXTDOMAIN%' ), 'Tidal' ); ?>" class="wolf-release-tidal <?php echo apply_filters( 'wd_release_button_class', 'button' ); ?>" href="<?php echo esc_url( $release_tidal ); ?>"><?php esc_html_e( 'Tidal', '%TEXTDOMAIN%' ); ?></a>
			</span>
			<?php endif; ?>
				<?php if ( $release_apple ) : ?>
			<span class="wolf-release-button">
				<a target="_blank" title="<?php printf( esc_html__( 'Stream on %s', '%TEXTDOMAIN%' ), 'Apple Music' ); ?>" class="wolf-release-apple <?php echo apply_filters( 'wd_release_button_class', 'button' ); ?>" href="<?php echo esc_url( $release_apple ); ?>"><?php esc_html_e( 'Apple', '%TEXTDOMAIN%' ); ?></a>
			</span>
			<?php endif; ?>
			<?php if ( $release_deezer ) : ?>
			<span class="wolf-release-button">
				<a target="_blank" title="<?php printf( esc_html__( 'Stream on %s', '%TEXTDOMAIN%' ), 'Deezer' ); ?>" class="wolf-release-deezer <?php echo apply_filters( 'wd_release_button_class', 'button' ); ?>" href="<?php echo esc_url( $release_deezer ); ?>"><?php esc_html_e( 'Deezer', '%TEXTDOMAIN%' ); ?></a>
			</span>
			<?php endif; ?>
			<?php if ( $release_itunes ) : ?>
			<span class="wolf-release-button">
				<a target="_blank" title="<?php printf( esc_html__( 'Stream on %s', '%TEXTDOMAIN%' ), 'iTunes' ); ?>" class="wolf-release-itunes <?php echo apply_filters( 'wd_release_button_class', 'button' ); ?>" href="<?php echo esc_url( $release_itunes ); ?>"><?php esc_html_e( 'iTunes', '%TEXTDOMAIN%' ); ?></a>
			</span>
			<?php endif; ?>
			<?php if ( $release_amazon ) : ?>
			<span class="wolf-release-button">
				<a target="_blank" title="<?php printf( esc_html__( 'Stream on %s', '%TEXTDOMAIN%' ), 'Amazon Music' ); ?>" class="wolf-release-amazon <?php echo apply_filters( 'wd_release_button_class', 'button' ); ?>" href="<?php echo esc_url( $release_amazon ); ?>"><?php esc_html_e( 'Amazon', '%TEXTDOMAIN%' ); ?></a>
			</span>
			<?php endif; ?>
			<?php if ( $release_bandcamp ) : ?>
			<span class="wolf-release-button">
				<a target="_blank" title="<?php printf( esc_html__( 'Buy on %s', '%TEXTDOMAIN%' ), 'Bandcamp' ); ?>" class="wolf-release-bandcamp <?php echo apply_filters( 'wd_release_button_class', 'button' ); ?>" href="<?php echo esc_url( $release_bandcamp ); ?>"><?php esc_html_e( 'Bandcamp', '%TEXTDOMAIN%' ); ?></a>
			</span>
			<?php endif; ?>
			<?php if ( $release_qobuz ) : ?>
			<span class="wolf-release-button">
				<a target="_blank" title="<?php printf( esc_html__( 'Buy on %s', '%TEXTDOMAIN%' ), 'Qobuz' ); ?>" class="wolf-release-qobuz <?php echo apply_filters( 'wd_release_button_class', 'button' ); ?>" href="<?php echo esc_url( $release_qobuz ); ?>"><?php esc_html_e( 'Qobuz', '%TEXTDOMAIN%' ); ?></a>
			</span>
			<?php endif; ?>
				<?php if ( $release_yt ) : ?>
			<span class="wolf-release-button">
				<a target="_blank" title="<?php printf( esc_html__( 'Stream on %s', '%TEXTDOMAIN%' ), 'YouTube Music' ); ?>" class="wolf-release-google_play <?php echo apply_filters( 'wd_release_button_class', 'button' ); ?>" href="<?php echo esc_url( $release_yt ); ?>"><?php esc_html_e( 'YT Music', '%TEXTDOMAIN%' ); ?></a>
			</span>
			<?php endif; ?>
				<?php if ( $release_buy ) : ?>
			<span class="wolf-release-button">
				<a target="_blank" title="<?php esc_html_e( 'Buy Now', '%TEXTDOMAIN%' ); ?>" class="wolf-release-buy <?php echo apply_filters( 'wd_release_button_class', 'button' ); ?>" href="<?php echo esc_url( $release_buy ); ?>"><?php esc_html_e( 'Buy', '%TEXTDOMAIN%' ); ?></a>
			</span>
			<?php endif; ?>
				<?php if ( $product_id && 0 != $product_id ) : ?>
				<span class="wolf-release-button">
					<?php echo Utilities::add_to_cart( $product_id, 'wolf-release-add-to-cart ' . apply_filters( 'wd_release_button_class', 'button' ), '<span class="wolf-release-add-to-cart-button-title" title="' . esc_html__( 'Add to cart', '%TEXTDOMAIN%' ) . '">' . esc_html__( 'Add to cart', '%TEXTDOMAIN%' ) . '</span>' ); ?>

				</span>
			<?php endif; ?>
		</span><!-- .wolf-release-buttons -->
		<?php
		$output = ob_get_clean();
		echo apply_filters( 'wolf_discography_release_buttons', $output );
	}


	/**
	 * Output release tracklist
	 */
	public static function release_tracklist() {

		$post_id         = get_the_ID();
		$tracklist       = Meta::release_get_tracklist();
		$tracklist_count = Meta::release_get_tracklist_count();

		if ( $tracklist ) {
			?>
			<ol class="release-tracklist">
			<?php if ( $tracklist_count ) : ?>
				<meta itemprop="numTracks" content="<?php echo esc_attr( $tracklist_count ); ?>">
			<?php endif; ?>
			<?php
			foreach ( $tracklist as $track ) {
				?>
				<li itemprop="track" itemscope itemtype="http://schema.org/MusicRecording">
					<span class="track-title" itemprop="name">
						<?php echo sanitize_text_field( $track ); ?>
					</span>
				</li>
				<?php
			}
			?>
			</ol><!-- .release-tracklist -->
			<?php
		}
	}


	/**
	 * Output release meta
	 */
	public static function release_meta() {

		$meta            = Meta::get_meta();
		$release_title   = $meta['title'];
		$release_date    = $meta['date'];
		$release_catalog = $meta['catalog'];
		$release_format  = $meta['format'];

		ob_start();
		echo meta::get_artist();
		?>
		<?php
		// Title
		if ( $release_title ) :
			?>
		<strong><?php esc_html_e( 'Title', 'wolf-discography' ); ?></strong> : <?php echo sanitize_text_field( $release_title ); ?><br>
		<?php endif; ?>

		<?php
		// Date
		if ( $release_date ) :
			?>
		<strong><?php esc_html_e( 'Release Date', 'wolf-discography' ); ?></strong> : <?php echo sanitize_text_field( $release_date ); ?><br>
		<?php endif; ?>

		<?php echo Meta::get_label(); ?>

		<?php
		// Catalog number
		if ( $release_catalog ) :
			?>
		<strong><?php esc_html_e( 'Catalog ref.', 'wolf-discography' ); ?></strong> : <?php echo sanitize_text_field( $release_catalog ); ?><br>
		<?php endif; ?>

		<?php
		// Type
		if ( $release_format && Options::get_option( 'display_format' ) ) :
			?>
		<strong><?php esc_html_e( 'Format', 'wolf-discography' ); ?></strong> : <?php echo sanitize_text_field( $release_format ); ?><br>
		<?php endif; ?>
		<?php edit_post_link( esc_html__( 'Edit', 'wolf-discography' ), '<span class="edit-link">', '</span>' ); ?>
		<?php
		$output = ob_get_clean();
		echo apply_filters( 'wolf_discography_release_meta', $output );
	}

	/**
	 * Displays release page navigation
	 *
	 * @return string
	 */
	public static function release_page_nav( $loop = null ) {

		if ( ! $loop ) {
			global $wp_query;
			$max = $wp_query->max_num_pages;
		} else {
			$max = $loop->max_num_pages;
		}

		// Don't print empty markup if there's only one page.
		if ( $max < 2 ) {
			return;
		}

		?>
		<nav class="navigation release-paging-navigation" role="navigation">
			<div class="nav-links clearfix">

				<?php if ( get_next_posts_link( '', $max ) ) : ?>
				<div class="nav-previous"><?php next_posts_link( esc_html__( '<span class="meta-nav">&larr;</span> Older releases', 'wolf-discography' ), $max ); ?></div>
				<?php endif; ?>

				<?php if ( get_previous_posts_link( '', $max ) ) : ?>
				<div class="nav-next"><?php previous_posts_link( esc_html__( 'Newer releases <span class="meta-nav">&rarr;</span>', 'wolf-discography' ), $max ); ?></div>
				<?php endif; ?>

			</div><!-- .nav-links -->
		</nav><!-- .navigation -->
		<?php
	}

	/**
	 * Displays release navigation
	 *
	 * @return string
	 */
	public static function release_nav() {

		global $post;

		// Don't print empty markup if there's nowhere to navigate.
		$previous = get_adjacent_post( false, '', true );
		$next     = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous ) {
			return;
		}
		?>
		<nav class="release-navigation" role="navigation">
			<?php previous_post_link( '%link', _x( '<span class="meta-nav">&larr;</span> %title', 'Previous post link', 'wolf-discography' ) ); ?>
			<?php next_post_link( '%link', _x( '%title <span class="meta-nav">&rarr;</span>', 'Next post link', 'wolf-discography' ) ); ?>
		</nav><!-- .navigation -->
		<?php
	}

	/**
	 * Display background overlay
	 *
	 * @param array $args
	 * @return string $output
	 */
	public static function background_overlay( $args ) {

		extract(
			wp_parse_args(
				$args,
				array(
					'overlay_color'        => 'black',
					'overlay_custom_color' => '#000000',
					'overlay_opacity'      => '',
					'overlay_tag'          => 'div',
				)
			)
		);

		$overlay_opacity = ( $overlay_opacity ) ? absint( $overlay_opacity ) / 100 : .4;

		$overlay_style = '';
		$class         = 'wd-bg-overlay';

		if ( ( 'custom' === $overlay_color || 'auto' === $overlay_color ) && $overlay_custom_color ) {

			$overlay_style .= 'background-color:' . Helper::sanitize_color( $overlay_custom_color ) . ';';

		} else {
			$class .= " wd-background-color-$overlay_color";
		}

		$overlay_style .= "opacity:$overlay_opacity;";

		return '<' . $overlay_tag . ' style="' . Helpers::esc_style_attr( $overlay_style ) . '" class="' . Helpers::sanitize_html_classes( $class ) . '"></' . $overlay_tag . '><!--.wd-bg-overlay-->';
	}
}