<?php

$buttons_position = isset( $atts["buttons_pos"] ) ? $atts["buttons_pos"] : "atw-flex-row";
$redirect_to  = isset( $atts["redirect_to"] ) ? $atts["redirect_to"] : "";
//echo "<pre>";print_r($atts);print_r(wpadverts_block_form_styles( $atts["form"] ));echo "</pre>";
?>

<style type="text/css">
    .wpadverts-blocks.wpadverts-block-search {
        <?php wpadverts_print_grays_variables( $atts["form"] ) ?>
    }
    <?php wpadverts_block_button_css( "primary", isset( $atts["primary_button"] ) ? $atts["primary_button"] : array(), ".wpadverts-blocks.wpadverts-block-search" ) ?>
    <?php wpadverts_block_button_css( "secondary", isset( $atts["secondary_button"] ) ? $atts["secondary_button"] : array(), ".wpadverts-blocks.wpadverts-block-search" ) ?>
</style>

<script type="text/javascript">
jQuery(function($) {
    $("#js-wpa-filter").on("click", function(e) {
        e.preventDefault();
        if($("#js-wpa-filters-wrap").is(":visible")) {
            $("#js-wpa-filters-wrap").addClass("atw-hidden");
        } else {
            $("#js-wpa-filters-wrap").removeClass("atw-hidden");
        }
        return false;
    });
});
</script>

<div class="wpadverts-blocks wpadverts-block-search atw-flex atw-flex-col">

    <form action="<?php echo esc_attr( $redirect_to ) ?>" method="get" class="wpadverts-form <?php echo wpadverts_block_form_styles( $atts["form"] ) ?>  atw-block atw-py-0">
        
        <?php foreach($form->get_fields( array( "type" => array( "adverts_field_hidden" ) ) ) as $field): ?>
        <?php call_user_func( adverts_field_get_renderer($field), $field, $form ) ?>
        <?php endforeach; ?>
        
        <div class="atw-flex atw-flex-col md:<?php echo $buttons_position ?>">
            
            <div class="wpa-form-wrap">
                <?php if( !empty( $fields_visible ) ): ?>
                <div class="wpa-form-wrap-inner">
                    <?php foreach( $fields_visible as $field ): ?>
                    <?php $width = $this->_get_field_width( $field ) ?>
                    <?php $pr = $pl = ""; ?>
                    <div class="atw-relative atw-items-end atw-box-border atw-pb-3 md:atw-px-1 <?php echo esc_attr( $width ) ?>">
                        <?php if( isset( $field["label"] ) && ! empty( $field["label"] ) ): ?>
                        <span class="atw-block atw-w-full atw-box-border atw-px-2 atw-py-0 atw-pb-1 atw-text-base atw-text-gray-600 adverts-search-input-label"><?php echo esc_html( $field["label"] ) ?></span>
                        <?php endif; ?>
                        <?php $field["class"] = isset( $field["class"] ) ?  $field["class"] : ""; ?>
                        <?php $field["class"] .= " atw-text-base atw-w-full atw-max-w-full"; ?>
                        <div class="atw-block atw-w-full">
                            <?php $r = adverts_field_get_renderer($field); ?>
                            <?php $r = function_exists( $r . "_block" ) ? $r . "_block" : $r; ?>
                            <?php call_user_func( $r, $field, $form ) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if( !empty( $fields_hidden ) ): ?>
                <div id="js-wpa-filters-wrap" class="atw-hidden atw-flex atw-flex-wrap atw-items-end atw-justify-between atw-py-0 atw-px-0">
                    <?php foreach( $fields_hidden as $field ): ?>
                    <?php $width = $this->_get_field_width( $field ) ?>
                    <div class="atw-relative atw-items-end atw-box-border atw-pb-3 md:atw-px-1 <?php echo esc_attr( $width ) ?>">
                        <?php if( isset( $field["label"] ) && ! empty( $field["label"] ) ): ?>
                        <span class="atw-block atw-w-full atw-box-border atw-px-2 atw-py-0 atw-pb-1 atw-text-base atw-text-gray-700 adverts-search-input-label"><?php echo esc_html( $field["label"] ) ?></span>
                        <?php endif; ?>
                        <?php $field["class"] = isset( $field["class"] ) ?  $field["class"] : ""; ?>
                        <?php $field["class"] .= " atw-text-base atw-w-full "; ?>
                        <div class="atw-block atw-w-full">
                            <?php call_user_func( adverts_field_get_renderer($field), $field, $form ) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="atw-flex <?php echo ( empty( $fields_hidden ) ? "atw-grid-cols-1" : "atw-grid-cols-2" ) ?> atw-gap-2 atw-pb-3 md:atw-flex-none <?php echo $buttons_position == "atw-flex-row" ? "md:atw-ml-2" : "" ?>">
                 
                <?php if( ! empty( $fields_hidden ) ): ?>
                <div class="atw-flex-auto atw-pr-2">
                    <?php echo wpadverts_block_button( $button_s_args, $atts["secondary_button"] ) ?>
                </div>
                <?php endif; ?>
                
                <div class="atw-flex-auto">
                    <?php echo wpadverts_block_button( $button_p_args, $atts["primary_button"] ) ?>
                </div>

            </div>
            
        </div>
        
        
    </form>

</div>