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
        <?php _e("Payment History Edit", "wpadverts") ?>
    </h2>

    <?php adverts_admin_flash() ?>
    
    <?php if( $payment !== null ): ?>
    
    <form action="" method="post" class="adverts-form">
    <div id="poststuff" class=" ">
    <div id="post-body" class="metabox-holder columns-2">
    
    <div id="postbox-container-1" class="postbox-container">
        <div id="side-sortables" class="meta-box-sortables ui-sortable">
            <div id="" class="postbox adverts-ph-edit-sidebar">
                <h3 class="hndle"><span><?php _e("Payment", "wpadverts") ?></span></h3>
                    <div class="inside">
                        <div class="">
                            <div class="">
                                <p>
                                    <span class="label"><strong><?php _e("Gateway", "wpadverts") ?>:</strong></span>
                                    <span>
                                        <?php if($gateway): ?>
                                        <?php esc_html_e($gateway["title"]) ?>
                                        <?php else: ?>
                                         —
                                        <?php endif; ?>
                                    </span>
                                </p>
                                
                                <p>
                                    <span class="label"><strong><?php _e("Created", "wpadverts") ?>:</strong></span>
                                    <span>
                                        <?php echo date_i18n( get_option( 'date_format' ), get_post_time( 'U', false, $payment->ID ) )  ?>
                                    </span>
                                </p>

                                <p>
                                    <span class="label"><strong><?php _e("User IP", "wpadverts") ?>:</strong></span>
                                    <span>
                                        <?php esc_html_e( get_post_meta( $payment->ID, "_adverts_user_ip", true ) ) ?>
                                    </span>
                                </p>
                            </div>

                            <div class="">
                                <p>
                                    <span class="label"><strong><?php _e("Status", "wpadverts") ?>:</strong></span>&nbsp;
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
                                    <span class="label"><strong><?php _e("To Pay", "wpadverts") ?>:</strong></span>&nbsp;
                                    <input type="text" class="adverts-payment-price" name="_adverts_payment_total" value="<?php esc_attr_e( get_post_meta( $payment->ID, "_adverts_payment_total", true ) ) ?>" />
                                </p>
                            </div>
                            
                            <div class="">
                                <p>
                                    <span class="label"><strong><?php _e("Paid", "wpadverts") ?>:</strong></span>&nbsp;
                                    <input type="text" class="adverts-payment-price" name="_adverts_payment_paid" value="<?php esc_attr_e( get_post_meta( $payment->ID, "_adverts_payment_paid", true ) ) ?>" />
                                </p>
                            </div>

                        </div><!-- /.column-container -->
                    </div><!-- /.inside -->

                    <div class="">
                        <div id="major-publishing-actions">
                            <div id="publishing-action">
                                <input type="submit" class="button button-primary right" value="<?php _e("Update Payment", "wpadverts") ?>" />
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>

            </div>
        </div><!-- /#side-sortables -->
    </div>
        


    <div id="postbox-container-2" class="postbox-container">
    

        <div id="" class="postbox">
            <h3 class="hndle">
                <span><?php _e("Purchase Details", "wpadverts") ?></span>
            </h3>
            
            <?php do_action( "adext_payments_details_box", $payment ) ?>

            <div class="inside" style="font-size:1.1em; clear:both; overflow:hidden">
                <hr/>
                <strong><?php _e("Secure Complete Payment URL", "wpadverts") ?></strong><br/>
                <input type="text" readonly="readonly" name="_adverts_frontend_hash" value="<?php echo esc_attr( adext_payments_get_checkout_url( $payment->ID ) ) ?>" style="width: 100%" />
            </div>
        </div>
        
        
        <div id="" class="postbox">
            <h3 class="hndle">
                <span><?php _e("Customer Details", "wpadverts") ?></span>
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
                <span><?php _e("Payment Log", "wpadverts") ?></span>
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
    
    <?php endif; ?>
    
</div>

<?php wp_enqueue_script( 'adverts-auto-numeric' ); ?>

<script type="text/javascript">
    jQuery(function($) {
        $(".adverts-payment-price").autoNumeric('init', adverts_currency);
    });
</script>

