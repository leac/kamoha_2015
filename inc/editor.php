<?php

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
                'contact_details_meta', __( 'Excerpt', 'kamoha_2015' ), 'post_excerpt_meta_box', $post_type, 'test', // change to something other then normal, advanced or side. Probably shouldn't use test, but I already did, and now when I try to change to something else, it doesn't work...
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

/**
 * Add post class to edit post screen, so editor style can be added
 * @global type $post_ID
 * @param type $classes
 * @return comma separated list of classes
 */
function kamoha_2015_body_class_admin_filter( $init_array ) {
    global $post;

    if ( is_a( $post, 'WP_Post' ) ) {
        $init_array['body_class'] .= ' ' . join( ' ', get_post_class( '', $post->ID ) );
    }
    return $init_array;
}

add_filter( 'tiny_mce_before_init', 'kamoha_2015_body_class_admin_filter' );
