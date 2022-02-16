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
            "label" => __( 'Contact Information', 'wpadverts' )
        ),
        array(
            "name" => "_adverts_account",
            "type" => "adverts_field_account",
            "order" => 2,
            "label" => __( "Account", "wpadverts" ),
        ),
        array(
            "name" => "adverts_person",
            "type" => "adverts_field_text",
            "order" => 3,
            "label" => __( "Contact Person", "wpadverts" ),
            "is_required" => true,
            "validator" => array( 
                array( "name" => "is_required" ),
            )
        ),
        array(
            "name" => "adverts_email",
            "type" => "adverts_field_text",
            "order" => 4,
            "label" => __( "Email", "wpadverts" ),
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
            "label" => __( "Phone Number", "wpadverts"),
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
            "label" => __( 'Item Information', 'wpadverts' )
        ),
        array(
            "name" => "post_title",
            "type" => "adverts_field_text",
            "order" => 7,
            "label" => __( "Title", "wpadverts" ),
            "validator" => array(
                array( "name"=> "is_required" )
            )
        ),
        array(
            "name" => "advert_category",
            "type" => "adverts_field_select",
            "order" => 8,
            "label" => __("Category", "wpadverts"),
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
            "label" => __( "Gallery", "wpadverts" ),
            "validator" => array(
                array(
                    "name" => "upload_type",
                    "params" => array( "allowed" => array( "image", "video" ) )
                )
            )
        ),
        array(
            "name" => "post_content",
            "type" => "adverts_field_textarea",
            "order" => 10,
            "label" => __( "Description", "wpadverts" ),
            "validator" => array(
                array( "name"=> "is_required" )
            ),
            "mode" => "tinymce-mini"
        ),
        array(
            "name" => "adverts_price",
            "type" => "adverts_field_text",
            "order" => 11,
            "label" => __("Price", "wpadverts"),
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
            "label" => __( "Location", "wpadverts" ),
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
            "placeholder" => __("Keyword ...", "wpadverts"),
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
            "placeholder" => __("Location ...", "wpadverts"),
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

// Register <input type="password" /> input
/** @see adverts_field_text() */
adverts_form_add_field("adverts_field_password", array(
    "renderer" => "adverts_field_password",
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
    "description" => __( "Money - converts string formatted as price to float.", "wpadverts" ),
    "callback" => "adverts_filter_money"
));

// Register money filter (text input with currency validation)
/** @see adverts_filter_money() */
adverts_form_add_filter("url", array(
    "description" => __( "URL - converts string to a valid URL (if possible).", "wpadverts" ),
    "callback" => "adverts_filter_url"
));

// Register URL filter (text input with URL validation)
/** @see adverts_filter_money() */
adverts_form_add_filter("int", array(
    "description" => __( "Integer - converts string to absolute integer.", "wpadverts" ),
    "callback" => "adverts_filter_int"
));

// Register number filter
/** @see adverts_filter_money() */
adverts_form_add_filter("number", array(
    "description" => __( "Number - converts string to a number.", "wpadverts" ),
    "callback" => "adverts_filter_number"
));

// Register number filter
/** @see adverts_filter_kses() */
adverts_form_add_filter("kses", array(
    "description" => __( "Number - converts string to a number.", "wpadverts" ),
    "callback" => "adverts_filter_kses"
));

/* REGISTER FORM VALIDATORS */

// Register "is required" validator
/** @see adverts_is_required() */
adverts_form_add_validator("is_required", array(
    "callback" => "adverts_is_required",
    "label" => __( "Is Required", "wpadverts" ),
    "params" => array(),
    "default_error" => __( "Field cannot be empty.", "wpadverts" ),
    "on_failure" => "break",
    "validate_empty" => true
));

// Register "is email" validator
/** @see adverts_is_email() */
adverts_form_add_validator("is_email", array(
    "callback" => "adverts_is_email",
    "label" => __( "Email", "wpadverts" ),
    "params" => array(),
    "default_error" => __( "Provided email address is invalid.", "wpadverts" ),
    "validate_empty" => false
));

// Register "is_email_registered" validator
/** @see adverts_is_email_registered() */
adverts_form_add_validator("is_email_registered", array(
    "callback" => "adverts_is_email_registered",
    "label" => __( "Email", "wpadverts" ),
    "params" => array(),
    "default_error" => __( "Cannot create account. User with this email address already exists.", "wpadverts" ),
    "validate_empty" => false
));

// Register "is url" validator
/** @see adverts_is_url() */
adverts_form_add_validator("is_url", array(
    "callback" => "adverts_is_url",
    "label" => __( "URL", "wpadverts" ),
    "params" => array(),
    "default_error" => __( "Provided URL is invalid.", "wpadverts" ),
    "validate_empty" => false
));

// Register "is integer" validator
/** @see adverts_is_integer() */
adverts_form_add_validator("is_integer", array(
    "callback" => "adverts_is_integer",
    "label" => __( "Is Integer", "wpadverts" ),
    "params" => array(),
    "default_error" => __( "Provided value is not an integer.", "wpadverts" ),
    "validate_empty" => false
));

// Register "is integer" validator
/** @see adverts_is_number() */
adverts_form_add_validator("is_number", array(
    "callback" => "adverts_is_number",
    "label" => __( "Is Number", "wpadverts" ),
    "params" => array(),
    "default_error" => __( "Provided value is not a number.", "wpadverts" ),
    "validate_empty" => false
));

// Register "string length" validator
/** @see adverts_string_length() */
adverts_form_add_validator("string_length", array(
    "callback" => "adverts_string_length",
    "label" => __( "String Length", "wpadverts" ),
    "params" => array(),
    "default_error" => __( "Incorrect string length.", "wpadverts" ),
    "message" => array(
        "to_short" => __( "Text needs to be at least %min% characters long.", "wpadverts" ),
        "to_long" => __( "Text cannot be longer than %max% characters.", "wpadverts")
    ),
    "validate_empty" => false
));

// Register "max_choices" validator
/** @see adverts_max_choices() */
adverts_form_add_validator("max_choices", array(
    "callback" => "adverts_max_choices",
    "label" => __( "Max Choices", "wpadverts" ),
    "params" => array(),
    "default_error" => __( "You cannot select more than %max_choices% items.", "wpadverts" ),
    "message" => array(),
    "validate_empty" => false
));

// Register "verify_choices" validator
/** @see adverts_verify_choices() */
adverts_form_add_validator("verify_choices", array(
    "callback" => "adverts_verify_choices",
    "label" => __( "Verify Choices", "wpadverts" ),
    "params" => array(),
    "default_error" => __( "One or more selected values are not available in the options list.", "wpadverts" ),
    "message" => array(),
    "validate_empty" => false
));

// Register "is_spam" validator
/** @see adverts_is_spam() */
adverts_form_add_validator("is_spam", array(
    "callback" => "adverts_is_spam",
    "label" => __( "Spam", "wpadverts" ),
    "params" => array(),
    "default_error" => __( "Provided text is invalid.", "wpadverts" ),
    "message" => array(
        "too_many_links" => __( "Text cannot have more than %max_links% links.", "wpadverts" ),
        "bad_words" => __( "Text contains phrases that are considered SPAM.", "wpadverts" )
    ),
    "validate_empty" => false
));

// Register "upload_limit" validator
/** @see adverts_validate_upload_limit() */
adverts_form_add_validator("upload_limit", array(
    "callback" => "adverts_validate_upload_limit",
    "label" => __( "Upload Limit", "wpadverts" ),
    "params" => array(),
    "default_error" => "",
    "message" => array(
        "max_limit" => __( "You cannot upload more than %max% files.", "wpadverts" ),
        "min_limit" => __( "You need to upload at least %min% files.", "wpadverts" )
    ),
    "validate_empty" => true
));


// Register "upload_size" validator
/** @see adverts_validate_upload_size() */
adverts_form_add_validator("upload_size", array(
    "callback" => "adverts_validate_upload_size",
    "label" => __( "Upload Type", "wpadverts" ),
    "params" => array(),
    "default_error" => "",
    "message" => array(
        "too_big" => __( "The max. allowed file size is %max%.", "wpadverts" ),
        "too_small" => __( "The min. allowed file size is %min%.", "wpadverts" )
    ),
    "validate_empty" => false
));

// Register "upload_type" validator
/** @see adverts_validate_upload_type() */
adverts_form_add_validator("upload_type", array(
    "callback" => "adverts_validate_upload_type",
    "label" => __( "Upload Type", "wpadverts" ),
    "params" => array(),
    "default_error" => __( "This file type is not allowed.", "wpadverts" ),
    "message" => array(),
    "validate_empty" => false
));

// Register "upload_dimensions" validator
/** @see adverts_validate_upload_dimensions() */
adverts_form_add_validator("upload_dimensions", array(
    "callback" => "adverts_validate_upload_dimensions",
    "label" => __( "Upload Dimensions", "wpadverts" ),
    "params" => array(),
    "default_error" => __( "The file size is incorrect.", "wpadverts" ),
    "message" => array(
        "cannot_check" => __( "Cannot validate uploaded image width and height.", "wpadverts" ),
        "incorrect_min_width" => __( "The image min. width should be %min_width%.", "wpadverts" ),
        "incorrect_max_width" => __( "The image max. width should be %max_width%.", "wpadverts" ),
        "incorrect_min_height" => __( "The image min. height should be %min_height%.", "wpadverts" ),
        "incorrect_max_height" => __( "The image max. height should be %max_height%.", "wpadverts" ),
    ),
    "validate_empty" => false
));

include_once ADVERTS_PATH . '/includes/class-field-autocomplete.php';

new Adverts_Field_Autocomplete();