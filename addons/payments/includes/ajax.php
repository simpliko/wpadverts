<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_ajax_adext_payments_render', 'adext_payments_ajax_render');
add_action('wp_ajax_nopriv_adext_payments_render', 'adext_payments_ajax_render');

add_action('wp_ajax_adext_payments_complete_payment', 'adext_payments_ajax_complete_payment');
add_action('wp_ajax_nopriv_adext_payments_complete_payment', 'adext_payments_ajax_complete_payment');

/**
 * AJAX Function renders payment form in [adverts_add] third step.
 * 
 * This function renders a proper payment form based on $_REQUEST['gateway'] value
 * and echos it to the browser as a JSON code.
 * 
 * @since 0.1
 * @return void 
 */
function adext_payments_ajax_render() {
    
    $gateway_name = adverts_request('gateway');
    $gateway = adext_payment_gateway_get( $gateway_name );
    
    $listing_id = absint( adverts_request( "listing_id" ) );
    $payment_id = absint( adverts_request( "payment_id" ) );
    
    $response = null;
    
    $data = array();
    $data["page_id"] = adverts_request( "page_id" );
    $data["listing_id"] = adverts_request( "listing_id" );
    $data["object_id"] = adverts_request( "object_id" );
    $data["payment_id"] = adverts_request( "payment_id" );
    $data["payment_for"] = adverts_request( "object_type", "post" );
    $data["gateway_name"] = $gateway_name;
    $data["bind"] = array();
    foreach(adverts_request( 'form', array() ) as $item) {
        $data["bind"][$item["name"]] = $item["value"];
    }

    $form = new Adverts_Form();
    $form->load( $gateway["form"]["payment_form"] );
    $form->bind( $data["bind"] );
    
    if( isset( $data["bind"] ) && !empty( $data["bind"] ) ) {
        
        $isValid = $form->validate();
        
        if($isValid) {
            
            $pricing = get_post( $data["listing_id"] );
            $price = get_post_meta( $listing_id, "adverts_price", true );
            
            
            $price = get_post_meta( $payment_id, "_adverts_payment_total", true );
            
            $data["price"] = $price;
            $data["form"] = $form->get_values();
            $data["payment_id"] = $payment_id;
            
            wp_update_post( array(
                'ID' => $payment_id,
                'post_title' => $form->get_value( "adverts_person" ),
                'post_status' => 'pending'
            ) );
            
            update_post_meta( $payment_id, 'adverts_person', $form->get_value('adverts_person') );
            update_post_meta( $payment_id, 'adverts_email', $form->get_value('adverts_email') );
            update_post_meta( $payment_id, '_adverts_payment_gateway', $data["gateway_name"] );
            
            $data = apply_filters("adverts_payments_order_create", $data);
            
            $response = call_user_func( $gateway["callback"]["render"], $data );
        } 
    }
    
    if($response === null) {
        ob_start();
        include ADVERTS_PATH . 'templates/form.php';
        $html_form = ob_get_clean();

        $response = array(
            "result" => 0,
            "html" => $html_form,
            "execute" => null
        );
    }
    
    echo json_encode( $response );
    exit;
}

function adext_payments_ajax_complete_payment() {
    
    adverts_init_frontend();
    adext_payments_init_frontend();
    
    $title = __( "Complete Payment", "wpadverts" );
    $text_direction = 'ltr';
    if( function_exists( 'is_rtl' ) && is_rtl() ) {
        $text_direction = 'rtl';
    }
    $content = shortcode_payments_checkout( array() );
    
    wp_enqueue_style( 'adverts-payments-white-page' );
    
    include apply_filters( "adverts_template_load", ADVERTS_PATH . 'addons/payments/templates/white-page.php' );
    exit;
}