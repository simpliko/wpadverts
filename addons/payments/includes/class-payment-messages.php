<?php

class Adverts_Payment_Messages {
    
    public function __construct() {
        
        // Payment Started (ad saved) -> Notify User
        add_action( "advert_tmp_to_advert-pending", array( $this, "on_advert_pending_notify_user" ), 10, 1 );
        // Payment Started (payment instructions) -> Notify User
        add_action( "adverts_payment_new_to_pending", array( $this, "on_payment_pending_notify_user" ), 10, 1 );
        // Payment Completed -> Notify User
        add_action( "adverts_payment_pending_to_completed", array( $this, "on_payment_completed_notify_user" ) );
 
        // Payment Started -> Notify Admin
        add_action( "advert_tmp_to_advert-pending", array( $this, "on_payment_pending_notify_admin" ), 10, 1 );
        // Payment Completed -> Notify Admin
        add_action( "adverts_payment_pending_to_completed", array( $this, "on_payment_completed_notify_admin" ), 10, 1 );
        
        // Paid Publish -> Notify User
        add_action( "advert-pending_to_publish", array( $this, "on_paid_pending_to_publish_notify_user" ), 10, 1 );
        // Paid Publish -> Notify Admin
        add_action( "advert-pending_to_publish", array( $this, "on_paid_pending_to_publish_notify_admin" ), 10, 1 );
        
        // Advert Renewal To Publish -> Notify User
        add_action( "expired_to_publish", array( $this, "on_expired_to_publish_notify_user"), 10, 1 );
        // Advert Renewal to Pending -> Notify User
        add_action( "expired_to_pending", array( $this, "on_expired_to_pending_notify_user"), 10, 1 );
        
        // Advert Renewal To Publish -> Notify Admin
        add_action( "expired_to_publish", array( $this, "on_expired_to_publish_notify_admin"), 10, 1 );
        // Advert Renewal to Pending -> Notify Admin
        add_action( "expired_to_pending", array( $this, "on_expired_to_pending_notify_admin"), 10, 1 );
    }
    
    protected function _get_to( $post_id ) {
        $author = get_post_field( 'post_author', $post_id );

        $to = get_post_meta( $post_id, "adverts_email", true );
        if( !$to && $author ) {
            $to = get_the_author_meta('user_email');
        }

        return $to;
    }
    
    public function on_advert_pending_notify_user( $post ) {
        if( $post->post_type !== "advert" ) {
            return;
        }
        
        $to = $this->_get_to( $post->ID );
        $subject = "Your Ad has been saved.";
        $message = array();
        $message[] = "Hello,";
        $message[] = sprintf( "Your Ad titled '%s' has been saved and is pending payment.", $post->post_title );
        $message[] = "";
        $message[] = "You will be notified by email how to proccess the payment.";

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
    
    public function on_payment_pending_notify_user( $payment ) {
        $post = get_post( get_post_meta( $payment->ID, '_adverts_object_id', true ) );

        $to = $this->_get_to( $post->ID );
        $subject = "Payment pending.";
        $message = array();
        $message[] = "Hello,";
        $message[] = sprintf( "your Ad titled '%s' is pending payment.", $post->post_title );
        $message[] = "";
        $message[] = "If you were not able to finish the payment you can do that using the link below.";
        $message[] = "@TODO: payment complete link here ...";

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
    
    public function on_payment_completed_notify_user( $payment ) {
        $post = get_post( get_post_meta( $payment->ID, '_adverts_object_id', true ) );

        $to = $this->_get_to( $post->ID );
        $subject = "Payment completed.";
        $message = array();
        $message[] = "Hello,";
        $message[] = sprintf( "your payment for Ad '%s' has been completed.", $post->post_title );
        $message[] = "";

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
    
    public function on_paid_pending_to_publish_notify_user( $post ) {
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
    
    public function on_paid_pending_to_publish_notify_admin( $post ) {
        if( $post->post_type !== "advert" ) {
            return;
        }
        $to = $this->_get_to( $post->ID );
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
    
    public function on_payment_pending_notify_admin( $post ) {
        $listing_id = get_post_meta( $post->ID, "payments_listing_type", true );
        $listing = get_post( $listing_id );

        $to = get_option( 'admin_email ');
        $subject = "New Ad has been posted (pending payment).";
        $message = array();
        $message[] = "Hello,";
        $message[] = sprintf( "new Ad titled '%s' has been posted and is pending payment.", $post->post_title );
        $message[] = "";
        $message[] = "You will be notified again when the payment will be proccessed.";
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
    
    public function on_payment_completed_notify_admin( $payment ) {
        $post = get_post( get_post_meta( $payment->ID, '_adverts_object_id', true ) );

        $to = get_option( 'admin_email' );
        $subject = "Payment completed.";
        $message = array();
        $message[] = "Hello,";
        $message[] = sprintf( "the payment for Ad '%s' has been completed.", $post->post_title );
        $message[] = "";
        $message[] = "You can view the payment details here:";
        $message[] = admin_url( 'edit.php?post_type=advert&page=adext-payment-history&edit=%s', $payment->ID );

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
    
    public function on_expired_to_publish_notify_user( $post ) {
        if( $post->post_type !== "advert" ) {
            return;
        }
        
        $to = $this->_get_to( $post->ID );
        $subject = "Your Ad has been renewed.";
        $message = array();
        $message[] = "Hello,";
        $message[] = sprintf( "Your Ad titled '%s' has been renewed and is visible on site again.", $post->post_title );
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
    
    public function on_expired_to_pending_notify_user( $post ) {
        if( $post->post_type !== "advert" ) {
            return;
        }
        
        $to = $this->_get_to( $post->ID );
        $subject = "Your Ad has been renewed and is panding approval.";
        $message = array();
        $message[] = "Hello,";
        $message[] = sprintf( "Your Ad titled '%s' has been renewed and is pending moderation.", $post->post_title );
        $message[] = "";
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
    
    public function on_expired_to_publish_notify_admin( $post ) {
        if( $post->post_type !== "advert" ) {
            return;
        }
        
        $to = get_option( "admin_email" );
        $subject = "Ad has been renewed.";
        $message = array();
        $message[] = "Hello,";
        $message[] = sprintf( "Ad titled '%s' has been renewed and published.", $post->post_title );
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
    
    public function on_expired_to_pending_notify_admin( $post ) {
        if( $post->post_type !== "advert" ) {
            return;
        }
        
        $to = get_option( "admin_email" );
        $subject = "Ad has been renewed (action required).";
        $message = array();
        $message[] = "Hello,";
        $message[] = sprintf( "Ad titled '%s' has been renewed and is pending approval.", $post->post_title );
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
}















