<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

Adverts::instance()->get("emails")->admin->dispatch();