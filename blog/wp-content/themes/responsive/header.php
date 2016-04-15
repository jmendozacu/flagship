<?php
/**
 * Header Template
 *
 *
 * @file           header.php
 * @package        Responsive
 * @author         Emil Uzelac
 * @copyright      2003 - 2014 CyberChimps
 * @license        license.txt
 * @version        Release: 1.3
 * @filesource     wp-content/themes/responsive/header.php
 * @link           http://codex.wordpress.org/Theme_Development#Document_Head_.28header.php.29
 * @since          available since Release 1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

?>
	<!doctype html>
	<!--[if !IE]>
	<html class="no-js non-ie" <?php language_attributes(); ?>> <![endif]-->
	<!--[if IE 7 ]>
	<html class="no-js ie7" <?php language_attributes(); ?>> <![endif]-->
	<!--[if IE 8 ]>
	<html class="no-js ie8" <?php language_attributes(); ?>> <![endif]-->
	<!--[if IE 9 ]>
	<html class="no-js ie9" <?php language_attributes(); ?>> <![endif]-->
	<!--[if gt IE 9]><!-->
<html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
	<head>

		<meta charset="<?php bloginfo( 'charset' ); ?>"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title><?php wp_title( '&#124;', true, 'right' ); ?></title>

		<link rel="profile" href="http://gmpg.org/xfn/11"/>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>"/>

		<?php wp_head(); ?> 
	</head>

<body <?php body_class(); ?>>

<?php responsive_container(); // before container hook ?>
<div id="container" class="hfeed">

<?php responsive_header(); // before header hook ?>
	<div class="skip-container cf">
		<a class="skip-link screen-reader-text focusable" href="#content"><?php _e( '&darr; Skip to Main Content', 'responsive' ); ?></a>
	</div><!-- .skip-container -->
	<div class="header-language-background">
<div class="header-language-container">
<div class="store-language-container">


<div class="search_account_wrapper">

<div class="skip-content" id="header-search">
<form method="get" action="http://directrangehoods.com/catalogsearch/result/" id="search_mini_form">
<div class="input-box">
<input type="search" placeholder="Search entire store here..." maxlength="128" class="input-text required-entry" value="" name="q" id="search" autocomplete="off">
<button class="button search-button blogsearchbtn" title="Search" type="submit"><span><span></span></span></button>
</div>
<div class="search-autocomplete" id="search_autocomplete" style="display: none;"></div>
<script type="text/javascript">
//&lt;![CDATA[
var searchForm = new Varien.searchForm('search_mini_form', 'search', '');
searchForm.initAutocomplete('http://directrangehoods.com/catalogsearch/ajax/suggest/', 'search_autocomplete');
//]]&gt;
</script>
</form>
</div>

<ul class="account_cart">
	<li><a href="https://directrangehoods.com/customer/account/">Account</a></li>
    <li><a href="http://directrangehoods.com/checkout/cart/">Cart</a></li>
</ul>

</div>

</div>

</div>
</div>
    <div class="headerwrapper">
    
    
    
    
    <div id="header">

		<?php responsive_header_top(); // before header content hook ?>

		<?php if ( has_nav_menu( 'top-menu', 'responsive' ) ) {
			wp_nav_menu( array(
				'container'      => '',
				'fallback_cb'    => false,
				'menu_class'     => 'top-menu',
				'theme_location' => 'top-menu'
			) );
		} ?>

		<?php responsive_in_header(); // header hook ?>

		<?php if ( get_header_image() != '' ) : ?>
<div id="header2">
			<div id="logo">
				<a href="<?php echo home_url( '/' ); ?>"><img src="<?php header_image(); ?>" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="<?php bloginfo( 'name' ); ?>"/></a>
			</div><!-- end of #logo -->
<div class="h_sosialmedia">
<a target="_new" href="https://twitter.com/directhoods"><div class="sicons ticon"></div></a>
<a target="_new" href="https://www.pinterest.com/directhoods/"><div class="sicons picon"></div></a>
<a target="_new" href="https://www.facebook.com/directrangehoods?fref=ts"><div class="sicons ficon"></div></a>
<a target="_new" href="https://instagram.com/directrangehoods"><div class="sicons Intsagram"></div></a>
<a target="_new" href="https://www.youtube.com/user/prolinerangehoods"><div class="sicons youtube"></div></a>

        
            </div>
            
		<?php endif; // header image was removed ?>

		<?php if ( !get_header_image() ) : ?>

			<div id="logo">
				<span class="site-name"><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></span>
				<span class="site-description"><?php bloginfo( 'description' ); ?></span>
			</div><!-- end of #logo -->

		<?php endif; // header image was removed (again) ?>

		<?php get_sidebar( 'top' ); ?>
        
		<?php wp_nav_menu( array(
			'container'       => 'div',
			'container_class' => 'main-nav',
			'fallback_cb'     => 'responsive_fallback_menu',
			'theme_location'  => 'header-menu'
		) ); ?>
        </div>
<div class="blogbanner">
<?php get_header_image(); ?>
<h2>KITCHEN</h2>
</div>
		<?php if ( has_nav_menu( 'sub-header-menu', 'responsive' ) ) {
			wp_nav_menu( array(
				'container'      => '',
				'menu_class'     => 'sub-header-menu',
				'theme_location' => 'sub-header-menu'
			) );
		} ?>

		<?php responsive_header_bottom(); // after header content hook ?>

	</div><!-- end of #header -->
    
    
    
    </div>
<?php responsive_header_end(); // after header container hook ?>

<?php responsive_wrapper(); // before wrapper container hook ?>
	<div id="wrapper" class="clearfix">
<?php responsive_wrapper_top(); // before wrapper content hook ?>
<?php responsive_in_wrapper(); // wrapper hook ?>
<style>
#content-blog h2.entry-title{min-height: 75px !important;}
</style>