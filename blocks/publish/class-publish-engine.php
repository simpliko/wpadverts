<?php
/**
 * [adverts_add] shortcode
 * 
 * This class handles the [adverts_add] shortcode logic, based on input generates a correct step.
 *
 * @package Adverts
 * @subpackage Classes
 * @since 2.0
 * @access public
 */



class Adverts_Block_Publish_Engine {
    
    /**
     * Raw params passed to the shortcode.
     *
     * @var array
     */
    protected $_atts = array();
    
    /**
     * Shortcode $_atts filtered using shortcode_atts() function
     *
     * @var array
     */
    protected $_params = array();
    
    /**
     * Form object generated for this shortcode
     *
     * @var Adverts_Form
     */
    protected $_form = null;
    
    /**
     * Post ID created when saving the Ad data
     *
     * @var int
     */
    protected $_post_id = null;
    
    /**
     * Post ID nonce created when setting the post ID
     *
     * @var int
     */
    protected $_post_id_nonce = null;
    
    /**
     * Checks the submitted form integrity
     *
     * @var Adverts_Checksum
     */
    protected $_checksum = null;
    
    /**
     * Returns the shortcode params.
     * 
     * @since   1.4.0
     * @return  array   self::$_params
     */
    public function get_params() {
        return $this->_params;
    }
    
    /**
     * Returns current action based on $_REQUEST['_adverts_action']
     * 
     * @since   1.4.0
     * @return  string  action name
     */
    public function get_action() {
        $action = apply_filters( 'adverts_action', adverts_request("_adverts_action", ""), "shortcode_adverts_add" );
        $action = apply_filters( "wpadverts/block/publish/action", $action, $this->get_params() );

        return $action;
    }
    
    /**
     * Returns Post ID
     * 
     * @since   1.4.0
     * @return  int      Post ID
     */
    public function get_post_id() {
        return $this->_post_id;
    } 
    
    /**
     * Sets the Post ID
     * 
     * @since   1.4.0
     * @param   int     $id     New Post ID
     * @return  void
     */
    public function set_post_id( $id ) {
        $this->_post_id = $id;
        
        if( absint( $id ) > 0 ) {
            $this->set_post_id_nonce( wp_create_nonce( sprintf( "wpadverts-publish-%d", $id ) ) );
        }
    }
    
    /**
     * Returns Post ID Nonce
     * 
     * @since   1.4.0
     * @return  int      Post ID Nonce
     */
    public function get_post_id_nonce() {
        return $this->_post_id_nonce;
    } 
    
    /**
     * Sets the Post ID Nonce
     * 
     * @since   1.4.0
     * @param   int     $id     New Post ID Nonce
     * @return  void
     */
    public function set_post_id_nonce( $id ) {
        $this->_post_id_nonce = $id;
    }
    
    /**
     * Returns Form Object
     * 
     * @since   1.4.0
     * @return  Adverts_Form    Form Object
     */
    public function get_form() {
        return $this->_form;
    }
    
    /**
     * Sets form object
     * 
     * @since   1.4.0
     * @param   Adverts_Form    $form
     */
    public function set_form( $form ) {
        $this->_form = $form;
    }
    
    /**
     * Returns a checksum object
     * 
     * @since   1.4.0
     * @return  Adverts_Checksum
     */
    public function get_checksum() {
        return $this->_checksum;
    }
    
    /**
     * Check if skip_preview param was set in [adverts_add]
     * 
     * If skip_preview is set to true then the [adverts_add] shortcode will have
     * only 2 steps: the form and save/checkout.
     * 
     * @since   1.4.0
     * @return  boolean
     */
    public function skip_preview() {
        if( $this->_params["skip_preview"] != "0" ) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Loads shortcode arguments from checksum
     * 
     * @since   1.4.0
     * @return  void
     */
    public function load_args_from_checksum() {
        include_once ADVERTS_PATH . 'includes/class-checksum.php';
        
        $this->_checksum = new Adverts_Checksum( $this->_checksum_args );
        $this->_params = $this->get_checksum()->get_args_from_checksum();
    }
    
    /**
     * Initiates the shortcode
     * 
     * Creates and instance of Adverts_Form, binds the form values 
     * and sets post_id (and post_id_nonce).
     * 
     * @since   1.4.0
     * @return  void
     */
    public function init() {
        
        include_once ADVERTS_PATH . 'includes/class-html.php';
        include_once ADVERTS_PATH . 'includes/class-form.php';

        $form_scheme = apply_filters( "adverts_form_scheme", Adverts::instance()->get("form"), $this->_params );

        // adverts_form_load filter will register checksum fields
        $this->_form = new Adverts_Form( $form_scheme );
        
        $post_id = adverts_request( "_post_id", null );
        $post_id_nonce = adverts_request( "_post_id_nonce", null );
        
        if( $post_id > 0 && $post_id_nonce && wp_verify_nonce( $post_id_nonce, "wpadverts-publish-" . $post_id )) { 
            $this->set_post_id_nonce( $post_id_nonce );
            $this->set_post_id( $post_id );
        }
        
        $this->_form->bind( $this->_bind() );
    }
    
    /**
     * Main shortcode function
     * 
     * @since   1.4.0
     * @param   array   $atts   Shortcode params
     * @return  string          Content generated by the shortcode
     */
    public function main( $atts = array() ) {

        $this->_params = array();
        $this->_atts = $atts;
        
        wp_enqueue_style( 'adverts-frontend' );
        wp_enqueue_style( 'adverts-icons' );
        wp_enqueue_style( 'adverts-icons-animate' );

        wp_enqueue_script( 'adverts-frontend' );
        wp_enqueue_script( 'adverts-auto-numeric' );

        $this->_params = shortcode_atts(array(
            'name' => 'default',
            'form_name' => 'advert',
            'scheme_name' => 'form',
            'moderate' => false,
            'requires' => "",
            'requires_error' => "",
            'skip_preview' => 0,
            'post_type' => 'advert',
            'preselect_category' => 1,
            'form' => null,
            'button_primary' => null,
            'button_secondary' => null
        ), $atts, 'adverts_add');
        
        $this->_params = apply_filters(
            "wpadverts/block/publish/params",
            $this->_params,
            $this->get_action(),
            $atts
        );

        $requires_param = $this->_handle_requires_param();
        if( $requires_param !== true ) {
            return $requires_param;
        }
        
        // @todo: if(isset(post_id) && !verify_nonce(wpadverts-publish-id)) return error;
        
        include_once ADVERTS_PATH . 'includes/class-checksum.php';
        
        $checksum_args = array();
        $checksum_keys = array( 
            "name", "form_name", "scheme_name", "moderate", "requires", 
            "skip_preview", "post_type", "form_scheme_id" 
        );
        foreach( $checksum_keys as $key ) {
            if( isset( $this->_params[$key] ) ) {
                $checksum_args[$key] = $this->_params[$key];
            }
        }
        
        $this->_checksum_args = $checksum_args;
        $this->_checksum = new Adverts_Checksum( $checksum_args );
        
        extract( $this->_params );

        $this->init();
        
        $action = $this->get_action();
        $content = "";

        $possible_actions = apply_filters( "wpadverts/block/publish/possible-actions", array(
            "" => array( "order" => 0, "name" => "adverts_action_", "callback" => array( $this, "action_add" ) ),
            "preview" => array( "order" => 5, "name" => "adverts_action_preview", "callback" => array( $this, "action_preview" ) ),
            "save-ff" => array( "order" => 10, "name" => "adverts_action_save-ff", "callback" => array( $this, "action_preview" ) ),
            "save" => array( "order" => 15, "name" => "adverts_action_save", "callback" => array( $this, "action_save" ) ),
        ) );

        if( ! isset( $possible_actions[ $action ] ) ) {
            return sprintf( "Incorrect action name [%s].", $action );
        }

        $next_action = $possible_actions[$action];

        $content = call_user_func_array( $next_action["callback"], array( "", $this->get_form(), $this->get_post_id() ) );

        return apply_filters( $next_action["name"], $content, $this->get_form(), $this->get_post_id() );
    }
    
    /**
     * Generates [adverts_add] form (1st step)
     * 
     * @since   1.4.0
     * @return  string  Content generated by this step
     */
    public function action_add() {
        
        $adverts_flash = array( "error" => array(), "info" => array() );
        $bind = $this->_bind();
        $post_id = $this->get_post_id();
        $form = $this->get_form();
        
        // show post ad form page
        //wp_enqueue_script( 'adverts-frontend-add' );
        wp_enqueue_script( 'adverts-form' );
        
        $actions_class = "adverts-field-actions";
        
        
        $bind["_post_id"] = $post_id;
        if( $this->skip_preview() ) {
            $bind["_adverts_action"] = "save-ff";
            $action_label = __( "Publish Listing", "wpadverts" );
        } else {
            $bind["_adverts_action"] = "preview";
            $action_label = __( "Preview Listing", "wpadverts" );
        }
        
        $form->bind( $bind );

        $buttons = apply_filters( "wpadverts/block/publish/buttons", array(
            array(
                "html" => $action_label . '<i class="fa fa-arrow-right atw-pl-3"></i>',
                "icon" => "fas-mail-alt",
                "type" => "primary",
                "class" => "wpadverts-block-cf-send",
                "action" => "submit",
                "name" => "adverts_contact_form",
                "value" => "1"
            )
        ), $this->_params );;

        $_layouts = array(
            "adverts-form-stacked" => "wpa-layout-stacked",
            "adverts-form-aligned" => "wpa-layout-aligned"
        );

        $buttons_position = "atw-flex-col";

        $show_buttons = apply_filters( "wpadverts/block/publish/show-buttons", true, $this->_params );
       
        $form_layout = $_layouts[$form->get_layout()];
        $atts = $this->_params["form"];

        // adverts/templates/add.php
        ob_start();
        include dirname( __FILE__ ) . '/templates/publish-form.php';
        $content = ob_get_clean();
        
        return $content;
    }
    
    /**
     * Generates [adverts_add] preview (2nd step)
     * 
     * @since   1.4.0
     * @return  string  Content generated by this step
     */
    public function action_preview() {

        $args = $this->get_checksum()->get_args_from_checksum();
        
        if( ! is_array( $args ) ) {

            if( $args == -1 ) {
                $error = __( "Could not verify the request checksum. Please refresh the page and try again.", "wpadverts" );
            } else {
                $error = __( "Checksum does not exist. Please refresh the page and try again.", "wpadverts" );
            }

            return $this->flash( array( "info" => array(), "error" => array( array( 
                "icon" => "adverts-icon-close",
                "message" => $error
                
            ) ) ), "big" );
        }
        
        $form = $this->get_form();
        $post_id = $this->get_post_id();
        $post_id_nonce = $this->get_post_id_nonce();
        $error = array();
        $info = array();
        
        if( $post_id > 0 && ! wp_verify_nonce( $post_id_nonce, "wpadverts-publish-" . $post_id ) ) {
            return $this->flash( array( "info" => array(), "error" => array( array( 
                "icon" => "adverts-icon-close",
                "message" => __( "Could not validate. Refresh your session and make sure you are using cookies.", "wpadverts" )
                
            ) ) ), "big" );
        }
        
        //wp_enqueue_script( 'adverts-frontend-add' );
        wp_enqueue_script( 'adverts-form' );

        $form->bind( (array)stripslashes_deep( $_POST ) );
        $valid = $form->validate();

        // Allow to preview only if data in the form is valid.
        if($valid) {
            
            $init = array(
                "post" => array(
                    "ID" => $post_id,
                    "post_name" => sanitize_title( $form->get_value( "post_title" ) ),
                    "post_type" => $this->_params['post_type'],
                    "post_author" => get_current_user_id(),
                    "post_date" => current_time( 'mysql' ),
                    "post_date_gmt" => current_time( 'mysql', 1 ),
                    "post_status" => adverts_tmp_post_status(),
                    "comment_status" => adverts_default_comments_status( $this->_params['post_type'] ),
                    "guid" => ""
                ),
                "meta" => array()
            );
            
            if( adverts_config( "config.visibility" ) > 0 ) {
                $init["meta"]["_expiration_date"] = array(
                    "value" => strtotime( current_time('mysql') . " +". adverts_config( "config.visibility" ) ." DAYS" ),
                    "field" => array(
                        "type" => "adverts_field_hidden"
                    )
                );
            }
            
            $init = apply_filters( "adverts_add_pre_save", $init, $post_id, $form );
            
            // Save post as temporary in DB
            $post_id = Adverts_Post::save($form, $post_id, $init);
            
            $post_content = get_post( $post_id )->post_content;
            $post_content = wp_kses($post_content, wp_kses_allowed_html( 'post' ) );
            $post_content = apply_filters("adverts_the_content", $post_content );
            
            if( is_wp_error( $post_id ) ) {
                $error[] = $post_id->get_error_message();
                $valid = false;
            } 
            
            $adverts_flash = array( "error" => $error, "info" => $info );
            
            if( is_wp_error( $post_id ) ) {
                return shortcode_adverts_flash( $adverts_flash );
            }

            adverts_force_featured_image( $post_id );
            
            $this->set_post_id( $post_id );
            $post_id_nonce = $this->get_post_id_nonce();
            
            if( $this->skip_preview() ) {
                return true;
            }
            
            include_once ADVERTS_PATH . '/includes/class-block-templates.php';
            $block_templates = new Adverts_Block_Templates;

            Adverts_Block_Templates::set_id( $post_id );

            remove_action( 'adverts_tpl_single_bottom', 'adverts_single_contact_information' );
            remove_action( 'adverts_tpl_single_bottom', 'adext_contact_form' );
            remove_action( 'adverts_tpl_single_bottom', 'adext_bp_send_private_message_button', 50 );

            add_action( 'wpadverts/block/details/contact-options', '__return_null', 1000 );

            $post_content = $post_content;
            $post_content = wp_kses($post_content, wp_kses_allowed_html( 'post' ) );
            $post_content = apply_filters( "adverts_the_content", $post_content );
            
            //include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/single.php' );
            $ad_preview = do_blocks( $block_templates->get_post_template( $this->_params['post_type'] ) );

            Adverts_Block_Templates::unset_id();
            // adverts/templates/add-preview.php
            ob_start();
            include dirname( __FILE__ ) . "/templates/publish-preview.php";
            $content = ob_get_clean();
            
        } else {
            $error[] = array(
                "message" => __("There are errors in your form. Please correct them before proceeding.", "wpadverts"),
                "icon" => "adverts-icon-attention-alt"
            );
            
            $adverts_flash = array( "error" => $error, "info" => $info );
            $actions_class = "adverts-field-actions";
            
            if( $this->skip_preview() ) {
                $action_label = __( "Publish Listing", "wpadverts" );
            } else {
                $action_label = __( "Preview Listing", "wpadverts" );
            }
            
            // adverts/templates/add.php
            ob_start();

            $buttons = apply_filters( "wpadverts/block/publish/buttons", array(
                array(
                    "html" => $action_label . '<i class="fa fa-arrow-right atw-pl-3"></i>',
                    "icon" => "fas-mail-alt",
                    "type" => "primary",
                    "class" => "wpadverts-block-cf-send",
                    "action" => "submit",
                    "name" => "adverts_contact_form",
                    "value" => "1"
                )
            ), $this->_params );
    
            $buttons_position = "atw-flex-col";
    
            $show_buttons = apply_filters( "wpadverts/block/publish/show-buttons", true, $this->_params );
            $form_layout = "wpa-layout-aligned";
    
            // adverts/templates/add.php
            ob_start();
            include dirname( __FILE__ ) . '/templates/publish-form.php';
            $content = ob_get_clean();
            
        } // endif $valid
        
        return $content;
    }
    
    /**
     * Generates [adverts_add] save / checkout (3rd step)
     * 
     * @since   1.4.0
     * @return  string  Content generated by this step
     */
    public function action_save() {
        
        $post_id = $this->get_post_id();
        $post_id_nonce = $this->get_post_id_nonce();
        
        if( $post_id > 0 && ! wp_verify_nonce( $post_id_nonce, "wpadverts-publish-" . $post_id ) ) {
            return shortcode_adverts_flash( array( "info" => array(), "error" => array( array( 
                "icon" => "adverts-icon-close",
                "message" => __( "Could not validate. Refresh your session and make sure you are using cookies.", "wpadverts" )
                
            ) ) ) );
        }
        
        $info = array();
        $error = array();
        $success = array();
        
        $moderate = $this->_params["moderate"];
        
        // copied from functions.php ...
        
        if( absint( $post_id ) > 0 ) { 
            $post_id = wp_update_post( array(
                "ID" => $post_id,
                "post_status" => $moderate == "1" ? 'pending' : 'publish',
            ));
        }
        
        $success[] = array(
            "message" => __("Thank you for submitting your ad!", "wpadverts"),
            "icon" => "adverts-icon-ok"
        );
        
        $adverts_flash = array( "error" => $error, "info" => $info, "success" => $success );

        if( !is_user_logged_in() && get_post_meta( $post_id, "_adverts_account", true) == 1 ) {
            adverts_create_user_from_post_id( $post_id, true );
        }
        
        do_action( "wpadverts_advert_saved", $post_id );
        
        $url_publish_ad = get_permalink( get_the_ID() );
        $url_view_list = get_permalink( adverts_config( "config.ads_list_id" ) );

        // adverts/templates/add-save.php
        ob_start();
        include dirname( __FILE__ ) . '/templates/publish-save.php';
        $content = ob_get_clean();
        
        return $content;
    }
    
    /**
     * Returns default data for the [adverts_add] form
     * 
     * @uses    adverts_add_form_bind filter that allows setting the default data.
     * 
     * @since   1.4.0
     * @return  array   Default data for [adverts_add] form
     */
    protected function _bind() {
        
        $post_id = $this->get_post_id();
        $form = $this->get_form();
        $bind = array();
        
        if( $post_id>0 && get_post( $post_id )->post_author == get_current_user_id() ) {

            // if post was already saved in DB (for example for preview) then load it.
            $post = get_post( $post_id );

            // bind data by field name
            $bind = Adverts_Post::get_form_data( $post, $form );


        } elseif( is_user_logged_in() ) {
            $bind["adverts_person"] = wp_get_current_user()->display_name;
            $bind["adverts_email"] = wp_get_current_user()->user_email;
        }
        

        $keys = $this->get_checksum()->get_integrity_keys( $this->_params );
        $checksum = $keys["checksum"];
        $nonce = $keys["nonce"];

        $bind["_wpadverts_checksum"] = $checksum;
        $bind["_wpadverts_checksum_nonce"] = $nonce;
        $bind["_post_id"] = $this->get_post_id();
        $bind["_post_id_nonce"] = $this->get_post_id_nonce();
        
        return apply_filters( "adverts_add_form_bind", $bind );
    }
    
    /**
     * Handles "requires" param
     * 
     * If the [adverts_add] uses "requires" param the function will check if the user
     * has required capability to see the form.
     * 
     * @since   1.4.0
     * @return  mixed   Boolean true || String adverts_flash() content
     */
    protected function _handle_requires_param() {
        
        $params = $this->get_params();
        
        if( ! empty( $params["requires"] ) && ! current_user_can( $params["requires"] ) ) {
            // do nothing ...
        } else {
            return true;
        }

        if( !empty( $params["requires_error"] ) ) {
            $parsed = $params["requires_error"];
        } else {
            $permalink = get_permalink();
            $message = __('Only logged in users can access this page. <a href="%1$s">Login</a> or <a href="%2$s">Register</a>.', "wpadverts");
            $parsed = sprintf($message, wp_login_url( $permalink ), wp_registration_url( $permalink ) );

        }
        
        $adverts_flash = array(
            "info" => array(
                array( 
                    "message" => $parsed,
                    "icon" => "fa-solid fa-lock"
                )
            ),
        );
        return $this->flash( $adverts_flash, "big" );
    }

    public function flash( $data, $layout ) {
        return wpadverts_block_flash( $data, $layout );
    }
}

