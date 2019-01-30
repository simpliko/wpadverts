<?php
/**
 * Emails Module
 * 
 * This module sends predefined emails when some action occurs, for example
 * an email can be sent when a user published an Advert.
 * 
 * Additionally the module allows customizing the emails templates from
 * wp-admin panel.
 *
 * @package Adverts
 * @subpackage Emails
 * @author Grzegorz Winiarski
 * @version 1.3
 */

global $adverts_namespace, $adverts_emails;

$adverts_namespace['emails'] = array(
    'option_name' => 'adext_emails_config',
    'default' => array(
        
    )
);

include_once ADVERTS_PATH . 'addons/emails/includes/class-emails.php';

Adverts::instance()->set("emails", new Adext_Emails);

