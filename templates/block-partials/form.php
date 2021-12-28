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
$buttons_position = isset( $buttons_position ) ? $buttons_position : "atw-flex-row";

?>

<div class="wpadverts-block wpadverts-partial">
    <form action="<?php echo esc_attr( $redirect_to ) ?>" method="get" class="wpadverts-form <?php echo wpadverts_block_form_styles( $atts ) ?>  atw-block atw-py-0">
        
        <?php foreach($form->get_fields( array( "type" => array( "adverts_field_hidden" ) ) ) as $field): ?>
        <?php call_user_func( adverts_field_get_renderer($field), $field, $form ) ?>
        <?php endforeach; ?>
        
        
        <div class="atw-flex atw-flex-col md:<?php echo $buttons_position ?>">
            
            <div class="md:atw-flex-grow md:atw--mx-1">
                <div class="atw-flex atw-flex-wrap atw-items-end atw-justify-between atw-py-0 atw-px-0">
                    <?php foreach($form->get_fields() as $field): ?>
                    <?php $width = wpadverts_block_tpl_field_width( $field ) ?>
                    <?php $pr = $pl = ""; ?>
                    <?php if($field["type"] == "adverts_field_header"): ?>
                        <div class="atw-relative atw-items-end atw-box-border atw-pb-3 md:atw-px-1 <?php echo esc_attr( $width ) ?>">
                            <span class="atw-text-xl atw-font-bold"><?php echo esc_html($field["label"]) ?></span>
                            <?php if( isset( $field["description"] ) ): ?>
                            <span class="adverts-field-header-description"><?php echo esc_html( $field["description"] ) ?></span>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div data-name="<?php echo esc_attr( $field["name"] ) ?>" class="wpa-field-wrap <?php echo sprintf( "wpa-field--%s", $field["name"] ) ?> atw-relative atw-items-end atw-box-border atw-pb-3 md:atw-px-1 <?php echo esc_attr( $width ) ?> <?php echo adverts_field_has_errors($field) ? "wpa-field-error" : "" ?>">
                            <?php if( isset( $field["label"] ) && ! empty( $field["label"] ) ): ?>
                            <span class="atw-block atw-w-full atw-box-border atw-px-2 atw-py-0 atw-pb-1 atw-text-base atw-text-gray-600 adverts-search-input-label"><?php echo esc_html( $field["label"] ) ?></span>
                            <?php endif; ?>
                            <?php $field["class"] = isset( $field["class"] ) ?  $field["class"] : ""; ?>
                            <?php $field["class"] .= " atw-text-base atw-w-full atw-max-w-full"; ?>
                            <div class="atw-block atw-w-full">
                                <?php $r = adverts_field_get_renderer($field); ?>
                                <?php $r = function_exists( $r . "_block" ) ? $r . "_block" : $r; ?>
                                <?php call_user_func( $r, $field, $form ) ?>

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
            
            <div class="wpa-feedback">
                <div style="display:none" class="wpa-feedback-error atw-flex atw-flex-row atw-my-3 atw-py-3 atw-px-6 atw-bg-red-100 atw-text-red-600 atw-rounded-lg">
                    <div>
                        <span>
                            <i class="fas fa-exclamation-triangle atw-text-lg"></i>
                        </span>
                    </div>
                    <div class="atw-px-3">
                        <div>
                            <span class="wpa-feedback-title atw-text-red-700"></span>
                        </div>
                        <div class="wpa-feedback-text">
                            <!--ul>
                                <li>Errror 1</li>
                            </ul-->
                        </div>
                    </div>

                </div>
            </div>

            <?php if( isset( $show_buttons ) && $show_buttons ): ?>
            <div class="atw-flex atw-pb-3 md:atw-flex-none <?php echo $buttons_position == "atw-flex-row" ? "md:atw-ml-2" : "" ?>">
        
                <?php foreach( $buttons as $button ): ?>
                <div class="atw-flex-auto">
                    <?php echo wpadverts_block_button( $button, array() ) ?>
                </div>
                <?php endforeach; ?>

            </div>
            <?php endif; ?>
            
        </div>
        
        
    </form>
</div>