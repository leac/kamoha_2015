<?php
/* --------------------------------------------------------------
  >>> TABLE OF CONTENTS:
  ----------------------------------------------------------------
 * - Define Globals
 * - Init and setup functions
 * --   Define Constants
 * --   Create custom post type for newsflashe on sidebar
 * --   Add to extended_valid_elements for TinyMCE 
 * - Admin functions
 * --   Thumbnail column in the admin posts list
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

    // This theme styles the visual editor to resemble the theme style.
    add_editor_style( array('editor-style.css', kamoha_fonts_url()) );
}

add_action( 'after_setup_theme', 'kamoha_setup_more' );

/**
 * Create custom post type for newsflash on sidebar
 */
function kamoha_init_theme() {
    /* Create News Flash post type */
    register_post_type( 'kamoha_newsflash', array(
        'labels' => array(
            'name' => __( 'News Flashes', 'kamoha' ),
            'singular_name' => __( 'News Flash', 'kamoha' )
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'post-formats', 'thumbnail'),
        'exclude_from_search' => true // Lea 2015/03 - we don't want newsflash posts appearing in search results
            )
    );
}

add_action( 'init', 'kamoha_init_theme' );

/**
 * Add to extended_valid_elements for TinyMCE 
 * Prevents TinyMCE from stripping some attribute (such as the onclick) from the input element
 * @param $init assoc. array of TinyMCE options 
 * @return $init the changed assoc. array 
 */
function change_mce_options( $init ) {
    //code that adds additional attributes to the input tag. Needed for join form which has onchange attribute
    $ext = 'input[id|name|class|style|onclick|type|value|onchange|disabled|src]';

    //if extended_valid_elements alreay exists, add to it  
    //otherwise, set the extended_valid_elements to $ext  
    if ( isset( $init['extended_valid_elements'] ) ) {
        $init['extended_valid_elements'] .= ',' . $ext;
    } else {
        $init['extended_valid_elements'] = $ext;
    }

    //important: return $init!  
    return $init;
}

/*
 * Grants access to the TinyMCE settings array
 *  */
add_filter( 'tiny_mce_before_init', 'change_mce_options' );


/* * *********************************************** */
/* * **************  Admin functions *************** */
/* * *********************************************** */

/* -----------------------------------------
 * Thumb column
 * ----------------------------------------- */

/* taken from http://wpengineer.com/1960/display-post-thumbnail-post-page-overview/ */
if ( !function_exists( 'admin_thumb_column' ) ) {

    /**
     * Adds a column in the admin posts list, to show posts thumbnails
     * @param array $cols
     * @return type
     */
    function admin_thumb_column( $cols ) {
        $cols['thumbnail'] = __( 'Thumbnail', 'kamoha' );
        return $cols;
    }

    /**
     * Shows the posts thumbnails in the thumbnail column added above
     * @param type $column_name
     * @param type $post_id
     */
    function admin_thumb_value( $column_name, $post_id ) {
        $width = (int) 50;
        $height = (int) 50;

        if ( 'thumbnail' == $column_name ) {
            // thumbnail of WP 2.9
            $thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
            // image from gallery
            $attachments = get_children( array('post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image') );
            if ( $thumbnail_id ) {
                $thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true );
            } elseif ( $attachments ) {
                foreach ( $attachments as $attachment_id => $attachment ) {
                    $thumb = wp_get_attachment_image( $attachment_id, array($width, $height), true );
                }
            }



            if ( isset( $thumb ) && $thumb ) {
                echo $thumb;
            } else {

                echo __( 'None', 'kamoha' );
            }
        }
    }

    // for posts
    add_filter( 'manage_posts_columns', 'admin_thumb_column' );
    add_action( 'manage_posts_custom_column', 'admin_thumb_value', 10, 2 );

    // for pages
    add_filter( 'manage_pages_columns', 'admin_thumb_column' );
    add_action( 'manage_pages_custom_column', 'admin_thumb_value', 10, 2 );
}

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
            'page_title' => __( 'Install Required Plugins', 'tgmpa' ),
            'menu_title' => __( 'Install Plugins', 'tgmpa' ),
            'installing' => __( 'Installing Plugin: %s', 'tgmpa' ), // %s = plugin name.
            'oops' => __( 'Something went wrong with the plugin API.', 'tgmpa' ),
            'notice_can_install_required' => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s).
            'notice_can_install_recommended' => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_install' => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s).
            'notice_can_activate_required' => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
            'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_activate' => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s).
            'notice_ask_to_update' => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_update' => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s).
            'install_link' => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
            'activate_link' => _n_noop( 'Begin activating plugin', 'Begin activating plugins' ),
            'return' => __( 'Return to Required Plugins Installer', 'tgmpa' ),
            'plugin_activated' => __( 'Plugin activated successfully.', 'tgmpa' ),
            'complete' => __( 'All plugins installed and activated successfully. %s', 'tgmpa' ), // %s = dashboard link.
            'nag_type' => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
        )
    );

    tgmpa( $plugins, $config );
}

/* -----------------------------------------
 * Put excerpt meta-box before editor
 * ----------------------------------------- */

/* adapted from here: http://wordpress.stackexchange.com/a/158485/373 */

/**
 * Place the excerpt meta_box above the post editor
 * @param type $post_type
 */
function kamoha_add_excerpt_meta_box( $post_type ) {
    if ( in_array( $post_type, array('post', 'page') ) ) {
        add_meta_box(
                'contact_details_meta', __( 'Excerpt', 'kamoha' ), 'post_excerpt_meta_box', $post_type, 'test', // change to something other then normal, advanced or side. Probably shouldn't use test, but I already did, and now when I try to change to something else, it doesn't work...
                'high'
        );
    }
}

/*
 * The hook allows meta box registration for any post type. 
 */
add_action( 'add_meta_boxes', 'kamoha_add_excerpt_meta_box' );

/**
 * Place the excerpt meta_box above the post editor
 * @global type $post
 * @global type $wp_meta_boxes
 */
function kamoha_run_excerpt_meta_box() {
# Get the globals:
    global $post, $wp_meta_boxes;

# Output the "advanced" meta boxes:
    do_meta_boxes( get_current_screen(), 'test', $post );

# Remove the initial "advanced" meta boxes:
    //unset( $wp_meta_boxes[ 'post' ][ 'test' ] );
}

add_action( 'edit_form_after_title', 'kamoha_run_excerpt_meta_box' );

/**
 * Remove the excerpt meta_box from its original position, under the post editor
 */
function kamoha_remove_normal_excerpt() { /* this added on my own */
    remove_meta_box( 'postexcerpt', 'post', 'normal' );
}

add_action( 'admin_menu', 'kamoha_remove_normal_excerpt' );

/* -----------------------------------------
 * Add second featured image box to posts
 * ----------------------------------------- */

if ( class_exists( 'MultiPostThumbnails' ) ) {
    new MultiPostThumbnails(
            array(
        'label' => __( 'Secondary Image', 'kamoha' ),
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
        'title' => __( 'Holiday Header', 'kamoha' ),
        'priority' => 25,
    ) );

    $wp_customize->add_setting( 'holiday_header', array(
        'default' => '',
        'transport' => 'postMessage',
        'sanitize_callback' => 'kamoha_sanitize_choices',
    ) );

    $wp_customize->add_control( 'holiday_header', array(
        'label' => stripslashes( __( 'choose holiday', 'kamoha' ) ),
        'section' => 'holiday_header_changer',
        'type' => 'radio',
        'choices' => array(
            'regular' => __( 'regular', 'kamoha' ),
            'rosh_hashana' => __( 'rosh hashana', 'kamoha' ),
            'yom_kipur' => __( 'yom kipur', 'kamoha' ),
            'sukot' => __( 'sukot', 'kamoha' ),
            'birthday_baloons' => __( 'birthday - baloons', 'kamoha' ),
            'birthday_flowers' => __( 'birthday - flowers', 'kamoha' ),
            'birthday_ribbons' => __( 'birthday - ribbons', 'kamoha' ),
            'trip_during' => __( 'trip during registration', 'kamoha' ),
            'trip_before_close' => __( 'trip before close', 'kamoha' ),
            'trip_after_close' => __( 'trip after close', 'kamoha' ),
            'shabbat_early' => __( 'shabbat early', 'kamoha' ),
            'shabbat_before_close' => __( 'shabbat before close', 'kamoha' ),
            'shabbat_before_close_urban' => __( 'shabbat before close urban', 'kamoha' ),
            'shabbat_after_close' => __( 'shabbat after close', 'kamoha' ),
            'hanuka' => __( 'hanuka', 'kamoha' ),
            'purim' => __( 'purim', 'kamoha' ),
            'pesah' => __( 'pesah', 'kamoha' ),
            'memorial' => __( 'memorial', 'kamoha' ),
            'independence' => __( 'independence', 'kamoha' ),
            'jerusalem' => __( 'jerusalem', 'kamoha' ),
            'shavuot' => __( 'shavuot', 'kamoha' ),
            'tishabeav' => __( 'tishabeav', 'kamoha' ),
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
            '0.7.9', //Define a version (optional)
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
    if ( !is_admin() ) {
        // loop thru the menu items, and find the ones that are categories
        $menu_order = count( $items ) + 1;
        $is_archive = false;
        foreach ( $items as $item ) {
            if ( 'category' == $item->object ) {
                // check if item has child categories
                $args = 'child_of=' . $item->object_id . '&hierarchical=1'; /* the cat_id */
                $args .= kamoha_order_categories_by();

                $termchildren = get_categories( $args );

                if ( !empty( $termchildren ) ) {
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
    /* In homepage, get sticky post, or - if no sticky post exists - get the 7 newest posts */
    if ( $query->is_home() && $query->is_main_query() ) {
        global $kamoha_sticky_exists;
        $kamoha_sticky_exists = count( get_option( 'sticky_posts' ) ) > 0 ? true : false;
        if ( $kamoha_sticky_exists ) {
            $query->set( 'posts_per_page', 1 );
            $query->set( 'post__in', array(get_option( 'sticky_posts' ), 'posts') );
            $query->set( 'ignore_sticky_posts', 0 );
        } else { // if no sticky, just get the 7 latest posts
            $query->set( 'posts_per_page', 7 );
        }
    }
    /* In events category, if the pastposts parameter exists, get only the posts whos date field has a date before today. But only in the main query 
     *    if the futureposts parameter exists, get only the posts whos date field has a date after today  */ elseif ( is_category( MEETINGS_CAT ) && !is_admin() && $query->is_main_query() ) {
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
    if ( is_category( MEETINGS_CAT ) && !is_admin() && $query->is_main_query()  // condition for events category
            || (isset( $query->query_vars["category"] ) && $query->query_vars["category"] == MEETINGS_CAT && !$query->is_main_query()) // condition for event list in sidebar
    ) {
        $datedir = '';
        if ( filter_input( INPUT_GET, 'pastposts' ) != NULL ) {
            $datedir = '<'; // get past events
        } elseif ( filter_input( INPUT_GET, 'futureposts' ) != NULL || (isset( $query->query_vars["category"] ) && $query->query_vars["category"] == MEETINGS_CAT) ) {
            $datedir = '>'; //get future events - in category page or in sidebar
        }
        if ( !empty( $datedir ) ) {
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
    if ( !$is_film && !$is_audio ) {
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
        if ( !$is_film && !$is_audio ) {
            // better solution than regexp: http://wordpress.stackexchange.com/a/175795/373
            $content = do_shortcode( apply_filters( 'the_content', get_the_content() ) );
            $audio_embeds = get_media_embedded_in_content( $content, array('video', 'object', 'embed', 'iframe') );
            if ( count( $audio_embeds ) > 0 ) {
                $is_film = true;
            } else {
                $film_embeds = get_media_embedded_in_content( $content, array('audio') );
                if ( count( $film_embeds ) > 0 ) {
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
 * @param type $post_type
 * @return html - a list of all posts contents (without title)
 */
function kamoha_get_custom_post_type( $post_type ) {
    $ret = '';
    $args = array('post_type' => $post_type);
    $posts_secondary = new WP_Query( $args );
    if ( $posts_secondary->have_posts() ) {
        $ret .= '<ul>';
        while ($posts_secondary->have_posts()) {
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
    <?php // add a button for loading more posts. JS will hide the 2 last posts, and this button will toggle show/hide them                                   ?>
    <div id="moreEvents" class="toOpen showMore"> <?php _e( 'Load more', 'kamoha' ) ?> </div>

    <a class="to_older_events" href="<?php echo htmlentities( add_query_arg( 'pastposts', '1', get_category_link( MEETINGS_CAT ) ) ); ?>"><?php _e( 'Go to older events and meetings', 'kamoha' ) ?> > </a>
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
              if ( $curr_cat->parent == BLOGS_CAT || cat_is_ancestor_of( BLOGS_CAT, $curr_cat->cat_ID ) ) {
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
        $ret = __( 'homepage_description', 'kamoha' ); //"כמוך היא קבוצה של הנמשכים לבני מינם, הפועלת לאור ההלכה האורתודוקסית. כמוך קם כדי לשמש בית להומוסקסואלים דתיים המשתדלים להקפיד על קלה כבחמורה.";
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
