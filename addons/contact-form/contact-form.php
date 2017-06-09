<?php
/**
 * Contact Form Module
 * 
 * This module display contact form on Ad details pages instead of just the contact information.
 * 
 * @package Adverts
 * @subpackage ContactForm
 * @author Grzegorz Winiarski
 * @version 0.1
 */

global $adverts_namespace;

// Add Contact Form to adverts_namespace, in order to store module options and default options
$adverts_namespace['contact_form'] = array(
    'option_name' => 'adext_contact_form_config',
    'default' => array(
        'show_phone' => '1',
        'from_name' => '',
        'from_email' => ''
    )
);

if(is_admin() ) {
    add_action( "init", "adext_contact_form_init_admin", 20 );
} else {
    add_action( "init", "adext_contact_form_init_frontend", 20 );
}

/**
 * Renders contact form on Ad details page
 * 
 * This function is called by adverts_tpl_single_bottom action in
 * wpadverts/templates/single.php
 * 
 * @see adverts_tpl_single_bottom action
 * 
 * @since 1.0.10
 * @access public
 * @param int $post_id Post ID
 * @return void
 */
function adext_contact_form( $post_id ) {
   
    include_once ADVERTS_PATH . 'includes/class-form.php';
    include_once ADVERTS_PATH . 'includes/class-html.php';
    
    $show_form = false;
    $flash = null;
    $email = get_post_meta( $post_id, "adverts_email", true );
    $phone = get_post_meta( $post_id, "adverts_phone", true );
    $message = null;
    $form = new Adverts_Form( Adverts::instance()->get( "form_contact_form" ) );
    $buttons = array(
        array(
            "tag" => "input",
            "name" => "adverts_contact_form",
            "type" => "submit",
            "value" => __( "Send Message", "adverts" ),
            "style" => "font-size:1.2em; margin-top:1em",
            "html" => null
        ),
    );
    
    if( adverts_request( "adverts_contact_form" ) ) {
        
        wp_enqueue_script( 'adverts-contact-form-scroll' );
        
        $form->bind( stripslashes_deep( $_POST ) );
        $valid = $form->validate();
        
        if( $valid ) {
            
            $reply_to = $form->get_value( "message_email" );
            
            if( $form->get_value( "message_name" ) ) {
                $reply_to = $form->get_value( "message_name" ) . "<$reply_to>";
            }
            
            $mail = array(
                "to" => get_post_meta( $post_id, "adverts_email", true ),
                "subject" => $form->get_value( "message_subject" ),
                "message" => $form->get_value( "message_body" ),
                "headers" => array(
                    "Reply-To: " . $reply_to
                )
            );
            
            $mail = apply_filters( "adverts_contact_form_email", $mail, $post_id, $form );
           
            add_filter( 'wp_mail_from', 'adext_contact_form_mail_from' );
            add_filter( 'wp_mail_from_name', 'adext_contact_form_mail_from_name' );
            
            wp_mail( $mail["to"], $mail["subject"], $mail["message"], $mail["headers"] );
            
            remove_filter( 'wp_mail_from', 'adext_contact_form_mail_from' );
            remove_filter( 'wp_mail_from_name', 'adext_contact_form_mail_from_name' );
            
            $form->bind( array() );
            
            $message = __( "Your message has been sent.", "adverts" );
            $flash = new Adverts_Html( "div", array( "class" => "adverts-flash-info" ), "<span>" . $message . "</span>" );
        } else {
            $message = __( "There are errors in your form.", "adverts" );
            $flash = new Adverts_Html( "div", array( "class" => "adverts-flash-error" ), "<span>" . $message . "</span>" );
            $show_form = true; 
        }
    }
    
    ?>

    <div id="adverts-contact-form-scroll"></div>

    <?php echo $flash ?>

    <div class="adverts-single-actions">
        <?php if( ! empty( $email ) ): ?>
        <a href="#" class="adverts-button adverts-show-contact-form">
            <?php esc_html_e("Send Message", "adverts") ?>
            <span class="adverts-icon-down-open"></span>
        </a>
        <?php endif; ?>
        
        <?php if( adverts_config( "contact_form.show_phone") == "1" && ! empty( $phone ) ): ?>
        <span class="adverts-button" style="background-color: transparent; cursor: auto">
            <?php esc_html_e( "Phone", "adverts" ) ?>
            <a href="tel:<?php echo esc_html( $phone ) ?>"><?php echo esc_html( $phone ) ?></a>
            <span class="adverts-icon-phone"></span>
        </span>
        <?php endif; ?>
    </div>

    <?php if( ! empty( $email ) ): ?>
    <div class="adverts-contact-box" <?php if($show_form): ?>style="display: block"<?php endif ?>>
        <?php include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/form.php' ) ?>
    </div>
    <?php endif; ?>

    <?php
}

/**
 * Frontend Adverts Contact Form Init Function
 * 
 * Deregister default contact box and register contact form box instead.
 * 
 * @since 1.0.10
 * @return void
 */
function adext_contact_form_init_frontend() {
    remove_action('adverts_tpl_single_bottom', 'adverts_single_contact_information');
    add_action('adverts_tpl_single_bottom', 'adext_contact_form');
    
    wp_register_script( 'adverts-contact-form-scroll', ADVERTS_URL  .'/assets/js/adverts-contact-form-scroll.js', array( 'jquery' ), "1", true);
}

/**
 * Frontend Adverts Contact Form Admin Init Function
 * 
 * Deregister default show contact AJAX action
 * 
 * @since 1.0.10
 * @return void
 */
function adext_contact_form_init_admin() {
    remove_action('wp_ajax_adverts_show_contact', 'adverts_show_contact');
    remove_action('wp_ajax_nopriv_adverts_show_contact', 'adverts_show_contact');
}

/**
 * Sets default mail "From" email
 * 
 * This function is applied via wp_mail_from filter in adext_contact_form function.
 * 
 * @since 1.0.10
 * @param string $from_email
 * @return string
 */
function adext_contact_form_mail_from( $from_email ) {
    if( adverts_config( "contact_form.from_email") ) {
        return adverts_config( "contact_form.from_email");
    } else {
        return $from_email;
    }
}

/**
 * Sets default mail "From" name
 * 
 * This function is applied via wp_mail_from filter in adext_contact_form function.
 * 
 * @since 1.0.10
 * @param string $from_name
 * @return string
 */
function adext_contact_form_mail_from_name( $from_name ) {
    if( adverts_config( "contact_form.from_name") ) {
        return adverts_config( "contact_form.from_name");
    } else {
        return $from_name;
    }
}

// Contact Form
Adverts::instance()->set("form_contact_form", array(
    "name" => "contact",
    "action" => "",
    "field" => array(
        array(
            "name" => "message_name",
            "type" => "adverts_field_text",
            "label" => __("Your Name", "adverts"),
            "order" => 10,
            "class" => "",
            "validator" => array( 
                array( "name" => "is_required" ),
            )
        ),
        array(
            "name" => "message_email",
            "type" => "adverts_field_text",
            "label" => __("Your Email", "adverts"),
            "order" => 10,
            "class" => "",
            "validator" => array( 
                array( "name" => "is_required" ),
                array( "name" => "is_email" ),
            )
        ),
        array(
            "name" => "message_subject",
            "type" => "adverts_field_text",
            "label" => __("Subject", "adverts"),
            "order" => 10,
            "class" => "",
            "validator" => array( 
                array( "name" => "is_required" ),
            )
        ),
        array(
            "name" => "message_body",
            "type" => "adverts_field_textarea",
            "mode" => "plain-text",
            "label" => __("Message", "adverts"),
            "order" => 10,
            "class" => "",
            "validator" => array( 
                array( "name" => "is_required" ),
            )
        ),
    )
));