<div class="wpadverts-cpt <?php echo sprintf( "wpadverts-cpt-", $atts["post_type"] ) ?> wpadverts-cpt-data-table wpadverts-cpt-data-table-list atw-w-full atw-flex atw-flex-col">
    <div class="atw-grid atw-grid-cols-1 md:atw-grid-cols-1 atw-border-x-0 atw-border-b-0 <?php if( $atts["closed_top"] ): ?>atw-mt-6 atw-border-t<?php else: ?>atw-border-t-0<?php endif; ?> atw-border-solid atw-border-gray-100">
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
        <?php #do_action( "adverts_tpl_single_details", $post_id, true ) ?>
    </div>
</div>