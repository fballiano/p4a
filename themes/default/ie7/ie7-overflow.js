/*
	IE7, version 0.8 (alpha) (2005/05/23)
	Copyright: 2004-2005, Dean Edwards (http://dean.edwards.name/)
	License: http://creativecommons.org/licenses/LGPL/2.1/
*/
IE7.addModule("ie7-overflow", function() {

/* ---------------------------------------------------------------------

  This module alters the structure of the document.
  It may adversely affect other CSS rules. Be warned.

--------------------------------------------------------------------- */

// Thanks to Mark 'Tarquin' Wilton-Jones and Rainer Åhlfors

var $STYLE = {
	backgroundColor: "transparent",
	backgroundImage: "none",
	backgroundPositionX: null,
	backgroundPositionY: null,
	backgroundRepeat: null,
	borderTopWidth: 0,
	borderRightWidth: 0,
	borderBottomWidth: 0,
	borderLeftStyle: "none",
	borderTopStyle: "none",
	borderRightStyle: "none",
	borderBottomStyle: "none",
	borderLeftWidth: 0,
	height: null,
	marginTop: 0,
	marginBottom: 0,
	marginRight: 0,
	marginLeft: 0,
	width: "100%"
};

function _copyStyle($propertyName, $source, $target) {
	$target.style[$propertyName] = $source.currentStyle[$propertyName];
	if ($STYLE[$propertyName] != null) {
		$source.runtimeStyle[$propertyName] = $STYLE[$propertyName];
	}
};

ie7CSS.addRecalc("overflow", "visible", function($element) {
	// don't do this again
	if ($element.parentNode.ie7_wrapper) return;

	// if max-height is applied, makes sure it gets applied first
	if (ie7Layout && $element.currentStyle["max-height"] != "auto") {
		ie7Layout.maxHeight($element);
	}

	if ($element.currentStyle.marginLeft == "auto") $element.style.marginLeft = 0;
	if ($element.currentStyle.marginRight == "auto") $element.style.marginRight = 0;

	var $wrapper = document.createElement(ANON);
	$wrapper.ie7_wrapper = true;
	for (var $propertyName in $STYLE) _copyStyle($propertyName, $element, $wrapper);
	$wrapper.style.display = "block";
	$wrapper.style.position = "relative";
	$element.runtimeStyle.position = "absolute";
	$element.parentNode.insertBefore($wrapper, $element);
	$wrapper.appendChild($element);
});

// -----------------------------------------------------------------------
// fix cssQuery
// -----------------------------------------------------------------------

cssQuery.addModule("ie7-overflow", function() {

function _wrappedElement($element) {
	return ($element && $element.ie7_wrapper) ? $element.firstChild : $element;
};

var _previousElementSibling = previousElementSibling;
previousElementSibling = function($element) {
	return _wrappedElement(_previousElementSibling($element));
};

var _nextElementSibling = nextElementSibling;
nextElementSibling = function($element) {
	return _wrappedElement(_nextElementSibling($element));
};

selectors[" "] = function($results, $from, $tagName, $namespace) {
	// loop through current selection
	var $element, i, j;
	for (i = 0; i < $from.length; i++) {
		// get descendants
		var $subset = getElementsByTagName($from[i], $tagName, $namespace);
		// loop through descendants and add to results selection
		for (j = 0; ($element = _wrappedElement($subset[j])); j++) {
			if (thisElement($element) && (!$namespace || compareNamespace($element, $namespace)))
				$results.push($element);
		}
	}
};

selectors[">"] = function($results, $from, $tagName, $namespace) {
	var $element, i, j;
	for (i = 0; i < $from.length; i++) {
		var $subset = childElements($from[i]);
		for (j = 0; ($element = _wrappedElement($subset[j])); j++) {
			if (compareTagName($element, $tagName, $namespace)) $results.push($element);
		}
	}
};

}); // cssQuery

}); // ie7-overflow
