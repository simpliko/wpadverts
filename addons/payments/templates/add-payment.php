<?php adverts_flash( $adverts_flash ) ?>

<div class="adverts-grid adverts-grid-closed-top">
    <div class="adverts-grid-row">
        <div class="adverts-grid-col adverts-col-65">
            <?php esc_html_e($listing->post_title) ?>
        </div>
        <div class="adverts-grid-col adverts-col-35">
            <?php echo adverts_price($price) ?>
        </div>
    </div>  
    
    <div class="adverts-grid-row">
        <div class="adverts-grid-col adverts-col-65">
            <strong><?php _e( 'Total', 'adverts') ?></strong>
        </div>
        <div class="adverts-grid-col adverts-col-35">
            <strong><?php echo adverts_price($price) ?></strong>
        </div>
    </div>        
            
</div>

<?php $gateways = adext_payment_gateway_get() ?>
<?php if(empty($gateways)): ?>
<div class="adverts-flash-error">
    <span><?php _e("No Payment Gateways Enabled!", "adverts") ?></span>
</div>
<?php else: ?>

<br/>

<ul class="adverts-tabs adverts-payment-data" data-page-id="<?php esc_attr_e(get_the_ID()) ?>" data-listing-id="<?php esc_attr_e($listing->ID) ?>" data-object-id="<?php esc_attr_e($post->ID) ?>">
    <?php foreach($gateways as $g_name => $gateway): ?>
    <li class="adverts-tab-link <?php if($g_name==adverts_config("payments.default_gateway")):?>current<?php endif; ?>" data-tab="<?php esc_attr_e($g_name) ?>"><?php esc_html_e($gateway["title"]) ?></li>
    <?php endforeach; ?>
</ul>
<div class="adverts-tab-content">
    
</div>

<br/>

<a href="#" class="adverts-button adext-payments-place-order"><?php esc_html_e("Place Order", "adverts") ?></a>

<?php endif; ?>