<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package kamoha
 */
?><!DOCTYPE html>
<!--[if lt IE 7 ]><html <?php language_attributes(); ?> class="no-js ie ie6 lte7 lte8 lte9"><![endif]-->
<!--[if IE 7 ]><html <?php language_attributes(); ?> class="no-js ie ie7 lte7 lte8 lte9"><![endif]-->
<!--[if IE 8 ]><html <?php language_attributes(); ?> class="no-js ie ie8 lte8 lte9"><![endif]-->
<!--[if IE 9 ]><html <?php language_attributes(); ?> class="no-js ie ie9 lte9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri() ?>/favicon.ico" />
        <?php
        // For facebook link details
        ?>
        <?php $post_description = kamoha_get_facebook_page_description(); ?>
        <?php if ( is_single() ) : ?>
            <?php $post_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' ); ?>
            <meta property="og:url" content="<?php echo post_permalink( $post->ID ); ?>" />
            <meta property="og:title" content="<?php echo str_replace( "\"", "''", html_entity_decode( get_the_title( $post->ID ), ENT_QUOTES, "UTF-8" ) ); /* decode if for hyphen etc. replace is so quotes don't interrupt the content attribute */ ?>" />
            <meta property="og:image" content="<?php echo $post_image_url[0]; ?>" />
            <link rel="image_src" href=" <?php echo $post_image_url[0]; ?> ">
            <meta property="og:description" content="<?php echo htmlentities( $post_description, ENT_QUOTES, "UTF-8" ); ?>" />
        <?php endif; ?>
        <meta name="description" content="<?php echo htmlentities( $post_description, ENT_QUOTES, "UTF-8" ); ?>">
        <title><?php wp_title( '|', true, 'right' ); ?></title>
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
        <!-- http://www.google.com/fonts#UsePlace:use/Collection:Alef -->
        <link href='http://fonts.googleapis.com/css?family=Alef:400,700&subset=latin,hebrew' rel='stylesheet' type='text/css'>
        <style type="text/css">
            @font-face {font-family: 'icomoon';src:url('<?php echo get_stylesheet_directory_uri(); ?>/fonts/icomoon.eot?-416wh5');src:url('<?php echo get_stylesheet_directory_uri(); ?>/fonts/icomoon.eot?#iefix-416wh5') format('embedded-opentype'),url('<?php echo get_stylesheet_directory_uri(); ?>/fonts/icomoon.woff?-416wh5') format('woff'),url('<?php echo get_stylesheet_directory_uri(); ?>/fonts/icomoon.ttf?-416wh5') format('truetype'),url('<?php echo get_stylesheet_directory_uri(); ?>/fonts/icomoon.svg?-416wh5#icomoon') format('svg');font-weight: normal;font-style: normal;}
        </style>
        <?php wp_head(); ?>
    </head>
