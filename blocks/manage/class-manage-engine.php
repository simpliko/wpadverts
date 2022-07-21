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

    public function get_sort_options() {
        return apply_filters( "adverts_manage_sort_options", array(
            "date" => array(
                "label" => __("Publish Date", "wpadverts"),
                "items" => array(
                    "date-desc" => __("Newest First", "wpadverts"),
                    "date-asc" => __("Oldest First", "wpadverts")
                )
            ),
            "price" => array(
                "label" => __("Price", "wpadverts"),
                "items" => array(
                    "price-asc" => __("Cheapest First", "wpadverts"),
                    "price-desc" => __("Most Expensive First", "wpadverts")
                )
            ),
            "title" => array(
                "label" => __("Title", "wpadverts"),
                "items" => array(
                    "title-asc" => __("From A to Z", "wpadverts"),
                    "title-desc" => __("From Z to A", "wpadverts")
                )
            )
        ) );
    }

    public function main( $atts = array() ) {

        if(!get_current_user_id()) {

            $permalink = get_permalink();
            $message = __('Only logged in users can access this page.', "wpadverts");
            $parsed = sprintf($message, wp_login_url( $permalink ), wp_registration_url( $permalink ) );
            
            return $this->flash( array( 
                "info" => array( 
                    array( 
                        "message" => $parsed, 
                        "icon" => "fa fa-lock",
                        "link" => array(
                            array( "title" => __( "Login", "wpadverts" ), "url" => wp_login_url( $permalink ) ),
                            array( "title" => __( "Register", "wpadverts" ), "url" => wp_registration_url() ),
                        ) 

                    ),  
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
        
        $sort_options = $this->get_sort_options();

        if($allow_sorting && adverts_request("adverts_sort")) {
            $adverts_sort = adverts_request("adverts_sort");
        } else {
            $adverts_sort = $atts["order_by"];
        }

        $sarr = explode("-", $adverts_sort);
        $sort_current_text = __("Publish Date", "wpadverts");
        $sort_current_title = sprintf( __( "Sort By: %s - %s", "wpadverts"), __("Publish Date", "wpadverts"), __("Newest First", "wpadverts") );

        if( isset( $sarr[1] ) && isset( $sort_options[$sarr[0]]["items"][$adverts_sort] ) ) {

            $sort_key = $sarr[0];
            $sort_dir = $sarr[1];

            if($sort_dir == "asc") {
                $sort_dir = "ASC";
            } else {
                $sort_dir = "DESC";
            }

            if($sort_key == "title") {
                $orderby["title"] = $sort_dir;
            } elseif($sort_key == "date") {
                $orderby["date"] = $sort_dir;
            } elseif($sort_key == "price") {
                $orderby["adverts_price__orderby"] = $sort_dir;
                $meta["adverts_price__orderby"] = array(
                    'key' => 'adverts_price',
                    'type' => 'NUMERIC',
                    'compare' => 'NUMERIC',
                );
            } else {
                // apply sorting using adverts_list_query filter.
            }

            $sort_current_text = $sort_options[$sort_key]["label"] ;
            $s_descr = $sort_options[$sort_key]["items"][$adverts_sort];
            $sort_current_title = sprintf( __( "Sort By: %s - %s", "wpadverts"), $sort_current_text, $s_descr );
        } else {
            $adverts_sort = $order_by;
            $orderby["date"] = "desc"; 
        }

        // Load ONLY current user data
        $loop = new WP_Query( apply_filters( "adverts_manage_query", array( 
            'post_type' => 'advert', 
            'post_status' => apply_filters("adverts_sh_manage_list_statuses", array('publish', 'advert-pending', 'pending', 'expired') ),
            'posts_per_page' => $posts_per_page, 
            'paged' => $paged,
            'author' => get_current_user_id(),
            'orderby' => $orderby
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

        wp_enqueue_style( 'adverts-frontend' );
        wp_enqueue_style( 'adverts-icons' );
        wp_enqueue_style( 'adverts-icons-animate' );

        wp_enqueue_script( 'adverts-frontend' );
        wp_enqueue_script( 'adverts-auto-numeric' );

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
        
        $adverts_flash = array( "error" => $error, "success" => $info );
        $baseurl = apply_filters( "adverts_manage_baseurl", get_the_permalink() );
        $actions_class = "adverts-field-actions";
        
        add_action( "wp_footer", array( $this, "modal_delete" ) );



        if( adverts_config( "adverts_manage_moderate" ) == "1" ) {
            add_action( "wpadverts/tpl/partial/form/before-buttons", array( $this, "moderation_notice" ) );
        }

        $buttons_position = "";
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

        $_layouts = array(
            "adverts-form-stacked" => "wpa-layout-stacked",
            "adverts-form-aligned" => "wpa-layout-aligned"
        );
        $form_layout = $_layouts[$form->get_layout()];

        // adverts/templates/manage-edit.php
        ob_start();
        include dirname( __FILE__ ) . '/templates/manage-edit.php';
        $result = ob_get_clean();

        remove_action( "wpadverts/tpl/partial/form/before-buttons", array( $this, "moderation_notice" ) );

        return $result;
    }

    public function moderation_notice() {
        echo $this->flash( array( "info" => array( array(
            "icon" => "fa-solid fa-info-circle",
            "message" => __( "<strong>Important Note.</strong> After submitting changes your Ad will be held for moderation. It will become active again once the Administrator will approve it.", "wpadverts" )
        ) ) ) );
    }

    public function flash( $data, $layout = "normal" ) {
        return wpadverts_block_flash( $data, $layout );
    }

    public function modal_delete() {
        wpadverts_block_modal();
    }

    public function sort_buttons( $a, $b ) {
        return ( $a["order"] >= $b["order"] );
    }
}