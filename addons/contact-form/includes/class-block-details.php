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

        add_action( "wpadverts/block/details/tpl/contact", array( $this, "contact_options_form" ), 10 );

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
        $atts = $this->_atts;
        //echo "<pre>";print_r($atts);echo "</pre>"; 
        $data = _adext_contact_form_content( $post_id );

        $form = $data["form"];
        $flash = $data["adverts_flash"];
        $show_form = $data["show_form"];
    
        $show_buttons = true;

        $loader = '<i class="fas fa-spinner atw-animate-spin atw-duration-1000 atw-text-xl"></i>';
        $loader = $this->_svg_loader();
        $html = '<span class="atw-inline-flex atw-items-center"><span class="">Send Message</span>'.$loader.'</span>';

        $buttons = array(
            array(
                "text" => __( "Send Message", "wpadverts" ),
                "html" => $html,
                "icon" => "fas-mail-alt",
                "type" => "primary",
                "class" => "wpadverts-block-cf-send"

            )
        )
    
        ?>
    
        <style type="text/css">
        .wpa-utility-sticky {
            /*display: none;*/
            z-index: 100000;
        }


        </style>

        <?php if( adext_contact_form_get_to( $post_id ) ): ?>
        <div class="wpa-utility-sticky-wrap" style="display:none">
            <div class="wpa-utility-sticky-bg atw-fixed atw-inset-0 atw-bg-gray-500 atw-bg-opacity-75 atw-transition-opacity atw-ease-in-out atw-duration-500 atw-opacity-0 atw-hidden" aria-hidden="true"></div>
            <div class="wpa-utility-sticky wpa-slide-rtl md:atw-max-w-xl atw-fixed atw-transform atw-translate-x-full atw-ease-in atw-top-0 atw-bottom-0 atw-right-0 atw-px-6 atw-bg-white atw-overflow-y-scroll atw-duration-300 atw-transition-all md:atw-shadow-lg">
                <div class="atw-block">
                    <div class="atw-flex atw-items-center atw-bg-gray-50 atw-py-6 atw-px-6 atw--mx-6 atw-mb-6 atw-border-b atw-border-solid atw-border-gray-100">
 
                            <i class="wpa-sticky-close fas fa-arrow-left atw-text-3xl atw-cursor-pointer"></i>
                            <span class="atw-text-xl atw-font-bold atw-pl-4 atw-inline-block"><?php _e( "Contact Seller", "wpadverts" ) ?></span>
                    </div>
                </div>
            
                <div class="wpadverts-block-contact-box wpadverts-block-contact-box-toggle" <?php if($show_form): ?>style="display: block"<?php endif ?>>
                    <?php adverts_flash( $flash ) ?>
                    <?php $buttons_position = "atw-flex-col"; ?>
                    <?php include ADVERTS_PATH . '/templates/block-partials/form.php' ?>
                </div>
            
            </div>
        </div>
        <?php endif; ?>
    
        <?php //add_action( "wp_footer", array( $this, "_get_modal" ) ) ?>

        <?php
    }

    public function _get_modal() {
        ?>
        <div class="atw-fixed atw-z-10 atw-inset-0 atw-overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="atw-flex atw-items-end atw-justify-center atw-box-border atw-min-h-screen atw-pt-4 atw-px-4 atw-pb-20 atw-text-center sm:atw-block sm:atw-p-0">
            <!--
            https://tailwindui.com/components/application-ui/overlays/modals

            Background overlay, show/hide based on modal state.

            Entering: "ease-out duration-300"
                From: "opacity-0"
                To: "opacity-100"
            Leaving: "ease-in duration-200"
                From: "opacity-100"
                To: "opacity-0"
            -->
            <div class="atw-fixed atw-inset-0 atw-bg-gray-500 atw-bg-opacity-75 atw-transition-opacity" aria-hidden="true"></div>

            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="atw-hidden sm:atw-inline-block sm:atw-align-middle sm:atw-h-screen" aria-hidden="true">&#8203;</span>

            <!--
            Modal panel, show/hide based on modal state.

            Entering: "ease-out duration-300"
                From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                To: "opacity-100 translate-y-0 sm:scale-100"
            Leaving: "ease-in duration-200"
                From: "opacity-100 translate-y-0 sm:scale-100"
                To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            -->
            <div class="atw-inline-block atw-align-bottom atw-bg-white atw-rounded-lg atw-text-left atw-overflow-hidden atw-shadow-xl atw-transform atw-transition-all sm:atw-my-8 sm:atw-align-middle sm:atw-max-w-lg sm:atw-w-full">
            <div class="atw-bg-white atw-px-4 atw-pt-5 atw-pb-4 sm:atw-p-6 sm:atw-pb-4">
                <div class="sm:atw-flex sm:atw-items-start">
                <div class="atw-mx-auto atw-flex-shrink-0 atw-flex atw-items-center atw-justify-center atw-h-12 atw-w-12 atw-rounded-full atw-bg-red-100 sm:atw-mx-0 sm:atw-h-10 sm:atw-w-10">
                    <!-- Heroicon name: outline/exclamation -->
                    <svg class="atw-h-6 atw-w-6 atw-text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="atw-mt-3 atw-text-center sm:atw-mt-0 sm:atw-ml-4 sm:atw-text-left">
                    <h3 class="atw-text-lg atw-leading-6 atw-font-medium atw-text-gray-900" id="modal-title">
                    Deactivate account
                    </h3>
                    <div class="atw-mt-2">
                    <p class="atw-text-sm atw-text-gray-500">
                        Are you sure you want to deactivate your account? All of your data will be permanently removed. This action cannot be undone.
                    </p>
                    </div>
                </div>
                </div>
            </div>
            <div class="atw-bg-gray-50 atw-px-4 atw-py-3 sm:atw-px-6 sm:atw-flex sm:atw-flex-row-reverse">
                <button type="button" class="atw-w-full atw-inline-flex atw-justify-center atw-rounded-md atw-border atw-border-transparent atw-shadow-sm atw-px-4 atw-py-2 atw-bg-red-600 atw-text-base atw-font-medium atw-text-white hover:atw-bg-red-700 focus:atw-outline-none focus:atw-ring-2 focus:atw-ring-offset-2 focus:atw-ring-red-500 sm:atw-ml-3 sm:atw-w-auto sm:atw-text-sm">
                Deactivate
                </button>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                Cancel
                </button>
            </div>
            </div>
        </div>
        </div>

        <?php
    }
}