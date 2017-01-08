<?php
/* --------------------------------------------------------------
  >>> TABLE OF CONTENTS:
  ----------------------------------------------------------------
 * - Define Globals
 * - Init and setup functions
 * --   Define Constants
 * - Admin functions
 * --   TGM_Plugin_Activation function
 * --   Put excerpt meta-box before editor
 * --   Add second featured image box to posts
 * --   Gallery link should lead to file
 * --   Theme Customizer for holidays
 * - Menu and pre_get_posts functions
 * --   Menu function - adds child categories to menu items
 * --   pre_get_posts function
 * --   posts_where hook for events category
 * - Post content, image & excerpt functions
 * --   Show video or audio icon
 * - Sidebar & Widget functions
 * --   Newsflash
 * --   Event List
 * --   Tag cloud
 * - Homepage functions - show categories in homepage
 * - Facebook header info functions
 * -------------------------------------------------------------- */


/* Our own after setup function:
 * Define the blogs category,
 * Override the default image sizes so that the image is cropped exactly to the size
 * */
/* * *********************************************** */
/* * **************  Globals *********************** */
/* * *********************************************** */

global $kamoha_blog_post_index; /* in blog posts, show only the image of the first post. So keep track of what post number we're showing */
global $kamoha_latest_post_index; /* if latest posts are shown when there is no sticky, then the first post gets larger image. So keep track of what post number we're showing */
global $kamoha_sticky_exists;
global $kamoha_homepage_part;

/**
 * Class as enum, for determiing what part of the homepage is being displayed
 */
abstract class KamohaHomepagePart {

    const Sticky = 0;
    const Newest = 1;
    const Categories = 2;
    const Blogs = 3;
    const Issues = 4;
    const Tabs = 5;

}

/* * *********************************************** */
/* * *********  Init and setup functions *********** */
/* * *********************************************** */

/**
 * Define image upload sizes, and length of excerpts on homepage
 */
function kamoha_setup_more() {

    /*     * **************  Constants *********************** */

    define( 'BLOGS_CAT', 1303 ); /* 145 */
    define( 'ISSUES_CAT', 1296 ); /* 1164 */
    define( 'ASK_RABBI_PAGE', 15263 );
    define( 'MEETINGS_CAT', 313 ); /* 25 */
    define( 'LINKS_AND_EMERGECY', 22128 ); /* 20212 */
    define( 'STICKY_EXISTS', false );
    define( 'ABOUT_PAGE', 62 );
    define( 'POST_404_PAGE', 22126 ); /* 26583 */
    define( 'DOCUMENTATION_PAGE', 23376 ); /* 26583 */
    define( 'NEWEST_POSTS_CNT', 7 );
    define( 'FACEBOOK_LINK', 'https://www.facebook.com/kamoha.org.il' );

// define thumbnail sizes
    define( 'MEDIUM_WIDTH', 345 ); /* sticky post */
    define( 'MEDIUM_HEIGHT', 220 );

    define( 'SMALL_WIDTH', 155 ); /* top posts */
    define( 'SMALL_HEIGHT', 155 );


    define( 'TEENY_WIDTH', 100 ); /* tabs posts */
    define( 'TEENY_HEIGHT', 90 );

    add_image_size( 'medium', MEDIUM_WIDTH, MEDIUM_HEIGHT, true );
    add_image_size( 'small', SMALL_WIDTH, SMALL_HEIGHT, true );
    add_image_size( 'teeny', TEENY_WIDTH, TEENY_HEIGHT, true );

    // This theme styles the visual editor to resemble the theme style. Google fonts areadded via import from css file
    add_editor_style();
}

add_action( 'after_setup_theme', 'kamoha_setup_more' );


/* * *********************************************** */
/* * **************  Admin functions *************** */
/* * *********************************************** */

/* -----------------------------------------
 * TGM_Plugin_Activation function
 * ----------------------------------------- */

/**
 * Include the TGM_Plugin_Activation class.
 */
require_once get_template_directory() . '/inc/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'kamoha_register_required_plugins' );

/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function kamoha_register_required_plugins() {

    /**
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(
        // This is an example of how to include a plugin from the WordPress Plugin Repository.
        array(
            'name' => 'Demo Tax meta class',
            'slug' => 'bainternet-Tax-Meta-Class',
            'source' => get_template_directory() . '/inc/plugins/bainternet-Tax-Meta-Class.zip', // The plugin source.
            'required' => false,
            'force_deactivation' => false,
        ),
        array(
            'name' => 'CodeStyling Localization',
            'slug' => 'codestyling-localization',
            'required' => false,
        ),
        array(
            'name' => 'Intuitive Custom Post Order',
            'slug' => 'intuitive-custom-post-order',
            'required' => false,
            'force_deactivation' => true,
        ),
        array(
            'name' => 'My Category Order',
            'slug' => 'my-category-order',
            'required' => false,
            'force_deactivation' => false,
        ),
        array(
            'name' => 'Simple Share Buttons Adder',
            'slug' => 'simple-share-buttons-adder',
            'required' => false,
            'force_deactivation' => false,
        ),
        array(
            'name' => 'Responsive Menu',
            'slug' => 'responsive-menu',
            'required' => false,
            'force_deactivation' => false,
        ),
        array(
            'name' => 'WP-PageNavi',
            'slug' => 'wp-pagenavi',
            'required' => false,
            'force_deactivation' => false,
        ),
        array(
            'name' => 'T(-) Countdown',
            'slug' => 'jquery-t-countdown-widget',
            'required' => false,
        ),
        array(
            'name' => 'TF FAQ',
            'slug' => 'tf-faq',
            'source' => get_template_directory() . '/inc/plugins/tf-faq.zip', // The plugin source.
            'required' => false,
        ),
        array(
            'name' => 'WordPress Related Posts',
            'slug' => 'wordpress-23-related-posts-plugin',
            'required' => false,
            'force_deactivation' => false,
        ),
        array(
            'name' => 'WordPress Popular Posts',
            'slug' => 'wordpress-popular-posts',
            'required' => false,
        ),
        array(
            'name' => 'Regenerate Thumbnails',
            'slug' => 'regenerate-thumbnails',
            'required' => false,
            'force_deactivation' => false,
        ),
    );

    /**
     * Array of configuration settings. Amend each line as needed.
     * If you want the default strings to be available under your own theme domain,
     * leave the strings uncommented.
     * Some of the strings are added into a sprintf, so see the comments at the
     * end of each line for what each argument will be.
     */
    $config = array(
        'default_path' => '', // Default absolute path to pre-packaged plugins.
        'menu' => 'tgmpa-install-plugins', // Menu slug.
        'has_notices' => true, // Show admin notices or not.
        'dismissable' => true, // If false, a user cannot dismiss the nag message.
        'dismiss_msg' => '', // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false, // Automatically activate plugins after installation or not.
        'message' => '', // Message to output right before the plugins table.
        'strings' => array(
            'page_title' => __( 'Install Required Plugins', 'kamoha_2015' ),
            'menu_title' => __( 'Install Plugins', 'kamoha_2015' ),
            'installing' => __( 'Installing Plugin: %s', 'kamoha_2015' ), // %s = plugin name.
            'oops' => __( 'Something went wrong with the plugin API.', 'kamoha_2015' ),
            'notice_can_install_required' => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'kamoha_2015' ), // %1$s = plugin name(s).
            'notice_can_install_recommended' => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'kamoha_2015' ), // %1$s = plugin name(s).
            'notice_cannot_install' => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'kamoha_2015' ), // %1$s = plugin name(s).
            'notice_can_activate_required' => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'kamoha_2015' ), // %1$s = plugin name(s).
            'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'kamoha_2015' ), // %1$s = plugin name(s).
            'notice_cannot_activate' => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'kamoha_2015' ), // %1$s = plugin name(s).
            'notice_ask_to_update' => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'kamoha_2015' ), // %1$s = plugin name(s).
            'notice_cannot_update' => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'kamoha_2015' ), // %1$s = plugin name(s).
            'install_link' => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'kamoha_2015' ),
            'activate_link' => _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'kamoha_2015' ),
            'return' => __( 'Return to Required Plugins Installer', 'kamoha_2015' ),
            'plugin_activated' => __( 'Plugin activated successfully.', 'kamoha_2015' ),
            'complete' => __( 'All plugins installed and activated successfully. %s', 'kamoha_2015' ), // %s = dashboard link.
            'nag_type' => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
        )
    );

    tgmpa( $plugins, $config );
}

/* -----------------------------------------
 * Add second featured image box to posts
 * ----------------------------------------- */

if ( class_exists( 'MultiPostThumbnails' ) ) {
    new MultiPostThumbnails(
            array(
        'label' => __( 'Secondary Image', 'kamoha_2015' ),
        'id' => 'secondary-image',
        'post_type' => 'post'
            )
    );
}

/* -----------------------------------------
 * Gallery link should lead to file
 * ----------------------------------------- */

/*
 * Add a function to run when the gallery shortcode is run.
 * I know that adding shortcodes is plugin territory, but am reluctant to create a plugin for only 5 lines of code...
 */
add_shortcode( 'gallery', 'kamoha_gallery_shortcode' );

/**
 * Make all images in the gallery link to the media file
 * @param array $atts
 * @return string
 */
function kamoha_gallery_shortcode( $atts ) {
    $atts['link'] = 'file';
    return gallery_shortcode( $atts );
}

/* -----------------------------------------
 * Theme Customizer for holidays
 * ----------------------------------------- */
/* ---------------------------------------- */

/**
 * Let the admin user change the header background image & logo on special events such as holidays, the site birthday, etc.
 * @param type $wp_customize
 */
function kamoha_customize_register_func( $wp_customize ) {
    /* Create a section - holiday header */
    $wp_customize->add_section( 'holiday_header_changer', array(
        'title' => __( 'Holiday Header', 'kamoha_2015' ),
        'priority' => 25,
    ) );

    $wp_customize->add_setting( 'holiday_header', array(
        'default' => '',
        'transport' => 'postMessage',
        'sanitize_callback' => 'kamoha_sanitize_choices',
    ) );

    $wp_customize->add_control( 'holiday_header', array(
        'label' => stripslashes( __( 'choose holiday', 'kamoha_2015' ) ),
        'section' => 'holiday_header_changer',
        'type' => 'radio',
        'choices' => array(
            'regular' => __( 'regular', 'kamoha_2015' ),
            'rosh_hashana' => __( 'rosh hashana', 'kamoha_2015' ),
            'yom_kipur' => __( 'yom kipur', 'kamoha_2015' ),
            'sukot' => __( 'sukot', 'kamoha_2015' ),
            'birthday_6_blue_conff' => __( 'birthday - blue confetti', 'kamoha_2015' ),
            'birthday_6_black_conff' => __( 'birthday - black confetti', 'kamoha_2015' ),
            'birthday_6_baloons_conff' => __( 'birthday - baloons confetti', 'kamoha_2015' ),
            'trip_during' => __( 'trip during registration', 'kamoha_2015' ),
            'trip_before_close' => __( 'trip before close', 'kamoha_2015' ),
            'trip_after_close' => __( 'trip after close', 'kamoha_2015' ),
            'shabbat_early' => __( 'shabbat early', 'kamoha_2015' ),
            'shabbat_before_close' => __( 'shabbat before close', 'kamoha_2015' ),
            'shabbat_before_close_urban' => __( 'shabbat before close urban', 'kamoha_2015' ),
            'shabbat_after_close' => __( 'shabbat after close', 'kamoha_2015' ),
            'hanuka' => __( 'hanuka', 'kamoha_2015' ),
            'purim' => __( 'purim', 'kamoha_2015' ),
            'pesah' => __( 'pesah', 'kamoha_2015' ),
            'shoa' => __( 'shoa', 'kamoha_2015' ),
            'memorial' => __( 'memorial', 'kamoha_2015' ),
            'independence' => __( 'independence', 'kamoha_2015' ),
            'jerusalem' => __( 'jerusalem', 'kamoha_2015' ),
            'shavuot' => __( 'shavuot', 'kamoha_2015' ),
            'tishabeav' => __( 'tishabeav', 'kamoha_2015' ),
            'birthday_4_baloons' => __( 'birthday - baloons', 'kamoha_2015' ),
            'birthday_4_flowers' => __( 'birthday - flowers', 'kamoha_2015' ),
            'birthday_4_ribbons' => __( 'birthday - ribbons', 'kamoha_2015' ),
            'birthday_5_blue' => __( 'birthday - blue', 'kamoha_2015' ),
            'birthday_5_purple' => __( 'birthday - purple', 'kamoha_2015' ),
            'birthday_5_tubishvat' => __( 'birthday - tu bishvat', 'kamoha_2015' ),
        ),
    ) );
}

/* http://cachingandburning.com/wordpress-theme-customizer-sanitizing-radio-buttons-and-select-lists */

/**
 * Sanitize the choice by comparing to the array of radio button options
 * @global type $wp_customize
 * @param type $input
 * @param object $setting
 * @return string
 */
function kamoha_sanitize_choices( $input, $setting ) {
    global $wp_customize;

    $control = $wp_customize->get_control( $setting->id );

    if ( array_key_exists( $input, $control->choices ) ) {
        return $input;
    } else {
        return $setting->default;
    }
}

/**
 * Used to customize and manipulate the Theme Customization admin screen 
 */
add_action( 'customize_register', 'kamoha_customize_register_func' );

/**
 * Add body class based on the theme options which were chosen in the Theme Customizer
 * @param type $classes
 * @return type
 */
function kamoha_add_body_class( $classes ) {
    $color = strtolower( get_theme_mod( 'holiday_header' ) );
    if ( $color != 'regular' ) {
        $classes[] = 'special';
    }
    $classes[] = $color;
    return $classes;
}

/**
 * The "body_class" filter is used to filter the classes that are assigned to the body HTML element on the current page. 
 */
add_filter( 'body_class', 'kamoha_add_body_class' );

/**
 * Enqueue the JS file adds some LIVE to the Theme Customizer live preview
 *
 * @see add_action('customize_preview_init',$func)
 */
function kamoha_customizer_live_preview() {
    wp_enqueue_script(
            'kamoha-themecustomizer', //Give the script an ID
            get_template_directory_uri() . '/js/customize-themes.js', //Point to file
            array('jquery', 'customize-preview'), //Define dependencies
            '0.8.1', //Define a version (optional)
            true   //Put script in footer?
    );
}

/**
 * This action hook allows you to enqueue assets (such as javascript files) directly in the Theme Customizer
 */
add_action( 'customize_preview_init', 'kamoha_customizer_live_preview' );

/* * *********************************************** */
/* * *****  Menu and pre_get_posts functions ******* */
/* * *********************************************** */

/* -----------------------------------------
 * Menu function
 * ----------------------------------------- */

/**
 * This function adds child categories to the menu items that are categories that have children.
 * Taken from http://teleogistic.net/2013/02/dynamically-add-items-to-a-wp_nav_menu-list/
 * */
function kamoha_menu_cat_subnav( $items, $menu, $args ) {
    // Only do this in the topics menu
    if ( ! is_admin() ) {
        // loop thru the menu items, and find the ones that are categories
        $menu_order = count( $items ) + 1;
        $is_archive = false;
        foreach ( $items as $item ) {
            if ( 'category' == $item->object ) {
                // check if item has child categories
                $args = 'child_of=' . $item->object_id . '&hierarchical=1'; /* the cat_id */
                $args .= kamoha_order_categories_by();

                $termchildren = get_categories( $args );

                if ( ! empty( $termchildren ) ) {
                    // if it has child categories, add them to the menu
                    foreach ( $termchildren as $child ) {

                        // first check if this item has children
                        $children_array = array_filter( $termchildren, function($obj) use($child) { // pass $child as argument, otherwise it's not known in the context
                            if ( $obj->parent == $child->term_id ) {
                                return true;
                            }
                            return false;
                        } );

                        // then check if this item has parents in the array
                        $parents_array = array_filter( $termchildren, function($obj) use($child) { // pass $child as argument, otherwise it's not known in the context
                            if ( $obj->term_id == $child->parent ) {
                                return true;
                            }
                            return false;
                        } );

                        // get the correct id of parent - either existing item, or added item
                        $menu_item_parent = count( $parents_array ) > 0 ? $child->parent : $item->ID;

                        $new_item = wp_setup_nav_menu_item( $child );
                        $new_item->menu_item_parent = $menu_item_parent; /* the parent id should be the ID of the item in the menu, not the object_id which is the category id */
                        $new_item->db_id = $child->term_id;
                        $new_item->url = get_term_link( $child );
                        $new_item->title = $child->name;
                        $new_item->menu_order = $menu_order;
                        $new_item->status = 'draft';
                        if ( count( $children_array ) > 0 ) {
                            $new_item->classes[] = 'menu-item-has-children';
                        }

                        $items[] = $new_item;
                        $menu_order ++;
                    }
                }
            }
        }
    }
    return $items;
}

add_filter( 'wp_get_nav_menu_items', 'kamoha_menu_cat_subnav', 10, 3 );

/* -----------------------------------------
 * pre_get_posts function
 * ----------------------------------------- */

/**
 * Define the main homepage query arguments. Unfortunatly, WP isn't able to bring
 * sticky & recent posts together without mixing them up, so we first get the sticky post
 * and then run another query to get the 6 most recent
 * @param type $query
 * @return type
 */
function kamoha_modify_query( $query ) {
    /* In events category, if the pastposts parameter exists, get only the posts whos date field has a date before today. But only in the main query 
     *    if the futureposts parameter exists, get only the posts whos date field has a date after today  */
    if ( is_category( MEETINGS_CAT ) && ! is_admin() && $query->is_main_query() ) {
        if ( filter_input( INPUT_GET, 'pastposts' ) != NULL || filter_input( INPUT_GET, 'futureposts' ) != NULL ) {
            $query->set( 'meta_key', 'date' );
            /* order events by the meta field */
            add_filter( 'posts_orderby', 'edit_posts_orderby_desc' );
        }
        if ( filter_input( INPUT_GET, 'futureposts' ) != NULL ) {
            /* in future events, we want to see the closest event first, and then the next. However, this order isn't enough - we also have a posts_orderby filter */
            $query->set( 'order', 'ASC' );
            add_filter( 'posts_orderby', 'edit_posts_orderby_asc' );
        }
    }
}

function kamoha_homepage_main_query() {
    $args = array();
    global $kamoha_sticky_exists;
    $kamoha_sticky_exists = count( get_option( 'sticky_posts' ) ) > 0 ? true : false;
    if ( $kamoha_sticky_exists ) {
        $args = array(
            'posts_per_page' => 1,
            'post__in' => array(get_option( 'sticky_posts' ), 'posts'),
            'ignore_sticky_posts' => 0);
    } else { // if no sticky, just get the 7 latest posts
        $args = array('posts_per_page' => 7);
    }
    $home_page_query = new WP_Query( $args );
    return $home_page_query;
}

/**
 * This hook is called after the query variable object is created, but before the actual query is run
 */
add_filter( 'pre_get_posts', 'kamoha_modify_query' );

/* -----------------------------------------
 * posts_where function for events category
 * ----------------------------------------- */

/**
 * In MEETINGS_CAT category, analyze whther we're showing past or future events, 
 * and add a WHERE cluase which will filter the posts acording the the date meta_field
 * @param string $where
 * @param type $query
 * @return string
 */
function kamoha_posts_where( $where, &$query ) {
    /* Add where clause to query in events category, and to event list in sidebar */
    if ( is_category( MEETINGS_CAT ) && ! is_admin() && $query->is_main_query()  // condition for events category
            || (isset( $query->query_vars["category"] ) && $query->query_vars["category"] == MEETINGS_CAT && ! $query->is_main_query()) // condition for event list in sidebar
    ) {
        $datedir = '';
        if ( filter_input( INPUT_GET, 'pastposts' ) != NULL ) {
            $datedir = '<'; // get past events
        } elseif ( filter_input( INPUT_GET, 'futureposts' ) != NULL || (isset( $query->query_vars["category"] ) && $query->query_vars["category"] == MEETINGS_CAT) ) {
            /* >= in order to show today's event */
            $datedir = '>='; //get future events - in category page or in sidebar
        }
        if ( ! empty( $datedir ) ) {
            $where .= " AND CONCAT(SUBSTR(SUBSTRING_INDEX(meta_value,'/',-1),1,4),'-',SUBSTR(SUBSTRING_INDEX(meta_value,'/',-2),1,2),'-',SUBSTR(SUBSTRING_INDEX(meta_value,'/',1),1,2)) " . $datedir . " '" . date( "Y-m-d" ) . "'";
        }
    }
    return $where;
}

/**
 * This filter applies to the posts where clause and allows you to restrict which posts will show up in various areas of the site.
 */
add_filter( 'posts_where', 'kamoha_posts_where', 2, 10 );

/**
 * Order posts by value in the meta-field "date", in descending order. Meant for past events
 * @param string $orderby_statement
 * @return string
 */
function edit_posts_orderby_desc( $orderby_statement ) {
    // Disable this filter for future queries!
    remove_filter( current_filter(), __FUNCTION__ );

    $orderby_statement = "CONCAT(SUBSTR(SUBSTRING_INDEX(meta_value,'/',-1),1,4),'-',SUBSTR(SUBSTRING_INDEX(meta_value,'/',-2),1,2),'-',SUBSTR(SUBSTRING_INDEX(meta_value,'/',1),1,2)) DESC";
    return $orderby_statement;
}

/**
 * Order posts by value in the meta-field "date", in ascending order. Meant for future events
 * @param string $orderby_statement
 * @return string
 */
function edit_posts_orderby_asc( $orderby_statement ) {
    // Disable this filter for future queries!
    remove_filter( current_filter(), __FUNCTION__ );

    $orderby_statement = "CONCAT(SUBSTR(meta_value,7 + INSTR(meta_value,'-'),4),'-',SUBSTR(meta_value,4 + INSTR(meta_value,'-'),2),'-',SUBSTR(meta_value,1 + INSTR(meta_value,'-'),2))  ASC";
    return $orderby_statement;
}

/* * *********************************************** */
/* * ***  Post content, image & excerpt functions ** */
/* * *********************************************** */

/* -------------------------
 * Show video or audio icon
 * ------------------------ */

/**
 * Check if content has a movie in it. If so - show the movie icon.
 * There are 3 ways to tell if there is a movie:
 * 1. If the content has an object tag or the youtube.com/embed string,
 * 2. if the tag סרטון or שמע was assigned to it.
 * 3. If it has a field of audio or video set to 1
 */
function kamoha_show_movie_icon() {
    $is_film = false;
    $is_audio = false;
    $post_id = get_the_ID();
    // first check user fields - this is the best indicator:
    if ( (get_post_meta( $post_id, 'video', true ) == '1' ) ) {
        $is_film = true;
    } elseif ( (get_post_meta( $post_id, 'audio', true ) == '1' ) ) {
        $is_audio = true;
    }

    // if no user fields were defined for this post, check tags
    if ( ! $is_film && ! $is_audio ) {
        // look in tags
        $posttags = wp_get_post_tags( $post_id );
        foreach ( $posttags as $thistag ) {
            if ( $thistag->name == 'סרטון' ) {
                $is_film = 'true';
            } elseif ( $thistag->name == 'שמע' ) {
                $is_audio = true;
            }
        }

        // if no tags found either, then check the content
        if ( ! $is_film && ! $is_audio ) {
            // better solution than regexp: http://wordpress.stackexchange.com/a/175795/373
            $content = do_shortcode( apply_filters( 'the_content', get_the_content() ) );
            $film_embeds = get_media_embedded_in_content( $content, array('video', 'object', 'embed', 'iframe') );
            if ( count( $film_embeds ) > 0 ) {
                /* iframe could be something other that video, so make sure only video is shown: 
                 * taken from: http://wordpress.stackexchange.com/questions/175793/get-first-video-from-the-post-both-embed-and-video-shortcodes/175795#175795
                 */
                foreach ( $film_embeds as $embed ) {
                    if ( strpos( $embed, 'video' ) || strpos( $embed, 'youtube' ) || strpos( $embed, 'vimeo' ) || strpos( $embed, 'download.macromedia.com' ) ) {
                        $is_film = true;
                    }
                }
            } else {
                $audio_embeds = get_media_embedded_in_content( $content, array('audio') );
                if ( count( $audio_embeds ) > 0 ) {
                    $is_audio = true;
                }
            }
        }
    }

    // finally, after all check are done, if a movie was found, print the image
    if ( $is_film ) {
        ?>
        <span aria-hidden="true" class="icon-film"></span>
        <?php
    } elseif ( $is_audio ) {
        ?>
        <span aria-hidden="true" class="icon-audio"></span>
        <?php
    }
}

/* * *********************************************** */
/* * ***  Sidebar & Widget functions -
 * Newsflash, Calendar, Event List, Tag cloud, latest comments ** */
/* * *********************************************** */

/* -----------------------------------------
 * Newsflash function
 * ----------------------------------------- */

/**
 * Get all posts of type $post_type
 * @return html - a list of all posts contents (without title)
 */
function kamoha_get_newsflash() {
    $ret = '';
    $args = array('post_type' => 'kamoha_newsflash');
    $posts_secondary = new WP_Query( $args );
    if ( $posts_secondary->have_posts() ) {
        $ret .= '<ul>';
        while ( $posts_secondary->have_posts() ) {
            $posts_secondary->the_post();
            $post_format = get_post_type();
            $ret .= '<li class="news_' . $post_format . '">';
            $ret .= '<div class="news_content">';
            $ret .= wpautop( get_the_content() );
            $ret .= '</div>';
            $ret .= '</li>';
        }

        $ret .= '</ul>';
    }
    wp_reset_postdata();
    return $ret;
}

/* -----------------------------------------
 * Events list functions
 * ----------------------------------------- */

/**
 * Show list of upcoming events in sidebar.
 * Gets posts from events category, and checks the date custom field
 * Shoe only events whos date is after today
 */
function kamoha_event_list() {
    add_filter( 'posts_orderby', 'edit_posts_orderby_asc' );
    $events = get_posts( array('numberposts' => -1, 'category' => MEETINGS_CAT, 'suppress_filters' => false, 'meta_key' => 'date') );
    $first_event = 1;
    ?>
    <ul class="events_list">
        <?php
        foreach ( $events as $event ) {
            $custom_fields = get_post_custom( $event->ID );
            $full_date = explode( " ", $custom_fields['date'][0] );
            $date_begin = explode( "/", $full_date[0] );
            $date_end = explode( "-", $date_begin[0] );
            /* only show those events who's end date hasn't passed yet */
            $hebdate = '';
            $gregdate = '';
            if ( isset( $custom_fields['hebdate'][0] ) ) {
                $hebdate = $custom_fields['hebdate'][0];
                $gregdate = $custom_fields['date'][0];
            } else {
                /* the date field has the gregorian date first, and then the hebrew date.
                 * When split by spaces, the first item is the gregorian date,
                 * and the rest is all parts of the hebrew date
                 */
                $date_arr = explode( ' ', $custom_fields['date'][0] );
                $gregdate = $date_arr[0];
                array_shift( $date_arr );
                $hebdate = implode( ' ', $date_arr );
            }
            ?>
            <?php
            if ( $event->ID != get_the_ID() && $first_event != 2 ) {
                $first_event = 0;
            }
            if ( $first_event == 1 || $first_event == 2 ) {
                ?>

                <div itemscope itemtype="http://data-vocabulary.org/Event">
                <?php } ?>
                <li class="evt">
                    <a class="evt-title" href="<?php echo get_permalink( $event->ID ); ?>"
                    <?php
                    if ( $first_event == 1 || $first_event == 2 ) {
                        echo ' itemprop="url"';
                    }
                    ?> > <?php
                           if ( $first_event == 1 || $first_event == 2 ) {
                               echo '<span class="evt-date" itemprop="summary">';
                           }
                           ?><?php
                           echo $event->post_title;
                           if ( $first_event == 1 || $first_event == 2 ) {
                               echo '</span>';
                           }
                           ?>
                        <span><?php
                            if ( $first_event == 1 || $first_event == 2 ) {
                                echo '<time itemprop="startDate" datetime="' . date( 'c', mktime( 0, 0, 0, $date_begin[1], $date_end[0], $date_begin[2] ) ) . '">';
                            }
                            echo $hebdate . " " . $gregdate;
                            if ( $first_event == 1 || $first_event == 2 ) {
                                echo '</time>';
                            }
                            ?></span>
                    </a>
                </li>
                <?php
                if ( $first_event == 2 ) {
                    $first_event = 0;
                    ?>
                </div>
                <?php
            }
            if ( $first_event == 1 ) {
                $first_event = 2;
                ?>

            </div>
            <?php
        }
    } // foreach
    //remove_filter( 'posts_orderby', 'edit_posts_orderby' ); // Lea 2015/04 - moved the remove_filter to the function called by add_filter
    ?>
    </ul>
    <?php // add a button for loading more posts. JS will hide the 2 last posts, and this button will toggle show/hide them                                      ?>
    <div id="moreEvents" class="toOpen showMore"> <?php _e( 'Load more', 'kamoha_2015' ) ?> </div>

    <a class="to_older_events" href="<?php echo htmlentities( add_query_arg( 'pastposts', '1', get_category_link( MEETINGS_CAT ) ) ); ?>"><?php _e( 'Go to older events and meetings', 'kamoha_2015' ) ?> > </a>
    <?php
}

/* -----------------------------------------
 * Tag cloud functions
 * ----------------------------------------- */

/**
 * Cahnges the total number of tags to display
 * Based on this article: http://designpx.com/tutorials/customize-tag-cloud-widget/
 * @param array $args - the tag cloud taxonomy arguments
 * @return the $args array
 */
function kamoha_tag_cloud_widget( $args ) {
    $args['number'] = 10; //adding a 0 will display all tags
    return $args;
}

/**
 * Filter the taxonomy used in the Tag Cloud widget.
 */
add_filter( 'widget_tag_cloud_args', 'kamoha_tag_cloud_widget' );


/* * *********************************************** */
/* * ***********  Homepage functions -  ************ */
/* * *********************************************** */

/**
 * This function returns one category of the current post.
 * If the post has only one category - that's the one it returns.
 * If it has multiple categories, then it checks meta field category.
 * If the name in that field matches one of the posts category - that's the category it returns.
 * If there's no meta field, or the name in it doesn't match any of the post's categories - it returns the first category by a-b-c.
 * @return object category
 */
function kamoha_show_post_one_cat() {
    $curr_cat = '';
    $categories_list = get_the_category();
    if ( count( $categories_list ) > 1 ) {
        $category_field = get_post_meta( get_the_ID(), 'category' );
        if ( count( $category_field ) > 0 ) {
            $curr_cat_name = $category_field[0];
            $children_array = array_filter( $categories_list, function($obj) use($curr_cat_name) { // pass $child as argument, otherwise it's not known in the context
                if ( $obj->name == $curr_cat_name ) {
                    return true;
                }
                return false;
            } );
            if ( count( $children_array ) > 0 ) {
                // rebase the array, because it only has some of its keyschildren_array
                $children_array = array_values( $children_array );
                $curr_cat = $children_array[0];
            } else {
                $curr_cat = $categories_list[0];
            }
        } else {
            $curr_cat = $categories_list[0];
        }
    } else {
        if ( count( $categories_list ) > 0 ) {
            $curr_cat = $categories_list[0];
        }
    }
//	return $curr_cat;
    if ( $curr_cat && kamoha_categorized_blog() ) {
        ?>
        <span class="cat-links <?php
              /* Lea 09/16 - add 'cat-blogs' class to blogs category too */
              if ( $curr_cat->cat_ID == BLOGS_CAT || $curr_cat->parent == BLOGS_CAT || cat_is_ancestor_of( BLOGS_CAT, $curr_cat->cat_ID ) ) {
                  echo 'cat-blogs';
              } elseif ( $curr_cat->parent == ISSUES_CAT ) {
                  echo 'cat-issues';
              }
              ?>">
            <a href="<?php echo get_category_link( $curr_cat->cat_ID ) ?>"><?php echo $curr_cat->name ?></a>
        </span>

        <?php
    } // End if categories_list
}

/**
 * Get the sub-categories to display in homepage, according to the value in the "show in homepage" custom field of category
 * @param type $parent - parent category
 * @return array of category objects
 */
function get_homepage_categories( $parent = 0 ) {

    $cat_list = Array();
    $args = 'parent=' . $parent . kamoha_order_categories_by();

    $categories = get_categories( $args );
    foreach ( $categories as $category ) {
        // use tax-meta plugin. taken from here: http://en.bainternet.info/2012/wordpress-taxonomies-extra-fields-the-easy-way
        if ( function_exists( 'get_tax_meta' ) ) {
            $prefix = 'kamoha_';
            $saved_data = get_tax_meta( $category->term_id, $prefix . 'checkbox_field_id' ); // get the value of the "show in homepage" checkbox
            if ( $saved_data === 'on' ) {
                $cat_list[] = $category;
            }
        }
    }
    return $cat_list;
}

/**
 * returns orderby string to pass to wp_list_categories
 * @param $order_by
 * @return the orderby string to add to query
 */
function kamoha_order_categories_by( $order_by = 'ID' ) {
    $cat_args = '&orderby=ID';
    /* orderby=order dpeneds on the "My category order" plugin. Sometimes even when the plugin is active,
     * the menubar won't show the sub categories, until you actually go to the dashboard and use the plugin,
     * i.e, click on the "Click to order categories" button for the relevant category... This is because the
     * plugin creates an addition column- term_order - in the wp_terms table, and doesn't do it until someone has actually
     * ordered at least one category. So add the 'orderby=order' argument, only if such a column exists */
    global $wpdb;
    $query1 = $wpdb->query( "SHOW COLUMNS FROM {$wpdb->terms} LIKE 'term_order'" );
    if ( $query1 == 1 ) { // if the colimn exists, then order by it
        $cat_args .= '&orderby=order';
    } else {
        $cat_args = '&orderby=' . $order_by;
    }
    return $cat_args;
}

/* * *********************************************************** */
/* * ***********  Facebook header info functions *************** */
/* * *********************************************************** */

/**
 * Get the excerpt as page description - for homepage and for single page
 * @return string
 */
function kamoha_get_facebook_page_description() {

    $ret = "";

    if ( is_single() ) {
        $desc = get_the_excerpt();
    }

    if ( $_SERVER["REQUEST_URI"] == "/" ) {
        $ret = __( 'homepage_description', 'kamoha_2015' ); //"כמוך היא קבוצה של הנמשכים לבני מינם, הפועלת לאור ההלכה האורתודוקסית. כמוך קם כדי לשמש בית להומוסקסואלים דתיים המשתדלים להקפיד על קלה כבחמורה.";
        //$ret = "כמוך היא קבוצה של הנמשכים לבני מינם, הפועלת לאור ההלכה האורתודוקסית. כמוך קם כדי לשמש בית להומוסקסואלים דתיים המשתדלים להקפיד על קלה כבחמורה.";
    } else if ( isset( $desc ) ) {
        $ret = htmlentities( $desc, ENT_QUOTES, "UTF-8" );
    }
    return $ret;
}

/* Disable logging of Akismet debug data when WP_DEBUG_LOG is true 
  https://wordpress.org/support/topic/akismet-and-wp_debug_log */
add_filter( 'akismet_debug_log', '__return_false' );

/* * *******************************
 * Remove image size functions
 * ******************************* */

/**
 * Remove the medium_large image size
 * @param array $sizes
 * @return array $sizes sans the medium_large image size
 */
function kamoha_remove_default_image_sizes( $sizes ) {
    unset( $sizes['medium_large'] );

    return $sizes;
}

add_filter( 'intermediate_image_sizes_advanced', 'kamoha_remove_default_image_sizes' );

// display featured post thumbnails in WordPress feeds
function wcs_post_thumbnails_in_feeds( $content ) {
    global $post;
    if ( has_post_thumbnail( $post->ID ) ) {
        $content = '<p>' . get_the_post_thumbnail( $post->ID, 'small' ) . '</p>' . $content;
    }
    return $content;
}

add_filter( 'the_excerpt_rss', 'wcs_post_thumbnails_in_feeds' );
add_filter( 'the_content_feed', 'wcs_post_thumbnails_in_feeds' );

/* * *******************************
 * AUXILIARY FUNCTIONS
 * ******************************* */

/**
 * Write any type of object to debug.log
 * @param type $data
 */
function kamoha_write_to_log( $data ) {
    // Dump data
    ob_start();
    var_dump( $data );
    $contents = ob_get_contents();
    ob_end_clean();
    error_log( $contents );
}
