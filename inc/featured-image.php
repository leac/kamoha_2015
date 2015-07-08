<?php

/*
 * Featured image functions - if post doesn't have featured image, use the thumb user-field, or the post's first image
 */

/* ----------------------------------------------------------------
 * Get thumbnail (create one if none exists)
 * -------------------------------------------------------------- */

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
                $response = wp_remote_get( 'http://www.example.com/index.html' );
                if ( is_array( $response ) ) {
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
                    $response = wp_remote_get( 'http://www.example.com/index.html' );
                    if ( is_array( $response ) ) {
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
