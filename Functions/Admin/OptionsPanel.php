<?php
/**
 * Generic Options Panel Class
 *
 * Reusable options panel builder for WordPress plugins
 *
 * @package WolfDiscography
 * @subpackage Admin
 * @since 2.0.0
 */

namespace WolfDiscography\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Generic Options Panel class
 *
 * This class can be reused across multiple plugins by just changing the namespace
 */
class OptionsPanel {

    /**
     * Options panel arguments
     */
    protected $args = [];

    /**
     * Options panel title
     */
    protected $title = '';

    /**
     * Options panel slug
     */
    protected $slug = '';

    /**
     * Option name to use for saving options in the database (public for custom renderers)
     */
    public $option_name = '';

    /**
     * Option group name (public for custom renderers)
     */
    public $option_group_name = '';

    /**
     * User capability allowed to access the options page
     */
    protected $user_capability = '';

    /**
     * Array of settings (public for custom renderers)
     */
    public $settings = [];

    /**
     * Parent menu slug for submenu pages
     */
    protected $parent_slug = '';

    /**
     * Constructor
     *
     * @param array $args Panel arguments
     * @param array $settings Panel settings
     */
    public function __construct( array $args, array $settings ) {
        $this->args              = $args;
        $this->settings          = $settings;
        $this->title             = $this->args['title'] ?? esc_html__( 'Options', 'wolf-discography' );
        $this->slug              = $this->args['slug'] ?? sanitize_key( $this->title );
        $this->option_name       = $this->args['option_name'] ?? sanitize_key( $this->title );
        $this->option_group_name = $this->option_name . '_group';
        $this->user_capability   = $args['user_capability'] ?? 'manage_options';
        $this->parent_slug       = $args['parent_slug'] ?? '';

        add_action( 'admin_menu', [ $this, 'register_menu_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Register the menu page
     */
    public function register_menu_page() {
        if ( $this->parent_slug ) {
            // Add as submenu
            add_submenu_page(
                $this->parent_slug,
                $this->title,
                $this->title,
                $this->user_capability,
                $this->slug,
                [ $this, 'render_options_page' ]
            );
        } else {
            // Add as main menu
            add_menu_page(
                $this->title,
                $this->title,
                $this->user_capability,
                $this->slug,
                [ $this, 'render_options_page' ]
            );
        }
    }

    /**
     * Register the settings
     */
    public function register_settings() {
        register_setting( $this->option_group_name, $this->option_name, [
            'sanitize_callback' => [ $this, 'sanitize_fields' ],
            'default'           => $this->get_defaults(),
        ] );

        add_settings_section(
            $this->option_name . '_sections',
            false,
            false,
            $this->option_name
        );

        foreach ( $this->settings as $key => $args ) {
            $type = $args['type'] ?? 'text';
            $callback = "render_{$type}_field";

            if ( method_exists( $this, $callback ) ) {
                $tr_class = '';
                if ( array_key_exists( 'tab', $args ) ) {
                    $tr_class .= 'wd-tab-item wd-tab-item--' . sanitize_html_class( $args['tab'] );
                }

                add_settings_field(
                    $key,
                    $args['label'],
                    [ $this, $callback ],
                    $this->option_name,
                    $this->option_name . '_sections',
                    [
                        'label_for' => $key,
                        'class'     => $tr_class
                    ]
                );
            }
        }
    }

    /**
     * Sanitize all fields
     *
     * @param array $value Raw form values
     * @return array Sanitized values
     */
    public function sanitize_fields( $value ) {
        $value = (array) $value;
        $new_value = [];

        foreach ( $this->settings as $key => $args ) {
            $field_type = $args['type'];
            $new_option_value = $value[$key] ?? '';

            if ( $new_option_value ) {
                $sanitize_callback = $args['sanitize_callback'] ?? $this->get_sanitize_callback_by_type( $field_type );
                $new_value[$key] = call_user_func( $sanitize_callback, $new_option_value, $args );
            } elseif ( 'checkbox' === $field_type ) {
                $new_value[$key] = 0;
            }
        }

        return $new_value;
    }

    /**
     * Get sanitization callback by field type
     *
     * @param string $field_type Field type
     * @return callable Sanitization callback
     */
    protected function get_sanitize_callback_by_type( $field_type ) {
        switch ( $field_type ) {
            case 'select':
                return [ $this, 'sanitize_select_field' ];
            case 'textarea':
                return 'wp_kses_post';
            case 'checkbox':
                return [ $this, 'sanitize_checkbox_field' ];
            case 'email':
                return 'sanitize_email';
            case 'url':
                return 'esc_url_raw';
            case 'number':
                return 'absint';
            default:
            case 'text':
                return 'sanitize_text_field';
        }
    }

    /**
     * Get default values for all fields
     *
     * @return array Default values
     */
    protected function get_defaults() {
        $defaults = [];
        foreach ( $this->settings as $key => $args ) {
            $defaults[$key] = $args['default'] ?? '';
        }
        return $defaults;
    }

    /**
     * Sanitize checkbox field
     */
    protected function sanitize_checkbox_field( $value = '', $field_args = [] ) {
        return ( 'on' === $value || 1 == $value ) ? 1 : 0;
    }

    /**
     * Sanitize select field
     */
    protected function sanitize_select_field( $value = '', $field_args = [] ) {
        $choices = $field_args['choices'] ?? [];
        if ( array_key_exists( $value, $choices ) ) {
            return $value;
        }
        return '';
    }

    /**
     * Render the options page
     */
    public function render_options_page() {
        if ( ! current_user_can( $this->user_capability ) ) {
            return;
        }

        if ( isset( $_GET['settings-updated'] ) ) {
            add_settings_error(
               $this->option_name . '_messages',
               $this->option_name . '_message',
               esc_html__( 'Settings Saved', 'wolf-discography' ),
               'updated'
            );
        }

        settings_errors( $this->option_name . '_messages' );

        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

            <?php
            // Allow custom content before tabs
            do_action( "wolf_options_panel_before_tabs_{$this->slug}" );

            $this->render_tabs();

            // Allow custom content after tabs but before form
            do_action( "wolf_options_panel_before_form_{$this->slug}" );
            ?>

            <form action="options.php" method="post" class="wd-options-form">
                <?php
                    settings_fields( $this->option_group_name );
                    do_settings_sections( $this->option_name );
                    submit_button( esc_html__( 'Save Settings', 'wolf-discography' ) );
                ?>
            </form>

            <?php
            // Allow custom content after form
            do_action( "wolf_options_panel_after_form_{$this->slug}" );
            ?>
        </div>
        <?php
    }

	/**
	 * Render tabs navigation with dependency handling
	 */
	protected function render_tabs() {
		if ( empty( $this->args['tabs'] ) ) {
			return;
		}

		$tabs = $this->args['tabs'];
		?>
		<style>
		.wd-tab-item { display: none; }
		.wd-field-hidden { display: none !important; }
		</style>

		<h2 class="nav-tab-wrapper wd-tabs"><?php
			$first_tab = true;
			foreach ( $tabs as $id => $label ) { ?>
				<a href="#" data-tab="<?php echo esc_attr( $id ); ?>" class="nav-tab<?php echo ( $first_tab ) ? ' nav-tab-active' : ''; ?>">
					<?php echo esc_html( $label ); ?>
				</a>
				<?php
				$first_tab = false;
			}
		?></h2>

		<script>
			( function() {
				'use strict';

				// Handle tab switching
				function handleTabSwitch(targetTab) {
					// Remove active class from all tabs
					document.querySelectorAll( '.wd-tabs a' ).forEach( function( tablink ) {
						tablink.classList.remove( 'nav-tab-active' );
					});

					// Add active class to clicked tab
					var targetTabElement = document.querySelector('.wd-tabs a[data-tab="' + targetTab + '"]');
					if (targetTabElement) {
						targetTabElement.classList.add( 'nav-tab-active' );
					}

					// Show/hide tab content
					document.querySelectorAll( '.wd-options-form .wd-tab-item' ).forEach( function( item ) {
						if ( item.classList.contains( 'wd-tab-item--' + targetTab ) ) {
							item.style.display = 'table-row';
						} else {
							item.style.display = 'none';
						}
					});

					// Re-run dependency checks after tab switch
					setTimeout(handleFieldDependencies, 10);
				}

				document.addEventListener( 'click', function( event ) {
					var target = event.target;
					if ( ! target.closest( '.wd-tabs a' ) ) {
						return;
					}
					event.preventDefault();

					var targetTab = target.getAttribute( 'data-tab' );
					handleTabSwitch(targetTab);
				});

				// Handle field dependencies
				function handleFieldDependencies() {
					// Build dependency relationships
					var dependencies = {};

					<?php foreach ( $this->settings as $key => $args ) :
						if ( isset( $args['depends_on'] ) ) { ?>

					var parentFieldId_<?php echo esc_js( $key ); ?> = '<?php echo esc_js( $args['depends_on']['field'] ); ?>';
					var dependentFieldId_<?php echo esc_js( $key ); ?> = '<?php echo esc_js( $key ); ?>';
					var expectedValue_<?php echo esc_js( $key ); ?> = <?php echo json_encode( $args['depends_on']['value'] ); ?>;

					if ( !dependencies[parentFieldId_<?php echo esc_js( $key ); ?>] ) {
						dependencies[parentFieldId_<?php echo esc_js( $key ); ?>] = [];
					}
					dependencies[parentFieldId_<?php echo esc_js( $key ); ?>].push({
						fieldId: dependentFieldId_<?php echo esc_js( $key ); ?>,
						expectedValue: expectedValue_<?php echo esc_js( $key ); ?>
					});

					<?php } ?>
					<?php endforeach; ?>

					// Process each parent field and its dependents
					Object.keys(dependencies).forEach(function(parentFieldId) {
						var parentField = document.getElementById(parentFieldId);
						if (!parentField) {
							return;
						}

						function toggleDependentFields() {
							var currentValue = '';

							if (parentField.type === 'checkbox') {
								currentValue = parentField.checked ? '1' : '0';
							} else {
								currentValue = parentField.value;
							}

							// Process all dependent fields for this parent
							dependencies[parentFieldId].forEach(function(dependent) {
								var dependentField = document.getElementById(dependent.fieldId);
								if (!dependentField) {
									return;
								}

								var dependentRow = dependentField.closest('tr');
								if (!dependentRow) {
									return;
								}

								var shouldShow = false;

								if (Array.isArray(dependent.expectedValue)) {
									// Multiple accepted values
									shouldShow = dependent.expectedValue.indexOf(currentValue) !== -1;
								} else {
									// Single expected value
									shouldShow = currentValue === dependent.expectedValue;
								}

								if (shouldShow) {
									dependentRow.classList.remove('wd-field-hidden');
								} else {
									dependentRow.classList.add('wd-field-hidden');
								}
							});
						}

						// Remove existing event listeners to prevent duplicates
						var newToggleFunction = function() {
							toggleDependentFields();
						};

						// Store reference to remove old listeners
						if (parentField.wdToggleFunction) {
							parentField.removeEventListener('change', parentField.wdToggleFunction);
						}
						parentField.wdToggleFunction = newToggleFunction;

						// Add event listener to parent field
						parentField.addEventListener('change', newToggleFunction);

						// Run initial check
						toggleDependentFields();
					});
				}

				// Initialize first tab and dependencies on page load
				document.addEventListener( 'DOMContentLoaded', function() {
					// Show first tab
					var firstTab = document.querySelector( '.wd-tabs .nav-tab' );
					if ( firstTab ) {
						var firstTabId = firstTab.getAttribute('data-tab');
						handleTabSwitch(firstTabId);
					}

					// Initialize field dependencies
					setTimeout(function() {
						handleFieldDependencies();
					}, 100);
				});
			})();
		</script>
		<?php
	}

    /**
     * Get option value (public so custom field renderers can access it)
     *
     * @param string $option_name Option name
     * @return mixed Option value
     */
    public function get_option_value( $option_name ) {
        $option = get_option( $this->option_name, [] );
        if ( ! is_array( $option ) || ! array_key_exists( $option_name, $option ) ) {
            return $this->settings[$option_name]['default'] ?? '';
        }
        return $option[$option_name];
    }

    /**
     * Render text field
     */
    public function render_text_field( $args ) {
        $option_name = $args['label_for'];
        $value       = $this->get_option_value( $option_name );
        $description = $this->settings[$option_name]['description'] ?? '';
        $placeholder = $this->settings[$option_name]['placeholder'] ?? '';
        $class       = $this->settings[$option_name]['class'] ?? 'regular-text';
        ?>
        <input
            type="text"
            id="<?php echo esc_attr( $option_name ); ?>"
            name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $option_name ); ?>]"
            value="<?php echo esc_attr( $value ); ?>"
            placeholder="<?php echo esc_attr( $placeholder ); ?>"
            class="<?php echo esc_attr( $class ); ?>">
        <?php if ( $description ) { ?>
            <p class="description"><?php echo wp_kses_post( $description ); ?></p>
        <?php } ?>
        <?php
    }

    /**
     * Render textarea field
     */
    public function render_textarea_field( $args ) {
        $option_name = $args['label_for'];
        $value       = $this->get_option_value( $option_name );
        $description = $this->settings[$option_name]['description'] ?? '';
        $rows        = $this->settings[$option_name]['rows'] ?? '4';
        $cols        = $this->settings[$option_name]['cols'] ?? '50';
        $class       = $this->settings[$option_name]['class'] ?? 'large-text';
        ?>
        <textarea
            id="<?php echo esc_attr( $option_name ); ?>"
            rows="<?php echo esc_attr( absint( $rows ) ); ?>"
            cols="<?php echo esc_attr( absint( $cols ) ); ?>"
            name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $option_name ); ?>]"
            class="<?php echo esc_attr( $class ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
        <?php if ( $description ) { ?>
            <p class="description"><?php echo wp_kses_post( $description ); ?></p>
        <?php } ?>
        <?php
    }

    /**
     * Render checkbox field
     */
    public function render_checkbox_field( $args ) {
        $option_name = $args['label_for'];
        $value       = $this->get_option_value( $option_name );
        $description = $this->settings[$option_name]['description'] ?? '';
        ?>
        <label>
            <input
                type="checkbox"
                id="<?php echo esc_attr( $option_name ); ?>"
                name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $option_name ); ?>]"
                <?php checked( $value, 1, true ); ?>
            >
            <?php echo wp_kses_post( $description ); ?>
        </label>
        <?php
    }

    /**
     * Render select field
     */
    public function render_select_field( $args ) {
        $option_name = $args['label_for'];
        $value       = $this->get_option_value( $option_name );
        $description = $this->settings[$option_name]['description'] ?? '';
        $choices     = $this->settings[$option_name]['choices'] ?? [];
        ?>
        <select
            id="<?php echo esc_attr( $option_name ); ?>"
            name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $option_name ); ?>]"
        >
            <?php foreach ( $choices as $choice_value => $choice_label ) { ?>
                <option value="<?php echo esc_attr( $choice_value ); ?>" <?php selected( $choice_value, $value, true ); ?>>
                    <?php echo esc_html( $choice_label ); ?>
                </option>
            <?php } ?>
        </select>
        <?php if ( $description ) { ?>
            <p class="description"><?php echo wp_kses_post( $description ); ?></p>
        <?php } ?>
        <?php
    }

    /**
     * Render number field
     */
    public function render_number_field( $args ) {
        $option_name = $args['label_for'];
        $value       = $this->get_option_value( $option_name );
        $description = $this->settings[$option_name]['description'] ?? '';
        $min         = $this->settings[$option_name]['min'] ?? '';
        $max         = $this->settings[$option_name]['max'] ?? '';
        $step        = $this->settings[$option_name]['step'] ?? '';
        ?>
        <input
            type="number"
            id="<?php echo esc_attr( $option_name ); ?>"
            name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $option_name ); ?>]"
            value="<?php echo esc_attr( $value ); ?>"
            min="<?php echo esc_attr( $min ); ?>"
            max="<?php echo esc_attr( $max ); ?>"
            step="<?php echo esc_attr( $step ); ?>"
            class="small-text">
        <?php if ( $description ) { ?>
            <p class="description"><?php echo wp_kses_post( $description ); ?></p>
        <?php } ?>
        <?php
    }

    /**
     * Render page select field
     */
    public function render_page_select_field( $args ) {
        $option_name = $args['label_for'];
        $value       = $this->get_option_value( $option_name );
        $description = $this->settings[$option_name]['description'] ?? '';

        $pages = get_pages();
        ?>
        <select
            id="<?php echo esc_attr( $option_name ); ?>"
            name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $option_name ); ?>]"
        >
            <option value=""><?php esc_html_e( '- Select Page -', 'wolf-discography' ); ?></option>
            <?php foreach ( $pages as $page ) { ?>
                <option value="<?php echo esc_attr( $page->ID ); ?>" <?php selected( $page->ID, $value, true ); ?>>
                    <?php
                    if ( get_post_field( 'post_parent', $page->ID ) ) {
                        echo '&nbsp;&nbsp;&nbsp; ';
                    }
                    echo esc_html( $page->post_title );
                    ?>
                </option>
            <?php } ?>
        </select>
        <?php if ( $description ) { ?>
            <p class="description"><?php echo wp_kses_post( $description ); ?></p>
        <?php } ?>
        <?php
    }

    /**
     * Render license field (calls action hook for custom implementation)
     */
    public function render_license_field( $args ) {
        // Allow custom license field rendering via action hook
        do_action( 'wolf_options_panel_render_license_field', $args, $this );

        // Fallback to regular text field if no custom renderer is hooked
        if ( ! has_action( 'wolf_options_panel_render_license_field' ) ) {
            $this->render_text_field( $args );
        }
    }
}