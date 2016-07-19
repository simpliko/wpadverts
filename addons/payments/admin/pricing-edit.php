<?php
/**
 * Displays Payment Edit Page
 * 
 * This file is a template for wp-admin / Classifieds / Options / Payments / Pricing / Edit panel. 
 * 
 * It is being loaded by adext_payments_page_pricing function.
 * 
 * @see adext_payments_page_pricing()
 * @since 0.1
 */
?>
<div class="wrap">
    
    <h2 class="nav-tab-wrapper">
        <a href="<?php esc_attr_e( remove_query_arg( array( 'add', 'edit', 'adaction' ) ) ) ?>" class="nav-tab "><?php _e("Options", "adverts") ?></a>
        <a href="<?php esc_attr_e( remove_query_arg( array( 'add', 'edit' ), add_query_arg( array('adaction'=>'list') ) ) ) ?>" class="nav-tab nav-tab-active">
            <?php _e("Pricing", "adverts") ?>
            <a class="add-new-h2" href="<?php esc_attr_e( remove_query_arg( array('edit'), add_query_arg( array( 'add'=>1 ) ) ) ) ?>"><?php _e("Add New") ?></a> 
        </a>
    </h2>

    <?php adverts_admin_flash() ?>

    <form action="" method="post" class="adverts-form">
        <table class="form-table">
            <tbody>
            <?php echo adverts_form_layout_config($form) ?>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" value="<?php esc_attr_e($button_text) ?>" class="button-primary" name="Submit"/>
        </p>

    </form>

</div>

<script type="text/javascript">
    jQuery(function($) {
        $("#adverts_price").autoNumeric('init', adverts_currency);
    });
    
</script>