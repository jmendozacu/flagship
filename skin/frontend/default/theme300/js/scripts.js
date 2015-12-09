jQuery(document).ready(function(){
								
		var fl=false, 
			fl2=false;
			jQuery('.block-cart-header .cart-content').hide();
			jQuery('.block-cart-header  .amount a, .block-cart-header .cart-content').hover(function(){
				jQuery('.block-cart-header .cart-content').stop(true, true).slideDown(400);
			},function(){
				jQuery('.block-cart-header .cart-content').stop(true, true).delay(400).slideUp(300);
		});
	
       jQuery("a[data-gal^='prettyPhoto']").prettyPhoto({
            animationSpeed: 'normal',
            padding: 40,
            opacity: 0.35,
            showTitle: true,
            allowresize: true,
            counter_separator_label: '/',          
            theme: 'facebook' 
        });
        jQuery('.sidebar .block:last').addClass ('last_block'); 
        jQuery('.sidebar .block:first').addClass ('item_block'); 
        jQuery('.sidebar .block:first-child').addClass ('block_item');
		jQuery('.nav-container').addClass('block');
		jQuery('.main button.button span span, .page-print button.button span span, .page-popup button.button span span, .block-cart-header button.button span span').wrapInner('<strong></strong>'); 
		jQuery('.cms-home .subtitle_home').wrapInner('<span></span>');
		jQuery('.cms-index-index .home-products .products-grid:last').addClass ('last');

});
