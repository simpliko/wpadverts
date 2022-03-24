<?php 
    // alias functions
    use function wpadverts_block_tpl_wrap as wrap; 

?>
<?php $image_id = adverts_get_main_image_id( get_the_ID() ) ?>

<div class="wpa-result-item atw-flex atw-border-solid atw-px-0 atw-border-0 atw-border-t atw-border-gray-100 hover:atw-bg-gray-50 atw-relative <?php echo adverts_css_classes( '', get_the_ID() ) ?>">
    
    <?php if( $show_image_column ): ?>
    <div class="atw-flex atw-flex-none atw-items-center">
        
        <?php include apply_filters( "wpadverts/blocks/load/template", $this->path . "./../list/templates/list-item-img-list.php", $atts )?>
        <?php include apply_filters( "wpadverts/blocks/load/template", $this->path . "./../list/templates/list-item-img-grid.php", $atts )?>


        
    </div>
    <?php endif; ?>
       
    <div class="wpa-result-details atw-flex atw-grow">
    
        <div class="wpa-detail-left atw-flex atw-flex-col atw-flex-1  ">

            <div class="wpa-result-title atw-mb-1 atw-leading-snug">
                <a href="<?php echo esc_attr( add_query_arg( "advert_id", get_the_ID() )) ?>" title="<?php echo esc_attr( get_the_title() ) ?>" class="wpa-result-link atw-inline-block atw-no-underline ">
                    <span class="wpa-result-title-text atw-inline-block atw-max-h-16 atw-text-gray-700 atw-text-lg atw-leading-tight atw-font-semibold "><?php echo esc_html( wpadverts_get_object_value( get_the_ID(), $atts["title_source"] ) ) ?></span>
                    <?php do_action( "adverts_list_after_title", get_the_ID() ) ?>
                </a>
            </div>

            <div class="wpa-result-meta atw-flex atw-flex-none atw-text-base atw-font-medium atw-text-gray-500">

                <?php $post = get_post( get_the_ID() ) ?>
                
                <?php foreach( $atts["data"] as $element ): ?>
                <?php echo wrap( get_the_ID(), $element["name"] ) ?>
                <?php endforeach; ?>

                <div class="atw-block">
                    <?php if($post->post_status == "pending"): ?>
                    <div class="wpa-bulb wpa-bulb-info" title="">
                        <?php _e("Inactive — This Ad is in moderation.", "wpadverts") ?>
                    </div>
                    <?php endif; ?>

                    <?php if($post->post_status == "expired"): ?>
                    <div class="wpa-bulb wpa-bulb-info" title="">
                        <?php _e("Inactive — This Ad expired.", "wpadverts") ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if( $post->post_status == "publish" ): ?>
                        <span class="wpa-bulb wpa-bulb-success">
                            <?php echo sprintf( __( "Published  — %s", "wpadverts"), wpadverts_block_list_post_date( get_the_ID() ) ) ?>
                        </span>
                    <?php endif; ?>

                    <?php //do_action("adverts_sh_manage_list_status", $post) ?>
                    <?php do_action("wpadverts/block/manage/list/status", $post) ?>
                </div>



            </div>


        </div>

        <?php if( $show_price_column ): ?>
        <div class="wpa-list-only wpa-detail-right wpa-result-last  ">
            <div class="atw-flex atw-items-center atw-h-full">
                <i class="fa-solid fa-angle-right atw-text-4xl atw-text-gray-500"></i>
            </div>
        </div>        
        <div class="wpa-grid-only wpa-detail-right wpa-result-last ">
            <div class="atw-flex atw-flex-row atw-items-center atw-pt-2">
                <?php echo wpadverts_block_button( $button_s1_args, $atts["secondary_button"] ) ?>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
    
</div>