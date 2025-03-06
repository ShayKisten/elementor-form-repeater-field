<?php
namespace ElementorFormFieldRepeater\Fields;

use ElementorPro\Modules\Forms\Fields\Field_Base;
use Elementor\Controls_Manager;
use ElementorPro\Modules\Forms\Widgets\Form;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class RepeaterSection extends Field_Base {
    public function get_type() {
        return 'repeater_container';
    }

    public function get_name(): string {
        return esc_html__('Repeater Container', 'elementor-form-repeater-field');
    }

    public function update_controls($widget): void {
        $control_data = \ElementorPro\Plugin::elementor()->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');

        if (is_wp_error($control_data)) {
            return;
        }

        $field_controls = [
            'repeater-type' => [
                'name' => 'repeater-type',
                'label' => esc_html__('Repeater Type', 'elementor-form-repeater-field'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'open' => __('Open', 'elementor-form-repeater-field'),
                    'close' => __('Close', 'elementor-form-repeater-field'),
                ],
                'default' => 'open',
                'condition' => [
                    'field_type' => $this->get_type(),
                ],
                'tab' => 'content',
                'inner_tab' => 'form_fields_content_tab',
                'tabs_wrapper' => 'form_fields_tabs',
            ],
            'repeater-identifier' => [
                'name' => 'repeater-identifier',
                'label' => esc_html__('Repeater Identifier', 'elementor-form-repeater-field'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'description' => esc_html__('Unique identifier to match open and close blocks.', 'elementor-form-repeater-field'),
                'condition' => [
                    'field_type' => $this->get_type(),
                ],
                'tab' => 'content',
                'inner_tab' => 'form_fields_content_tab',
                'tabs_wrapper' => 'form_fields_tabs',
            ],
            'repeater-title' => [
                'name' => 'repeater-title',
                'label' => esc_html__('Title', 'elementor-form-repeater-field'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'condition' => [
                    'field_type' => $this->get_type(),
                    'repeater-type' => 'open',
                ],
                'tab' => 'content',
                'inner_tab' => 'form_fields_content_tab',
                'tabs_wrapper' => 'form_fields_tabs',
            ],
            'button-text' => [
                'name' => 'button-text',
                'label' => esc_html__('Button Text', 'elementor-form-repeater-field'),
                'type' => Controls_Manager::TEXT,
                'default' => 'Add More',
                'condition' => [
                    'field_type' => $this->get_type(),
                    'repeater-type' => 'close',
                ],
                'tab' => 'content',
                'inner_tab' => 'form_fields_content_tab',
                'tabs_wrapper' => 'form_fields_tabs',
            ],
        ];

        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);

        add_action('elementor/element/form/section_form_style/after_section_end', [$this, 'register_form_style_controls'], 10, 2);
    }

    public function render($item, $item_index, $form): void {
        $repeater_identifier = strtolower(str_replace(' ', '-', $item['repeater-identifier'] ?? 'repeater_' . $item_index));
        $repeater_type = strtolower($item['repeater-type'] ?? 'open');

        if ($repeater_type === 'open') {
            $title = $item['repeater-title'] ?? '';
            echo '<div class="efrf-repeater-start" data-repeater-identifier="' . esc_attr($repeater_identifier) . '" style="display:none;"></div>';
            if (!empty($title)) {
                echo '<label class="elementor-field-label">' . esc_html($title) . '</label>';
            }
        } elseif ($repeater_type === 'close') {
            $button_text = $item['button-text'] ?? 'Add More';
            echo '<div class="efrf-repeater-end" data-repeater-identifier="' . esc_attr($repeater_identifier) . '">';
            echo '<div class="efrf-repeater-controls">';
            echo '<button type="button" class="efrf-add-more elementor-button elementor-size-sm elementor-button-default" data-repeater-identifier="' . esc_attr($repeater_identifier) . '">' . esc_html($button_text) . '</button>';
            echo '</div>';
            echo '</div>';
        }
    }

    public function register_form_style_controls($widget, $args) {
        $widget->start_controls_section(
            'section_repeater_styles',
            [
                'label' => esc_html__('Repeater', 'elementor-form-repeater-field'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $widget->add_control(
            'repeater_header_styles',
            [
                'label' => esc_html__('Repeater Header', 'elementor-form-repeater-field'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $widget->add_control(
            'header_font_family',
            [
                'label' => esc_html__('Font Family', 'elementor-form-repeater-field'),
                'type' => Controls_Manager::FONT,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-type-repeater_container .elementor-field-label' => 'font-family: {{VALUE}};',
                ],
            ]
        );

        $widget->add_control(
            'header_font_size',
            [
                'label' => esc_html__('Font Size', 'elementor-form-repeater-field'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 1,
                        'max' => 10,
                    ],
                    'rem' => [
                        'min' => 1,
                        'max' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-type-repeater_container .elementor-field-label' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->add_control(
            'header_color',
            [
                'label' => esc_html__('Color', 'elementor-form-repeater-field'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-type-repeater_container .elementor-field-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $widget->add_control(
            'repeater_title_styles',
            [
                'label' => esc_html__('Title', 'elementor-form-repeater-field'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $widget->add_control(
            'title_font_family',
            [
                'label' => esc_html__('Font Family', 'elementor-form-repeater-field'),
                'type' => Controls_Manager::FONT,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .efrf-section-header .elementor-field-label' => 'font-family: {{VALUE}};',
                ],
            ]
        );

        $widget->add_control(
            'title_font_size',
            [
                'label' => esc_html__('Font Size', 'elementor-form-repeater-field'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 1,
                        'max' => 10,
                    ],
                    'rem' => [
                        'min' => 1,
                        'max' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 14,
                ],
                'selectors' => [
                    '{{WRAPPER}} .efrf-section-header .elementor-field-label' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->add_control(
            'title_color',
            [
                'label' => esc_html__('Color', 'elementor-form-repeater-field'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .efrf-section-header .elementor-field-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $widget->add_control(
            'arrow_color',
            [
                'label' => esc_html__('Arrow Color', 'elementor-form-repeater-field'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .efrf-toggle' => 'color: {{VALUE}};',
                ],
            ]
        );

        $widget->end_controls_section();
    }
}