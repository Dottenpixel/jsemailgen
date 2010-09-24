/* Wmode *must* be opaque for this rig to work properly. */

sIFR.replaceElementBetter = function(sSelector, sFlashSrc, sColor, sLinkColor, sHoverColor, sBgColor, nPaddingTop, nPaddingRight, nPaddingBottom, nPaddingLeft, sFlashVars, sCase, sWmode)
{
	named.extract(arguments, {
		sSelector : function(value){ sSelector = value },
		sFlashSrc : function(value){ sFlashSrc = value },
		sColor : function(value){ sColor = value },
		sLinkColor : function(value){ sLinkColor = value },
		sHoverColor : function(value){ sHoverColor = value },
		sBgColor : function(value){ sBgColor = value },
		nPaddingTop : function(value){ nPaddingTop = value },
		nPaddingRight : function(value){ nPaddingRight = value },
		nPaddingBottom : function(value){ nPaddingBottom = value },
		nPaddingLeft : function(value){ nPaddingLeft = value },
		sFlashVars : function(value){ sFlashVars = value },
		sCase : function(value){ sCase = value },
		sWmode : function(value){ sWmode = value }
	});

	var listNodes = parseSelector(sSelector);
	var runOriginal = false;
	var runNew = false;
	var postProcess = new Array();

	/* Check for a child link in the to-be-sIFR-ed elements.
	   If there's no link, then there's nothing to do. 
	   
	   This pre-processing section is all about breaking apart the link and setting it up
	   so that sIFR generates two separate movies-- one for the normal, and one for the hover state.
	*/
	
	for (var i = 0; i < listNodes.length; i++)
	{
		var thisNode = listNodes[i];

		if (thisNode.childNodes[0].tagName == 'A' || thisNode.childNodes[0].tagName == 'a')
		{
			/* wrap our new concoction */
			var wrapper = document.createElement('div');
			thisNode.parentNode.insertBefore(wrapper,thisNode);
			wrapper.appendChild(thisNode);

			/* extract the link and move it elsewhere */
			var theLink = thisNode.childNodes[0];
			thisNode.appendChild(theLink.childNodes[0]);
			wrapper.insertBefore(theLink, thisNode);
			
			/* dupe the node (sans link) to create the hover-state sIFR */
			var dupeNode = thisNode.cloneNode(true);
			wrapper.insertBefore(dupeNode, thisNode);

			thisNode.className += " sifr-hover-normalstate";
			dupeNode.className += " sifr-hover-hoverstate";

			postProcess.push(wrapper);
			runNew = true;
		}
		else
			runOriginal = true;
	}
	
	if (runNew)
	{
		this.replaceElement(".sifr-hover-normalstate", sFlashSrc, sLinkColor, null, null, sBgColor, nPaddingTop, nPaddingRight, nPaddingBottom, nPaddingLeft, sFlashVars, sCase, sWmode);
		this.replaceElement(".sifr-hover-hoverstate", sFlashSrc, sHoverColor, null, null, sBgColor, nPaddingTop, nPaddingRight, nPaddingBottom, nPaddingLeft, sFlashVars, sCase, sWmode);

		/* Post-processing. Here's where we actually assemble the package and make up 
		   mouseover events, etc.
		*/
		while (wrapper = postProcess.pop())
		{
			var theLink = wrapper.childNodes[0];
			var dupeNode = wrapper.childNodes[1];
			var thisNode = wrapper.childNodes[2];

			wrapper.style.position = "relative";
			wrapper.style.textAlign = "left";

			theLink.style.display = "block";
			theLink.style.position = "absolute";
			theLink.style.top = "0";
			theLink.style.left = "0";
			theLink.style.height = wrapper.offsetHeight / 2 + "px";
			theLink.style.width = wrapper.offsetWidth + "px";
			theLink.style.zIndex = "4";
			
			wrapper.style.width = theLink.style.width;
			wrapper.style.height = theLink.style.height;

			thisNode.style.position = dupeNode.style.position = "absolute";
			thisNode.style.zIndex = "2";
			dupeNode.style.zIndex = "1";

			function setZIndex(element, level) {
				return function() { element.style.zIndex = level; }
			}

			theLink.onmouseover = setZIndex(dupeNode, "3");
			theLink.onmouseout = setZIndex(dupeNode, "2");
		}
	}

	if (runOriginal)
		this.replaceElement(sSelector, sFlashSrc, sColor, sLinkColor, sHoverColor, sBgColor, nPaddingTop, nPaddingRight, nPaddingBottom, nPaddingLeft, sFlashVars, sCase, sWmode);
};