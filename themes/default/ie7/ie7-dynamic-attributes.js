/*
	IE7, version 0.8 (alpha) (2005/05/23)
	Copyright: 2004-2005, Dean Edwards (http://dean.edwards.name/)
	License: http://creativecommons.org/licenses/LGPL/2.1/
*/

IE7.addModule("ie7-dynamic-attributes", function() {

// -----------------------------------------------------------------------
// IE7 Dynamic Attribute Class
// -----------------------------------------------------------------------

// requires another module
if (!modules["ie7-css2-selectors"]) return;

/* ---

// Class properties:
// attach: the element(s) that a dynamic attribute selector will be attached to
// target: the element(s) that will have the IE7 class applied

For example:
  fieldset p.required > input[value=""] + sup {color: red;}
In this example attach="fieldset p.required > input" and target="+ sup".

--- */

// cssQuery internals
var attributeSelectors = cssQuery.valueOf("attributeSelectors");
var parseSelector = cssQuery.valueOf("parseSelector");

// constructor
function DynamicAttribute($selector, $attach, $dynamicAttribute, $target, $cssText) {
	// initialise object properties
	this.attach = $attach || "*";
	parseSelector($dynamicAttribute);
	this.dynamicAttribute = attributeSelectors["@" + $dynamicAttribute];
	this.target = $target;
	this.inherit($selector, $cssText);
};
// protoytype
ie7CSS.Rule.specialize({
	// properties
	constructor: DynamicAttribute,
//- attach: "",
//- dynamicAttribute: null,
//- target: "",
	// methods
	recalc: function() {
		// execute the underlying css query for this class
		var $match = cssQuery(this.attach);
		// process results
		for (var i = 0; i < $match.length; i++) {
			// retrieve the event handler's target element(s)
			var $target = (this.target) ? cssQuery(this.target, $match[i]) : [$match[i]];
			// attach event handlers for dynamic attributes
			if ($target.length) this.apply($match[i], $target);
		}
	},
	apply: function($element, $target) {
		var self = this;
		// watch property changes
		addEventHandler($element, "onpropertychange", function() {
			// check the attribute name
			if (event.propertyName == self.dynamicAttribute.name)
				// turn the selector on/off
				self.test($element, $target);
		});
		this.test($element, $target);
	},
	test: function($element, $target) {
		var $action = this.dynamicAttribute.test($element) ? "add" : "remove";
		for (var i = 0; ($element = $target[i]); i++) this[$action]($element);

	}
});
// constants
DynamicAttribute.MATCH = /(.*)(\[[^\]]*\])(.*)/;

// intercept creation of IE7 rules
StyleSheet.prototype.specialize({
	createRule: function($selector, $cssText) {
		var $match;
		if ($match = $selector.match(DynamicAttribute.MATCH)) {
			return new DynamicAttribute($selector, $match[1], $match[2], $match[3], $cssText);
		} else return this.inherit($selector, $cssText);
	}
});

}); // addModule
