/**
 * $RCSfile: cleanup.js,v $
 * $Revision: 1.3 $
 * $Date: 2006/01/21 20:13:54 $
 *
 * @author Moxiecode
 * @copyright Copyright © 2006, Moxiecode Systems AB, All rights reserved.
 */

/**
 * TinyMCE_Cleanup class.
 */

function TinyMCE_Cleanup() {
	this.isMSIE = (navigator.appName == "Microsoft Internet Explorer");
	this.rules = new Array();

	// Default config
	this.settings = {
		indent_elements : 'head,body,table,tbody,thead,tfoot,form,tr,td,ul,ol',
		newline_before_elements : 'p,h1,h2,h3,h4,h5,h6,pre,address,div,ul,ol,li,meta,option,area,title,link,base,script,td',
		newline_after_elements : 'br,hr,p,h1,h2,h3,h4,h5,h6,pre,address,div,ul,ol,li,meta,option,area,title,link,base,script',
		newline_before_after_elements : 'html,head,body,table,thead,tbody,tfoot,tr,td,form',
		indent_char : '\t',
		indent_levels : 1,
		entity_encoding : 'raw',
		valid_elements : '*[*]',
		entities : "160,nbsp,161,iexcl,162,cent,163,pound,164,curren,165,yen,166,brvbar,167,sect,168,uml,169,copy,170,ordf,171,laquo,172,not,173,shy,174,reg,175,macr,176,deg,177,plusmn,178,sup2,179,sup3,180,acute,181,micro,182,para,183,middot,184,cedil,185,sup1,186,ordm,187,raquo,188,frac14,189,frac12,190,frac34,191,iquest,192,Agrave,193,Aacute,194,Acirc,195,Atilde,196,Auml,197,Aring,198,AElig,199,Ccedil,200,Egrave,201,Eacute,202,Ecirc,203,Euml,204,Igrave,205,Iacute,206,Icirc,207,Iuml,208,ETH,209,Ntilde,210,Ograve,211,Oacute,212,Ocirc,213,Otilde,214,Ouml,215,times,216,Oslash,217,Ugrave,218,Uacute,219,Ucirc,220,Uuml,221,Yacute,222,THORN,223,szlig,224,agrave,225,aacute,226,acirc,227,atilde,228,auml,229,aring,230,aelig,231,ccedil,232,egrave,233,eacute,234,ecirc,235,euml,236,igrave,237,iacute,238,icirc,239,iuml,240,eth,241,ntilde,242,ograve,243,oacute,244,ocirc,245,otilde,246,ouml,247,divide,248,oslash,249,ugrave,250,uacute,251,ucirc,252,uuml,253,yacute,254,thorn,255,yuml,402,fnof,913,Alpha,914,Beta,915,Gamma,916,Delta,917,Epsilon,918,Zeta,919,Eta,920,Theta,921,Iota,922,Kappa,923,Lambda,924,Mu,925,Nu,926,Xi,927,Omicron,928,Pi,929,Rho,931,Sigma,932,Tau,933,Upsilon,934,Phi,935,Chi,936,Psi,937,Omega,945,alpha,946,beta,947,gamma,948,delta,949,epsilon,950,zeta,951,eta,952,theta,953,iota,954,kappa,955,lambda,956,mu,957,nu,958,xi,959,omicron,960,pi,961,rho,962,sigmaf,963,sigma,964,tau,965,upsilon,966,phi,967,chi,968,psi,969,omega,977,thetasym,978,upsih,982,piv,8226,bull,8230,hellip,8242,prime,8243,Prime,8254,oline,8260,frasl,8472,weierp,8465,image,8476,real,8482,trade,8501,alefsym,8592,larr,8593,uarr,8594,rarr,8595,darr,8596,harr,8629,crarr,8656,lArr,8657,uArr,8658,rArr,8659,dArr,8660,hArr,8704,forall,8706,part,8707,exist,8709,empty,8711,nabla,8712,isin,8713,notin,8715,ni,8719,prod,8721,sum,8722,minus,8727,lowast,8730,radic,8733,prop,8734,infin,8736,ang,8743,and,8744,or,8745,cap,8746,cup,8747,int,8756,there4,8764,sim,8773,cong,8776,asymp,8800,ne,8801,equiv,8804,le,8805,ge,8834,sub,8835,sup,8836,nsub,8838,sube,8839,supe,8853,oplus,8855,otimes,8869,perp,8901,sdot,8968,lceil,8969,rceil,8970,lfloor,8971,rfloor,9001,lang,9002,rang,9674,loz,9824,spades,9827,clubs,9829,hearts,9830,diams,34,quot,38,amp,60,lt,62,gt,338,OElig,339,oelig,352,Scaron,353,scaron,376,Yuml,710,circ,732,tilde,8194,ensp,8195,emsp,8201,thinsp,8204,zwnj,8205,zwj,8206,lrm,8207,rlm,8211,ndash,8212,mdash,8216,lsquo,8217,rsquo,8218,sbquo,8220,ldquo,8221,rdquo,8222,bdquo,8224,dagger,8225,Dagger,8240,permil,8249,lsaquo,8250,rsaquo,8364,euro"
	};

	this.vElements = new Array();
	this.vElementsRe = '';
	this.closeElements = /^(IMG|BR|HR|LINK|META|BASE)$/;
	this.codeElementsRe = /^(SCRIPT|STYLE)$/;
	this.mceAttribs = {
		href : 'mce_href',
		src : 'mce_src',
		type : 'mce_type'
	};
}

TinyMCE_Cleanup.prototype = {
	init : function(s) {
		var n, a, i, ir, or, st;

		for (n in s)
			this.settings[n] = s[n];

		// Setup code formating
		s = this.settings;

		// Setup regexps
		this.inRe = this.buildRe('^<(', s.indent_elements, ')[^>]*');
		this.ouRe = this.buildRe('^<\\/(', s.indent_elements, ')[^>]*');
		this.nlBeforeRe = this.buildRe('<(', s.newline_before_elements, ')([^>]*)>');
		this.nlAfterRe = this.buildRe('<(', s.newline_after_elements, ')([^>]*)>');
		this.nlBeforeAfterRe = this.buildRe('<\\/?(', s.newline_before_after_elements, ')([^>]*)>');

		// Setup separator
		st = '';
		for (i=0; i<s.indent_levels; i++)
			st += s.indent_char;

		this.inStr = st;

		// Setup default rule
		if (s.valid_elements != '*' && s.valid_elements != '*[*]')
			this.addRuleStr(s.valid_elements);

		// Setup entities
		n = new Array();
		a = this.split(',', s.entities);
		for (i=0; i<a.length; i+=2)
			n['c' + a[i]] = a[i+1];

		this.entities = n;
	},

	buildRe : function(be, l, af) {
		var r, a, i;

		r = be;
		a = l.split(',');
		for (i=0; i<a.length; i++)
			r += a[i] + (i != a.length-1 ? "|" : "");

		r += af;

		return new RegExp(r, 'gi');
	},

	// a[x|y|z]
	addRuleStr : function(s) {
		var ta, p, r, a, i, x, ve;

		ta = s.split(',');
		for (x=0; x<ta.length; x++) {
			r = r = {};

			s = ta[x];
			if (s.length == 0)
				continue;

			// Split tag/attrs
			p = this.split(/\[|\]/, s);
			if (p == null || p.length < 1)
				r.tag = s.toUpperCase();
			else
				r.tag = p[0].toUpperCase();

			// Setup valid attributes
			if (p.length > 1) {
				r.vAttribsRe = '^(';
				a = this.split(/\|/, p[1]);

				for (i=0; i<a.length; i++)
					r.vAttribsRe += '' + a[i].toUpperCase() + (i != a.length - 1 ? '|' : '');

				r.vAttribsRe += ')$';
				r.vAttribsRe = new RegExp(r.vAttribsRe);
				r.vAttribs = a;
			} else {
				r.vAttribsRe = '';
				r.vAttribs = new Array();
			}

			// Setup global rules
			this.vElements[this.vElements.length] = r.tag;
			a = this.vElements;
			ve = '^(';
			for (i=0; i<a.length; i++)
				ve += '' + a[i] + (i != a.length - 1 ? '|' : '');
			ve += ')$';
			this.vElementsRe = new RegExp(ve);

			this.rules[r.tag] = r;
		}
	},

	getRules : function() {
		return this.rules;
	},

	serializeNode : function(n) {
		var en, h = '', i, r, an, av, cn, va = false;

		switch (n.nodeType) {
			case 1: // Element
				// Is valid element
				if (this.vElementsRe.test(n.nodeName)) {
					va = true;

					en = n.nodeName.toLowerCase();
					r = this.rules[n.nodeName];

					h += '<' + en;

					// Serialize attributes
					for (i=0; i<r.vAttribs.length; i++) {
						an = r.vAttribs[i];
						av = '';

						if (this.mceAttribs[an])
							av = this.getAttrib(n, this.mceAttribs[an]);

						if (av.length == 0)
							av = this.getAttrib(n, r.vAttribs[i]);

						if (av.length != 0)
							h += " " + an + "=" + '"' + this.xmlEncode(av) + '"';
					}

					// Close these
					if (this.closeElements.test(n.nodeName))
						return h + ' />';

					h += '>';

					if (this.isMSIE && this.codeElementsRe.test(n.nodeName))
						h += n.innerHTML;
				}
			break;

			case 3: // Text
				if (n.parentNode && this.codeElementsRe.test(n.parentNode.nodeName))
					return this.isMSIE ? '' : n.nodeValue;

				return this.xmlEncode(n.nodeValue);

			case 8: // Comment
				return "<!--" + n.nodeValue + "-->";
		}

		if (n.hasChildNodes()) {
			cn = n.childNodes;

			for (i=0; i<cn.length; i++)
				h += this.serializeNode(cn[i]);
		}

		// End element
		if (va && n.nodeType == 1)
			h += '</' + en + '>'

		return h;
	},

	cleanupNode : function(n) {
		var t, h;

		h = this.serializeNode(n);
		h = this.formatHTML(h);

		return h;
	},

	formatHTML : function(h) {
		var s = this.settings, p = '', i = 0, li = 0, o = '', l;

		h = h.replace(new RegExp('\n' + s.indent_char + '+', 'gi'), '\n'); // Remove previous formatting
		h = h.replace(this.nlBeforeRe, '\n$&');
		h = h.replace(this.nlAfterRe, '$&\n');
		h = h.replace(this.nlBeforeAfterRe, '\n$&\n');
		h += '\n';

		while ((i = h.indexOf('\n', i + 1)) != -1) {
			if ((l = h.substring(li + 1, i)).length != 0) {
				if (this.ouRe.test(l) && p.length >= s.indent_levels)
					p = p.substring(s.indent_levels);

				o += p + l + '\n';

				if (this.inRe.test(l))
					p += this.inStr;
			}

			li = i;
		}

		return o;
	},

	xmlEncode : function(s) {
		var i, o = '', c;

		switch (this.settings.entity_encoding) {
			case "raw":
				s = "" + s;
				s = s.replace(/&/g, '&amp;');
				s = s.replace(/\"/g, '&quot;');
				//s = s.replace(/\'/g, '&apos;');
				s = s.replace(/</g, '&lt;');
				s = s.replace(/>/g, '&gt;');

				return s;

			case "named":
				for (i=0; i<s.length; i++) {
					c = s.charCodeAt(i);

					if (typeof(this.entities["c" + c]) != 'undefined' && this.entities["c" + c] != '')
						o += '&' + this.entities["c" + c] + ';';
					else
						o += String.fromCharCode(c);
				}

				return o;

			case "numeric":
				for (i=0; i<s.length; i++) {
					c = s.charCodeAt(i);

					if (c > 127 || c == 60 || c == 62 || c == 38 || c == 39 || c == 34)
						o += '&#' + c + ";";
					else
						o += String.fromCharCode(c);
				}

				return o;
		}

		return s;
	},

	split : function(re, s) {
		var c = s.split(re);
		var i, o = new Array();

		for (i=0; i<c.length; i++) {
			if (c[i] != '')
				o[i] = c[i];
		}

		return o;
	},

	getAttrib : function(e, n, d) {
		if (typeof(d) == "undefined")
			d = "";

		if (!e || e.nodeType != 1)
			return d;

		var v = e.getAttribute(n, 0);

		if (n == "class" && !v)
			v = e.className;

		if (n == "http-equiv" && this.isMSIE)
			v = e.httpEquiv;

		if (n == "style" && !tinyMCE.isOpera)
			v = e.style.cssText;

		if (n == 'style')
			v = tinyMCE.serializeStyle(tinyMCE.parseStyle(v));

		return (v && v != "") ? v : d;
	}
};
