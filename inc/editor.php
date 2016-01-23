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
 * Add style select to visual editor
 * ----------------------------------------- */

// Callback function to insert 'styleselect' into the $buttons array
function kamoha_mce_buttons_2( $buttons ) {
    array_unshift( $buttons, 'styleselect' );
    return $buttons;
}

// Register our callback to the appropriate filter
add_filter( 'mce_buttons_2', 'kamoha_mce_buttons_2' );


// Callback function to filter the MCE settings
function kamoha_mce_before_init_insert_formats( $init_array ) {  
	// Define the style_formats array
	$style_formats = array(  
		// Each array child is a format with it's own settings
		array(  
			'title' => __('.letter', 'kamoha'),  
			'block' => 'div',  
			'classes' => 'letter',
			'wrapper' => true,
			
		),  
	);  
	// Insert the array, JSON ENCODED, into 'style_formats'
	$init_array['style_formats'] = json_encode( $style_formats );  
	
	return $init_array;  
  
} 
// Attach callback to 'tiny_mce_before_init' 
add_filter( 'tiny_mce_before_init', 'kamoha_mce_before_init_insert_formats' );  