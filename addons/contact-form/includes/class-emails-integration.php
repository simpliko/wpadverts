<?php
/**
 * Integration with Emails Module
 * 
 * This class registers emails that can be sent by WPAdverts Emails module 
 * and edited from the wp-admin / Classifieids / Options / Emails panel
 * 
 * @author Grzegorz Winiarski
 * @since 1.3.0
 * @package Adverts
 * @subpackage ContactForm
 * 
 */
class Adext_Contact_Form_Emails_Integration {
    
    /**
     * Class constructor
     * 
     * Registers a wpadverts_messages_register filter which registers
     * a new messages in the Email Module.
     * 
     * @since  1.3.0
     * @return void
     */
    public function __construct() {
        add_filter( "wpadverts_messages_register", array( $this, "register_messages" ) );
        add_filter( "adext_emails_list_filter_options", array( $this, "register_filter_optons" ) );
    }
    
    /**
     * Register option for filter dropdown
     * 
     * This function registers an option for dropdown in wp-admin / Classifieds / Options / Emails list
     * 
     * The function is executed by adext_emails_list_filter_options filter registered in self::__construct().
     * 
     * @see     adext_emails_list_filter_options filter
     * 
     * @since   1.3.0
     * @param   array   $options    List of filter options
     * @return  array               Updated list of options
     */
    public function register_filter_optons( $options ) {
        $options[] = array( "key" => "contact-form", "label" => __( "Contact Form", "wpadverts" ) );
        return $options;
    }
    
    /**
     * Registers new messages in Emails Module
     * 
     * This function is called by wpadverts_messages_register filter registered
     * in self::__construct()
     * 
     * @since   1.3.0
     * @param   array $messages     List of registered messages
     * @return  array               Modified list of messages
     */
    public function register_messages( $messages ) {
        $messages["contact-form::on_contact_form_sent"] = array(
            "name" => "contact-form::on_contact_form_sent",
            "action" => "adext_contact_form_send",
            "callback" => array( "function" => array( $this, "on_contact_form_sent" ), "priority" => 10, "args" => 2 ),
            "help" => "https://wpadverts.com/documentation/contact-form/#contact-form-on_contact_form_sent",
            "enabled" => 1,
            "label" => "",
            "notify" => "user",
            "from" => array( "name" => adverts_config( "contact_form.from_name"), "email" => adverts_config( "contact_form.from_email") ),
            "to" => "{\$advert|contact_email}",
            "subject" => __( "[{\$advert.post_title}] {\$form.message_subject}.", "wpadverts" ),
            "body" => __("{\$form.message_body}\n\nURL: {\$advert.ID|get_permalink}\nPrice: {\$advert.ID|adverts_get_the_price}", "wpadverts"),
            "headers" => array(
                array( 'name' => 'Reply-To', 'value' => "{\$form.message_name} <{\$form.message_email}>")
            ),
            "attachments" => array()
        );
            
        return $messages;
    }
    
    /**
     * Sends a contact form email
     * 
     * This function is responsible for sending an email from an Advert details
     * page when user clicks a "Send Message" button in the contact form.
     * 
     * @param   int             $post_id    ID of an Advert
     * @param   Adverts_Form    $form       Contact Form
     * @return  void
     */
    public function on_contact_form_sent( $post_id, $form ) {
        return Adext_Emails::instance()->messages->send_message( "contact-form::on_contact_form_sent", array( 
            "advert" => get_post( $post_id ),
            "advert_files" => adverts_get_post_files( $post_id ),
            "form" => $form->get_values(),
            "form_files" => $form->get_files( adverts_request( "wpadverts-form-upload-uniqid" ) )
        ) );
    }
    

}