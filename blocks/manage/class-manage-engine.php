<?php
/**
 * [adverts_add] shortcode
 * 
 * This class handles the [adverts_add] shortcode logic, based on input generates a correct step.
 *
 * @package Adverts
 * @subpackage Classes
 * @since 1.4.0
 * @access public
 */



class Adverts_Block_Manage_Engine {
    
    public $path = null;

    public function __construct() {
        $this->path = dirname( __FILE__ );
    }

    public function main( $atts = array() ) {

        if(!get_current_user_id()) {

            $permalink = get_permalink();
            $message = __('Only logged in users can access this page. <a href="%1$s">Login</a> or <a href="%2$s">Register</a>.', "wpadverts");
            $parsed = sprintf($message, wp_login_url( $permalink ), wp_registration_url( $permalink ) );
            
            return $this->flash( array( 
                "info" => array( 
                    array( "message" => $parsed, "icon" => "fa fa-lock" ),  
                )
            ), "big" );
        }
        
        if( adverts_request("advert_id") ) {
            $action = "edit";
        } else {
            $action = "";
        }
        
        $action = apply_filters( "adverts_manage_action", $action );
        
        
        if( $action == "" ) {
            $content = $this->action_list( $atts );
        } else if( $action == "edit" ) {
            $content = $this->action_edit( $atts );
        } 
        
        return apply_filters("adverts_manage_action_$action", $content, $atts, true);
    }

    public function action_list( $atts ) {

        extract(shortcode_atts(array(
            'name' => 'default',
            'paged' => adverts_request("pg", 1),
            'posts_per_page' => 20,
        ), $atts));
        
        // Load ONLY current user data
        $loop = new WP_Query( apply_filters( "adverts_manage_query", array( 
            'post_type' => 'advert', 
            'post_status' => apply_filters("adverts_sh_manage_list_statuses", array('publish', 'advert-pending', 'pending', 'expired') ),
            'posts_per_page' => $posts_per_page, 
            'paged' => $paged,
            'author' => get_current_user_id()
        ) ) );
    
        $baseurl = apply_filters( "adverts_manage_baseurl", get_the_permalink() );
        $paginate_base = $baseurl . '%_%';
        $paginate_format = stripos( $paginate_base, '?' ) ? '&pg=%#%' : '?pg=%#%';
        $edit_format = stripos( $baseurl, '?' ) ? '&advert_id=%#%' : '?advert_id=%#%';
    
        // adverts/templates/manage.php
     
        $button_s1_args = array(
            "type" => "secondary",
            "icon" => "", 
            "html" => __( "Manage", "wpadverts" ), 
            "desktop" => "text-a", 
            "mobile" => "text", 
        );

        ob_start();
        include dirname( __FILE__ ) . '/templates/manage-list.php';
        return ob_get_clean();
    }

    public function action_edit( $atts ) {
        $params = shortcode_atts(array(
            'name' => 'default',
            'moderate' => false
        ), $atts, "adverts_manage");
        
        extract( $params );
        
        include_once ADVERTS_PATH . 'includes/class-html.php';
        include_once ADVERTS_PATH . 'includes/class-form.php';
        include_once ADVERTS_PATH . 'includes/class-checksum.php';
        
        $checksum_args = array(
            "requires-post-id" => 1,
            "form_name" => "advert",
            "name" => $params["name"],
            "moderate" => $params["moderate"]
        );
        
        $checksum = new Adverts_Checksum();
        $checksum_keys = $checksum->get_integrity_keys( $checksum_args );
        
        add_filter( 'adverts_form_load', 'adverts_remove_account_field' );
        
        $form_scheme = apply_filters( "adverts_form_scheme", Adverts::instance()->get("form"), $params );
        $form = new Adverts_Form( $form_scheme );
        $valid = null;
        $error = array();
        $info = array();
        $bind = array();
        $flash = array( "error" => array(), "info" => array() );
        
        remove_filter( 'adverts_form_load', 'adverts_remove_account_field' );
        
        $action = apply_filters( 'adverts_action', adverts_request("_adverts_action", ""), __FUNCTION__ );
        $post_id = (adverts_request("advert_id", null));
    
        // $post_id hijack attempt protection here!
    
        $post = get_post( $post_id );
        
        if( $post === null) {
            $flash["error"][] = array(
                "message" =>  __("Ad does not exist.", "wpadverts"),
                "icon" => "adverts-icon-attention-alt"
            );
            return $this->flash( $flash, "big" );
        }
    
        if( $post->post_author != get_current_user_id() ) {
            $flash["error"][] = array(
                "message" =>  __("You do not own this Ad.", "wpadverts"),
                "icon" => "adverts-icon-attention-alt"
            );
            return $this->flash( $flash, "big" );
        }
        
        $slist = apply_filters("adverts_sh_manage_list_statuses", array( 'publish', 'expired', 'pending', 'advert-pending', 'draft') );
        
        if( !in_array( $post->post_status, $slist ) ) {
            $flash["error"][] = array(
                "message" =>  sprintf( __( "Incorrect post status [%s].", "wpadverts" ), $post->post_status ),
                "icon" => "adverts-icon-attention-alt"
            );
            return $this->flash( $flash, "big" );
        }
        
        $bind = Adverts_Post::get_form_data($post, $form);
        $bind["_adverts_action"] = "update";
        $bind["_post_id"] = $post_id;
        $bind["_post_id_nonce"] = wp_create_nonce( "wpadverts-publish-" . $post_id );
        $bind["_wpadverts_checksum"] = $checksum_keys["checksum"];
        $bind["_wpadverts_checksum_nonce"] = $checksum_keys["nonce"];
    
        $form->bind( $bind );
        
        if($action == "update") {
            
            $form->bind( (array)stripslashes_deep( $_POST ) );
            $valid = $form->validate();
    
            if($valid) {
                
                $init = array();
                
                if( adverts_config( "adverts_manage_moderate") == "1" ) {
                    $init["post"] = array( "post_status" => "pending" );
                }
                
                $post_id = Adverts_Post::save( $form, $post_id, $init );
    
                if(is_wp_error($post_id)) {
                    $error[] = array(
                        "message" => $post_id->get_error_message(),
                        "icon" => "adverts-icon-attention-alt"
                    );
                } else {
                    adverts_force_featured_image( $post_id );
                    
                    $info[] = array(
                        "message" => __("Post has been updated.", "wpadverts"),
                        "icon" => "adverts-icon-ok"
                    );
                }
                
            } else {
                $error[] = array(
                    "message" => __("Cannot update. There are errors in your form.", "wpadverts"),
                    "icon" => "adverts-icon-attention-alt"
                );
            }
        }
        
        $adverts_flash = array( "error" => $error, "info" => $info );
        $baseurl = apply_filters( "adverts_manage_baseurl", get_the_permalink() );
        $actions_class = "adverts-field-actions";
        
        add_action( "wp_footer", array( $this, "modal_delete" ) );



        if( adverts_config( "adverts_manage_moderate" ) == "1" ) {
            add_action( "wpadverts/tpl/partial/form/before-buttons", array( $this, "moderation_notice" ) );
        }

        $show_buttons = true;
        $buttons = array(
            array(
                "text" => __( "Update", "wpadverts" ),
                "html" => null,
                "icon" => "",
                "type" => "primary",
                "class" => "",
                "action" => "submit",
                "name" => "wpadverts_manage_update",
                "value" => "1"
            )
        );

        $buttons_manage = array(
            array(
                "class" => "",
                "order" => 10,
                "button" => array( 
                    "text" => __( "View", "wpadverts" ), 
                    "type" => "secondary",
                    "attr" => array(
                        "onclick" => sprintf( "window.location.href='%s'", get_permalink( $post_id ) )
                    )
                ),
                "button_type" => "secondary_button"
            ),
            array(
                "class" => "js-wpa-block-manage-delete",
                "order" => 1000,
                "button" => array( 
                    "text" => __( "Delete ...", "wpadverts" ), 
                    "type" => "secondary",
                    "attr" => array(
                        "data-nonce" => wp_create_nonce( sprintf( 'wpadverts-delete-%d', $post_id ) ),
                        "data-id" => $post_id,
                        "data-title" => get_the_title( $post_id )
                    )
                ),
                "button_type" => "secondary_button"
            )
        );

        $buttons_manage = apply_filters( "wpadverts/block/manage/buttons-manage", $buttons_manage, $post_id );
        uasort( $buttons_manage, array( $this, "sort_buttons" ) );

        // adverts/templates/manage-edit.php
        include dirname( __FILE__ ) . '/templates/manage-edit.php';

        remove_action( "wpadverts/tpl/partial/form/before-buttons", array( $this, "moderation_notice" ) );
    }

    public function moderation_notice() {
        echo $this->flash( array( "info" => array( array(
            "icon" => "fa-solid fa-info-circle",
            "message" => __( "<strong>Important Note.</strong> After submitting changes your Ad will be held for moderation. It will become active again once the Administrator will approve it.", "wpadverts" )
        ) ) ) );
    }

    public function flash( $data, $layout = "normal" ) {
        ob_start();
        wpadverts_block_flash( $data, $layout );
        return ob_get_clean();
    }

    public function modal_delete() {
        wpadverts_block_modal();
    }

    public function sort_buttons( $a, $b ) {
        return ( $a["order"] >= $b["order"] );
    }
}