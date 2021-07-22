<?php

class WPAdverts_Timetrap {

    public $hash = null;

    public $delta = null;
    
    public function __construct() {

        $salt = trim( adverts_config( "moderate.timetrap_salt" ) );
        
        if( ! empty( $salt ) ) {
            $this->hash = $salt;
        } else {
            $this->hash = NONCE_SALT;
        }
        
        $this->hash = apply_filters( "wpadverts_timetrap_salt", $this->hash );
        $this->delta = absint( adverts_config( "moderate.timetrap_delta" ) );
        
        add_action( "adverts_form_load", array( $this, "form_load" ), 5000 );
        
        do_action( "wpadverts_timetrap_init", $this );
    }

    public function get_supported_forms() {
        return apply_filters( "wpadverts_timetrap_forms", array( "advert", "contact" ) );
    }
    
    public function get_field_name() {
        return apply_filters( "wpadverts_timetrap_field_name", "_timetrap_field" );
    }

    public function register_field_and_validator() {

        adverts_form_add_validator("adverts_timetrap_validator", array(
            "callback" => array( $this, "validator" ),
            "label" => "",
            "params" => array(),
            "default_error" => __( "You are filling the forms too fast!", "wpadverts" ),
            "validate_empty" => true
        ));
    }

    public function encrypt( $text ) {
        return base64_encode( openssl_encrypt ($text, "AES-128-ECB", $this->hash ) );
    }

    public function decrypt( $text ) {
        return openssl_decrypt( base64_decode( $text ), "AES-128-ECB", $this->hash );
    }

    public function form_load( $form ) {

        if( ! in_array( $form["name"], $this->get_supported_forms() ) ) {
            return $form;
        }

        $form["field"][] = apply_filters( "wpadverts_timetrap_input", array(
            "name" => $this->get_field_name(),
            "type" => "adverts_field_hidden",
            "label" => "",
            "value" => $this->encrypt( time() ),
            "order" => 9999,
            "validator" => array(
                array( "name" => "adverts_timetrap_validator" )
            )
        ) );

        if( $form["name"] == "advert" ) {
            add_filter( "adverts_add_form_bind", array( $this, "bind_add" ) );
        }
        
        add_filter( "adverts_form_bind", array( $this, "bind" ) );

        return $form;
    }
    
    /**
     * 
     * @param Adverts_Form $form
     * @return type
     */
    public function bind( $form ) {
        if( $form->get_value( $this->get_field_name() ) === null ) {
            $form->set_value( $this->get_field_name(), $this->encrypt( time() ) );
        }
        return $form;
    }
    
    public function bind_add( $form ) {
        $form[ $this->get_field_name() ] = $this->encrypt( time() );
        return $form;
    }

    public function validator( $encoded ) {
        
        $t2 = time();
        $t1 = absint( $this->decrypt( $encoded ) );

        if( ! $t1 ) {
            add_filter( "adverts_flash_data", array( $this, "flash_data" ) );
            return "invalid";
        }

        $delta = $t2-$t1;
        $min_delta = apply_filters( "wpadverts_timetrap_min_delta", $this->delta );

        if( $delta <= $min_delta ) {
            add_filter( "adverts_flash_data", array( $this, "flash_data" ) );
            return "invalid";
        } else {
            return true;
        }
    }

    public function flash_data( $data ) {
        $data["error"] = array(
            array(
                "message" => __( "You are filling the forms too fast!", "wpadverts" ),
                "icon" => "adverts-icon-user-secret"
            )
        );
        return $data;
    }
}

