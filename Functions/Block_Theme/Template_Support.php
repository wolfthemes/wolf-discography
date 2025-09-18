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

	const PLUGIN_SLUG = 'wolf-discography';
	const TEMPLATES_ROOT_DIR = 'block-templates';

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'register_block_templates' ) );
		add_filter( 'pre_get_block_file_template', array( $this, 'get_block_file_template' ), 10, 3 );
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
	 * Get block file template - similar to WooCommerce's approach
	 */
	public function get_block_file_template( $template, $id, $template_type ) {
		$template_name_parts = explode( '//', $id );

		if ( count( $template_name_parts ) < 2 ) {
			return $template;
		}

		list( $template_id, $template_slug ) = $template_name_parts;

		// Only handle our templates
		if ( self::PLUGIN_SLUG !== $template_id ) {
			return $template;
		}

		// Check if template is available
		if ( ! $this->block_template_is_available( $template_slug, $template_type ) ) {
			return $template;
		}

		$template_file_path = $this->get_template_file_path( $template_slug );

		if ( $template_file_path ) {
			$template_object = $this->create_template_object( $template_file_path, $template_type, $template_slug );
			return $this->build_template_result_from_file( $template_object, $template_type );
		}

		return $template;
	}

	/**
	 * Add block templates to query results
	 */
	public function add_block_templates( $query_result, $query, $template_type ) {
		if ( 'wp_template' !== $template_type ) {
			return $query_result;
		}

		$slugs = isset( $query['slug__in'] ) ? $query['slug__in'] : array();
		$template_files = $this->get_block_templates( $slugs, $template_type );

		foreach ( $template_files as $template_file ) {
			// Check if template already exists in results
			$template_already_exists = false;
			foreach ( $query_result as $existing_template ) {
				if ( $existing_template->slug === $template_file->slug ) {
					$template_already_exists = true;
					break;
				}
			}

			if ( ! $template_already_exists ) {
				$query_result[] = $template_file;
			}
		}

		return $query_result;
	}

	/**
	 * Get available block templates
	 */
	public function get_block_templates( $slugs = array(), $template_type = 'wp_template' ) {
		$templates = array();
		$template_files = $this->get_template_files();

		foreach ( $template_files as $template_slug => $template_file ) {
			// Filter by slugs if provided
			if ( ! empty( $slugs ) && ! in_array( $template_slug, $slugs, true ) ) {
				continue;
			}

			// Check if theme has this template (theme overrides plugin)
			if ( $this->theme_has_template( $template_slug ) ) {
				continue;
			}

			$template_object = $this->create_template_object( $template_file, $template_type, $template_slug );
			$built_template = $this->build_template_result_from_file( $template_object, $template_type );

			if ( $built_template ) {
				$templates[] = $built_template;
			}
		}

		return $templates;
	}

	/**
	 * Get template files mapping
	 */
	private function get_template_files() {
		$template_dir = WD_DIR . '/' . self::TEMPLATES_ROOT_DIR . '/';
		$templates = array();

		$template_files = array(
			'archive-release' => 'archive-release.html',
			'single-release' => 'single-release.html',
			'taxonomy-band' => 'taxonomy-band.html',
			'taxonomy-label' => 'taxonomy-label.html',
			'taxonomy-release_genre' => 'taxonomy-release_genre.html',
		);

		foreach ( $template_files as $slug => $filename ) {
			$file_path = $template_dir . $filename;
			if ( file_exists( $file_path ) ) {
				$templates[ $slug ] = $file_path;
			}
		}

		// Add discography page template
		$discography_page_id = Core::get_discography_page_id();
		if ( $discography_page_id && file_exists( $template_dir . 'archive-release.html' ) ) {
			$templates[ 'page-' . $discography_page_id ] = $template_dir . 'archive-release.html';
		}

		return $templates;
	}

	/**
	 * Check if theme has template
	 */
	private function theme_has_template( $template_slug ) {
		$theme_template_path = get_template_directory() . '/templates/' . $template_slug . '.html';
		return file_exists( $theme_template_path );
	}

	/**
	 * Get template file path
	 */
	private function get_template_file_path( $template_slug ) {
		$template_files = $this->get_template_files();
		return isset( $template_files[ $template_slug ] ) ? $template_files[ $template_slug ] : false;
	}

	/**
	 * Check if template is available
	 */
	private function block_template_is_available( $template_slug, $template_type = 'wp_template' ) {
		return (bool) $this->get_template_file_path( $template_slug );
	}

	/**
	 * Create template object
	 */
	private function create_template_object( $template_file_path, $template_type, $template_slug ) {
		return (object) array(
			'slug' => $template_slug,
			'path' => $template_file_path,
			'type' => $template_type,
		);
	}

	/**
	 * Build template result from file
	 */
	private function build_template_result_from_file( $template_object, $template_type ) {
		if ( ! file_exists( $template_object->path ) ) {
			return null;
		}

		$template_content = file_get_contents( $template_object->path );

		$template = new \WP_Block_Template();
		$template->id = self::PLUGIN_SLUG . '//' . $template_object->slug;
		$template->theme = self::PLUGIN_SLUG;
		$template->slug = $template_object->slug;
		$template->source = 'plugin';
		$template->type = $template_type;
		$template->title = $this->get_template_title( $template_object->slug );
		$template->content = $template_content;
		$template->status = 'publish';
		$template->has_theme_file = false;
		$template->is_custom = false;
		$template->wp_id = $template->id;
		$template->plugin = self::PLUGIN_SLUG;

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

		// Handle page templates
		if ( strpos( $slug, 'page-' ) === 0 ) {
			return __( 'Discography Page', 'wolf-discography' );
		}

		return $titles[ $slug ] ?? ucfirst( str_replace( '-', ' ', $slug ) );
	}

	/**
	 * Create template files if they don't exist
	 */
	public function create_template_files() {
		if ( ! wp_is_block_theme() ) {
			return;
		}

		$template_dir = WD_DIR . '/' . self::TEMPLATES_ROOT_DIR . '/';

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