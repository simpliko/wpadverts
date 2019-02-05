<?php
/**
 * Emails Module
 * 
 * The main Emails Module class it registers all actions, filters and if needed
 * creates $admin object.
 * 
 * @author Grzegorz Winiarski
 * @since 1.3.0
 * @package Adverts
 * @subpackage Emails
 */

class Adext_Emails {
    
    /**
     * Admin Object
     *
     * @since 1.3.0
     * @var Adext_Emails_Admin
     */
    public $admin = null;
    
    /**
     * Messages Object
     *
     * @since 1.3.0
     * @var Adext_Emails_Messages
     */
    public $messages = null;
    
    /**
     * Text Parser
     * 
     * The default parser used to parse email variables.
     * 
     * @since 1.3.0
     * @var Adext_Emails_Parser
     */
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
        
        include_once ADVERTS_PATH . 'addons/emails/includes/class-emails-messages.php';
        include_once ADVERTS_PATH . 'addons/emails/includes/class-emails-parser.php';
        
        $this->messages =  new Adext_Emails_Messages();
        $this->parser = new Adext_Emails_Parser();
        
        $this->parser->add_function( "meta", array( $this, "get_meta" ) );
        $this->parser->add_function( "terms", array( $this, "get_terms" ) );
        
        add_filter( "wpadverts_messages_register", array( $this->messages, "load" ) );
        add_filter( "wpadverts_message", array( $this->parser, "compile" ), 10, 3 );
        
        $this->messages->register_messages();
        $this->messages->register_actions();
    }
    
    /**
     * init_admin filter
     * 
     * This function is executed by admin_init action, registered in self::__construct()
     * 
     * @see self::__construct()
     * @see admin_init action
     * 
     * @since   1.3.0
     * @return  void
     */
    public function init_admin() {
        wp_register_script( 'adverts-emails-admin', ADVERTS_URL . '/addons/emails/assets/js/emails-edit.js', array( 'jquery', 'wp-util' ), '1.3.0' );
        wp_register_style( 'adverts-emails-admin', ADVERTS_URL . '/addons/emails/assets/css/emails-admin.css', array(), '1.3.0' );
    }
    
    /**
     * Calls a get_post_meta() function
     * 
     * This function is used as a registered function in the emails templates
     * basically it allows short syntax for calling a get_post_meta()
     * 
     * @since   1.3.0
     * @param   mixed    $post          Either post_id or WP_Post object
     * @param   string   $meta_key      Name of a the meta field
     * @return  string
     */
    public function get_meta( $post, $meta_key ) {
        if( ! is_int( $post ) ) {
            $post_id = $post->ID;
        } else {
            $post_id = $post;
        }
        
        return get_post_meta( $post_id, $meta_key, true );
    }
    
    /**
     * Returns taxonomy value
     * 
     * @since   1.3.0
     * @param   mixed    $post          Either post_id or WP_Post object
     * @param   string   $tax_name      Name of a the taxonomy to check
     * @return  string
     */
    public function get_terms( $post, $tax_name ) {
        if( ! is_int( $post ) ) {
            $post_id = $post->ID;
        } else {
            $post_id = $post;
        }
        
        $terms = wp_get_post_terms( $post_id, $tax_name );
        $terms_list = array();
        
        foreach( $terms as $term ) {
            $terms_list[] = $term->name;
        }
        
        return join( ", ", $terms_list );
    }
    
    /**
     * Adds "Content-Type: text/html" header to all WPAdverts emails
     * 
     * This function is executed by wpadverts_message filter registered in
     * self::__construct()
     * 
     * @see wpadverts_message filter
     * @see self::__construct()
     * 
     * @since   1.3.0
     * @param   array   $args   Data to be passed to wp_mail() function
     * @return  array
     */
    public function add_html_header( $args ) {
        if( ! isset( $args["headers"] ) ) {
            $args["headers"] = array();
        }
        
        $args["headers"]["Content-Type"] = "text/html; charset=utf-8";
        return $args;
    }
    
    /**
     * Enables tinyMCE editor for email body in 
     * wp-admin / Classifieds / Options / Emails / Edit panel.
     * 
     * The function is executed by adverts_form_load filter registered in self::__construct()
     * 
     * @see adverts_form_load filter
     * @see self::__construct()
     * 
     * @since   1.3.0
     * @param   array   $form   Form scheme
     * @return  array           Updated form scheme
     */
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
