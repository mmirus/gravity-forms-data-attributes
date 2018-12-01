/**
 * These scripts are responsible for:
 * - Initializing the custom settings when fields are rendered
 * - Showing / hiding the settings when toggled
 * - Saving the settings values when updated by the user
 */

// Show/hide the "Data Attribute Names" setting and the settings for individual data attributes
function ToggleDataAttrs(isInit) {
  var speed = isInit ? "" : "slow";

  if (jQuery("#field_enable_data_attrs_value").is(":checked")) {
    jQuery("#gform_data_attrs, .field-choice-data-attr-wrapper").show(speed);

    SetFieldProperty("enableDataAttrsField", true);
  } else {
    jQuery("#gform_data_attrs, .field-choice-data-attr-wrapper").hide(speed);
    SetFieldProperty("enableDataAttrsField", false);
    SetFieldProperty("dataAttrsField", "");
  }
}

/**
 * When fields are rendered:
 * 1. Initialize "Enable Data Attributes" and "Data Attribute Names" settings
 * 2. If the field doesn't have choices, add the field for each data attribute
 */
jQuery(document).on("gform_load_field_settings", function(event, field, form) {
  jQuery("#field_enable_data_attrs_value").attr(
    "checked",
    field.enableDataAttrsField == true
  );

  var dataAttrsInputContainer = jQuery("#gform_data_attr_inputs");
  dataAttrsInputContainer.html("");

  jQuery("#field_data_attrs").val(field.dataAttrsField);

  var dataAttrs = field.dataAttrsField;

  if (field.choices || !dataAttrs)
    return ToggleDataAttrs(!field.enableDataAttrsField);

  dataAttrs = dataAttrs.split("\n").map(function(name) {
    return {
      name: name,
      value: field[name] || "",
    };
  });

  var inputs = dataAttrs
    .map(function(dataAttr) {
      return (
        "<br><label class='section_label'>" +
        dataAttr.name +
        "</label><input type='text' id='" +
        dataAttr.name +
        "' value='" +
        dataAttr.value +
        "' class='field-" +
        dataAttr.name +
        " field-data-attr' data-attr-name='" +
        dataAttr.name +
        "' /><br>"
      );
    })
    .join("");

  dataAttrsInputContainer.html(inputs);

  ToggleDataAttrs(!field.enableDataAttrsField);
});

// Add the field for each data attribute when fields choices are rendered
gform.addFilter("gform_append_field_choice_option", function(str, field, i) {
  var inputType = GetInputType(field);

  var dataAttrs = field.dataAttrsField;

  if (!dataAttrs) return "";

  dataAttrs = dataAttrs.split("\n").map(function(name) {
    return {
      name: name,
      value: field.choices[i][name] || "",
    };
  });

  var inputs = dataAttrs
    .map(function(dataAttr) {
      var id = inputType + "_choice_" + dataAttr.name + "_" + i;
      return (
        "<label>" +
        dataAttr.name +
        " <input type='text' id='" +
        id +
        "' value='" +
        dataAttr.value +
        "' class='field-choice-input field-choice-" +
        dataAttr.name +
        " field-choice-data-attr' data-attr-name='" +
        dataAttr.name +
        "' /></label>"
      );
    })
    .join("");

  return "<div class='field-choice-data-attr-wrapper'>" + inputs + "</div>";
});

// save data attribute values (general / non-choices)
jQuery(document).on("input propertychange", ".field-data-attr", function() {
  var $this = jQuery(this);

  var field = GetSelectedField();
  field[$this.data("attrName")] = $this.val();
});

// save data attribute values (choices)
jQuery(document).on(
  "input propertychange",
  ".field-choice-data-attr",
  function() {
    var $this = jQuery(this);
    var i = $this.closest("li.field-choice-row").data("index");

    var field = GetSelectedField();
    field.choices[i][$this.data("attrName")] = $this.val();
  }
);
