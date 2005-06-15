/*
	IE7, version 0.8 (alpha) (2005/05/23)
	Copyright: 2004-2005, Dean Edwards (http://dean.edwards.name/)
	License: http://creativecommons.org/licenses/LGPL/2.1/
*/
IE7.addModule("ie7-recalc", function() {

/* ---------------------------------------------------------------------

  This allows refreshing of IE7 style rules. If you modify the DOM
  you can update IE7 by calling document.recalc().

  This module is still in development.
  There may be memory problems if document.recalc() is
  called excessively as currently I am not detatching old
  event handlers when the document is refreshed.
  Let me know if there are any problems.

  -dean

--------------------------------------------------------------------- */

// remove all IE7 classes from an element
$CLASSES = /\sie7_class\d+/g;
function _removeClasses($element) {
	$element.className = $element.className.replace($CLASSES, "");
};

// clear IE7 assigned styles
function _removeStyle($element) {
	$element.runtimeStyle.cssText = "";
};

ie7CSS.specialize({
	// store for elements that have style properties calculated
	elements: {},
	// clear IE7 classes and styles
	reset: function() {
		// reset IE7 classes here
		var $elements = this.elements;
		for (var i in $elements) _removeStyle($elements[i]);
		this.elements = {};
		// reset runtimeStyle here
		if (this.Rule) {
			var $elements = this.Rule.elements;
			for (var i in $elements) _removeClasses($elements[i]);
			this.Rule.elements = {};
		}
	},
	addRecalc: function($propertyName, $test, $handler, $replacement) {
		// call the ancestor method to add a wrapped recalc method
		this.inherit($propertyName, $test, function($element) {
			// execute the original recalc method
			$handler($element);
			// store a reference to this element so we can clear its style later
			ie7CSS.elements[$element.uniqueID] = $element;
		}, $replacement);
	},
	recalc: function() {
		// clear IE7 styles and classes
		this.reset();
		// execute the ancestor method to perform recalculations
		this.inherit();
	}
});

if (ie7CSS.Rule) {
	// store all elements with an IE7 class assigned
	ie7CSS.Rule.elements = {};

	ie7CSS.Rule.prototype.specialize({
		add: function($element) {
			// execute the ancestor "add" method
			this.inherit($element);
			// store a reference to this element so we can clear its classes later
			ie7CSS.Rule.elements[$element.uniqueID] = $element;
		}
	});
}

if (isHTML && ie7HTML) {
	ie7HTML.specialize({
		elements: {},
		addRecalc: function($selector, $handler) {
			// call the ancestor method to add a wrapped recalc method
			this.inherit($selector, function($element) {
				if (!ie7HTML.elements[$element.uniqueID]) {
					// execute the original recalc method
					$handler($element);
					// store a reference to this element so that
					//  it is not "fixed" again
					ie7HTML.elements[$element.uniqueID] = $element;
				}
			});
		}
	});
}

// allow refreshing of IE7 fixes
document.recalc = recalc;

});
