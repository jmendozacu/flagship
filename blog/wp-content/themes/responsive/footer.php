<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Footer Template
 *
 *
 * @file           footer.php
 * @package        Responsive
 * @author         Emil Uzelac
 * @copyright      2003 - 2014 CyberChimps
 * @license        license.txt
 * @version        Release: 1.2
 * @filesource     wp-content/themes/responsive/footer.php
 * @link           http://codex.wordpress.org/Theme_Development#Footer_.28footer.php.29
 * @since          available since Release 1.0
 */

/*
 * Globalize Theme options
 */
global $responsive_options;
$responsive_options = responsive_get_options();
?>
<?php responsive_wrapper_bottom(); // after wrapper content hook ?>
</div>
<!-- end of #wrapper -->
<?php responsive_wrapper_end(); // after wrapper hook ?>
</div>
<!-- end of #container -->
<?php responsive_container_end(); // after container hook ?>

<div id="footer" class="clearfix">
  <?php responsive_footer_top(); ?>
  <div id="footer-wrapper">
    <div class="links">
      <div class="block-title"><strong><span>Quick Links</span></strong></div>
      <ul>
        <li class="first"><a title="Site Map" href="http://directrangehoods.com/catalog/seo_sitemap/category/">Site Map</a></li>
        <li><a title="Search Terms" href="http://directrangehoods.com/catalogsearch/term/popular/">Search Terms</a></li>
        <li class=" last"><a title="Advanced Search" href="http://directrangehoods.com/catalogsearch/advanced/">Advanced Search</a></li>
      </ul>
    </div>
    <div class="links">
      <ul>
        <li class="first last"><a title="Orders and Returns" href="https://directrangehoods.com/sales/guest/form/">Orders and Returns</a></li>
      </ul>
    </div>
    <div class="f_sosialmedia"> <a target="_new" href="https://twitter.com/directhoods">
      <div class="sicons ticon"></div>
      </a> <a target="_new" href="https://www.pinterest.com/directhoods/">
      <div class="sicons picon"></div>
      </a> <a target="_new" href="https://www.facebook.com/directrangehoods?fref=ts">
      <div class="sicons ficon"></div>
      </a> <a target="_new" href="https://instagram.com/directrangehoods">
      <div class="sicons Intsagram"></div>
      </a> <a target="_new" href="https://www.youtube.com/user/prolinerangehoods">
      <div class="sicons youtube"></div>
      </a> </div>
    <div class="block block-subscribe">
      <div class="block-title"> <strong><span>Newsletter</span></strong> </div>
      <form id="newsletter-validate-detail" method="post" action="https://directrangehoods.com/newsletter/subscriber/new/">
        <div class="block-content">
          <div class="input-box">
            <input type="email" class="input-text required-entry validate-email" title="Sign up for our newsletter" id="newsletter" name="email" spellcheck="false" autocorrect="off" autocapitalize="off">
          </div>
          <div class="actions">
            <button class="button" title="Subscribe" type="submit"><span><span>Subscribe</span></span></button>
          </div>
        </div>
      </form>
      <script type="text/javascript">
    //&lt;![CDATA[
        var newsletterSubscriberFormDetail = new VarienForm('newsletter-validate-detail');
    //]]&gt;
    </script>
    </div>
    <?php get_sidebar( 'footer' ); ?>
    <div class="grid col-940">
      <div class="grid col-540">
        <?php if ( has_nav_menu( 'footer-menu', 'responsive' ) ) {
					wp_nav_menu( array(
						'container'      => '',
						'fallback_cb'    => false,
						'menu_class'     => 'footer-menu',
						'theme_location' => 'footer-menu'
					) );
				} ?>
      </div>
      <!-- end of col-540 -->
      <div class="grid col-380 fit"> <?php echo responsive_get_social_icons() ?> </div>
      <!-- end of col-380 fit -->
    </div>
    <!-- end of col-940 -->
    <?php get_sidebar( 'colophon' ); ?>
    <div class="grid col-300 copyright">
      <?php esc_attr_e( '&copy;', 'responsive' ); ?>
      <?php echo date( 'Y' ); ?><a href="<?php echo home_url( '/' ) ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
      <?php bloginfo( 'name' ); ?>
      All Rights Reserved </a> </div>
    <!-- end of .copyright -->
    <div class="grid col-300 scroll-top"><a href="#scroll-top" title="<?php esc_attr_e( 'scroll to top', 'responsive' ); ?>">
      <?php _e( '&uarr;', 'responsive' ); ?>
      </a></div>
    <!-- end .powered -->
  </div>
  <!-- end #footer-wrapper -->
  <?php responsive_footer_bottom(); ?>
</div>
<!-- end #footer -->
<?php responsive_footer_after(); ?>
<?php wp_footer(); ?>
</body></html>