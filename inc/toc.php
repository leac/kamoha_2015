<?php

/*
 * Specail function for toc in posts
 */

add_action( 'wp_enqueue_scripts', 'toc_scripts' );

/**
 * Enqueue JS and CSS for tables of contnets in posts
 */
function toc_scripts() {

    if ( is_single( 28592 ) ) { 

        wp_enqueue_script( 'masonry' );
        wp_enqueue_style( 'kamoha-toc-style', get_template_directory_uri() . '/css/toc.css' );
    }
}
