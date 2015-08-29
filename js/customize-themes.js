/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 */
( function ( $ ) {

	//Update body class color in real time...
	wp.customize( 'holiday_header', function ( value ) {
		value.bind( function ( newval ) {
			$('body').removeClass('special birthday purim pesah hanuka trip_before_close trip_after_close shabbat_before_close shabbat_before_close_urban shabbat_early shabbat_after_close independence memorial jerusalem shavuot tishabeav rosh_hashana yom_kipur sukot');
			if (newval !== 'regular'){
				$( 'body' ).addClass( newval + ' special');
			}
		} );
	} );

} )( jQuery );
