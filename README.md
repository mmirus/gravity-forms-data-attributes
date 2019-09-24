# Gravity Forms Data Attributes

- [Usage](#usage)
- [Installation](#installation)
- [Contributing](#contributing)
- [Screenshots](#screenshots)

This plugin allows you to add custom data attributes to Gravity Forms fields.

It should work with:

- all of the Standard field types
- any of the Advanced field types that only have one input field (e.g., Email but not Address)
- the List advanced field type, including with multiple columns (thanks @reddo!).

For fields with choices, such as checkboxes, you can assign separate values for each data attribute for each choice (the UI for this is ugly right now 🤷‍).

## Usage

For each field you want to enable data attributes for:

1. On the field's General settings tab, check the **Enable Data Attributes** box.
2. In the **Data Attribute Names** field that appears, enter one data attribute name (e.g., `my-attribute`) per line.
   - _Do not_ prefix your attribute name with `data-`. The plugin will do that for you,
3. Save your form.
4. Reopen the field settings. You will now see additional setting fields where you can enter the value of each data attribute you added.
   - For fields with choices, such as checkboxes, the data attribute value settings appear next to each choice's name and value fields.
   - For other fields, the data attribute value settings will appear underneath the Data Attribute Names field.

## Installation

There are three options for installing this plugin:

1. With composer from [Packagist](https://packagist.org/packages/mmirus/gravity-forms-data-attributes): `composer require mmirus/gravity-forms-data-attributes`
2. With [GitHub Updater](https://github.com/afragen/github-updater)
3. By downloading the latest release ZIP from this repository and installing it like any normal WordPress plugin

## Contributing

To work on this project (for yourself or to contribute a PR):

1. Clone this repo
2. Run `composer install`
3. Run `yarn` or `npm install`

And you should be good to go.

Pre-commit linting is enforced (see `.eslintrc.js` and `.stylelintrc.js` for scripts and styles; PHP uses PSR-2).

## Screenshots

_Settings for a field without choices_

![Alt Text](/screenshots/single-line-text.png)

_Settings for a field with choices (checkboxes)_

![Alt Text](/screenshots/checkboxes.png)
