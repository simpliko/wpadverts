<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php if ( function_exists( 'language_attributes' ) && function_exists( 'is_rtl' ) ) language_attributes(); else echo "dir='$text_direction'"; ?>>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width">
    <?php if ( function_exists( 'wp_no_robots' ) ) { wp_no_robots(); } ?>
    <title><?php echo $title ?></title>
    <style type="text/css">
        <?php
        if ( 'rtl' == $text_direction ) {
                echo 'body { font-family: Tahoma, Arial; }';
        }
        ?>
    </style>
    <?php do_action( "wp_head" ) ?>
</head>
    <body id="main-page">
        <?php echo $content; ?>
    </body>
    
    <?php do_action( "wp_footer" ) ?>
</html>