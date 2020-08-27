<?php
/**
 * Discography Functions
 *
 * Hooked-in functions for Discography related events on the front-end.
 *
 * @author WolfThemes
 * @category Coref
 * @package WolfDiscography/Functions
 * @since 1.0.2
 */

/**
 * Output post microdata
 *
 * @since 1.3.0
 */
function wd_release_microdata() {

	$band = strip_tags( get_the_term_list( get_the_ID(), 'band', '', ', ', '' ) );
	$meta = wd_get_meta();
	$release_date = $meta['date'];
	$tracklist = wd_release_get_tracklist();
	?>
	<meta itemprop="publisher" content="<?php echo esc_url( home_url( '/' ) ); ?>">
	<link itemprop="mainEntityOfPage" content="<?php the_permalink(); ?>">
	<meta itemprop="name" content="<?php the_title(); ?>">
	<meta itemprop="image" content="<?php echo wd_get_post_thumbnail_url( 'large' ); ?>">
	<?php if ( $band ) : ?>
		<meta itemprop="byArtist" content="<?php echo esc_attr( $band ); ?>">
	<?php endif; ?>
	<?php if ( $release_date ) : ?>
		<meta itemprop="datePublished" content="<?php echo esc_attr( $release_date ); ?>">
	<?php endif; ?>
	<?php
}
add_action( 'wolf_release_start', 'wd_release_microdata' );

/**
 * Create a formatted sample of any text
 *
 * Remove HTML and shortcode, sanitize and shorten a string
 *
 * @param string $text
 * @param int $num_words
 * @param string $more
 * @return string
 */
function wd_sample( $text, $num_words = 55, $more = '...' ) {
	return wp_trim_words( strip_shortcodes( $text ), $num_words, $more );
}

/**
 * Get any thumbnail URL
 * @param string $format
 * @param int $post_id
 * @return string
 */
function wd_get_post_thumbnail_url( $format = 'medium', $post_id = null ) {
	global $post;

	if ( is_object( $post ) && isset( $post->ID ) && null == $post_id ) {

		$ID = $post->ID;
	} else {
		$ID = $post_id;
	}

	if ( $ID && has_post_thumbnail( $ID ) ) {

		$attachment_id = get_post_thumbnail_id( $ID );
		if ( $attachment_id ) {
			$img_src = wp_get_attachment_image_src( $attachment_id, $format );

			if ( $img_src && isset( $img_src[0] ) )
				return esc_url( $img_src[0] );
		}
	}
}

/**
 * Output release meta
 *
 * @since 1.4.2
 */
function wd_release_buttons() {

	$meta = wd_get_meta();
	$release_itunes = $meta['itunes'];
	$release_google_play = $meta['google_play'];
	$release_amazon = $meta['amazon'];
	$release_spotify = $meta['spotify'];
	$release_buy = $meta['buy'];
	$release_free = $meta['free'];

	ob_start();
	?>
	<span class="wolf-release-buttons">

		<?php if ( $release_free ) : ?>
		<span class="wolf-release-button">
			<a class="wolf-release-free" href="<?php echo $release_free; ?>"><?php esc_html_e( 'Free Download', 'wolf-discography' ); ?></a>
		</span>
		<?php else : ?>
			<?php if ( $release_spotify ) : ?>
			<span class="wolf-release-button">
				<a title="<?php printf( esc_html__( 'Listen on %s', 'wolf-discography' ), 'Spotify' ); ?>" class="wolf-release-spotify" href="<?php echo $release_spotify; ?>"><?php esc_html_e( 'Spotify', 'wolf-discography' ); ?></a>
			</span>
			<?php endif; ?>
			<?php if ( $release_itunes ) : ?>
			<span class="wolf-release-button">
				<a title="<?php printf( esc_html__( 'Buy on %s', 'wolf-discography' ), 'iTunes' ); ?>" class="wolf-release-itunes" href="<?php echo $release_itunes; ?>"><?php esc_html_e( 'iTunes', 'wolf-discography' ); ?></a>
			</span>
			<?php endif; ?>
			<?php if ( $release_google_play ) : ?>
			<span class="wolf-release-button">
				<a title="<?php printf( esc_html__( 'Buy on %s', 'wolf-discography' ), 'Google Play' ); ?>" class="wolf-release-google_play" href="<?php echo $release_google_play; ?>"><?php esc_html_e( 'Google Play', 'wolf-discography' ); ?></a>
			</span>
			<?php endif; ?>
			<?php if ( $release_amazon ) : ?>
			<span class="wolf-release-button">
				<a title="<?php printf( esc_html__( 'Buy on %s', 'wolf-discography' ), 'amazon' ); ?>" class="wolf-release-amazon" href="<?php echo $release_amazon; ?>"><?php esc_html_e( 'Amazon', 'wolf-discography' ); ?></a>
			</span>
			<?php endif; ?>
			<?php if ( $release_buy ) : ?>
			<span class="wolf-release-button">
				<a title="<?php esc_html_e( 'Buy Now', 'wolf-discography' ); ?>" class="wolf-release-buy" href="<?php echo $release_buy; ?>"><?php esc_html_e( 'Buy', 'wolf-discography' ); ?></a>
			</span>
			<?php endif; ?>
		<?php endif; ?>
	</span><!-- .wolf-release-buttons -->
	<?php
	$output = ob_get_clean();
	echo apply_filters( 'wolf_discography_release_buttons', $output );
}

/**
 * Output release thumbnail
 *
 * @since 1.2.6
 */
function wd_release_thumbnail( $thumbnail_size = '' ) {

	$post_id = get_the_ID();
	$thumbnail_size = get_post_meta( $post_id, '_wolf_release_type', true ) == 'DVD' || get_post_meta( $post_id, '_wolf_release_type', true ) == 'K7' ? 'DVD' : 'CD';
	$thumbnail_size = apply_filters( 'wd_thumbnail_size', $thumbnail_size );
	if ( has_post_thumbnail() ) : ?>
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
	<?php endif;
}

/**
 * Get release tracklist as array
 */
function wd_release_get_tracklist() {

	$post_id = get_the_ID();
	$tracklist = ( is_array( get_post_meta( $post_id, '_wolf_release_tracklist', true ) ) ) ? get_post_meta( $post_id, '_wolf_release_tracklist', true ) : array();

	if ( isset( $tracklist[0] ) && '' != $tracklist[0] ) {
		return $tracklist;
	}
}


/**
 * Output release tracklist
 */
function wd_release_tracklist() {

	$post_id = get_the_ID();
	$tracklist = wd_release_get_tracklist();
	$tracklist_count = wd_release_get_tracklist_count();

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
 * Get tracklist count
 */
function wd_release_get_tracklist_count() {

	$post_id = get_the_ID();
	$tracklist = wd_release_get_tracklist();

	if ( $tracklist ) {
		return absint( count( $tracklist ) );
	}
}

/**
 * Output release meta
 */
function wd_release_meta() {

	$meta = wd_get_meta();
	$release_title = $meta['title'];
	$release_date = $meta['date'];
	$release_catalog = $meta['catalog'];
	$release_format = $meta['format'];

	ob_start();
	echo wd_get_artist();
	?>
	<?php // Title
	if ( $release_title ) : ?>
	<strong><?php esc_html_e( 'Title', 'wolf-discography' ); ?></strong> : <?php echo sanitize_text_field( $release_title ); ?><br>
	<?php endif; ?>

	<?php // Date
	if ( $release_date ) : ?>
	<strong><?php esc_html_e( 'Release Date', 'wolf-discography' ); ?></strong> : <?php echo sanitize_text_field( $release_date ); ?><br>
	<?php endif; ?>

	<?php echo wd_get_label(); ?>

	<?php // Catalog number
	if ( $release_catalog ) : ?>
	<strong><?php esc_html_e( 'Catalog ref.', 'wolf-discography' ); ?></strong> : <?php echo sanitize_text_field( $release_catalog ); ?><br>
	<?php endif; ?>

	<?php // Type
	if ( $release_format && wolf_get_release_option( 'display_format' ) ) : ?>
	<strong><?php esc_html_e( 'Format', 'wolf-discography' ); ?></strong> : <?php echo sanitize_text_field( $release_format ); ?><br>
	<?php endif; ?>
	<?php edit_post_link( esc_html__( 'Edit', 'wolf-discography' ), '<span class="edit-link">', '</span>' ); ?>
	<?php
	$output = ob_get_clean();
	echo apply_filters( 'wolf_discography_release_meta', $output );
}

/**
 * Get release meta
 *
 * @since 1.2.6
 * @return array
 */
function wd_get_meta() {

	$meta = wd_get_default_meta(); // get empty object

	$post_id = get_the_ID();
	$title = get_post_meta( $post_id, '_wolf_release_title', true );
	$date = get_post_meta( $post_id, '_wolf_release_date', true );
	$catalog = get_post_meta( $post_id, '_wolf_release_catalog_number', true );
	$format = get_post_meta( $post_id, '_wolf_release_type', true );
	$itunes = get_post_meta( $post_id, '_wolf_release_itunes', true );
	$google_play = get_post_meta( $post_id, '_wolf_release_google_play', true );
	$amazon = get_post_meta( $post_id, '_wolf_release_amazon', true );
	$spotify = get_post_meta( $post_id, '_wolf_release_spotify', true );
	$buy = get_post_meta( $post_id, '_wolf_release_buy', true );
	$free = get_post_meta( $post_id, '_wolf_release_free', true );
	$tracklist = wd_release_get_tracklist();

	$display_date = '';
	if ( $date ) {
		list( $month, $day, $year ) = explode( "-", $date );
		$sql_date = $year . '-' . $month . '-' . $day . ' 00:00:00';
		$display_date = mysql2date( get_option( 'date_format' ), $sql_date );
	}

	if ( $title ) {
		$meta['title'] = $title;
	}

	if ( $display_date ) {
		$meta['date'] = $display_date;
	}

	if ( $catalog ) {
		$meta['catalog'] = $catalog;
	}

	if ( $format ) {
		$meta['format'] = $format;
	}

	if ( $itunes ) {
		$meta['itunes'] = $itunes;
	}

	if ( $google_play ) {
		$meta['google_play'] = $google_play;
	}

	if ( $amazon ) {
		$meta['amazon'] = $amazon;
	}

	if ( $spotify ) {
		$meta['spotify'] = $spotify;
	}

	if ( $buy ) {
		$meta['buy'] = $buy;
	}

	if ( $free ) {
		$meta['free'] = $free;
	}

	if ( $tracklist ) {
		$meta['tracklist'] = $tracklist;
	}

	return apply_filters( 'wd_meta', $meta );
}

/**
 * Get default release meta
 *
 * @since 1.2.6
 * @return object
 */
function wd_get_default_meta() {

	$meta = array(
		'title' => '',
		'date' => '',
		'catalog' => '',
		'format' => '',
		'itunes' => '',
		'google_play' => '',
		'amazon' => '',
		'spotify' => '',
		'buy' => '',
		'free' => '',
		'tracklist' => array(),
	);

	return $meta;
}

/**
 * Get release artist
 *
 * @since 1.2.6
 * @return string
 */
function wd_get_artist() {

	$post_id = get_the_ID();
	$band = '';

	if ( strip_tags( get_the_term_list( $post_id, 'band', '', ', ', '' ) ) != '' ) {

		$band =  '<strong>' . apply_filters( 'wolf_discography_band_string', esc_html( 'Band', 'wolf-discography' ) ) . ' </strong> : ' . strip_tags( get_the_term_list( $post_id, 'band', '', ', ', '' ) ) . '<br>';

	}

	if ( wolf_get_release_option( 'use_band_tax' ) ) {
		$band = get_the_term_list( $post_id, 'band', '<strong>' . apply_filters( 'wolf_discography_band_string', esc_html( 'Band', 'wolf-discography' ) ) . ' </strong> : ', ', ', '<br>' );
	}

	return $band;
}

/**
 * Get release label
 *
 * @since 1.2.6
 * @return string
 */
function wd_get_label() {

	$post_id = get_the_ID();
	$label = '';

	if ( ! taxonomy_exists( 'label' ) ) {
		return;
	}

	if ( wp_strip_all_tags( get_the_term_list( $post_id, 'label', '', ', ', '' ) ) != '' ) {

		$label =  '<strong>' . esc_html( 'Label', 'wolf-discography') . ' </strong> : ' . wp_strip_all_tags( get_the_term_list( $post_id, 'label', '', ', ', '' ) ) . '<br>';
	}

	if ( wolf_get_release_option( 'use_label_tax' ) ) {
		$label = get_the_term_list( $post_id, 'label', '<strong>' . esc_html( 'Label', 'wolf-discography') . ' </strong> : ', ', ', '<br>' );
	}

	return $label;
}

/**
 * Enqeue default style
 *
 * @since 1.2.6
 */
function wd_enqueue_style() {

	wp_enqueue_style( 'wolf-discography', WD_URI . '/assets/css/discography.css', array(), WD_VERSION, 'all' );
}
add_action( 'wp_enqueue_scripts',  'wd_enqueue_style' );

/**
 * Handle redirects before content is output - hooked into template_redirect so is_page works.
 */
function wolf_discography_template_redirect() {

	if ( is_page( wolf_discography_get_page_id() ) && ! post_password_required() ) {

		wolf_discography_get_template( 'discography-template.php' );
		exit();
	}
}

/**
 * Displays release navigation
 *
 * @return string
 */
function wolf_release_nav() {

	global $post;

	// Don't print empty markup if there's nowhere to navigate.
	$previous = get_adjacent_post( false, '', true );
	$next = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous )
		return;
	?>
	<nav class="release-navigation" role="navigation">
		<?php previous_post_link( '%link', _x( '<span class="meta-nav">&larr;</span> %title', 'Previous post link', 'wolf-discography' ) ); ?>
		<?php next_post_link( '%link', _x( '%title <span class="meta-nav">&rarr;</span>', 'Next post link', 'wolf-discography' ) ); ?>
	</nav><!-- .navigation -->
	<?php
}

/**
 * Displays release page navigation
 *
 * @return string
 */
function wolf_release_page_nav( $loop = null ) {

	if ( ! $loop ){
		global $wp_query;
		$max = $wp_query->max_num_pages;
	} else {
		$max = $loop->max_num_pages;
	}

	// Don't print empty markup if there's only one page.
	if ( $max < 2 )
		return;

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
