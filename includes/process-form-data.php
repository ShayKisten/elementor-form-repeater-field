<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function process_repeated_form_data($record, $ajax_handler) {
    $raw_fields = $record->get('fields');
    $formatted_fields = [];

    foreach ($raw_fields as $field_id => $field) {
        if (preg_match('/^([^_]+)(?:_([^_]+)_(\d+)(?:_([^_]+)_(\d+))?)?$/', $field_id, $matches)) {
            $base_field_id = $matches[1];
            $parent_identifier = isset($matches[2]) ? $matches[2] : null;
            $parent_index = isset($matches[3]) ? (int)$matches[3] + 1 : null;
            $child_identifier = isset($matches[4]) ? $matches[4] : null;
            $child_index = isset($matches[5]) ? (int)$matches[5] + 1 : null;

            $label = '';
            if ($parent_identifier) {
                $label .= ucfirst($parent_identifier) . ' ' . $parent_index;
                if ($child_identifier) {
                    $label .= ' - ' . ucfirst($child_identifier) . ' ' . $child_index;
                }
                $label .= ' - ' . ($field['title'] ?? ucfirst(str_replace('field_', '', $base_field_id)));
            } else {
                $label = $field['title'] ?? ucfirst($field_id);
            }
            $formatted_fields[$label] = $field['value'];
        }
    }

    $record->set('fields_display', $formatted_fields);

    error_log('Processed submission data: ' . print_r($formatted_fields, true));
}