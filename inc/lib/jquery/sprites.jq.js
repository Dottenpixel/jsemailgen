function sprite(selector, basePath, replace_img, auto_prefix, auto_suffix) {
	$(selector).each(function() {
		r = new Replacement(selector, basePath, replace_img, auto_prefix, auto_suffix, this);
	});
	function Replacement(selector, basePath, replace_img, auto_prefix, auto_suffix, obj) {
		if(replace_img == 'content') {
			filename = obj.innerHTML;
			filename = filename.toLowerCase();
			filename = basePath+auto_prefix+filename.replace(/[^a-zA-Z0-9]/g, '_')+auto_suffix;
		} else {
			filename = basePath+replace_img;
		}
		
		obj.innerHTML = '<span style="display: none">'+this.innerHTML+'<'+'/span>';
		
		var img = new Image();
		img.src = filename;

		$(img).load(function() {
			filename = this.src;
			width = this.width;
			height = this.height;
			
			$(obj).css('display', 'block');
			$(obj).css('width', width+'px');
			$(obj).css('height', height+'px');
			$(obj).css('background', 'url('+filename+') no-repeat');
		});
	}
}