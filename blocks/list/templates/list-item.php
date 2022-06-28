<?php 
    // alias functions
    use function wpadverts_block_tpl_wrap as wrap; 

?>
<?php $image_id = adverts_get_main_image_id( get_the_ID() ) ?>

<div class="wpa-result-item atw-flex atw-border-solid atw-px-0 atw-border-0 atw-border-t atw-border-gray-100 hover:atw-bg-gray-50 atw-relative <?php echo adverts_css_classes( '', get_the_ID() ) ?>">
    
    <?php if( $show_image_column ): ?>
    <div class="atw-flex atw-flex-none atw-items-center">
        
        <?php include apply_filters( "wpadverts/blocks/load/template", dirname( __FILE__ ) . "/list-item-img-list.php", $atts )?>
        <?php include apply_filters( "wpadverts/blocks/load/template", dirname( __FILE__ ) . "/list-item-img-grid.php", $atts )?>


        
    </div>
    <?php endif; ?>
       
    <div class="wpa-result-details atw-flex atw-grow">
    
        <div class="wpa-detail-left atw-flex atw-flex-col atw-flex-1  ">

            <div class="wpa-result-title atw-mb-1 atw-leading-snug">
                <a href="<?php the_permalink() ?>" title="<?php echo esc_attr( get_the_title() ) ?>" class="wpa-result-link atw-inline-block atw-no-underline ">
                    <span class="wpa-result-title-text atw-inline-block atw-max-h-16 atw-text-gray-700 atw-text-lg atw-leading-tight atw-font-semibold "><?php echo esc_html( wpadverts_get_object_value( get_the_ID(), $atts["title_source"] ) ) ?></span>
                    <?php do_action( "adverts_list_after_title", get_the_ID(), true ) ?>
                </a>
            </div>

            <div class="wpa-result-meta atw-flex atw-flex-none atw-text-base atw-font-medium atw-text-gray-500">

                <?php foreach( $atts["data"] as $element ): ?>
                <?php echo wrap( get_the_ID(), $element["name"] ) ?>
                <?php endforeach; ?>

            </div>


        </div>

        <?php if( $show_price_column ): ?>
        <div class="wpa-detail-right wpa-result-last atw-flex atw-items-center ">
            <?php echo wrap( get_the_ID(), $atts["alt_source"], "wpa-result-last-text atw-font-bold atw-text-red-700 atw-text-lg") ?>
        </div>
        <?php endif; ?>
        
    </div>
    
    <?php do_action( "wpadverts/block/list-item/tpl/after-ad", get_the_ID() ) ?>
    

</div>