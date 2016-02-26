<?php 
/*  instalation of javascript and css  */
function sfsiplus_plugin_back_enqueue_script()
{
		/* include CSS for backend  */
  		wp_enqueue_style('thickbox');
		wp_enqueue_style("SFSIPLUSmainCss", SFSI_PLUS_PLUGURL . 'css/sfsi-style.css' );
		if(isset($_GET['page']))
		{
			if($_GET['page'] == 'sfsi-plus-options')
			{
				wp_enqueue_style("SFSIPLUSJqueryCSS", SFSI_PLUS_PLUGURL . 'css/jquery-ui-1.10.4/jquery-ui-min.css' );
				wp_enqueue_style("SFSIPLUSColorCss", SFSI_PLUS_PLUGURL . 'css/colorpicker/css/colorpicker-min.css' );
				wp_enqueue_style("SFSIPLUSLayoutCss", SFSI_PLUS_PLUGURL . 'css/colorpicker/css/layout-min.css' );
			}
		}
		
		if(isset($_GET['page']))
		{
			if($_GET['page'] == 'sfsi-plus-options')
			{
				//wp_register_script('SFSIMigrate',  SFSI_PLUGURL . 'js/jquery-migrate-min.js', '', '', true);
				//wp_enqueue_script("SFSIMigrate");
				
				wp_enqueue_script('jquery');
				wp_enqueue_script("jquery-migrate");
				
				wp_enqueue_script('media-upload');
				wp_enqueue_script('thickbox'); 
				
				wp_register_script('SFSIPLUSjquery.ui.min', SFSI_PLUS_PLUGURL . 'js/jquery-ui-min.js', '', '', true);
				wp_enqueue_script("SFSIPLUSjquery.ui.min");	
				
				
				wp_register_script('SFSIPLUSJqueryFRM', SFSI_PLUS_PLUGURL . 'js/jquery.form-min.js', '', '', true);
				wp_enqueue_script("SFSIPLUSJqueryFRM");
				
				wp_register_script('SFSIPLUSJqueryColorPicker', SFSI_PLUS_PLUGURL . 'js/color-picker/colorpicker-min.js', '', '', true);
				wp_enqueue_script("SFSIPLUSJqueryColorPicker");
				
				wp_register_script('SFSIPLUSJqueryEye', SFSI_PLUS_PLUGURL . 'js/color-picker/eye-min.js', '', '', true);
				//wp_enqueue_script("SFSIPLUSJqueryEye");
				
				wp_register_script('SFSIPLUSJqueryLayout', SFSI_PLUS_PLUGURL . 'js/color-picker/layout-min.js', '', '', true);
				//wp_enqueue_script("SFSIPLUSJqueryLayout");
				
				wp_register_script('SFSIPLUSJqueryutils', SFSI_PLUS_PLUGURL . 'js/color-picker/utils-min.js', '', '', true);
				//wp_enqueue_script("SFSIPLUSJqueryutils");
				
				wp_register_script('SFSIPLUSCustomFormJs', SFSI_PLUS_PLUGURL . 'js/custom-form-min.js', '', '', true);
				wp_enqueue_script("SFSIPLUSCustomFormJs");
				
				wp_register_script('SFSIPLUSCustomJs', SFSI_PLUS_PLUGURL . 'js/custom-admin.js', '', '', true);
				wp_enqueue_script("SFSIPLUSCustomJs");
				
				wp_register_script('SFSIPLUSCustomValidateJs', SFSI_PLUS_PLUGURL . 'js/customValidate-min.js', '', '', true);
				wp_enqueue_script("SFSIPLUSCustomValidateJs");
				/* end cusotm js */
				
				/* initilaize the ajax url in javascript */
				wp_localize_script( 'SFSIPLUSCustomJs', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
				wp_localize_script( 'SFSIPLUSCustomValidateJs', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ),'plugin_url'=> SFSI_PLUS_PLUGURL) );
			}
		}
}
add_action( 'admin_enqueue_scripts', 'sfsiplus_plugin_back_enqueue_script' );

function sfsiplus_plugin_front_enqueue_script()
{
		wp_enqueue_style("SFSIPLUSmainCss", SFSI_PLUS_PLUGURL . 'css/sfsi-style.css' );
		$option5=  unserialize(get_option('sfsi_plus_section5_options',false));
		if($option5['sfsi_plus_disable_floaticons'] == 'yes')
		{
			wp_enqueue_style("disable_sfsiplus", SFSI_PLUS_PLUGURL . 'css/disable_sfsi.css' );
		}
		
		wp_enqueue_script('jquery');
	 	wp_enqueue_script("jquery-migrate");
		
		//wp_register_script('SFSIPLUSMigrate',  SFSI_PLUS_PLUGURL . 'js/jquery-migrate-min.js', '','',true);
		//wp_enqueue_script("SFSIPLUSMigrate");
		
		wp_register_script('SFSIPLUSjquery.ui.min', SFSI_PLUS_PLUGURL . 'js/jquery-ui-min.js', '','',true);
		wp_enqueue_script("SFSIPLUSjquery.ui.min");	
		
		wp_register_script('SFSIPLUSjqueryModernizr', SFSI_PLUS_PLUGURL . 'js/shuffle/modernizr.custom.min.js', '','',true);
		wp_enqueue_script("SFSIPLUSjqueryModernizr");
		
		wp_register_script('SFSIPLUSjqueryShuffle', SFSI_PLUS_PLUGURL . 'js/shuffle/jquery.shuffle.min.js', '','',true);
		wp_enqueue_script("SFSIPLUSjqueryShuffle");
		
		wp_register_script('SFSIPLUSjqueryrandom-shuffle', SFSI_PLUS_PLUGURL . 'js/shuffle/random-shuffle-min.js', '','',true);
		wp_enqueue_script("SFSIPLUSjqueryrandom-shuffle");
		
		wp_register_script('SFSIPLUSCustomJs', SFSI_PLUS_PLUGURL . 'js/custom.js', '', '', true);
		wp_enqueue_script("SFSIPLUSCustomJs");
		/* end cusotm js */
		
		/* initilaize the ajax url in javascript */
		wp_localize_script( 'SFSIPLUSCustomJs', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ),'plugin_url'=> SFSI_PLUS_PLUGURL) );
}
add_action( 'wp_enqueue_scripts', 'sfsiplus_plugin_front_enqueue_script' );		
?>