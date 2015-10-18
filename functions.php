<?php
/**
 * kamoha functions and definitions
 *
 * @package kamoha
 */
/**
 * Set the content width based on the theme's design and stylesheet.
 */
if (!isset($content_width)) {
    $content_width = 640; /* pixels */
}

if (!function_exists('kamoha_setup')) :

    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function kamoha_setup() {

        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on kamoha, use a find and replace
         * to change 'kamoha' to the name of your theme in all the template files
         */
        load_theme_textdomain('kamoha', get_template_directory() . '/languages');

// Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
         */
        add_theme_support('post-thumbnails');

// This theme uses wp_nav_menu() in two locations.
        register_nav_menus(array(
            'primary' => __('Primary Menu', 'kamoha'),
            'secondary' => __('Secondary Navigation', 'kamoha'), /* Lea - 02/2014 - add another menu */
        ));


        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

// Enable support for HTML5 markup.
        add_theme_support('html5', array('comment-list', 'search-form', 'comment-form',));
    }

endif; // kamoha_setup
add_action('after_setup_theme', 'kamoha_setup');

if (!function_exists('_wp_render_title_tag')) {

    function kamoha_render_title() {
        ?>
        <title><?php wp_title('|', true, 'right'); ?></title>
        <?php
    }

    add_action('wp_head', 'kamoha_render_title');
}

/**
 * Register widgetized area and update sidebar with default widgets.
 */
function kamoha_widgets_init() {
    register_sidebar(array(
        'name' => __('Countdown Sidebar', 'kamoha'), /* (Lea 2014/05) - change sidebar name */
        'id' => 'sidebar-countdown',
        'before_widget' => '<section id="%1$s" class="aside_content %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ));
    register_sidebar(array(
        'name' => __('Related Posts Sidebar', 'kamoha'), /* (Lea 2014/07) - add sidebar for single page */
        'id' => 'sidebar-related-posts',
        'before_widget' => '<section id="%1$s" class="aside_content %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ));
}

add_action('widgets_init', 'kamoha_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function kamoha_scripts() {

    if (!is_admin()) { // all scripts and styles are front end
        // Style.css belongs everywhere execept in the site documentation page template:
        if (!is_page_template('page_site_documentation.php')) {

            wp_enqueue_style('kamoha-style', get_stylesheet_uri(), array(), '1.6.1.8');

            // The following scripts and styles belong on every regular site page, except the 2 page templates
            if (!is_page_template('page_without-header-and-footer.php')) {

                wp_enqueue_script('kamoha-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true);

                wp_enqueue_script('kamoha-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true);

                wp_enqueue_script('kamoha-script', get_template_directory_uri() . '/js/script.js', array('jquery'), 1, TRUE);

                if (is_singular() && comments_open() && get_option('thread_comments')) {
                    wp_enqueue_script('comment-reply');
                }
            }
        } else { // styles & scripts only for documentation page
            wp_enqueue_style('kamoha-colorbox-style', get_template_directory_uri() . '/site_documentation/colorbox.css');
            wp_enqueue_style('kamoha-documentation-style', get_template_directory_uri() . '/site_documentation/page_site_documentation.css');
            //image mapster taken from here: http://www.outsharked.com/imagemapster
            wp_enqueue_script('kamoha-imagemapster-script', get_template_directory_uri() . '/site_documentation/jquery.imagemapster.min.js', array(), '20130115', true);
            wp_enqueue_script('kamoha-colorbox-script', get_template_directory_uri() . '/site_documentation/jquery.colorbox-min.js', array(), '20130115', true);
            wp_enqueue_script('kamoha-documentation-script', get_template_directory_uri() . '/site_documentation/page_site_documentation.js', array(), '20130115', true);
        }

        /* remove uneeded scripts from homepage and page template */
        if (is_home() || is_page_template('page_without-header-and-footer.php') || is_page_template('page_site_documentation.php')) {
            wp_deregister_script('jquery-form');
            wp_dequeue_script('jquery-form');
            wp_deregister_script('contact-form-7');
            wp_dequeue_script('contact-form-7');
            wp_deregister_script('wp_rp_edit_related_posts_js');
            wp_dequeue_script('wp_rp_edit_related_posts_js');

            wp_deregister_style('contact-form-7');
            wp_dequeue_style('contact-form-7');
            wp_deregister_style('contact-form-7-rtl');
            wp_dequeue_style('contact-form-7-rtl');
            wp_deregister_style('wp_rp_edit_related_posts_css');
            wp_dequeue_style('wp_rp_edit_related_posts_css');
            wp_deregister_style('wp-pagenavi');
            wp_dequeue_style('wp-pagenavi');

            /* unneeded scripts and styles that are added via hooks: */
            remove_action('wp_footer', 'fbmlsetup', 100);
            remove_action('wp_head', 'wp_rp_head_resources');
        }

        /* Show tffaq css only on ask rabbi page */
        if (!is_page(ASK_RABBI_PAGE)) {
            wp_deregister_style('tffaq_jquery_custom');
            wp_dequeue_style('tffaq_jquery_custom');
            wp_deregister_style('tffaq_frontend');
            wp_dequeue_style('tffaq_frontend');
        }


        /* get strings from language files, adn put them into javascript variables */
        $params = array(
            'sLoadPosts' => __("Load more", "kamoha"),
            'sUnloadPosts' => __('Unload more', 'kamoha'),
        );

        /* Create inline definitions of these vars, for use in the script.js file */
        wp_localize_script('kamoha-script', 'MyScriptParams', $params);
        /* category page is designed like pinterest, so in those pages enqueue masonry */
        if (is_archive() && !is_search()) {
            wp_enqueue_script('masonry');
        }

        /* Make the ajaxurl var available to the script */
        wp_localize_script('script', 'the_ajax_script', array('ajaxurl' => admin_url('admin-ajax.php')));

        wp_enqueue_style('kamoha-fonts', kamoha_fonts_url(), array(), null);

        /* Add icomoon font-family deinition inline, for better performace: */
        $kamoha_custom_css = "
            @font-face {
            font-family: 'icomoon';
            src:url('" . get_template_directory_uri() . "/fonts/icomoon.eot?-416wh5');"
                . "src:url('" . get_template_directory_uri() . "/fonts/icomoon.eot?#iefix-416wh5') format('embedded-opentype'),"
                . "url('" . get_template_directory_uri() . "/fonts/icomoon.woff?-416wh5') format('woff'),"
                . "url('" . get_template_directory_uri() . "/fonts/icomoon.ttf?-416wh5') format('truetype'),"
                . "url('" . get_template_directory_uri() . "/fonts/icomoon.svg?-416wh5#icomoon') format('svg');"
                . "font-weight: normal;"
                . "font-style: normal;}";

        wp_add_inline_style('kamoha-style', $kamoha_custom_css);
    }
}

add_action('wp_enqueue_scripts', 'kamoha_scripts');

/**
 * Add Google fonts - Alef
 * Taken from: http://themeshaper.com/2014/08/13/how-to-add-google-fonts-to-wordpress-themes/
 * @return string
 */
function kamoha_fonts_url() {
    $fonts_url = '';

    /* Translators: If there are characters in your language that are not
     * supported by Lora, translate this to 'off'. Do not translate
     * into your own language.
     */
    $alef = _x('on', 'Alef font: on or off', 'kamoha');

    if ('off' !== $alef) {

        $font_families = array();

        $font_families[] = 'Alef:400,700';

        $query_args = array(
            'family' => urlencode(implode('|', $font_families)),
            'subset' => urlencode('latin,hebrew'),
        );

        $fonts_url = add_query_arg($query_args, 'https://fonts.googleapis.com/css');
    }

    return esc_url_raw($fonts_url);
}

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

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
