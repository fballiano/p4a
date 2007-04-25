function getPageSize()
{
	var xScroll, yScroll, windowWidth, windowHeight;

	if (window.innerHeight && window.scrollMaxY) {
		xScroll = document.body.scrollWidth;
		yScroll = window.innerHeight + window.scrollMaxY;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		xScroll = document.body.scrollWidth;
		yScroll = document.body.scrollHeight;
	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		xScroll = document.body.offsetWidth;
		yScroll = document.body.offsetHeight;
	}

	if (self.innerHeight) {	// all except Explorer
		windowWidth = self.innerWidth;
		windowHeight = self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
		windowWidth = document.documentElement.clientWidth;
		windowHeight = document.documentElement.clientHeight;
	} else if (document.body) { // other Explorers
		windowWidth = document.body.clientWidth;
		windowHeight = document.body.clientHeight;
	}

	// for small pages with total height less then height of the viewport
	if(yScroll < windowHeight){
		pageHeight = windowHeight;
	} else {
		pageHeight = yScroll;
	}

	// for small pages with total width less then width of the viewport
	if(xScroll < windowWidth){
		pageWidth = windowWidth;
	} else {
		pageWidth = xScroll;
	}

	arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight)
	return arrayPageSize;
}

//
// getPageScroll()
// Returns array with x,y page scroll values.
// Core code from - quirksmode.org
//
function getPageScroll()
{
	var yScroll;

	if (self.pageYOffset) {
		yScroll = self.pageYOffset;
	} else if (document.documentElement && document.documentElement.scrollTop){	 // Explorer 6 Strict
		yScroll = document.documentElement.scrollTop;
	} else if (document.body) {// all other Explorers
		yScroll = document.body.scrollTop;
	}

	arrayPageScroll = new Array('',yScroll)
	return arrayPageScroll;
}

function showPopup()
{
	var arrayPageSize = getPageSize();
	var arrayPageScroll = getPageScroll();

	var overlay = $('#overlay');
	overlay.style.height = 	arrayPageSize[1] + "px";

	var popup = document.getElementById('popup');
	popup.style.zIndex = -1;
	$('#popup').show();

	var width = $('sheetContainerPopup').childNodes[0].scrollWidth + "px";
	var top = arrayPageScroll[1] + ((arrayPageSize[3] - popup.scrollHeight - 40) / 2 ) + "px";
	var left = ((arrayPageSize[2] - popup.scrollWidth ) / 2 ) + "px";

	popup.style.width = width;
	popup.style.top = top;
	popup.style.left = left;

	Element.show('overlay');
	popup.style.zIndex = 100;
}

function hidePopup()
{
	Element.hide('overlay');
	Element.hide('popup');
}