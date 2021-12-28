<?php
/**
 * Contact Form Module Admin Pages
 * 
 * This file contains function to handle contact form logic in wp-admin 
 * and config form.
 *
 * @package     Adverts
 * @subpackage  BankTransfer
 * @copyright   Copyright (c) 2016, Grzegorz Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.10
 */

/**
 * Renders config Contact Form config form.
 * 
 * The page is rendered in wp-admin / Classifieds / Options / Contact Form 
 * 
 * @since 1.0.10
 * @return void
 */
function adext_styling_page_options() {
    global $adverts_namespace;

    wp_enqueue_style( 'adverts-admin' );
    $flash = Adverts_Flash::instance();
    $error = array();
    
    $options_defaults = $adverts_namespace["blocks_styling"]["default"];

    $options = get_option( 'adverts_blocks_styling' );
    if( $options === null || empty( $options ) ) {
        $options = $options_defaults;
    }

    if( isset( $options['primary_button'] ) ) {
        $pb = $options['primary_button'];
    } else {
        $pb = $options_defaults['primary_button'];  
    }
    $pd = $options_defaults['primary_button'];    
    
    if( isset( $options['secondary_button'] ) ) {
        $sb = $options['secondary_button'];
    } else {
        $sb = $options_defaults['secondary_button'];
    }
    $sd = $options_defaults['secondary_button'];    
    
    if( isset( $options['form'] ) ) {
        $frm = $options['form'];
    } else {
        $frm = $options_defaults['form'];
    }
    $frmd = $options_defaults['form'];
    
    //echo "<pre>";var_dump($sb);echo "</pre>";

    $show_buttons = false;
    $button_text = __("Update Options", "wpadverts");

    $form = new Adverts_Form( adext_styling_page_options_demo_form() );

    include ADVERTS_PATH . 'addons/styling/admin/options.php';
    
    
}

function adext_styling_page_options_demo_form() {
    return array(
        "name" => "demo-form",
        "field" => array(
            array(
                "name" => "_item_information",
                "type" => "adverts_field_header",
                "order" => 6,
                "label" => __( 'Example Header', 'wpadverts' )
            ),
            array(
                "name" => "_post_title",
                "type" => "adverts_field_text",
                "order" => 7,
                "label" => __( "Title", "wpadverts" ),
            ),
            array(
                "name" => "_dropdown",
                "type" => "adverts_field_select",
                "order" => 7,
                "label" => __( "Dropdown", "wpadverts" ),
                "value" => "",
                "empty_option" => true,
                "empty_option_text" => __( "Select Option", "wpadverts" ),
                "options" => array(
                    array( "value" => "Option 1", "text" => "Option 1" ),
                    array( "value" => "Option 2", "text" => "Option 2" ),
                    array( "value" => "Option 3", "text" => "Option 3" ),
                )
            ),            
            array(
                "name" => "_checkbox",
                "type" => "adverts_field_checkbox",
                "order" => 7,
                "label" => __( "Checkbox", "wpadverts" ),
                "value" => "",
                "options" => array(
                    array( "value" => "Option 1", "text" => "Option 1" ),
                    array( "value" => "Option 2", "text" => "Option 2" ),
                    array( "value" => "Option 3", "text" => "Option 3" ),
                )
            ),
        )
    );
}