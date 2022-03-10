<?php

class WPAdverts_Honeypot {

    public function __construct() {

        add_action( "adverts_form_load", array( $this, "form_load" ), 5000 );
        add_action( "wp_head", array( $this, "head" ) ); 

        do_action( "wpadverts_honeypot_init", $this );
    }

    public function is_rest() {
        if ( defined('REST_REQUEST') && REST_REQUEST ) {
            return true;
        } else {
            return false;
        }
    }

    public function get_supported_forms() {
        return apply_filters( "wpadverts_honeypot_forms", array( "advert", "contact" ) );
    }

    public function register_field_and_validator() {

        adverts_form_add_validator("wpadverts_honeypot_validator", array(
            "callback" => array( $this, "validator" ),
            "label" => "",
            "params" => array(),
            "default_error" => __( "Are you a bot trying to submit a classified Ad?", "wpadverts" ),
            "validate_empty" => false
        ));
    }

    public function form_load( $form ) {

        if( $this->is_rest() ) {
           return $form; 
        }

        if( ! in_array( $form["name"], $this->get_supported_forms() ) ) {
            return $form;
        }

        $form["field"][] = apply_filters( "wpadverts_honeypot_input", array(
            "name" => adverts_config( "moderate.honeypot_name"),
            "type" => "adverts_field_text",
            "label" => adverts_config( "moderate.honeypot_title" ),
            "value" => "",
            "order" => 9999,
            "validator" => array(
                array( "name" => "wpadverts_honeypot_validator" )
            ),
            "attr" => array(
                "autocomplete" => "off"
            )
        ) );

        return $form;
    }
    
    public function validator( $data ) {
        if( strlen( trim( $data ) ) > 0 ) {
            add_filter( "adverts_flash_data", array( $this, "flash_data" ) );
            return "invalid";
        } else {
            return true;
        }
    }

    public function flash_data( $data ) {
        $data["error"] = array(
            array(
                "message" => __( "Are you a bot trying to submit a classified Ad?", "wpadverts" ),
                "icon" => "adverts-icon-user-secret"
            )
        );
        return $data;
    }

    public function head() {
        $hp = adverts_config( "moderate.honeypot_name" );
        echo sprintf( '<style type="text/css">.wpa-field--%s, .adverts-field-name-%s { display: none !important }</style>', $hp, $hp );
    }
}

