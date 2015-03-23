jQuery(document).ready(function(){
	var fl=false, 
	fl2=false;
	jQuery('.block-cart-header .cart-content').hide();
	jQuery('.block-cart-header  .amount, .block-cart-header .cart-content').hover(function(){
		jQuery('.block-cart-header .cart-content').stop(true, true).slideDown(400);
	},function(){
		jQuery('.block-cart-header .cart-content').stop(true, true).delay(400).slideUp(300);
	})
});
