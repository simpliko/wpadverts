<?php

class Adverts_Block_Single_Contact {
    
    public $path = null;
    
    public function __construct() {
        add_action( "init", array( $this, "init" ) );
    }
    
    public function init() {
        
        $package = "wpadverts";
        $module = "single-contact";
        
        $js_handler = sprintf( "block-%s-%s", $package, $module );
        
        // automatically load dependencies and version
        $asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');

        $this->path = dirname(__FILE__);
        
        /*
        wp_register_style(
            'wpadverts-blocks-editor-single-contact',
            ADVERTS_URL . '/assets/css/blocks-editor-single-contact.css',
            array( 'wp-edit-blocks' ),
            filemtime( ADVERTS_PATH . '/assets/css/blocks-editor-single-contact.css' )
        );
        */

        wp_register_script(
            $js_handler,
            plugins_url( 'build/index.js', __FILE__ ),
            $asset_file['dependencies'],
            $asset_file['version']
        );

        wp_register_script(
            "wpadverts-block-single-contact",
            ADVERTS_URL . '/assets/js/block-single-contact.js',
            array( 'jquery' ),
            '2.0.4'
        );

        register_block_type_from_metadata(
            dirname( __FILE__ ) . '/src/block.json',
            array(            
                //'editor_style' => 'wpadverts-blocks-editor-single-contact',
                'editor_script' => $js_handler,
                'render_callback' => array( $this, "render" ),
                //'style' => wpadverts_load_assets_globally() ? 'wpadverts-blocks' : null,
                //'script' => wpadverts_load_assets_globally() ? 'wpadverts-block-single-contact' : null
            )
        );

    }
    
    public function render( $atts = array() ) {

        // If user is in Publish -> Preview use the Adverts_Block_Templates::get_id() instead of current page ID.
        if( Adverts_Block_Templates::get_id() !== null ) {
            $post_id = null;
        } else {
            $post_id = get_the_ID();
        }

        $atts = apply_filters( "wpadverts/block/single-contact/atts", $atts );

        if(adverts_config( 'expired_ad_status' ) === "200" && $post_id && get_post_status( $post_id ) == "expired" ) {
            $params = array(
                "message_header" =>  "",
                "message" => __("Contact options are disabled for expired ads.", "wpadverts"),
                "show_buttons" => false
            );
            return $this->render_disabled( $atts, $params );
        } else if($atts["requires"] && ! current_user_can( $atts["requires"] ) ) {
            $params = array(
                "message_header" =>  __("Only logged-in members can contact sellers.", "wpadverts"),
                "message" => __("Please login or register to to send a message.", "wpadverts"),
                "show_buttons" => true
            );
            return $this->render_disabled( $atts, $params );
        } else {
            return $this->render_contacts( $atts );
        }
    }

    public function render_disabled( $atts, $params ) {

        $url_login = wp_login_url( get_permalink() );
        $url_register = wp_registration_url();
        $message_header = $params["message_header"];
        $message = $params["message"];
        $show_buttons = $params["show_buttons"];

        if( $atts["requires_login"] ) {
            $url_login = $atts["requires_login"];
        }

        if( $atts["requires_register"] ) {
            $url_register = $atts["requires_register"];
        }

        $atts["layout"] = "contact-disabled";

        $template = sprintf( "%s/templates/%s.php", dirname( __FILE__ ), $atts["layout"]);
        ob_start();
        include $template;
        return ob_get_clean();
    }

    public function render_contacts( $atts ) {

        if( ! wpadverts_load_assets_globally() ) {
            wp_enqueue_style( 'wpadverts-blocks' );
            wp_enqueue_script( 'wpadverts-block-details');
        }

        $params = shortcode_atts(array(
            'name' => 'default',
            'post_type' => 'advert'
        ), $atts, 'adverts_details' );

        extract( $params );

        include_once ADVERTS_PATH . '/includes/class-block-templates.php';

        // If user is in Publish -> Preview use the Adverts_Block_Templates::get_id() instead of current page ID.
        if( Adverts_Block_Templates::get_id() !== null ) {
            $post_id = Adverts_Block_Templates::get_id();
        } else {
            $post_id = get_the_ID();
        }

        add_action( "wp_footer", [ "Adverts_Block_Manager", "print_custom_styles" ] );

        $contact_options = array( );

        if( $atts["custom_contact"] !== true ) {
            $contact_options = $this->_get_default_contacts( $atts, $post_id );
        } else {
            $contact_options = $this->_get_custom_contacts( $atts, $post_id );
        }

        

        foreach( $contact_options as $k => $o ) {
            if( ! isset( $o["is_visible"] ) ) {
                $contact_options[$k]["is_visible"] = $o["is_active"];
            }

            if( $contact_options[$k]["is_active"] && $contact_options[$k]["is_visible"] ) {

                if($o["class"] == "wpadverts-show-contact-form" && $atts["form_button_hide"] ) {
                    $contact_options[$k]["is_visible"] = false;
                }

                if( isset( $o["content_callback"] ) && is_array( $o["content_callback"] ) ) {
                    $this->_add_content_callback( $o["content_callback"] );
                }
            }
        }
        
        $contact_options = apply_filters( "wpadverts/block/single-contact/contact-options", $contact_options, $post_id, $atts );
        $has_visible_contact_options = false;
        
        foreach( $contact_options as $k => $o ) {
            if( $contact_options[$k]["is_active"] && $contact_options[$k]["is_visible"] ) {
                $has_visible_contact_options = true;
            }
        }

        $contact_options = $this->_set_primary_contact_option( $contact_options );

        $atts["layout"] = "contact";

        $options_flex = "row";
        if( $atts["contacts_stacked"] == false ) {
            $options_flex = "md:atw-flex-row md:atw-flex-wrap";
        } else {
            $options_flex = "md:atw-flex-col atw-w-full";
        }

        $template = sprintf( "%s/templates/%s.php", dirname( __FILE__ ), $atts["layout"]);
        ob_start();
        include $template;
        return ob_get_clean();

    }

    protected function _set_primary_contact_option( $contact_options ) {
        $has_primary = false;

        foreach( $contact_options as $k => $o ) {
            if( $o["is_active"] && $o["is_visible"] && ! $has_primary ) {
                $contact_options[$k]["type"] = "primary";
                $has_primary = true;
            } else {
                $contact_options[$k]["type"] = "secondary";
            }
        }

        return $contact_options;
    }

    protected function _get_default_contacts( $atts, $post_id ) {
        $contact_methods = $this->_get_contact_options( $atts, $post_id );
        $contact_options = array( );

        foreach($contact_methods as $key => $cm) {
            if(isset($cm["is_active"]) && $cm["is_active"]) {
                $contact_options[] = $cm;
            }
        }

        return $contact_options;
    }

    protected function _get_custom_contacts( $atts, $post_id ) {
        $contact_methods = $this->_get_contact_options( $atts, $post_id );
        //return [];
        //echo "<pre>";print_r($contact_methods);echo "</pre>";
        $contact_options = array( );
        
//echo "<pre>";print_r($atts);echo "</pre>";
        $primary = false;
        $contacts_order = [];
        foreach($atts['contact_order'] as $c) {
            if(in_array($c, $atts["contact"])) {
                $contacts_order[] = $c;
            }
        }
        if( empty( $contacts_order ) ) {
            $contacts_order = $atts['contact'];
        }


        foreach( $contacts_order as $k => $contact ) {
            if( isset( $contact_methods[ $contact ] ) ) {
                $cm = $contact_methods[ $contact ];
                if(!$primary) {
                    $cm["type"] = "primary";
                    $primary = true;
                } else {
                    $cm["type"] = "secondary";
                }
                $contact_options[] = $cm;
            }
        } 

        return $contact_options;
    }

    protected function _add_content_callback( $cb ) {
        $callback = null;
        $priority = 10;

        if( isset( $cb["callback"] ) && is_callable( $cb["callback"] ) ) {
            $callback = $cb["callback"];
        }

        if( isset( $cb["priority"]) && is_numeric( $cb["priority"] ) ) {
            $priority = absint( $cb["priority"] );
        } 

        if( ! has_action( "wpadverts/block/details/tpl/contact-content", $callback ) ) {
            add_action( "wpadverts/block/details/tpl/contact-content", $callback, $priority, 2 );
        }
    }
    
    protected function _get_contact_options( $atts, $post_id ) {
        return wpadverts_block_get_contact_options( $post_id, $atts );
    }
}