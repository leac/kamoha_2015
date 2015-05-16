<?php
/* --------------------------------------------------------------
  >>> TABLE OF CONTENTS:
  ----------------------------------------------------------------
 * - Define Globals
 * - Init and setup functions
 * --   Define Constants
 * --   Enqueue scripts
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

global $blog_post_index; /* in blog posts, show only the image of the first post. So keep track of what post number we're showing */
global $latest_post_index; /* if latest posts are shown when there is no sticky, then the first post gets larger image. So keep track of what post number we're showing */
global $sticky_exists;

/**
 * Class as enum, for determiing what part of the homepage is being displayed
 */
abstract class HomepagePart{

    const Sticky = 0;
    const Newest = 1;
    const Categories = 2;
    const Blogs = 3;
    const Issues = 4;
    const Tabs = 5;

}

global $homepage_part;


/* * *********************************************** */
/* * *********  Init and setup functions *********** */
/* * *********************************************** */

/**
 * Define image upload sizes, and length of excerpts on homepage
 */
function kamoha_setup_more(){

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

    define( 'MEDIUM_BIG_WIDTH', 200 ); /* categories posts */
    define( 'MEDIUM_BIG_HEIGHT', 200 );

    define( 'TEENY_WIDTH', 100 ); /* tabs posts */
    define( 'TEENY_HEIGHT', 90 );

    add_image_size( 'medium', MEDIUM_WIDTH, MEDIUM_HEIGHT, true );
    add_image_size( 'small', SMALL_WIDTH, SMALL_HEIGHT, true );
    add_image_size( 'medium_big', MEDIUM_BIG_WIDTH, MEDIUM_BIG_HEIGHT );
    add_image_size( 'teeny', TEENY_WIDTH, TEENY_HEIGHT, true );
}

add_action( 'after_setup_theme', 'kamoha_setup_more' );

/* -----------------------------------------
 * Enqueue scripts
 * ----------------------------------------- */

/**
 * Enqueu scripts on front end
 */
function kamoha_enqueu_scripts(){
    if ( !is_admin() ) {
        //add css and js
        wp_enqueue_script( 'script', get_template_directory_uri() . '/js/script.js', array('jquery'), 1, TRUE );

        /* remove scripts from homepage */
        if ( is_home() ) {
            wp_deregister_script( 'jquery-form' );
            wp_dequeue_script( 'jquery-form' );
            wp_deregister_script( 'contact-form-7' );
            wp_dequeue_script( 'contact-form-7' );
            wp_deregister_script( 'wp_rp_edit_related_posts_js' );
            wp_dequeue_script( 'wp_rp_edit_related_posts_js' );

            wp_deregister_style( 'contact-form-7' );
            wp_dequeue_style( 'contact-form-7' );
            wp_deregister_style( 'contact-form-7-rtl' );
            wp_dequeue_style( 'contact-form-7-rtl' );
            wp_deregister_style( 'wp_rp_edit_related_posts_css' );
            wp_dequeue_style( 'wp_rp_edit_related_posts_css' );
            wp_deregister_style( 'wp-pagenavi' );
            wp_dequeue_style( 'wp-pagenavi' );

            // don't include facebook script on front page
            remove_action( 'wp_footer', 'fbmlsetup', 100 );


            remove_action( 'wp_head', 'wp_rp_head_resources' );
        }
        // show tffaq css only on ask rabbi page
        if ( !is_page( ASK_RABBI_PAGE ) ) {
            wp_deregister_style( 'tffaq_jquery_custom' );
            wp_dequeue_style( 'tffaq_jquery_custom' );
            wp_deregister_style( 'tffaq_frontend' );
            wp_dequeue_style( 'tffaq_frontend' );
        }


        // get strings from language files, adn put them into javascript variables
        $params = array(
            'sLoadPosts' => __( "Load more", "kamoha" ),
            'sUnloadPosts' => __( 'Unload more', 'kamoha' ),
        );

// create inline definitions of these vars, for use in the script.js file
        wp_localize_script( 'script', 'MyScriptParams', $params );
        /* category page is designed like pinterest, so in those pages enqueue masonry */
        if ( is_archive() && !is_search() ) {
            wp_enqueue_script( 'masonry' );
        }

// make the ajaxurl var available to the script
        wp_localize_script( 'script', 'the_ajax_script', array('ajaxurl' => admin_url( 'admin-ajax.php' )) );
    }
}

add_action( 'wp_enqueue_scripts', 'kamoha_enqueu_scripts' );

/**
 * Create custom post type for newsflashe on sidebar
 */
function kamoha_init_theme(){
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
    /* Add admin editor style, but only on post page.
     * The init hool, however, only works on new post page.
     * In order to load editor style on edit post page, we need to hook onto pre_get_posts.
     * As per the explenation here: http://codex.wordpress.org/Function_Reference/add_editor_style  */
    add_editor_style();
}

add_action( 'init', 'kamoha_init_theme' );

/**
 * Add to extended_valid_elements for TinyMCE 
 * 
 * @param $init assoc. array of TinyMCE options 
 * @return $init the changed assoc. array 
 */
function change_mce_options( $init ){
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

// Lea 2014/09 - This filter is needed in order to prevent TinyMCE from stripping the onclick attribute from the input element
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
    function admin_thumb_column( $cols ){
        $cols['thumbnail'] = __( 'Thumbnail', 'kamoha' );
        return $cols;
    }

    /**
     * Shows the posts thumbnails in the thumbnail column added above
     * @param type $column_name
     * @param type $post_id
     */
    function admin_thumb_value( $column_name, $post_id ){
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
require_once get_stylesheet_directory() . '/inc/class-tgm-plugin-activation.php';

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
function kamoha_register_required_plugins(){

    /**
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(
        // This is an example of how to include a plugin from the WordPress Plugin Repository.
        array(
            'name' => 'Demo Tax meta class',
            'slug' => 'bainternet-Tax-Meta-Class',
            'source' => get_stylesheet_directory() . '/inc/plugins/bainternet-Tax-Meta-Class.zip', // The plugin source.
            'required' => false,
            'force_deactivation' => true,
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
            'force_deactivation' => true,
        ),
        array(
            'name' => 'Simple Share Buttons Adder',
            'slug' => 'simple-share-buttons-adder',
            'required' => false,
            'force_deactivation' => true,
        ),
        array(
            'name' => 'Responsive Menu',
            'slug' => 'responsive-menu',
            'required' => false,
            'force_deactivation' => true,
        ),
        array(
            'name' => 'WP-PageNavi',
            'slug' => 'wp-pagenavi',
            'required' => false,
            'force_deactivation' => true,
        ),
        array(
            'name' => 'T(-) Countdown',
            'slug' => 'jquery-t-countdown-widget',
            'required' => false,
        ),
        array(
            'name' => 'TF FAQ',
            'slug' => 'tf-faq',
            'source' => get_stylesheet_directory() . '/inc/plugins/tf-faq.zip', // The plugin source.
            'required' => false,
        ),
        array(
            'name' => 'WordPress Related Posts',
            'slug' => 'wordpress-23-related-posts-plugin',
            'required' => false,
            'force_deactivation' => true,
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
            'force_deactivation' => true,
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

function kamoha_add_excerpt_meta_box( $post_type ){
    if ( in_array( $post_type, array('post', 'page') ) ) {
        add_meta_box(
                'contact_details_meta', __( 'Excerpt', 'kamoha' ), 'post_excerpt_meta_box', $post_type, 'test', // change to something other then normal, advanced or side
                'high'
        );
    }
}

add_action( 'add_meta_boxes', 'kamoha_add_excerpt_meta_box' );

function kamoha_run_excerpt_meta_box(){
# Get the globals:
    global $post, $wp_meta_boxes;

# Output the "advanced" meta boxes:
    do_meta_boxes( get_current_screen(), 'test', $post );

# Remove the initial "advanced" meta boxes:
    //unset( $wp_meta_boxes[ 'post' ][ 'test' ] );
}

add_action( 'edit_form_after_title', 'kamoha_run_excerpt_meta_box' );

function kamoha_remove_normal_excerpt(){ /* this added on my own */
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

add_shortcode( 'gallery', 'kamoha_gallery_shortcode' );

function kamoha_gallery_shortcode( $atts ){
    $atts['link'] = 'file';
    return gallery_shortcode( $atts );
}

/* -----------------------------------------
 * Theme Customizer for holidays
 * ----------------------------------------- */
/* ---------------------------------------- */
add_action( 'customize_register', 'kamoha_customize_register_func' );

function kamoha_customize_register_func( $wp_customize ){
    /* Create a section - holiday header */
    $wp_customize->add_section( 'holiday_header_changer', array(
        'title' => __( 'Holiday Header', 'kamoha' ),
        'priority' => 25,
    ) );

    $wp_customize->add_setting( 'holiday_header', array(
        'default' => '',
        'transport' => 'postMessage',
    ) );

    $wp_customize->add_control( 'holiday_header', array(
        'label' => stripslashes( __( 'holiday_header', 'kamoha' ) ),
        'section' => 'holiday_header_changer',
        'type' => 'radio',
        'choices' => array(
            'regular' => __( 'regular', 'kamoha' ),
            'birthday' => __( 'birthday', 'kamoha' ),
            'trip_before_close' => __( 'trip before close', 'kamoha' ),
            'trip_after_close' => __( 'trip after close', 'kamoha' ),
            'shabbat_before_close' => __( 'shabbat before close', 'kamoha' ),
            'shabbat_early' => __( 'shabbat early', 'kamoha' ),
            'purim' => __( 'purim', 'kamoha' ),
            'pesah' => __( 'pesah', 'kamoha' ),
            'memorial' => __( 'memorial', 'kamoha' ),
            'independence' => __( 'independence', 'kamoha' ),
            'hanuka' => __( 'hanuka', 'kamoha' ),
        ),
    ) );
}

add_filter( 'body_class', 'mop_add_body_class' );

function mop_add_body_class( $classes ){
    $color = strtolower( get_theme_mod( 'holiday_header' ) );
    if ( $color != 'regular' ) {
        $classes[] = 'special';
    }
    $classes[] = $color;
    return $classes;
}

/**
 * Used by hook: 'customize_preview_init'
 *
 * @see add_action('customize_preview_init',$func)
 */
function kamoha_customizer_live_preview(){
    wp_enqueue_script(
            'kamoha-themecustomizer', //Give the script an ID
            get_template_directory_uri() . '/js/customize-themes.js', //Point to file
            array('jquery', 'customize-preview'), //Define dependencies
            '0.7.1', //Define a version (optional)
            true   //Put script in footer?
    );
}

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
function kamoha_menu_cat_subnav( $items, $menu, $args ){
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
function kamoha_modify_query( $query ){
    /* In homepage, get sticky post, or - if no sticky post exists - get the 7 newest posts */
    if ( $query->is_home() && $query->is_main_query() ) {
        global $sticky_exists;
        $sticky_exists = count( get_option( 'sticky_posts' ) ) > 0 ? true : false;
        if ( $sticky_exists ) {
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
// this happens in the admin part - load editor style on edit post page
    if ( stristr( $_SERVER['REQUEST_URI'], 'post.php' ) !== false ) {
        add_editor_style();
    }
}

// in homepage show 6 posts
add_filter( 'pre_get_posts', 'kamoha_modify_query' );

/* -----------------------------------------
 * posts_where function for events category
 * ----------------------------------------- */

function kamoha_posts_where( $where, &$query ){
    /* Add where clause to query in events category, and to event list in sidebar */
    if ( is_category( MEETINGS_CAT ) && !is_admin() && $query->is_main_query()  // condition for events category
            || (isset( $query->query_vars["category"] ) && $query->query_vars["category"] == MEETINGS_CAT && !$query->is_main_query()) // condition for event list in sidebar
    ) {
        if ( filter_input( INPUT_GET, 'pastposts' ) != NULL ) {
            $datedir = '<'; // get past events
        } elseif ( filter_input( INPUT_GET, 'futureposts' ) != NULL || (isset( $query->query_vars["category"] ) && $query->query_vars["category"] == MEETINGS_CAT) ) {
            $datedir = '>'; //get future events - in category page or in sidebar
        }
        $where .= " AND CONCAT(SUBSTR(SUBSTRING_INDEX(meta_value,'/',-1),1,4),'-',SUBSTR(SUBSTRING_INDEX(meta_value,'/',-2),1,2),'-',SUBSTR(SUBSTRING_INDEX(meta_value,'/',1),1,2)) " . $datedir . " '" . date( "Y-m-d" ) . "'";
    }
    return $where;
}

add_filter( 'posts_where', 'kamoha_posts_where', 2, 10 );

/**
 * Order posts by value in the meta-field "date", in descending order. Meant for past events
 * @param string $orderby_statement
 * @return string
 */
function edit_posts_orderby_desc( $orderby_statement ){
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
function edit_posts_orderby_asc( $orderby_statement ){
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
function kamoha_show_movie_icon(){
    $is_film = false;
    $is_audio = false;
    $post_id = get_the_ID();
    // look in content:
    $content = get_the_content();
    $movie_indicators = '/(object)|(youtube\.com\/embed)|(youtube\.com\/watch)/';
    preg_match( $movie_indicators, $content, $matches );
    if ( count( $matches ) > 0 && count( $matches[0] ) > 0 ) {
        $is_film = true;
    }

    if ( !$is_film ) {
        // look in tags
        $posttags = wp_get_post_tags( $post_id );
        foreach ( $posttags as $thistag ) {
            if ( $thistag->name == 'סרטון' ) {
                $is_film = 'true';
            } elseif ( $thistag->name == 'שמע' ) {
                $is_audio = true;
            }
        }

        if ( !$is_film ) {
            if ( (get_post_meta( $post_id, 'video', true ) == '1' ) ) {
                $is_film = true;
            } elseif ( (get_post_meta( $post_id, 'audio', true ) == '1' ) ) {
                $is_audio = true;
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
function kamoha_get_custom_post_type( $post_type ){
    $ret = '';
    $args = array('post_type' => $post_type);
    $posts_secondary = new WP_Query( $args );
    if ( $posts_secondary->have_posts() ) {
        $ret .= '<ul class="clear">'; // clear class is needed for lower resolutions, where the list items are floated
        $i = 1;
        while ( $posts_secondary->have_posts() ) {
            $posts_secondary->the_post();
            $post_format = get_post_type();
            $ret .= '<li class="news_' . $post_format . '" id=nf_' . $i++ . '>';
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
function kamoha_event_list(){
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
    <?php // add a button for laoding more posts. JS will hide the 2 last posts, and this button will toggle show/hide them                               ?>
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
function kamoha_tag_cloud_widget( $args ){
    $args['number'] = 10; //adding a 0 will display all tags
    return $args;
}

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
function kamoha_show_post_one_cat(){
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
function get_homepage_categories( $parent = 0 ){

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
function kamoha_order_categories_by( $order_by = 'ID' ){
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
 * Get the excerpt - for homepage and for single page
 * @return string
 */
function kamoha_get_facebook_page_description(){

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

/* * *******************************
 * AUXILIARY FUNCTIONS
 * ******************************* */

function kamoha_write_to_log( $data ){
    // Dump data
    ob_start();
    var_dump( $data );
    $contents = ob_get_contents();
    ob_end_clean();
    error_log( $contents, 3 );
}
