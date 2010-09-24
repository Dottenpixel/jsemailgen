<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Facelift Image Replacement | Examples</title>
<script language="javascript" src="flir.js"></script>

<style type="text/css">
html, body {
	padding:0;
	margin:0;
}
body {
	width:100%;
	height:100%;
	font-size: 14px;
	line-height:160%;
	letter-spacing:110%;
	font-family:Arial, Helvetica, sans-serif;
	color:#1f1f1f;
}

div#c {	width:750px;	margin:0 auto;}
div#menu {	border-bottom:4px solid #cccccc;	padding-bottom:2px;}
#ftr {	border-top:4px solid #cccccc;	padding-top:2px;}

#bod ul,#bod ul , pre,p { color:#4f4f4f;}
pre { border-left:3px solid #e3e3e3; padding:5px; background:#f6f6f6; }
em { color:#6f6f6f; }

a { color:blue; text-decoration:none; }
a:hover { text-decoration:underline; }

a.menu { font-family:Decorative2,Arial, Helvetica, sans-serif; }

img {	border:0; }
h1 {	font-size:36px; font-family: Default, Arial, Helvetica, sans-serif; }
h2 {	font-size:18px;}
h3 {	font-size:16px;}

span.dark, #logo-black {	color: #1f1f1f;	padding-right:4px;}
span.alt, #logo-alt {	color: red;}
#logo a, #logo a:hover {	text-decoration:none;}
#logo-ver {	color: #999999;	font-size:11px;}
.page-title { font-size:20px; color:#6f6f6f; }

ul#menu {	width:100%;}
ul#menu, ul#menu li {	float:left;	padding:0;	margin:0;	list-style:none;	padding:4px 20px 4px 0;}
ul#menu li a {	display:block;	color:red;	opacity:.4;}
ul#menu li a:hover {	opacity:1;}

div#bod {	clear:both;}
div#clr {	clear:both;	margin-top:-1px;	height:1px;	font-size:1px;}

#cols {	width:100%;}
#col {	float:left;	width:50%;}

.code {	font-family:"Courier New", Courier, monospace;	border: solid #cccccc;	border-width: 1px 0;	padding:0 2px;}

#inr { padding:10px; }

h2 {	font-size:24px; }

#examples li {  clear:both; }

#example-details { margin-top:10px; border:solid #e3e3e3; border-width:3px 0; padding:5px; }

a.update { float:right; display:block; background:url(/i/btn-flir-update.png) no-repeat; width:150px; height:35px; text-align:center; padding:15px 0 0; color:white; }
a.update:hover { background:url(/i/btn-flir-update.png) no-repeat bottom; }

.example-bkg { padding:10px 10px 0; color:#3011bd; }
.example-bkg.flir-replaced { padding:6px 10px 0; color:#3011bd; }
</style>
</head>
<body>
<div id="c">
  <div id="menu">
    <ul id="menu">
      <li><a class="menu" href="http://facelift.mawhorter.net/learn-more/">about</a></li>
      <li><a class="menu" href="http://facelift.mawhorter.net/examples/">examples</a></li>
      <li><a class="menu" href="http://facelift.mawhorter.net/quick-start/">quick start</a></li>
      <li><a class="menu" href="http://facelift.mawhorter.net/download/">download</a></li>
      <li><a class="menu" href="http://facelift.mawhorter.net/doc/">documentation</a></li>
      <li><a class="menu" href="http://www.mawhorter.net/c/projects/facelift-projects">blog</a></li>
    </ul>
    <div id="clr">&nbsp;</div>
  </div>
  <div id="bod">
    <h1 id="logo"><a href="http://facelift.mawhorter.net/"><span id="logo-black">face</span><span id="logo-alt">lift</span> <span id="logo-ver">v1.0</span> <span class="page-title">&raquo; Image Replacement Techniques and Examples</span></a></h1>

<p>
All of the following effects can be achieved with only two lines of Javascript:<br />
<span class="code">1. FLIR.init();</span><br />
<span class="code">2. FLIR.auto();</span><br />
<br />
&raquo; <strong>Read more at the <a href="http://facelift.mawhorter.net/quick-start/">quick start guide</a></strong><br />
&raquo; <strong>Learn more about <a href="http://facelift.mawhorter.net/learn-more/">facelift image replacement</a></strong>
</p>


<ol id="examples">    
   <li id="example">
      <a href="javascript: void(0);" onclick="if(this.flir_replaced) return; FLIR.replace(FLIR.getChildren(FLIR.getParentNode(this))[1]); this.flir_replaced=true;">Update It!</a>
      <h2>An ordinary heading</h2>
      <p>Elements can be replaced automatically or one at a time.  FLIR will automatically grab the text from the element and replace it with an image using your custom font.</p>
   </li>


   <li id="example">
      <a class="update" href="javascript: void(0);" onclick="if(this.flir_replaced) return; FLIR.replace(FLIR.getChildren(FLIR.getParentNode(this))[1]); this.flir_replaced=true;">Update It!</a>
      <h2 style="font-family: Antagea, Arial, Helvetica, sans-serif;">An ordinary heading with a different font</h2>
      <p>The replacement image is dynamically generated using the CSS styles that you have applied to the element.  This is the default behavior but you can also disable it and set styles, and replace elements, one at a time.</p>
   </li>
   
   <li id="example">
      <a class="update" href="javascript: void(0);" onclick="if(this.flir_replaced) return; FLIR.replace(FLIR.getChildren(FLIR.getParentNode(this))[1]); this.flir_replaced=true;">Update It!</a>
      <h2><span>Now let's</span> <span style="font-family: TribalBenji, Arial, Helvetica, sans-serif; color:green; font-size:32px;">spice</span> <span style="color:red;">it</span> <span style="color:orange;">up!</span></h2>
      <p>With FLIR, you are not limited to one font and color with your replacement images.  Add as many fonts and colors as you wish to your elements and they will be detected and replaced &mdash; automatically.</p>
   </li>
   
   <li id="example">
      <a class="update" href="javascript: void(0);" onclick="if(this.flir_replaced) return; FLIR.replace(FLIR.getChildren(FLIR.getParentNode(this))[1], new FLIRStyle({mode:'wrap'})); this.flir_replaced=true;">Update It!</a>
      <h2 style="margin-right:150px; text-align:center;">Maybe you have a really long header that you would like to wrap down to the next line.  You know, so there are multiple lines of text.  You can even align it to the left, center, or right.  This text is center aligned.</h2>
      <p>Image replacements can be generated usng three separate modes: stretch, progressive, and wrap &mdash; which is the mode you see above.  They can be set on an element to element basis or only once. </p>
      <p>Progressive will shrink the text size of the generated image until it fits inside the HTML elements box.</p>
      <p>Stretch will generate an image and resize the HTML element to contain it.</p>
   </li>
   
   <li id="example" style="background:url(/i/flir-ex-bkg.jpg) no-repeat; ">
      <a class="update" href="javascript: void(0);" onclick="if(this.flir_replaced) return; FLIR.replace(FLIR.getChildren(FLIR.getParentNode(this))[1]); this.flir_replaced=true;">Update It!</a>
      <h2 class="example-bkg">How 'bout with a background?</h2>
      <p>Create custom backgrounds for your elements and have them shine through!  All generated images are transparent PNG (pronounced ping) files which will allow your text images to lay nicely overtop of whatever is behind it &mdash; just like normal text.</p>
      <p>FLIR transparent PNG images will work in all modern browsers and even the craptacular Internet Explorer 6.</p>
   </li>
   
   <li id="example">
      <a class="update" href="javascript: void(0);" onclick="if(this.flir_replaced) return; FLIR.replace(FLIR.getChildren(FLIR.getParentNode(this))[1]); this.flir_replaced=true;">Update It!</a>
      <h2><span>Use symbol fonts!</span> <span style="font-family: AnimalDings, Arial, Helvetica, sans-serif; color:red; font-size:36px;">ABC</span></h2>
      <p>Any font that is supported by PHP, FreeType, and GD is supported by FLIR.  Use symbol fonts to create stylish accents or custom bullets for your <span class="code">&lt;ul&gt;</span> and <span class="code">&lt;ol&gt;</span> lists.</p>
   </li>
</ol>
    
    
  </div>
  <div id="ftr">    <div style="float:right"><a href="http://facelift.mawhorter.net/learn-more/">About Facelift</a> | <a href="http://facelift.mawhorter.net/example/">Example</a> | <a href="http://facelift.mawhorter.net/download/">Download facelift</a> | <a href="http://facelift.mawhorter.net/doc/">Documentation</a></div>
    <strong>face<span style="color:#ff0000;">lift</span></strong> was written by <a href="http://www.mawhorter.net/">Cory Mawhorter</a> </div>
</div>
<br />
<script type="text/javascript">
function get(n) { return document.getElementById(n); }

FLIR.init();
FLIR.defaultStyle.options.useBackgroundMethod=false;
FLIR.auto(['h1','a.update']);
</script>
</body>
</html>
