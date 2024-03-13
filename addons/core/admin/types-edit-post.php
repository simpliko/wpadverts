<?php wp_enqueue_style( 'media-views' ) ; ?>

<div class="wrap">
    
    <h2 class="nav-tab-wrapper">
        <a href="<?php esc_attr_e( remove_query_arg( array( 'adaction', 'edit-post' => false, 'edit-user' => false, 'edit-taxonomy' => false ) ) ) ?>" class="nav-tab"><?php _e("Core Options", "wpadverts") ?></a>
        <a href="<?php esc_attr_e( add_query_arg( array('adaction' => 'gallery', 'edit-post' => false, 'edit-user' => false, 'edit-taxonomy' => false ) ) ) ?>" class="nav-tab "><?php _e("Gallery", "wpadverts") ?></a>
        <a href="<?php esc_attr_e( add_query_arg( array('adaction' => 'types', 'edit-post' => false, 'edit-user' => false, 'edit-taxonomy' => false ) ) ) ?>" class="nav-tab nav-tab-active"><?php _e("Types", "wpadverts") ?></a>
        <a href="<?php esc_attr_e( add_query_arg( array('adaction' => 'moderate') ) ) ?>" class="nav-tab "><?php _e("Spam", "wpadverts") ?></a>
    </h2>
    
    <h2 class="">
        <?php echo esc_html( $h2_title ) ?>
        
    </h2>
</div>

<div id="poststuff" class="wrap">
    
    <?php adverts_admin_flash() ?>
    
    <?php if( isset( $supports_comments ) && $supports_comments && $without_comments > 0 ): ?>
    <div class="update-message notice inline notice-info notice-alt" style="padding:8px 8px">
        <span style="color:#004991;line-height:2rem;"><?php echo sprintf( __("There are <strong>%d</strong> published ads with comments disabled.", "wpadverts" ), $without_comments ) ?> <a href="<?php echo esc_url( $enable_url ) ?>" style="margin-left:8px" class="button-secondary"><?php _e( "Enable comments for these ads now", "wpadverts" ) ?></a></span>
    </div>
    <?php endif; ?>

    <form action="" method="post">
        <?php wpadverts_config_nonce( $form_simple ) ?>
        <div class="postbox ">
            <div class="postbox-header">
                <h2 class="hndle ui-sortable-handle">
                    <?php _e( "Configuration", "wpadverts" ) ?>
                </h2>
   
            </div>
            <div class="inside wpadverts-types-edit">

                <div class="wpadverts-types-edit-form" >
                    
                    <div class="adverts-dt-form adverts-form wpadverts-types-form-general" data-tab="general">
                        <table class="form-table">
                            <tbody>
                            <?php echo adverts_form_layout_config($form_simple) ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="adverts-dt-form adverts-form wpadverts-types-form-labels" data-tab="labels">
                        <table class="form-table">
                            <tbody>
                            <?php echo adverts_form_layout_config($form_labels) ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="adverts-dt-form adverts-form wpadverts-types-form-renderers" data-tab="renderers">
                        <table class="form-table">
                            <tbody>
                            <?php echo adverts_form_layout_config($form_renderers) ?>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                
                <ul class="wpadverts-types-edit-menu">
                    <li><a href="#" data-tab="general"><?php _e( "General", "wpadverts" ) ?></a></li>
                    <li><a href="#" data-tab="labels"><?php _e( "Labels", "wpadverts" ) ?></a></li>
                    <li><a href="#" data-tab="renderers"><?php _e( "Rendering", "wpadverts" ) ?></a></li>
                </ul>
            </div>
        </div>
        

        
        
        <div class="postbox ">
            <div class="postbox-header">
                <h2 class="hndle ui-sortable-handle">
                    <?php _e( "Actions", "wpadverts" ) ?>
                </h2>
                
            </div>
            <div class="inside">
                
                <input type="submit" value="<?php echo esc_attr($button_text) ?>" class="button-primary" name="_submit_cpt" />
                <a href="<?php echo esc_attr( $restore_url ) ?>" class="button-secondary"><?php _e( "Restore Defaults", "wpadverts" ) ?></a>
            </div>
        </div>

    </form>

</div>

<?php Adverts_Types_Admin::icon_picker() ?>