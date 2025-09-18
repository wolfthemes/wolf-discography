<?php
/**
 * Elementor Helper
 *
 * @package WolfDiscography
 * @subpackage PageBuilders
 * @since 2.0.0
 */

namespace WolfDiscography\Page_Builders;

defined( 'ABSPATH' ) || exit;
defined( 'ELEMENTOR_VERSION' ) || exit;

/**
 * Elementor Helper Class
 */
class Elementor_Helper {

	/**
	 * Covert raw params to Elementor format params
	 *
	 * @param object $widget The widget object.
	 * @param array  $params The parameters to pass.
	 * @return void
	 */
	public static function convert_params_to_elementor( $widget, $params = array() ) {

		if ( array() === $params ) {
			$params = $widget->params['params'];
		}

		$elementor_types = array(
			'text'        => \Elementor\Controls_Manager::TEXT,
			'textarea'    => \Elementor\Controls_Manager::TEXTAREA,
			'text_html'   => \Elementor\Controls_Manager::WYSIWYG,
			'font_family' => \Elementor\Controls_Manager::FONT,
			'select'      => \Elementor\Controls_Manager::SELECT,
			'checkbox'    => \Elementor\Controls_Manager::SWITCHER,
			'link'        => \Elementor\Controls_Manager::URL,
			'image'       => \Elementor\Controls_Manager::MEDIA,
			'audio'       => \Elementor\Controls_Manager::MEDIA,
			'video'       => \Elementor\Controls_Manager::MEDIA,
			'icon'        => \Elementor\Controls_Manager::ICON,
			'colorpicker' => \Elementor\Controls_Manager::COLOR,
			'number'      => \Elementor\Controls_Manager::NUMBER,
			'date'        => \Elementor\Controls_Manager::DATE_TIME,
			'background'  => '',
		);

		foreach ( $params as $p ) {

			if ( isset( $p['page_builder'] ) && 'vc' === $p['page_builder'] ) {
				continue;
			}

			$field_params = array();

			$type                        = isset( $p['type'] ) ? $p['type'] : '';
			$field_params['label']       = isset( $p['label'] ) ? $p['label'] : '';
			$field_params['placeholder'] = isset( $p['placeholder'] ) ? $p['placeholder'] : '';
			$field_params['selector']    = isset( $p['selector'] ) ? $p['selector'] : '';

			if ( isset( $p['default'] ) ) {
				$field_params['default'] = $p['default'];
			}

			if ( 'repeater' === $type ) {

				$repeater = new \Elementor\Repeater();

				foreach ( $p['params'] as $r_param ) {

					$r_type = ( isset( $r_param['type'] ) ) ? $elementor_types[ $r_param['type'] ] : 'text';

					if ( isset( $r_param['type'] ) && 'background' === $r_param['type'] ) {

						$repeater->add_group_control(
							\Elementor\Group_Control_Background::get_type(),
							array(
								'name'      => $r_param['param_name'],
								'label'     => esc_html__( 'Background', 'wolf-discography' ),
								'types'     => array( 'classic', 'video' ),
								'selectors' => $r_param['selectors'],
							)
						);

					} else {

						$r_params = array(
							'label'       => $r_param['label'],
							'type'        => $r_type,
							// 'default'     => ( isset( $r_param['default'] ) ) ? $r_param['default'] : array(),
							'placeholder' => ( isset( $r_param['placeholder'] ) ) ? $r_param['placeholder'] : '',
							'description' => ( isset( $r_param['description'] ) ) ? $r_param['description'] : '',
							'condition'   => ( isset( $r_param['condition'] ) ) ? $r_param['condition'] : array(),
							'conditions'  => ( isset( $r_param['conditions'] ) ) ? $r_param['conditions'] : array(),
							'label_block' => true,
						);

						if ( 'font' === $r_type ) {
							$r_params['selectors'] = ( isset( $r_param['selectors'] ) ) ? $r_param['selectors'] : '';
						}

						if ( 'select' === $r_type ) {
							$r_params['default'] = ( isset( $r_param['default'] ) ) ? $r_param['default'] : '';
						}

						if ( isset( $r_param['type'] ) ) {

							if ( 'audio' === $r_param['type'] ) {
								$r_params['media_type'] = 'audio';
							} elseif ( 'image' === $r_param['type'] ) {
								$r_params['media_type'] = 'image';
							} elseif ( 'video' === $r_param['type'] ) {
								$r_params['media_type'] = 'video';
							} elseif ( 'select' === $r_param['type'] ) {
								$r_params['options'] = $r_param['options'];
							}
						}

						// debug( $r_params );

						$repeater->add_control(
							$r_param['param_name'],
							$r_params
						);
					}
				}

				// die();

				$widget->add_control(
					$p['param_name'],
					array(
						'label'       => $p['label'],
						'type'        => \Elementor\Controls_Manager::REPEATER,
						'fields'      => $repeater->get_controls(),
						'default'     => ( isset( $p['defaults'] ) ) ? $p['defaults'] : array(),
						'condition'   => ( isset( $p['condition'] ) ) ? $p['condition'] : array(),
						'conditions'  => ( isset( $p['conditions'] ) ) ? $p['conditions'] : array(),
						'title_field' => '{{{ ' . $p['params'][0]['param_name'] . ' }}}',
					)
				);

			} elseif ( 'text' === $type ) {

				$field_params['type'] = \Elementor\Controls_Manager::TEXT;

				if ( isset( $p['ai'] ) ) {
					$field_params['ai'] = $p['ai'];
				}
			} elseif ( 'textarea' === $type ) {

				$field_params['type'] = \Elementor\Controls_Manager::TEXTAREA;

			} elseif ( 'text_html' === $type ) {

				$field_params['type'] = \Elementor\Controls_Manager::WYSIWYG;

			} elseif ( 'select' === $type ) {

				$field_params['type']    = \Elementor\Controls_Manager::SELECT;
				$field_params['options'] = $p['options'];

				/* Set the first option as default */
				if ( ! isset( $p['default'] ) ) {
					$field_params['default'] = array_key_first( $p['options'] );
				}
			} elseif ( 'choose' === $type ) {

				$field_params['type']    = \Elementor\Controls_Manager::CHOOSE;
				$field_params['options'] = $p['options'];

			} elseif ( 'checkbox' === $type ) {

				$field_params['type']         = \Elementor\Controls_Manager::SWITCHER;
				$field_params['label_on']     = ( isset( $p['label_on'] ) ) ? $p['label_on'] : esc_html__( 'Yes', 'wolf-discography' );
				$field_params['label_off']    = ( isset( $p['label_off'] ) ) ? $p['label_off'] : esc_html__( 'No', 'wolf-discography' );
				$field_params['return_value'] = ( isset( $p['return_value'] ) ) ? $p['return_value'] : 'yes';

			} elseif ( 'date' === $type ) {

				$field_params['type'] = \Elementor\Controls_Manager::DATE_TIME;

				if ( ! isset( $p['picker_options'] ) ) {
					$field_params['picker_options'] = array(
						'enableTime' => false,
					);
				}
			} elseif ( 'number' === $type ) {

				$field_params['type'] = \Elementor\Controls_Manager::NUMBER;

			} elseif ( 'font_family' === $type ) {

				$field_params['type'] = \Elementor\Controls_Manager::FONT;

			} elseif ( 'link' === $type ) {

				$field_params['type']        = \Elementor\Controls_Manager::URL;
				$field_params['placeholder'] = 'https://your-website.com';

			} elseif ( 'icon' === $type ) {

				$field_params['type']             = \Elementor\Controls_Manager::ICONS;
				$field_params['fa4compatibility'] = 'icon';

			} elseif ( 'animated_icon' === $type ) {

				$field_params['type'] = \Elementor\Controls_Manager::ICONS;

			} elseif ( 'colorpicker' === $type ) {

				$field_params['type'] = \Elementor\Controls_Manager::COLOR;

			} elseif ( 'number' === $type ) {

				$field_params['type'] = \Elementor\Controls_Manager::NUMBER;
				$field_params['min']  = ( isset( $p['min'] ) ) ? $p['min'] : 0;
				$field_params['max']  = ( isset( $p['max'] ) ) ? $p['max'] : 100;
				$field_params['step'] = ( isset( $p['step'] ) ) ? $p['step'] : 1;

			} elseif ( 'slider' === $type ) {

				$field_params['type'] = \Elementor\Controls_Manager::SLIDER;

				if ( isset( $p['default'] ) && ! is_array( $p['default'] ) ) {
					$field_params['default'] = array(
						'unit' => '%',
						'size' => $p['default'],
					);
				}

				$field_params['size_units'] = isset( $p['size_units'] ) ? $p['size_units'] : array( '%' );

				if ( isset( $p['range'] ) ) {
					$field_params['range'] = $p['range'];
				} else {
					$field_params['range']['%'] = array(
						'min'  => ( isset( $p['min'] ) ) ? $p['min'] : 0,
						'max'  => ( isset( $p['max'] ) ) ? $p['max'] : 100,
						'step' => ( isset( $p['step'] ) ) ? $p['step'] : 1,
					);
				}
			} elseif ( 'dimensions' === $type ) {

				$field_params['type']       = \Elementor\Controls_Manager::DIMENSIONS;
				$field_params['size_units'] = isset( $p['size_units'] ) ? $p['size_units'] : array( 'px' );

			} elseif ( 'scheme' === $type ) {

				$field_params['type']  = \Elementor\Controls_Manager::COLOR;
				$field_params['type']  = \Elementor\Scheme_Color::get_type();
				$field_params['value'] = \Elementor\Scheme_Color::COLOR_1;

			} elseif ( 'hidden' === $type ) {

				$field_params['type'] = \Elementor\Controls_Manager::HIDDEN;

			} elseif ( 'video' === $type ) {

				$field_params['type']       = \Elementor\Controls_Manager::MEDIA;
				$field_params['media_type'] = 'video';

			} elseif ( 'audio' === $type ) {

				$field_params['type']       = \Elementor\Controls_Manager::MEDIA;
				$field_params['media_type'] = 'audio';

			} elseif ( 'image' === $type ) {

				$field_params['type']       = \Elementor\Controls_Manager::MEDIA;
				$field_params['media_type'] = 'image';

			} elseif ( 'images' === $type ) {

				$field_params['type'] = \Elementor\Controls_Manager::GALLERY;

			} elseif ( 'hover_animation' === $type ) {

				$field_params['type'] = \Elementor\Controls_Manager::HOVER_ANIMATION;

			}

				$elementor_params = array(
					'condition',
					'conditions',
					'description',
					'label_block',
					'separator',
					'tablet_default',
					'mobile_default',
					'prefix_class',
					'selectors',
					'style_transfer',
					'skin',
				);

				foreach ( $elementor_params as $elementor_param ) {
					if ( isset( $p[ $elementor_param ] ) ) {
						$field_params[ $elementor_param ] = $p[ $elementor_param ];
					}
				}

				/* Goupe Tabs */
				if ( isset( $p['group_tabs'] ) && 'open' === $p['group_tabs'] ) {
					$widget->start_controls_tabs( $p['name'] );
				}

				if ( isset( $p['tab'] ) && 'open' === $p['tab'] ) {
					$widget->start_controls_tab(
						$p['name'],
						array(
							'label' => $p['label'],
						)
					);
				}

				if ( isset( $p['responsive_control'] ) && $p['responsive_control'] ) {

					$widget->add_responsive_control(
						$p['param_name'],
						$field_params
					);

				} elseif ( 'padding' === $type ) {

					$widget->add_responsive_control(
						$p['param_name'],
						array(
							'label'      => $field_params['label'],
							'type'       => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => array( 'px', 'em', '%', 'rem' ),
							'selectors'  => array(
								$p['selector'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							),
							'default'    => isset( $field_params['default'] ) ? $field_params['default'] : array(),
							'condition'  => isset( $p['condition'] ) ? $p['condition'] : array(),
							'conditions' => isset( $p['conditions'] ) ? $p['conditions'] : array(),
						)
					);

				} elseif ( 'margin' === $type ) {

					$widget->add_responsive_control(
						$p['param_name'],
						array(
							'label'      => $field_params['label'],
							'type'       => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => array( 'px', 'em', '%', 'rem' ),
							'selectors'  => array(
								$p['selector'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							),
							'default'    => isset( $field_params['default'] ) ? $field_params['default'] : array(),
							'condition'  => isset( $p['condition'] ) ? $p['condition'] : array(),
							'conditions' => isset( $p['conditions'] ) ? $p['conditions'] : array(),
						)
					);
				} elseif ( 'css_filters' === $type ) {

					$widget->add_group_control(
						\Elementor\Group_Control_Css_Filter::get_type(),
						array(
							'name'     => $p['param_name'],
							'selector' => $p['selector'],
						)
					);

				} elseif ( 'border' === $type ) {

					$widget->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						array_merge(
							array( 'name' => $p['param_name'] ),
							$field_params
						)
					);

				} elseif ( 'background' === $type ) {

					$widget->add_group_control(
						\Elementor\Group_Control_Background::get_type(),
						array(
							'name'     => $p['param_name'],
							'label'    => esc_html__( 'Background', 'wolf-discography' ),
							'types'    => array( 'classic', 'gradient', 'video' ),
							'selector' => $p['selector'],
						)
					);

				} elseif ( 'box_shadow' === $type ) {

					$widget->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						array_merge(
							array( 'name' => $p['param_name'] ),
							$field_params
						)
					);

				} elseif ( 'typography' === $type ) {

					$widget->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						array_merge(
							array( 'name' => $p['param_name'] ),
							$field_params
						)
					);

				} elseif ( 'text_shadow' === $type ) {

					$widget->add_group_control(
						\Elementor\Group_Control_Text_Shadow::get_type(),
						array_merge(
							array( 'name' => $p['param_name'] ),
							$field_params
						)
					);

				} elseif ( isset( $p['param_name'] ) && 'repeater' !== $type ) {

					$widget->add_control(
						$p['param_name'],
						$field_params
					);
				}

					/* End tab */
				if ( isset( $p['tab'] ) && 'close' === $p['tab'] ) {
					$widget->end_controls_tab();
				}

				/* End group tabs */
				if ( isset( $p['group_tabs'] ) && 'close' === $p['group_tabs'] ) {
					$widget->end_controls_tabs();
				}
		}
	}

	/**
	 * Register Elementor controls.
	 *
	 * Register control sections of a widget from its params array.
	 *
	 * @param object $widget The widget object.
	 * @return void
	 */
	public static function register_elementor_controls( $widget ) {
		/* Reorder params by group */
		$content_group_params  = array();
		$query_group_params    = array();
		$style_group_params    = array();
		$custom_group_params   = array();
		$extra_group_params    = array();
		$advanced_group_params = array();
		$options_group_params  = array();

		foreach ( $widget->params['params'] as $param ) {

			if ( ! isset( $param['group'] ) ) {
				$content_group_params[] = $param;
			} elseif ( 'Query' === $param['group'] ) {
				$query_group_params[] = $param;
			} elseif ( 'Style' === $param['group'] ) {
				$style_group_params[] = $param;
			} elseif ( 'Custom' === $param['group'] ) {
				$custom_group_params[] = $param;
			} elseif ( 'Extra' === $param['group'] ) {
				$extra_group_params[] = $param;
			} elseif ( 'Advanced' === $param['group'] ) {
				$advanced_group_params[] = $param;
			} elseif ( 'Options' === $param['group'] ) {
				$options_group_params[] = $param;
			}
		}

		$widget->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Content', 'wolf-discography' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		self::convert_params_to_elementor( $widget, $content_group_params );

		$widget->end_controls_section();

		if ( array() !== $query_group_params ) {
			$widget->start_controls_section(
				'query_section',
				array(
					'label' => esc_html__( 'Query', 'wolf-discography' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			self::convert_params_to_elementor( $widget, $query_group_params );

			$widget->end_controls_section();
		}

		if ( array() !== $style_group_params ) {
			$widget->start_controls_section(
				'style_section',
				array(
					'label' => esc_html__( 'Title', 'wolf-discography' ),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				)
			);

			self::convert_params_to_elementor( $widget, $style_group_params );

			$widget->end_controls_section();
		}

		if ( array() !== $custom_group_params ) {
			$widget->start_controls_section(
				'custom_section',
				array(
					'label' => esc_html__( 'Custom', 'wolf-discography' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			self::convert_params_to_elementor( $widget, $custom_group_params );

			$widget->end_controls_section();
		}

		if ( array() !== $extra_group_params ) {
			$widget->start_controls_section(
				'extra_section',
				array(
					'label' => esc_html__( 'Extra', 'wolf-discography' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			self::convert_params_to_elementor( $widget, $extra_group_params );

			$widget->end_controls_section();
		}

		if ( array() !== $advanced_group_params ) {
			$widget->start_controls_section(
				'advanced_section',
				array(
					'label' => esc_html__( 'Advanced', 'wolf-discography' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			self::convert_params_to_elementor( $widget, $advanced_group_params );

			$widget->end_controls_section();
		}

		if ( array() !== $options_group_params ) {
			$widget->start_controls_section(
				'options_section',
				array(
					'label' => esc_html__( 'Options', 'wolf-discography' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			self::convert_params_to_elementor( $widget, $options_group_params );

			$widget->end_controls_section();
		}
	}
}