<?php
/**
 * Adverts Defaults
 * 
 * Load class-adverts.php and functions.php before using this file
 * 
 * This file contains default values for frontend "post ad" form structure and currency.
 * 
 * Registers Form fields and validators.
 *
 * @uses Adverts
 * @uses adverts_config
 * 
 * @package     Adverts
 * @copyright   Copyright (c) 2015, Grzegorz Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Set default "Post Ad" form structure and save it in Adverts Singleton
Adverts::instance()->set("form", array(
    "name" => "advert",
    "action" => "",
    "field" => array(
        array(
            "name" => "_post_id",
            "type" => "adverts_field_hidden",
            "order" => 0,
            "label" => ""
        ),
        array(
            "name" => "_adverts_action",
            "type" => "adverts_field_hidden",
            "order" => 0,
            "label" => ""
        ),
        array(
            "name" => "_contact_information",
            "type" => "adverts_field_header",
            "order" => 1,
            "label" => __( 'Contact Information', 'adverts' )
        ),
        array(
            "name" => "_adverts_account",
            "type" => "adverts_field_account",
            "order" => 2,
            "label" => __( "Account", "adverts" ),
        ),
        array(
            "name" => "adverts_person",
            "type" => "adverts_field_text",
            "order" => 3,
            "label" => __( "Contact Person", "adverts" ),
            "is_required" => true,
            "validator" => array( 
                array( "name" => "is_required" ),
            )
        ),
        array(
            "name" => "adverts_email",
            "type" => "adverts_field_text",
            "order" => 4,
            "label" => __( "Email", "adverts" ),
            "is_required" => true,
            "validator" => array( 
                array( "name" => "is_required" ),
                array( "name" => "is_email" ),
                array( "name" => "is_email_registered")
            )
        ),
        array(
            "name" => "adverts_phone",
            "type" => "adverts_field_text",
            "order" => 5,
            "label" => __( "Phone Number", "adverts"),
            "validator" => array(
                array(
                    "name" => "string_length",
                    "params" => array( "min" => 5 )
                )
            )
        ),
        array(
            "name" => "_item_information",
            "type" => "adverts_field_header",
            "order" => 6,
            "label" => __( 'Item Information', 'adverts' )
        ),
        array(
            "name" => "post_title",
            "type" => "adverts_field_text",
            "order" => 7,
            "label" => __( "Title", "adverts" ),
            "validator" => array(
                array( "name"=> "is_required" )
            )
        ),
        array(
            "name" => "advert_category",
            "type" => "adverts_field_select",
            "order" => 8,
            "label" => __("Category", "adverts"),
            "max_choices" => 10,
            "options" => array(),
            "options_callback" => "adverts_taxonomies",
            "validator" => array(
                array( 
                    "name" => "max_choices",
                    "params" => array( "max_choices" => 10 )
                )
            )
        ),
        array(
            "name" => "gallery",
            "type" => "adverts_field_gallery",
            "order" => 9,
            "label" => __( "Gallery", "adverts" )
        ),
        array(
            "name" => "post_content",
            "type" => "adverts_field_textarea",
            "order" => 10,
            "label" => __( "Description", "adverts" ),
            "validator" => array(
                array( "name"=> "is_required" )
            ),
            "mode" => "tinymce-mini"
        ),
        array(
            "name" => "adverts_price",
            "type" => "adverts_field_text",
            "order" => 11,
            "label" => __("Price", "adverts"),
            "class" => "adverts-filter-money",
            "description" => "",
            "attr" => array( ),
            "filter" => array(
                array( "name" => "money" )
            ),
        ),
        array(
            "name" => "adverts_location",
            "type" => "adverts_field_text",
            "order" => 12,
            "label" => __( "Location", "adverts" ),
        ),
    )
));

// Set default search form in [adverts_list] shortcode
Adverts::instance()->set("form_search", array(
    "name" => "search",
    "action" => "",
    "field" => array(
        array(
            "name" => "query",
            "type" => "adverts_field_text",
            "label" => "",
            "order" => 10,
            "placeholder" => __("Keyword ...", "adverts"),
            "meta" => array(
                "search_group" => "visible",
                "search_type" => "half" 
            )

        ),
        array(
            "name" => "location",
            "type" => "adverts_field_text",
            "label" => "",
            "order" => 10,
            "placeholder" => __("Location ...", "adverts"),
            "meta" => array(
                "search_group" => "visible",
                "search_type" => "half"
            )
        )
    )
));

// Set default currency and save it in Adverts Singleton
Adverts::instance()->set("currency", array(
    'code' => adverts_config("config.currency_code"),
    'sign' => adverts_currency_list( adverts_config("config.currency_code"), 'sign'),
    'sign_type' => adverts_config("config.currency_sign_type"), // either p=prefix or s=suffix
    'decimals' => adverts_config("config.currency_decimals"),
    'char_decimal' => adverts_config("config.currency_char_decimal"),
    'char_thousand' => adverts_config("config.currency_char_thousand"),
) );

/** REGISTER FORM FIELDS */

// Register <span> input
/** @see adverts_field_label() */
adverts_form_add_field("adverts_field_label", array(
    "renderer" => "adverts_field_label",
    "callback_save" => null,
    "callback_bind" => null,
));

// Register <input type="hidden" /> input
/** @see adverts_field_hidden() */
adverts_form_add_field("adverts_field_hidden", array(
    "renderer" => "adverts_field_hidden",
    "callback_save" => "adverts_save_single",
    "callback_bind" => "adverts_bind_single",
));

// Register <input type="text" /> input
/** @see adverts_field_text() */
adverts_form_add_field("adverts_field_text", array(
    "renderer" => "adverts_field_text",
    "callback_save" => "adverts_save_single",
    "callback_bind" => "adverts_bind_single",
));

// Register <textarea></textarea> input
/** @see adverts_field_textarea() */
adverts_form_add_field("adverts_field_textarea", array(
    "renderer" => "adverts_field_textarea",
    "callback_save" => "adverts_save_single",
    "callback_bind" => "adverts_bind_single",
));

// Register <select>...</select> input
/** @see adverts_field_select() */
adverts_form_add_field("adverts_field_select", array(
    "renderer" => "adverts_field_select",
    "callback_save" => "adverts_save_multi",
    "callback_bind" => "adverts_bind_multi",
));

// Register <input type="checkbox" /> input
/** @see adverts_field_checkbox() */
adverts_form_add_field("adverts_field_checkbox", array(
    "renderer" => "adverts_field_checkbox",
    "callback_save" => "adverts_save_multi",
    "callback_bind" => "adverts_bind_multi",
));

// Register <input type="radio" /> input
/** @see adverts_field_radio() */
adverts_form_add_field("adverts_field_radio", array(
    "renderer" => "adverts_field_radio",
    "callback_save" => "adverts_save_single",
    "callback_bind" => "adverts_bind_single",
));

// Register custom image upload field
/** @see adverts_field_gallery() */ 
adverts_form_add_field("adverts_field_gallery", array(
    "renderer" => "adverts_field_gallery",
    "callback_save" => null,
    "callback_bind" => null,
));

// Register <input type="hidden" /> input
/** @see adverts_field_account() */
adverts_form_add_field("adverts_field_account", array(
    "renderer" => "adverts_field_account",
    "callback_save" => "adverts_save_multi",
    "callback_bind" => "adverts_bind_multi",
));

/* REGISTER FORM FILTERS */

// Register money filter (text input with currency validation)
/** @see adverts_filter_money() */
adverts_form_add_filter("money", array(
    "description" => __( "Money - converts string formatted as price to float.", "adverts" ),
    "callback" => "adverts_filter_money"
));

// Register money filter (text input with currency validation)
/** @see adverts_filter_money() */
adverts_form_add_filter("url", array(
    "description" => __( "URL - converts string to a valid URL (if possible).", "adverts" ),
    "callback" => "adverts_filter_url"
));

// Register URL filter (text input with URL validation)
/** @see adverts_filter_money() */
adverts_form_add_filter("int", array(
    "description" => __( "Integer - converts string to absolute integer.", "adverts" ),
    "callback" => "adverts_filter_int"
));

// Register number filter
/** @see adverts_filter_money() */
adverts_form_add_filter("number", array(
    "description" => __( "Number - converts string to a number.", "adverts" ),
    "callback" => "adverts_filter_number"
));

/* REGISTER FORM VALIDATORS */

// Register "is required" validator
/** @see adverts_is_required() */
adverts_form_add_validator("is_required", array(
    "callback" => "adverts_is_required",
    "label" => __( "Is Required", "adverts" ),
    "params" => array(),
    "default_error" => __( "Field cannot be empty.", "adverts" ),
    "on_failure" => "break",
    "validate_empty" => true
));

// Register "is email" validator
/** @see adverts_is_email() */
adverts_form_add_validator("is_email", array(
    "callback" => "adverts_is_email",
    "label" => __( "Email", "adverts" ),
    "params" => array(),
    "default_error" => __( "Provided email address is invalid.", "adverts" ),
    "validate_empty" => false
));

// Register "is_email_registered" validator
/** @see adverts_is_email_registered() */
adverts_form_add_validator("is_email_registered", array(
    "callback" => "adverts_is_email_registered",
    "label" => __( "Email", "adverts" ),
    "params" => array(),
    "default_error" => __( "Cannot create account. User with this email address already exists.", "adverts" ),
    "validate_empty" => false
));

// Register "is url" validator
/** @see adverts_is_url() */
adverts_form_add_validator("is_url", array(
    "callback" => "adverts_is_url",
    "label" => __( "URL", "adverts" ),
    "params" => array(),
    "default_error" => __( "Provided URL is invalid.", "adverts" ),
    "validate_empty" => false
));

// Register "is integer" validator
/** @see adverts_is_integer() */
adverts_form_add_validator("is_integer", array(
    "callback" => "adverts_is_integer",
    "label" => __( "Is Integer", "adverts" ),
    "params" => array(),
    "default_error" => __( "Provided value is not an integer.", "adverts" ),
    "validate_empty" => false
));

// Register "is integer" validator
/** @see adverts_is_number() */
adverts_form_add_validator("is_number", array(
    "callback" => "adverts_is_number",
    "label" => __( "Is Number", "adverts" ),
    "params" => array(),
    "default_error" => __( "Provided value is not a number.", "adverts" ),
    "validate_empty" => false
));

// Register "string length" validator
/** @see adverts_string_length() */
adverts_form_add_validator("string_length", array(
    "callback" => "adverts_string_length",
    "label" => __( "String Length", "adverts" ),
    "params" => array(),
    "default_error" => __( "Incorrect string length.", "adverts" ),
    "message" => array(
        "to_short" => __( "Text needs to be at least %min% characters long.", "adverts" ),
        "to_long" => __( "Text cannot be longer than %max% characters.", "adverts")
    ),
    "validate_empty" => false
));

// Register "max_choices" validator
/** @see adverts_max_choices() */
adverts_form_add_validator("max_choices", array(
    "callback" => "adverts_max_choices",
    "label" => __( "Max Choices", "adverts" ),
    "params" => array(),
    "default_error" => __( "You cannot select more than %max_choices% items.", "adverts" ),
    "message" => array(),
    "validate_empty" => false
));

