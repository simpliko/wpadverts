<?php

class Adext_Emails {
    
    public $admin = null;
    
    public $messages = null;
    
    public $parser = null;
    
    public function __construct() {
        
        add_action( "admin_init", array( $this, "init_admin" ) );
        
        if( is_admin() ) {
            include_once ADVERTS_PATH . 'addons/emails/includes/class-emails-admin.php';
            $this->admin = new Adext_Emails_Admin();
        }
        
        include_once ADVERTS_PATH . 'includes/class-messages.php';
        include_once ADVERTS_PATH . 'addons/emails/includes/class-emails-parser.php';
        
        $this->messages =  new Adverts_Messages();
        $this->parser = new Adext_Emails_Parser();
        
        add_filter( "wpadverts_messages_register", array( $this->messages, "load" ) );
        add_filter( "wpadverts_message", array( $this->parser, "compile" ), 10, 3 );
        
        $this->messages->register_messages();
        $this->messages->register_actions();
    }
    
    public function init_admin() {
        


        wp_register_script( 'adverts-emails-admin', ADVERTS_URL . '/addons/emails/assets/js/emails-edit.js', array( 'jquery', 'wp-util' ), '1.3.0' );
        wp_register_style( 'adverts-emails-admin', ADVERTS_URL . '/addons/emails/assets/css/adverts-admin.css', array(), '1.3.0' );
    }
}
