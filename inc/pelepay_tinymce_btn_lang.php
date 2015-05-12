<?php

/* Translation file for tinymce button */
if ( ! defined ( 'ABSPATH' ) )
    exit;

if ( ! class_exists ( '_WP_Editors' ) ) {
    require( ABSPATH . WPINC . '/class-wp-editor.php' );
}

function kamoha_tc_button_translation () {
    $strings = array(
	'button_label' => __ ( 'Add Pelepay form' , 'kamoha' )  ,
        'first_option_text' => __('First option text', 'kamoha'),
        'price_list' => __('Comma separated price list', 'kamoha'),
        'price_text' => __('Comma separated price text list', 'kamoha'),
        'payments_text' => __('Max number of payments', 'kamoha')
    );

    $locale = _WP_Editors::$mce_locale;

    $translated = 'tinyMCE.addI18n("' . $locale . '.kamoha_tc_button", ' . json_encode ( $strings ) . ");\n";

    return $translated;
}

$strings = kamoha_tc_button_translation ();
