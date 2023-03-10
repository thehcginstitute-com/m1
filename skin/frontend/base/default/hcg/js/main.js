(function($) {$(function() {
	var $nav = $('#nav');
	var $icon = $('.hcg-mobile-menu-icon');
	var $w = $(window);
	$icon.click(function() {$nav.toggle();});
	var $aP = $('a[href*=products]', $nav);
	var $aT = $('li > a.level-top', $nav);
	var prevW = 0;
	var prevH = 0;
	var prevO = window.orientation;
	var init = function() {
		var w = $w.width();
		var h = $w.height();
		if (window.orientation !== prevO && (prevW !== h || prevH !== w)) {
			prevO = window.orientation;
		}
		else {
			prevO = window.orientation;
			if (prevW !== w && prevH !== h) {
				prevW = w; prevH = h;
				if (865 < w) {
					$aT.wrapInner("<span class='hcg-animation'>");
					$aP.siblings().hide();
					$aP.parent().removeClass('hcg-has-children');
					$aP.html('<span>Products</span>');
					$aP.attr('href', 'https://www.thehcginstitute.com/store/products');
					$nav.removeAttr('style');
				}
				else {
					$aP.siblings().show();
					$aT.each(function() {
						var $this = $(this);
						$this.html($this.children('.hcg-animation').html());
					});
					$aP.parent().addClass('hcg-has-children');
					$aP.html("Products<i aria-hidden='true' class='fa fa-angle-down'></i>");
					//$aP.attr('href', '#');
				}
			}
		}
	};
	init();
	$w.resize(init);
});})(jQuery);