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
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
        <?php wp_head(); ?>
    </head>

    <body <?php body_class( 'clear' );  // clear class is added so the footer and its bottom margin fit into the body.    ?>>
        <div id="page" class="hfeed site">

            <header id="masthead" class="site-header" role="banner">
                <?php // nav is before site-branding, so that in lower resolutions it will get a row of its own above the logo  ?>
                <nav id="site-navigation" class="main-navigation clear" role="navigation">
                    <a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'kamoha' ); ?></a>

                    <?php wp_nav_menu( array('theme_location' => 'primary', 'menu_class' => 'clear') ); ?>
                </nav><!-- #site-navigation -->
                <div class="site-branding">
                    <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                </div>

                <?php get_search_form(); ?>

                <nav id="topics-navigation" class="topics-navigation" role="navigation">
                    <a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'kamoha' ); ?></a>

                    <?php wp_nav_menu( array('theme_location' => 'secondary', 'menu_class' => 'clear site_nav') ); ?>
                </nav><!-- #site-navigation -->
            </header><!-- #masthead -->

            <div id="content" class="site-content">
