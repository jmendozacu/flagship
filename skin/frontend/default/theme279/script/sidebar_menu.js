jQuery(document).ready(function() {
	jQuery('#nav li').hover(
		function() {
			jQuery(this).addClass("hover");
		},
		function() {
			jQuery(this).removeClass("hover");
			if(!jQuery(this).hasClass("active")) {
				jQuery(this).children('ul').slideUp('normal');
			}
		}
	);
	jQuery('#nav ul').each(function() {
		jQuery(this).parent().addClass('items');
	});
	var is_playing = false;
	
	jQuery('#nav > li').not('.active').each(function() {
		jQuery(this).on("mouseenter", function() {
			if (jQuery(this).hasClass('items')) {
				slide_check(jQuery(this));
			}
		});
	});
	
	function slide_check(this_button) {
		var counter = 30;
		if (counter > 0) {
			if (this_button.hasClass('hover')) {
				if (is_playing == false) {
					is_playing = true;
					this_button.children('ul').slideDown('slow', function() {
						is_playing = false
					});
					
					jQuery('#nav > li').not('.active').each(function() {
						if(!jQuery(this).hasClass('hover')) {
							jQuery(this).children('ul').slideUp('normal');
						}
					});
					counter = counter - 10;
				}
			}
			counter--;
			setTimeout(function() {
				slide_check(this_button)
			}, 200);
		}
	}
});