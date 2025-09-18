<?php
/**
 * Block Theme Template Support
 *
 * @package WolfDiscography
 * @subpackage BlockTheme
 * @since 2.0.0
 */

namespace Wolf_Discography\Block_Theme;

defined( 'ABSPATH' ) || exit;

class Template_Support {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_block_templates' ) );
		add_filter( 'theme_templates', array( $this, 'add_custom_templates' ), 10, 4 );
		add_filter( 'template_include', array( $this, 'load_custom_templates' ) );
	}

	/**
	 * Register block templates for discography
	 */
	public function register_block_templates() {
		if ( ! wp_is_block_theme() ) {
			return;
		}

		// Register custom templates for block themes
		add_theme_support( 'block-templates' );

		// Register template parts if needed
		add_theme_support( 'block-template-parts' );
	}

	/**
	 * Add custom templates to theme templates array
	 */
	public function add_custom_templates( $page_templates, $theme, $post, $post_type ) {
		if ( 'release' === $post_type ) {
			$page_templates['single-release-detailed'] = esc_html__( 'Release - Detailed', 'wolf-discography' );
			$page_templates['single-release-minimal'] = esc_html__( 'Release - Minimal', 'wolf-discography' );
		}

		return $page_templates;
	}

	/**
	 * Load custom templates
	 */
	public function load_custom_templates( $template ) {
		global $post;

		if ( ! $post ) {
			return $template;
		}

		// Handle single release templates
		if ( is_singular( 'release' ) ) {
			$custom_template = get_post_meta( $post->ID, '_wp_page_template', true );

			if ( $custom_template && 'default' !== $custom_template ) {
				$template_file = $this->locate_block_template( $custom_template );
				if ( $template_file ) {
					return $template_file;
				}
			}
		}

		// Handle archive templates
		if ( is_post_type_archive( 'release' ) || is_tax( array( 'band', 'label', 'release_genre' ) ) ) {
			$template_file = $this->locate_archive_template();
			if ( $template_file ) {
				return $template_file;
			}
		}

		return $template;
	}

	/**
	 * Locate block template file
	 */
	private function locate_block_template( $template_name ) {
		$plugin_path = WD_DIR . '/block-templates/';
		$theme_path = get_template_directory() . '/templates/';

		// Check theme first
		if ( file_exists( $theme_path . $template_name . '.html' ) ) {
			return $theme_path . $template_name . '.html';
		}

		// Check plugin
		if ( file_exists( $plugin_path . $template_name . '.html' ) ) {
			return $plugin_path . $template_name . '.html';
		}

		return false;
	}

	/**
	 * Locate archive template
	 */
	private function locate_archive_template() {
		$plugin_path = WD_DIR . '/block-templates/';
		$theme_path = get_template_directory() . '/templates/';

		$templates = array(
			'archive-release.html',
			'taxonomy-band.html',
			'taxonomy-label.html',
			'taxonomy-release_genre.html',
		);

		foreach ( $templates as $template ) {
			// Check theme first
			if ( file_exists( $theme_path . $template ) ) {
				return $theme_path . $template;
			}

			// Check plugin
			if ( file_exists( $plugin_path . $template ) ) {
				return $plugin_path . $template;
			}
		}

		return false;
	}

	/**
	 * Create default block templates if they don't exist
	 */
	public function create_default_templates() {
		$template_dir = WD_DIR . '/block-templates/';

		if ( ! file_exists( $template_dir ) ) {
			wp_mkdir_p( $template_dir );
		}

		$templates = array(
			'single-release.html' => $this->get_single_release_template(),
			'archive-release.html' => $this->get_archive_release_template(),
			'taxonomy-band.html' => $this->get_taxonomy_template( 'band' ),
			'taxonomy-label.html' => $this->get_taxonomy_template( 'label' ),
			'taxonomy-release_genre.html' => $this->get_taxonomy_template( 'genre' ),
		);

		foreach ( $templates as $filename => $content ) {
			$file_path = $template_dir . $filename;
			if ( ! file_exists( $file_path ) ) {
				file_put_contents( $file_path, $content );
			}
		}
	}

	/**
	 * Get single release template content
	 */
	private function get_single_release_template() {
		return '<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->
<main class="wp-block-group">
	<!-- wp:post-title {"level":1} /-->

	<!-- wp:post-featured-image {"isLink":false,"width":"600px","height":"600px"} /-->

	<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap"}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph -->
		<p><strong>Release Date:</strong> [release_date]</p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph -->
		<p><strong>Band:</strong> [release_bands]</p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph -->
		<p><strong>Label:</strong> [release_labels]</p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph -->
		<p><strong>Genre:</strong> [release_genres]</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:post-content /-->

	<!-- wp:wolf-discography/buy-links /-->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->';
	}

	/**
	 * Get archive template content
	 */
	private function get_archive_release_template() {
		return '<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->
<main class="wp-block-group">
	<!-- wp:query-title {"type":"archive"} /-->

	<!-- wp:wolf-discography/releases {"postsPerPage":12,"display":"grid","columns":4} /-->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->';
	}

	/**
	 * Get taxonomy template content
	 */
	private function get_taxonomy_template( $taxonomy_type ) {
		return '<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->
<main class="wp-block-group">
	<!-- wp:query-title {"type":"archive"} /-->

	<!-- wp:term-description /-->

	<!-- wp:wolf-discography/releases {"postsPerPage":12,"display":"grid","columns":3,"' . $taxonomy_type . '":"current"} /-->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->';
	}
}