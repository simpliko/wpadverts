<?php
/**
 * Core Admin Pages
 * 
 * This file contains function to handle default/core config logic in wp-admin 
 * and config form.
 *
 * @package     Adverts
 * @subpackage  Core
 * @copyright   Copyright (c) 2015, Grzegorz Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

/**
 * Renders default/core config form.
 * 
 * The page is rendered in wp-admin / Classifieds / Options / Core 
 * 
 * @since 0.1
 * @return void
 */
function adext_core_page_options() {
    
    $page_title = __("Core Options", "adverts");
    $button_text = __("Update Options", "adverts");
    
    wp_enqueue_style( 'adverts-admin' );
    $flash = Adverts_Flash::instance();
    $error = array();
    
    $scheme = Adverts::instance()->get("form_core_config");
    $form = new Adverts_Form( $scheme );
    
    if(isset($_POST) && !empty($_POST)) {
        $form->bind( stripslashes_deep( $_POST ) );
        $valid = $form->validate();

        if($valid) {
            
            $data = $form->get_values();
            $data["module"] = adverts_config( 'config.module' );
            
            update_option("adverts_config", $data );
            $flash->add_info( __("Settings updated.", "adverts") );
        } else {
            $flash->add_error( __("There are errors in your form.", "adverts") );
        }
    } else {
        $form->bind( adverts_config("config.ALL") );
    }
    
    include ADVERTS_PATH . 'addons/core/admin/options.php';
}

$currency_code_options = array();

foreach(adverts_currency_list() as $tmp_currency) {
    
    $sign = "";
    
    if($tmp_currency['sign']) {
        $sign = ' (' . $tmp_currency['sign'] . ')';
    }
    
    $currency_code_options[] = array(
        "value" => $tmp_currency["code"],
        "text" => $tmp_currency["label"] . $sign
    );
}

/**
 * Register <select> input with list of Pages as options.
 * 
 * This is basically a wrapper for wp_dropdown_pages() WordPress function.
 * 
 * @see wp_dropdown_pages()
 * 
 * @param array $field Fields settings
 * @since 0.3
 * @return void
 */
function adverts_dropdown_pages( $field ) {
    
    if(isset($field["value"])) {
        $value = $field["value"];
    } else {
        $value = null;
    }
    
    $args = array(
        'selected' => $value, 
        'echo' => 1,
	'name' => $field["name"], 
        'id' => $field["name"],
	'show_option_none' => ' ',
        'option_none_value' => 0
    );
    
    wp_dropdown_pages( $args );
}

// Register <select> with list of pages 
/** @see adverts_dropdown_pages() */
adverts_form_add_field("adverts_dropdown_pages", array(
    "renderer" => "adverts_dropdown_pages",
    "callback_bind" => "adverts_bind_single",
    "callback_save" => "adverts_save_single",
));

// Core options config form
Adverts::instance()->set("form_core_config", array(
    "name" => "",
    "action" => "",
    "field" => array(
        array(
            "name" => "_common_settings",
            "type" => "adverts_field_header",
            "order" => 10,
            "label" => __( 'Common Settings', 'adverts' ),
            "title" => __( 'Common Settings', 'adverts' )
        ),
        array(
            "name" => "ads_list_id",
            "type" => "adverts_dropdown_pages",
            "order" => 10,
            "label" => __("Default Ads List Page", "adverts"),
            "hint" => __("Select page on which the main [adverts_list] shortcode is being used.", "adverts")
        ),
        array(
            "name" => "visibility",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __("Default Visibility", "adverts"),
            "hint" => __("Number of days Ad will be visible by default.", "adverts"),
            "validator" => array(
                array("name"=>"is_required"),
                array("name"=>"is_integer")
            )
        ),
        array(
            "name" => "_currency_settings",
            "type" => "adverts_field_header",
            "order" => 10,
            "label" => __( 'Currency Settings', 'adverts' ),
            "title" => __( 'Currency Settings', 'adverts' )
        ),
        array(
            "name" => "currency_code",
            "type" => "adverts_field_select",
            "order" => 10,
            "label" => __("Currency", "adverts"),
            "options" => $currency_code_options
        ),
        array(
            "name" => "currency_sign_type",
            "type" => "adverts_field_select",
            "order" => 10,
            "label" => __("Currency Position", "adverts"),
            "options" => array(
                array("value"=>"p", "text"=>__("Prefix - $10", "adverts")),
                array("value"=>"s", "text"=>__("Suffix - 10$", "adverts")),
            )
        ),
        array(
            "name" => "currency_decimals",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __("Decimal Places", "adverts"),
            "validator" => array(
                array("name"=>"is_required"),
                array("name"=>"is_integer")
            )
        ),
        array(
            "name" => "currency_char_decimal",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __("Decimal Separator", "adverts"),
        ),
        array(
            "name" => "currency_char_thousand",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __("Thousands Separator", "adverts"),
        ),
    )
));



