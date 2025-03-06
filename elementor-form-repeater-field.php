<?php
/*
Plugin Name: Elementor Form Repeater Field
Description: Adds a custom form field to Elementor Pro forms for repeating form fields.
Version: 1.0.0
Author: ShayKisten
Author URI: https://shaykisten.com
License: GPLv2 or later
Text Domain: elementor-form-repeater-field
Requires Plugins: elementor-pro
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function elementor_form_field_repeater_load_textdomain() {
    load_plugin_textdomain('elementor-form-repeater-field', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'elementor_form_field_repeater_load_textdomain');

function elementor_form_field_repeater_register_form_fields($form_fields_registrar) {
    require_once __DIR__ . '/includes/Fields/RepeaterSection.php';
    $form_fields_registrar->register(new \ElementorFormFieldRepeater\Fields\RepeaterSection());
}
add_action('elementor_pro/forms/fields/register', 'elementor_form_field_repeater_register_form_fields');

function elementor_form_field_repeater_process_form_data($record, $ajax_handler) {
    require_once __DIR__ . '/includes/process-form-data.php';
    process_repeated_form_data($record, $ajax_handler);
}
add_action('elementor_pro/forms/new_record', 'elementor_form_field_repeater_process_form_data', 10, 2);

function elementor_form_field_repeater_enqueue_scripts() {
    if (is_singular() && \Elementor\Plugin::$instance->db->is_built_with_elementor(get_the_ID())) {
        wp_enqueue_style(
            'efrf-repeater-styles',
            plugin_dir_url(__FILE__) . 'assets/css/repeater-styles.css',
            [],
            '1.0.0'
        );
        wp_enqueue_script(
            'efrf-repeater-scripts',
            plugin_dir_url(__FILE__) . 'assets/js/repeater-scripts.js',
            ['jquery', 'elementor-frontend'],
            '1.0.0',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'elementor_form_field_repeater_enqueue_scripts');