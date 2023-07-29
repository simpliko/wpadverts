
<div class="wpadverts-cpt <?php echo sprintf( "wpadverts-cpt-", $atts["post_type"] ) ?> wpadverts-cpt-data-table wpadverts-cpt-data-table-text atw-w-full atw-flex atw-flex-col">

    <div class="atw-mb-6">
        <?php foreach( $data_table as $content ): ?>
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

</div>