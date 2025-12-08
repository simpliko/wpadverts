<?php
/**
 * Integration with Block Details
 * 
 * This class integrates the contact form options with the 
 * Classifieds Details block
 * 
 * @author Grzegorz Winiarski
 * @since 2.0
 * @package Adverts
 * @subpackage ContactForm
 * 
 */
class Adext_Contact_Form_Block_Details {

    protected $_atts = array();

    protected $_post_id = null;

    protected $_form = null;

    protected $_flash = null;

    public function __construct( $atts, $post_id ) {
        $this->_atts = $atts;
        $this->_post_id = $post_id;
    }

    public function get_contact_options() {

        $co["contact-form"] = $this->get_contact_form_button();
        $co["phone-button"] = $this->get_phone_button();

        return $co;
    }

    public function get_contact_form_button() {

        $cf = array( 
            "text" => __("Send Message", "wpadverts"), 
            "icon" => "fas fa-envelope", 
            "class" => "wpadverts-show-contact-form",
            "type" => "primary",
            "order" => 0,
            "options" => array(
                "mobile" => "text-and-icon",
                "desktop" => "text-and-icon"
            ),
            "label" => __( "Contact Form", "wpadverts" ),
            "is_active" => true,
            "is_visible" => true,
            "content_callback" => array(
                "callback" => array( $this, "contact_options_form" ),
                "priority" => 10
            )

        );

        //add_action( "wpadverts/block/details/tpl/contact-content", array( $this, "contact_options_form" ), 10 );

        return $cf;
    }

    public function get_phone_button() {

        $phone = get_post_meta( $this->_post_id, "adverts_phone", true );
        $ph1 = "";
        $ph2 = "";

        if( empty( $phone ) ) {
            $is_active = false;
        } else {
            $is_active = adverts_config( "contact_form.show_phone");
            $phone = trim( $phone );
            if( $phone ) {
                $ph1 = str_replace( " ", "", substr( $phone, 0, 3 ) );
                $ph2 = str_replace( " ", "", substr( $phone, 3 ) );
            }
        }

        if( isset( $this->_atts["phone_reveal"] ) ) {
            $phone_reveal = $this->_atts["phone_reveal"];
        } else {
            $phone_reveal = adverts_config( "contact_form.reveal_on_click" );
        }

        if( $phone_reveal == 1) {
            $pb = array( 
                "html" => sprintf( __('Call <span data-ph1="%s" class="wpadverts-phone">%s...</span> <a href="#" class="wpadverts-phone-reveal">show phone</a>', "wpadverts"), $ph1, $ph1 ), 
                "icon" => "fas fa-phone-alt", 
                "class" => "wpadverts-reveal-phone",
                "type" => "secondary",
                "order" => 1,
                "attr" => array(
                    "data-ph1" => $ph1,
                    "data-ph2" => $ph2
                ),            
                "options" => array(
                    "mobile" => "text-and-icon",
                    "desktop" => "text-and-icon"
                ),
                "label" => __( "Phone", "wpadverts" ),
                "is_active" => $is_active,
                "is_visible" => $is_active,
                "content_callback" => null

            );
        } else {
            $pb = array( 
                "html" => sprintf( __('Call <span data-ph1="%s" class="wpadverts-phone">%s</span>', "wpadverts"), $ph1, $phone ), 
                "icon" => "fas fa-phone-alt", 
                "class" => "wpadverts-reveal-phone",
                "type" => "secondary",
                "order" => 1,
                "attr" => array(
                    "data-ph1" => $ph1,
                    "data-ph2" => $ph2
                ),
                "options" => array(
                    "mobile" => "text-and-icon",
                    "desktop" => "text-and-icon"
                ),
                "label" => __("Phone", "wpadverts"),
                "is_active" => $is_active,
                "is_visible" => $is_active,
                "content_callback" => null
            );
        }



        return $pb;
    }

    protected function _svg_loader() {
        return '
            <svg class="wpa-utility-spinner atw-hidden atw-animate-spin atw-transition-transform atw-h-5 atw-w-5 atw-ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="atw-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="atw-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        ';
    }

    public function contact_options_form( $post_id = 0, $atts = array() ) {

        $post_id = $this->_post_id;
        $atts = $this->_atts;
        $atts_style = [];

        /*
        if( $atts["form_style"] ) {

            $atts_style = [
                "customize" => 1,
            ];

            //add_filter("wpadverts/block/form/styles/atts", [$this, "change_form_style"], 50);
        }
        */

        $data = $this->_adext_contact_form_content( $post_id, $atts );

        $form = $data["form"];
        $flash = $data["adverts_flash"];
        $show_form = $data["show_form"];
        $form_sent = $data["form_sent"];

        if( isset( $flash["info"][0] ) ) {
            $flash["success"] = $flash["info"];
            $flash["success"][0]["icon"] = "fas fa-check";
            $flash["info"] = array();
        }

        $show_buttons = true;
        $loader = $this->_svg_loader();

        add_filter( "wpadverts/block/form/styles/atts", array( $this, "no_interline" ) );

        if( $atts["form_expand"] ) {
            $show_form = true;
        }

        $data = [
            "buttons" => $this->_buttons( $atts ),
            "buttons_position" => "atw-flex-col",
            "redirect_to" => "",
            "atts" => $atts_style,
            "form" => $form, // Adverts_Form (required)
            "form_id" => "wpadverts-contact-form",
            "form_style_atts" => $atts["form_style"],
            "form_layout" => "wpa-layout-stacked",
            "form_layout_prop" => "atw-w-1/3",
        ];

        $style = "";
        if(!$show_form) {
            $style .= "display:none;";
        }
        if($atts["form_bg"]) {
            $style .= sprintf( "background-color:%s", $atts["form_bg"] );
        }

        if($atts["contacts_stacked"]) {
            $data["_btn_classes"] = ["top" => "", "inner" => ""];
        }

        ?>

        <?php if( adext_contact_form_get_to( $post_id ) ): ?>
        <div id="wpadverts-block-contact-box" class="wpadverts-block-contact-box wpadverts-block-contact-box-toggle <?php if(!$atts["form_bg"]): ?>atw-bg-gray-50<?php endif; ?> <?php echo $this->_getPxMark($atts["form_px"]) ?> <?php echo $this->_getPyMark($atts["form_py"]) ?>" <?php echo $style ? sprintf('style="%s"', $style) : ""  ?>>
                            
            <?php if( $atts["form_title"] ): ?>
            <div class="atw-my-3 atw-mx-0 atw-pt-2 atw-pb-0 atw-font-bold atw-text-2xl"><?php echo esc_html( $atts["form_title"] ) ?></div>
            <?php endif; ?>

            <?php if( $form_sent ): ?>
                <?php echo wpadverts_block_flash( $flash, "big" ) ?>
            <?php else: ?>
                <?php echo wpadverts_block_flash( $flash ) ?>
                <?php echo wpadverts_block_form_partial( $data ) ?>
                <?php //$buttons_position = "atw-flex-col"; ?>
                <?php //include ADVERTS_PATH . '/templates/block-partials/form.php' ?>
            <?php endif; ?>

        </div>
        <?php endif; ?>
        <?php

        remove_filter( "wpadverts/block/form/styles/atts", array( $this, "no_interline" ) );
        remove_filter( "wpadverts/block/form/styles/atts", [$this, "change_form_style"], 50);
    }

    protected function _buttons( $atts ) {
        return array(
            array(
                "text" => __( "Send Message", "wpadverts" ),
                "html" => null,
                "icon" => "fas-mail-alt",
                "type" => "primary",
                "class" => "wpadverts-block-cf-send",
                "action" => "submit",
                "name" => "adverts_contact_form",
                "value" => "1"
            )
        );
    }

    protected function _adext_contact_form_content( $post_id, $atts = array() ) {
    

        include_once ADVERTS_PATH . 'includes/class-form.php';
        include_once ADVERTS_PATH . 'includes/class-html.php';
        
        $show_form = false;
        $form_sent = false;
        $flash = array( "error" => array(), "info" => array());;

        $form_scheme_params = array();
        if( isset( $atts["form_scheme"] ) && $atts["form_scheme"] ) {
            $form_scheme_params["form_scheme"] = $atts["form_scheme"];
        }

        $form_scheme = apply_filters( 
            "adverts_form_scheme", 
            Adverts::instance()->get( "form_contact_form" ), 
            $form_scheme_params
        );

        if( $atts["form_condensed"] ) {
            add_filter( "adverts_form_load", [$this, "_condense_form" ], 2000 );
        }
        // adverts_form_load filter will add checksum fields
        $form = new Adverts_Form( $form_scheme );
        
        if( $atts["form_condensed"] ) {
            remove_filter( "adverts_form_load", [$this, "_condense_form" ], 2000 );
        }

        wp_enqueue_script( 'adverts-form' );
        
        if( adverts_request( "adverts_contact_form" ) ) {
            
            wp_enqueue_script( 'adverts-contact-form-scroll' );
            
            $form->bind( stripslashes_deep( $_POST ) );
            $valid = $form->validate();
    
            if( $valid ) {
                
                //Adext_Contact_Form::instance()->send_message( get_post( $post_id ), $form );
                do_action( "adext_contact_form_send", $post_id, $form );
                
                // delete uploaded files ($form)
                $request_uniqid = adverts_request( "wpadverts-form-upload-uniqid" );
                if(is_string( $request_uniqid ) ) {
                    $uniqid = sanitize_file_name( $request_uniqid );
                    adext_contact_form_delete_tmp_files( $form->get_scheme(), $uniqid );
                }
                
                $bind = array();
                $bind["_wpadverts_checksum"] = adverts_request( "_wpadverts_checksum" );
                $bind["_wpadverts_checksum_nonce"] = adverts_request( "_wpadverts_checksum_nonce" );
                
                $form->bind( $bind );
                
                $flash["info"][] = array(
                    "message" => __( "Your message has been sent.", "wpadverts" ),
                    "icon" => "adverts-icon-ok"
                );
                $show_form = true; 
                $form_sent = true;
            } else {
                $flash["error"][] = array(
                    "message" => __( "There are errors in your form.", "wpadverts" ),
                    "icon" => "adverts-icon-attention-alt"
                );
                $show_form = true; 
            }
        } else {
            
            $bind = array();
            
            if( get_current_user_id() > 0 ) {
                $user = wp_get_current_user();
                /* @var $user WP_User */
                
                $bind = array(
                    "message_name" => $user->display_name,
                    "message_email" => $user->user_email
                );
            }
            
            include_once ADVERTS_PATH . '/includes/class-checksum.php';
            
            $checksum = new Adverts_Checksum();
    
            $keys = $checksum->get_integrity_keys( array(
                "ignore-post-id" => true,
                "form_name" => "contact",
                "scheme_name" => "form_contact_form",
                "form_scheme_id" => ""
            ) );
    
            $bind["_wpadverts_checksum"] = $keys["checksum"];
            $bind["_wpadverts_checksum_nonce"] = $keys["nonce"];
    
            $form->bind( $bind );
            
        }
        
        return array(
            "form" => $form,
            "form_sent" => $form_sent,
            "adverts_flash" => $flash,
            "show_form" => $show_form
        );
    }

    public function _condense_form( $form ) {
        $condense = ["adverts_field_text", "adverts_field_textarea"];

        foreach( $form["field"] as $key => $field ) {
            if( ! in_array( $field["type"], $condense ) ) {
                continue;
            }
            if( ! isset( $form["field"][$key]["atts"] ) ) {
                $form["field"][$key]["attr"] = [];
            }
            $form["field"][$key]["attr"]["placeholder"] = $form["field"][$key]["label"];
            $form["field"][$key]["label"] = "";

            if( $field["type"] == "adverts_field_textarea" ) {
                $form["field"][$key]["attr"]["rows"] = 5;
            } 
        }
        return $form;
    }

    protected function _getPxMark($form_px) {
        $p = [
            "atw-px-0",
            "atw-px-0.5",
            "atw-px-1",
            "atw-px-1.5",
            "atw-px-2",
            "atw-px-2.5",
            "atw-px-3",
            "atw-px-3.5",
            "atw-px-4",
            "atw-px-4.5",
            "atw-px-5",
            "atw-px-5.5",
            "atw-px-6",
            "atw-px-6.5",
            "atw-px-7",
            "atw-px-7.5",
            "atw-px-8",
            "atw-px-8.5",
            "atw-px-9",
            "atw-px-9.5",
            "atw-px-10",
            "atw-px-10.5"
        ];

        return $p[$form_px];
    }

    protected function _getPyMark($form_py) {
        $p = [
            "atw-py-0",
            "atw-py-0.5",
            "atw-py-1",
            "atw-py-1.5",
            "atw-py-2",
            "atw-py-2.5",
            "atw-py-3",
            "atw-py-3.5",
            "atw-py-4",
            "atw-py-4.5",
            "atw-py-5",
            "atw-py-5.5",
            "atw-py-6",
            "atw-py-6.5",
            "atw-py-7",
            "atw-py-7.5",
            "atw-py-8",
            "atw-py-8.5",
            "atw-py-9",
            "atw-py-9.5",
            "atw-py-10",
            "atw-py-10.5"
        ];

        return $p[$form_py];
    }

    public function no_interline( $atts ) {
        if( isset( $atts["interline"] ) ) {
            unset( $atts["interline"] );
        }
        if( isset( $atts["customize"] ) && $atts["customize"] == 1) {
            return $atts;
        }
        if( isset( $atts["style"] ) && $atts["style"] == "wpa-solid" ) {
            $atts["style"] = "wpa-flat";
        }
        return $atts;
    }

}