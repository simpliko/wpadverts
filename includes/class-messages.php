<?php

/**
 * The message keys are generated using the following scheme
 * 
 * Adverts_Messages -> WPAdverts Messages
 * Adverts_Payment_Messages
 * Adverts_Authos_Messages -> WPAdverts Author Messages
 * Adverts_Wc_Messages
 * 
 * 
 */

class Adverts_Messages {

    public function __construct() {
        
        // Free Advert Published -> Notify User
        add_action( "advert_tmp_to_publish", array( $this, "on_draft_to_publish_notify_user" ), 10, 1 );
        // Free Advert Pending -> Notify User
        add_action( "advert_tmp_to_pending", array( $this, "on_draft_to_pending_notify_user" ), 10, 1 );
        // Free Advert Approved -> Notify User
        add_action( "pending_to_publish", array( $this, "on_pending_to_publish_notify_user" ), 10, 1 );
        
        // Free Advert Rejected -> Notify User
        add_action( "pending_to_trash", array( $this, "on_pending_to_trash_notify_user" ), 10, 1 );
        // Advert Expired -> Notify User
        add_action( "publish_to_expired", array( $this, "on_publish_to_expired_notify_user" ), 10, 1 );
        
        // Free Advert Published -> Notify Admin
        add_action( "advert_tmp_to_publish", array( $this, "on_draft_to_publish_notify_admin" ), 10, 1 );
        // Free Advert Pending -> Notify Admin
        add_action( "advert_tmp_to_pending", array( $this, "on_draft_to_pending_notify_admin" ), 10, 1 );
    }
    
    protected function _get_to( $post_id ) {
        $author = get_post_field( 'post_author', $post_id );

        $to = get_post_meta( $post_id, "adverts_email", true );
        if( !$to && $author ) {
            $to = get_the_author_meta('user_email');
        }

        return $to;
    }

    public function on_draft_to_publish_notify_user( $post ) {
        if( $post->post_type !== "advert" ) {
            return;
        }
        $to = $this->_get_to( $post->ID );
        $subject = "Your Ad has been published.";
        $message = array();
        $message[] = "Hello,";
        $message[] = sprintf( "Your Ad titled '%s' has been published.", $post->post_title );
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

