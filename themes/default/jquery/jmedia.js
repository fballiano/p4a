/*
	jmedia - a jQuery-Plugin for unobtrusive multimedia embedding <http://www.contentwithstructure.com/extras/jmedia>
	Copyright 2007 Christoph Liell <info[at]contentwithstructure[dot]com>
	This software is licensed under the MIT ( http://www.opensource.org/licenses/mit-license.php) and GNU (http://www.gnu.org/licenses/gpl.html) License

	inspired by SWFObject (http://blog.deconcept.com/swfobject/), UFO (http://www.bobbyvandersluis.com/ufo/) and sIFR (http://www.mikeindustries.com/sifr/)
	SWF cleanup functions from SWFObject
	Flash Express Install Implementation from UFO
*/
var _jm;
_jm = jQuery.fn.jmedia = function(scriptoptions,mediaoptions){
	$(".jm_noscript").remove();
	if(typeof(version)=='undefined')var version=[];
	return this.each(function(index){
		var $this=jQuery(this);
		var soptions=jQuery.extend({
			elemType:this.nodeName,
			elemClass:'jm_replaced',
			forceObjectTag:false,
			mode:'replace',
			version:'6,0,0',
			fullScreen:false,
			errTxt:'',
			sifrPaddingTop:0,
			sifrPaddingBottom:0,
			sifrPaddingLeft:0,
			sifrPaddingRight:0,
			flashXI:false,
			flashXIsrc:'js/XI.swf'
		}, scriptoptions || {});
		var moptions=jQuery.extend({
			src: $this.attr('href') || $this.attr('src') || '#',
			width:320,
			height:280
			}, mediaoptions || {});
		if(moptions.src=="#")return false;	//	exit: no valid src
		//var t=_jm.getPluginName(moptions.src);
		var t=moptions.type;
		if (t==false) return false;			//	exit: no valid src extension
		if (typeof(version[t])=='undefined' && _jm.uaHas("w3cdom") && !_jm.uaHas("ieMac")) version[t]=_jm.detectPluginVersion(t,soptions.forceObjectTag);
		if(true || _jm.checkVersion(soptions.version,version[t])==true){
			if(t=='flash' && soptions.mode=='sifr')moptions=_jm.getSifrContent($this,moptions,soptions);
			else if(soptions.mode=='replace')$this.hide();
			var _el=$('<'+soptions.elemType+' class="'+ soptions.elemClass +'"></'+soptions.elemType+'>');
			if (soptions.fullScreen==true){
				$(_el).width("100%");
				$(_el).height("100%");
				$(_el).css({"position":"absolute","left":0,"top":0});
				$("body").css({"overflow":"hidden","height":"100%","width":"100%"});
				$("html").css({"overflow":"hidden","height":"100%","width":"100%"});
				moptions.width="100%";
				moptions.height="100%";
			}
			var _s=_jm.writeHtml($this,t,soptions,moptions);
			if(t=='flash' && soptions.mode=='sifr'){
				var _alternate=document.createElement('span');
				$(_alternate).addClass('sifr-alternate');
				$(_alternate).append($this.html());
				$this.html('');
				if ((typeof(_s)).toString().toLowerCase()=='string')$this.html(_s);
				else $this.append(_s);
				$this.append(_alternate);
			}
			else if(soptions.mode=='onclick'){
				$(this).click(function(){
					$(_el).hide();
					$(".jm_closebtn").remove();
					var _closecon=$("<span></span>");;
					$(_closecon).addClass("jm_closebtn");
					$(_closecon).css({width:moptions.width});
					var _closebtn=$('<a href="#">close&nbsp;</a>');
					$(_closecon).append($(_closebtn));
					$(_closebtn).click(function(){$(_el).remove();return false;});
					$(this).after($(_el));
					$(_el).show();
					if ((typeof(_s)).toString().toLowerCase()=='string')$(_el).html(_s);
					else $(_el).append(_s);
					$(_el).prepend($(_closecon));
					return false;
				});
			}
			else {
				if ((typeof(_s)).toString().toLowerCase()=='string')_el.html(_s);
				else _el.append(_s);
				$this.after(_el).remove();
			}
			return true;
		}
		else if( t=='flash' && soptions.flashXI==true && (_jm.checkVersion("6,0,65",version[t])==true) ){
			// do flashXI
			moptions.src=soptions.flashXIsrc;
			moptions.width='215';
			moptions.height='138';
			var _type = _jm.type=='axo' ? "ActiveX" : "PlugIn";
			var _uc = typeof soptions.flashXIcancelURL != "undefined" ? "&xiUrlCancel=" + soptions.flashXIcancelURL : "";
			var _uf = typeof soptions.flashXIfailedURL != "undefined" ? "&xiUrlFailed=" + soptions.flashXIfailedURL : "";
			if (_jm.uaHas("xml") && _jm.uaHas("safari")) var _mmd = document.getElementsByTagName("title")[0].firstChild.nodeValue = document.getElementsByTagName("title")[0].firstChild.nodeValue.slice(0, 47) + " - Flash Player Installation";
			else var _mmd = document.title = document.title.slice(0, 47) + " - Flash Player Installation";
			moptions.flashVars="MMredirectURL=" + window.location + "&MMplayerType=" + _type + "&MMdoctitle=" + _mmd + _uc + _uf;
			var _s=_jm.writeHtml($this,t,soptions,moptions);
			var _el=jQuery('<div id="flashXI"></div>');
			var _con=jQuery('<div id="flashXIcon"></div>');
			if ((typeof(_s)).toString().toLowerCase()=='string')_el.html(_s);
			else _el.append(_s);
			$('body').css({height:"100%",overflow:"hidden"});
			$('html').css({height:"100%",overflow:"hidden"});
			$(_con).css({position:"absolute", top:0, left:0, "z-index":1000, background:"white", width:"100%", height:"100%", filter:"alpha(opacity:75)", opacity:0.75});
			$(_el).css({position:"absolute", left:"50%", top:"50%", "margin-left":parseInt(moptions.width/2*(-1)) + "px", "margin-top":parseInt(moptions.height/2*(-1)) + "px"});
			$(_el).css({width:moptions.width + "px",height:moptions.height + "px"});
			$(_con).append(_el);
			$('body').prepend($(_con));
		}
		else if(soptions.mode=='onclick'){
			var _trig=false;
			$(this).click(function(){
				$(".jm_onfailure").remove();
				if (_trig==false){
					var errtxt=$(soptions.errTxt.replace(/#link/,$(this).attr("href")));
					$(this).after($(errtxt));
					if(_jm.uaHas("w3cdom") && !_jm.uaHas("ieMac"))$(".jm_oldbrowsers").remove();
					_trig=true;
				}
				else {
					_trig=false;
				}
				return false;
			});
		}
		else return false;
	});
};
_jm.checkVersion = function(reqver,uaver){
	if (typeof(uaver)=='undefined' || uaver=='not installed')return false;
	else {
		if(uaver=='unknown')return true;	// wmedia && npapi
		var _uav = uaver.split(",");
		var _rqv = reqver.toString().replace(/\./,",").split(",");
		for(var i = 0; i < 3; i++) {
			_uav[i] = parseInt(_uav[i] || 0);
			_rqv[i] = parseInt(_rqv[i] || 0);
			if(_uav[i] < _rqv[i]) return false;
			if(_uav[i] > _rqv[i]) return true;
		}
		return true;
}};
_jm.detectPluginVersion = function(t,fo){
	if(typeof(t)=='undefined')return false;	//	exit: no plugintype
	var _gao=false;
	var _np=navigator.plugins;
	if (_np && _np.length){
		if ( t == 'wmedia' && fo==false) _gao = _jm.detectGeckoAXO(); 	//		deactivate to skip geckoactiveX detection
		if (_gao==false){
		_jm.type='npapi';
		//	_jm.type='axo'; 	//to simulate axo markup;
		for (_i = 0; _i < _np.length; _i++){
			var _p = _np[_i];
			var _sl=_jm.plugins[t].description.length;
			for (_ii = 0; _ii < _sl; _ii++){
				if (_p.name.indexOf(_jm.plugins[t].description[_ii]) != -1) {
					var _n = _p.name;
					var _d = _p.description;
					switch(t){
						case "flash" :
							return _d.replace(/([a-zA-Z]|\s)+/,"").replace(/(\s+r|\s+b[0-9]+)/,".").replace(/(\.)/g,",");
						case "director" :
							return  _d.split('version ')[1].replace(/(\.)/g,",");
						case "quicktime" :
							return  _n.replace(/([a-zA-Z]|\s|-)+/, "").replace(/(\s+r|\s+b[0-9]+)/, ".").replace(/(\.)/g,",");
						case "real" :
							return  _d.replace(/(\.)/g,",");
						case "wmedia" :
							return  'unknown';
						default:
							return  'not installed';
	}}}}}}
	if( window.ActiveXObject || _gao == true ) {
		_jm.type='axo';
		for (_i = 0; _i < _jm.plugins[t].progID.length; _i++){
			var _axon=_jm.plugins[t].progID[_i];
			try {
				if (_gao == true) _axo = new GeckoActiveXObject(_axon);
				else _axo = new ActiveXObject(_axon);
				switch(t){
					case "flash":
						var _axov=0;
						if (_axon=="ShockwaveFlash.ShockwaveFlash.7")_axov = axo.GetVariable("$version");
						else if("ShockwaveFlash.ShockwaveFlash.6"){
							_axov =  "6,0,21,0";
							_axo.AllowScriptAccess = "always";
							_axov = _axo.GetVariable("$version");
						}
						else if("ShockwaveFlash.ShockwaveFlash.3"){
							_axov = axo.GetVariable("$version");
							if(_axov==0)_axov = "3,0,18,0";
						}
						else if("ShockwaveFlash.ShockwaveFlash")_axov = "2,0,0,11";
						return  _axov.replace(/([a-zA-Z]|\s|-)+/, "").toString();
					case "director":
						return  _axo.ShockwaveVersion("").replace(/r/,",").replace(/\./g,",");
					case "quicktime" :
						return _axo.QuickTimeVersion.toString(16).replace(/(\d)/g,"$1,").toString().replace(/\./,",");
					case "real":
						return _axo.GetVersionInfo().toString().replace(/(\.)/g,",");
					case "wmedia":
						return _axo.versionInfo.toString().replace(/(\.)/g,",");
					case "acrobat":
						var _acv=_axon.split(".")[2];
						if (_acv=="1")_acv=7;
						return _acv+",0";
					default:
						return 'not installed';
					}}
			catch (e) {}
}}};
_jm.detectGeckoAXO = function(){
	var _n=navigator.plugins;
	if (_n && _n.length){
	for (_x=0; _x<_n.length; _x++){
		if (_n[_x].name.indexOf('ActiveX') != -1 && window.GeckoActiveXObject)return true;
	}}
	return false;
};
_jm.getPluginName = function(src){
	var _arr=src.split(".");
	var ext=_arr[(_arr.length-1)];
	switch(ext){
		case 'ram':
			return 'real';
		case 'rm':
			return 'real';
		case 'swf':
			return 'flash';
		case 'mov':
			return 'quicktime';
		case 'dcr':
			return 'director';
		case 'wmv':
			return 'wmedia';
		case 'asx':
			return 'wmedia';
	}
	return false;
};
_jm.writeHtml = function(elem,t,soptions,moptions){
	var _pt=t;
	if(_pt=='flash' && typeof(moptions.flashVars)!='undefined' && moptions.flashVars.indexOf(/&/)==0)moptions.flashVars=moptions.flashVars.substr(1,(moptions.flashVars.length-1));
	if(_jm.type=='axo'){
		var _objPar = "";
		for(var key in moptions)
			if(typeof moptions[key] != 'function' && key !="height" && key !="width" && key !="src" && (_pt!="flash" || (key!="align" && key!="base" && key!="swfliveconnect")))
				_objPar += '<param name="'+key+'" value="'+moptions[key]+'">';
		if(_pt=="flash")_objPar += '<param name="movie" value="' + moptions.src + '">';
		else if(_pt=="wmedia")_objPar += '<param name="filename" value="' +moptions.src + '">';
		else _objPar += '<param name="src" value="' + moptions.src + '">';
		var _objAtt = "";
		if (_pt=="flash" && typeof(moptions.align)!='undefined')_objAtt += ' align="' + moptions.align + '"';
		if (_pt=="flash" && typeof(moptions.base)!='undefined')_objAtt += ' base="' + moptions.base + '"';
		else if (_pt=="wmedia")_objAtt += ' type="' + _jm.plugins[_pt].mimeType[2] + '"';
		var _cb="";
		var _p = window.location.protocol == "https:" ? "https:" : "http:";
		if(_jm.plugins[_pt].codeBase!=undefined) _cb=' codebase="' + _p+ "//" + _jm.plugins[_pt].codeBase + '"';
		var _oStr='<object classid="clsid:' + _jm.plugins[_pt].classID +'" '+ _objAtt + ' width="' + moptions.width + '" height="' + moptions.height + '"' + _cb + '>' + _objPar + '</object>';
		return _oStr;
		}
	else if (_jm.type=='npapi'){
	   if ((soptions.forceObjectTag==true && _jm.uaHas("gecko")) || _jm.uaHas("xml")) {
			var _obj = $("<object></object>");
			var _mt = _jm.plugins[_pt].mimeType[1] ? _jm.plugins[_pt].mimeType[1] : _jm.plugins[_pt].mimeType[0];
			$(_obj).attr({type:_mt, data: moptions.src,width:moptions.width,height: moptions.height});
			var _objPar = "";
			for(var key in moptions){
				if(typeof moptions[key] != 'function' && key !="height" && key !="width" && key !="src" ){
					if (_pt != 'real') {
						var _p = $("<param>");
					   $(_p).attr({name: key, value: moptions[key] });
					   $(_obj).append($(_p));
					}
					else $(_obj).attr(key,moptions[key]);
				}
			}
			if (_pt=="flash"){
			  var _p = $("<param>");
			  $(_p).attr({name:"movie", value:moptions.src});
			  $(_obj).append($(_p));
			}
			else {
			   var _p = $("<param>");
			   $(_p).attr({name:"src", value: moptions.src});
			   $(_obj).append($(_p));
			}
			return _obj;
		}
		else {
			var _embAttr ='';
			for(var key in moptions)
				if(typeof moptions[key] != 'function' && key!='src')
					_embAttr += key+'="'+moptions[key]+'" ';
			var _embStr='<embed type="' + _jm.plugins[_pt].mimeType[0] + '" src="' + moptions.src + '" pluginspage="' + _jm.plugins[_pt].pluginsPage + '" ' + _embAttr + '></embed>';
			return _embStr;
		}
 }};
_jm.getSifrContent = function(elem,moptions,soptions){
	$('body').addClass('sifr-hasflash');
	$(elem).addClass('sifr-replaced');
	moptions.width=parseInt($(elem).offsetWidth - soptions.sifrPaddingLeft - soptions.sifrPaddingRight);
	moptions.height=$(elem).height() - soptions.sifrPaddingTop - soptions.sifrPaddingBottom;
	if(isNaN(moptions.width))moptions.width=$(elem).width() - soptions.sifrPaddingLeft - soptions.sifrPaddingRight;
	if (typeof(moptions.flashVars)=='undefined') jQuery.extend(moptions,{flashVars:''});
	if(!moptions.flashVars.match(/textcolor/)) moptions.flashVars += "&textcolor=#000000";
	var _ch=$(elem).children();
	if(_ch.length>0){
		var content="";
		var sLinkVars="";
		var sLinkCnt=0;
		_ch.each(function(){
			if(this.nodeName.toLowerCase()=="a" && this.href != 'undefined'){
				if($(this).attr("target")){
						sLinkVars += "&sifr_url_" + nLinkCount + "_target=" + $(this).attr("target");
					};

				content+='<a href="asfunction:_root.launchURL,'+ sLinkCnt + '">' + $(this).text() + '</a>';
				sLinkVars+='&sifr_url_' + sLinkCnt + '=' + escapeHex(this.href).replace(/&/g, "%26");
				sLinkCnt++;
			}
			else content+=$(this).text();
		});
		moptions.flashVars += "&txt=" + escapeHex(content).replace(/\+/g, "%2B").replace(/&/g, "%26").replace(/\"/g, "%22").normalize() + "&h=" + moptions.height  + "&w=" + moptions.width + sLinkVars;
	}
	else moptions.flashVars += "&txt=" + escapeHex(elem.text()).replace(/\+/g, "%2B").replace(/&/g, "%26").replace(/\"/g, "%22").normalize() + "&h=" + moptions.height  + "&w=" + moptions.width;
	moptions.sifr="true";
	return moptions;
};
_jm.uaHas = function(ft) {
	var _u = navigator.userAgent.toLowerCase();
	switch(ft) {
		case "w3cdom":
			return (typeof document.getElementById != "undefined" && typeof document.getElementsByTagName != "undefined" && (typeof document.createElement != "undefined" || typeof document.createElementNS != "undefined"));
		case "xml":
			var _m = document.getElementsByTagName("meta");
			var _l = _m.length;
			for (var i = 0; i < _l; i++) if (/content-type/i.test(_m[i].getAttribute("http-equiv")) && /xml/i.test(_m[i].getAttribute("content"))) return true;
			return false;
		case "ieMac":
			return /msie/.test(_u) && !/opera/.test(_u) && /mac/.test(_u);
		case "ieWin":
			return /msie/.test(_u) && !/opera/.test(_u) && /win/.test(_u);
		case "gecko":
			return /gecko/.test(_u) && !/applewebkit/.test(_u);
		case "opera":
			return /opera/.test(_u);
		case "safari":
			return /applewebkit/.test(_u);
		default:
			return false;
}};
_jm.plugins ={
	"flash": {
		classID: "D27CDB6E-AE6D-11CF-96B8-444553540000",
		progID: ["ShockwaveFlash.ShockwaveFlash.9", "ShockwaveFlash.ShockwaveFlash.8.5", "ShockwaveFlash.ShockwaveFlash.8", "ShockwaveFlash.ShockwaveFlash.7", "ShockwaveFlash.ShockwaveFlash.6", "ShockwaveFlash.ShockwaveFlash.5", "ShockwaveFlash.ShockwaveFlash.4"],
		description: ["Shockwave Flash"],
		mimeType: ["application/x-shockwave-flash"],
		pluginsPage: "http://www.macromedia.com/go/getflashplayer",
		codeBase: "download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab"},
	"quicktime": {
		classID: "02BF25D5-8C17-4B23-BC80-D3488ABDDC6B",
		progID: ["QuickTimeCheckObject.QuickTimeCheck.1", "QuickTime.QuickTime"],
		description: ["QuickTime"],
		mimeType: ["video/quicktime"],
		pluginsPage: "http://www.apple.com/quicktime/download/",
		codeBase: "www.apple.com/qtactivex/qtplugin.cab"},
	"real": {
		classID: "CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA",
		progID: ["RealPlayer.RealPlayer(tm) ActiveX Control (32-bit)", "RealVideo.RealVideo(tm) ActiveX Control (32-bit)", "rmocx.RealPlayer G2 Control"],
		description: ["RealOne Player","RealPlayer Version"],
		mimeType: ["audio/x-pn-realaudio-plugin"],
		pluginsPage: "http://www.real.com/freeplayer/?rppr=rnwk"},
	"wmedia": {
		progID: ["WMPlayer.OCX", "MediaPlayer.MediaPlayer.1"],
		classID: "22D6f312-B0F6-11D0-94AB-0080C74C7E95",
		description: ["Windows Media"],
		pluginsPage: "http://www.microsoft.com/windows/windowsmedia/",
		codeBase: "activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab",
		mimeType: ["application/x-mplayer2","video/x-ms-asf","application/x-oleobject"]},
	"director": {
		classID: "166B1BCA-3F9C-11CF-8075-444553540000",
		progID: ["SWCtl.SWCtl.11","SWCtl.SWCtl.10","SWCtl.SWCtl.9","SWCtl.SWCtl.8","SWCtl.SWCtl.7","SWCtl.SWCtl.6","SWCtl.SWCtl.5","SWCtl.SWCtl.4","SWCtl.SWCtl"],
		description: ["Shockwave for Director"],
		pluginsPage: "http://www.macromedia.com/shockwave/download/",
		codeBase: "download.macromedia.com/pub/shockwave/cabs/director/sw.cab",
		mimeType: ["application/x-director"]}
};
function escapeHex(sHex){
	if(_jm.uaHas('ieWin') || _jm.uaHas('ieMac')){ /* The RegExp for IE breaks old Gecko's, the RegExp for non-IE breaks IE 5.01 */
		return sHex.replace(new RegExp("%\d{0}", "g"), "%25");
	}
	return sHex.replace(new RegExp("%(?!\d)", "g"), "%25");
};
String.prototype.normalize = function(){
	return this.replace(/\s+/g, " ");
};

// SWF cleanup functions
_jm.cleanupSWFs=function(){
	if(_jm.uaHas("opera")||!document.all){return;}
	var _2d=document.getElementsByTagName("OBJECT");
	for(var i=0;i<_2d.length;i++){
		_2d[i].style.display="none";
		for(var x in _2d[i]){
			if(typeof _2d[i][x]=="function"){_2d[i][x]=function(){};}
		}
	}
};
var prepUnload=function(){
	var __flash_unloadHandler=function(){};
	var __flash_savedUnloadHandler=function(){};
	if(typeof window.onunload=="function"){
		var _30=window.onunload;
		window.onunload=function(){
			_jm.cleanupSWFs();
			_30();
		};
	}
	else window.onunload=_jm.cleanupSWFs;
};
if(typeof window.onbeforeunload=="function"){
	var oldBeforeUnload=window.onbeforeunload;
	window.onbeforeunload=function(){
		prepUnload();
		oldBeforeUnload();
	};
}
else window.onbeforeunload=prepUnload;

//	UFO expressInstall callback onAbort
var UFO = {
	expressInstallCallback: function() {
		var _b = document.getElementsByTagName("body")[0];
		var _c = document.getElementById("flashXIcon");
		_b.removeChild(_c);
		_b.style.setAttribute("overflow","auto");
		_b.style.setAttribute("height","auto");
		document.getElementsByTagName("html")[0].style.setAttribute("overflow","auto");
		document.getElementsByTagName("html")[0].stylesetAttribute("height","auto");
	}
};