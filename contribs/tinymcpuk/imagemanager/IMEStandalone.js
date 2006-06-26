/**
 * The ImageManager plugin javascript.
 * @author $Author: Wei Zhuo $
 * @version $Id: image-manager.js 26 2004-03-31 02:35:21Z Wei Zhuo $
 * @package ImageManager
 */

//Translation unit.
var I18N;	

/**
 * This is the ImageManager constructor.
 * manager_url is where this script is.
 * lang is the lanaguage file you wish to use.
 */
function ImageManager(manager_url,lang)
{
	var plugin_lang = manager_url + "/lang/" + lang + ".js";
	this.url = manager_url + '/manager.php';

	document.write("<script type='text/javascript' src='" + plugin_lang + "'></script>");
	this.I18N = I18N;
};

ImageManager._pluginInfo = {
	name          : "ImageManager Stand Alone",
	version       : "1.0",
	developer     : "Xiang Wei Zhuo",
	developer_url : "http://www.zhuo.org/htmlarea/",
	license       : "htmlArea"
};

//Call this to popup the Image Manager and Editor.
//updater is an object with a update method that accepts
//an array of parameters.
//The returned parameters are
//param.f_url, param.f_file, param.f_alt,
//param.f_border, param.f_align, param.f_vert, param.f_horiz, 
//param.f_width, param.f_height
ImageManager.prototype.popManager = function(updater)
{
	this._insertImage(updater);
}

// Open up the ImageManger script.
// when the dialog returns, if there are parameters
// then updater.update is called with the parameters
ImageManager.prototype._insertImage = function(updater) {

	var manager = this;	// for nested functions

	Dialog(manager.url, function(param) {
		if (!param) {	// user must have pressed Cancel
			return false;
		}
		else
		{
			if(updater && typeof updater.update != 'undefined')
				updater.update(param);
		}
		
	}, null);
};


