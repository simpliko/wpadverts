<?php 
/*
 *
 * 
 * @var $redirect_to
 * @var $atts
 * @var $form
 * @var $buttons_position
 */ 

$redirect_to = "";
$atts = isset( $atts ) ? $atts : array();
$form_id = isset( $form_id ) ? $form_id : null;
$buttons_position = isset( $buttons_position ) ? $buttons_position : "atw-flex-row";

$form_layout = isset( $form_layout ) ? $form_layout : "wpa-layout-stacked";
$form_layout_prop = "atw-w-1/3";

?>

<div class="wpadverts-block wpadverts-partial wpadverts-form">
    <form <?php if($form_id): ?>id="<?php echo esc_attr( $form_id ) ?>"<?php endif; ?> action="<?php echo esc_attr( $redirect_to ) ?>" method="post" class="wpadverts-form <?php echo wpadverts_block_form_styles( $atts ) ?> <?php echo esc_attr( $form_layout ) ?>  atw-block atw-py-0">

        <?php foreach($form->get_fields( array( "type" => array( "adverts_field_hidden" ) ) ) as $field): ?>
        <?php call_user_func( adverts_field_get_renderer($field), $field, $form ) ?>
        <?php endforeach; ?>
        
        <!--div class="atw-flex atw-flex-col md:<?php echo $buttons_position ?>"-->
            
            <div class="wpa-form-wrap">
                <div class="wpa-form-wrap-inner">
                    <?php foreach($form->get_fields() as $field): ?>
                    <?php $width = wpadverts_block_tpl_field_width( $field ) ?>
                    <?php $pr = $pl = ""; ?>
                    <?php if($field["type"] == "adverts_field_header"): ?>
                        <div data-name="<?php echo esc_attr( $field["name"] ) ?>" class="wpa-form-header <?php echo sprintf( "wpa-field--%s", $field["name"] ) ?> <?php echo esc_attr( $width ) ?>">
                            <span class="wpa-form-header-label "><?php echo esc_html($field["label"]) ?></span>
                            <?php if( isset( $field["description"] ) ): ?>
                            <div>
                                <span class="wpa-form-header-text"><?php echo esc_html( $field["description"] ) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div data-name="<?php echo esc_attr( $field["name"] ) ?>" class="wpa-field-wrap <?php echo sprintf( "wpa-field--%s", $field["name"] ) ?> <?php echo esc_attr( $width ) ?> <?php echo adverts_field_has_errors($field) ? "wpa-field-error" : "" ?>">
                            <?php if( isset( $field["label"] ) && ! empty( $field["label"] ) ): ?>
                            <span class="wpa-field-label">
                                <span class="wpa-field-label-text"><?php echo esc_html( $field["label"] ) ?></span>
                                <?php if(adverts_field_is_required($field)): ?>
                                <span class="wpa-field-asterisk">*</span>
                                <?php endif; ?>
                            </span>
                            <?php endif; ?>
                            <?php $field["class"] = isset( $field["class"] ) ?  $field["class"] : ""; ?>
                            <?php $field["class"] .= " atw-text-base atw-w-full atw-max-w-full"; ?>
                            <div class="wpa-field-input <?php echo wpadverts_block_tpl_field_type( $field ) ?>">
                                <?php $r = adverts_field_get_renderer($field); ?>
                                <?php $r = function_exists( $r . "_block" ) ? $r . "_block" : $r; ?>
                                <?php call_user_func( $r, $field, $form ) ?>

                                <?php if(isset( $field["description"] ) ): ?>
                                <div class="wpa-field-desc">
                                    <span class="wpa-field-desc-text "><?php echo esc_html( $field["description"] ) ?></span>
                                </div>
                                <?php endif; ?>

                                <?php if(adverts_field_has_errors($field)): ?>
                                <ul class="wpa-errors-list">
                                    <?php foreach($field["error"] as $k => $v): ?>
                                    <li class="wpa-error-item"><?php echo esc_html($v) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>

            </div>
            
            <?php do_action( "wpadverts/tpl/partial/form/before-buttons", $atts ) ?>

            <?php if( isset( $show_buttons ) && $show_buttons ): ?>
            <div class="wpa-form-buttons <?php echo $buttons_position == "atw-flex-row" ? "md:atw-ml-2" : "" ?>">
        
                <?php foreach( $buttons as $button ): ?>
                <div class="wpa-form-buttons-list">
                    <?php echo wpadverts_block_button( $button, array() ) ?>
                </div>
                <?php endforeach; ?>

            </div>
            <?php endif; ?>
            
            <?php do_action( "wpadverts/tpl/partial/form/after-buttons", $atts ) ?>

        <!--/div-->
        
        
    </form>
</div>