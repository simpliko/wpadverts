<?php

class Adext_Emails_Admin {

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
     * Renders Emails config form.
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
        $messages = Adverts::instance()->get_messages();

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
        
        $scheme = $this->edit_form();
        $form = new Adverts_Form( $scheme );
        $form->bind( get_option ( "adext_payments_config", array() ) );

        $button_text = __("Update Options", "adverts");

        if(isset($_POST) && !empty($_POST)) {
            $form->bind( stripslashes_deep( $_POST ) );
            $valid = $form->validate();

            if($valid) {
                update_option("adext_payments_config", $form->get_values());
                $flash->add_info( __("Settings updated.", "adverts") );
            } else {
                $flash->add_error( __("There are errors in your form.", "adverts") );
            }
        }
        
        include ADVERTS_PATH . 'addons/emails/admin/emails-edit.php';
    }
    
    public function edit_form_add_header( $field ) {
        echo '<a href="#" class="button button-secondary adext-emails-add-header"><span class="dashicons dashicons-plus" style="vertical-align:middle"></span> Add Header</a>';
    }
    
    public function edit_form_name_email( $field ) {
        $field1 = $field;
        $field2 = $field;
        
        $field1["placeholder"] = __( "Full Name", "adverts" );
        $field2["placeholder"] = __( "Email Address (e.g. user@example.com)", "adverts" );
        
        echo '<div class="adext-emails-field-name-email">';
        adverts_field_text($field1);
        adverts_field_text($field2);
        echo '</div>';
    }
    
    public function edit_form() {
        
        adverts_form_add_field("adext_emails_field_name_email", array(
            "renderer" => array( $this, "edit_form_name_email"),
            "callback_save" => "adverts_save_multi",
            "callback_bind" => "adverts_bind_multi",
        ));
        
        adverts_form_add_field("adext_emails_field_add_header", array(
            "renderer" => array( $this, "edit_form_add_header"),
            "callback_save" => "adverts_save_multi",
            "callback_bind" => "adverts_bind_multi",
        ));
        
        return array(
            "name" => "adext_email_edit",
            "field" => array(
                array(
                    "name" => "is_active",
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
                    "name" => "from",
                    "type" => "adext_emails_field_name_email",
                    "label" => __( "From", "adverts" ),
                    "class" => "adext-emails-full-width",
                ),
                array(
                    "name" => "to",
                    "type" => "adext_emails_field_name_email",
                    "label" => __( "To", "adverts" ),
                    
                ),
                array( 
                    "name" => "_headers",
                    "type" => "adext_emails_field_add_header",
                    "label" => "&nbsp;"
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
