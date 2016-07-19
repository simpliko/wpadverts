<?php
/**
 * Bank Transfer Module
 * 
 * This module allows to accept payments using bank transfer.
 * 
 * Note. In order to use this module you need to enable Payments module first.
 *
 * @package Adverts
 * @subpackage BankTransfer
 * @author Grzegorz Winiarski
 * @version 0.1
 */

global $adverts_namespace;

// Add bank_transfer to adverts_namespace, in order to store module options and default options
$adverts_namespace['bank_transfer'] = array(
    'option_name' => 'adext_bank_transfer_config',
    'default' => array(
        'custom_title' => '',
        'custom_text' => "<p>Make a Bank Transfer to account XXX-XXX-XXX for a total of <strong>{total}</strong>.</p><p>In transfer title put <strong>{order_number}</strong></p><p>Thank You.</p>"
    )
);

// Register Bank Transfer as a payment gateway
add_action("adext_register_payment_gateway", "adext_bank_transfer_register");

/**
 * Registers Bank Transfer payment gateway
 * 
 * @uses adext_payment_gateway_add()
 * 
 * @since 0.1
 * @return void
 */
function adext_bank_transfer_register() {
    
    if( adverts_config('bank_transfer.custom_title') ) {
        $title = adverts_config('bank_transfer.custom_title');
    } else {
        $title = __("Bank Transfer", "adverts");
    }
    
    adext_payment_gateway_add("bank-transfer", array(
        "name" => "bank-transfer",
        "title" => $title,
        "order" => 10,
        "data" => array(),
        "callback" => array(
            "render" => "adext_bank_transfer_render"
        ),
        "form" => array(
            "payment_form" => array(
                "name" => "adverts-bank-transfer",
                "action" => "",
                "field" => array(
                    array(
                        "name" => "adverts_person",
                        "type" => "adverts_field_text",
                        "order" => 10,
                        "label" => __( "Contact Person", "adverts" ),
                        "is_required" => true,
                        "validator" => array( 
                            array( "name" => "is_required" ),
                        )
                    ),
                    array(
                        "name" => "adverts_email",
                        "type" => "adverts_field_text",
                        "order" => 10,
                        "label" => __( "Email", "adverts" ),
                        "is_required" => true,
                        "validator" => array( 
                            array( "name" => "is_required" ),
                            array( "name" => "is_email" )
                        )
                    )
                ) // end field
            ) // end payment_form
        ) // end form
    ));
}

/**
 * Renders Bank Transfer Payment Form
 * 
 * If user will select Bank Transfer as a payment method, this function will render
 * payment instructions.
 * 
 * It is executed in third step in [adverts_add] shortcode.
 * 
 * @param array $data Payment data
 * @return array
 */
function adext_bank_transfer_render($data) {
    
    $price = $data["price"];
    $payment_id = $data["payment_id"];

    $atts = array("class"=>"adverts-success");
    
    $repl = array(
        "{total}" => adverts_price($price),
        "{order_number}" => str_pad($payment_id, 6, "0", STR_PAD_LEFT)
    );
    
    $html = str_replace(array_keys($repl), array_values($repl), adverts_config("bank_transfer.custom_text"));
    $html = apply_filters("adext_bank_transfer_custom_text", $html, $data);

    return array(
        "result" => 1,
        "html" => Adverts_Html::build("div", $atts, wpautop( $html ) ),
        "execute" => null, // null|click|submit
        "execute_id" => ""
    );

}

// Set default values for Bank Transfer form
add_filter( "adverts_form_bind", "adext_bank_transfer_form_bind_defaults", 10, 2);

/**
 * Sets default values for Bank Transfer form
 * 
 * This function checks if current payment form is Bank Transfer. If so and 
 * the $data is empty then we set default values for contact person and email fields.
 * 
 * @param Adverts_Form $form Instance of form.
 * @param array $data User submitted form values ( key => value )
 * @return Adverts_Form Modified instance of form.
 */
function adext_bank_transfer_form_bind_defaults( Adverts_Form $form, $data ) {
    
    $scheme = $form->get_scheme();
    
    if( $scheme["name"] != "adverts-bank-transfer" ) {
        return $form;
    }
    
    if( empty( $data ) && adverts_request( "action" ) == "adext_payments_render" ) {
        $ad = get_post( adverts_request( "object_id" ) );
        
        $form->set_value( "adverts_person", get_post_meta( $ad->ID, "adverts_person", true ) );
        $form->set_value( "adverts_email", get_post_meta( $ad->ID, "adverts_email", true ) );
    }

    return $form;
}
