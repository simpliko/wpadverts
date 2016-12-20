<?php
/**
 * Displays Payments History Edit Page
 * 
 * This file is a template for wp-admin / Classifieds / Payment History / Edit panel. 
 * 
 * It is being loaded by adext_payments_page_history function.
 * 
 * @see adext_payments_page_history()
 * @since 0.1
 */
?>
<div class="wrap">
    
    <h2>
        <?php _e("Payment History Edit", "adverts") ?>
    </h2>

    <?php adverts_admin_flash() ?>
    
    <form action="" method="post" class="adverts-form">
    <div id="poststuff" class=" ">
    <div id="post-body" class="metabox-holder columns-2">
    
    <div id="postbox-container-1" class="postbox-container">
        <div id="side-sortables" class="meta-box-sortables ui-sortable">
            <div id="" class="postbox adverts-ph-edit-sidebar">
                <h3 class="hndle"><span><?php _e("Payment", "adverts") ?></span></h3>
                    <div class="inside">
                        <div class="">
                            <div class="">
                                <p>
                                    <span class="label"><strong><?php _e("Gateway", "adverts") ?>:</strong></span>
                                    <span>
                                        <?php if($gateway): ?>
                                        <?php esc_html_e($gateway["title"]) ?>
                                        <?php else: ?>
                                         —
                                        <?php endif; ?>
                                    </span>
                                </p>
                                
                                <p>
                                    <span class="label"><strong><?php _e("Created", "adverts") ?>:</strong></span>
                                    <span>
                                        <?php echo date_i18n( get_option( 'date_format' ), get_post_time( 'U', false, $payment->ID ) )  ?>
                                    </span>
                                </p>

                                <p>
                                    <span class="label"><strong><?php _e("User IP", "adverts") ?>:</strong></span>
                                    <span>
                                        <?php esc_html_e( get_post_meta( $payment->ID, "_adverts_user_ip", true ) ) ?>
                                    </span>
                                </p>
                            </div>

                            <div class="">
                                <p>
                                    <span class="label"><strong><?php _e("Status", "adverts") ?>:</strong></span>&nbsp;
                                    <select name="post_status" class="medium-text">
                                        <?php foreach(array("pending", "completed", "failed", "refunded") as $status): ?>
                                        <?php $status_object = get_post_status_object($status) ?>
                                        <option value="<?php esc_attr_e($status) ?>" <?php selected($status, $payment->post_status) ?>><?php esc_html_e($status_object->label) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </p>
                            </div>
                            
                            <div class="">
                                <p>
                                    <span class="label"><strong><?php _e("To Pay", "adverts") ?>:</strong></span>&nbsp;
                                    <input type="text" class="adverts-payment-price" name="_adverts_payment_total" value="<?php esc_attr_e( get_post_meta( $payment->ID, "_adverts_payment_total", true ) ) ?>" />
                                </p>
                            </div>
                            
                            <div class="">
                                <p>
                                    <span class="label"><strong><?php _e("Paid", "adverts") ?>:</strong></span>&nbsp;
                                    <input type="text" class="adverts-payment-price" name="_adverts_payment_paid" value="<?php esc_attr_e( get_post_meta( $payment->ID, "_adverts_payment_paid", true ) ) ?>" />
                                </p>
                            </div>

                        </div><!-- /.column-container -->
                    </div><!-- /.inside -->

                    <div class="">
                        <div id="major-publishing-actions">
                            <div id="publishing-action">
                                <input type="submit" class="button button-primary right" value="<?php _e("Update Payment", "adverts") ?>" />
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>

            </div>
        </div><!-- /#side-sortables -->
    </div>
        
    <?php $listing_id = get_post_meta( $payment->ID, "_adverts_pricing_id", true ); ?>
    <?php $listing = get_post( $listing_id ) ?>

    <div id="postbox-container-2" class="postbox-container">
    

        <div id="" class="postbox">
            <h3 class="hndle">
                <span><?php _e("Purchase Details", "adverts") ?></span>
            </h3>
            <div class="inside " style="font-size:1.1em; clear:both; overflow:hidden">
                
                <div class="column" style="width:33%; float:left">
                    <strong><?php _e("Purchase Type", "adverts") ?></strong><br/>
                    <span>
                        <?php if( $listing->post_type == "adverts-pricing" ): ?>
                        <?php _e( "Posting", "adverts" ) ?>
                        <?php elseif( $listing->post_type == "adverts-renewal" ): ?>
                        <?php _e( "Renewal", "adverts" ) ?>
                        <?php else: ?>
                        -
                        <?php endif; ?>
                    </span>
                </div>
                
                <div class="column" style="width:33%; float: left">
                    <strong><?php _e("Listing Type", "adverts") ?></strong><br/>
                    <?php if($listing): ?>
                    <span>
                        <a href="<?php echo admin_url('edit.php?post_type=advert&page=adverts-extensions&module=payments&adaction=list&edit='.$listing->ID) ?>"><?php esc_html_e($listing->post_title) ?></a> 
                        <?php echo adverts_price( get_post_meta( $listing->ID, "adverts_price", true ) ) ?>
                    </span>
                    <?php else: ?>
                    <?php esc_html_e( sprintf( __("Listing [%d] no longer exists.", "adverts"), $listing_id ) ) ?>
                    <?php endif; ?>
                </div>
                
                <div class="column" style="width:33%; float: left">
                    <strong><?php _e("Purchased Item", "adverts") ?></strong><br/>
                    <?php $post_id = get_post_meta( $payment->ID, "_adverts_object_id", true ); ?>
                    <?php $post = get_post( $post_id ) ?>
                    <?php if($post): ?>
                    <span>
                        <a href="<?php echo admin_url('post.php?post='.$post->ID.'&action=edit') ?>"><?php esc_html_e($post->post_title) ?></a>
                    </span>
                    <?php else: ?>
                    <?php esc_html_e( sprintf( __("Ad [%d] no longer exists.", "adverts"), $listing_id ) ) ?>
                    <?php endif; ?>
                </div>
                
            </div><!-- /.inside -->
        </div>
        
        
        <div id="" class="postbox">
            <h3 class="hndle">
                <span><?php _e("Customer Details", "adverts") ?></span>
            </h3>
            <div class="inside ">

                
                    <table class="form-table">
                        <tbody>
                        <?php echo adverts_form_layout_config($form, array("exclude"=>array("_adverts_payment_total", "_adverts_payment_paid", "post_status"))) ?>
                        </tbody>
                    </table>


                
            </div><!-- /.inside -->
        </div>
        
        <div id="" class="postbox">
            <h3 class="hndle">
                <span><?php _e("Payment Log", "adverts") ?></span>
            </h3>
            <div class="inside ">

                <div style="width: 100%; display: inline-block; box-sizing: border-box; font-size: 13px; line-height:1.6em">
                    <?php echo nl2br( $payment->post_content ) ?>
                </div>
                
                <!--textarea type="text" style="width:100%; height:2em" placeholder="Add Note ..."></textarea-->
            </div><!-- /.inside -->
        </div>

        
        
        
        
        
        
    </div>
    </div>
    </div>
    </form>
    
</div>

<?php wp_enqueue_script( 'adverts-auto-numeric' ); ?>

<script type="text/javascript">
    jQuery(function($) {
        $(".adverts-payment-price").autoNumeric('init', adverts_currency);
    });
</script>

