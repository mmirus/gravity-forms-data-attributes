<?php

namespace GFDA;

function consoleLog(){
    if(func_num_args() == 0){
        return;
    }

    $tag = '';
    for ($i = 0; $i < func_num_args(); $i++) {
        $arg = func_get_arg($i);
        if(!empty($arg)){
            if (is_string($arg) && strtolower(substr($arg, 0, 4)) === 'tag-') {
                $tag = substr($arg, 4);
            } else {
                $arg = json_encode($arg, JSON_HEX_TAG | JSON_HEX_AMP );
                echo "<script>console.log('" . $tag . " " . $arg . "');</script>";
            }
        }
    }
}

// Convert the value of the data attribute setting from a multi line string to an array
function dataAttrNamesToArray($attrs)
{
    return preg_split("/\r\n|\n|\r/", $attrs);
}

// Add data attributes to inputs that don't have multiple choices
add_filter('gform_field_content', function ($content, $field, $value, $lead_id, $form_id) {
    // Bail if: in the admin, the field has choices, or the field doesn't have data attributes enabled
    if (is_admin() || !empty($field->choices) || !property_exists($field, 'enableDataAttrsField') || !$field->enableDataAttrsField) {
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
    // Bail if: in the admin or the field doesn't have data attributes enabled
    if (is_admin() || !property_exists($field, 'enableDataAttrsField') || !$field->enableDataAttrsField) {
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

add_filter( 'gform_column_input_content', function ($input, $input_info, $field, $column_name, $value, $form_id) {
    // Bail if: in the admin or the field doesn't have data attributes enabled
    if (is_admin() || !property_exists($field, 'enableDataAttrsField') || !$field->enableDataAttrsField) {
        return $input;
    }

    $attrs = dataAttrNamesToArray($field->dataAttrsField);

    consoleLog($attrs);

    $attrHtml = '';

    // foreach ($attrs as $attr) {
    //     // skip if not set
    //     if (!array_key_exists($attr, $input_info)) {
    //         continue;
    //     }

    //     $value = $input_info[$attr];
    //     $attrHtml .= " data-{$attr}='{$value}'";
    // }

    // if ($attrHtml) {
    //     $input = str_replace(' name=', "$attrHtml name=", $input);
    // }

    return $input;
}, 10, 5 );
