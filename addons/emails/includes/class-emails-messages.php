<?php
/**
 * Emails Module - Messages Class
 * 
 * This class registers emails that can be sent by WPAdverts and edited from 
 * the wp-admin / Classifieids / Options / Emails panel
 * 
 * @author Grzegorz Winiarski
 * @since 1.3.0
 * @package Adverts
 * @subpackage Emails
 * 
 * The message keys are generated using the following scheme
 * 
 * Adext_Emails_Messages -> WPAdverts Messages
 * Adverts_Payment_Messages
 * Adverts_Authos_Messages -> WPAdverts Author Messages
 * Adverts_Wc_Messages
 * 
 * Free Advert Published -> Notify User
 * add_action( "advert_tmp_to_publish", array( $this, "on_draft_to_publish_notify_user" ), 10, 1 );
 * Free Advert Pending -> Notify User
 * add_action( "advert_tmp_to_pending", array( $this, "on_draft_to_pending_notify_user" ), 10, 1 );
 * Free Advert Approved -> Notify User
 * add_action( "pending_to_publish", array( $this, "on_pending_to_publish_notify_user" ), 10, 1 );

 * Free Advert Rejected -> Notify User
 * add_action( "pending_to_trash", array( $this, "on_pending_to_trash_notify_user" ), 10, 1 );
 * Advert Expired -> Notify User
 * add_action( "publish_to_expired", array( $this, "on_publish_to_expired_notify_user" ), 10, 1 );

 * Free Advert Published -> Notify Admin
 * add_action( "advert_tmp_to_publish", array( $this, "on_draft_to_publish_notify_admin" ), 10, 1 );
 * Free Advert Pending -> Notify Admin
 * add_action( "advert_tmp_to_pending", array( $this, "on_draft_to_pending_notify_admin" ), 10, 1 );
 * 
 */

class Adext_Emails_Messages {

    /**
     * List of registered messages
     *
     * @var array 
     */
    public $messages = null;
    
    /**
     * Class Constructor
     * 
     * @since 1.3.0
     * @return void
     */
    public function __construct() {

    }
    
    /**
     * Registers Actions and Filters
     * 
     * This action registers actions (usually post status transitions) and filters
     * which will be sending the emails.
     * 
     * @since 1.3.0
     * @return void
     */
    public function register_actions() {
        
        foreach( $this->messages as $message ) {
            
            if( ! isset( $message["callback"] ) ) {
                $message["callback"] = array( $this, "post_status_transition" );
            }
            
            $callback = $message["callback"];
            $action = $message["action"];
            
            $func = $message["callback"];
            $priority = 10;
            $args = 1;
            $filter = "action";
            
            if( isset( $message['enabled'] ) && $message['enabled'] == 0 ) {
                continue;
            }

            if( is_array( $callback ) && array_key_exists( "function", $callback ) ) {
                $func = $callback["function"];
            }
            if( isset( $callback["priority"] ) ) {
                $priority = $callback["priority"];
            }
            if( isset( $callback["args"] ) ) {
                $args = $callback["args"];
            }
            
            if( $filter === "action" ) {
                add_action( $action, $func, $priority, $args );
            } else if( $filter === "filter" ) {
                add_filter( $action, $func, $priority, $args );
            } else {
                // ??
            }
        }
    }
    
    /**
     * Registers Messages
     * 
     * This function registers messages which WPAdverts can send. 
     * 
     * By default there are few messages sent by WPAdverts Core registered but it
     * is possible to register additional messages using wpadverts_messages_register filter.
     * 
     * @see wpadverts_messages_register filter
     * 
     * @since 1.3.0
     * @return void
     */
    public function register_messages() {

        $this->messages = apply_filters( "wpadverts_messages_register", array(
            "core::on_draft_to_publish_notify_user" => array(
                "name" => "core::on_draft_to_publish_notify_user",
                "action" => "advert_tmp_to_publish",
                "callback" => array( $this, "on_draft_to_publish_notify_user" ),
                "enabled" => 1,
                "help" => "https://wpadverts.com/documentation/emails/#core-on_draft_to_publish_notify_user",
                "notify" => "user",
                "from" => array( "name" => "", "email" => "" ),
                "to" => "{\$advert.ID|meta:adverts_email}",
                "subject" => __( "Your Ad has been published.", "wpadverts" ),
                "body" => __("Hello,\nyour Ad titled '{\$advert.post_title}' has been published.\n\nTo view your Ad you can use the link below:\n{\$advert.ID|get_permalink}", "wpadverts"),
                "headers" => array(),
                "attachments" => array()
            ),
            "core::on_draft_to_pending_notify_user" => array(
                "name" => "core::on_draft_to_pending_notify_user",
                "action" => "advert_tmp_to_pending",
                "callback" => array( $this, "on_draft_to_pending_notify_user" ),
                "enabled" => 1,
                "help" => "https://wpadverts.com/documentation/emails/#core-on_draft_to_pending_notify_user",
                "notify" => "user",
                "from" => array( "name" => "", "email" => "" ),
                "to" => "{\$advert.ID|meta:adverts_email}",
                "subject" => __( "Your Ad has been saved (pending moderation).", "wpadverts" ),
                "body" => __( "Hello,\nyour Ad titled '{\$advert.post_title}' has been saved and is pending moderation.\n\nOnce the administrator will approve or reject your Ad you will be notified by email.", "wpadverts" ),
                "headers" => array(),
                "attachments" => array()
            ),
            "core::on_pending_to_publish_notify_user" => array(
                "name" => "core::on_pending_to_publish_notify_user",
                "action" => "pending_to_publish",
                "callback" => array( $this, "on_pending_to_publish_notify_user" ),
                "enabled" => 1,
                "help" => "https://wpadverts.com/documentation/emails/#core-on_pending_to_publish_notify_user",
                "notify" => "user",
                "from" => array( "name" => "", "email" => "" ),
                "to" => "{\$advert.ID|meta:adverts_email}",
                "subject" => __( "Your Ad has been approved by administrator.", "wpadverts" ),
                "body" => __( "Hello,\nyour Ad titled '{\$advert.post_title} has been approved.\n\nTo view your Ad you can use the link below:\n{\$advert.ID|get_permalink}", "wpadverts"),
                "headers" => array(),
                "attachments" => array()
            ),
            "core::on_pending_to_trash_notify_user" => array(
                "name" => "core::on_pending_to_trash_notify_user",
                "action" => "pending_to_trash",
                "callback" => array( $this, "on_pending_to_trash_notify_user" ),
                "enabled" => 1,
                "help" => "https://wpadverts.com/documentation/emails/#core-on_pending_to_trash_notify_user",
                "notify" => "user",
                "from" => array( "name" => "", "email" => "" ),
                "to" => "{\$advert.ID|meta:adverts_email}",
                "subject" => __( "Your Ad has been rejected by administrator.", "wpadverts" ),
                "body" => __( "Hello,\nwe are sorry to inform you that your Ad titled '{\$advert.post_title}' has been rejected by the administrator and will not be published.", "wpadverts" ),
                "headers" => array(),
                "attachments" => array()
            ),
            "core::on_publish_to_expired_notify_user" => array(
                "name" => "core::on_publish_to_expired_notify_user",
                "action" => "publish_to_expired",
                "callback" => array( $this, "on_publish_to_expired_notify_user" ),
                "enabled" => 1,
                "help" => "https://wpadverts.com/documentation/emails/#core-on_publish_to_expired_notify_user",
                "notify" => "user",
                "from" => array( "name" => "", "email" => "" ),
                "to" => "{\$advert.ID|meta:adverts_email}",
                "subject" => __( "Your Ad has expired.", "wpadverts" ),
                "body" => __( "Hello,\nyour Ad titled '{\$advert.post_title}' has expired and is no longer available on site.", "wpadverts" ),
                "headers" => array(),
                "attachments" => array()
            ),
            "core::on_draft_to_publish_notify_admin" => array(
                "name" => "core::on_draft_to_publish_notify_admin",
                "action" => "advert_tmp_to_publish",
                "callback" => array( $this, "on_draft_to_publish_notify_admin" ),
                "enabled" => 1,
                "help" => "https://wpadverts.com/documentation/emails/#core-on_draft_to_publish_notify_admin",
                "notify" => "admin",
                "from" => array( "name" => "", "email" => "" ),
                "to" => "{\$admin_email}",
                "subject" => __( "New Ad has been published.", "wpadverts" ),
                "body" => __( "Hello,\nnew Ad titled '{\$advert.post_title}' has been published.\n\nYou can view the Ad here:\n{\$advert.ID|get_permalink}\n\nYou can edit the Ad here:\n{\$advert|admin_edit_url}", "wpadverts" ),
                "headers" => array(),
                "attachments" => array()
            ),
            "core::on_draft_to_pending_notify_admin" => array(
                "name" => "core::on_draft_to_pending_notify_admin",
                "action" => "advert_tmp_to_pending",
                "callback" => array( $this, "on_draft_to_pending_notify_admin" ),
                "enabled" => 1,
                "help" => "https://wpadverts.com/documentation/emails/#core-on_draft_to_pending_notify_admin",
                "notify" => "admin",
                "from" => array( "name" => "", "email" => "" ),
                "to" => "{\$admin_email}",
                "subject" => __( "New Ad is pending (action required).", "wpadverts" ),
                "body" => __( "Hello,\nNew Ad titled '{\$advert.post_title}' has been saved and is pending moderation.\n\nYou can edit the Ad here:\n{\$advert|admin_edit_url}\n\nPlease either Publish or Trash it then the owner will be notified.", "wpadverts" ),
                "headers" => array(),
                "attachments" => array()
            ),
        ) );
    }
    
    /**
     * Returns list of registered messages
     * 
     * @since 1.3.0
     * @return  array    List of registered messages
     */
    public function get_messages() {
        return $this->messages;
    }
    
    /**
     * Returns to address
     * 
     * @deprecated      1.3.0
     * @param  int       $post_id
     * @return string
     */
    private function _get_to( $post_id ) {
        _deprecated_function(__METHOD__, "1.3.0");
        
        $author = get_post_field( 'post_author', $post_id );

        $to = get_post_meta( $post_id, "adverts_email", true );
        if( !$to && $author ) {
            $to = get_the_author_meta('user_email');
        }

        return $to;
    }

    /**
     * Returns parsed list of headers
     * 
     * The headers are in an associative array 
     * <code>array( "From" => "test", "Reply-To" => "test" )</code>
     * 
     * @since       1.3.0
     * @param       array    $message   Message
     * @param       array    $exclude   List of header names (lowercase) to exclude
     * @return      array               Parsed list of headers
     */
    private function _get_headers( $message, $exclude = null ) {
        
        if( $exclude === null ) {
            $exclude = array( "from", "subject" );
        } else if( is_string( $exclude ) ) {
            $exclude = array_map( "trim", explode( ",", $exclude ) );
        } else if( ! is_array( $exclude ) ) {
            $exclude = (array)$exclude;
        }
        
        $exclude = array_map( "strtolower", $exclude );
        $fr = $message["from"];
        
        $headers = array(
            
            "From" => empty( $fr["name"] ) ? $fr["email"] : sprintf( "%s <%s>", $fr["name"], $fr["email"] ),  
        );
        
        foreach( $message["headers"] as $key => $value ) {
            if( in_array( strtolower( $value["name"] ), $exclude ) ) {
                continue;
            }
            
            $headers[ $value["name"] ] = $value["value"];
        }
        
        return $headers;
    }
    
    /**
     * Loads messages config from the database
     * 
     * The data from the database is merged with the messages registered in
     * self::register_messages() function.
     * 
     * This function is executed using wpadverts_messages_register filter registered
     * in Adext_Emails::__construct()
     * 
     * @see Adext_Emails::__construct()
     * @see wpadverts_messages_register filter
     * 
     * @since   1.3.0
     * @param   array   $messages   List of messages
     * @return  array
     */
    public function load( $messages ) {
        $templates = get_option( "adext_emails_templates" );
        
        if( ! is_array( $templates ) ) {
            return $messages;
        }
        
        foreach( $templates as $k => $update ) {
            if( isset( $messages[$k] ) ) {
                $messages[$k] = array_merge( $messages[$k], $update );
            }
        }
        
        return $messages;
    }
    
    /**
     * Sends a $message
     * 
     * This function parses email variables and the message and then sends
     * an email using wp_mail() function.
     * 
     * @see wpadverts_message_args filter
     * @see wpadverts_message filter
     * 
     * @since   1.3.0
     * @param   array     $message      One of registered messages to be sent
     * @param   array     $args         Arguments passed tothe message as variables
     * @return  void
     */
    public function send( $message, $args ) {
        
        $mail_args = array(
            "to" => $message["to"],
            "subject" => $message["subject"],
            "message" => $message["body"],
            "headers" => $this->_get_headers( $message ),
            "attachments" => $message["attachments"]
        );
        
        $args = apply_filters( "wpadverts_message_args", $args, $message, $mail_args );
        
        // by default Adext_Emails_Parser::compile() function is run
        $mail = apply_filters( "wpadverts_message", $mail_args, $message, $args );
        
        $headers = array();
        foreach( $mail["headers"] as $k => $v ) {
            $headers[] = sprintf( "%s: %s", $k, $v );
        }

        wp_mail( $mail["to"], $mail["subject"], $mail["message"], $headers, $mail["attachments"] );
    }
    
    /**
     * Calls self::send() function
     * 
     * @param   string  $message_key    Message key
     * @param   array   $args           Arguments passed tothe message as variables
     * @return  void
     */
    public function send_message( $message_key, $args ) {
        return $this->send( $this->messages[ $message_key ], $args );
    }
    
    /**
     * [adverts_add] / Free Advert Published
     * 
     * Variables
     * - $advert => WP_Post
     * 
     * @since   1.3.0
     * @param   WP_Post     $post
     * @return  void
     */
    public function on_draft_to_publish_notify_user( $post ) {
        
        if( $post->post_type !== "advert" ) {
            return;
        }
        
        return $this->send_message( "core::on_draft_to_publish_notify_user", array( 
            "advert" => $post,
            "advert_files" => adverts_get_post_files( $post )
        ) );
    }
    
    /**
     * [adverts_add] / Free Advert Pending
     * 
     * Variables
     * - $advert => WP_Post
     * 
     * @since   1.3.0
     * @param   WP_Post     $post
     * @return  void
     */
    public function on_draft_to_pending_notify_user( $post ) {

        if( $post->post_type !== "advert" ) {
            return;
        }
        
        return $this->send_message( "core::on_draft_to_pending_notify_user", array( 
            "advert" => $post,
            "advert_files" => adverts_get_post_files( $post )
        ) );
    }
    
    /**
     * wp-admin / Free Advert Approved
     * 
     * Variables
     * - $advert => WP_Post
     * 
     * @since   1.3.0
     * @param   WP_Post     $post
     * @return  void
     */
    public function on_pending_to_publish_notify_user( $post ) {
        
        if( $post->post_type !== "advert" ) {
            return;
        }
        
        return $this->send_message( "core::on_pending_to_publish_notify_user", array( 
            "advert" => $post,
            "advert_files" => adverts_get_post_files( $post )
        ) );
    }
    
    /**
     * wp-admin / Free Advert Rejected
     * 
     * Variables
     * - $advert => WP_Post
     * 
     * @since   1.3.0
     * @param   WP_Post     $post
     * @return  void
     */
    public function on_pending_to_trash_notify_user( $post ) {
        
        if( $post->post_type !== "advert" ) {
            return;
        }
        
        return $this->send_message( "core::on_pending_to_trash_notify_user", array( 
            "advert" => $post,
            "advert_files" => adverts_get_post_files( $post )
        ) );
    }

    /**
     * Core / Advert Expired
     * 
     * Variables
     * - $advert            => WP_Post
     * 
     * @since   1.3.0
     * @param   WP_Post     $post
     * @return  void
     */
    public function on_publish_to_expired_notify_user( $post ) {
        
        if( $post->post_type !== "advert" ) {
            return;
        }
        
        return $this->send_message( "core::on_publish_to_expired_notify_user", array( 
            "advert" => $post,
            "advert_files" => adverts_get_post_files( $post )
        ) );
    }
    
    /**
     * [adverts_add] / Free Advert Published -> Notify Admin
     * 
     * Variables
     * - $advert            => WP_Post
     * - $admin_edit_url    => string           Post edit URL in wp-admin
     * 
     * @since   1.3.0
     * @param   WP_Post     $post
     * @return  void
     */
    public function on_draft_to_publish_notify_admin( $post ) {

        if( $post->post_type !== "advert" ) {
            return;
        }
        
        return $this->send_message( "core::on_draft_to_publish_notify_admin", array( 
            "advert" => $post,
            "advert_files" => adverts_get_post_files( $post ),
            "admin_email" => Adext_Emails::admin_email(),
            "admin_edit_url" => admin_url( sprintf( 'post.php?post=%d&action=edit', $post->ID ) )
        ) );
    }
    
    /**
     * [adverts_add] / Free Advert Pending -> Notify Admin
     * 
     * Variables
     * - $advert            => WP_Post
     * - $admin_edit_url    => string           Post edit URL in wp-admin
     * 
     * @since   1.3.0
     * @param   WP_Post     $post
     * @return  void
     */
    public function on_draft_to_pending_notify_admin( $post ) {

        if( $post->post_type !== "advert" ) {
            return;
        }
        
        return $this->send_message( "core::on_draft_to_pending_notify_admin", array( 
            "advert" => $post,
            "advert_files" => adverts_get_post_files( $post ),
            "admin_email" => Adext_Emails::admin_email(),
            "admin_edit_url" => admin_url( sprintf( 'post.php?post=%d&action=edit', $post->ID ) )
        ) );
    }

}

