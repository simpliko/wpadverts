<style type="text/css">
    .wpadverts-blocks.wpadverts-block-manage form.wpadverts-form {
        <?php wpadverts_print_grays_variables( isset( $atts["form"] ) ? $atts["form"] : "" ) ?>
    }
    <?php wpadverts_block_button_css( "primary", isset( $atts["primary_button"] ) ? $atts["primary_button"] : array(), ".wpadverts-blocks.wpadverts-block-manage" ) ?>
    <?php wpadverts_block_button_css( "secondary", isset( $atts["secondary_button"] ) ? $atts["secondary_button"] : array(), ".wpadverts-blocks.wpadverts-block-manage" ) ?>
</style>



<div class="wpadverts-blocks wpadverts-block-manage">
    <div class="atw-mb-2">
        <span>
            <a href="<?php echo esc_attr( remove_query_arg( "advert_id" ) ) ?>" class="js-wpa-manage-go-back atw-no-underline atw-text-lg atw-flex atw-items-center">
                <i class="fa-solid fa-angle-left atw-text-2xl atw-pr-2"></i>
                <span><?php _e("Go back", "wpadverts" ) ?></span>
            </a>
        </span>
    </div>

    <?php echo wpadverts_block_flash( $adverts_flash ) ?>

    <div class="atw-flex atw-flex-col md:atw-flex-row atw--mx-1 atw-flex-wrap">

        <?php foreach( $buttons_manage as $m ): ?>
        <div class="<?php echo isset( $m["class"] ) ? esc_attr( $m["class"] ) : "" ?> atw-items-center atw-w-full atw-px-1 atw-pt-2 md:atw-w-auto">
            <form action="<?php  echo isset( $m["link"] ) ? esc_attr( $m["link"] ) : "" ?>" mathod="get">
                <?php echo wpadverts_block_button( $m["button"] , $atts[ $m["button_type" ] ] ) ?>
            </form>
        </div> 
        <?php endforeach; ?>       

    </div>

    <?php do_action( "wpadverts/block/manage-edit/tpl/before-form", $post_id ) ?>

    <div>
        <?php include ADVERTS_PATH . '/templates/block-partials/form.php' ?>
    </div>

    <?php do_action( "wpadverts/block/manage-edit/tpl/after-form", $post_id ) ?>
</div>

