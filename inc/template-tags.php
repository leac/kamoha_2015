<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package kamoha
 */

if ( !function_exists( 'kamoha_posted_on' ) ) :

    /**
     * Prints HTML with meta information for the current post-date/time and author.
     */
    function kamoha_posted_on(){
        // (Lea - 06/2014) get jewish date and add as 5th parameter of sprintf
        $jew_date = get_hebrew_date( get_the_date( 'm' ), get_the_date( 'd' ), get_the_date( 'Y' ) );

        $time_string = '<time class="entry-date published" datetime="%1$s">%5$s, %2$s</time>';

        if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
            $time_string .= '<time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf( $time_string, esc_attr( get_the_date( 'c' ) ), esc_html( get_the_date() ), esc_attr( get_the_modified_date( 'c' ) ), esc_html( get_the_modified_date() ), $jew_date );

        printf( __( '<span class="posted-on">%1$s</span>', 'kamoha' ), $time_string
        );
        /* (Lea 2014/06) - we don't need the author info */
    }

endif;

/**
 * Returns true if a blog has more than 1 category.
 */
function kamoha_categorized_blog(){
    if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
        // Create an array of all the categories that are attached to posts.
        $all_the_cool_cats = get_categories( array(
            'hide_empty' => 1,
                ) );

        // Count the number of categories that are attached to the posts.
        $all_the_cool_cats = count( $all_the_cool_cats );

        set_transient( 'all_the_cool_cats', $all_the_cool_cats );
    }

    if ( '1' != $all_the_cool_cats ) {
        // This blog has more than 1 category so kamoha_categorized_blog should return true.
        return true;
    } else {
        // This blog has only 1 category so kamoha_categorized_blog should return false.
        return false;
    }
}

/**
 * Flush out the transients used in kamoha_categorized_blog.
 */
function kamoha_category_transient_flusher(){
    // Like, beat it. Dig?
    delete_transient( 'all_the_cool_cats' );
}

add_action( 'edit_category', 'kamoha_category_transient_flusher' );
add_action( 'save_post', 'kamoha_category_transient_flusher' );
