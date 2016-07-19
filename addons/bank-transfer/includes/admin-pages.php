<?php
/**
 * Bank Transfer Module Admin Pages
 * 
 * This file contains function to handle bank transfer module logic in wp-admin 
 * and config form.
 *
 * @package     Adverts
 * @subpackage  BankTransfer
 * @copyright   Copyright (c) 2015, Grzegorz Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

/**
 * Renders config Bank Transfer config form.
 * 
 * The page is rendered in wp-admin / Classifieds / Options / Bank Transfer 
 * 
 * @since 0.1
 * @return void
 */
function adext_bank_transfer_page_options() {
    
    wp_enqueue_style( 'adverts-admin' );
    $flash = Adverts_Flash::instance();
    $error = array();

    $scheme = Adverts::instance()->get("form_bank_transfer_config");
    $form = new Adverts_Form( $scheme );
    
    $button_text = __("Update Options", "adverts");
    
    if(isset($_POST) && !empty($_POST)) {
        $form->bind( stripslashes_deep( $_POST ) );
        $valid = $form->validate();

        if($valid) {

            update_option("adext_bank_transfer_config", $form->get_values());
            $flash->add_info( __("Settings updated.", "adverts") );
        } else {
            $flash->add_error( __("There are errors in your form.", "adverts") );
        }
    } else {
        $form->bind( get_option ( "adext_bank_transfer_config", array() ) );
    }
    
    include ADVERTS_PATH . 'addons/bank-transfer/admin/options.php';
}

// Bank Transfer config form
Adverts::instance()->set("form_bank_transfer_config", array(
    "name" => "",
    "action" => "",
    "field" => array(
        array(
            "name" => "custom_title",
            "type" => "adverts_field_text",
            "label" => __("Payment Name", "adverts"),
            "hint" => __("Payment gateway name visible in the frontend when making a payment.", "adverts"),
            "placeholder" => adverts_config_default('bank_transfer.custom_title'),
            "order" => 10,
            "class" => "",
        ),
        array(
            "name" => "custom_text",
            "type" => "adverts_field_textarea",
            "mode" => "tinymce-mini",
            "label" => __("Message", "adverts"),
            "placeholder" => adverts_config_default('bank_transfer.custom_text'),
            "value" => adverts_config_default('bank_transfer.custom_text'),
            "hint" => __("User will see this message when asked to make payment, make sure that instructions here will make it as easy as possible to make a payment.<br/>Allowed variables:<br/>- {total} - total amount to pay (for example $10.00)<br/>- {order_number} - unique order number<br/>To revert to default message remove whole text from editor and save the form.", "adverts"),
            "order" => 10,
            "class" => "",
        )
    )
));



