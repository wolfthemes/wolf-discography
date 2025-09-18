<?php
/**
 * Block Theme Template Support
 *
 * @package WolfDiscography
 * @subpackage BlockTheme
 * @since 2.0.0
 */

namespace Wolf_Discography\Block_Theme;

use Wolf_Discography\Core\Core;

defined( 'ABSPATH' ) || exit;

class Template_Support {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'register_block_templates' ) );
		add_filter( 'get_block_templates', array( $this, 'add_block_templates' ), 10, 3 );
		add_action( 'wp_loaded', array( $this, 'create_template_files' ) );
	}

	/**
	 * Register block templates for discography
	 */
	public function register_block_templates() {
		if ( ! wp_is_block_theme() ) {
			return;
		}

		add_theme_support( 'block-templates' );
		add_theme_support( 'block-template-parts' );
	}

	/**
	 * Add our custom templates to the block template system
	 */
	public function add_block_templates( $query_result, $query, $template_type ) {
		if ( 'wp_template' !== $template_type ) {
			return $query_result;
		}

		$template_files = $this->get_template_files();

		foreach ( $template_files as $template_file ) {
			// Check if template already exists in query result
			$template_already_exists = false;
			foreach ( $query_result as $existing_template ) {
				if ( $existing_template->slug === $template_file['slug'] ) {
					$template_already_exists = true;
					break;
				}
			}

			// Only add if it doesn't already exist
			if ( ! $template_already_exists ) {
				$query_result[] = $this->create_block_template_object( $template_file );
			}
		}

		return $query_result;
	}

	/**
	 * Get template files from plugin
	 */
	private function get_template_files() {
		$template_dir = WD_DIR . '/block-templates/';
		$templates = array();

		$template_files = array(
			'archive-release.html',
			'single-release.html',
			'taxonomy-band.html',
			'taxonomy-label.html',
			'taxonomy-release_genre.html',
		);

		foreach ( $template_files as $file ) {
			$file_path = $template_dir . $file;
			if ( file_exists( $file_path ) ) {
				$templates[] = array(
					'slug' => str_replace( '.html', '', $file ),
					'path' => $file_path,
					'type' => 'wp_template',
				);
			}
		}

		// Add discography page template if page is set
		$discography_page_id = Core::get_discography_page_id();
		if ( $discography_page_id && file_exists( $template_dir . 'archive-release.html' ) ) {
			$templates[] = array(
				'slug' => 'page-' . $discography_page_id,
				'path' => $template_dir . 'archive-release.html',
				'type' => 'wp_template',
			);
		}

		return $templates;
	}

	/**
	 * Create block template object
	 */
	private function create_block_template_object( $template_file ) {
		$template_content = file_get_contents( $template_file['path'] );

		$template = new \WP_Block_Template();
		$template->id = 'wolf-discography//' . $template_file['slug'];
		$template->theme = 'wolf-discography';
		$template->slug = $template_file['slug'];
		$template->source = 'plugin';
		$template->type = $template_file['type'];
		$template->title = $this->get_template_title( $template_file['slug'] );
		$template->content = $template_content;
		$template->status = 'publish';
		$template->has_theme_file = false;
		$template->is_custom = true;
		$template->wp_id = $template->id;

		return $template;
	}

	/**
	 * Get template title
	 */
	private function get_template_title( $slug ) {
		$titles = array(
			'archive-release' => __( 'Release Archive', 'wolf-discography' ),
			'single-release' => __( 'Single Release', 'wolf-discography' ),
			'taxonomy-band' => __( 'Band Archive', 'wolf-discography' ),
			'taxonomy-label' => __( 'Label Archive', 'wolf-discography' ),
			'taxonomy-release_genre' => __( 'Genre Archive', 'wolf-discography' ),
		);

		return $titles[ $slug ] ?? ucfirst( str_replace( '-', ' ', $slug ) );
	}

	/**
	 * Create template files if they don't exist
	 */
	public function create_template_files() {
		if ( ! wp_is_block_theme() ) {
			return;
		}

		$template_dir = WD_DIR . '/block-templates/';

		if ( ! file_exists( $template_dir ) ) {
			wp_mkdir_p( $template_dir );
		}

		$templates = array(
			'archive-release.html' => $this->get_archive_template(),
			'single-release.html' => $this->get_single_template(),
			'taxonomy-band.html' => $this->get_taxonomy_template( 'band' ),
			'taxonomy-label.html' => $this->get_taxonomy_template( 'label' ),
			'taxonomy-release_genre.html' => $this->get_taxonomy_template( 'release_genre' ),
		);

		foreach ( $templates as $filename => $content ) {
			$file_path = $template_dir . $filename;
			if ( ! file_exists( $file_path ) ) {
				file_put_contents( $file_path, $content );
			}
		}
	}

	/**
	 * Get archive template content
	 */
	private function get_archive_template() {
		return '<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->
<main class="wp-block-group">
	<!-- wp:query-title {"type":"archive"} /-->

	<!-- wp:html -->
	<div class="wolf-discography-releases">
		[wolf_discography_releases]
	</div>
	<!-- /wp:html -->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->';
	}

	/**
	 * Get single template content
	 */
	private function get_single_template() {
		return '<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->
<main class="wp-block-group">
	<!-- wp:post-title {"level":1} /-->

	<!-- wp:post-featured-image /-->

	<!-- wp:post-content /-->

	<!-- wp:html -->
	<div class="wolf-discography-release-meta">
		[wolf_release_meta]
	</div>
	<!-- /wp:html -->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->';
	}

	/**
	 * Get taxonomy template content
	 */
	private function get_taxonomy_template( $taxonomy ) {
		return '<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->
<main class="wp-block-group">
	<!-- wp:query-title {"type":"archive"} /-->

	<!-- wp:term-description /-->

	<!-- wp:html -->
	<div class="wolf-discography-releases">
		[wolf_discography_releases ' . $taxonomy . '="current"]
	</div>
	<!-- /wp:html -->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->';
	}
}