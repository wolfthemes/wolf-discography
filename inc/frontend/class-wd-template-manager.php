<?php
/**
 * Enhanced Template System for Wolf Discography
 * Add this to your main plugin file or create a new inc/class-wd-templates.php
 *
 * @author WolfThemes
 * @category Core
 * @package WolfDiscography/Templates
 * @version 1.6.0
 * @since 1.6.0
 */

defined( 'ABSPATH' ) || exit;

class WD_Template_Manager {

    public function __construct() {
        add_action( 'init', array( $this, 'register_block_templates' ) );
        add_action( 'init', array( $this, 'handle_templates' ) );
        add_filter( 'theme_templates', array( $this, 'add_templates_to_dropdown' ), 10, 4 );


	}
	public function handle_templates() {
		// debug( is_singular('release') );

		if ( ! $this->is_block_page() ) {
			//debug( 'so' );
			add_action( 'template_redirect', array( $this, 'discography_template_redirect' ), 40 );

			add_filter( 'template_include', array( $this, 'handle_classic_theme' ) );
		} else {
			add_action( 'template_redirect', array( $this, 'handle_block_theme' ), 40 );
		}

	}

	public function is_block_page() {
		return wp_is_block_theme();
	}

    /**
     * Check if we're on a discography-related page
     */
    private function is_discography_page() {
        return (
            is_singular( 'release' ) ||
            is_post_type_archive( 'release' ) ||
            is_tax( array( 'band', 'label', 'release_genre' ) ) ||
            ( function_exists( 'wolf_discography_get_page_id' ) && is_page( wolf_discography_get_page_id() ) )
        );
    }

    /**
     * Handle block theme templates
     */
    public function handle_block_theme() {

        // For single release pages
        if ( is_singular( 'release' ) ) {

            $template = $this->get_block_template( 'single-release' );
            if ( $template ) {
                $this->render_block_template( $template );
                return;
            }
        }

        // For archive pages
        if ( is_post_type_archive( 'release' ) ) {
            $template = $this->get_block_template( 'archive-release' );
            if ( $template ) {
                $this->render_block_template( $template );
                return;
            }
        }

        // For taxonomy pages
        if ( is_tax( array( 'band', 'label', 'release_genre' ) ) ) {
            $term = get_queried_object();
            $template = $this->get_block_template( 'taxonomy-' . $term->taxonomy );
            if ( $template ) {
                $this->render_block_template( $template );
                return;
            }
        }

        // Fallback: inject content into existing page
        add_filter( 'the_content', array( $this, 'inject_discography_content' ), 20 );
    }

    /**
     * Register block templates for discography
     */
    public function register_block_templates() {

        if ( ! wp_is_block_theme() ) {
            return;
        }

        // Register single release template
        $this->register_single_release_template();

        // Register archive template
        $this->register_archive_template();

        // Register taxonomy templates
        $this->register_taxonomy_templates();
    }

    /**
     * Register single release block template
     */
    private function register_single_release_template() {

        $template_content = $this->get_single_release_template_content();

        register_block_template( 'wolf-discography//single-release', array(
            'title'         => __( 'Single Release', 'wolf-discography' ),
            'description'   => __( 'Template for single release pages', 'wolf-discography' ),
            'content'       => $template_content,
            'post_types'    => array( 'release' ),
        ) );
    }

    /**
     * Register archive block template
     */
    private function register_archive_template() {

        $template_content = $this->get_archive_template_content();

        register_block_template( 'wolf-discography//archive-release', array(
            'title'         => __( 'Release Archive', 'wolf-discography' ),
            'description'   => __( 'Template for release archive pages', 'wolf-discography' ),
            'content'       => $template_content,
            'post_types'    => array( 'release' ),
        ) );
    }

    /**
     * Register taxonomy templates
     */
    private function register_taxonomy_templates() {

        $taxonomies = array( 'band', 'label', 'release_genre' );

        foreach ( $taxonomies as $taxonomy ) {
            if ( taxonomy_exists( $taxonomy ) ) {
                register_block_template( 'wolf-discography//taxonomy-' . $taxonomy, array(
                    'title'         => sprintf( __( '%s Archive', 'wolf-discography' ), ucfirst( $taxonomy ) ),
                    'description'   => sprintf( __( 'Template for %s taxonomy pages', 'wolf-discography' ), $taxonomy ),
                    'content'       => $this->get_taxonomy_template_content( $taxonomy ),
                ) );
            }
        }
    }

    /**
     * Get block template
     */
    private function get_block_template( $template_slug ) {
        return get_block_template( get_stylesheet() . '//' . $template_slug );
    }

    /**
     * Render block template
     */
    private function render_block_template( $template ) {
        global $_wp_current_template_content;
        $_wp_current_template_content = $template->content;

        if ( have_posts() ) {
            the_post();
        }

        include ABSPATH . WPINC . '/template-canvas.php';
        exit;
    }

    /**
     * Add templates to theme template dropdown
     */
    public function add_templates_to_dropdown( $templates, $theme, $post, $post_type ) {

        if ( ! wp_is_block_theme() ) {
            return $templates;
        }

        if ( $post_type === 'release' ) {
            $templates['wolf-discography//single-release'] = __( 'Single Release (Wolf Discography)', 'wolf-discography' );
            $templates['wolf-discography//archive-release'] = __( 'Release Archive (Wolf Discography)', 'wolf-discography' );
        }

        return $templates;
    }

    /**
     * Inject discography content (fallback method)
     */
    public function inject_discography_content( $content ) {

        if ( ! $this->is_discography_page() ) {
            return $content;
        }

        // Remove filter to prevent infinite loops
        remove_filter( 'the_content', array( $this, 'inject_discography_content' ), 20 );

        ob_start();

        if ( is_singular( 'release' ) ) {
            wolf_discography_get_template_part( 'content', 'single' );
            wolf_release_nav();
        } else {
            do_action( 'wolf_discography_before_loop_content' );
            do_action( 'wolf_discography_posts', array( 'el_id' => 'discography-index' ) );
            do_action( 'wolf_discography_after_loop_content' );
        }

        $discography_content = ob_get_clean();

        return $discography_content;
    }

    /**
     * Get single release template content for block themes
     */
    private function get_single_release_template_content() {
        return '<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<main class="wp-block-group" style="margin-top:0;margin-bottom:0">

    <!-- wp:query-title {"type":"archive","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"}}}} /-->

    <!-- wp:archive-description {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}}} /-->

    <!-- wp:html -->
    <?php
    do_action( "wolf_discography_before_main_content" );
	do_action( "wolf_discography_single_content" );
    do_action( "wolf_discography_after_main_content" );
    ?>
    <!-- /wp:html -->

</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->';
    }

    /**
     * Get archive template content for block themes
     */
    private function get_archive_template_content() {
        return '<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<main class="wp-block-group" style="margin-top:0;margin-bottom:0">

    <!-- wp:query-title {"type":"archive","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"}}}} /-->

    <!-- wp:archive-description {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}}} /-->

    <!-- wp:html -->
    <?php
    do_action( "wolf_discography_before_loop_content" );
    do_action( "wolf_discography_posts", array( "el_id" => "discography-archive" ) );
    do_action( "wolf_discography_after_loop_content" );
    ?>
    <!-- /wp:html -->

</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->';
    }

    /**
     * Get taxonomy template content for block themes
     */
    private function get_taxonomy_template_content( $taxonomy ) {
        return '<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<main class="wp-block-group" style="margin-top:0;margin-bottom:0">

    <!-- wp:query-title {"type":"archive","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}}} /-->

    <!-- wp:term-description {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}}} /-->

    <!-- wp:html -->
    <?php
    do_action( "wolf_discography_before_loop_content" );
    do_action( "wolf_discography_posts", array( "el_id" => "discography-taxonomy-' . $taxonomy . '" ) );
    do_action( "wolf_discography_after_loop_content" );
    ?>
    <!-- /wp:html -->

</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->';
    }

	public function discography_template_redirect() {
		if ( is_page( wolf_discography_get_page_id() ) && ! post_password_required() ) {
			wolf_discography_get_template( 'discography-template.php' );
			exit();
		}
	}

	public function handle_classic_theme( $template ) {

		$find = array( 'wolf-discography.php' ); // nope! not used
		$file = '';


		if ( is_single() && WD()->cpt_slug == get_post_type() ) {

			$file    = 'single-' . WD()->cpt_slug . '.php';
			$find[] = $file;
			$find[] = WD()->template_url . $file;

		} elseif ( is_tax( 'band' ) || is_tax( 'label' ) || is_tax( 'release_genre' ) ) {

			$term = get_queried_object();

			$file 	= 'taxonomy-' . $term->taxonomy . '.php';
			$find[] 	= 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] 	= WD()->template_url . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] 	= $file;
			$find[] 	= WD()->template_url . $file;

		} elseif ( is_post_type_archive( WD()->cpt_slug ) ) {

			$file = 'archive-' . WD()->cpt_slug . '.php';
			$find[] = $file;
			$find[] = WD()->template_url . $file;

		}

		if ( $file ) {
			$template = locate_template( $find );
			if ( ! $template ) $template = WD()->plugin_path() . '/templates/' . $file;
		}

		return $template;
	}

}

// Initialize the template manager
new WD_Template_Manager();