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
			$('body').removeClass('special birthday_baloons birthday_flowers birthday_ribbons purim pesah hanuka trip_before_close trip_after_close trip_during shabbat_before_close shabbat_before_close_urban shabbat_early shabbat_after_close independence memorial jerusalem shavuot tishabeav rosh_hashana yom_kipur sukot birthday_5_blue birthday_5_purple birthday_5_tubishvat shoa birthday_6_blue_conff birthday_6_black_conff birthday_6_baloons_conff');
			if (newval !== 'regular'){
				$( 'body' ).addClass( newval + ' special');
			}
		} );
	} );

} )( jQuery );
