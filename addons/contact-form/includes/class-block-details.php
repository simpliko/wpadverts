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

    public function __construct( $atts, $post_id ) {
        $this->_atts = $atts;
        $this->_post_id = $post_id;
    }

    public function get_contact_options() {

        $co["contact-form"] = $this->get_contact_form_button();
        
        $pb = $this->get_phone_button();

        if( is_array( $pb ) ) {
            $co["phone-button"] = $pb;
        }
    
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
            )

        );

        add_action( "wpadverts/block/details/tpl/contact-content", array( $this, "contact_options_form" ), 10 );

        return $cf;
    }

    public function get_phone_button() {

        $phone = get_post_meta( $this->_post_id, "adverts_phone", true );
    
        if( empty( $phone ) ) {
            return null;
        }

        $phone = trim( $phone );

        if( $phone ) {
            $ph1 = str_replace( " ", "", substr( $phone, 0, 3 ) );
            $ph2 = str_replace( " ", "", substr( $phone, 3 ) );
        }

        if( $this->_atts["phone_reveal"] == 1) {
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
                )
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
                )
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

    public function contact_options_form() {

        $post_id = $this->_post_id;
        $form_id = "wpadverts-contact-form";
        $atts = $this->_atts;
        
        $data = _adext_contact_form_content( $post_id );

        $form = $data["form"];
        $flash = $data["adverts_flash"];
        $show_form = $data["show_form"];
        $form_sent = $data["form_sent"];

        $show_buttons = true;

        $loader = '<i class="fas fa-spinner atw-animate-spin atw-duration-1000 atw-text-xl"></i>';
        $loader = $this->_svg_loader();
        $html = '<span class="atw-inline-flex atw-items-center"><span class="">Send Message</span>'.$loader.'</span>';

        $buttons = array(
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

        add_filter( "wpadverts/block/form/styles/atts", array( $this, "no_interline" ) );

        ?>

        <?php if( adext_contact_form_get_to( $post_id ) ): ?>
        <div id="wpadverts-block-contact-box" class="wpadverts-block-contact-box wpadverts-block-contact-box-toggle atw-bg-gray-50 atw-px-6" <?php if(!$show_form): ?>style="display: none"<?php endif ?>>
                            
            <div class="atw-my-3 atw-mx-0 atw-pt-6 atw-pb-0 atw-font-bold atw-text-2xl"><?php _e( "Contact Seller", "wpadverts" ) ?></div>

            <?php if( $form_sent ): ?>
                <?php echo wpadverts_block_flash( $flash, "big" ) ?>
            <?php else: ?>
                <?php echo wpadverts_block_flash( $flash ) ?>
                <?php $buttons_position = "atw-flex-col"; ?>
                <?php include ADVERTS_PATH . '/templates/block-partials/form.php' ?>
            <?php endif; ?>

        </div>
        <?php endif; ?>
    


        <?php

        remove_filter( "wpadverts/block/form/styles/atts", array( $this, "no_interline" ) );
    }

    public function no_interline( $atts ) {
        if( isset( $atts["interline"] ) ) {
            unset( $atts["interline"] );
        }
        if( isset( $atts["style"] ) && $atts["style"] == "wpa-solid" ) {
            $atts["style"] = "wpa-flat";
        }
        return $atts;
    }
}