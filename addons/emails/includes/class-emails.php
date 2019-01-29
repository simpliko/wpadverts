<?php

class Adext_Emails {
    
    public $admin = null;
    
    public function __construct() {
        
        add_action( "admin_init", array( $this, "init_admin" ) );
        
        if( is_admin() ) {
            include_once ADVERTS_PATH . 'addons/emails/includes/class-emails-admin.php';
            $this->admin = new Adext_Emails_Admin();
        }
    }
    
    public function init_admin() {
        


        wp_register_script( 'adverts-emails-admin', ADVERTS_URL . '/addons/emails/assets/js/emails-edit.js', array( 'jquery', 'wp-util' ), '1.3.0' );
        wp_register_style( 'adverts-emails-admin', ADVERTS_URL . '/addons/emails/assets/css/adverts-admin.css', array(), '1.3.0' );
    }
}
