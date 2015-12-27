<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

add_action( 'wp_enqueue_scripts', 'toc_scripts' );

function toc_scripts() {
    if ( is_single( 28413 ) ) { /* production: 28592 */
        wp_enqueue_script( 'masonry' );
    }
}
