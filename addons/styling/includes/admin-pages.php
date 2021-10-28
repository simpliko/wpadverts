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
    
    wp_enqueue_style( 'adverts-admin' );
    $flash = Adverts_Flash::instance();
    $error = array();
    
    $options = get_option ( "adext_contact_form_config", array() );
    if( $options === null || empty( $options ) ) {
        $options = adverts_config( "contact_form.ALL" );
    }

    $scheme = Adverts::instance()->get("form_contact_form_config");
    $form = new Adverts_Form( $scheme );
    
    $button_text = __("Update Options", "wpadverts");
    
    if(isset($_POST) && !empty($_POST)) {
        $form->bind( stripslashes_deep( $_POST ) );
        $valid = $form->validate();

        if($valid) {
            $form_data = $form->get_values();
            
            if(!isset($form_data['show_phone'])) {
                $form_data['show_phone'] = 0;
            }
            if(!isset($form_data['reveal_on_click'])) {
                $form_data['reveal_on_click'] = "0";
            } else {
                $form_data['reveal_on_click'] = "1";
            }
            
            update_option("adext_contact_form_config", $form_data);
            $flash->add_info( __("Settings updated.", "wpadverts") );
        } else {
            $flash->add_error( __("There are errors in your form.", "wpadverts") );
        }
    } else {
        $form->bind( $options );
    }
    
    include ADVERTS_PATH . 'addons/styling/admin/options.php';
}