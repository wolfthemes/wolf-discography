<?php
/**
 * Hooks
 *
 * @package WolfDiscography
 * @subpackage Frontend
 * @since 2.0.0
 */

namespace WolfDiscography\Frontend;

use WolfDiscography\Core\Core;
use WolfDiscography\Core\Utilities;
use WolfDiscography\Core\Meta;

defined( 'ABSPATH' ) || exit;

class Hooks {

	/**
	 * Constructor
	 */
	public function __construct() {

		/**
		 * Body class
		 *
		 * @see  wd_body_class()
		 */
		add_filter( 'body_class', array( $this, 'body_class' ) );

		/**
		 * WP Header
		 *
		 * @see  wd_generator_tag()
		 */
		add_action( 'get_the_generator_html', array( $this, 'generator_tag' ), 10, 2 );
		add_action( 'get_the_generator_xhtml', array( $this, 'generator_tag' ), 10, 2 );

		add_action( 'wolf_release_start', array( $this, 'release_microdata' ) );

		/**
		 * Content wrappers
		 *
		 * @see wolf_discography_output_content_wrapper()
		 * @see wolf_discography_output_content_wrapper_end()
		 */
		add_action( 'wolf_discography_before_main_content', 'wolf_discography_output_content_wrapper', 10 );
		add_action( 'wolf_discography_after_main_content', 'wolf_discography_output_content_wrapper_end', 10 );

		add_action( 'wolf_discography_single_content', 'wolf_discography_output_single_content', 10 );
	}

	/**
	 * Output post microdata
	 *
	 * @since 1.3.0
	 */
	public function release_microdata() {

		$band         = strip_tags( get_the_term_list( get_the_ID(), 'band', '', ', ', '' ) );
		$meta         = Meta::get_meta();
		$release_date = $meta['date'];
		$tracklist    = meta::release_get_tracklist();
		?>
		<meta itemprop="publisher" content="<?php echo esc_url( home_url( '/' ) ); ?>">
		<link itemprop="mainEntityOfPage" content="<?php the_permalink(); ?>">
		<meta itemprop="name" content="<?php the_title(); ?>">
		<meta itemprop="image" content="<?php echo Utilities::get_post_thumbnail_url( 'large' ); ?>">
		<?php if ( $band ) : ?>
			<meta itemprop="byArtist" content="<?php echo esc_attr( $band ); ?>">
		<?php endif; ?>
		<?php if ( $release_date ) : ?>
			<meta itemprop="datePublished" content="<?php echo esc_attr( $release_date ); ?>">
		<?php endif; ?>
		<?php
	}


	/*
	 * Output generator tag to aid debugging.
	 */
	public function generator_tag( $gen, $type ) {
		switch ( $type ) {
			case 'html':
				$gen .= "\n" . '<meta name="generator" content="WolfDiscography ' . esc_attr( WD_VERSION ) . '">';
				break;
			case 'xhtml':
				$gen .= "\n" . '<meta name="generator" content="WolfDiscography ' . esc_attr( WD_VERSION ) . '" />';
				break;
		}
		return $gen;
	}

	/**
	 * Add specific class to the body when we're on the discography page
	 *
	 * @since 1.2.6
	 * @param array $classes
	 * @return array $classes
	 */
	public function body_class( $classes ) {

		if ( is_page( Core::get_discography_page_id() ) ) {
			$classes[] = 'discography-page';
		}

		if (
			! is_singular( 'release' )
			&& ( 'release' == get_post_type() || is_page( Core::get_discography_page_id() ) )
		) {
			$classes[] = 'wolf-discography';
		}

		return $classes;
	}
}