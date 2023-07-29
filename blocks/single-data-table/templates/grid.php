<div class="wpadverts-cpt <?php echo sprintf( "wpadverts-cpt-", $atts["post_type"] ) ?> wpadverts-cpt-data-table wpadverts-cpt-data-table-grid atw-w-full atw-flex atw-flex-col">

    <div class="atw-grid atw-grid-cols-1 <?php echo $cols_class ?> <?php if( $atts["closed_top"] ): ?>atw-mt-6<?php endif; ?>">
    <?php foreach( $data_table as $data ): ?>
        <div class="atw-border-solid atw-border-gray-100 atw-pb-2">
            <div class="atw-flex atw-items-center atw-py-3 atw-mx-0">
                <div class="atw-flex atw-justify-center atw-items-center atw-flex-none atw-mr-3">
                    <div class=" ">
                        <i class="<?php echo esc_attr($data["icon"]) ?> atw-text-gray-300 atw-text-3xl"></i>
                    </div>
                </div>
                <div class="atw-flex atw-flex-col atw-grow">
                    <div class="atw-flex atw-flex-none atw-items-center --atw-w-1/3 atw-text-gray-600 atw-text-base">
                        <span class="atw-inline-block atw-font-normal"><?php echo esc_html( $data["label"] ) ?></span>
                    </div>
                    <div class="atw-flex atw-grow atw-items-center atw-text-gray-800">
                        <span class="atw-inline-block"><?php echo $data["value"] ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

</div>