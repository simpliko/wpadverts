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
     * Object instance
     *
     * @var Adext_Emails
     */
    private static $_instance = null;
    
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
    protected $_parser = null;
    
    public static function instance() {
        if( self::$_instance === null ) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    /**
     * Class constructor
     * 
     * @since   1.3.0
     * @return  self
     */
    public function __construct() {
        
        add_action( "admin_init", array( $this, "init_admin" ) );
        add_action( "init", array( $this, "init" ), 1000 );
        
        if( is_admin() ) {
            include_once ADVERTS_PATH . 'addons/emails/includes/class-emails-admin.php';
            $this->admin = new Adext_Emails_Admin();
        }
        
        if( adverts_config( "emails.enable_html_emails" ) == 1 ) {
            add_filter( "adverts_form_load", array( $this, "enable_html_body" ) );
            add_filter( "wpadverts_message", array( $this, "add_html_header" ) );
        }
        
        include_once ADVERTS_PATH . 'addons/emails/includes/class-emails-messages.php';
        include_once ADVERTS_PATH . 'addons/emails/includes/class-emails-parser-interface.php';
        include_once ADVERTS_PATH . 'addons/emails/includes/class-emails-parser.php';
        
        $this->messages =  new Adext_Emails_Messages();
        $this->_parser = new Adext_Emails_Parser();
        
        $this->_parser->add_function( "meta", array( $this, "get_meta" ) );
        $this->_parser->add_function( "terms", array( $this, "get_terms" ) );
        $this->_parser->add_function( "contact_email", array( $this, "contact_email" ) );
        $this->_parser->add_function( "admin_edit_url", array( $this, "admin_edit_url" ) );
        $this->_parser->add_function( "complete_payment_url", array( $this, "complete_payment_url" ) );
        $this->_parser->add_function( "format_date", array( $this, "format_date" ) );
        $this->_parser->add_function( "get_all_files", array( $this, "get_all_files" ) );
        
        add_filter( "wpadverts_messages_register", array( $this->messages, "load" ), 20 );
        add_filter( "wpadverts_message", array( $this->_parser, "compile" ), 10, 3 );
        add_filter( "wpadverts_message_args", array( $this, "common_args" ) );


    }
    
    /**
     * Sets a new parser
     * 
     * @since 1.3.0
     * @param Adext_Emails_Parser_Interface     $parser
     */
    public function set_parser( Adext_Emails_Parser_Interface $parser ) {
        $this->_parser = $parser;
    }
    
    /**
     * Returns Email Parser Object
     * 
     * @since  1.3.0
     * @return Adext_Emails_Parser_Interface
     */
    public function get_parser() {
        return $this->_parser;
    }
    
    /**
     * Registers common variables
     * 
     * These variables can be used in all emails sent by Emails Module.
     * 
     * This function is executed by wpadverts_message_args filter registered
     * in self::__construct()
     * 
     * @see wpadverts_message_args filter
     * 
     * @since   1.3.0
     * @param   array $args     List of message variables
     * @return  array
     */
    public function common_args( $args ) {
        $args["admin_email"] = self::admin_email();
        return $args;
    }
    
    /**
     * Registers actions and messages
     * 
     * This function is executed by "init" action registered in self::__construct() with
     * a low priority so other plugins can register their own messages.
     * 
     * @see init action
     * 
     * @since 1.3.0
     * @return void
     */
    public function init() {
        
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
    public function get_meta( $post, $meta_key, $separator = ", " ) {
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
     * Returns contact email
     * 
     * This function is registered as email function and can be used in the
     * email templates.
     * 
     * @since   1.3.0
     * @param   WP_Post     $post   Post object
     * @return  string              Contact email or empty string
     */
    public function contact_email( $post ) {
        
        if( $post instanceof WP_Post ) {
            $post_id = $post->ID;
        } else {
            $post_id = $post;
        }
        
        $contact_email = get_post_meta( $post_id, "adverts_email", true );
        
        if( $contact_email ) {
            return $contact_email;
        }
        
        $post_author = get_post_field( "post_author", $post );
        
        if( $post_author > 0 ) {
            return get_user_by( "id", $post_author )->user_email;
        }
    }
    
    /**
     * Returns Post edit URL
     * 
     * This function is registered as email function and can be used in the
     * email templates.
     * 
     * @since   1.3.0
     * @param   WP_Post     $post   Post object
     * @return  string              Admin edit URL
     */
    public function admin_edit_url( $post ) {
        $url = null;
        
        if( ! $post instanceof WP_Post ) {
            $url = "";
        } else if( in_array( $post->post_type, array( 'adverts-payment' ) ) ) {
            // advert-payment
            $url = admin_url( sprintf( 'edit.php?post_type=advert&page=adext-payment-history&edit=%d', $post->ID ) );
        } else {
            // advert, advert-author, page, post and etc.
            $url = admin_url( sprintf( 'post.php?post=%d&action=edit', $post->ID ) );
        }
        
        return apply_filters( "adext_emails_admin_edit_url", $url, $post );
    }
    
    /**
     * Returns complete payment URL
     * 
     * This function is registered as email function and can be used in the
     * email templates.
     * 
     * @since   1.3.0
     * @param   WP_Post     $post   Post object
     * @return  string              Admin edit URL
     */
    public function payment_complete_url( $post ) {
        return "@todo";
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
    
    /**
     * Returns Admin Email Address
     * 
     * This function will return admin email set in wp-admin / Classifieds / Options / Emails / Options panel
     * or in wp-admin / Settings / General panel.
     * 
     * @since   1.3.0
     * @return  string   Administrator Email
     */
    public static function admin_email() {
        $admin_email = adverts_config( "emails.admin_email" );
        
        if( empty( $admin_email ) ) {
            $admin_email = get_option( "admin_email" );
        }
        
        return $admin_email;
    }
    
    /**
     * Returns filtering options
     * 
     * Returns options for a dropdown in wp-admin / Classifieds / Options / Emails / Options list
     * 
     * @since   1.3.0
     * @return  array    Filter options
     */
    public function get_filter_options() {
        return apply_filters( "adext_emails_list_filter_options", array(
            array( "key" => "core", "label" => __( "Core", "wpadverts" ) )
        ) );
    }
    
    /**
     * Returns a formatted date
     * 
     * To format a date the default date_format option is used.
     * 
     * @since   1.3.0
     * @since   1.5.3   $format param
     * 
     * @param   mixed   $date       Date or timestamp
     * @param   string  $format     Date format. If null default WP format will be used
     * @return  string              Formatted date
     */
    public function format_date( $date, $format = null ) {
        
        if( ! is_numeric( $date ) ) {
            $date = strtotime( $date );
        }
        
        if( $format === null ) {
            $format = get_option( 'date_format' );
        }
        
        return date_i18n( $format, $date );
    }
    
    public function get_all_files( $data ) {
        $files_list = array();
        
        if( is_string( $data ) && ! empty( $data ) ) {
            $data = array( $data );
        } else if( ! is_array( $data ) ) {
            $data = array();
        }
        
        foreach( $data as $file ) {
            if( is_string( $file ) && file_exists( $file ) ) {
                $files_list[] = $file;
            }
        }
        return join( "\n", $files_list );
    }
}
