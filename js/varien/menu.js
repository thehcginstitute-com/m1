/**
 * @classDescription simple Navigation with replacing old handlers
 * @param {String} id id of ul element with navigation lists
 * @param {Object} settings object with settings
 */
var mainNav = function() {
	var J = jQuery;
	var mobile = function() {return J('.hcg-mobile-menu-icon').is(':visible');};
	// 2018-09-24 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	// A tablet with a wide screen does not support the `mouseenter` / `mouseleave` events.
	// https://stackoverflow.com/a/20293441
	var touch = function() {
		var r = false;
		try {document.createEvent('TouchEvent'); r = true;} catch(e){}
		return r;
	};
	var main = {
		obj_nav:    $(arguments[0]) || $("nav"),

		settings:   {
			show_delay     :    0,
			hide_delay     :    0,
			_ie6           :    /MSIE 6.+Win/.test(navigator.userAgent),
			_ie7           :    /MSIE 7.+Win/.test(navigator.userAgent)
		},

		init: function(obj, level) {
			obj.lists = obj.childElements();
			obj.lists.each(function(el, ind) {
				main.handlNavElement(el);
				if((main.settings._ie6 || main.settings._ie7) && level) {
					main.ieFixZIndex(el, ind, obj.lists.size());
				}
			});
			if(main.settings._ie6 && !level) {
				document.execCommand("BackgroundImageCache", false, true);
			}
		},

		handlNavElement: function(l) {
			// 2018-09-23 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			var $l = J(l);
			var f = function() {main.fireNavEvent(l);};
			$l.on('click', 'i.fa', function(e) {
				e.stopImmediatePropagation();
				e.preventDefault();
				main.fireNavEvent(l);
			});
			// 2018-09-26 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			// https://www.upwork.com/messages/rooms/room_83b901b4acc8f695bac43d90418f35f1/story_33428fce631cc30cf78c51e931c06eb7
			// https://humanwhocodes.com/blog/2012/07/05/ios-has-a-hover-problem
			if (!mobile() && !touch()) {
				// 2018-09-23 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
				// l.hover(f) does not work here for an unknown reason
				l.on('mouseenter', f);
				// 2018-09-23 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
				// Chaining `.on().on()` does not work.
				l.on('mouseleave', f);
			}
			if (l.down('ul')) {
				main.init(l.down('ul'), true);
			}
		},

		fireNavEvent: function(e) {
			var $e = J(e);
			var c = 'open';
			$e.toggleClass(c);
			var open = $e.hasClass(c);
			if (mobile()) {
				J('i.fa', $e).toggleClass('fa-angle-down');
			}
			if (open) {
				e.down('a').addClassName(c);
				if (e.childElements()[1]) {
					main.show(e.childElements()[1]);
				}
			}
			else {
				e.down('a').removeClassName(c);
				if (e.childElements()[1]) {
					main.hide(e.childElements()[1]);
				}
			}
		},

		ieFixZIndex:  function(el, i, l) {
			if(el.tagName.toString().toLowerCase().indexOf("iframe") == -1) {
				el.style.zIndex = l - i;
			} else {
				el.onmouseover = "null";
				el.onmouseout = "null";
			}
		},

		show:  function (sub_elm) {
			if (sub_elm.hide_time_id) {
				clearTimeout(sub_elm.hide_time_id);
			}
			sub_elm.show_time_id = setTimeout(function() {
				if (!sub_elm.hasClassName("shown-sub")) {
					sub_elm.addClassName("shown-sub");
				}
			}, main.settings.show_delay);
		},

		hide:  function (sub_elm) {
			if (sub_elm.show_time_id) {
				clearTimeout(sub_elm.show_time_id);
			}
			sub_elm.hide_time_id = setTimeout(function() {
				if (sub_elm.hasClassName("shown-sub")) {
					sub_elm.removeClassName("shown-sub");
				}
			}, main.settings.hide_delay);
		}

	};
	if (arguments[1]) {
		main.settings = Object.extend(main.settings, arguments[1]);
	}
	if (main.obj_nav) {
		main.init(main.obj_nav, false);
	}
};

document.observe("dom:loaded", function() {
	//run navigation without delays and with default id="#nav"
	//mainNav();

	//run navigation with delays
	mainNav("nav", {"show_delay":"100","hide_delay":"100"});
});
