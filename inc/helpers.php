<?php
/**
 * Our own after setup function:
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

// define title lengths
    define( 'STICKY_TITLE_3_ROWS', 150 );
    define( 'STICKY_TITLE_2_ROWS', 110 );
    define( 'BLOCK1_TITLE_3_ROWS', 75 );
    define( 'BLOCK1_TITLE_2_ROWS', 53 );
    define( 'BLOCK2_TITLE_3_ROWS', 75 );
    define( 'BLOCK2_TITLE_2_ROWS', 53 );
    define( 'TAB_TITLE_3_ROWS', 75 );
    define( 'TAB_TITLE_2_ROWS', 50 );

// define excerpt lengths
    define( 'STICKY_EXCERPT_3_ROWS', 220 );
    define( 'STICKY_EXCERPT_2_ROWS', 200 );
    define( 'STICKY_EXCERPT_1_ROW', 250 );
    define( 'BLOCK1_EXCERPT_3_ROWS', 60 );
    define( 'BLOCK1_EXCERPT_2_ROWS', 80 );
    define( 'BLOCK1_EXCERPT_1_ROW', 180 );
    define( 'BLOCK2_EXCERPT_3_ROWS', 120 );
    define( 'BLOCK2_EXCERPT_2_ROWS', 160 );
    define( 'BLOCK2_EXCERPT_1_ROW', 180 );
    define( 'BLOCK3_EXCERPT_OTHER_POSTS', 250 );
    define( 'TAB_EXCERPT_3_ROWS', 50 );
    define( 'TAB_EXCERPT_2_ROWS', 70 );
    define( 'TAB_EXCERPT_1_ROW', 90 );

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
 * Create custom post type for newsflasher on sidebar
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
     * As per the explenation here: http://codex.wordpress.org/Function_Reference/add_editor_style
     * "Note that the pre_get_posts action hook is used to ensure that the post type is already
     * determined but, at the same time, that TinyMCE has not been configured yet.
     * That hook is not run when creating new posts, that is why we need to use it in combination
     * with the init hook to achieve a consistent result.  */
    if ( stristr( $_SERVER['REQUEST_URI'], 'post-new.php' ) !== false ) {
        add_editor_style();
    }
}

add_action( 'init', 'kamoha_init_theme' );

/**
 * Create shortcode for pelepay form
 */
function register_shortcodes(){
    add_shortcode( 'kamoha_pelepay_form', 'kamoha_insert_pelepay_form' );

    /**
     * Register a UI for the Shortcode.
     * Pass the shortcode tag (string)
     * and an array or args.
     */
//    shortcode_ui_register_for_shortcode(
//            'kamoha_pelepay_form', array(
//        // Display label. String. Required.
//        'label' => 'kamoha_pelepay_form',
//        // Icon/image for shortcode. Optional. src or dashicons-$icon. Defaults to carrot.
//        'listItemImage' => 'dashicons-editor-quote',
//        // Available shortcode attributes and default values. Required. Array.
//        // Attribute model expects 'attr', 'type' and 'label'
//        // Supported field types:** text, checkbox, textarea, radio, select, email, url, number, and date.  
//        'attrs' => array(
//            array(
//                'label' => 'first_option',
//                'attr' => 'first_option',
//                'type' => 'text',
//                'placeholder' => 'Firstname Lastname',
//                'description' => 'Optional',
//            ),
//        ),
//            )
//    );
}

add_action( 'init', 'register_shortcodes' );

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

/* ----------------------------------------------------------------
 * Get short excerpt, length defined by location, and title length
 * -------------------------------------------------------------- */

/**
 * Get a limited part of the content - sans html tags and shortcodes -
 * according to the amount written in $limit. Make sure words aren't cut in the middle
 * @param int $limit - number of characters
 * @return string - the shortened content
 */
function kamoha_the_short_excerpt( $limit ){
    $content = get_the_excerpt();
    /* sometimes there are <p> tags that separate the words, and when the tags are removed,
     * words from adjoining paragraphs stick together.
     * so replace the end <p> tags with space, to ensure unstickinees of words */
    $content = str_replace( '</p>', ' ', $content );
    $content = strip_tags( $content );
    $content = strip_shortcodes( $content );
    $ret = $content; /* if the limit is more than the length, this will be returned */
    if ( mb_strlen( $content ) >= $limit ) {
        $ret = mb_substr( $content, 0, $limit );
        // make sure not to cut the words in the middle:
        // 1. first check if the substring already ends with a space
        if ( mb_substr( $ret, - 1 ) !== ' ' ) {
            // 2. If it doesn't, find the last space before the end of the string
            $space_pos_in_substr = mb_strrpos( $ret, ' ' );
            // 3. then find the next space after the end of the string(using the original string)
            $space_pos_in_content = mb_strpos( $content, ' ', $limit );
            // 4. now compare the distance of each space position from the limit
            if ( $space_pos_in_content != false && $space_pos_in_content - $limit <= $limit - $space_pos_in_substr ) {
                /* if the closest space is in the original string, take the substring from there */
                $ret = mb_substr( $content, 0, $space_pos_in_content );
            } else {
                // else take the substring from the original string, but with the earlier (space) position
                $ret = mb_substr( $content, 0, $space_pos_in_substr );
            }
        }
        $ret .= '...';
    }
    return $ret;
}

/**
 * Show a certain number of characters from the excerpt, depending on the length of the title.
 * If the title is one line - bring more of the excerpt. The more lines the title is, the less
 * of the excerpt should we show.
 * There is, of course, a difference between the various blocks, and therefore that parameter is passed by the caller
 * */
function kamoha_the_short_excerpt_by_len(){
    global $homepage_part;
    $the_title = get_the_title();
    $title_len = mb_strlen( $the_title );
    $ret = '';
    switch ( $homepage_part ) {
        case HomepagePart::Sticky:
            /* case of three title rows */
            if ( $title_len >= STICKY_TITLE_3_ROWS ) {
                $ret = kamoha_the_short_excerpt( STICKY_EXCERPT_3_ROWS );
            } else {
                /* case of two title rows */
                if ( $title_len >= STICKY_TITLE_2_ROWS ) { //echo STICKY_EXCERPT_2_ROWS;
                    $ret = kamoha_the_short_excerpt( STICKY_EXCERPT_2_ROWS );
                } else {

                    /* case of one title row */
                    $ret = kamoha_the_short_excerpt( STICKY_EXCERPT_1_ROW );
                }
            }
            break;

        case HomepagePart::Newest:
            /* case of three title rows */
            if ( $title_len >= BLOCK1_TITLE_3_ROWS ) {
                $ret = kamoha_the_short_excerpt( BLOCK1_EXCERPT_3_ROWS );
            } else {

                /* case of two title rows */
                if ( $title_len >= BLOCK1_TITLE_2_ROWS ) {
                    $ret = kamoha_the_short_excerpt( BLOCK1_EXCERPT_2_ROWS );
                } else {

                    /* case of one title row */
                    $ret = kamoha_the_short_excerpt( BLOCK1_EXCERPT_1_ROW );
                }
            }
            break;

        case HomepagePart::Categories:
            /* case of three title rows */
            if ( $title_len >= BLOCK2_TITLE_3_ROWS ) {
                $ret = kamoha_the_short_excerpt( BLOCK2_EXCERPT_3_ROWS );
            } else {

                /* case of two title rows */
                if ( $title_len >= BLOCK2_TITLE_2_ROWS ) {
                    $ret = kamoha_the_short_excerpt( BLOCK2_EXCERPT_2_ROWS );
                } else {

                    /* case of one title row */
                    $ret = kamoha_the_short_excerpt( BLOCK2_EXCERPT_1_ROW );
                }
            }
            break;

        case HomepagePart::Blogs:
        case HomepagePart::Issues:
            global $blog_post_index;
            /* case of three title rows */
            if ( $title_len >= BLOCK1_TITLE_3_ROWS ) {
                $ret = kamoha_the_short_excerpt( TAB_EXCERPT_3_ROWS );
            } else {

                /* case of two title rows */
                if ( $title_len >= BLOCK1_TITLE_2_ROWS ) {
                    $ret = kamoha_the_short_excerpt( TAB_EXCERPT_2_ROWS );
                } else {

                    /* case of one title row */
                    $ret = kamoha_the_short_excerpt( TAB_EXCERPT_1_ROW );
                }
            }

            break;

        case HomepagePart::Tabs:

            /* case of three title rows */
            if ( $title_len >= TAB_TITLE_3_ROWS ) {
                $ret = kamoha_the_short_excerpt( TAB_EXCERPT_3_ROWS );
            } else {

                if ( $title_len >= TAB_TITLE_2_ROWS ) {/* case of two title rows */
                    $ret = kamoha_the_short_excerpt( TAB_EXCERPT_2_ROWS );
                } else {
                    $ret = kamoha_the_short_excerpt( TAB_EXCERPT_1_ROW );
                }
            }
            break;
    }
    return $ret;
}

/* ----------------------------------------------------------------
 * Get thumbnail (create one if none exists)
 * -------------------------------------------------------------- */

/**
 * Display the first image from the post, wherever you want
 *  'catch_that_image' from http://css-tricks.com/snippets/wordpress/get-the-first-image-from-a-post/
 * */
function kamoha_catch_that_image(){
    global $post, $posts;
    $first_img = '';
    ob_start();
    ob_end_clean();
    $output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches );
    if ( count( $matches ) > 0 && count( $matches[0] ) > 0 ) {
        $first_img = $matches[1][0];

        if ( empty( $first_img ) ) {
            $first_img = content_url() . "/uploads/2014/05/Logo-big.png";
        }
    }
    return $first_img;
}

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

/* ---------------------------------------------------
 * Calendar, Countdown watch, Next meetings functions
 * --------------------------------------------------- */

/**
 * Display event calendar with days that have posts as links.
 *
 * The calendar is cached, which will be retrieved, if it exists. If there are
 * no posts for the month, then it will not be displayed.
 *
 * First get gregorian date. Then convert it to hebrew date. Then
 *
 * @since 1.0.0
 *
 * @param int $catID Optional, default is false. If true, this function will use asSet to false for return.
 */
function kamoha_get_event_calendar( $catID = MEETINGS_CAT, $page_url ){

    global $wp_locale;
    $thisHmonthNum = $thisHyearNum = '';

    $cur_url = (empty( $page_url )) ? curPageURL() : $page_url;

    /* ----------------------------------------------------
     * Init relevant dates - gregorian, hewbrew, and julian
     * ---------------------------------------------------- */
    /* Let's figure out what the relevant month and year are */
    if ( strpos( $cur_url, 'month=' ) === false ) { // this is the current month
// Get first get the gregorian date of today
        $thisGdate = explode( "/", date( 'Y/m/d' ) );
        $thisGyear = $thisGdate[0];
        $thisGmonth = $thisGdate[1];
        $thisGday = $thisGdate[2];
// and then we derive the hebrew date
        $thisJdate = gregoriantojd( $thisGmonth, $thisGday, $thisGyear ); // first convert gregorian date to julian
        $thisHDateNUm = explode( "/", jdtojewish( $thisJdate ) ); // get the jewish date in numbers (i.e., 11/19/5774), and separate them into an array, so can be passed to jewishtojd
        $thisHmonthNum = $thisHDateNUm[0];
        $thisHyearNum = $thisHDateNUm[2];
        // in non-leap year, there isn't a 6th month. So in case of Adar (6), turn in into 7
        if ( $thisHmonthNum == 6 && !isJewishLeapYear( $thisHyearNum ) ) {
            $thisHmonthNum = 7;
        }
        $firstOfMonthJdate = jewishtojd( $thisHmonthNum, 1, $thisHyearNum ); // we need julian date of first of month for later use
    } else { // this is a different month, gotten to by the prev or next link
        /* here we start with the Jewish date - which is given in the url, and then convert it to the Gregorian date */
        /* We either get here by ajax - and this is what the condition checks - or by explicit url of type http://dev.linux.ort.org.il/kamoha/?month=577412#events_box */
        if ( !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {
            $thisHmonthNum = substr( $cur_url, strpos( $cur_url, 'month' ) + 10, 2 );
            $thisHyearNum = substr( $cur_url, strpos( $cur_url, 'month' ) + 6, 4 );
        } else {
            // Some urls are with old calendar parameters, like http://www.kamoha.org.il/?p=13017&month=201304
            $thisHmonthNum = substr( filter_input( INPUT_GET, 'month' ), 4, 2 );
            $thisHyearNum = substr( filter_input( INPUT_GET, 'month' ), 0, 4 );
            // check if gregorian date:
            if ( substr( $thisHyearNum, 0, 2 ) != '57' ) {
                //error_log($cur_url);
                if ( $thisHmonthNum == 13 ) { // can't be in gregorian year 
                    $thisHmonthNum = 12;
                }
                $thisJdate = gregoriantojd( $thisHmonthNum, 1, $thisHyearNum );
                $thisHDateNUm = explode( "/", jdtojewish( $thisJdate ) ); // get the jewish date in numbers (i.e., 11/19/5774), and separate them into an array, so can be passed to jewishtojd
                $thisHmonthNum = $thisHDateNUm[0];
                $thisHyearNum = $thisHDateNUm[2];
                //error_log($cur_url. ' ' . $thisHmonthNum . ' ' . $thisHyearNum);
                // take care of month 6 in non-leap year - turn it to 7
                if ( $thisHmonthNum == '6' && !isJewishLeapYear( $thisHyearNum ) ) {
                    $thisHmonthNum = 7;
                }
                if ( $thisHmonthNum == '13' && !isJewishLeapYear( $thisHyearNum ) ) {
                    $thisHmonthNum = 12;
                }
            }
        }
        $thisGdate = explode( "/", jdtogregorian( jewishtojd( $thisHmonthNum, 1, $thisHyearNum ) ) );
        $thisGmonth = zeroise( $thisGdate[0], 2 );
        $thisGyear = zeroise( $thisGdate[2], 2 );
        $thisGday = zeroise( $thisGdate[1], 2 );
        $firstOfMonthJdate = gregoriantojd( $thisGmonth, $thisGday, $thisGyear ); // we need julian date of first of month for later use
    }

    /* ----------------------------------------------------
     * Print the month title
     * ---------------------------------------------------- */
    kamoha_display_month_title( $thisGmonth, $thisGday, $thisGyear, $thisHmonthNum, $thisHyearNum );

    /* ----------------------------------------------------
     * Query DB for events posts, that happened in relevant month/year
     * ---------------------------------------------------- */

// Get hebrew month and year in string, for getting the events that happened in this month
    $thisHmonthStr = $thisHYearStr = '';
    kamoha_get_hebrew_date_str( $thisGmonth, $thisGday, $thisGyear, $thisHmonthStr, $thisHYearStr );
// Get the posts from the category that have dates that match the relevant month and year
    $events = new WP_Query( array(
        'meta_key' => 'date', // bring only posts that have a custom fiels called date
        'cat=' => $catID, // the posts have to belong to the meetings cat is
        'post__not_in' => get_option( 'sticky_posts' ),
        'meta_query' => array(// bring only posts who's date custom field match the 2 parameters:
            'relation' => 'AND', // both conditions have to be met
            array(// the date has to have the current year in it
                'key' => 'date',
                'value' => stripslashes( mb_substr( $thisHYearStr, 1 ) ), /* substr is to remove the leading ה in the year */
                'compare' => 'LIKE',
            ),
            array(// the date has to have the current month in it, between slashes
                'key' => 'date',
                'value' => $thisHmonthStr,
                'compare' => 'LIKE',
            )
        ),
            )
    );

    /* ----------------------------------------------------
     * Create array of events that happened in this month/year
     * ---------------------------------------------------- */
    $eventArr = array(); // array of all events
//
    // Loop through all posts returned by query, and create an object for each with post title, content, date and link
    foreach ( $events->posts as $event ) {
        /* post meta date field example:
         * 16-17/05/2014 ט"ז-י"ז באייר תשע"ד (פרשת בחוקותי)         */
        $date_field = explode( " ", get_post_meta( $event->ID, 'date', true ) ); // get the gregorian date from the field
        $full_date = explode( "/", $date_field[0] ); // divide it into day(s)/month/year
        $days = explode( "-", $full_date[0] ); // divide the day(s) part into seperate days
        $month = $full_date[1];
        $year = $full_date[2];

        foreach ( $days as $day ) {
            $postdata = array(
                "post_title" => $event->post_title,
                // "post_content" => strip_tags ( $event->post_content ),
                "guid" => $event->guid,
                "year" => $year,
                "month" => $month,
                "day" => $day
            );


            array_push( $eventArr, $postdata );
        }
    }
    wp_reset_postdata();


    /* ----------------------------------------------------
     * Display calendar
     * ---------------------------------------------------- */
    if ( !isset( $calendar_output ) ) {
        $calendar_output = '';
    }
    $calendar_output .= '<div class="calendar_wrap"><table class="wp-calendar">';

    /*     * ****** Begin build the calendar header - names of days of week ************* */
    $calendar_output .= '<thead>
	<tr>';

    $myweek = array();
    $week_begins = intval( get_option( 'start_of_week' ) ); /* week_begins = 0 stands for Sunday */
    for ( $wdcount = 0; $wdcount <= 6; $wdcount ++ ) {
        $myweek[] = $wp_locale->get_weekday( ($wdcount + $week_begins) % 7 );
    }

    foreach ( $myweek as $wd ) {
        $day_name = $wp_locale->get_weekday_abbrev( $wd );

        $calendar_output .= "\n\t\t<th scope=\"col\" title=\"$wd\">$day_name</th>";
    }

    $calendar_output .= '
	</tr>
	</thead>';
    /*     * ********* End build the calendar header  *************** */


    /*     * ****** Begin build the calendar body ******************** */
    $calendar_output .= '
	<tbody>
	<tr>';

// See how much we should pad in the beginning
    $pad = calendar_week_mod2( jddayofweek( $firstOfMonthJdate ) - $week_begins ); // 'w' is the numeric representation of the day of the week - 0 (for Sunday) through 6 (for Saturday)
    if ( 0 != $pad ) { // $pad is the number of days before the first of the month. Use it to make the first column span that number of columns
        $calendar_output .= "\n\t\t" . '<td colspan="' . esc_attr( $pad ) . '" class="pad">&nbsp;</td>';
    }

    $found_event = false; // was event found for this day in month

    $hebrew_days_dates = array('א', 'ב', 'ג', 'ד', 'ה', 'ו', 'ז', 'ח', 'ט', 'י', 'י"א', 'י"ב', 'י"ג', 'י"ד', 'ט"ו', 'ט"ז', 'י"ז', 'י"ח', 'י"ט', 'כ', 'כ"א', 'כ"ב', 'כ"ג', 'כ"ד', 'כ"ה', 'כ"ו', 'כ"ז', 'כ"ח', 'כ"ט', 'ל');

    /* get days in month to know the last date in current jewish month */
    $daysinmonth = cal_days_in_month( CAL_JEWISH, $thisHmonthNum, $thisHyearNum );

    $today = date( 'n/j/Y' );

    $num_days_last_week = 0; // needed to give colspan to last td in last row
    /*     * ** create a table cell for each day in month ** */
    for ( $day = 0; $day < $daysinmonth; ++$day ) { // loop for all days in current month
        if ( isset( $newrow ) && $newrow ) {
            $calendar_output .= "\n\t</tr>\n\t<tr>\n\t\t"; // close previous tr (if exists, and open a new one)
        }

        $newrow = false;

// get current gregorian date
        $currGdate = jdtogregorian( jewishtojd( $thisHmonthNum, $day + 1, $thisHyearNum ) );
        $curGdateArr = explode( "/", $currGdate );
        $currGday = $curGdateArr[1];
        $currGmonth = $curGdateArr[0];
        $currGyear = $curGdateArr[2];

// table cell with indication the current date (today)
        /* gmdate is like date, except return time is GMT
         * j is day of the month without leading zeros          */
        if ( $currGdate == $today ) {
            $calendar_output .= '<td class="today">';
        } else {
            $calendar_output .= '<td>';
        }


// check if eny event happened on this date
        $event_index = array();
        $event_counter = 0;
        foreach ( $eventArr as $event ) {
            if ( $event['day'] == $currGday && $event['month'] == $currGmonth && $event['year'] == $currGyear ) {
                $event_index[] = $event_counter;
            }

            $event_counter ++;
        }


// table cell with indication of an event date (event-day)
        if ( count( $event_index ) > 0 ) {
            $calendar_output .='<div class="event-day"><span class="jewish_daycal">' . $hebrew_days_dates[$day] . '</span><span class="gregorian_daycal"> ' . $currGday . '</span><div class="events">';
            for ( $i = 0; $i < sizeof( $event_index ); $i ++ ) {
                $calendar_output .= '<a href="' . $eventArr[$event_index[$i]]['guid'] . '" ><em class="event-day-title"> ' . $eventArr[$event_index[$i]]['post_title'] . '</em></a>'; // rel="tooltip"
            }
            $calendar_output .='</div></div>';
            $found_event = true;
        }

        /* If an event wasn't found for this date, create a regular table cell */
        if ( $found_event == false ) {
            $calendar_output .= "<span class=\"daycal\"><span class=\"jewish_daycal\">" . $hebrew_days_dates[$day] . "</span><span class=\"gregorian_daycal\"> " . $currGday . "</span></span>";
        } else { // else, initialize $found_event
            $found_event = false;
        }

        $calendar_output .= '</td>';

// if we've gotten to end of week, turn on $newrow so we closr the current <tr> and open a new one
        $num_days_last_week = calendar_week_mod2( jddayofweek( gregoriantojd( $currGmonth, $currGday, $currGyear ) ) - $week_begins );
        if ( 6 == $num_days_last_week )
            $newrow = true;
    }

// after looping through the days of the current month, check if the last row was finished. if not, add a <td> with padded colspan, and close the <tr>
    $pad = 7 - $num_days_last_week - 1;
    if ( $pad != 0 && $pad != 7 ) {
        $calendar_output .= "\n\t\t" . '<td class="pad" colspan="' . esc_attr( $pad ) . '">&nbsp;</td>';
    }

    $calendar_output .= "\n\t</tr>\n\t</tbody>\n\t</table>";

    /*     * ****** End build the calendar body ******************** */

    echo $calendar_output;

    echo '</div>';

    kamoha_display_next_prev_links( $thisHmonthNum, $thisHyearNum, $cur_url );
}

/**
 * Get the current page url. Used by kamoha_get_event_calendar, to determione what month should be displayed
 * @return string
 */
function curPageURL(){
    $pageURL = 'http';
    if ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" ) {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ( $_SERVER["SERVER_PORT"] != "80" ) {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

/**
 * Display the current hebrew and gregorian months and year
 * @param type $thisGmonth - current gregorian month
 * @param type $thisGday - current gregorian day
 * @param type $thisGyear - current gregorian year
 * @param type $thisHmonthNum - current hebrew month as int
 * @param type $thisHyearNum - current hebrew year as int
 */
function kamoha_display_month_title( $thisGmonth, $thisGday, $thisGyear, $thisHmonthNum, $thisHyearNum ){
    global $wp_locale;
// Get hebrew month and year in string
    $thisHmonthStr = $thisHYearStr = '';
    kamoha_get_hebrew_date_str( $thisGmonth, $thisGday, $thisGyear, $thisHmonthStr, $thisHYearStr );

// get the range of gregorian months the this hebrew month elapses
    $first_of_jewish_month_julian = jewishtojd( $thisHmonthNum, 01, $thisHyearNum );
    $first_of_jewish_month_gregorian = jdtogregorian( $first_of_jewish_month_julian );
    $gregorian_date_start = explode( "/", $first_of_jewish_month_gregorian ); // get gregorian date in numbers (i.e., 6/29/2014) and separate them into an array
    $startyear = $gregorian_date_start[2];
    $startmonth = $gregorian_date_start[0];

    /* get days in month to know the last date in current jewish month */
    $daysinmonth = cal_days_in_month( CAL_JEWISH, $thisHmonthNum, $thisHyearNum );
    $gregorian_date_end = explode( "/", jdtogregorian( jewishtojd( $thisHmonthNum, $daysinmonth, $thisHyearNum ) ) );
    $endyear = $gregorian_date_end[2];
    $endmonth = $gregorian_date_end[0];

    /* Print the name of the hebrew and gregorian month / months before calendar */
    /* log error if exists */
    if ( $startmonth == '0' ) {
        error_log( '$startmonth == 00' );
        error_log( $thisHmonthNum . ' ' . $thisHyearNum . ' ' . curPageURL() );
    }
    if ( $endmonth == '0' ) {
        error_log( '$endmonth == 00' );
        error_log( $thisHmonthNum . ' ' . $thisHyearNum . ' ' . curPageURL() );
    }

    if ( $startyear == $endyear ) {
        $calendar_output = '<h3 class="clear">'
                . '<span class="month_name_jew">' . $thisHmonthStr . ' ' . $thisHYearStr . '</span> \ '
                . '<span class="month_name_greg">' . $wp_locale->get_month( $startmonth );
        if ( $startmonth != $endmonth ) {
            $calendar_output.= ' - ' . $wp_locale->get_month( $endmonth );
        }
        $calendar_output .= ' ' . $startyear . '</span>'
                . '</h3>';
    } else {
        $calendar_output = '<h3 class="clear">'
                . '<span class="month_name_jew">' . $thisHmonthStr . ' ' . $thisHYearStr . '</span> \ '
                . '<span class="month_name_greg">' . $wp_locale->get_month( $startmonth ) . ' ' . $startyear . ' - ' . $wp_locale->get_month( $endmonth ) . ' ' . $endyear . '</span>'
                . '</h3>';
    }
    echo $calendar_output;
}

/**
 * Fill the value of $thisHmonthStr (for example תשרי), and $thisHYearStr (for example תשע"ד) according to given gregorian date
 * @param type $thisGmonth - gregorian month
 * @param type $thisGday - gregorian day
 * @param type $thisGyear - gregorian year
 * @param type $thisHmonthStr - passed by reference, get a value of hebrew month name  (כסלו)
 * @param type $thisHYearStr - passed by reference, get a value of hebrew year name  (תשנ"ו)
 */
function kamoha_get_hebrew_date_str( $thisGmonth, $thisGday, $thisGyear, &$thisHmonthStr, &$thisHYearStr ){
    $thisJdate = gregoriantojd( $thisGmonth, $thisGday, $thisGyear ); // first convert gregorian date to julian
    $thisHdateStr = explode( " ", iconv( 'WINDOWS-1255', 'UTF-8', jdtojewish( $thisJdate, true, CAL_JEWISH_ADD_GERESHAYIM ) ) ); // and then convert it to jewish
    $jewish_date_length = count( $thisHdateStr );
    $thisHYearStr = $thisHdateStr[$jewish_date_length - 1]; // the last item is the year. It's not always the 3rd item, because in Adar b' the 3rd item is the b'
    $thisHmonthStr = kamoha_get_heb_month_name( $thisHdateStr );
}

/**
 * Get number of days since the start of the week.
 *
 * @since 1.5.0
 * @usedby get_calendar()
 *
 * @param int $num Number of day.
 * @return int Days since the start of the week.
 */
function calendar_week_mod2( $num ){
    $base = 7;
    return ($num - $base * floor( $num / $base ));
}

/**
 * Get the Hebrew month name from full date.
 * The hewbrew month could be the 2nd or 3rd item in the array, because of אדר ב which is separated to 2 items
 * Also correct חשון to מרחשוון and סיון to סיוון
 * @param type $hebDate - date array
 * @return string - hebrew month name (אייר)
 */
function kamoha_get_heb_month_name( $hebDate ){
    if ( count( $hebDate ) == 3 ) {
        $ret = $hebDate[1];
    } else {
        $ret = substr( $hebDate[1], 1 ) . ' ' . $hebDate[2] . '\''; //
    }
    switch ( $ret ) {
        case 'חשון':
            $ret = 'מרחשוון';
            break;
        case 'סיון' :
            $ret = 'סיוון';
    }
    return $ret;
}

/**
 * Create links for next and previous months
 * @param type $thismonth
 * @param type $thisyear
 */
function kamoha_display_next_prev_links( $thismonth, $thisyear, $page_url ){
// Calculate next month and year, for links.
    $prevurl = $nexturl = $page_url;
    $prevmonth = '' . zeroise( $thismonth - 1, 2 );
    $nextmonth = '' . zeroise( $thismonth + 1, 2 );
    switch ( $thismonth ) {
        case '01':
            $prevyear = '' . zeroise( $thisyear - 1, 2 );
            $nextyear = '' . zeroise( $thisyear, 2 );
            $prevmonth = zeroise( 13, 2 );
            break;
        case '13':
            $prevyear = '' . zeroise( $thisyear, 2 );
            $nextyear = '' . zeroise( $thisyear + 1, 2 );
            $nextmonth = '' . zeroise( 1, 2 );
            break;
        case '05':
            if ( isJewishLeapYear( $thisyear ) ) {
                /* In both leap years and and non-leap years, jdtojewish, jewishtojd, and cal_days_in_month expects 13 months.
                 * In non-leap years, cal_days_in_month returns 0 for month 6, and for jdtojewish and jewishtojd return the same data (אדר) for both month 6 and month 7
                 * This means that month 6 has to be skipped...  cal_days_in_month is documented here: https://bugs.php.net/bug.php?id=61185 */
                $prevyear = '' . zeroise( $thisyear, 2 );
                $nextyear = '' . zeroise( $thisyear, 2 );
            } else {
                $prevyear = '' . zeroise( $thisyear, 2 );
                $nextyear = '' . zeroise( $thisyear, 2 );
                $nextmonth = '' . zeroise( 7, 2 );
            }
            break;
        case '07':
            if ( isJewishLeapYear( $thisyear ) ) {
                /* In both leap years and and non-leap years, jdtojewish, jewishtojd, and cal_days_in_month expects 13 months.
                 * In non-leap years, cal_days_in_month returns 0 for month 6, and for jdtojewish and jewishtojd return the same data (אדר) for both month 6 and month 7
                 * This means that month 6 has to be skipped...  cal_days_in_month is documented here: https://bugs.php.net/bug.php?id=61185 */
                $prevyear = '' . zeroise( $thisyear, 2 );
                $nextyear = '' . zeroise( $thisyear, 2 );
            } else {
                $prevyear = '' . zeroise( $thisyear, 2 );
                $nextyear = '' . zeroise( $thisyear, 2 );
                $prevmonth = '' . zeroise( 5, 2 );
            }
            break;

        default :
            $prevyear = '' . zeroise( $thisyear, 2 );
            $nextyear = '' . zeroise( $thisyear, 2 );
            break;
    }

// Prev/Next month links, with anchor to the events box
    $prevurl = add_query_arg( 'month', $prevyear . $prevmonth, str_replace( $prevurl, '#events_box', '' ) ) . '#events_box';
    $nexturl = add_query_arg( 'month', $nextyear . $nextmonth, str_replace( $nexturl, '#events_box', '' ) ) . '#events_box';

// Prev/Next month buttons
    echo '<span class="prev_next_links prevmonth"><a href="' . esc_url( $prevurl ) . '" class="ajax-link">&lt; ' . __( 'Previous month', 'kamoha' ) . ' </a></span>';
    echo '<span class="prev_next_links nextmonth"><a href="' . esc_url( $nexturl ) . '" class="ajax-link">' . __( 'Next month', 'kamoha' ) . ' &gt; </a></span>';
}

function isJewishLeapYear( $year ){
    if ( $year % 19 == 0 || $year % 19 == 3 || $year % 19 == 6 ||
            $year % 19 == 8 || $year % 19 == 11 || $year % 19 == 14 ||
            $year % 19 == 17 ) {
        return true;
    } else {
        return false;
    }
}

/**
 * The function taking care of the ajax request for calendar.
 */
function kamoha_calendar_ajax_process_request(){
// first check if data is being sent and that it is the data we want
    if ( isset( $_POST["month"] ) ) {
// now set our response var equal to that of the POST var (this will need to be sanitized based on what you're doing with with it)
        $response = $_POST["month"];
        ?>
        <?php
        kamoha_get_event_calendar( MEETINGS_CAT, $response );
        die();
    }
}

add_action( 'wp_ajax_calendar_response', 'kamoha_calendar_ajax_process_request' );
add_action( 'wp_ajax_nopriv_calendar_response', 'kamoha_calendar_ajax_process_request' );

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
    <?php // add a button for laoding more posts. JS will hide the 2 last posts, and this button will toggle show/hide them                              ?>
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

/* -----------------------------------------
 * Latest Comments functions
 * ----------------------------------------- */

/**
 * Show latest comments. Adapted from the default-widget comments function
 * */
function kamoha_comments_widget(){
    global $comments, $comment;
    $args = array();

    $cache = wp_cache_get( 'widget_recent_comments', 'widget' );

    if ( !is_array( $cache ) )
        $cache = array();

    if ( !isset( $args['widget_id'] ) )
        $args['widget_id'] = 'recent-comments-2';

    if ( isset( $cache[$args['widget_id']] ) ) {
        echo $cache[$args['widget_id']];
        return;
    }

    $output = '';

    $title = __( 'Recent Comments', 'kamoha' );
    $before_title = '<h3 class="aside_title">';
    $after_title = '</h3>';
    $number = 5;

    $comments = get_comments( apply_filters( 'widget_comments_args', array('number' => $number, 'status' => 'approve', 'post_status' => 'publish') ) );
    if ( $title )
        $output .= $before_title . $title . $after_title;

    $output .= '<section class="aside_content">';
    $output .= '<ul id="recentcomments">';
    if ( $comments ) {
        // Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
        $post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
        _prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );

        foreach ( (array) $comments as $comment ) {
            $comment_author = $comment->comment_author == '' ? __( 'anonymous user', 'kamoha' ) : strip_tags( $comment->comment_author );
            $output .= '<li class="recentcomments">' .
                    '<a href="' . get_permalink( $comment->ID ) . '#comment-' . $comment->comment_ID . '">' .
                    '<span class="comment_name">' . $comment_author . ' </span>' .
                    ' ' . __( 'about', 'kamoha' ) . ' ' .
                    '<span class="comment_post_name">' . get_the_title( $comment->comment_post_ID ) . '</span>: ' .
                    '<span class="comment_content">' . mb_substr( strip_tags( $comment->comment_content ), 0, 60 ) . '...</span>' .
                    '<div class="comment_date">' . get_hebrew_date( get_comment_date( 'm' ), get_comment_date( 'd' ), get_comment_date( 'Y' ) ) . ', ' . get_comment_date() . '</div>' .
                    '</a>' .
                    '</li>';
        }
    }
    $output .= '</ul>';
    $output .= '<div id="moreComments" class="toOpen showMore">' . __( 'Load more', 'kamoha' ) . ' </div>';
    $output .= '</section>';

    echo $output;
    $cache[$args['widget_id']] = $output;
    wp_cache_set( 'widget_recent_comments', $cache, 'widget' );
}

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

/**
 * This function shows the current post's thumbnail (if defined) by the size passed,
 * or if there isn't a post thumbnail, it gets the first port image, and displays the passed thumbsized version of it
 * @param string $thumb_size - the size of the thumbnail, from a predefines size list
 */
function kamoha_show_homepage_thumbnail( $thumb_size = 'medium' ){

    /* declare vars */
    $post_id = get_the_ID(); // needed to get the correct thumbnail, or correct user field

    if ( has_post_thumbnail() ) { // if the post already has a thumbnail, just get it, using the passed size
        /* There are some thumbnails which don't crop well when saved as square.
         * In these cases, we'll upload 2 images - one rectangle and one square, using the Multi Post Thumbnails plugin.
         * The square one will be the featured image, because most of the theme uses the square format, as do
         * the Related Posts and the Popular Posts plugins.
         * The rectangle one will be uploaded as secondary image.
         * In our code, we look for the secondary image. If such an image exists (most of the time it won't exists,
         * and if the thumbnail size is medium - that's the only size that's rectangle - then show the secondary image. 
         */
        $second_img = '';

        if ( class_exists( 'MultiPostThumbnails' ) ) :
            $second_img = MultiPostThumbnails::get_the_post_thumbnail(
                            get_post_type(), 'secondary-image'
            );
        endif;

        /* If a secondary image exists, show it when the thumbnail size is rectangle */
        if ( !empty( $second_img ) && $thumb_size == 'medium' ) {
            echo $second_img;
        } else {
            echo get_the_post_thumbnail( null, $thumb_size );
        }
    } else {
        /* if the post doesn't have a thumbnail, the first place to look for it is the userfiled 'thumb'.
          if there is no img there, then get the first image in the post */

        $imgsrc = get_post_meta( $post_id, 'thumb', true ); // first get the value of thumb

        if ( empty( $imgsrc ) ) {

            /* if no value in thumb, get the first image from the post */
            $imgsrc = kamoha_catch_that_image();
        }

        if ( !empty( $imgsrc ) ) {
            /* proceed only if an image was found */

            $attachment_id = 0; /* the id of the image that will become the thumbnail */

            $orig_imgsrc = kamoha_get_img_src_without_size( $imgsrc ); // get the image without its size part of its name

            /* Sometimes a url of an external image is used in the user field, or in the post.
             * If that's the case, then first the image has to be uploaded as an attachment to the post   */
            if ( is_external_image( $imgsrc ) ) {

                /* check if full size image exists */
                if ( true === file_get_contents( $orig_imgsrc ) ) {
                    $attachment_id = handle_sideload_and_get_id( $orig_imgsrc, $post_id, '' ); // try to get the id of the original image
                } else {
                    $attachment_id = handle_sideload_and_get_id( $imgsrc, $post_id, '' ); // get the id of the image found in the user field or post
                }
            } else {
                /* If the image is alreay on the site, get its id. */
                $attachment_id = kamoha_get_attachment_id_from_url( $orig_imgsrc ); // try to get the id of the original image

                if ( !$attachment_id ) {
                    // if the id of the sizeless image doesn't exist, use the image that was found
                    $attachment_id = kamoha_get_attachment_id_from_url( $imgsrc );
                }
                if ( !$attachment_id ) {
                    /* check if full size image exists */
                    if ( true === file_get_contents( $orig_imgsrc ) ) {
                        $attachment_id = handle_sideload_and_get_id( $orig_imgsrc, $post_id, '' ); // try to get the id of the original image
                    } else {
                        $attachment_id = handle_sideload_and_get_id( $imgsrc, $post_id, '' ); // get the id of the image found in the user field or post
                    }
                }
            }

            $success = add_post_meta( $post_id, '_thumbnail_id', $attachment_id );

            echo get_the_post_thumbnail( null, $thumb_size );
        }
    }
}

/**
 * If an image src has its size as part of its name, remove the size.
 * This is needed in order to get to the orginal image
 * @param string $imgsrc
 * @return image url without size as part of name
 */
function kamoha_get_img_src_without_size( $imgsrc ){
    $ret = $imgsrc;
    /* remove the size from end of filename */
    $last_hyphen = strrpos( $imgsrc, '-' );
// make sure it's the right hyphen
    $last_dot = strrpos( $imgsrc, '.' );
    $file_type = substr( $imgsrc, $last_dot );
    if ( $last_dot - $last_hyphen > 10 || !strpos( $imgsrc, 'x', $last_hyphen ) ) {
        $last_hyphen = strlen( $imgsrc ) - strlen( $file_type );
    }
    if ( $last_hyphen == -1 ) {
        $last_hyphen = null;
    }
    $ret = mb_substr( $imgsrc, 0, $last_hyphen ) . $file_type; //. '-' . $thumb_size 
    return $ret;
}

/**
 * Check if url is local or external
 * @param type $url
 * @return true if external, false if local
 */
function is_external_image( $url ){

    $dir = wp_upload_dir();

    // baseurl never has a trailing slash
    if ( false === strpos( $url, $dir['baseurl'] . '/' ) ) {
        // URL points to a place outside of upload directory
        return true;
    }

    return false;
}

/**
 * Get the ID of a WordPress image attachment from the image URL 
 * https://philipnewcomer.net/2012/11/get-the-attachment-id-from-an-image-url-in-wordpress/
 * @global type $wpdb
 * @param type $attachment_url
 * @return type
 */
function kamoha_get_attachment_id_from_url( $attachment_url = '' ){

    global $wpdb;
    $attachment_id = false;

    // If there is no url, return.
    if ( '' == $attachment_url )
        return;

    // Get the upload directory paths
    $upload_dir_paths = wp_upload_dir();

    // Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
    if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

        // If this is the URL of an auto-generated thumbnail, get the URL of the original image
        $attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

        // Remove the upload path base directory from the attachment URL
        $attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

        // Finally, run a custom database query to get the attachment ID from the modified attachment URL
        $attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );
    }

    return $attachment_id;
}

/**
 * Upload an image from a given url as a post thumbnail
 * @param type $url
 * @param type $post_id
 * @param type $description
 * @return type
 */
function handle_sideload_and_get_id( $url, $post_id, $description ){

    require_once ( ABSPATH . "wp-admin" . '/includes/file.php' );
    require_once ( ABSPATH . "wp-admin" . '/includes/media.php' );
    require_once ( ABSPATH . "wp-admin" . '/includes/image.php' );
    $id = '';
    $file = download_url( $url );

    // Set variables for storage
    // fix file filename for query strings
    preg_match( '/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $url, $matches );
    $file_array = array();
    if ( count( $matches ) > 0 ) {
        $file_array['name'] = basename( $matches[0] );
        $file_array['tmp_name'] = $file;

        // If error storing temporarily, unlink
        if ( is_wp_error( $file ) ) {
            @unlink( $file_array['tmp_name'] );
            $file_array['tmp_name'] = '';
        }

        // do the validation and storage stuff
        $id = media_handle_sideload( $file_array, $post_id, $description );
        // If error storing permanently, unlink
        if ( is_wp_error( $id ) ) {
            @unlink( $file_array['tmp_name'] );
        }

        set_post_thumbnail( $post_id, $id );
    }

    return $id;
}

/* * *********************************************** */
/* * ***********  Comments functions *************** */
/* * *********************************************** */

/**
 * Output a comment in the HTML5 format.
 *
 * @access protected
 * @since 3.6.0
 *
 * @see wp_list_comments()
 *
 * @param object $comment Comment to display.
 * @param int    $depth   Depth of comment.
 * @param array  $args    An array of arguments.
 */
function kamoha_comment( $comment, $args, $depth ){
    ?>
    <li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? 'clear' : 'clear parent'  ); ?>>
        <article id="div-comment-<?php comment_ID(); ?>" class="comment-body clear">
            <footer class="comment-meta">
                <div class="comment-author vcard">
                    <?php printf( __( '%s <span class="says">says:</span>', 'kamoha' ), sprintf( '<b class="fn">%s</b>', get_comment_author_link() ) ); ?>
                </div><!-- .comment-author -->

                <div class="comment-metadata">
                    <a href="<?php echo esc_url( get_comment_link( $comment->comment_ID, $args ) ); ?>">
                        <time datetime="<?php comment_time( 'c' ); ?>">
                            <?php printf( _x( '%1$s, %2$s at %3$s', '1: hewbrewdate, 2: date, 3:time', 'kamoha' ), get_hebrew_date( get_comment_date( 'm' ), get_comment_date( 'd' ), get_comment_date( 'Y' ) ), get_comment_date(), get_comment_time() ); ?>
                        </time>
                    </a>


                    <?php edit_comment_link( __( 'Edit', 'kamoha' ), '<span class="edit-link">', '</span>' ); ?>
                </div><!-- .comment-metadata -->

                <?php if ( '0' == $comment->comment_approved ) : ?>
                    <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'kamoha' ); ?></p>
                <?php endif; ?>
            </footer><!-- .comment-meta -->
            <!-- commenter img -->
            <?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
            <div class="comment-content">

                <?php comment_text(); ?>

                <div class="reply">
                    <?php comment_reply_link( array_merge( $args, array('add_below' => 'div-comment', 'depth' => $depth, 'max_depth' => $args['max_depth']) ) ); ?>
                </div><!-- .reply -->

            </div><!-- .comment-content -->


        </article><!-- .comment-body -->
        <?php
    }

    /**
     * Remove the url fiels from comments form.
     * This function also altered the label and input fields of name and email,
     * in order to apply a cute style, but it counted on those fields being required,
     * which isn't the case in this site.
     * Am leaving the code commented out, in order to get back to it and try to make it 
     * @param type $fields
     * @return $fields
     */
    function kamoha_alter_comment_form_fields( $fields ){
        // $commenter = wp_get_current_commenter();
        // $req = get_option( 'require_name_email' );
        // $aria_req = ( $req ? " aria-required='true'" : '' );
        // $normal_req = ( $req ? " required=''" : '' );
        // $html5 = 'html5' === current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
        // $fields[ 'author' ] = '<p class="comment-form-author">' .
        // '<input id="author" name="author" type="text" value="' . esc_attr( $commenter[ 'comment_author' ] ) . '" size="30"' . $aria_req . $normal_req . ' />' .
        // '<label for="author" alt="' . __( 'Name' ) . ( $req ? ' *' : '' ) . '" placeholder="' . __( 'Name' ) . ( $req ? ' *' : '' ) . '"> </label>'
        // . '</p> ';
        // $fields[ 'email' ] = '<p class="comment-form-email">' .
        // '<input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr( $commenter[ 'comment_author_email' ] ) . '" size="30"' . $aria_req . $normal_req . ' />' .
        // '<label for="email" alt="' . __( 'Email' ) . ( $req ? ' *' : '' ) . '" placeholder="' . __( 'Email' ) . ( $req ? ' *' : '' ) . '">  </label> ' .
        // '</p>';

        $fields['url'] = '';  //removes website field
        return $fields;
    }

    add_filter( 'comment_form_default_fields', 'kamoha_alter_comment_form_fields' );

    function get_hebrew_date( $month, $day, $year ){
        $jew_date = jdtojewish( gregoriantojd( $month, $day, $year ), true, CAL_JEWISH_ADD_GERESHAYIM );
        $jew_date = iconv( 'WINDOWS-1255', 'UTF-8', $jew_date );

        // (Lea 06/2014) add מר before חשון and add ב before month, as Hebrew standards dictates
        $jew_date = explode( ' ', $jew_date );
        if ( $jew_date[1] == 'חשון' ) {
            $jew_date[1] = 'מר' . $jew_date[1];
        }

        // in אדר ב' move the apostrophe from before the אדר to after the ב
        if ( mb_strpos( $jew_date[1], '\'' ) === 0 ) {
            $jew_date[1] = mb_substr( $jew_date[1], 1 );
            $jew_date[2] .= '\'';
        }

        $jew_date[1] = 'ב' . $jew_date[1];
        $jew_date = implode( $jew_date, ' ' );

        return $jew_date;
    }

    /*     * *********************************************************** */
    /*     * ***********  Facebook header info functions *************** */
    /*     * *********************************************************** */

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

    /*     * *******************************
     * PELEPAY FORM SHORTOCDE
     * ******************************* */
    /* add button to tinymce, for inserting pelepay form */

    /**
     * Add tinymce button for form shortcode, using admin_head action
     */
    add_action( 'admin_head', 'kamoha_add_my_tc_button' );

    function kamoha_add_my_tc_button(){
        global $typenow;
        // check user permissions
        if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
            return;
        }

        // verify the post type
        if ( !in_array( $typenow, array('post', 'page') ) )
            return;
        // check if WYSIWYG is enabled
        if ( get_user_option( 'rich_editing' ) == 'true' ) {
            add_filter( "mce_external_plugins", "kamoha_add_tinymce_plugin" );
            add_filter( 'mce_buttons', 'kamoha_register_my_tc_button' );
        }
    }

    /**
     * Called by kamoha_add_my_tc_button, adds our tinymce button's js file to plugin array
     * @param array $plugin_array
     * @return $plugin_array
     */
    function kamoha_add_tinymce_plugin( $plugin_array ){
        $plugin_array['kamoha_tc_button'] = get_template_directory_uri() . '/js/pelepay_form.js';
        return $plugin_array;
    }

    /**
     * Called by kamoha_add_my_tc_button, adds our button name to  the buttons array. This name is the one used in the editor.addButton in the js file
     * @param type $buttons
     * @return $buttons
     */
    function kamoha_register_my_tc_button( $buttons ){
        array_push( $buttons, "kamoha_tc_button" );
        return $buttons;
    }

    /**
     * In order to give the tinymce button its own style, add a css file to admin
     */
    function kamoha_tc_css(){
        wp_enqueue_style( 'kamoha-tc', get_template_directory_uri() . '/css/tinymce.css' );
    }

    add_action( 'admin_enqueue_scripts', 'kamoha_tc_css' );

    /* Create shortcode functions */

    /**
     * Get the attributes from the shortcode:
     * The price list, the text to attach to each price, and the fist option that is displayd
     * @param type $atts - shortcode attributes
     * @return the HTML to insert instead of shortcode
     */
    function kamoha_insert_pelepay_form( $atts ){
        $first_option_text = $atts['first_option'];
        $price_list = explode( ',', $atts['price_list'] );
        $price_text = explode( ',', $atts['price_text'] );
        $price_len = count( $price_list );
        /* create dropdown list with options from attributes */
        $ret = '<select id="amount">' .
                '<option value="0">' . $first_option_text . '</option>';
        for ( $i = 0; $i < $price_len; $i ++ ) {
            $ret .='<option value="' . trim( $price_list[$i] ) . '">' . trim( $price_text[$i] ) . '</option>';
        }
        $ret .= '</select>';
        /* save number of payments */
        $payments = empty( $atts['payments'] ) ? 1 : $atts['payments'];
        /* create the pelepay form with hiiden fields, and a button which a function is attached to its click event */
        $ret .= '<form action="https://www.pelepay.co.il/pay/paypage.aspx" method="post" name="pelepayform">'
                . '<input name="business" type="hidden" value="kamoha.or@gmail.com" />'
                . '<input name="amount" type="hidden" value="" />'
                . '<input name="orderid" type="hidden" value="" />'
                . '<input name="description" type="hidden" value="_chart_shopp" />'
                . '<input id="pelepay_submit" alt="Make payments with pelepay" name="pelepay_submit" src="https://www.pelepay.co.il/images/banners/respect_pp_8c.gif" type="image" />'
                . '<input type="hidden" name="max_payments" value="' . $payments . '">'
                . '</form>';
        return $ret;
    }

    /**
     * Add translation file to tinymce plugin
     * @param array $locales
     * @return string
     */
    function kamoha_add_my_tc_button_lang( $locales ){
        $locales['kamoha_tc_button'] = get_template_directory() . '/inc/pelepay_tinymce_btn_lang.php';
        return $locales;
    }

    add_filter( 'mce_external_languages', 'kamoha_add_my_tc_button_lang' );

    /*     * *******************************
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
    