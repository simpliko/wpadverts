<?php wp_enqueue_script( 'adverts-frontend' ); ?>
<style type="text/css">
    .wpadverts-publish-payment form.wpadverts-form {
        <?php wpadverts_print_grays_variables( isset( $atts["form"] ) ? $atts["form"] : "" ) ?>
    }
    <?php wpadverts_block_button_css( "primary", isset( $atts["primary_button"] ) ? $atts["primary_button"] : array() ) ?>
    <?php wpadverts_block_button_css( "secondary", isset( $atts["secondary_button"] ) ? $atts["secondary_button"] : array() ) ?>
</style>

<?php echo wpadverts_block_flash( $adverts_flash ) ?>

<div class="wpadverts-blocks wpadverts-publish-payment">

    <div class="atw-flex atw-flex-col">
        <div class="atw-flex atw-py-3 atw-border-b atw-border-t atw-border-solid atw-border-gray-100">
            <div class="atw-flex-grow">
                <?php echo esc_html($listing->post_title) ?>
            </div>
            <div class="atw-flex-none">
                <?php echo adverts_price($price) ?>
            </div>
        </div>
        <div class="atw-flex atw-py-3 atw-border-b atw-border-solid atw-border-gray-100">
            <div class="atw-flex-grow atw-font-bold">
                <?php _e( "Total", "wpadverts") ?>
            </div>
            <div class="atw-flex-none atw-font-bold">
                <strong><?php echo adverts_price($price) ?></strong>
            </div>
        </div>
    </div>

    <?php $gateways = adext_payment_gateway_get() ?>
    <?php if(empty($gateways)): ?>
    <div class="adverts-flash-error">
        <span><?php _e("No Payment Gateway Enabled!", "wpadverts") ?></span>
    </div>
    <?php else: ?>

    <span class="atw-block atw-pt-6 atw-pb-3 atw-text-base atw-font-bold"><?php _e( "Select Payment Method", "wpadverts" ) ?></span>

    <div class="jswpa-payment-tabs wpa-tabs adverts-payment-data" data-page-id="<?php esc_attr_e(get_the_ID()) ?>" data-listing-id="<?php echo esc_attr($listing->ID) ?>" data-object-id="<?php echo esc_attr($post->ID) ?>" data-object-type="<?php echo esc_attr( get_post_meta( $payment->ID, '_adverts_payment_for', true ) ) ?>" data-payment-id="<?php echo esc_attr($payment->ID) ?>" data-is-block="1">
        <?php foreach($gateways as $g_name => $gateway): ?>
        <div class="jswpa-payment-tab-link wpa-tab-link <?php if($g_name==adverts_config("payments.default_gateway")):?>current<?php endif; ?>" data-tab="<?php echo esc_attr($g_name) ?>">
            <i class="fa fa-circle-check"></i>
            <?php esc_html_e($gateway["title"]) ?>
            
        </div>
        <?php endforeach; ?>
    </div>

    <span class="atw-block atw-pt-3 atw-pb-3 atw-text-base atw-font-bold"><?php _e( "Fill Payment Form", "wpadverts" ) ?></span>

    <div class="jswpa-payment-tab-content wpa-tab-content">
        
    </div>


    <div class="atw-flex atw-py-3">
        <form action="" method="get" class="atw-flex-0">
        <?php 
            echo wpadverts_block_button( array( 
                "text" => __( "Place Order", "wpadverts" ),
                "type" => "primary",
                "class" => "adext-payments-place-order"
            ) );
        ?>
        </form>
    </div>

    <?php endif; ?>

</div>