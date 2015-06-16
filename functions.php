<?php

/**
 * kamoha functions and definitions
 *
 * @package kamoha
 */
/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( !isset( $content_width ) ) {
    $content_width = 640; /* pixels */
}

if ( !function_exists( 'kamoha_setup' ) ) :

    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function kamoha_setup(){

        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on kamoha, use a find and replace
         * to change 'kamoha' to the name of your theme in all the template files
         */
        load_theme_textdomain( 'kamoha', get_template_directory() . '/languages' );

        // Add default posts and comments RSS feed links to head.
        add_theme_support( 'automatic-feed-links' );

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
         */
        add_theme_support( 'post-thumbnails' );

        // This theme uses wp_nav_menu() in two locations.
        register_nav_menus( array(
            'primary' => __( 'Primary Menu', 'kamoha' ),
            'secondary' => __( 'Secondary Navigation', 'kamoha' ), /* Lea - 02/2014 - add another menu */
        ) );

        // Enable support for Post Formats.
        add_theme_support( 'post-formats', array('aside', 'image', 'video', 'quote', 'link') );

        // Setup the WordPress core custom background feature.
        /* 	add_theme_support( 'custom-background', apply_filters( 'kamoha_custom_background_args', array(
          'default-color' => 'ffffff',
          'default-image' => '',
          ) ) );
         */
        // Enable support for HTML5 markup.
        add_theme_support( 'html5', array('comment-list', 'search-form', 'comment-form',) );
    }

endif; // kamoha_setup
add_action( 'after_setup_theme', 'kamoha_setup' );

/**
 * Register widgetized area and update sidebar with default widgets.
 */
function kamoha_widgets_init(){
    register_sidebar( array(
        'name' => __( 'Countdown Sidebar', 'kamoha' ), /* (Lea 2014/05) - change sidebar name */
        'id' => 'sidebar-countdown',
        'before_widget' => '<section id="%1$s" class="aside_content %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ) );
    register_sidebar( array(
        'name' => __( 'Related Posts Sidebar', 'kamoha' ), /* (Lea 2014/07) - add sidebar for single page */
        'id' => 'sidebar-related-posts',
        'before_widget' => '<section id="%1$s" class="aside_content %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ) );
}

add_action( 'widgets_init', 'kamoha_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function kamoha_scripts(){
    wp_enqueue_style( 'kamoha-style', get_stylesheet_uri(), array(), '1.6.1.2' );

    wp_enqueue_script( 'kamoha-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

    wp_enqueue_script( 'kamoha-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}

add_action( 'wp_enqueue_scripts', 'kamoha_scripts' );

/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
//require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Lea - 02/2014 - load customized functions
 */
require get_template_directory() . '/inc/helpers.php';

/**
 * Lea -  load Jewish calendar functions
 */
require get_template_directory() . '/inc/jewish-calendar.php';

/**
 * Lea -  load Featured image functions
 */
require get_template_directory() . '/inc/featured-image.php';

/**
 * Lea -  load Comments and latest comments functions
 */
require get_template_directory() . '/inc/comments-and-latest-comments.php';

/**
 * Lea -  load Short excerpt functions
 */
require get_template_directory() . '/inc/short-excerpt.php';