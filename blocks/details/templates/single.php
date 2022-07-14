<?php do_action( "wpadverts/block/details/tpl/start", $post_id, $atts ) ?>

<style type="text/css">
    .wpadverts-cpt form.wpadverts-form {
        <?php wpadverts_print_grays_variables( isset( $atts["form"] ) ? $atts["form"] : "" ) ?>
    }
    <?php wpadverts_block_button_css( "primary", isset( $atts["primary_button"] ) ? $atts["primary_button"] : array() ) ?>
    <?php wpadverts_block_button_css( "secondary", isset( $atts["secondary_button"] ) ? $atts["secondary_button"] : array() ) ?>
</style>

<div class="wpadverts-cpt <?php echo sprintf( "wpadverts-cpt-", $atts["post_type"] ) ?> atw-w-full atw-flex atw-flex-col">

    <?php do_action( "adverts_tpl_single_top", $post_id, true ) ?>

    <div class="atw-flex atw-flex-col md:atw-flex-row">
        <div class="atw-flex atw-grow ">
            <div class="atw-flex-none atw-m-0 atw-pr-4 -adverts-single-author-avatar">
                <?php $id_or_email = get_post_field( 'post_author', $post_id ) ?>
                <?php $id_or_email = $id_or_email ? $id_or_email : get_post_meta($post_id, 'adverts_email', true) ?>
                <?php echo get_avatar( $id_or_email, 64, "", "", array( "class" => "atw-m-0 atw-p-0 atw-block atw-rounded-full" ) ) ?>
            </div>
            <div class="atw-flex atw-flex-col atw-grow atw-justify-center -adverts-single-author-name">
                <div class="atw-block">
                    <span class="atw-font-bold atw-text-gray-700 atw-text-xl"><?php echo apply_filters( "adverts_tpl_single_posted_by", esc_html( get_post_meta($post_id, 'adverts_person', true) ), $post_id ) ?></span>
                </div>
                <div class="atw-block">
                    <span class="atw-text-gray-500 atw-text-base">
                    <?php printf( __('Published %1$s - %2$s ago', "wpadverts"), date_i18n( 'd/m/Y' /*get_option( 'date_format' )*/, get_post_time( 'U', false, $post_id ) ), human_time_diff( get_post_time( 'U', false, $post_id ), current_time('timestamp') ) ) ?>
                    </span>
                </div>
                
            </div>
        </div>
    
        <?php if( get_post_meta( $post_id, "adverts_price", true) ): ?>
        <div class="atw-flex atw-flex-none atw-items-center atw-mt-6 md:atw-m-0 -adverts-single-price">
            <span class="atw-text-2xl atw-font-normal atw-text-white atw-bg-red-700 atw-px-4 atw-py-2 wpadverts-price-box"><?php echo esc_html( adverts_get_the_price( $post_id ) ) ?></span>
        </div>
        <?php elseif( adverts_config( 'empty_price' ) ): ?>
        <div class="atw-flex atw-flex-none atw-items-center atw-mt-6 md:atw-m-0 -adverts-single-price adverts-price-empty">
            <span class="atw-text-2xl atw-font-normal atw-text-white atw-bg-red-700 atw-px-4 atw-py-2 wpadverts-price-box"><?php echo esc_html( adverts_empty_price( get_the_ID() ) ) ?></span>
        </div>
        <?php endif; ?>

    </div>

    <div class="atw-grid atw-grid-cols-1 md:atw-grid-cols-1 atw-mt-6 atw-border-t atw-border-solid atw-border-gray-100">
        <?php foreach( $data_table as $k => $data ): ?>
        <?php if( $data["value"] !== false ): ?>
        <div class="atw-border-b atw-border-solid atw-border-gray-100 atw-pb-2">
            <div class="atw-flex atw-pt-3 atw-pb-1 atw-mx-0">
                <div class="atw-hidden md:atw-flex atw-justify-center atw-items-center atw-bg-gray-200 atw-w-10 atw-h-10 atw-rounded-full atw-mr-3">
                    <div class=" ">
                        <i class="<?php echo esc_attr( $data["icon"] ) ?> atw-text-gray-400 atw-text-lg"></i>
                    </div>
                </div>
                <div class="atw-flex atw-flex-col md:atw-flex-row atw-grow">
                    <div class="atw-flex atw-flex-none atw-items-center atw-w-1/3 atw-h-10 atw-text-gray-700 atw-text-base atw-mb-1 md:atw-mb-0">
                        <span class="atw-inline-block atw-font-bold md:atw-font-normal"><?php echo esc_html( $data["label"] ) ?></span>
                    </div>
                    <div class="atw-flex atw-grow atw-items-center atw-text-gray-800">
                        <span class="atw-inline-block <?php echo isset( $data["row_classes"] ) ? esc_attr( $data["row_classes"] ) : "" ?>"><?php echo $data["value"] ?></span>
                    </div>
                </div>
            </div>
            
            
            <?php do_action( "wpadverts/block/details/tpl/after/meta", $post_id ) ?>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>
        <?php do_action( "adverts_tpl_single_details", $post_id, true ) ?>
    </div>

    <div class="atw-mb-6">
        <?php foreach( $content_table as $content ): ?>
        <div class="atw-mt-3">
            <div>
                <span class="atw-inline-block atw-text-gray-700 atw-text-xl atw-font-bold atw-py-3"><?php echo esc_html( $content["label"] ) ?></span>
            </div>
            <div>
                <?php echo $content["value"] ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>


    <!--div class="atw-grid atw-grid-cols-1 md:atw-grid-cols-3 atw-mt-6">
        <?php foreach( array(1,2,3) as $k ): ?>
        <div class="atw-border-solid atw-border-gray-100 atw-pb-2">
            <div class="atw-flex atw-py-3 atw-mx-0">
                <div class="atw-flex atw-justify-center atw-items-center atw-flex-none atw-mr-3">
                    <div class=" ">
                        <i class="fas fa-fire atw-text-gray-300 atw-text-3xl"></i>
                    </div>
                </div>
                <div class="atw-flex atw-flex-col atw-grow">
                    <div class="atw-flex atw-flex-none atw-items-center atw-w-1/3 atw-text-gray-600 atw-text-base">
                        <span class="atw-inline-block atw-font-normal">Price</span>
                    </div>
                    <div class="atw-flex atw-grow atw-items-center atw-text-gray-800">
                        <span class="atw-inline-block">$143.00</span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div-->


    <?php do_action( "adverts_tpl_single_bottom", $post_id, true ) ?>
    
    <?php if( ! empty( $contact_options ) || ! empty( $options ) ): ?>
    <div class="wpa-cpt-contact-details atw-my-6 atw--mx-1">

        <div class="atw-relative atw-flex atw-flex-col md:atw-flex-row atw--mx-1">

            <div class="atw-relative atw-flex atw-flex-1 atw-flex-col md:atw-flex-row md:atw-flex-none">
                <?php foreach( $contact_options as $contact_option ): ?>
                <div class="atw-flex-auto atw-mx-1 atw-mb-3">
                <?php 
                    echo wpadverts_block_button( 
                        $contact_option, 
                        $contact_option["options"]
                    ) 
                ?>
                </div>
                <?php endforeach; ?>
            </div>
            
        </div>

        <?php do_action( "wpadverts/block/details/tpl/contact-content", $post_id, $atts ) ?>

    </div>

    <div class="wpa-more-bg atw-z-30 atw-bg-black atw-inset-0 atw-absolute atw-opacity-60" style="display:none"></div>

    <?php endif; ?>

    <?php do_action( "wpadverts/block/details/tpl/contact", $post_id, $atts ) ?>

</div>

<?php do_action( "wpadverts/block/details/tpl/end", $post_id, $atts ) ?>
