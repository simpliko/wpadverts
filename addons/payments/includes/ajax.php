<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_ajax_adext_payments_render', 'adext_payments_ajax_render');
add_action('wp_ajax_nopriv_adext_payments_render', 'adext_payments_ajax_render');

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
    
    $listing_id = adverts_request( "listing_id" );
    $response = null;
    
    $data = array();
    $data["page_id"] = adverts_request( "page_id" );
    $data["listing_id"] = adverts_request( "listing_id" );
    $data["object_id"] = adverts_request( "object_id" );
    $data["payment_for"] = "post";
    $data["gateway_name"] = $gateway_name;
    $data["bind"] = array();
    foreach(adverts_request( 'form', array() ) as $item) {
        $data["bind"][$item["name"]] = $item["value"];
    }
    
    $form = new Adverts_Form();
    $form->load( $gateway["form"]["payment_form"] );
    $form->bind( $data["bind"] );
    
    if( isset($data["bind"]) && !empty( $data["bind"] ) ) {
        
        $isValid = $form->validate();
        
        if($isValid) {
            
            $pricing = get_post( $data["listing_id"] );
            $price = get_post_meta( $listing_id, "adverts_price", true );
            
            $payment_data = array(
                'post_title'    =>  $form->get_value("adverts_person"),
                'post_content'  => '',
                'post_status'   => 'pending',
                'post_type'     => 'adverts-payment'
            );
            
            $meta = array(
                "pricing" => array(
                    "post_title" => $pricing->post_title,
                    "visible" => get_post_meta( $pricing->ID, "adverts_visible", true )
                ),
            );
            
            $payment_id = wp_insert_post( $payment_data );
            update_post_meta( $payment_id, 'adverts_person', $form->get_value('adverts_person') );
            update_post_meta( $payment_id, 'adverts_email', $form->get_value('adverts_email') );
            update_post_meta( $payment_id, '_adverts_user_ip', adverts_get_ip() );
            update_post_meta( $payment_id, '_adverts_user_id', wp_get_current_user()->ID );
            update_post_meta( $payment_id, '_adverts_object_id', $data["object_id"] );
            update_post_meta( $payment_id, '_adverts_pricing_id', $data["listing_id"] );
            update_post_meta( $payment_id, '_adverts_payment_type', $pricing->post_type );
            update_post_meta( $payment_id, '_adverts_payment_gateway', $data["gateway_name"] );
            update_post_meta( $payment_id, '_adverts_payment_for', $data["payment_for"] );
            update_post_meta( $payment_id, '_adverts_payment_paid', "0" );
            update_post_meta( $payment_id, '_adverts_payment_total', $price );
            update_post_meta( $payment_id, '_adverts_payment_meta', $meta );
            
            $data["price"] = $price;
            $data["form"] = $form->get_values();
            $data["payment_id"] = $payment_id;
            
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