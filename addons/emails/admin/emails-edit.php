<?php
/**
 * Displays Edit Email Options Page
 * 
 * This file is a template for wp-admin / Classifieds / Options / Emails panel. 
 * 
 * @since 1.3
 */
?>
<div class="wrap">
    
    <h2 class="nav-tab-wrapper">
        <a href="<?php esc_attr_e( remove_query_arg( array( 'edit', 'emaction' ) ) ) ?>" class="nav-tab nav-tab-active"><?php _e("Email Templates", "adverts") ?></a>
        <a href="<?php esc_attr_e( add_query_arg( array('emaction'=>'options') ) ) ?>" class="nav-tab "><?php _e("Options", "adverts") ?></a>
    </h2>

    <?php adverts_admin_flash() ?>

    <style type="text/css">
        .adverts-adext-emails-edit tr > th {
            width: 150px;
            padding: 10px 0px 10px 0;
        }
        .adverts-adext-emails-edit tr > td {
            padding: 5px 0px 5px 0;
        }
        #wp-message_body-wrap,
        .adext-emails-full-width {
            width: 85%;
        }
        .adext-emails-field-name-email {
            width: 85%;
            display: flex;
            justify-content: space-between;
        }
        .adext-emails-field-name-email > input:first-child {
            width: 50%;
        }
        .adext-emails-field-name-email > input:last-child {
            width: 50%;
            margin-left: 2px;
            margin-right: 0;
        }
        #message_subject {
            padding: 3px 8px;
            font-size: 1.7em;
            line-height: 100%;
            height: 1.7em;
            outline: 0;
            margin: 0 0 3px;
            background-color: #fff;
        }
        .adext-emails-edit-td .button-small {
            margin-top: 2px;
        }
        .adext-emails-edit-td .button-small .dashicons {
            vertical-align: middle;
        }
    </style>
    
    <form action="" method="post" class="adverts-form adverts-adext-emails-edit">
        <table class="form-table">
            <tbody>
            <?php echo adverts_form_layout_config($form) ?>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" value="<?php esc_attr_e($button_text) ?>" class="button-primary" name="Submit"/>
        </p>

    </form>

</div>

<script type="type/text" id="tmpl-adext-email-edit-header-row">
    <tr valign="top" class="adext-emails-edit-row" style="background:whitesmoke;display:none">
        <th scope="row">
            <label for="message_subject">
                <# if ( data._mode == "edit" ) { #>
                <input type="text" name="header_name" placeholder="<?php _e( "Header Name", "adverts" ) ?>" value="{{ data.header_name }}" style="width:150px" />      
                <# } else { #>
                <label for="from">{{ data.header_name }}</label>
                <# } #>
            </label>
        </th>
        <td class="adext-emails-edit-td">
            <input type="text" name="header_value" id="" class="adext-emails-full-width regular-text" value="{{ data.header_value }}" />
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