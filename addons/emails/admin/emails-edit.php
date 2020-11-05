<?php
/**
 * Displays Edit Email Options Page
 * 
 * This file is a template for wp-admin / Classifieds / Options / Emails panel. 
 * 
 * It is being loaded by Adext_Emails_Admin::edit_form()
 * 
 * @see Adext_Emails_Admin::edit_form()
 * @since 1.3
 */
?>
<div class="wrap">
    
    <h2 class="nav-tab-wrapper">
        <a href="<?php esc_attr_e( remove_query_arg( array( 'edit', 'emaction' ) ) ) ?>" class="nav-tab nav-tab-active"><?php _e("Email Templates", "wpadverts") ?></a>
        <a href="<?php esc_attr_e( add_query_arg( array('emaction'=>'options', 'edit'=>null) ) ) ?>" class="nav-tab "><?php _e("Options", "wpadverts") ?></a>
    </h2>

    <?php adverts_admin_flash() ?>

    <form action="" method="post" class="adverts-form adverts-adext-emails-edit">
        <table class="form-table">
            <tbody>
            <?php echo adverts_form_layout_config($form) ?>
                
            <tr valign="top">
                <th scope="row">&nbsp;</th>
                <td>
                    <p class="submit">
                       <input type="submit" value="<?php esc_attr_e($button_text) ?>" class="button-primary" name="Submit"/>
                    </p>
                </td>
            </tr>
                
            </tbody>
        </table>



    </form>

</div>

<script type="type/text" id="tmpl-adext-email-edit-header-row">
    <tr valign="top" class="adext-emails-edit-row" style="display:none">
        <th scope="row">
            <label for="message_subject">
                <# if ( data._mode == "edit" ) { #>
                <input type="text" class="header-name" name="header_name[]" placeholder="<?php _e( "Header Name", "wpadverts" ) ?>" value="{{ data.header_name }}" style="width:150px" />      
                <# } else { #>
                <label>{{ data.header_name }}</label>
                <input type="hidden" name="header_name[]" value="{{ data.header_name }}" />
                <# } #>
            </label>
        </th>
        <td class="adext-emails-edit-td">
            <input type="text" name="header_value[]" class="header-value adext-emails-full-width regular-text" value="{{ data.header_value }}" />
            <# if ( data._mode == "edit" ) { #>
            <a href="#" class="button button-secondary button-small adext-emails-edit-td-yes"><span class="dashicons dashicons-yes"></span></a>
            <a href="#" class="button button-secondary button-small adext-emails-edit-td-no"><span class="dashicons dashicons-no"></span></a>
            <# } else { #>
            <a href="#" class="button button-secondary button-small adext-emails-edit-td-edit"><span class="dashicons dashicons-edit"></span></a>
            <a href="#" class="button button-secondary button-small adext-emails-edit-td-no"><span class="dashicons dashicons-no"></span></a>
            <# } #>
        </td>
    </tr>
</script>

<script type="type/text" id="tmpl-adext-email-edit-attachment-row">
    <div class="adext-emails-edit-td" style="margin:5px 0 5px 0">
        <input type="text" name="message_attachments[]" class="header-value adext-emails-full-width regular-text" value="{{ data.attachment }}" />
        <a href="#" class="button button-secondary button-small adext-emails-edit-td-no"><span class="dashicons dashicons-no"></span></a>
    </div>
</script>


<?php if( ! empty( $message["headers"] ) && is_array( $message["headers"] ) ): ?>
<script type="text/javascript">
var WPADVERTS_EMAIL_EDIT_HEADERS = <?php echo json_encode( $message["headers"] ) ?>;
</script>
<?php endif; ?>

<?php if( ! empty( $message["attachments"] ) && is_array( $message["attachments"] ) ): ?>
<script type="text/javascript">
var WPADVERTS_EMAIL_EDIT_ATTACHMENTS = <?php echo json_encode( $message["attachments"] ) ?>;
</script>
<?php endif; ?>