<?php

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
        $config = adverts_request( "config" );
        
        if( ! empty( $edit ) )  {
            $this->edit();
        } else if( $config == "1" ) {
            $this->config();
        } else {
            $this->list();
        }
    }
    
    /** 
     * Renders Emails List.
     * 
     * The page is rendered in wp-admin / Classifieds / Options / Emails 
     * 
     * @since 1.3
     * @return void
     */
    public function list() {
        if(adverts_request( "edit" ) != "" ) {
            $this->emails_edit();
            return;
        }
        
        wp_enqueue_style( 'adverts-admin' );
        $flash = Adverts_Flash::instance();
        $messages = Adverts::instance()->get("emails")->messages;

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
            "message_body" => $message["body"]
        );
        
        $scheme = $this->edit_form( $message );
        $form = new Adverts_Form( $scheme );
        $form->bind( $bind );

        $button_text = __("Update Options", "adverts");

        if(isset($_POST) && !empty($_POST)) {
            $form->bind( stripslashes_deep( $_POST ) );
            $valid = $form->validate();

            if($valid) {
                $this->edit_save( $form, adverts_request( "edit" ) );
                Adverts::instance()->get("emails")->messages->register_messages();
                $message = $this->get_current_message();
                $flash->add_info( __("Email template has been saved.", "adverts") );
            } else {
                $flash->add_error( __("There are errors in your form.", "adverts") );
            }
        }
        
        include ADVERTS_PATH . 'addons/emails/admin/emails-edit.php';
    }
    
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
            "headers" => array()
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
    
    public function get_current_message() {
        $messages = Adverts::instance()->get("emails")->messages;
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
    
    public function edit_form_add_header( ) {
        return '<a href="#" class="button button-secondary adext-emails-add-header"><span class="dashicons dashicons-plus" style="vertical-align:middle"></span> Add Header</a>';
    }
    
    public function edit_form( $message ) {
        
        include_once ADVERTS_PATH . "/addons/emails/includes/class-field-name-email.php";
        
        $nameemail = new Adext_Emails_Field_Name_Email();

        return array(
            "name" => "adext_email_edit",
            "field" => array(
                array(
                    "name" => "_email",
                    "type" => "adverts_field_label",
                    "label" => __( "Email Name", "adverts" ),
                    "content" => sprintf( "<strong>%s</strong> 	— <code>%s</code>", $message["label"], $message["name"] )
                ),
                array(
                    "name" => "message_enabled",
                    "type" => "adverts_field_checkbox",
                    "label" => __( "Is Active", "adverts"),
                    "options" => array(
                        array( "value" => 1, "text" => __( "Enable sending this message.", "adverts" ) )
                    )
                ),
                array(
                    "name" => "message_subject",
                    "type" => "adverts_field_text",
                    "class" => "adext-emails-full-width",
                    "label" => __( "Subject", "adverts" ),
                ),
                array(
                    "name" => "message_from",
                    "type" => "adext_emails_field_name_email",
                    "label" => __( "From", "adverts" ),
                    "class" => "adext-emails-full-width",
                ),
                array(
                    "name" => "message_to",
                    "type" => "adext_emails_field_name_email",
                    "label" => __( "To", "adverts" ),
                    
                ),
                array( 
                    "name" => "_add_headers",
                    "type" => "adverts_field_label",
                    "label" => "&nbsp;",
                    "content" => $this->edit_form_add_header()
                ),
                array(
                    "name" => "_divider",
                    "type" => "adverts_field_header",
                ),
                array(
                    "name" => "message_body",
                    "type" => "adverts_field_textarea",
                    "mode" => "tinymce-full",
                    "label" => __( "Body", "adverts" )
                )
            )
        );
    }
    
    
}
