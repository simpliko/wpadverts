<?php
/**
 * Emails Module - Admin Class
 * 
 * This class is used to handle Emails Module configuration in 
 * wp-admin / Classifieds / Options / Emails panel.
 * 
 * @author Grzegorz Winiarski
 * @since 1.3.0
 * @package Adverts
 * @subpackage Emails
 */

class Adext_Emails_Admin {

    /**
     * Currently edited email headers
     *
     * @since 1.3
     * @var array
     */
    protected $_headers = null;
    
    /**
     * Function used to decide which action to execute.
     * 
     * This function is run when user will access wp-admin / Classifieds / Options / Emails panel.
     * 
     * @since 1.3
     * @return void
     */
    public function dispatch() {
        $edit = adverts_request( "edit" );
        $emaction = adverts_request( "emaction" );
        
        if( ! empty( $edit ) )  {
            $this->edit();
        } else if( $emaction == "options" ) {
            $this->options();
        } else {
            $this->browse();
        }
    }
    
    /** 
     * Renders Emails Options Page.
     * 
     * The page is rendered in wp-admin / Classifieds / Options / Emails / Options panel
     * 
     * @since 1.3
     * @return void
     */
    public function options() {
        wp_enqueue_style( 'adverts-admin' );
        $flash = Adverts_Flash::instance();
        $messages = Adext_Emails::instance()->messages;

        $scheme = $this->options_form( );
        $form = new Adverts_Form( $scheme );

        $button_text = __("Update Options", "wpadverts");

        if( isset( $_POST ) && !empty( $_POST ) ) {
            $form->bind( stripslashes_deep( $_POST ) );
            $valid = $form->validate();

            if($valid) {

                $update =  $form->get_values();
                
                if( ! isset( $update["enable_html_emails"] ) ) {
                    $update["enable_html_emails"] = 0;
                } else {
                    $update["enable_html_emails"] = 1;
                }
                
                update_option("adext_emails_config", $update);
                
                $flash->add_info( __("Settings updated.", "wpadverts") );
            } else {
                $flash->add_error( __("There are errors in your form.", "wpadverts") );
            }
        } else {
            $form->bind( adverts_config( "emails.ALL" ) );
        }

        include ADVERTS_PATH . 'addons/emails/admin/options.php';
    }
    
    /** 
     * Renders Emails List.
     * 
     * The page is rendered in wp-admin / Classifieds / Options / Emails 
     * 
     * @since 1.3
     * @return void
     */
    public function browse() {
        if(adverts_request( "edit" ) != "" ) {
            $this->emails_edit();
            return;
        }
        
        wp_enqueue_style( 'adverts-admin' );
        $flash = Adverts_Flash::instance();
        $messages = array();
        
        foreach(Adext_Emails::instance()->messages->get_messages() as $k => $m) {
            if( adverts_request( "ftype" ) && stripos( $k, adverts_request( "ftype" )."::" ) !== 0 ) {
                continue;
            }
            if( adverts_request( "fnotify" ) && $m["notify"] != adverts_request( "fnotify" ) ) {
                continue;
            }
            
            $messages[$k] = $m; 
        }

        include ADVERTS_PATH . 'addons/emails/admin/emails-list.php';
    }
    
    /** 
     * Renders Emails config form.
     * 
     * The page is rendered in wp-admin / Classifieds / Options / Emails / Edit panel
     * 
     * @since 1.3
     * @return void
     */
    public function edit() {
        
        wp_enqueue_style( 'adverts-admin' );
        wp_enqueue_style( 'adverts-emails-admin' );
        wp_enqueue_script( 'adverts-emails-admin' );
        
        $flash = Adverts_Flash::instance();
        $message = $this->get_current_message();
        
        if( $message === null ) {
            wp_die();
        }
        
        $bind = array(
            "message_enabled" => $message["enabled"],
            "message_subject" => $message["subject"],
            "message_from" => $message["from"],
            "message_to" => $message["to"],
            "message_headers" => $message["headers"],
            "message_body" => $message["body"],
            "message_attachments" => $message["attachments"]
        );
        
        $scheme = $this->edit_form( $message );
        $form = new Adverts_Form( $scheme );
        $form->bind( $bind );

        $button_text = __("Update Message Template", "wpadverts");

        if(isset($_POST) && !empty($_POST)) {
            $form->bind( stripslashes_deep( $_POST ) );
            $valid = $form->validate();

            if($valid) {
                $this->edit_save( $form, adverts_request( "edit" ) );
                Adext_Emails::instance()->messages->register_messages();
                $message = $this->get_current_message();
                $flash->add_info( __("Email template has been saved.", "wpadverts") );
            } else {
                $flash->add_error( __("There are errors in your form.", "wpadverts") );
            }
        }
        
        include ADVERTS_PATH . 'addons/emails/admin/emails-edit.php';
    }
    
    /**
     * Save function for self::edit()
     * 
     * This function is executed when trying to save an email template
     * from wp-admin / Classifieds / Options / Emails / Edit panel
     * 
     * @since   1.3.0
     * 
     * @param   array   $form           Form Scheme
     * @param   string  $email_name     Edited email name
     * @return  boolean                 True if the message was saved
     */
    public function edit_save($form, $email_name ) {
        
        $values = $form->get_values();

        $insert = false;
        $templates = get_option( "adext_emails_templates" );
        
        if( $templates === false ) {
            $insert = true;
            $templates = array();
        }
        
        $templates[ $email_name ] = array(
            "enabled" => isset( $values["message_enabled"] ) ? 1 : 0,
            "subject" => $values["message_subject"],
            "from" => $values["message_from"],
            "to" => $values["message_to"],
            "body" => $values["message_body"],
            "headers" => array(),
            "attachments" => adverts_request( "message_attachments" )
        );
        
        $x1 = 0;
        if( isset( $_POST["header_name"] ) ) {
            $x1 = count( $_POST["header_name"] );
        }
        
        $x2 = 0;
        if( isset( $_POST["header_value"] ) ) {
            $x2 = count( $_POST["header_value"] );
        }
        
        $x = max( $x1, $x2 );
        
        for( $i=0; $i<$x; $i++ ) {
            $hname = isset( $_POST["header_name"][$i] ) ? trim( $_POST["header_name"][$i] ) : "";
            $hvalue = isset( $_POST["header_value"][$i] ) ? trim( $_POST["header_value"][$i] ) : "";
            
            if( empty( $hname ) && empty( $hvalue ) ) {
                continue;
            }
            
            $templates[ $email_name ]["headers"][] = array( "name" => $hname, "value" => $hvalue );
        }
                
        if( $insert ) {
            add_option( "adext_emails_templates", $templates, '', 'no' );
        } else {
            update_option( "adext_emails_templates", $templates );
        }
        
        return true;
    }
    
    /**
     * Returns currently edited message
     * 
     * Retrives a message identified by $_GET['edit'] from Adext_Emails::instance()->messages

     * @return  array    Message
     */
    public function get_current_message() {
        $messages = Adext_Emails::instance()->messages;
        $message = null;
        $edit = adverts_request( "edit" );

        foreach( $messages->get_messages() as $m ) {
            if( $m["name"] == $edit ) {
                $message = $m;
                break;
            }
        }
        
        return $message;
    }
    
    /**
     * Renders "Add Header" button
     * 
     * The button is being used when editing an email template in
     * wp-admin / Classifieds / Options / Emails / Edit panel.
     * 
     * @since 1.3.0
     * @return string   HTML for "Add Header" button
     */
    public function edit_form_add_header( ) {
        include_once ADVERTS_PATH . "/includes/class-html.php";
        
        $icon_text = '<span class="dashicons dashicons-plus" style="vertical-align:middle"></span> ' . __( "Add Header", "wpadverts" );
        
        $a = new Adverts_Html( "a", array(
            "href" => "#",
            "class" => "button button-secondary adext-emails-add-header"
        ), $icon_text );
        
        return $a->render();
    }
    
    /**
     * Renders "Add Attachment" button
     * 
     * The button is being used when editing an email template in
     * wp-admin / Classifieds / Options / Emails / Edit panel.
     * 
     * @since 1.5.0
     * @return string   HTML for "Add Attachment" button
     */
    public function edit_form_add_attachment() {
        include_once ADVERTS_PATH . "/includes/class-html.php";
        
        $icon_text = '<span class="dashicons dashicons-plus" style="vertical-align:middle"></span> ' . __( "Add Attachment", "wpadverts" );
        
        $a = new Adverts_Html( "a", array(
            "href" => "#",
            "class" => "button button-secondary adext-emails-add-attachment"
        ), $icon_text );
        
        $wrap = new Adverts_Html( "div", array(
            "class" => "adext-emails-attachments"
        ), " " );
        
        return $wrap . " " . $a->render();  
    }
    
    /**
     * Returns a form scheme for Edit email page
     * 
     * @since   1.3.0
     * @param   array   $message    Currently edited message (identified by $_GET['edit'])
     * @return  array               Form scheme
     */
    public function edit_form( $message ) {
        
        include_once ADVERTS_PATH . "/addons/emails/includes/class-field-name-email.php";
        
        $nameemail = new Adext_Emails_Field_Name_Email();

        $code = sprintf( "<code>%s</code>", $message["name"] );
        if( isset( $message["help"] ) ) {
            $code .= sprintf( " <a href=\"%s\" style=\"text-decoration:none\"><span class=\"dashicons dashicons-welcome-learn-more\"></span></a>", $message["help"] );
        }
        
        return array(
            "name" => "adext_emails_edit",
            "field" => array(
                array(
                    "name" => "_email",
                    "type" => "adverts_field_label",
                    "label" => __( "Email Name", "wpadverts" ),
                    "content" => $code,
                    "order" => 10,
                ),
                array(
                    "name" => "message_enabled",
                    "type" => "adverts_field_checkbox",
                    "label" => __( "Is Active", "wpadverts"),
                    "options" => array(
                        array( "value" => 1, "text" => __( "Enable sending this message.", "wpadverts" ) )
                    ),
                    "order" => 20,
                ),
                array(
                    "name" => "message_subject",
                    "type" => "adverts_field_text",
                    "class" => "adext-emails-full-width",
                    "label" => __( "Subject", "wpadverts" ),
                    "order" => 30,
                ),
                array(
                    "name" => "message_from",
                    "type" => "adext_emails_field_name_email",
                    "label" => __( "From", "wpadverts" ),
                    "class" => "adext-emails-full-width",
                    "order" => 40,
                ),
                array(
                    "name" => "message_to",
                    "type" => "adverts_field_text",
                    "label" => __( "To", "wpadverts" ),
                    "order" => 50,
                ),
                array( 
                    "name" => "_add_headers",
                    "type" => "adverts_field_label",
                    "label" => "&nbsp;",
                    "content" => $this->edit_form_add_header(),
                    "order" => 60,
                ),
                array(
                    "name" => "_divider",
                    "type" => "adverts_field_header",
                    "order" => 70,
                ),
                array(
                    "name" => "message_body",
                    "type" => "adverts_field_textarea",
                    "mode" => "plain-text",
                    "label" => __( "Body", "wpadverts" ),
                    "order" => 80,
                ),
                array(
                    "name" => "message_attachments",
                    "type" => "adverts_field_label",
                    "label" => __( "Attachments", "wpadverts" ),
                    "content" => $this->edit_form_add_attachment(),
                    "order" => 100,
                ),
            )
        );
    }
    
    /**
     * Returns form scheme for Options page
     * 
     * @since   1.3.0
     * @return  array    Form scheme
     */
    public function options_form() {
        return array(
            "name" => "adext_emails_options",
            "field" => array(
                array(
                    "name" => "admin_email",
                    "type" => "adverts_field_text",
                    "label" => __( "Send Admin Notifications To", "wpadverts" ),
                    "placeholder" => get_option( "admin_email" ),
                    "order" => 10
                ),
                array(
                    "name" => "enable_html_emails",
                    "type" => "adverts_field_checkbox",
                    "label" => "HTML Emails",
                    "options" => array(
                        array( "value" => "1", "text" => __( "Send emails as HTML", "wpadverts" ) )
                    ),
                    "order" => 20
                )
            )
        );
    }
    
    
}
