// JavaScript Document
/*
Facelift Image Replacement v1.1.1

Facelift was written and is maintained by Cory Mawhorter.  
It is available from http://facelift.mawhorter.net/

===

This file is part of Facelife Image Replacement ("FLIR").

FLIR is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

FLIR is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
*/
var FLIR = {
	version: '1.1.1'

	,path: basePath+'inc/lib/flir/'
	,classnameIgnore: false
	,replaceLinks: true

	,flirElements: []
	,isCraptastic: true

	,defaultStyle: null
	,classStyles: {}
	
	// either (options Object, fstyle FLIRStyle Object) or (fstyle FLIRStyle Object)
	,init: function(options, fstyle) { // or options for flir style
		if(this.isFStyle(options)) { // (fstyle FLIRStyle Object)
			this.defaultStyle = options;
			
		}else { // [options Object, fstyle FLIRStyle Object]
			if(typeof options != 'undefined')
				this.loadOptions(options);
		
			if(typeof fstyle == 'undefined') {
				this.defaultStyle = new FLIRStyle();
			}else {
				if(this.isFStyle(fstyle))
					this.defaultStyle = fstyle;
				else
					this.defaultStyle = new FLIRStyle(fstyle);
			}
		}
		this.isCraptastic = (typeof document.body.style.maxHeight=='undefined');
	}
	
	,loadOptions: function(options) {
		for(var i in options)
			if(typeof this[i] != 'function')
				this[i] = options[i];
	}
	
	,auto: function(els) {
		var tags = typeof els=='undefined'?['h1','h2','h3','h4','h5']:els;
		var objs,cn,childs,tag,matches;

		for(var i=0; i<tags.length; i++) {
			tag = tags[i];
			
			var grain_id=false;
			if(tags[i].indexOf('#') > -1) {
				grain_id = tags[i].split('#')[1];
				tag = tags[i].split('#')[0];
			}

			var grain_cn=false;
			if(tags[i].indexOf('.') > -1) {
				grain_cn = tags[i].split('.')[1];
				tag = tags[i].split('.')[0];
			}

			objs = document.getElementsByTagName(tag);
			for(var p=0; p<objs.length; p++) {
				if(objs[p].nodeType != 1) continue;
				matches = false;
				
				cn = objs[p].className?objs[p].className:'';
				
				if(grain_id && objs[p].id && objs[p].id == grain_id) {
					matches=true;
				}
				if(grain_cn && FLIR.hasClass(objs[p], grain_cn)) {
					matches=true;
				}
				if(!grain_id && !grain_cn) {
					matches=true;
				}
				
				if(!matches) continue;
				
				if(this.classnameIgnore && cn.indexOf(this.classnameIgnore)>-1) { continue; }

				if(!this.replaceLinks) {
					childs = this.getChildren(objs[p]);
					// skip any links that have a first child that is a link (assuming all text for that element is a link then)
					if(childs.length>0 && childs[0].nodeName=='A') continue; 
					
					// if direct parent is a link then skip (assuming entire header is a link);
					if(this.getParentNode(objs[p]).nodeName=='A') continue;
				}
				
				if(!this.isFStyle(objs[p].FLIRStyleObj))
					objs[p].FLIRStyleObj = this.defaultStyle;
				
				this.replace(objs[p]);
			}
		}
	}
	
	,replace: function(o, fstyle) {
		var FStyle = this.getFStyle(o, fstyle);

		var objs;
		if((objs = FLIR.getChildren(o)).length == 0) {
			var objs = [o];
		}else if(objs.length == 1) {
			var subobjs = FLIR.getChildren(objs[0]);
			if(subobjs.length > 0)
				objs = subobjs;
		}
		
		var rep_obj;
		for(var i=0; i < objs.length; i++) {
			rep_obj = objs[i];
			if(FLIR.hasClass(rep_obj, 'flir-replaced')) continue;
			
			if(!rep_obj.innerHTML) continue; // internet explorer..
			
			if(!this.isCraptastic) {
				if(FStyle.options.useBackgroundMethod && this.getStyle(rep_obj, 'display') == 'block') {
					this.replaceMethodBackground(rep_obj, fstyle);
					
					// sometimes the above method won't work... some bug that I can't track down right now
					// this checks to see if it doesn't work and substitues the image overlay method
					if(this.getStyle(rep_obj, 'background-image')=='none') {
						rep_obj.style.textIndent = rep_obj.oldTextIndent?rep_obj.oldTextIndent:'0px';
						this.replaceMethodOverlay(rep_obj, fstyle);	
					}
				}else {
					this.replaceMethodOverlay(rep_obj, fstyle);
				}
			}else {
				this.replaceMethodCraptastic(rep_obj, fstyle);
			}
			
			rep_obj.className += ' flir-replaced';
		}
	}
	
	,replaceMethodBackground: function(o, fstyle) {
		var FStyle = this.getFStyle(o, fstyle);
		var oid = this.saveObject(o);
		var url = FStyle.generateURL(o);
		
		if(FStyle.options.resizeBox) {
			var tmp = new Image();
			tmp.onload = function() {
				FLIR.flirElements[oid].style.width=this.width+'px';
				FLIR.flirElements[oid].style.height=this.height+'px';
			};
			tmp.src = url;
		}
		
		o.style.background='url('+url+') no-repeat';
		
		o.oldTextIndent = o.style.textIndent;
		o.style.textIndent='-9999px';
	}

	,replaceMethodOverlay: function(o, fstyle) {
		var FStyle = this.getFStyle(o, fstyle);
		var oid = this.saveObject(o);
		var img = document.createElement('IMG');

		img.alt = o.title = this.sanitizeHTML(o.innerHTML);
		img.src = FStyle.generateURL(o);
		
		o.innerHTML='';
		o.appendChild(img);
	}

	,replaceMethodCraptastic: function(o, fstyle) {
		var FStyle = this.getFStyle(o, fstyle);
		var oid = this.saveObject(o);
		var url = FStyle.generateURL(o)+'&ie6='+escape(Math.random()); // the onload gets foobd if this isn't a unique url (guess: tries to reuse other onload even though it's a new image object?)
		
		var img = document.createElement('IMG');

		if(FStyle.options.resizeBox) {
			var tmp = new Image();
			tmp.onload = function() {
				var targ = FLIR.getChildren(FLIR.flirElements[oid])[0];
				
				if(targ.style) {
					targ.style.width = this.width+'px';
					targ.style.height = this.height+'px';
				}else {
					// error: could not resize box, ie sucks
				}
			};
			tmp.src = url;
		}

		img.src = this.path+'spacer.png';
		img.style.width=o.offsetWidth+'px';
		img.style.height=o.offsetHeight+'px';
		img.style.filter = 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src="'+url+'", sizingMethod="crop")';

		o.innerHTML='';
		o.appendChild(img);
	}

	,saveObject: function(o) {
		if(typeof o.flirId == 'undefined') {
			o.flirId = this.getUID();
			this.flirElements[o.flirId] = o;
		}
		
		return o.flirId;
	}
	
	,getUID: function() {
		var prefix='flir-';
		var id=prefix+Math.random().toString().split('.')[1];
		var i=0;
		while(typeof this.flirElements[id] != 'undefined') {
			if(i>100000) {
				console.error('Facelift: Unable to generate unique id.');	
			}
			id=prefix+Math.random().toString().split('.')[1];
			i++;
		}
		
		return id;
	}

	,getStyle: function(el,prop) {
		if(el.currentStyle) {
			if(prop.indexOf('-') > -1)
				prop = prop.split('-')[0]+prop.split('-')[1].substr(0, 1).toUpperCase()+prop.split('-')[1].substr(1);
			var y = el.currentStyle[prop];
		}else if(window.getComputedStyle) {
			var y = document.defaultView.getComputedStyle(el,'').getPropertyValue(prop);
		}
		return y;
	}
		
	,getChildren: function(n) {
		var children=[];
		if(n && n.hasChildNodes())
			for(var i in n.childNodes)
				if(n.childNodes[i] && n.childNodes[i].nodeType == 1)
					children[children.length]=n.childNodes[i];
	
		return children;
	}
	
	,getParentNode: function(n) {
		var o=n.parentNode;
		while(o != document && o.nodeType != 1)
			o=o.parentNode;
	
		return o;
	}
	
	,hasClass: function(o, cn) {
		return (o.className && o.className.indexOf(cn)>-1);
	}
	
	,sanitizeHTML: function(html) { return html.replace(/<[^>]+>/g, ''); }
	
	,getFStyle: function(o, fstyle) { 
		if(this.isFStyle(fstyle)) {
			return fstyle;
		}else if(typeof fstyle != 'undefined') {
			var cStyle = this.getClassStyle(o);
			return false!=cStyle?cStyle:new FLIRStyle(fstyle);
		}else if(!this.isFStyle(o.FLIRStyleObj)) {
			var cStyle = this.getClassStyle(o);
			o.FLIRStyleObj = false!=cStyle?cStyle:this.defaultStyle;
		}
		
		return o.FLIRStyleObj;
	}
	,setFStyle: function(o, fstyle) { o.FLIRStyleObj = fstyle; }
	,isFStyle: function(o) { return (typeof o != 'undefined' && o.toString() == 'FLIRStyle Object'); }

	,addClassStyle: function(classname, FStyle) {
		if(this.isFStyle(FStyle))
			this.classStyles[classname] = FStyle;
	}
	,getClassStyle: function(o) {
		var cn = o.className;
		if(typeof cn == 'undefined' || cn=='') return false;
		
		var classes = cn.split(' ');
		for(var i in this.classStyles) {
			for(var ii=0; ii<classes.length; ii++) {
				if(classes[ii]==i) {
					return this.classStyles[i];
				}
			}
		}
		
		return false;
	}
};


function FLIRStyle(options) {
	this.options = {
		 mode: '' // none (''), wrap,progressive or name of a plugin
		,resizeBox: true
		,useBackgroundMethod: false
		
		,inheritStyle: true
		,cssSize: ''
		,cssColor: ''
		,cssFont: '' // font-family
		,cssAlign: 'left' // left,right,center. only valid for wrap at this time
		
		,realFontHeight: false
		,dpi: 96
	};
	
	for(var i in options)
		this.options[i] = options[i];
		
	this.calcDPI();
}

// generate a url based on an object
FLIRStyle.prototype.generateURL = function(o) { 
	if(this.options.inheritStyle && typeof o == 'undefined') {
		console.error('FLIRStyle.generateURL: Missing argument 2.');
		return false;	
	}

	var enc_text = escape(o.innerHTML).replace(/&/g, '{amp}'); // opera was messing things up when this was in the return statement
	return FLIR.path+'generate.php?text='+enc_text+'&h='+o.offsetHeight+'&w='+o.offsetWidth+'&fstyle='+this.serialize(o);
};

// create custom url
FLIRStyle.prototype.buildURL = function(text, o) {
	if(this.options.inheritStyle && typeof o == 'undefined') {
		console.error('FLIRStyle.buildURL: Missing argument 2.');
		return false;	
	}
	
	var enc_text = escape(text).replace(/&/g, '{amp}'); // opera was messing things up when this was in the return statement
	return FLIR.path+'generate.php?text='+enc_text+'&h=800&w=800&fstyle='+this.serialize(o);
};

FLIRStyle.prototype.serialize = function(o, bDontEncode) {
	var sdata='';
	var optdata='';
	
	var options = this.copyObject(this.options);
	
	if(this.options.inheritStyle) {
		this.options.cssColor = this.getColor(o);
		this.options.cssSize = this.getFontSize(o);
		this.options.cssFont = this.getFont(o);
		this.options.cssAlign = FLIR.getStyle(o, 'text-align');
	}
	
	for(var i in this.options) {
		sdata += ',"'+i+'":"'+this.options[i].toString().replace(/"/g, '"')+'"';
	}
	sdata = '{'+sdata.substr(1)+'}';
	
	this.options = options;
	
	return bDontEncode?sdata:escape(sdata);
};

FLIRStyle.prototype.getFont = function(o) { 
	var font = FLIR.getStyle(o, 'font-family');
	if(font.indexOf(',')) {
		font = font.split(',')[0];
	}

	return font.replace(/['"]/g, '').toLowerCase();
};

FLIRStyle.prototype.getColor = function(o) { 
	var color = FLIR.getStyle(o, 'color');
	if(color.substr(0, 1)=='#')
		color = color.substr(1);
		
	return color.replace(/['"]/g, '').toLowerCase();
};

FLIRStyle.prototype.getFontSize = function(o) {
	var raw = FLIR.getStyle(o, 'font-size');
	var pix;

	if(raw.indexOf('px') > -1) {
		pix = Math.round(parseFloat(raw));
	}else {
		var dpi = this.dpi;
		
		if(raw.indexOf('pt') > -1) {
			var pts = parseFloat(raw);
			pix = pts/(72/dpi);
		}else if(raw.indexOf('em') > -1 || raw.indexOf('%') > -1) { // im too sleepy to do this right now
			var junk = parseInt(this.getStyle(o, 'padding-top'))+parseInt(this.getStyle(o, 'padding-bottom'))+parseInt(this.getStyle(o, 'border-top'))+parseInt(this.getStyle(o, 'border-bottom'));
			pix = o.offsetHeight-junk;
		}
	}
	
	return pix;
};

FLIRStyle.prototype.calcDPI = function() {
	if(screen.logicalXDPI) {
		var dpi = screen.logicalXDPI;
	}else {
		var id = 'flir-dpi-div-test';
		if(document.getElementById(id)) {
			var test = document.getElementById(id);
		}else {
			var test = document.createElement('DIV');
			test.id = id;
			test.style.position='absolute';
			test.style.visibility='hidden';
			test.style.left=test.style.top='-1000px';
			test.style.height=test.style.width='1in';
			document.body.appendChild(test);
		}
		
		var dpi = test.offsetHeight;
	}
	
	this.dpi = dpi;
};

FLIRStyle.prototype.copyObject = function(obj) { 
	var copy = {};
	for(var i in obj) {
		copy[i] = obj[i];	
	}
	
	return copy;
};

FLIRStyle.prototype.toString = function() { return 'FLIRStyle Object'; };
