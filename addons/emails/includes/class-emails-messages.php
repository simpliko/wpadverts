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
                "label" => __( "[adverts_add] / Free Advert Published", "adverts" ),
                "notify" => "user",
                "from" => array( "name" => "Admin", "email" => "admin@example.com" ),
                "to" => array( "name" => "", "email" => "" ),
                "subject" => __( "Your Ad has been published.", "adverts" ),
                "body" => __("Hello,\nYour Ad titled '{\$advert.post_title}' has been published.\n\nTo view your Ad you can use the link below:\n{\$advert.ID|get_permalink}", 'adverts'),
                "headers" => array(
                    array( "name" => "Reply-To", "value" => "admin@example.com" )
                ),
                "attachments" => array()
            ),
            "core::on_draft_to_pending_notify_user" => array(
                "name" => "core::on_draft_to_pending_notify_user",
                "action" => "advert_tmp_to_pending",
                "enabled" => 1,
                "label" => __( "[adverts_add] / Free Advert Pending", "adverts" ),
                "notify" => "user",
                "from" => array( "name" => "", "email" => "" ),
                "to" => array( "name" => "", "email" => "" ),
                "subject" => __( "Your Ad has been saved.", "adverts" ),
                "body" => "",
                "headers" => array(),
                "attachments" => array()
            ),
            "core::on_pending_to_publish_notify_user" => array(
                "name" => "core::on_pending_to_publish_notify_user",
                "action" => "pending_to_publish",
                "enabled" => 1,
                "label" => __( "wp-admin / Free Advert Approved", "adverts" ),
                "notify" => "user",
                "from" => array( "name" => "", "email" => "" ),
                "to" => array( "name" => "", "email" => "" ),
                "subject" => __( "Your Ad has been approved.", "adverts" ),
                "body" => "",
                "headers" => array(),
                "attachments" => array()
            ),
            "core::on_pending_to_trash_notify_user" => array(
                "name" => "core::on_pending_to_trash_notify_user",
                "action" => "pending_to_trash",
                "enabled" => 1,
                "label" => __( "wp-admin / Free Advert Rejected", "adverts" ),
                "notify" => "user",
                "from" => array( "name" => "", "email" => "" ),
                "to" => array( "name" => "", "email" => "" ),
                "subject" => __( "Your Ad has been rejected.", "adverts" ),
                "body" => "",
                "headers" => array(),
                "attachments" => array()
            ),
            "core::on_publish_to_expired_notify_user" => array(
                "name" => "core::on_publish_to_expired_notify_user",
                "action" => "publish_to_expired",
                "enabled" => 1,
                "label" => __( "Core / Advert Expired", "adverts" ),
                "notify" => "user",
                "from" => array( "name" => "", "email" => "" ),
                "to" => array( "name" => "", "email" => "" ),
                "subject" => __( "Your Ad has expired.", "adverts" ),
                "body" => "",
                "headers" => array(),
                "attachments" => array()
            ),
            "core::on_draft_to_publish_notify_admin" => array(
                "name" => "core::on_draft_to_publish_notify_admin",
                "action" => "advert_tmp_to_publish",
                "enabled" => 1,
                "label" => __( "[adverts_add] / Free Advert Published", "adverts" ),
                "notify" => "admin",
                "from" => array( "name" => "", "email" => "" ),
                "to" => array( "name" => "", "email" => "" ),
                "subject" => __( "New Ad has been published.", "adverts" ),
                "body" => "",
                "headers" => array(),
                "attachments" => array()
            ),
            "core::on_draft_to_pending_notify_admin" => array(
                "name" => "core::on_draft_to_pending_notify_admin",
                "action" => "advert_tmp_to_pending",
                "enabled" => 0,
                "label" => __( "[adverts_add] / Free Advert Pending", "adverts" ),
                "notify" => "admin",
                "from" => array( "name" => "", "email" => "" ),
                "to" => array( "name" => "", "email" => "" ),
                "subject" => __( "New Ad is pending (action required).", "adverts" ),
                "body" => "",
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
            if( in_array( strtolower( $key ), $exclude ) ) {
                continue;
            }
            
            $headers[ $key ] = $value;
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
            "to" => $message["to"]["value"],
            "subject" => $message["subject"],
            "message" => $message["body"],
            "headers" => $this->_get_headers( $message ),
            "attachments" => $message["headers"]
        );
        
        $args = apply_filters( "wpadverts_message_args", $args, $message, $mail_args );
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
    
    public function on_draft_to_publish_notify_user( $post ) {
        
        if( $post->post_type !== "advert" ) {
            return;
        }
        
        return $this->send_message( "core::on_draft_to_publish_notify_user", $post );
    }
    
    public function on_draft_to_pending_notify_user( $post ) {
        if( $post->post_type !== "advert" ) {
            return;
        }
        
        $to = $this->_get_to( $post->ID );
        $subject = "Your Ad has been saved.";
        $message = array();
        $message[] = "Hello,";
        $message[] = sprintf( "Your Ad titled '%s' has been saved and is pending moderation.", $post->post_title );
        $message[] = "Once the administrator will approve or reject your Ad you will be notified by email.";

        $mail_args = array(
            "to" => $to,
            "subject" => $subject,
            "message" => join( "\r\n", $message ),
            "headers" => "",
            "attachments" => array()
        );
        
        $mail = apply_filters( "wpadverts_message", $mail_args, __METHOD__, $post );
        
        wp_mail( $mail["to"], $mail["subject"], $mail["message"], $mail["headers"], $mail["attachments"] );
    }
    
    public function on_pending_to_publish_notify_user( $post ) {
        if( $post->post_type !== "advert" ) {
            return;
        }
        $to = $this->_get_to( $post->ID );
        $subject = "Your Ad has been approved.";
        $message = array();
        $message[] = "Hello,";
        $message[] = sprintf( "Your Ad titled '%s' has been approved.", $post->post_title );
        $message[] = "";
        $message[] = sprintf( "To view your Ad you can use the link below:" );
        $message[] = get_permalink( $post->ID );

        $mail_args = array(
            "to" => $to,
            "subject" => $subject,
            "message" => join( "\r\n", $message ),
            "headers" => "",
            "attachments" => array()
        );
        
        $mail = apply_filters( "wpadverts_message", $mail_args, __METHOD__, $post );
        
        wp_mail( $mail["to"], $mail["subject"], $mail["message"], $mail["headers"], $mail["attachments"] );
    }
    
    public function on_pending_to_trash_notify_user( $post ) {
        if( $post->post_type !== "advert" ) {
            return;
        }
        $to = $this->_get_to( $post->ID );
        $subject = "Your Ad has been rejected.";
        $message = array();
        $message[] = "Hello,";
        $message[] = sprintf( "we are sorry, but your Ad titled '%s' has been rejected.", $post->post_title );

        $mail_args = array(
            "to" => $to,
            "subject" => $subject,
            "message" => join( "\r\n", $message ),
            "headers" => "",
            "attachments" => array()
        );
        
        $mail = apply_filters( "wpadverts_message", $mail_args, __METHOD__, $post );
        
        wp_mail( $mail["to"], $mail["subject"], $mail["message"], $mail["headers"], $mail["attachments"] );
    }

    public function on_publish_to_expired_notify_user( $post ) {
        if( $post->post_type !== "advert" ) {
            return;
        }
        $to = $this->_get_to( $post->ID );
        $subject = "Your Ad has expired.";
        $message = array();
        $message[] = "Hello,";
        $message[] = sprintf( "your Ad titled '%s' has expired and is no longer available on site.", $post->post_title );

        $mail_args = array(
            "to" => $to,
            "subject" => $subject,
            "message" => join( "\r\n", $message ),
            "headers" => "",
            "attachments" => array()
        );
        
        $mail = apply_filters( "wpadverts_message", $mail_args, __METHOD__, $post );
        
        wp_mail( $mail["to"], $mail["subject"], $mail["message"], $mail["headers"], $mail["attachments"] );
    }
    
    public function on_draft_to_publish_notify_admin( $post ) {
        if( $post->post_type !== "advert" ) {
            return;
        }
        $to = get_option( 'admin_email ');
        $subject = "New Ad has been published.";
        $message = array();
        $message[] = "Hello,";
        $message[] = sprintf( "New Ad titled '%s' has been published.", $post->post_title );
        $message[] = "";
        $message[] = sprintf( "You can view the Ad here:" );
        $message[] = get_permalink( $post->ID );
        $message[] = "";
        $message[] = sprintf( "You can edit the Ad here:" );
        $message[] = admin_url( sprintf( 'post.php?post=%d&action=edit', $post->ID ) );

        $mail_args = array(
            "to" => $to,
            "subject" => $subject,
            "message" => join( "\r\n", $message ),
            "headers" => "",
            "attachments" => array()
        );
        
        $mail = apply_filters( "wpadverts_message", $mail_args, __METHOD__, $post );
        
        wp_mail( $mail["to"], $mail["subject"], $mail["message"], $mail["headers"], $mail["attachments"] );
    }
    
    public function on_draft_to_pending_notify_admin( $post ) {
        if( $post->post_type !== "advert" ) {
            return;
        }
        $to = get_option( 'admin_email ');
        $subject = "New Ad is pending (action required).";
        $message = array();
        $message[] = "Hello,";
        $message[] = sprintf( "New Ad titled '%s' has been saved and is pending moderation.", $post->post_title );
        $message[] = "";
        $message[] = sprintf( "You can edit the Ad here:" );
        $message[] = admin_url( sprintf( 'post.php?post=%d&action=edit', $post->ID ) );
        $message[] = "";
        $message[] = "Please either publish or trash the Ad.";

        $mail_args = array(
            "to" => $to,
            "subject" => $subject,
            "message" => join( "\r\n", $message ),
            "headers" => "",
            "attachments" => array()
        );
        
        $mail = apply_filters( "wpadverts_message", $mail_args, __METHOD__, $post );
        
        wp_mail( $mail["to"], $mail["subject"], $mail["message"], $mail["headers"], $mail["attachments"] );
    }

}

