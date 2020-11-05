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

add_action( "init", "adext_contact_form_init", 20 );

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

    $email = get_post_meta( $post_id, "adverts_email", true );
    $phone = get_post_meta( $post_id, "adverts_phone", true );
    
    ?>
    <div class="adverts-single-actions">
        <?php if( adext_contact_form_get_to( $post_id ) ): ?>
        <a href="#" class="adverts-button adverts-show-contact-form">
            <?php esc_html_e("Send Message", "wpadverts") ?>
            <span class="adverts-icon-down-open"></span>
        </a>
        <?php add_action( "adverts_tpl_single_bottom", "adext_contact_form_content", 2000 ) ?>
        <?php endif; ?>
        
        <?php if( adverts_config( "contact_form.show_phone") == "1" && ! empty( $phone ) ): ?>
        <span class="adverts-button" style="background-color: transparent; cursor: auto">
            <?php esc_html_e( "Phone", "wpadverts" ) ?>
            <a href="tel:<?php echo esc_html( $phone ) ?>"><?php echo esc_html( $phone ) ?></a>
            <span class="adverts-icon-phone"></span>
        </span>
        <?php endif; ?>
    </div>
    <?php
    
}

function adext_contact_form_content( $post_id ) {
    

    include_once ADVERTS_PATH . 'includes/class-form.php';
    include_once ADVERTS_PATH . 'includes/class-html.php';
    
    $show_form = false;
    $flash = array( "error" => array(), "info" => array());;
    $email = get_post_meta( $post_id, "adverts_email", true );
    $phone = get_post_meta( $post_id, "adverts_phone", true );
    $message = null;
    
    $form_scheme = apply_filters( "adverts_form_scheme", Adverts::instance()->get( "form_contact_form" ), array() );
   
    // adverts_form_load filter will add checksum fields
    $form = new Adverts_Form( $form_scheme );
    
    $actions_class = "adverts-field-actions";
    $buttons = array(
        array(
            "tag" => "input",
            "name" => "adverts_contact_form",
            "type" => "submit",
            "value" => __( "Send Message", "wpadverts" ),
            "class" => "adverts-button adverts-cancel-unload",
            "html" => null
        ),
    );
    
    wp_enqueue_script( 'adverts-form' );
    
    if( adverts_request( "adverts_contact_form" ) ) {
        
        wp_enqueue_script( 'adverts-contact-form-scroll' );
        
        $form->bind( stripslashes_deep( $_POST ) );
        $valid = $form->validate();

        if( $valid ) {
            
            //Adext_Contact_Form::instance()->send_message( get_post( $post_id ), $form );
            do_action( "adext_contact_form_send", $post_id, $form );
            
            // delete uploaded files ($form)
            $uniqid = sanitize_file_name( adverts_request( "wpadverts-form-upload-uniqid" ) );
            adext_contact_form_delete_tmp_files( $form->get_scheme(), $uniqid );
            
            $bind = array();
            $bind["_wpadverts_checksum"] = adverts_request( "_wpadverts_checksum" );
            $bind["_wpadverts_checksum_nonce"] = adverts_request( "_wpadverts_checksum_nonce" );
            
            $form->bind( $bind );
            
            $flash["info"][] = array(
                "message" => __( "Your message has been sent.", "wpadverts" ),
                "icon" => "adverts-icon-ok"
            );
            $show_form = true; 
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
    
    ?>

    <div id="adverts-contact-form-scroll"></div>
    
    <?php if( adext_contact_form_get_to( $post_id ) ): ?>
    <div class="adverts-contact-box adverts-contact-box-toggle" <?php if($show_form): ?>style="display: block"<?php endif ?>>
        <?php adverts_flash( $flash ) ?>
        <?php include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/form.php' ) ?>
    </div>
    <?php endif; ?>

    <?php
}

function adext_contact_form_delete_tmp_files( $form_scheme, $uniqid ) {
    
    $fields = array();
    
    $form_name = $form_scheme["name"];
    
    foreach($form_scheme["field"] as $field ) {
        if( $field["type"] == "adverts_field_gallery" && isset( $field["save"]["method"] ) && $field["save"]["method"] == "file" ) {
            $fields[] = $field;
        }
    }

    if( empty( $fields ) ) {
        return;
    }
    
    include_once ADVERTS_PATH . '/includes/class-upload-helper.php';
    
    foreach( $fields as $field ) {
        $v = new Adverts_Upload_Helper;
        $v->set_field( $field );
        $v->set_form_name( $form_name );
        $v->set_uniquid( $uniqid );

        $files_path = $v->get_path() . "/*";
        $files_all = glob( $files_path );
        
        foreach( $files_all as $file ) {
            
            if( ! file_exists( $file ) ) {
                continue;
            }
            
            do {
                if( is_dir( $file ) ) {
                    rmdir( $file );
                } else {
                    wp_delete_file( $file );
                }
                $file = dirname( $file );
                $files = glob( $file . "/*" );
            } while( empty( $files ) );

        } // endforeach
    }
}

/**
 * Adverts Contact Form Init Function
 * 
 * Registers actions and filters which need to be run in both wp-admin and frontend.
 * 
 * @since 1.3.0
 * @return void
 */
function adext_contact_form_init() {

    if( class_exists( "Adext_Emails" ) ) {
        include_once ADVERTS_PATH . '/addons/contact-form/includes/class-emails-integration.php';
        new Adext_Contact_Form_Emails_Integration();
    } else {
        add_action( "adext_contact_form_send", "adext_contact_form_send_default_message", 10, 2 );
    }
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

    wp_register_script( 'adverts-contact-form-scroll', ADVERTS_URL  .'/assets/js/wpadverts-contact-form-scroll.js', array( 'jquery' ), "1.3.5", true);
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

/**
 * Sends the default contact form message.
 * 
 * This function is executed by adext_contact_form_send action registered in
 * adext_contact_form_init function.
 * 
 * @see adext_contact_form_send filter
 * @see adext_contact_form_init()
 * 
 * @since   1.3.0
 * @param   int           $post_id    ID of the current Advert
 * @param   Adverts_Form  $form       Submitted form object
 * @return  void
 */
function adext_contact_form_send_default_message( $post_id, $form ) {
    
    $reply_to = $form->get_value( "message_email" );

    if( $form->get_value( "message_name" ) ) {
        $reply_to = $form->get_value( "message_name" ) . "<$reply_to>";
    }
    
    $mail = array(
        "to" => adext_contact_form_get_to( $post_id ),
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
}

/**
 * Returns "to" address for contact form.
 * 
 * Note this function is used only when Emails Module is disabled.
 * 
 * @uses adext_contact_form_get_to_meta_name filter
 * @uses adext_contact_form_get_to filter
 * 
 * @since   1.3.3
 * @param   int     $post_id    Current Post ID
 * @return  string              Email address to send the contact form to
 */
function adext_contact_form_get_to( $post_id ) {
    
    $meta_name = apply_filters( "adext_contact_form_get_to_meta_name", "adverts_email" );
    $to = trim( get_post_meta( $post_id, $meta_name, true ) );
    
    if( empty( $to ) && get_post_field( 'post_author') > 0 ) {
        
        $user_to = get_user_by( "ID", get_post_field( 'post_author' ) );
        
        if( $user_to ) {
            $to = $user_to->user_email;
        }
    }
    
    return apply_filters( "adext_contact_form_get_to", $to, $post_id );
}

// Contact Form
Adverts::instance()->set("form_contact_form", array(
    "name" => "contact",
    "action" => "",
    "field" => array(
        array(
            "name" => "message_name",
            "type" => "adverts_field_text",
            "label" => __("Your Name", "wpadverts"),
            "order" => 10,
            "class" => "",
            "validator" => array( 
                array( "name" => "is_required" ),
            )
        ),
        array(
            "name" => "message_email",
            "type" => "adverts_field_text",
            "label" => __("Your Email", "wpadverts"),
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
            "label" => __("Subject", "wpadverts"),
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
            "label" => __("Message", "wpadverts"),
            "order" => 10,
            "class" => "",
            "validator" => array( 
                array( "name" => "is_required" ),
            )
        ),
    )
));