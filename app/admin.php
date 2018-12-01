<?php

namespace GFDA;

// Enqueue admin scripts and styles
add_action('admin_enqueue_scripts', function () {
    $screen = get_current_screen();
    if (!$screen || $screen->id !== 'toplevel_page_gf_edit_forms') {
        return;
    }

    wp_register_script('gravity-forms-data-attributes', plugins_url('assets/admin.js', dirname(__FILE__)), ['gform_form_admin']);
    wp_enqueue_script('gravity-forms-data-attributes');

    wp_register_style('gravity-forms-data-attributes', plugins_url('assets/admin.css', dirname(__FILE__)));
    wp_enqueue_style('gravity-forms-data-attributes');
});

// Add "Enable Data Attributes" and "Data Attribute Names" settings
add_action('gform_field_standard_settings', function ($position, $form_id) {
    if ($position === 1350) :
        ?>
        <li class="enable_data_attrs_setting field_setting">
            <input type="checkbox" id="field_enable_data_attrs_value" onclick="ToggleDataAttrs();" />
            <label for="field_enable_data_attrs_value" class="inline">
                <?php esc_html_e('Enable Data Attributes', 'gravityforms'); ?>
            </label>

            <div id="gform_data_attrs" style="display:none;">
                <br>
                <label for="field_data_attrs" class="section_label">
                    <?php esc_html_e('Data Attribute Names', 'gravityforms'); ?>
                    <?php gform_tooltip('form_field_data_attrs') ?>
                </label>
                <textarea id="field_data_attrs" class="fieldwidth-3 fieldheight-2" oninput="SetFieldProperty('dataAttrsField', this.value);"></textarea>
                <div class="gfield_data_attr_note">You must save the form after changing this setting.</div>

                <div id="gform_data_attr_inputs"></div>
            </div>
        </li>
        <?php
    endif;
}, 10, 2);

// Add tooltip to "Data Attribute Names" setting
add_filter('gform_tooltips', function ($tooltips) {
    $tooltips['form_field_data_attrs'] = "<h6>Data Attribute Names</h6><p>Enter the names of the data attributes you wish to enable, one per line.</p><p>You must save the form after changing this setting.</p>";
    return $tooltips;
});

// Register "Enable Data Attributes" and "Data Attribute Names" settings for all field types
add_action('gform_editor_js', function () {
    ?>
    <script type='text/javascript'>
        for (i in fieldSettings) {
            fieldSettings[i] += ", .enable_data_attrs_setting, .data_attrs_setting";
        }
    </script>
    <?php
});
