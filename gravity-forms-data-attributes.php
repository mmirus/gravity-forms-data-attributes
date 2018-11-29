<?php
/*
Plugin Name: Gravity Forms Data Attributes
Plugin URI: https://github.com/mmirus/gravity-forms-data-attributes
Description: Add custom data attributes to your form inputs
Author: Matt Mirus
Author URI: https://github.com/mmirus
Version: 1.0.0
GitHub Plugin URI: https://github.com/mmirus/gravity-forms-data-attributes
 */

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
                <em style="display:block;">You must save the form after changing this setting.</em>

                <div id="gform_data_attr_inputs"></div>
            </div>
        </li>
    <?php
    endif;
}, 10, 2);

add_action('gform_editor_js', function () {
    ?>
    <script type='text/javascript'>
        // show/hide data attributes textarea
        function ToggleDataAttrs(isInit){
            var speed = isInit ? "" : "slow";

            if(jQuery("#field_enable_data_attrs_value").is(":checked")){
                jQuery("#gform_data_attrs").show(speed);

                SetFieldProperty('enableDataAttrsField', true);
            }
            else{
                jQuery("#gform_data_attrs").hide(speed);
                SetFieldProperty('enableDataAttrsField', false);
                SetFieldProperty('dataAttrsField', '');
            }
        }

        // make custom settings availalbe to all field types
        for (i in fieldSettings) {
            fieldSettings[i] += ', .enable_data_attrs_setting, .data_attrs_setting';
        }

        // initialize our custom settings on the field settings load event
        jQuery(document).on('gform_load_field_settings', function(event, field, form){
            jQuery('#field_enable_data_attrs_value').attr('checked', field.enableDataAttrsField == true);
            ToggleDataAttrs(true);
            jQuery('#field_data_attrs').val(field.dataAttrsField);
            
            if (['checkbox', 'radio'].includes(field.type)) return;
            
            var dataAttrsInputContainer = jQuery('#gform_data_attr_inputs');

            var dataAttrs = field.dataAttrsField
            
            if (!dataAttrs) return '';
            
            dataAttrs = dataAttrs.split("\n").map(function(name) {
                return {
                    name: name,
                    value: field[name] || ''
                };
            });

            var inputs = dataAttrs.map(function(dataAttr) {
                return "<br><label class='section_label'>" + dataAttr.name + "</label><input type='text' id='" + dataAttr.name + "' value='" + dataAttr.value + "' class='field-" + dataAttr.name + " field-data-attr' data-attr-name='" + dataAttr.name + "' /><br>";
            }).join('');

            dataAttrsInputContainer.append(inputs);
        });
        
        // save data attribute values (general)
        jQuery('#gform_data_attr_inputs').on('input propertychange', '.field-data-attr', function () {
            var $this = jQuery(this);

            field = GetSelectedField();
            field[$this.data('attrName')] = $this.val();
        });

        // save data attribute values ()checkbox and radio fields)
        jQuery('.choices_setting').on('input propertychange', '.field-choice-data-attr', function () {
            var $this = jQuery(this);
            var i = $this.closest('li.field-choice-row').data('index');

            field = GetSelectedField();
            field.choices[i][$this.data('attrName')] = $this.val();
        });
        
        // add data attribute fields to checkbox / radio choices
        gform.addFilter('gform_append_field_choice_option', function (str, field, i) {
            var inputType = GetInputType(field);
            var custom = field.choices[i].custom ? field.choices[i].custom : '';
            
            var dataAttrs = field.dataAttrsField
            
            if (!dataAttrs) return '';
            
            dataAttrs = dataAttrs.split("\n").map(function(name) {
                return {
                    name: name,
                    value: field.choices[i][name] || ''
                };
            });

            // TODO remove inline styles from below?
            var inputs = dataAttrs.map(function(dataAttr) {
                var id = inputType + "_choice_" + dataAttr.name + "_" + i;
                return "<label style='width:155px;margin:10px 0 0;'>" + dataAttr.name + " <input type='text' id='" + id + "' value='" + dataAttr.value + "' class='field-choice-input field-choice-" + dataAttr.name + " field-choice-data-attr' data-attr-name='" + dataAttr.name + "' /></label>";
            }).join('');

            return "<div style='display:flex; flex-wrap:wrap; margin-left:35px;'>" + inputs + "</div>";
        });
    </script>
<?php

});

add_filter('gform_tooltips', function ($tooltips) {
    $tooltips['form_field_data_attrs'] = "<h6>Data Attribute Names</h6><p>Enter the names of the data attributes you wish to enable, one per line.</p><p>You must save the form after changing this setting.</p>";
    return $tooltips;
});
