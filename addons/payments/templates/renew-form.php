<style type="text/css">
    .wpadverts-publish-payment form.wpadverts-form {
        <?php wpadverts_print_grays_variables( isset( $atts["form"] ) ? $atts["form"] : "" ) ?>
    }
    <?php wpadverts_block_button_css( "primary", isset( $atts["primary_button"] ) ? $atts["primary_button"] : array() ) ?>
    <?php wpadverts_block_button_css( "secondary", isset( $atts["secondary_button"] ) ? $atts["secondary_button"] : array() ) ?>
</style>

<?php echo wpadverts_block_flash( $adverts_flash ) ?>

<div class="wpadverts-blocks wpadverts-manage-renew">
<?php include ADVERTS_PATH . '/templates/block-partials/form.php' ?>
</div>
