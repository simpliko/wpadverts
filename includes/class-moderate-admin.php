<?php

class Adverts_Moderate_Admin {

    public function render() {
        
        $button_text = __( "Update", "wpadverts" );

        wp_enqueue_style( 'adverts-admin' );
        
        $flash = Adverts_Flash::instance();
        $error = array();

        $scheme = $this->get_form_scheme();
        $form = new Adverts_Form( $scheme );

        if( isset( $_POST ) && !empty( $_POST ) ) {

            if( adverts_request( "honeypot_enabled" ) === null ) {
                $honeypot_enabled = "0";
            } else {
                $honeypot_enabled = "1";
            }
            
            if( adverts_request( "timetrap_enabled" ) === null ) {
                $timetrap_enabled = "0";
            } else {
                $timetrap_enabled = "1";
            }
            
            $bind = array(
                "max_links" => adverts_request( "max_links" ),
                "phrases_moderate" => adverts_request( "phrases_moderate" ),
                "phrases_trash" => adverts_request( "phrases_trash" ),

                "honeypot_enabled" => $honeypot_enabled,
                "honeypot_title" => adverts_request( "honeypot_title" ),
                "honeypot_name" => adverts_request( "honeypot_name" ),

                "timetrap_enabled" => $timetrap_enabled,
                "timetrap_delta" => adverts_request( "timetrap_delta" ),
                "timetrap_salt" => adverts_request( "timetrap_salt" )
            );

            $form->bind( stripslashes_deep( $bind ) );
            $valid = $form->validate();

            if($valid) {

                update_option("adverts_moderate", $bind );
                $flash->add_info( __("Settings updated.", "wpadverts") );
            } else {
                $flash->add_error( __("There are errors in your form.", "wpadverts") );
            }
        } else {
            $bind = array(
                "max_links" => adverts_config( "moderate.max_links" ),
                "phrases_moderate" => adverts_config( "moderate.phrases_moderate" ),
                "phrases_trash" => adverts_config( "moderate.phrases_trash" ),

                "honeypot_enabled" => adverts_config( "moderate.honeypot_enabled" ),
                "honeypot_title" => adverts_config( "moderate.honeypot_title" ),
                "honeypot_name" => adverts_config( "moderate.honeypot_name" ),

                "timetrap_enabled" => adverts_config( "moderate.timetrap_enabled" ),
                "timetrap_delta" => adverts_config( "moderate.timetrap_delta" ),
                "timetrap_salt" => adverts_config( "moderate.timetrap_salt" )
            );

            $form->bind( $bind );
        }
        
        include ADVERTS_PATH . '/addons/core/admin/moderate.php';
    }
    
    public function get_form_scheme() {
        
        $form_scheme = array(
            "name" => "admin-moderate",
            "field" => array(
                array(
                    "type" => "adverts_field_header",
                    "name" => "_blacklist",
                    "title" => __( "Content Blacklist", "wpadverts" ),
                    "order" => 1,
                ),
                array(
                    "type" => "adverts_field_text",
                    "subtype" => "number",
                    "name" => "max_links",
                    "label" => __( "Max. Links In The Content", "wpadverts" ),
                    "order" => 2,
                    "attr" => array(
                        "min" => "0",
                        "max" => "20"
                    ),
                    "hint" => __( "Adding more than 'Max. Links' in the content will disallow posting an Ad. Empty value = unlimited number of links", "wpadverts" )
                ),
                array(
                    "type" => "adverts_field_textarea",
                    "name" => "phrases_trash",
                    "mode" => "plain-text",
                    "label" => __( "Blacklisted Phrases", "wpadverts" ),
                    "hint" => join( "<br/><br/>", array(
                        __( 'When a classified contains any of these words in its content, it will return an error message. One word or phrase per line. It will match inside words, so “press” will match “WordPress”.', "wpadverts" ),
                        __( 'HINT: Jeff Starr from PerishablePress.com compiled a great <a href="https://perishablepress.com/wp/wp-content/online/code/wordpress-ultimate-comment-blacklist.txt">list of common spam phrases</a>.', 'wpadverts' )
                        )),
                    "order" => 4,
                    "class" => "large-text code"
                ),
                
                array(
                    "type" => "adverts_field_header",
                    "name" => "_honepot",
                    "title" => __( "Honeypot", "wpadverts" ),
                    "order" => 10,
                ),
                array(
                    "type" => "adverts_field_checkbox",
                    "name" => "honeypot_enabled",
                    "label" => __( "Is Enabled", "wpadverts" ),
                    "order" => 11,
                    "options" => array(
                        array( "value" => "1", "text" => __( "Enable Honeypot", "wpadverts" ) )
                    )
                ),
                array(
                    "type" => "adverts_field_text",
                    "name" => "honeypot_title",
                    "label" => __( "Honeypot Title", "wpadverts" ),
                    "placeholder" => "Website Address",
                    "order" => 12,
                ),
                array(
                    "type" => "adverts_field_text",
                    "name" => "honeypot_name",
                    "label" => __( "Honeypot Name", "wpadverts" ),
                    "placeholder" => "website_address",
                    "order" => 12,
                ),
                
                array(
                    "type" => "adverts_field_header",
                    "name" => "_timetrap",
                    "title" => __( "Time Trap", "wpadverts" ),
                    "order" => 20,
                ),
                array(
                    "type" => "adverts_field_checkbox",
                    "name" => "timetrap_enabled",
                    "label" => __( "Is Enabled", "wpadverts" ),
                    "order" => 21,
                    "options" => array(
                        array( "value" => "1", "text" => __( "Enable Timetrap", "wpadverts" ) )
                    )
                ),
                array(
                    "type" => "adverts_field_text",
                    "subtype" => "number",
                    "name" => "timetrap_delta",
                    "label" => __( "Timetrap Delta (in seconds)", "wpadverts" ),
                    "placeholder" => "5",
                    "order" => 22,
                    "attr" => array(
                        "min" => "2",
                        "max" => "300"
                        
                    )
                ),
                array(
                    "type" => "adverts_field_text",
                    "name" => "timetrap_salt",
                    "label" => __( "Timetrap Key", "wpadverts" ),
                    "hint" => __( "Random password that will encrypt the Timetrap, you can generate one <a href='https://www.random.org/passwords/'>here</a>. If left empty then one of security keys in your wp-config.php file will be used."),
                    "order" => 23,
                ),
                
            )
        );
        
        return $form_scheme;
    }
    
}
