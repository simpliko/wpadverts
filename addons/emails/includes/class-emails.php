<?php

class Adext_Emails {
    
    public $admin = null;
    
    public $messages = null;
    
    public $parser = null;
    
    /**
     * Class constructor
     * 
     * @since   1.3.0
     * @return  self
     */
    public function __construct() {
        
        add_action( "admin_init", array( $this, "init_admin" ) );
        
        if( is_admin() ) {
            include_once ADVERTS_PATH . 'addons/emails/includes/class-emails-admin.php';
            $this->admin = new Adext_Emails_Admin();
        }
        
        if( adverts_config( "emails.enable_html_emails" ) == 1 ) {
            add_filter( "adverts_form_load", array( $this, "enable_html_body" ) );
            add_filter( "wpadverts_message", array( $this, "add_html_header" ) );
        }
        
        include_once ADVERTS_PATH . 'includes/class-messages.php';
        include_once ADVERTS_PATH . 'addons/emails/includes/class-emails-parser.php';
        
        $this->messages =  new Adverts_Messages();
        $this->parser = new Adext_Emails_Parser();
        
        $this->parser->add_function( "meta", array( $this, "get_meta" ) );
        $this->parser->add_function( "taxonomy", array( $this, "get_taxonomy" ) );
        
        add_filter( "wpadverts_messages_register", array( $this->messages, "load" ) );
        add_filter( "wpadverts_message", array( $this->parser, "compile" ), 10, 3 );
        
        $this->messages->register_messages();
        $this->messages->register_actions();
    }
    
    public function init_admin() {
        wp_register_script( 'adverts-emails-admin', ADVERTS_URL . '/addons/emails/assets/js/emails-edit.js', array( 'jquery', 'wp-util' ), '1.3.0' );
        wp_register_style( 'adverts-emails-admin', ADVERTS_URL . '/addons/emails/assets/css/emails-admin.css', array(), '1.3.0' );
    }
    
    public function get_meta( $post, $meta_key ) {
        if( ! is_int( $post ) ) {
            $post_id = $post->ID;
        } else {
            $post_id = $post;
        }
        
        return get_post_meta( $post_id, $meta_key, true );
    }
    
    public function get_taxonomy( $post, $tax_name ) {
        
    }
    
    public function add_html_header( $args ) {
        if( ! isset( $args["headers"] ) ) {
            $args["headers"] = array();
        }
        
        $args["headers"]["Content-Type"] = "text/html; charset=utf-8";
        return $args;
    }
    
    public function enable_html_body( $form ) {
        
        if( $form["name"] !== "adext_emails_edit" ) {
            return $form;
        }

        foreach( $form["field"] as $key => $field ) {
            if( $field["name"] == "message_body" ) {
                $form["field"][$key]["mode"] = "tinymce-full";
            }
        }
        
        return $form;
    }
}
