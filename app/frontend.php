<?php

namespace GFDA;

// Convert the value of the data attribute setting from a multi line string to an array
function dataAttrNamesToArray($attrs)
{
    return preg_split("/\r\n|\n|\r/", $attrs);
}

// test is the field has data attributes
function fieldHasDataAttributes($field)
{
    return property_exists($field, 'enableDataAttrsField') && $field->enableDataAttrsField;
}

// Add data attributes to inputs that don't have multiple choices
add_filter('gform_field_content', function ($content, $field, $value, $lead_id, $form_id) {
    // Bail if: in the admin, no data attributes, or the field has choices
    if (is_admin() || !fieldHasDataAttributes($field) || !empty($field->choices)) {
        return $content;
    }

    $attrs = dataAttrNamesToArray($field->dataAttrsField);

    $attrHtml = '';

    foreach ($attrs as $attr) {
        // skip if not set
        if (!property_exists($field, $attr)) {
            continue;
        }

        $value = $field[$attr];
        $attrHtml .= " data-{$attr}='{$value}'";
    }

    if ($attrHtml) {
        $content = str_replace(' name=', "$attrHtml name=", $content);
    }

    return $content;
}, 10, 5);

// Add data attributes to inputs that have multiple choices (checkboxes, drop downs, etc.)
add_filter('gform_field_choice_markup_pre_render', function ($choice_markup, $choice, $field, $value) {
    // Bail if: in the admin or no data attributes
    if (is_admin() || !fieldHasDataAttributes($field)) {
        return $choice_markup;
    }

    $attrs = dataAttrNamesToArray($field->dataAttrsField);

    $attrHtml = '';

    foreach ($attrs as $attr) {
        // skip if not set
        if (!array_key_exists($attr, $choice)) {
            continue;
        }

        $value = $choice[$attr];
        $attrHtml .= " data-{$attr}='{$value}'";
    }

    if ($attrHtml) {
        switch ($field->type) {
            case 'select':
            case 'multiselect':
                $choice_markup = str_replace('<option ', "<option $attrHtml", $choice_markup);
                break;

            default:
                $choice_markup = str_replace(' name=', "$attrHtml name=", $choice_markup);
                break;
        }
    }

    return $choice_markup;
}, 10, 4);

// Add data attributes to multi-column list fields
add_filter('gform_column_input_content', function ($input, $input_info, $field, $text) {
    // Bail if: in the admin, no data attributes, or the field isn't using multiple columns
    if (is_admin() || !fieldHasDataAttributes($field) || empty($field->choices)) {
        return $input;
    }

    $attrs = dataAttrNamesToArray($field->dataAttrsField);

    $attrHtml = '';

    foreach ($attrs as $attr) {
        $item = null;
        foreach ($field->choices as $choice) {
            if ($text == $choice['text']) {
                $item = $choice;
                break;
            }
        }

        // skip if not set
        if (!array_key_exists($attr, $item)) {
            continue;
        }

        $value = $item[$attr];
        $attrHtml .= " data-{$attr}='{$value}'";
    }

    if ($attrHtml) {
        $input = str_replace(' name=', "$attrHtml name=", $input);
    }

    return $input;
}, 10, 5);
