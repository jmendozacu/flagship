<!-- Loader Image section  -->
<div id="sfpluspageLoad" >  
    
</div><!-- END Loader Image section  -->

<!-- javascript error loader  -->
<div class="error" id="sfsi_onload_errors" style="margin-left: 60px;display: none;">  
    <p>We found errors in your javascript which may cause the plugin to not work properly. Please fix the error:</p><p id="sfsi_jerrors"></p>
</div> <!-- END javascript error loader  -->

<!-- START Admin view for plugin-->
<div class="wapper sfsi_mainContainer">
	
    <!-- Top content area of plugin -->
    <div class="main_contant">
        <!--<h1>Welcome to the Ultimate Social Icons and Share Plugin!</h1>
        <p>This plugin is 100% FREE and will fulfill all your subscription/sharing/liking needs!</p>
        <p>Get started by clicking on the first question below. Once done, go to the <a href="<?php //echo admin_url('/widgets.php');?>">widget area</a> and move the widget to the sidebar so that your icons are displayed.</p>
        <p> If you have any questions, <a  href="mailto:support@ultimatelysocial.com" title="support@ultimatelysocial.com" alt="support@ultimatelysocial.com" class="lit_txt">let us know</a>, we‘ll help you out!</p>-->
        <h1>Welcome to the Ultimate Social Media Icons PLUS plugin!</h1>
        <p>This plugin is 100% FREE and will fulfill all your subscription/sharing/liking needs!</p>
        <p>Simply answer the questions below (at least the first 3) by clicking on them - that's it!</p>
        <p> If you have questions, or <b>something doesn't work as it should,</b> please <a  href="mailto:support@ultimatelysocial.com" title="support@ultimatelysocial.com" alt="support@ultimatelysocial.com" class="lit_txt">get in touch with us</a>, we‘re happy to sort it out!</p>
    </div>
    <!-- END Top content area of plugin -->
      
    <!-- step 1 end  here -->
    <div id="accordion">
    <h3><span>1</span>Which icons do you want to show on your site? </h3>
    <!-- step 1 end  here -->
    <?php include(SFSI_PLUS_DOCROOT.'/views/sfsi_option_view1.php'); ?>
    <!-- step 1 end here --> 
    
    <!-- step 2 start here -->
    <h3><span>2</span>What do you want the icons to do? </h3>
    <?php include(SFSI_PLUS_DOCROOT.'/views/sfsi_option_view2.php'); ?>
    <!-- step 2 END here -->
    
    <!-- step new 3 start here -->
    <h3><span>3</span>Where shall they be displayed? </h3>
    <?php include(SFSI_PLUS_DOCROOT.'/views/sfsi_option_view8.php'); ?>
    <!-- step new3 end here -->
   </div>
   
   
	<h2 class="optional">Optional</h2>
	 <div id="accordion1">
	<!-- step old 3 start here -->
    <h3><span>4</span>What design &amp; animation do you want to give your icons?</h3>
     <?php include(SFSI_PLUS_DOCROOT.'/views/sfsi_option_view3.php'); ?>
    <!-- step old 3 END here -->

    <!-- step old 4 Start here -->
    <h3><span>5</span>Do you want to display "counts" next to your main icons?</h3>
     <?php include(SFSI_PLUS_DOCROOT.'/views/sfsi_option_view4.php'); ?>
    <!-- step old 4 END here -->

    <!-- step old 5 Start here -->
    <h3><span>6</span>Any other wishes for your main icons?</h3>
    <?php include(SFSI_PLUS_DOCROOT.'/views/sfsi_option_view5.php'); ?>
    <!-- step old 5 END here -->

    <!-- step old 6 Start here -->
    <!--<h3><span>7</span>Do you want to display icons at the end of every post?</h3>-->
     <?php //include(SFSI_PLUS_DOCROOT.'/views/sfsi_option_view6.php'); ?>
    <!-- step old 6 END here -->


    <!-- step old 7 Start here -->
    <h3><span>7</span>Do you want to display a pop-up, asking people to subscribe?</h3>
     <?php include(SFSI_PLUS_DOCROOT.'/views/sfsi_option_view7.php'); ?>
    <!-- step old 7 END here -->

     </div>
    <div class="tab9">
         <div class="save_button">
		  <img src="<?php echo SFSI_PLUS_PLUGURL; ?>images/ajax-loader.gif" class="loader-img" />
		<a href="javascript:;" id="save_plus_all_settings" title="Save All Settings">Save All Settings</a>
	 </div>
         <p class="red_txt errorMsg" style="display:none"> </p>
         <p class="green_txt sucMsg" style="display:none"> </p>
	 	 <p style="margin-top: 30px;">This plugin is 100% free. Please do us a BIG favor and give us a 5 star rating <a href="https://wordpress.org/support/view/plugin-reviews/ultimate-social-media-plus" target="_new">here</a>. If you're not happy, please <a href="mailto:support@ultimatelysocial.com">get in touch with us</a>, so that we can sort it out.Thank you!</p>
         <p class="sucMsg bldtxtmsg">Need top-notch Wordpress development work at a competitive price? Visit us at <a href="http://www.ultimatelysocial.com">ultimatelysocial.com</a></p>
          
    </div>
 <!-- all pops of plugin under sfsi_pop_content.php file --> 
 <?php include(SFSI_PLUS_DOCROOT.'/views/sfsi_pop_content.php'); ?>
</div> <!-- START Admin view for plugin-->

